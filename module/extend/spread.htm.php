<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$itemid) return false;
$item = $db->get_one("SELECT * FROM {$DT_PRE}spread WHERE itemid=$itemid");
if(!$item) return false;

$filename = DT_CACHE.'/htm/m'.urlencode($item['mid']).'_k'.urlencode($item['word']).'.htm';
if($DT_TIME - @filemtime($filename) < 60) return false;

$totime = 0;
$itemids = array();
$result = $db->query("SELECT * FROM {$DT_PRE}spread WHERE status=3 AND mid=$item[mid] AND word='$item[word]' AND fromtime<=$DT_TIME AND totime>=$DT_TIME ORDER BY price DESC,itemid ASC");
while($r = $db->fetch_array($result)) {
	if($r['totime'] > $totime) $totime = $r['totime'];
	$itemids[] = $r['tid'];
}
if(!$itemids) {
	file_del($filename);
	file_del(substr($filename, 0, -4).'_m.htm');
	return false;
}
$DT_PC = $GLOBALS['DT_PC'] = 1;
$spread_itemids = implode(',', $itemids);
$spread_moduleid = $item['mid']; 
$spread_module = $MODULE[$spread_moduleid]['module'];
$id = $spread_moduleid == 4 ? 'userid' : 'itemid';
$bmid = $moduleid;
$moduleid = $spread_moduleid;
if(!isset($MODULE[$moduleid])) {
	file_del($filename);
	file_del(substr($filename, 0, -4).'_m.htm');
	return false;
}
$pages = '';
$datetype = 5;
$showpage = 0;
$tags = $tag = array();
$result = $db->query("SELECT * FROM ".get_table($moduleid)." WHERE `{$id}` IN ($spread_itemids)");
while($r = $db->fetch_array($result)) {
	if($moduleid == 4) {
		$r['alt'] = $r['company'];
		$r['company'] = '<s class="adname">'.$L['ad_sign'].'</s>'.$r['company'];
	} else {
		$r['alt'] = $r['title'];
		$r['title'] = set_style($r['title'], $r['style']);
		$r['title'] = '<s class="adname">'.$L['ad_sign'].'</s>'.$r['title'];
		if(strpos($r['linkurl'], '://') === false) $r['linkurl'] = $MODULE[$spread_moduleid]['linkurl'].$r['linkurl'];
	}
	$tag[$r[$id]] = $r;
}
if(!$tag) {
	file_del($filename);
	file_del(substr($filename, 0, -4).'_m.htm');
	return false;
}
$spread_url = $EXT['spread_url'].rewrite('index'.DT_EXT.'?kw='.urlencode($item['word']));
foreach($itemids as $v) {//Order
	if(isset($tag[$v])) $tags[] = $tag[$v];
}
ob_start();
include template('spread', 'chip');
$data = ob_get_contents();
ob_clean();
if($data) file_put($filename, '<!--'.$totime.'-->'.$data);
if($EXT['mobile_enable']) {
	$spread_url = $EXT['spread_mob'].rewrite('index'.DT_EXT.'?kw='.urlencode($item['word']));
	$filename = substr($filename, 0, -4).'_m.htm';
	include DT_ROOT.'/include/mobile.htm.php';
	foreach($tags as $i=>$t) {
		$tags[$i]['linkurl'] = moburl($t['linkurl'], $spread_moduleid);
	}
	ob_start();
	include template('spread', 'chip');
	$data = ob_get_contents();
	ob_clean();
	if($data) file_put($filename, '<!--'.$totime.'-->'.$data);
}
$moduleid = $bmid;
return true;
?>