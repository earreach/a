<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$typeid = isset($typeid) ? intval($typeid) : 0;
$condition = "status=3 AND sprice>0";
if($keyword) $condition .= match_kw('title', $keyword);
if($catid) $condition .= $CAT['child'] ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
if($typeid == 1) {
	$condition .= " AND sfromtime>$DT_TIME";
} else {
	$condition .= " AND sfromtime<$DT_TIME AND stotime>$DT_TIME";
}
$target = '_blank';
$items = $db->count($table, $condition, $DT['cache_search']);
$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
$tags = array();
$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
while($r = $db->fetch_array($result)) {
	$r['thumb'] = str_replace('.thumb.', '.middle.', $r['thumb']);
	$r['alt'] = $r['title'];
	$r['title'] = set_style($r['title'], $r['style']);
	$tags[] = $r;
}
$head_title = $L['sec_title'].$DT['seo_delimiter'].$MOD['name'];
if($catid) $head_title = $CAT['catname'].$DT['seo_delimiter'].$head_title;
if($kw) $head_title = $kw.$DT['seo_delimiter'].$head_title;
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	if($job == 'ajax') {
		if($tags) include template('list-mall-sec', 'tag');
		exit;
	}
	$js_load = $MOD['mobile'].'sec'.DT_EXT.'?job=ajax&typeid='.$typeid;
	$head_name = $L['sec_title'];
	if($sns_app) $seo_title = $MOD['name'];
}
include template($MOD['template_sec'] ? $MOD['template_sec'] : 'sec', $module);
?>