<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$username = isset($username) ? trim($username) : '';
check_name($username) or $username = '';
$wid = isset($wid) ? trim($wid) : '';
is_wx($wid) or $wid = '';
$gzh = isset($gzh) ? trim($gzh) : '';
is_wx($gzh) or $gzh = '';
$wx = $wid ? $wid : $gzh;
$wxqr = '';
$user = $username ? userinfo($username) : array();
if($user) {
	if($gzh) {
		if($user['gzh']) $wx = $user['gzh'];
		if($user['gzhqr']) $wxqr = $user['gzhqr'];
		$head_title = $L['gzh_title'];
	} else {
		if($user['wx']) $wx = $user['wx'];
		if($user['wxqr']) $wxqr = $user['wxqr'];
		$head_title = $L['wx_title'];
	}
}
$template = 'wx';
$head_name = $head_title;
$head_keywords = $head_description = '';
if($DT_PC) {	
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace(DT_PATH, DT_MOB, $DT_URL);
} else {
	$foot = '';
	if($sns_app) $seo_title = $site_name;
}
include template($template, $module);
?>