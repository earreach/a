<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function cache_all() {
	cache_module();
	cache_area();
    cache_shuxing();
	cache_category();
	cache_fields();
	cache_group();
	cache_grade();
	cache_oauth();
	cache_type();
	cache_keylink();
	cache_pay();
	cache_weixin();
	cache_css();
	cache_bansearch();
	return true;
}

function cache_module($moduleid = 0) {
	if(defined('DT_MOB')) {
		$DT_MOB = DT_MOB;
	} else {
		$r = DB::get_one("SELECT * FROM ".DT_PRE."setting WHERE item_key='mobile_domain'");
		if($r && $r['item_value']) {
			$DT_MOB = $r['item_value'];
		} else {
			$DT_MOB = DT_PATH.'mobile/';
		}
	}
	if($moduleid) {
		$r = DB::get_one("SELECT * FROM ".DT_PRE."module WHERE disabled=0 AND moduleid='$moduleid'");
		$setting = array();
		$setting = get_setting($moduleid);
		if(isset($setting['seo_title_index'])) $setting['title_index'] = seo_title($setting['seo_title_index']);
		if(isset($setting['seo_title_list'])) $setting['title_list'] = seo_title($setting['seo_title_list']);
		if(isset($setting['seo_title_show'])) $setting['title_show'] = seo_title($setting['seo_title_show']);
		if(isset($setting['seo_title_search'])) $setting['title_search'] = seo_title($setting['seo_title_search']);
		if(isset($setting['seo_keywords_index'])) $setting['keywords_index'] = seo_title($setting['seo_keywords_index']);
		if(isset($setting['seo_keywords_list'])) $setting['keywords_list'] = seo_title($setting['seo_keywords_list']);
		if(isset($setting['seo_keywords_show'])) $setting['keywords_show'] = seo_title($setting['seo_keywords_show']);
		if(isset($setting['seo_keywords_search'])) $setting['keywords_search'] = seo_title($setting['seo_keywords_search']);
		if(isset($setting['seo_description_index'])) $setting['description_index'] = seo_title($setting['seo_description_index']);
		if(isset($setting['seo_description_list'])) $setting['description_list'] = seo_title($setting['seo_description_list']);
		if(isset($setting['seo_description_show'])) $setting['description_show'] = seo_title($setting['seo_description_show']);
		if(isset($setting['seo_description_search'])) $setting['description_search'] = seo_title($setting['seo_description_search']);
		//cache_write('setting/module-'.$moduleid.'.php', $setting);
		$setting['moduleid'] = $moduleid;
		$setting['name'] = $r['name'];
		$setting['moduledir'] = $r['moduledir'];
		$setting['module'] = $r['module'];
		$setting['ismenu'] = $r['ismenu'];
		$setting['domain'] = $r['domain'];
		$setting['icon'] = $r['icon'];
		$setting['linkurl'] = $r['linkurl'];
		$setting['mobile'] = $r['mobile'] ? $r['mobile'] : $DT_MOB.$r['moduledir'].'/';
		if($moduleid > 1) $setting['logo'] = $r['logo'];
		if($moduleid == 3) {
			foreach($setting as $k=>$v) {
				if(strpos($k, '_domain') !== false) {
					$e = str_replace('_domain', '', $k);
					$key = $e.'_url';
					$setting[$key] = $v ? $v : DT_PATH.$e.'/';
					$key = $e.'_mob';
					$setting[$key] = $DT_MOB.$e.'/';
				}
			}
		}
		if(isset($setting['split'])) {			
			if($setting['split']) {
				$setting['fulltext'] = 0;
				cache_write($moduleid.'.part', $moduleid);
			} else {
				cache_delete($moduleid.'.part');
			}
		}
		cache_write('module-'.$moduleid.'.php', $setting);
		return true;
	} else {
		$result = DB::query("SELECT moduleid,module,name,moduledir,domain,linkurl,mobile,style,listorder,islink,ismenu,isblank,logo,icon FROM ".DT_PRE."module WHERE disabled=0 ORDER by listorder asc,moduleid desc");
		$CACHE = $modules = $home = $menu = $side = $main = array();
		$searchid = 0;
		global $CFG;
		while($r = DB::fetch_array($result)) {
			if($r['islink']) {
				$r['mobile'] = $r['linkurl'];
			} else {
				$linkurl = $r['domain'] ? $r['domain'] : linkurl($r['moduledir'].'/');
				if($r['moduleid'] == 1) $linkurl = DT_PATH;
				if($linkurl != $r['linkurl']) {
					$r['linkurl'] = $linkurl;
					DB::query("UPDATE ".DT_PRE."module SET linkurl='$linkurl' WHERE moduleid='$r[moduleid]' ");
				}
				$mobile = $DT_MOB.($r['moduledir'] ? $r['moduledir'].'/' : '');
				if(substr_count($r['mobile'], '/') != 3) {
					$r['mobile'] = $mobile;
					DB::query("UPDATE ".DT_PRE."module SET mobile='$mobile' WHERE moduleid='$r[moduleid]' ");
				}
				if($searchid == 0 && $r['ismenu']) $searchid = $r['moduleid'];
				cache_module($r['moduleid']);
			}
			$modules[$r['moduleid']] = $r;
			if(is_file(DT_ROOT.'/module/company/'.$r['module'].'.inc.php')) $menu[$r['moduledir']] = $r['moduleid'];
        }
		$CACHE['module'] = $modules;
		$CACHE['dt'] = cache_read('module-1.php');
		$CACHE['dt']['searchid'] = $searchid;
		cache_write('module.php', $CACHE);
		$dir = DT_ROOT.'/template/'.$CFG['template'].'/homepage';
		if(!is_file($dir.'/these.name.php') && $CFG['template'] != 'default' && is_file(DT_ROOT.'/template/default/homepage/these.name.php')) $dir = DT_ROOT.'/template/default/homepage';
		if(is_dir($dir)) {
			include $dir.'/these.name.php';
			foreach(glob($dir.'/*.htm') as $v) {
				$k = basename($v, '.htm');
				if($k == 'channel') continue;
				if(strpos($k, 'side_') !== false) {
					$side[substr($k, 5)] = isset($names[$k]) ? cutstr($names[$k], '-') : substr($k, 5);
				} else if(strpos($k, 'main_') !== false) {
					$main[substr($k, 5)] = isset($names[$k]) ? cutstr($names[$k], '-') : substr($k, 5);
				} else {
					if(is_dir(DT_ROOT.'/company/'.$k) && !is_dir(DT_ROOT.'/module/'.$k)) $menu[$k] = isset($names[$k]) ? $names[$k] : $k;
				}
			}
		}
		cache_write('home.php', array('menu' => $menu, 'side' => $side, 'main' => $main));
	}
}

