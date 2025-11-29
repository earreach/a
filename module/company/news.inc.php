<?php 
defined('IN_DESTOON') or exit('Access Denied');
$table = $DT_PRE.'news';
$table_data = $DT_PRE.'news_data';
if($action == 'company') {
	$condition = "status=3";
	if($keyword) $condition .= match_kw('title', $keyword);
	$items = $db->count($table, $condition, $DT['cache_search']);
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$lists = array();
	if($items) {
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}", ($CFG['db_expires'] && $page <= $DT['cache_page']) ? 'CACHE' : '', $CFG['db_expires']);
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['date'] = timetodate($r['addtime'], 3);
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($DT_PC) {
		if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
		$CSS = array('article');
	} else {
		$tags = $lists;
		$js_item = 1;
	}
	$head_title = $L['news_title'].$DT['seo_delimiter'].$MOD['name'];
	if($kw) $head_title = $kw.$DT['seo_delimiter'].$head_title;
	include template('news', $module);
	exit;
}
$TYPE = get_type('news-'.$userid);
$_TP = sort_type($TYPE);
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if(!$item || $item['status'] < 3 || $item['username'] != $username) dheader($MENU[$menuid]['linkurl']);
	require DT_ROOT.'/include/content.class.php';
	extract($item);
	$t = $db->get_one("SELECT content FROM {$table_data} WHERE itemid=$itemid");
	$content = $t['content'];
	$content = DC::format($content, $DT_PC);
	if(!$DT_BOT) $db->query("UPDATE LOW_PRIORITY {$table} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
	$head_title = $title.$DT['seo_delimiter'].$head_title;
	$head_keywords = $title.','.$COM['company'];
	$head_description = get_intro($content, 200);
	if($DT_PC) {
		//
	} else {
		if($typeid) $head_name = $TYPE[$typeid]['typename'];
		$foot = 'news';
	}
} else {
	$typeid = isset($typeid) ? intval($typeid) : 0;
	$url = "file=$file";
	$condition = "username='$username' AND status=3";
	if($kw) {
		$condition .= match_kw('title', $keyword);
		$url .= "&kw=$kw";
		$head_title = $kw.$DT['seo_delimiter'].$head_title;
	}
	if($typeid) {
		$condition .= " AND typeid='$typeid'";
		$url .= "&typeid=$typeid";
		$head_title = $TYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
	}
	$demo_url = userurl($username, $url.'&page={destoon_page}', $domain);

	$pagesize = intval($MENU[$menuid]['pagesize']);
	if(!$pagesize || $pagesize > 100) $pagesize = 30;

	$offset = ($page-1)*$pagesize;
	$items = $db->count($table, $condition, $DT['cache_search']);
	$pages = $DT_PC ? home_pages($items, $page, $pagesize, $demo_url) : mobile_pages($items, $page, $pagesize, $demo_url);
	$lists = array();
	if($items) {
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['date'] = timetodate($r['addtime'], 3);
			$r['linkurl'] = userurl($username, "file=$file&itemid=$r[itemid]", $domain);
			if($kw) $r['title'] = str_replace($kw, '<span class="f_red">'.$kw.'</span>', $r['title']);
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($DT_PC) {
		//
	} else {
		$tags = $lists;
		if($typeid) $head_name = $TYPE[$typeid]['typename'];
		$foot = 'news';
	}
}
include template('news', $template);
?>