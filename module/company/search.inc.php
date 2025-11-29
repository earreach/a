<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
if($DT['rewrite'] && $DT['search_rewrite'] && $_SERVER["REQUEST_URI"] && $_SERVER['QUERY_STRING'] && $job != 'ajax') {
	$_URL = rewrite($_SERVER["REQUEST_URI"]);
	if($_URL != $_SERVER["REQUEST_URI"]) dheader($_URL);
}
if(bansearch()) exit(include template('search', 'message'));
if($DT['max_search'] > 0 && $page > $DT['max_search']) $page = 1;
if($DT_PC) {
	if(!check_group($_groupid, $MOD['group_search'])) include load('403.inc');
	include load('search.lang');
	$FD = cache_read('fields-'.substr($table, strlen($DT_PRE)).'.php');
	$MS = cache_read('module-2.php');
	$modes = explode('|', $L['choose'].'|'.$MS['com_mode']);
	$types = explode('|', $L['choose'].'|'.$MS['com_type']);
	$sizes = explode('|', $L['choose'].'|'.$MS['com_size']);
	$vips = array($L['vip_level'], VIP, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
	$thumb = isset($thumb) ? intval($thumb) : 0;
	//$vip = isset($vip) ? intval($vip) : 0;
	$mincapital = isset($mincapital) ? dround($mincapital) : '';
	$mincapital or $mincapital = '';
	$maxcapital = isset($maxcapital) ? dround($maxcapital) : '';
	$maxcapital or $maxcapital = '';
	if(!$areaid && $cityid && strpos($DT_URL, 'areaid') === false) {
		$areaid = $cityid;
		$ARE = get_area($areaid);
	}
	$FD = cache_read('fields-'.substr($table, strlen($DT_PRE)).'.php');
	if($FD) require DT_ROOT.'/include/fields.func.php';
	$CP = $catid && $CAT['property'];
	if($CP) require DT_ROOT.'/include/property.func.php';
	$PP = $CP ? property_search_arr($catid) : array();
	$maincat = get_maincat(($CAT ? ($CAT['child'] ? $catid : $CAT['parentid']) : 0), $moduleid);
	$mainarea = get_mainarea($ARE ? ($ARE['child'] ? $areaid : $ARE['parentid']) : 0);
	isset($mode) && isset($modes[$mode]) or $mode = 0;
	isset($type) && isset($types[$type]) or $type = 0;
	isset($size) && isset($sizes[$size]) or $size = 0;
	isset($vip) && isset($vips[$vip]) or $vip = 0;
	$category_select = ajax_category_select('catid', $L['all_category'], $catid, $moduleid);
	$area_select = ajax_area_select('areaid', $L['all_area'], $areaid);
	$mode_select = dselect($modes, 'mode', '', $mode);
	$type_select = dselect($types, 'type', '', $type);
	$size_select = dselect($sizes, 'size', '', $size);
	$vip_select = dselect($vips, 'vip', '', $vip);
	$tags = array();
	if($DT_QST) {
		if($kw) {
			if(strlen($kw) < $DT['min_kw'] || strlen($kw) > $DT['max_kw']) message(lang($L['word_limit'], array($DT['min_kw'], $DT['max_kw'])), $MOD['linkurl'].'search.php');
			if($DT['search_limit'] && $page == 1) {
				if(($DT_TIME - $DT['search_limit']) < get_cookie('last_search')) message(lang($L['time_limit'], array($DT['search_limit'])), $MOD['linkurl'].'search.php');
				set_cookie('last_search', $DT_TIME);
			}
		}
		$fds = $MOD['fields'];
		$condition = "groupid IN (".($MOD['gids'] ? $MOD['gids'] : get_gids()).")";
		if($keyword) $condition .= match_kw('keyword', $keyword);
		if($mode) $condition .= match_kw('mode', $modes[$mode]);
		if($type) $condition .= " AND type='$types[$type]'";
		if($size) $condition .= " AND size='$sizes[$size]'";
		#if($catid) $condition .= " AND MATCH (catids) AGAINST ( ',".$catid.",')";
		if($catid) $condition .= " AND catids LIKE '%,".$catid.",%'";
		if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		if($thumb) $condition .= " AND thumb<>''";
		if($vip) $condition .= $vip == 1 ? " AND vip>0" : " AND vip=$vip-1";
		if($mincapital)  $condition .= " AND capital>$mincapital";
		if($maxcapital)  $condition .= " AND capital<$maxcapital";
		if($FD) $condition .= fields_search_sql();
		if($CP) $condition .= property_search_sql();
		$pagesize = $MOD['pagesize'];
		$offset = ($page-1)*$pagesize;
		$items = $db->count($table, $condition, $DT['cache_search']);
		$pages = pages($items, $page, $pagesize);
		if($items) {
			$order = $MOD['order'] ? " ORDER BY ".$MOD['order'] : '';
			$result = $db->query("SELECT {$fds} FROM {$table} WHERE {$condition}{$order} LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
			if($kw) {
				$replacef = explode(' ', $kw);
				$replacet = array_map('highlight', $replacef);
			}
			while($r = $db->fetch_array($result)) {
				if($lazy && isset($r['thumb']) && $r['thumb']) $r['thumb'] = DT_STATIC.'image/lazy.gif" original="'.$r['thumb'];
				if($kw) $r['company'] = str_replace($replacef, $replacet, $r['company']);
				$tags[] = $r;
			}
			$db->free_result($result);
		}
	}
	if($page == 1 && $kw && $DT['search_kw']) keyword($DT['search_kw'], $_username, $kw, $items, $moduleid);
	$showpage = 1;
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	if($kw) {
		check_group($_groupid, $MOD['group_search']) or message($L['msg_no_search']);
	} else if($catid) {
		$CAT or message($L['msg_not_cate']);
		if(!check_group($_groupid, $MOD['group_list']) || !check_group($_groupid, $CAT['group_list'])) message($L['msg_no_right']);
	} else {
		check_group($_groupid, $MOD['group_index']) or message($L['msg_no_right']);
	}
	$head_title = $MOD['name'].$DT['seo_delimiter'].$head_title;
	if($kw) $head_title = $kw.$DT['seo_delimiter'].$head_title;
	if(!$areaid && $cityid && strpos($DT_URL, 'areaid') === false) {
		$areaid = $cityid;
		$ARE = get_area($areaid);
	}
	$elite = isset($elite) ? intval($elite) : 0;
	(isset($orderby) && in_array($orderby, array('dhits', 'dcomments', 'dvip', 'avip'))) or $orderby = '';
	$tags = array();
	if($DT_QST) {
		$condition = "groupid IN (".($MOD['gids'] ? $MOD['gids'] : get_gids()).")";
		if($keyword) $condition .= match_kw('keyword', $keyword);
		if($catid) $condition .= " AND catids LIKE '%,".$catid.",%'";
		if($areaid) $condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		if($elite) $condition .= " AND level>0";
		$items = $db->count($table, $condition, $DT['cache_search']);
		$pages = mobile_pages($items, $page, $pagesize);
		if($items) {
			$order = $MOD['order'];
			if($orderby) $order = substr($orderby, 0, 1) == 'd' ? substr($orderby, 1).' DESC' : substr($orderby, 1).' ASC';
			$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
			while($r = $db->fetch_array($result)) {
				if($kw) $r['company'] = str_replace($kw, '<span class="f_red">'.$kw.'</span>', $r['company']);
				$tags[] = $r;
			}
			$db->free_result($result);
			$js_load = preg_replace("/(.*)([&?]page=[0-9]*)(.*)/i", "\\1\\3", rewrite($DT_URL, 1)).'&job=ajax';
		}
		if($page == 1 && $kw && $DT['search_kw']) keyword($DT['search_kw'], $_username, $kw, $items, $moduleid);
	}
	if($job == 'ajax') {
		if($tags) include template('list-'.$module, 'tag');
		exit;
	}
	$head_title = $MOD['name'].$L['search'];
}
$seo_file = 'search';
include DT_ROOT.'/include/seo.inc.php';
include template($MOD['template_search'] ? $MOD['template_search'] : 'search', $module);
?>