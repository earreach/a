<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
foreach($DS as $v) {
	$_SESSION[$v] = '';
}
$murl = DT_TOUCH ? $MODULE[2]['mobile'] : $MODULE[2]['linkurl'];
if($success) {
	include DT_ROOT.'/module/member/oauth.class.php';
	$oa = new oauth($site);
	$U = $oa->userinfo($openid);
	if($U) {		
		$post = array();
		if($_username && $U['username'] != $_username) $U['username'] = $post['username'] = $_username;
		foreach(array('nickname', 'gender', 'city', 'province', 'country', 'avatar', 'url', 'unionid') as $v) {
			if($U[$v] != $$v && $$v) $post[$v] = $$v;
		}
		$oa->update($post);
		$oa->login($U);
	}
	if($_userid) dheader($murl.'oauth.php');
	set_cookie('bind', encrypt($oa->itemid.'|'.$site.'|'.$nickname, DT_KEY.'BIND'));
	set_cookie('oauth_site', $site);
	set_cookie('oauth_user', $nickname);
	dheader($murl.'oauth.php?action=bind');
}
dheader(OAUTH_LOGIN.'?error=oauth&step=userinfo&site='.$site);
?>