function cache_area() {
	$data = array();
    $result = DB::query("SELECT areaid,areaname,parentid,arrparentid,child,arrchildid FROM ".DT_PRE."area ORDER BY listorder,areaid");
    while($r = DB::fetch_array($result)) {
		$areaid = $r['areaid'];
        $data[$areaid] = $r;
    }
	cache_write('area.php', $data);
}
function cache_shuxing() {
    global $module,$moduleid;
    $data = array();
    $sql="SELECT shuxingid,shuxingname,parentid,arrparentid,child,arrchildid FROM ".DT_PRE.$module."_shuxing_".$moduleid." ORDER BY listorder,shuxingid";
// 	die(var_dump(2222));
    $result = DB::query($sql);
    while($r = DB::fetch_array($result)) {
        $shuxingid = $r['shuxingid'];
        $data[$shuxingid] = $r;
    }
    cache_write('shuxing.php', $data);
}
function cache_category($moduleid = 0, $data = array()) {
	global $MODULE;
	if($moduleid) {
		if(!$data) {
			$result = DB::query("SELECT * FROM ".DT_PRE."category WHERE moduleid='$moduleid' ORDER BY listorder,catid");
			while($r = DB::fetch_array($result)) {
				$data[$r['catid']] = $r;
			}
		}
		$mod = cache_read('module-'.$moduleid.'.php');
		$a = array();
		$d = array('listorder', 'moduleid', 'item', 'template', 'show_template', 'seo_title', 'seo_keywords', 'seo_description', 'group_list', 'group_show', 'group_add');
		foreach($data as $r) {
			$e = $r['catid'];
			foreach($d as $_d) {
				unset($r[$_d]);
			}
			$a[$e] = $r;
		};
		cache_write('category-'.$moduleid.'.php', $a);
		if(count($data) < 100) {
			$categorys = array();
			foreach($data as $id=>$cat) {
				$categorys[$id] = array('id'=>$id, 'parentid'=>$cat['parentid'], 'name'=>$cat['catname']);
			}
			require_once DT_ROOT.'/include/tree.class.php';
			$tree = new tree;
			$tree->tree($categorys);
			$content = $tree->get_tree(0, "<option value=\\\"\$id\\\">\$spacer\$name</option>").'</select>';
			cache_write('catetree-'.$moduleid.'.php', $content);
		} else {
			cache_delete('catetree-'.$moduleid.'.php');
		}
	} else {
		foreach($MODULE as $moduleid=>$module) {
			cache_category($moduleid);
		}
	}
}

