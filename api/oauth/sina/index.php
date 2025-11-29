<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['token']) {
	//https://open.weibo.com/wiki/2/users/show
	$rec = dcurl(OAUTH_USERINFO.'?access_token='.$_SESSION['token'].'&uid='.$_SESSION['sina_uid']);
	if(strpos($rec, 'screen_name') !== false) {
		$arr = json_decode($rec, true);
		$success = 1;
		$openid = $arr['id'];
		$nickname = $arr['screen_name'];
		$avatar = $arr['avatar_large'];
		$gender = $arr['gender'] == 'm' ? 1 : 2;
		$city = cutstr($arr['location'], ' ', '');
		$province = cutstr($arr['location'], '', ' ');
		$country = '';
		$url = $arr['profile_url'] ? 'https://weibo.com/'.$arr['profile_url'] : '';
		$unionid = '';
		$DS = array('token', 'sina_uid');
	}
}
require '../user.inc.php';
?>