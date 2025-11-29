<?php 
defined('IN_DESTOON') or exit('Access Denied');
class spider {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'spider';
		$this->fields = array('title','style','linkurl','mid','tb','name','catid','catname','content','setting','addtime','lasttime','editor','edittime');
    }

    function spider() {
		$this->__construct();
    }

	function pass($post) {
		global $MODULE, $L;
		if(!is_array($post)) return false;
		if(!$post['title']) return $this->_($L['spider_pass_title']);
		if(!is_url($post['linkurl'])) return $this->_($L['spider_pass_url']);
		if($post['mid'] < 1 && strlen($post['tb']) < 3) return $this->_($L['spider_pass_mid']);
		if($post['mid'] && !isset($MODULE[$post['mid']])) return $this->_($L['spider_pass_mod']);
		return true;
	}

	function set($post) {
		global $MODULE, $_username;
		$post['addtime'] = (isset($post['addtime']) && is_time($post['addtime'])) ? datetotime($post['addtime']) : DT_TIME;
		$post['edittime'] = DT_TIME;
		$post['editor'] = $_username;
		$post['tb'] = strip_sql($post['tb'], 0);
		if($post['tb'] && $post['mid'] && $post['tb'] != get_table($post['mid'])) $post['mid'] = 0;
		if($post['mid']) {
			$post['name'] = $MODULE[$post['mid']]['name'];
			$post['tb'] = $post['catname'] = '';
			if($post['catid']) {
				$CAT = get_cat($post['catid']);
				if($CAT && $CAT['moduleid'] == $post['mid']) {
					$post['catname'] = addslashes($CAT['catname']);
				} else {
					$post['catid'] = 0;
				}
			}
		} else {
			if(!$post['name'] || $post['name'] == $post['tb']) $post['name'] = substr($post['tb'], strlen(DT_PRE));
			$post['mid'] = $post['catid'] = 0;
			$post['catname'] = '';
		}
		if($this->itemid) {
			//
		} else {
			$post['lasttime'] = DT_TIME;
		}
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get($itemid) {
		$r = cache_read('spider-'.$itemid.'.php');
		return $r ? $r : $this->cache($itemid);
	}

	function cache($itemid = 0) {
		$itemid or $itemid = $this->itemid;
		$r = DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$itemid");
		if($r['setting']) {
			$setting = unserialize($r['setting']);
			$r['config'] = $setting[0];
			unset($setting[0]);
			$r['setting'] = $setting;
			$update = '';
			$urls = DB::count($this->table.'_url', "sid=$itemid");
			$datas = DB::count($this->table.'_url', "sid=$itemid AND status>1");
			if($r['urls'] != $urls) {
				$r['urls'] = $urls;
				$update .= ",urls=$urls";
			}
			if($r['datas'] != $datas) {
				$r['datas'] = $datas;
				$update .= ",datas=$datas";
			}
			if($update) DB::query("UPDATE {$this->table} SET ".substr($update, 1)." WHERE itemid=$itemid");
		}
		cache_write('spider-'.$itemid.'.php', $r);
		return $r;
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid");
	}

	function get_list($condition = '1', $order = 'addtime DESC') {
		global $pages, $page, $pagesize, $offset, $sum, $items;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['lastdate'] = timetodate($r['lasttime'], 5);
			$lists[] = $r;
		}
		return $lists;
	}

	function get_list_url($condition = '1', $order = 'itemid DESC') {
		global $pages, $page, $pagesize, $offset, $sum, $items;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table}_url WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table}_url WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = $r['edittime'] ? timetodate($r['edittime'], 5) : 'N/A';
			$r['postdate'] = $r['posttime'] ? timetodate($r['posttime'], 5) : 'N/A';
			$lists[] = $r;
		}
		return $lists;
	}

	function get_list_data($condition = '1', $order = 'itemid DESC') {
		global $pages, $page, $pagesize, $offset, $sum, $items;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table}_data WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table}_data WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['postdate'] = $r['posttime'] ? timetodate($r['posttime'], 5) : 'N/A';
			$r['url'] = ($r['tid'] && $r['mid']) ? gourl('?mid='.$r['mid'].'&itemid='.$r['tid']) : '';
			$lists[] = $r;
		}
		return $lists;
	}

	function get_tables() {
		global $CFG;
		$tables = array();
		$i = 0;
		$result = DB::query("SHOW TABLE STATUS FROM `".$CFG['db_name']."`");
		while($r = DB::fetch_array($result)) {
			if(preg_match('/^'.DT_PRE.'/', $r['Name'])) {
				$tables[$i]['name'] = $r['Name'];
				$tables[$i]['note'] = $r['Comment'];
				$i++;
			}
		}
		return $tables;
	}

	function get_fields($table) {
		global $MODULE;
		$fds = array();
		if(strpos($table, DT_PRE) === false) {
			$rtable = $table;
		} else {
			$rtable = substr($table, strlen(DT_PRE));
			$fds = cache_read('fields-'.$rtable.'.php');
			$rtable = preg_replace("/_[0-9]{1,}/", '', $rtable);
			if(is_numeric($rtable) && isset($MODULE[$rtable])) $rtable = $MODULE[$rtable]['module'].'_data';
		}
		$names = array();	
		if(is_file(DT_ROOT.'/file/setting/dict_'.$rtable.'.php')) {
			$tmp = file_get(DT_ROOT.'/file/setting/dict_'.$rtable.'.php');
			if(substr($tmp, 0, 13) == '<?php exit;?>') $tmp = trim(substr($tmp, 13));
			$arr = explode("\n", $tmp);
			foreach($arr as $v) {
				$t = explode(',', $v);
				$names[$t[0]] = $t[1];
			}
		}
		if($fds) {
			foreach($fds as $v) {
				if(isset($names[$v['name']]) && $names[$v['name']]) continue;
				$names[$v['name']] = $v['title'];
			}
		}
		$arr = array();
		$result = DB::query("SHOW COLUMNS FROM `{$table}`");
		while($r = DB::fetch_array($result)) {
			$arr[$r['Field']] = isset($names[$r['Field']]) ? $names[$r['Field']] : '';
		}
		return $arr;
	}

	function add($post) {
		global $DT, $MOD, $module;
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		$this->cache();
		return $this->itemid;
	}

	function edit($post) {
		global $DT, $MOD, $module;
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		$this->cache();
		return true;
	}

	function save($setting) {
		$setting[0]['page_max'] = intval($setting[0]['page_max']);
		$setting[0]['page_max'] > 0 or $setting[0]['page_max'] = 1;
		$setting = addslashes(serialize(dstripslashes($setting)));
		DB::query("UPDATE {$this->table} SET edittime=".DT_TIME.",setting='".$setting."' WHERE itemid=$this->itemid");
		$this->cache();
	}

	function save_list($lists, $sid) {
		global $_username;
		$i = 0;
		foreach($lists as $v) {
			if(is_url($v['linkurl'])) {
				$item = md5($v['linkurl']);
				$t = DB::get_one("SELECT itemid FROM {$this->table}_url WHERE item='$item'");
				if($t) {
					//
				} else {
					$title = addslashes($v['title']);
					$linkurl = addslashes($v['linkurl']);
					DB::query("INSERT INTO {$this->table}_url (sid,title,linkurl,item,addtime,editor) VALUES ('$sid','$title','$linkurl','$item','".DT_TIME."','$_username')");
					$i++;
				}
			}
		}
		DB::query("UPDATE {$this->table} SET lasttime=".DT_TIME." WHERE itemid=$sid");
		return $i;
	}

	function save_show($r) {
		global $_username;
		$this->itemid = $sid = $r['sid'];
		$s = $this->get($sid);	
		$setting = $s['setting'];
		$config = $s['config'];

		$itemid = $r['itemid'];
		$html = $this->dcurl($r['linkurl'], $config);
		if($this->is_html($html, $config)) {
			$status = 2;
			$post = array();
			$post['itemid'] = $itemid;
			$post['sid'] = $sid;
			$post['title'] = addslashes($r['title']);
			$post['linkurl'] = addslashes($r['linkurl']);
			$post['mid'] = $s['mid'];
			$post['name'] = addslashes($s['name']);
			$post['catid'] = $s['catid'];
			$post['catname'] = addslashes($s['catname']);
			$post['html'] = addslashes($html);
			$post['addtime'] = DT_TIME;
			$post['posttime'] = 0;
			$post['editor'] = $_username;
			$post['status'] = $status;
			$post['note'] = '';
			DB::query("INSERT INTO {$this->table}_data ".arr2sql($post, 0));
		} else {			
			$status = 1;
		}
		DB::query("UPDATE {$this->table}_url SET edittime=".DT_TIME.",status='$status' WHERE itemid=$itemid");
		return $status == 2 ? 1 : 0;
	}

	function export($setting) {
		return str_replace(array('=', '+', '/', '0x', '0X'), array('-E-', '-P-', '-S-', '-Z-', '-X-'), base64_encode($setting));
	}

	function import($rule) {
		$setting = base64_decode(str_replace(array('-E-', '-P-', '-S-', '-Z-', '-X-'), array('=', '+', '/', '0x', '0X'), $rule));
		return (substr($setting, 0, 2) == 'a:' && substr($setting, -1) == '}') ? $setting : '';
	}

	function dcurl($url, $config) {
		if(function_exists('curl_init') && is_url($url)) {
			$cur = curl_init($url);
			$header = array();
			if($config['ip']) {
				if(strpos($config['ip'], '|') === false) {
					$ip = $config['ip'];
				} else {
					$ips = explode('|', trim($config['ip']));
					$ip = $ips[array_rand($ips)];
				}
			} else {
				$ip = DT_IP;
			}
			$header[] = 'CLIENT-IP: '.$ip;
			$header[] = 'X-FORWARDED-FOR: '.$ip;
			if($config['header']) {
				foreach(explode("\n", trim($config['header'])) as $v) {
					$v = trim($v);
					if($v) $header[] = $v;
				}
			}
			if($header) curl_setopt($cur, CURLOPT_HTTPHEADER, $header);
			if($config['cookie']) curl_setopt($cur, CURLOPT_COOKIE, $config['cookie']);
			curl_setopt($cur, CURLOPT_USERAGENT, $config['agent'] ? $config['agent'] : $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($cur, CURLOPT_REFERER, $url);
			curl_setopt($cur, CURLOPT_ENCODING, 'gzip');
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($cur, CURLOPT_HEADER, 0);
			curl_setopt($cur, CURLOPT_TIMEOUT, 30);
			if(substr($url, 0, 8) == 'https://') {
				curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 0);
			}
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, 1);
			$rec = curl_exec($cur);
			curl_close($cur);
			if($rec) return ($config['encode'] && $config['encode'] != 'UTF-8') ? convert($rec, $config['encode'], 'UTF-8') : $rec;
		}
		return '';
	}

	function get_url($html, $config) {
		$lists = array();
		if($config['list_from'] || $config['list_to']) $html = cutstr($html, $config['list_from'], $config['list_to']);
		if(preg_match_all("/href=([\"|']?)([^ \"'>]+)\\1(.*?)>([^>]+)<\/a>/i", $html, $matches)) {
			foreach($matches[2] as $k=>$v) {
				$url = $this->get_link($v, $config);
				$txt = $this->get_text($matches[4][$k], $config);
				if($this->is_url($url, $config) && $this->is_txt($txt, $config)) $lists[] = array('linkurl' => $url, 'title' => $txt);
			}
		}
		return array_reverse($lists);
	}

	function get_data($html, $itemid, $title = '') {
		extract($GLOBALS, EXTR_SKIP);
		/*include/admin.func fetch_url()*/
		$s = $this->get($itemid);
		$post = array();
		foreach($s['setting'] as $k=>$v) {
			if(!$v['fm'] && !$v['to'] && !$v['vl']) continue;
			$vl = '';
			if($v['fm'] || $v['to']) {
				$vl = cutstr($html, $v['fm'], $v['to']);
				if($k != 'content') $vl = strip_tags($vl);
				$vl = trim($vl);
			}
			if($vl == '' && $v['vl']) {
				if(substr($v['vl'], 0, 1) == '{' && substr($v['vl'], -1, 1) == '}') {
					$temp = NULL;
					eval("\$temp = ".substr($v['vl'], 1, -1).";");
					if($temp != NULL) $vl = $temp;
				} else {
					$vl = $v['vl'];
				}
			}
			if($v['fd'] && $v['rp']) {
				$rp = explode('|', $v['rp']);
				foreach(explode('|', $v['fd']) as $kk=>$vv) {
					if(!isset($rp[$kk])) continue;
					if(substr($vv, 0, 1) == '/') {
						$vl = preg_replace($vv, $rp[$kk], $vl);
					} else {
						$vl = str_replace($vv, $rp[$kk], $vl);
					}
				}
			}
			if(strpos($v['fc'], '*') !== false) {
				foreach(explode(';', $v['fc']) as $vv) {
					$vv = trim($vv);
					if(strpos($vv, '*') === false) continue;
					$func = str_replace('*', '$vl', $vv);
					$temp = NULL;
					eval("\$temp = ".$func.";");
					if($temp != NULL) $vl = $temp;
				}
			}
			if($vl == $html) continue;
			if($k == 'addtime' && is_date($vl)) $vl = $vl.' '.timetodate(DT_TIME, 'H:i:s');
			if($k == 'totime' && is_date($vl)) $vl = $vl.' '.timetodate(DT_TIME, 'H:i:s');
			$post[$k] = $vl;
		}
		if(!isset($post['status']) && isset($s['setting']['status'])) $post['status'] = $s['config']['status'];
		if(!isset($post['catid']) && isset($s['setting']['catid']) && $s['catid']) $post['catid'] = $s['catid'];
		return $post;
	}

	function is_url($url, $config) {
		if(!is_url($url)) return 0;
		$pass = 1;
		if($config['show_include']) {
			if(strpos($config['show_include'], '|') !== false) {
				$pass = 0;
				foreach(explode('|', $config['show_include']) as $v) {
					if(strpos($url, $v) !== false) {$pass = 1; break;}
				}
			} else if(strpos($config['show_include'], '&') !== false) {
				$pass = 1;
				foreach(explode('&', $config['show_include']) as $v) {
					if(strpos($url, $v) === false) {$pass = 0; break;}
				}
			} else {
				$pass = strpos($url, $config['show_include']) !== false ? 1 : 0;
			}
		}
		if($config['show_exclude']) {
			if(strpos($config['show_exclude'], '|') !== false) {
				foreach(explode('|', $config['show_exclude']) as $v) {
					if(strpos($url, $v) !== false) {$pass = 0; break;}
				}
			} else if(strpos($config['show_exclude'], '&') !== false) {
				$tp = 1;
				foreach(explode('&', $config['show_exclude']) as $v) {
					if(strpos($url, $v) === false) {$tp = 0; break;}
				}
				if($tp) $pass = 0;
			} else {
				if(strpos($url, $config['show_exclude']) !== false) $pass = 0;
			}
		}
		return $pass;
	}

	function is_txt($txt, $config) {
		$pass = 1;
		if($config['text_exclude']) {
			if(strpos($config['text_exclude'], '|') !== false) {
				foreach(explode('|', $config['text_exclude']) as $v) {
					if(strpos($txt, $v) !== false) {$pass = 0; break;}
				}
			} else if(strpos($config['text_exclude'], '&') !== false) {
				$tp = 1;
				foreach(explode('&', $config['text_exclude']) as $v) {
					if(strpos($txt, $v) === false) {$tp = 0; break;}
				}
				if($tp) $pass = 0;
			} else {
				if(strpos($txt, $config['text_exclude']) !== false) $pass = 0;
			}
		}
		return $pass;
	}

	function is_html($html, $config) {
		if(strlen($html) < 100) return 0;
		$pass = 1;
		if($config['html_include']) {
			if(strpos($config['html_include'], '|') !== false) {
				foreach(explode('|', $config['html_include']) as $v) {
					if(strpos($html, $v) !== false) {$pass = 1; break;}
				}
			} else if(strpos($config['html_include'], '&') !== false) {
				foreach(explode('&', $config['html_include']) as $v) {
					if(strpos($html, $v) === false) {$pass = 0; break;}
				}
			} else {
				if(strpos($html, $config['html_include']) !== false) $pass = 1;
			}
		}
		if($config['html_exclude']) {
			if(strpos($config['html_exclude'], '|') !== false) {
				foreach(explode('|', $config['html_exclude']) as $v) {
					if(strpos($html, $v) !== false) {$pass = 0; break;}
				}
			} else if(strpos($config['html_exclude'], '&') !== false) {
				foreach(explode('&', $config['html_exclude']) as $v) {
					if(strpos($html, $v) === false) {$pass = 1; break;}
				}
			} else {
				if(strpos($html, $config['html_exclude']) !== false) $pass = 0;
			}
		}
		return $pass;
	}

	function get_link($url, $config) {
		if(strpos($url, '://') === false && strpos($config['show_basehref'], '://') !== false) {
			$uri = $url;
			$i = 100;
			while($i-- > 0) {
				if(in_array(substr($uri, 0, 1), array('.', '/'))) $uri = substr($uri, 1);
			}
			$url = $config['show_basehref'].(substr($config['show_basehref'], -1) == '/' ? '' : '/').$uri;
		}
		return $url;
	}

	function get_text($txt, $config) {
		$txt = strip_tags(trim($txt));
		return ($config['text_from'] || $config['text_to']) ? cutstr($txt, $config['text_from'], $config['text_to']) : $txt;
	}

	function delete($itemid, $job) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v, $job); 
			}
		} else {
			if($job == 'data') {
				DB::query("DELETE FROM {$this->table}_data WHERE itemid=$itemid");
				DB::query("UPDATE {$this->table}_url SET status=0,edittime=0,posttime=0 WHERE itemid=$itemid");
			} else if($job == 'url') {
				DB::query("DELETE FROM {$this->table}_url WHERE itemid=$itemid");
				DB::query("DELETE FROM {$this->table}_data WHERE itemid=$itemid");
			} else {
				DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				DB::query("DELETE FROM {$this->table}_url WHERE sid=$itemid");
				DB::query("DELETE FROM {$this->table}_data WHERE sid=$itemid");
				cache_delete('spider-'.$itemid.'.php');
			}
		}
	}

	function status($itemid, $status) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->status($v, $status); 
			}
		} else {
			if($status == 4) {
				$d = DB::get_one("SELECT * FROM {$this->table}_data WHERE itemid=$itemid");
				if($d) {
					$posttime = $d['posttime'] ? $d['posttime'] : DT_TIME;
					DB::query("UPDATE {$this->table}_url SET status=$status,posttime=$posttime WHERE itemid=$itemid");
					DB::query("UPDATE {$this->table}_data SET status=$status,posttime=$posttime,note='SET' WHERE itemid=$itemid");
				}
			} else if($status == 3) {
				$d = DB::get_one("SELECT * FROM {$this->table}_data WHERE itemid=$itemid");
				if($d) {
					$posttime = $d['posttime'] ? $d['posttime'] : DT_TIME;
					DB::query("UPDATE {$this->table}_url SET status=$status,posttime=$posttime WHERE itemid=$itemid");
					DB::query("UPDATE {$this->table}_data SET status=$status,posttime=$posttime,note='' WHERE itemid=$itemid");
				}
			} else if($status == 2) {
				$d = DB::get_one("SELECT * FROM {$this->table}_data WHERE itemid=$itemid");
				if($d) {
					DB::query("UPDATE {$this->table}_url SET status=$status,posttime=0 WHERE itemid=$itemid");
					DB::query("UPDATE {$this->table}_data SET status=$status,posttime=0,tid=0,note='' WHERE itemid=$itemid");
				}
			} else if($status == 1) {
				$update = "status=$status,posttime=0";
				$u = DB::get_one("SELECT * FROM {$this->table}_url WHERE itemid=$itemid");
				if($u) {
					if(!$u['edittime']) $update .= ",edittime=".DT_TIME;
					DB::query("UPDATE {$this->table}_url SET {$update} WHERE itemid=$itemid");
					DB::query("DELETE FROM {$this->table}_data WHERE itemid=$itemid");
				}
			} else if($status == 0) {
				DB::query("UPDATE {$this->table}_url SET status=$status,edittime=0,posttime=0 WHERE itemid=$itemid");
				DB::query("DELETE FROM {$this->table}_data WHERE itemid=$itemid");
			}
		}
	}
	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>