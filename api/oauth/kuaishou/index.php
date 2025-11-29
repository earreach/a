<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token'] && $_SESSION['openid']) {
	//https://open.kuaishou.com/platform/openApi?group=GROUP_OPEN_PLATFORM&menu=17
	$par = 'access_token='.$_SESSION['access_token'].'&app_id='.OAUTH_ID;
	$rec = dcurl(OAUTH_USERINFO, $par);
	if(strpos($rec, 'user_info') !== false) {
		$success = 1;
		$arr = json_decode($rec, true);
		$arr = $arr['user_info'];
		$openid = $_SESSION['openid'];
		$nickname = $arr['name'];
		$avatar = $arr['head'];
		$gender = $arr['sex'] == 'M' ? 1 : 2;
		$city = '';
		$province = $arr['city'];
		$country = '';
		$url = '';
		$unionid = '';
		$DS = array('access_token', 'openid');
	}
}
require '../user.inc.php';
?>