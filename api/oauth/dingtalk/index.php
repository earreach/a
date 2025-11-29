<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {
	#https://open.dingtalk.com/document/isvapp/dingtalk-retrieve-user-information
    $headers = array();
    $headers[] = "x-acs-dingtalk-access-token:".$_SESSION['access_token'];
    $headers[] = "Content-Type:application/json";
	$rec = dcurl(OAUTH_USERINFO, '', $headers);
	if(strpos($rec, 'nick') !== false) {
		$success = 1;
		$arr = json_decode($rec, true);
		$openid = $arr['openId'];
		$nickname = $arr['nick'];
		$avatar = $arr['avatarUrl'];
		$mobile = $arr['mobile'];
		$email = $arr['email'];
		$city = '';
		$province = '';
		$country = '';
		$url = '';
		$unionid = isset($arr['unionId']) ? $arr['unionId'] : '';
		$DS = array('access_token');
	}
}
require '../user.inc.php';
?>