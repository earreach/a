<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function tag($parameter, $expires = -1) {
	global $CFG, $MODULE, $DT, $DT_PC;
	$tag_expires = $expires > -1 ? $expires : $CFG['db_expires'];
	$parameter = str_replace(array('&amp;', '%'), array('', '##'), $parameter);
	parse_str($parameter, $par);
	if(!is_array($par)) return '';
	$par = dstripslashes($par);
	extract($par, EXTR_SKIP);
	isset($prefix) or $prefix = DT_PRE;
	isset($moduleid) or $moduleid = 1;
	if(!isset($MODULE[$moduleid])) return '';
	isset($fields) or $fields = '*';
	isset($catid) or $catid = 0;
	isset($child) or $child = 1;
	isset($areaid) or $areaid = 0;
	isset($areachild) or $areachild = 1;
	isset($itemid) or $itemid = 0;
	(isset($dir) && check_name($dir)) or $dir = 'tag';
	(isset($template) && check_name($template)) or $template = 'list';
//    var_dump($template);die();
	isset($condition) or $condition = '1';
	isset($group) or $group = '';
	isset($page) or $page = 1;
	isset($offset) or $offset = 0;
	isset($pagesize) or $pagesize = 10;
	isset($order) or $order = '';
	isset($showpage) or $showpage = 0;
	isset($showcat) or $showcat = 0;
	isset($datetype) or $datetype = 0;
	isset($target) or $target = '';
	isset($class) or $class = '';
	isset($length) or $length = 1;
	isset($introduce) or $introduce = 0;
	isset($debug) or $debug = 0;
	isset($lazy) or $lazy = 0;
	(isset($cols) && $cols) or $cols = 1;
	isset($sql) or $sql = '';
	if($sql && strtoupper(substr($sql, 0, 7)) != 'SELECT ') $sql = '';
	if($sql && stripos($sql, 'LIMIT') === false) $sql = '';
	$pages = '';
	if($sql) {
		$query = $sql;
		$showpage = 0;
	} else {
		if($catid) {
			if($moduleid > 4) {
				if(is_numeric($catid)) {
					$CAT = DB::get_one("SELECT child,arrchildid,moduleid FROM ".DT_PRE."category WHERE catid={$catid}");
					$condition .= ($child && $CAT['child'] && $CAT['moduleid'] == $moduleid) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid={$catid}";
				} else {
					if($child) {
						$catids = '';
						$result = DB::query("SELECT arrchildid FROM ".DT_PRE."category WHERE catid IN ({$catid})");
						while($r = DB::fetch_array($result)) {
							$catids .= ','.$r['arrchildid'];
						}
						if($catids) $catid = substr($catids, 1);
					}
					$condition .= " AND catid IN ($catid)";
				}
			} else if($moduleid == 4) {
				$condition .= " AND catids LIKE '%,$catid,%'";
			}
		}
		if($areaid) {
			if(is_numeric($areaid)) {
				$ARE = DB::get_one("SELECT child,arrchildid FROM ".DT_PRE."area WHERE areaid=$areaid");
				$condition .= ($areachild && $ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
			} else {
				if($areachild) {
					$areaids = '';
					$result = DB::query("SELECT arrchildid FROM ".DT_PRE."area WHERE areaid IN ({$areaid})");
					while($r = DB::fetch_array($result)) {
						$areaids .= ','.$r['arrchildid'];
					}
					if($areaids) $areaid = substr($areaids, 1);
				}
				$condition .= " AND areaid IN ({$areaid})";
			}
		}
		$table = isset($table) ? $prefix.$table : get_table($moduleid);	
		$offset or $offset = ($page-1)*$pagesize;
		$percent = dround(100/$cols).'%';
		$num = 0;
		$order = $order ? ' ORDER BY '.$order : '';
		$condition = stripslashes($condition);
		$condition = str_replace('##', '%', $condition);
		if($showpage) {
			$num = DB::count($table, $condition, $tag_expires);
			$pages = $catid ? listpages(get_cat($catid), $num, $page, $pagesize) : pages($num, $page, $pagesize);
		} else {
			if($group) $condition .= ' GROUP BY '.$group;
		}
		$query = "SELECT ".$fields." FROM ".$table." WHERE ".$condition.$order." LIMIT ".$offset.",".$pagesize;
	}
	$tags = $catids = $CATS = array();
	$result = DB::query($query, $tag_expires > 0 ? 'CACHE' : '', $tag_expires);
	while($r = DB::fetch_array($result)) {
		if($itemid) {
			if(isset($r['itemid']) && $r['itemid'] == $itemid) continue;
		}
		if($moduleid == 4 && isset($r['company'])) {
			$r['alt'] = $r['title'] = $r['companyname'] = $r['company'];
			if($length > 1) $r['company'] = dsubstr($r['company'], $length);
		}
		if(isset($r['title'])) {
			$r['title'] = str_replace('"', '&quot;', trim($r['title']));
			$r['alt'] = $r['title'];
			if($length > 1) $r['title'] = dsubstr($r['title'], $length);
			if(isset($r['style']) && $r['style']) $r['title'] = set_style($r['title'], $r['style']);
		}
		if($lazy && isset($r['thumb']) && $r['thumb']) $r['thumb'] = DT_STATIC.'image/lazy.gif" class="lazy" original="'.$r['thumb'];
		if(isset($r['thumb']) && isset($width) && $width > 100) $r['thumb'] = str_replace('.thumb.', '.middle.', $r['thumb']);
		if(isset($r['introduce']) && $introduce) $r['introduce'] = dsubstr($r['introduce'], $introduce);
		if(isset($r['linkurl']) && $r['linkurl'] && $moduleid > 4) {
			if($DT_PC) {
				if(strpos($r['linkurl'], '://') === false) $r['linkurl'] = $MODULE[$moduleid]['linkurl'].$r['linkurl'];
			} else {
				$r['linkurl'] = strpos($r['linkurl'], '://') === false ? $MODULE[$moduleid]['mobile'].$r['linkurl'] : moburl($r['linkurl']);
			}
		}
		if($showcat && $moduleid > 4 && isset($r['catid'])) $catids[$r['catid']] = $r['catid'];
		$tags[] = $r;
	}
	if($showcat && $moduleid > 4 && $catids) {
		$result = DB::query("SELECT catid,catname,linkurl FROM ".DT_PRE."category WHERE catid IN (".implode(',', $catids).")");
		while($r = DB::fetch_array($result)) {
			$CATS[$r['catid']] = $r;
		}
		if($CATS) {
			foreach($tags as $k=>$v) {
				$tags[$k]['catname'] = $v['catid'] ? $CATS[$v['catid']]['catname'] : '';
				$tags[$k]['caturl'] = $v['catid'] ? ($DT_PC ? $MODULE[$moduleid]['linkurl'] : $MODULE[$moduleid]['mobile']).$CATS[$v['catid']]['linkurl'] : '';
			}
		}
	}
	if($debug) {
		echo '<b>Tag:</b><br/>'.$parameter.'<br/><b>Sql:</b><br/>'.$query.'<br/><b>Result:</b><br/>';
		print_r($tags);
		echo '<br/>';
	}
	if($template == 'null') {
		if($pages && $tags) $tags[0]['pages'] = $pages;
		return $tags;
	}
	include template($template, $dir);
}
?>