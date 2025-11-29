<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/extend/spider.class.php';
$sp = new spider();
$table = $DT_PRE.'spider';
$r = $db->get_one("SELECT * FROM {$table} WHERE setting<>'' ORDER BY lasttime ASC");
if($r) {
	$itemid = $r['itemid'];
	$setting = unserialize($r['setting']);
	$config = $setting[0];
	$list_url = $r['linkurl'];
	$list_html = $sp->dcurl($list_url, $config);
	$lists = $sp->get_url($list_html, $config);
	$sp->save_list($lists, $itemid);
}
$num = intval($v1);
$num > 0 or $num = 10;
$result = $db->query("SELECT * FROM {$table}_url WHERE status=0 ORDER BY itemid ASC LIMIT 0,$num");
while($r = $db->fetch_array($result)) {
	$sp->save_show($r);
}
?>