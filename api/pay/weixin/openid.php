<?php
require '../../../common.inc.php';
($EXT['weixin'] && in_array($DT_MBS, array('weixin', 'wxmini'))) or dheader(DT_MOB);
$_userid or dheader(DT_MOB);
$itemid or dheader(DT_MOB);
$t = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE itemid=$itemid");
($t && $t['username'] == $_username && $t['status'] == 0) or dheader(DT_MOB);
$orderid = $t['itemid'];
$charge_title = $_username.'('.$orderid.')';
$t = $db->get_one("SELECT openid FROM {$DT_PRE}weixin_user WHERE username='$_username'");
if($t && is_openid($t['openid'])) {
	$openid = $t['openid'];
	dheader(DT_PATH.'api/pay/weixin/jsapi.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP.'|'.$openid, DT_KEY.'JSPAY'));
}
dheader(DT_PATH.'api/pay/weixin/qrcode.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP, DT_KEY.'QRPAY'));
?>