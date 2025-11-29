<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$dorder  = array('addtime DESC', 'price DESC', 'price ASC', 'item DESC', 'item ASC', 'edittime DESC', 'edittime ASC', 'hits DESC', 'hits ASC');
isset($order) && isset($dorder[$order]) or $order = 0;
$condition = "1";
if($keyword) $condition .= match_kw('title', $keyword);
if($catid) $condition .= $CAT['child'] ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
$items = $db->count($table_product, $condition, $DT['cache_search']);
$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
$lists = array();
$result = $db->query("SELECT * FROM {$table_product} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
while($r = $db->fetch_array($result)) {
	$r['mobile'] = $MOD['mobile'].rewrite('price'.DT_EXT.'?itemid='.$r['itemid']);
	$r['linkurl'] = $MOD['linkurl'].rewrite('price'.DT_EXT.'?itemid='.$r['itemid']);
	$lists[] = $r;
}
$head_title = $L['product_title'].$DT['seo_delimiter'].$MOD['name'];
if($catid) $head_title = $CAT['catname'].$DT['seo_delimiter'].$head_title;
if($kw) $head_title = $kw.$DT['seo_delimiter'].$head_title;
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	$tags = $lists;
	if($job == 'ajax') {
		if($tags) include template('list-quote-product', 'tag');
		exit;
	}
	$js_load = $MOD['mobile'].'product'.DT_EXT.'?job=ajax';
	$head_name = $L['product_title'];
	if($sns_app) $seo_title = $MOD['name'];
}
include template($MOD['template_product'] ? $MOD['template_product'] : 'product', $module);
?>