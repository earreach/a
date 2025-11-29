<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {
	$par = 'access_token='.$_SESSION['access_token'];
	$rec = dcurl(OAUTH_ME, $par);
	//https://wiki.connect.qq.com/openapi%e8%b0%83%e7%94%a8%e8%af%b4%e6%98%8e_oauth2-0
	//https://wiki.connect.qq.com/get_user_info
	if(strpos($rec, 'client_id') !== false) {
		$rec = str_replace('callback(', '', $rec);
		$rec = str_replace(');', '', $rec);
		$rec = trim($rec);
		$arr = json_decode($rec, true);
		$openid = $arr['openid'];		
		$par = 'access_token='.$_SESSION['access_token'].'&oauth_consumer_key='.OAUTH_ID.'&openid='.$openid;
		$rec = dcurl(OAUTH_USERINFO, $par);
		if(strpos($rec, 'nickname') !== false) {
			$success = 1;
			$arr = json_decode($rec, true);
			$nickname = $arr['nickname'];
			$avatar = $arr['figureurl_2'];
			$gender = $arr['gender'] == '女' ? 2 : 1;
			$city = '';
			$province = '';
			$country = '';
			$url = '';
			$unionid = '';
			$DS = array('access_token', 'state');
		}
	}
}
require '../user.inc.php';
?>