function cache_pay() {
	$setting = $order = $pay = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."setting WHERE item LIKE '%pay-%'");
	while($r = DB::fetch_array($result)) {
		if(substr($r['item'], 0, 4) == 'pay-') {
			$setting[substr($r['item'], 4)][$r['item_key']] = $r['item_value'];
			if($r['item_key'] == 'order') $order[substr($r['item'], 4)] = $r['item_value'];
		}
	}
	asort($order);
	foreach($order as $k=>$v) {
		$pay[$k] = $setting[$k];
	}
	cache_write('pay.php', $pay);
}

function cache_css() {
	$css = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."setting WHERE item='css'");
	while($r = DB::fetch_array($result)) {
		$css[$r['item_key']] = $r['item_value'];
	}
	cache_write('css.php', $css);
}

function cache_oauth() {
	$setting = $order = $oauth = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."setting WHERE item LIKE '%oauth-%'");
	while($r = DB::fetch_array($result)) {
		if(substr($r['item'], 0, 6) == 'oauth-') {
			$setting[substr($r['item'], 6)][$r['item_key']] = $r['item_value'];
			if($r['item_key'] == 'order') $order[substr($r['item'], 6)] = $r['item_value'];
		}
	}
	asort($order);
	foreach($order as $k=>$v) {
		$oauth[$k] = $setting[$k];
	}
	cache_write('oauth.php', $oauth);
}

function cache_fields($tb = '') {
	if($tb) {
		$data = array();
		$result = DB::query("SELECT * FROM ".DT_PRE."fields WHERE tb='$tb' ORDER BY listorder,itemid");
		while($r = DB::fetch_array($result)) {
			$data[$r['itemid']] = $r;
		}
		cache_write('fields-'.$tb.'.php', $data);
	} else {
		$tbs = array();
		$result = DB::query("SELECT * FROM ".DT_PRE."fields");
		while($r = DB::fetch_array($result)) {
			if(isset($tbs[$r['tb']])) continue;
			cache_fields($r['tb']);
			$tbs[$r['tb']] = $r['tb'];
		}
	}
}

function cache_group() {
	$data = $group = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."member_group ORDER BY listorder ASC,groupid ASC");
	while($r = DB::fetch_array($result)) {
		$groupid = $r['groupid'];
		$tmp = array();
		$tmp = get_setting('group-'.$groupid);
		$r['reg'] = $tmp['reg'];
		$r['fee'] = $tmp['fee'];
		$r['type'] = $tmp['type'];
		$r['grade'] = $tmp['grade'];
		$data[$groupid] = $r;
		if($tmp) {
			foreach($tmp as $k=>$v) {
				isset($r[$k]) or $r[$k] = $v;
			}
		}
		$r['groupid'] = $groupid;		
		$r['fee_12'] = $r['fee'];
		cache_write('group-'.$groupid.'.php', $r);
	}
	foreach($data as $k=>$v) {
		$home = array();
		$result = DB::query("SELECT * FROM ".DT_PRE."member_home WHERE groupid=$k AND status>0 ORDER BY listorder ASC");
		while($r = DB::fetch_array($result)) {
			$home[$r['type']][$r['file']]['name'] = $r['name'];
			$home[$r['type']][$r['file']]['pagesize'] = $r['pagesize'];
			$home[$r['type']][$r['file']]['listorder'] = $r['listorder'];
			$home[$r['type']][$r['file']]['status'] = $r['status'] == 2 ? 1 : 0;
		}
		cache_write('home-'.$k.'.php', $home);
	}
	cache_write('group.php', $data);
}

function cache_grade() {
	$data = $grade = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."member_grade ORDER BY gradeid ASC");
	while($r = DB::fetch_array($result)) {
		$gradeid = $r['gradeid'];
		$tmp = array();
		$tmp = get_setting('grade-'.$gradeid);
		$data[$gradeid] = $r;
		if($tmp) {
			foreach($tmp as $k=>$v) {
				isset($r[$k]) or $r[$k] = $v;
			}
		}
		$r['gradeid'] = $gradeid;
		cache_write('grade-'.$gradeid.'.php', $r);
	}
	cache_write('grade.php', $data);
}

