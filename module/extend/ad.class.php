<?php 
defined('IN_DESTOON') or exit('Access Denied');
class ad {
	var $aid;
	var $pid;
	var $table;
	var $table_place;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'ad';
		$this->table_place = DT_PRE.'ad_place';
    }

    function ad() {
		$this->__construct();
    }

	function is_place($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['name']) return $this->_($L['pass_ad_name']);
		if($post['typeid'] == 3 || $post['typeid'] == 4 || $post['typeid'] == 5) {
			if(!$post['width']) return $this->_($L['pass_place_width']);
			if(!$post['height']) return $this->_($L['pass_place_height']);
		}
		if($post['typeid'] == 6 || $post['typeid'] == 7) {
			if(!$post['moduleid']) return $this->_($L['pass_place_module']);
			$condition = "moduleid=$post[moduleid] AND typeid=$post[typeid]";
			if($this->pid) $condition .= " AND pid<>$this->pid";
			$r = DB::get_one("SELECT pid FROM {$this->table_place} WHERE {$condition}");
			if($r) return $this->_($L['pass_place_repeat']);
		}
		return true;
	}

	function set_place($post) {
		global $_username;
		$post = array_map('ad_restore', $post);
		if(!$this->pid) $post['addtime'] = DT_TIME;
		$post['edittime'] = DT_TIME;
		$post['editor'] = $_username;
		$post['width'] = intval($post['width']);
		$post['height'] = intval($post['height']);
		$post['setting'] = $post['setting'] ? serialize($post['setting']) : '';
		return $post;
	}

	function add_place($post) {
		$post = $this->set_place($post);
		DB::query("INSERT INTO {$this->table_place} ".arr2sql($post, 0));
		$this->pid = DB::insert_id();
		clear_upload($post['thumb'], $this->pid, $this->table_place);
		return $this->pid;
	}
	
	function edit_place($post) {
		$post = $this->set_place($post);
	    DB::query("UPDATE {$this->table_place} SET ".arr2sql($post, 1)." WHERE pid=$this->pid");
		clear_upload($post['thumb'], $this->pid, $this->table_place);
		return true;
	}

	function get_one_place() {
        $r = DB::get_one("SELECT * FROM {$this->table_place} WHERE pid=$this->pid");
		if($r['setting']) {
			$arr = unserialize($r['setting']);
			foreach($arr as $k=>$v) {
				$r[$k] = $v;
			}
		}
		return $r;
	}

	function get_list_place($condition = '1', $order = 'listorder DESC,pid DESC') {
		global $EXT, $TYPE, $pages, $page, $pagesize, $offset, $sum, $items;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table_place} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table_place} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['alt'] = $r['name'];
			$r['name'] = set_style($r['name'], $r['style']);
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['width'] = $r['width'] ? $r['width'].'px' : '--';
			$r['height'] = $r['height'] ? $r['height'].'px' : '--';
			$r['typename'] = $TYPE[$r['typeid']];
			$r['typeurl'] = $EXT['ad_url'].list_url($r['typeid']);
			$r['prices'] = $this->price_place($r);
			$lists[] = $r;
		}
		return $lists;
	}

	function price_place($r) {
		global $PTYPE;
		$prices = array();
		if($r['price'] > 0) $prices['m1'] = $r['price'];
		if($r['setting']) {
			$arr = unserialize($r['setting']);
			foreach($arr as $k=>$v) {
				if(isset($PTYPE[$k]) && dround($v) > 0) $prices[$k] = dround($v);
			}
		}
		return $prices;
	}

	function stats_place($pid) {
		$p = DB::get_one("SELECT ads FROM {$this->table_place} WHERE pid=$pid");
		$ads = DB::count($this->table, "pid=$pid");
		if($p['ads'] != $ads) DB::query("UPDATE {$this->table_place} SET ads=$ads WHERE pid=$pid");
	}

	function order_place($listorder) {
		if(!is_array($listorder)) return false;
		foreach($listorder as $k=>$v) {
			$k = intval($k);
			$v = intval($v);
			DB::query("UPDATE {$this->table_place} SET listorder=$v WHERE pid=$k");
		}
		return true;
	}

	function delete_place($pid) {
		if(is_array($pid)) {
			foreach($pid as $v) { 
				$this->delete_place($v); 
			}
		} else {			
			$p = DB::get_one("SELECT * FROM {$this->table_place} WHERE pid=$pid");
			DB::query("DELETE FROM {$this->table_place} WHERE pid=$pid");
			$filename = $p['typeid'] > 5 ? 'ad_'.$p['moduleid'].'_d'.$p['typeid'].'.htm' : 'ad_'.$a['pid'].'_d0.htm';
			file_del(DT_CACHE.'/htm/'.$filename);
			file_del(DT_CACHE.'/htm/ad_'.$pid.'.htm');
			file_del(DT_ROOT.'/file/script/A'.$pid.'.js');
			$result = DB::query("SELECT aid FROM {$this->table} WHERE pid=$pid ORDER BY aid DESC");
			while($r = DB::fetch_array($result)) {
				$this->delete($r['aid']);
			}
		}
	}

	function is_ad($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['title']) return $this->_($L['pass_ad_title']);
		if(!$post['fromtime'] || !is_time($post['fromtime'])) return $this->_($L['pass_ad_from']);
		if(!$post['totime'] || !is_time($post['totime'])) return $this->_($L['pass_ad_end']);
		if(datetotime($post['fromtime']) > datetotime($post['totime'])) return $this->_($L['pass_ad_bad_date']);
		if($post['typeid'] == 1 || $post['typeid'] == 7) {
			if(!$post['code']) return $this->_($L['pass_ad_code']);
		} else if($post['typeid'] == 2) {
			if(!$post['text_name']) return $this->_($L['pass_ad_text_name']);
			if(!$post['text_url']) return $this->_($L['pass_ad_text_url']);
		} else if($post['typeid'] == 3) {
			if(!$post['image_src']) return $this->_($L['pass_ad_image_src']);
		} else if($post['typeid'] == 4) {
			if(!$post['video_src']) return $this->_($L['pass_ad_video_src']);
		}
		return true;
	}

	function set_ad($post) {
		global $_username;
		$post = array_map('ad_restore', $post);
		if(!$this->aid) $post['addtime'] = DT_TIME;
		$post['edittime'] = DT_TIME;
		$post['editor'] = $_username;
		$post['fromtime'] = datetotime($post['fromtime']);
		$post['totime'] = datetotime($post['totime']);
		$post['username'] or $post['username'] = $_username;
		$post['url'] = '';
		if($post['typeid'] == 2) {
			$post['url'] = $post['text_url'];
		} else if($post['typeid'] == 3 || $post['typeid'] == 5) {
			$post['url'] = $post['image_url'];
		} else if($post['typeid'] == 4) {
			$post['url'] = $post['video_url'];
		}
		return $post;
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE aid=$this->aid");
	}

	function get_list($condition = '1', $order = 'fromtime DESC') {
		global $MOD, $TYPE, $pages, $page, $pagesize, $offset, $L, $sum;
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
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['fromdate'] = timetodate($r['fromtime'], 3);
			$r['todate'] = timetodate($r['totime'], 3);			
			if($r['totime'] < DT_TIME) {
				$r['process'] = $L['status_expired'];
				$r['days'] = 0;
			} else if($r['fromtime'] > DT_TIME) {
				$r['process'] = $L['status_not_start'];
				$r['days'] = intval(($r['totime'] - $r['fromtime'])/86400);
			} else {
				$r['process'] = $L['status_displaying'];
				$r['days'] = intval(($r['totime'] - DT_TIME)/86400);
			}
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set_ad($post);
		DB::query("INSERT INTO {$this->table}".arr2sql($post, 0));
		$this->aid = DB::insert_id();
		DB::query("UPDATE {$this->table_place} SET ads=ads+1 WHERE pid=$post[pid]");
		clear_upload($post['image_src'].$post['video_src'].$post['code'], $this->aid, $this->table);
		return $this->aid;
	}

	function edit($post) {
		$post = $this->set_ad($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1)." WHERE aid=$this->aid");
		clear_upload($post['image_src'].$post['video_src'].$post['code'], $this->aid, $this->table);
		return true;
	}

	function delete($aid) {
		if(is_array($aid)) {
			foreach($aid as $v) { 
				$this->delete($v); 
			}
		} else {
			$this->aid = $aid;
			$a = $this->get_one();
			$filename = ad_name($a);
			file_del(DT_CACHE.'/htm/'.$filename);
			$userid = get_user($a['username']);
			if($a['image_src']) delete_upload($a['image_src'], $userid, $aid);
			if($a['video_src']) delete_upload($a['video_src'], $userid, $aid);
			DB::query("DELETE FROM {$this->table} WHERE aid=$aid");
			DB::query("UPDATE {$this->table_place} SET ads=ads-1 WHERE pid=$a[pid]");
		}
	}

	function order_ad($listorder) {
		if(!is_array($listorder)) return false;
		foreach($listorder as $k=>$v) {
			$k = intval($k);
			$v = intval($v);
			DB::query("UPDATE {$this->table} SET listorder=$v WHERE aid=$k");
		}
		return true;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}

function ad_restore($string) {
	return str_replace(array('unio&#110;'), array('union'), $string);
}
?>