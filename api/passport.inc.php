<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$api_msg = '';
if(in_array($MOD['passport'], array('sso', 'uc'))) {
	$action = 'oauth';
	$passport = $user['passport'];
	include DT_ROOT.'/api/'.$MOD['passport'].'.inc.php';
}
if($api_msg) {
	if(in_array($DT_MBS, array('weixin', 'wxmini'))) {
		preg_match_all("/src=\"([^\"]+)\"/i", $api_msg, $matches);
		if($matches) {
			$api_img = '';
			foreach($matches[1] as $src) {
				$api_img .= '<img src="'.$src.'"/>';
			}
			if($api_img) message($api_img, $forward, -1);
		}
	}
	message($api_msg, $forward, -1);
}
?>