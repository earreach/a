<?php
defined('IN_DESTOON') or exit('Access Denied');
if(isset($charge_title)) $charge_title = dsubstr(str_replace('|', '', $charge_title), '40', '...');
if($EXT['weixin'] && in_array($DT_MBS, array('weixin', 'wxmini'))) {
	$openid = '';
	if($DT_MBS == 'wxmini') {
		$openid = get_cookie('mini_openid');
		if($openid) $openid = decrypt($openid, DT_KEY.'WXID');
		if(is_openid($openid)) dheader(DT_PATH.'api/pay/weixin/miniapi.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP.'|'.$openid, DT_KEY.'MINIPAY'));
	} else {
		$t = $db->get_one("SELECT openid FROM {$DT_PRE}weixin_user WHERE username='$_username'");
		if($t) {
			$openid = $t['openid'];
		} else {
			$openid = get_cookie('weixin_openid');
			if($openid) $openid = decrypt($openid, DT_KEY.'WXID');
		}
		$t = explode('MicroMessenger/', DT_UA);
		if(intval($t[1]) >= 5) {
			if(is_openid($openid)) {
				dheader(DT_PATH.'api/pay/weixin/jsapi.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP.'|'.$openid, DT_KEY.'JSPAY'));
			} else {
				dheader(DT_MOB.'api/weixin.php?url='.urlencode(DT_PATH.'api/pay/weixin/openid.php?itemid='.$orderid));
			}
		}
	}
}
if($DT_PC) dheader(DT_PATH.'api/pay/weixin/qrcode.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP, DT_KEY.'QRPAY'));
dheader(DT_PATH.'api/pay/weixin/h5api.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP, DT_KEY.'H5PAY'));
?>