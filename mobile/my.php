<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
$moduleid = 2;
require '../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
if($action == 'login') {
	dheader($MODULE[2]['mobile'].$DT['file_login'].'?forward='.urlencode($forward));
} elseif($action == 'oauth') {
	set_cookie('oauth_site', '');
	set_cookie('oauth_user', '');
	dheader('?reload='.$DT_TIME);
}
$oauth_site = $oauth_user = '';
if(!$_userid) {
	$oauth_site = get_cookie('oauth_site');
	if($oauth_site) $oauth_user = get_cookie('oauth_user');
}
$head_title = $head_name = $L['my_title'];
$js_pull = 0;
$foot = 'my';
include template('my', 'mobile');
?>