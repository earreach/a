<?php
require '../../../common.inc.php';
require 'init.inc.php';
$OAUTH[$site]['sync'] or exit;
$_token = get_cookie('sina_token');
$_token or exit;
isset($auth) or exit;
$d = decrypt($auth, DT_KEY.'SYNC');
strpos($d, '-') !== false or exit;
$t = explode('-', $d);
$mid = intval($t[0]);
isset($MODULE[$mid]) or exit;
$itemid = intval($t[1]);
$itemid > 0 or exit;
if($mid == 2) {
	$item = $db->get_one("SELECT title,thumb,linkurl,addtime,status FROM {$DT_PRE}news WHERE itemid=$itemid");
	if($item) $item['introduce'] = '';
} else if($mid > 4) {
	$item = $db->get_one("SELECT title,thumb,introduce,linkurl,addtime,status FROM ".get_table($mid)." WHERE itemid=$itemid");
} else {
	exit;
}
$item or exit;
$DT_TIME - $item['addtime'] < 30 or exit;
$item['status'] == 3 or exit;
$o = new SaeTClientV2(OAUTH_ID, OAUTH_SECRET, $_token);
$rec = $thumb ? $o->upload($content, $thumb) : $o->update($content);
#log_write($rec, 'wb', 1);
if(isset($rec['error_code']) && $rec['error_code'] > 0) {
	//fail
} else {
	//success
}
?>