<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token'] && $_SESSION['openid']) {
	//https://open.douyin.com/platform/doc?doc=docs/openapi/account-management/get-account-open-info
	$par = 'access_token='.$_SESSION['access_token'].'&open_id='.$_SESSION['openid'];
	$rec = dcurl(OAUTH_USERINFO, $par);
	if(strpos($rec, 'nickname') !== false) {
		$success = 1;
		$arr = json_decode($rec, true);
		$arr = $arr['data'];
		$openid = $arr['open_id'];
		$nickname = $arr['nickname'];
		$avatar = $arr['avatar'];
		$gender = intval($arr['gender']);
		$city = $arr['city'];
		$province = $arr['province'];
		$country = $arr['country'];
		$url = '';
		$unionid = isset($arr['union_id']) ? $arr['union_id'] : '';
		$DS = array('access_token', 'openid');
	}
}
require '../user.inc.php';
?>