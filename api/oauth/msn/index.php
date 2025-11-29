<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {
	$url = OAUTH_USERINFO.'?access_token='.$_SESSION['access_token'];
	$rec = dcurl($url);
	$arr = json_decode($rec, true);
	if(isset($arr['id'])) {
		$success = 1;
		$openid = $arr['id'];
		if($arr['first_name']) {
			$nickname = $arr['first_name'];
		} else {
			$nickname = $arr['emails']['account'];
			$nickname = str_replace(strstr($nickname, '@'), '', $nickname);
		}
		$avatar = '';
		$gender = 0;
		$city = '';
		$province = '';
		$country = '';
		$url = $arr['link'];
		$unionid = '';
		$DS = array('access_token');
	}
}
require '../user.inc.php';
?>