function cache_type($item = '') {
	if($item != '') {
		$types = array();
		$result = DB::query("SELECT typeid,parentid,typename,style FROM ".DT_PRE."type WHERE item='$item' AND cache=1 ORDER BY listorder ASC,typeid DESC");
		while($r = DB::fetch_array($result)) {
			$types[$r['typeid']] = $r;
		}
		cache_write('type-'.$item.'.php', $types);
		return $types;
	} else {
		$arr = array();
		$result = DB::query("SELECT item FROM ".DT_PRE."type WHERE item!='' AND cache=1 ORDER BY typeid DESC");
		while($r = DB::fetch_array($result)) {
			if(!in_array($r['item'], $arr)) {
				$arr[] = $r['item'];
				cache_type($r['item']);
			}
		}
	}
}

function cache_bancomment($moduleid = 0) {
	global $MODULE;
	if($moduleid) {
		$data = array();
		$result = DB::query("SELECT itemid FROM ".DT_PRE."comment_ban WHERE moduleid='$moduleid' ORDER BY bid DESC ");
		while($r = DB::fetch_array($result)) {
			$data[] = $r['itemid'];
		}
		cache_write('bancomment-'.$moduleid.'.php', $data);
		return $data;
	} else {
		foreach($MODULE as $k=>$v) {
			if($k < 4 || $v['islink']) continue;
			cache_bancomment($k);
		}
	}
}

function cache_keylink($item = '') {
	if($item) {
		$keylinks = array();
		$result = DB::query("SELECT title,url FROM ".DT_PRE."keylink WHERE item='$item' ORDER BY listorder DESC,itemid DESC");
		while($r = DB::fetch_array($result)) {
			$keylinks[] = $r;
		}
		cache_write('keylink-'.$item.'.php', $keylinks);
		return $keylinks;
	} else {
		$arr = array();
		$result = DB::query("SELECT item FROM ".DT_PRE."keylink");
		while($r = DB::fetch_array($result)) {
			if(!in_array($r['item'], $arr)) {
				$arr[] = $r['item'];
				cache_keylink($r['item']);
			}
		}
	}
}

function cache_banip() {
	$data = array();
	$result = DB::query("SELECT ip,totime FROM ".DT_PRE."banip ORDER BY itemid DESC");
	while($r = DB::fetch_array($result)) {
		if($r['totime'] && $r['totime'] < DT_TIME) continue;
		$data[] = $r;
	}
	cache_write('banip.php', $data);
}

function cache_banword() {
	$data = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."banword ORDER BY bid DESC");
	while($r = DB::fetch_array($result)) {
		$data[] = array($r['replacefrom'], $r['replaceto'], $r['deny']);
	}
	cache_write('banword.php', $data);
}

function cache_bansearch() {
	global $MODULE;
	$data = array();
	$result = DB::query("SELECT moduleid,word FROM ".DT_PRE."keyword WHERE status=1 ORDER BY itemid DESC");
	while($r = DB::fetch_array($result)) {
		$data[$r['moduleid']][] = $r['word'];
	}
	foreach($MODULE as $k=>$v) {
		if($k < 4 || $v['islink']) continue;
		$mid = $v['moduleid'];
		if(isset($data[$mid])) {
			cache_write('bansearch-'.$mid.'.php', $data[$mid]);
		} else {
			cache_delete('bansearch-'.$mid.'.php');
		}
	}
}

function cache_weixin() {
	$setting = get_setting('weixin');
	cache_write('weixin.php', $setting);
	$setting = get_setting('weixin-menu');
	cache_write('weixin-menu.php', $setting['menu'] ? unserialize($setting['menu']) : array());
}

function cache_clear_htm($pre = 'ad', $all = false) {
	$globs = glob(DT_CACHE.'/htm/*.htm');
	if($globs) {
		foreach($globs as $v) {
			if(substr(basename($v), 0, strlen($pre)) != $pre) continue;
			if($all) {
				file_del($v);
			} else {
				$exptime = intval(substr(file_get($v), 4, 14));
				if($exptime && DT_TIME > $exptime) file_del($v);
			}
		}
	}
}

function cache_clear_sql($dir, $all = false) {
	if($dir) {
		$globs = glob(DT_CACHE.'/sql/'.$dir.'/*.php');
		if($globs) {
			foreach($globs as $v) {
				if($all) {
					file_del($v);
				} else {
					$exptime = intval(substr(file_get($v), 8, 18));
					if($exptime && DT_TIME > $exptime) file_del($v);
				}
			}
		}
	} else {
		cache_clear('php', 'dir', 'sql');
	}
}
?>