<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {	
	$url = OAUTH_USERINFO.'?access_token='.$_SESSION['access_token'];
	$rec = dcurl($url);
	//https://reg.163.com/help/help_oauth2.html
	if(strpos($rec, 'userId') !== false) {
		$success = 1;
		$arr = json_decode($rec, true);
		$openid = $arr['userId'];
		$nickname = isset($arr['username']) ? $arr['username'] : $arr['userId'];
		$avatar = '';
		$gender = 0;
		$city = '';
		$province = '';
		$country = '';
		$url = '';
		$unionid = '';
		$DS = array('access_token');
	}
}
require '../user.inc.php';
?>