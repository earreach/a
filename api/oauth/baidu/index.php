<?php
require '../../../common.inc.php';
require 'init.inc.php';
$success = 0;
$DS = array();
if($_SESSION['access_token']) {
	$par = 'access_token='.$_SESSION['access_token'];
	$rec = dcurl(OAUTH_USERINFO, $par);
	//https://openauth.baidu.com/doc/doc.html
	//https://developer.baidu.com/wiki/index.php?title=%E5%B8%AE%E5%8A%A9%E6%96%87%E6%A1%A3%E9%A6%96%E9%A1%B5/web%E5%BA%94%E7%94%A8%E6%8E%A5%E5%85%A5/%E7%94%A8%E6%88%B7%E8%B4%A6%E6%88%B7%E6%8E%A5%E5%85%A5#.E7.AC.AC.E5.9B.9B.E6.AD.A5.EF.BC.9A.E8.8E.B7.E5.8F.96.E7.94.A8.E6.88.B7.E5.9F.BA.E6.9C.AC.E4.BF.A1.E6.81.AF
	if(strpos($rec, 'uname') !== false) {
		$success = 1;
		$arr = json_decode($rec, true);
		$openid = $arr['uid'];
		$nickname = $arr['uname'];
		$avatar = '';
		$gender = intval($arr['gender']);
		$city = '';
		$province = '';
		$country = '';
		$url = '';
		$unionid = isset($arr['unionid']) ? $arr['unionid'] : '';
		$DS = array('access_token');
	}
}
require '../user.inc.php';
?>