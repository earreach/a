<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {
	$par = 'access_token='.$_SESSION['access_token']
		 . '&openid='.$_SESSION['openid'];
	$rec = dcurl(OAUTH_USERINFO, $par);
	//https://developers.weixin.qq.com/doc/oplatform/Website_App/WeChat_Login/Authorized_Interface_Calling_UnionID.html
	if(strpos($rec, 'nickname') !== false) {
		$success = 1;
		$arr = json_decode($rec, true);
		$openid = $arr['openid'];
		$nickname = $arr['nickname'];
		$avatar = $arr['headimgurl'];
		$gender = intval($arr['sex']);
		$city = $arr['city'];
		$province = $arr['province'];
		$country = $arr['country'];
		$url = '';
		$unionid = isset($arr['unionid']) ? $arr['unionid'] : '';
		$DS = array('access_token', 'openid');
	}
}
require '../user.inc.php';
?>