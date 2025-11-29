<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$sso_url = trim($MOD['passport_url']);
$sso_key = trim($MOD['passport_key']);
if($action == 'login' || $action == 'oauth') {
	if($sso_url && $sso_key && is_array($user) && (($user['mobile'] && $user['vmobile']) || ($user['email'] && $user['vemail']))) {
		$sso_data = $action.'|'.$DT_TIME.'|'.$user['username'].'|'.$user['passport'].'|'.$user['mobile'].'|'.$user['vmobile'].'|'.$user['email'].'|'.$user['vemail'].'|'.$user['groupid'].'|'.$user['truename'].'|'.$user['company'].'|'.$user['password'].'|'.$user['passsalt'].'|'.$user['payword'].'|'.$user['paysalt'];
		$sso_auth = encrypt($sso_data, $sso_key, 300);
		foreach(explode('|', $sso_url) as $sso_uri) {
			if(substr($sso_uri, -1) != '/') $sso_uri = $sso_uri.'/';
			if($sso_uri == DT_PATH) continue;
			$api_msg .= '<script type="text/javascript" src="'.$sso_uri.'api/sso'.DT_EXT.'?auth='.$sso_auth.'"></script>';
		}
	}
} else if($action == 'logout') {
	if($sso_url && $sso_key) {
		$sso_data = $action.'|'.$DT_TIME;
		$sso_auth = encrypt($sso_data, $sso_key, 300);
		foreach(explode('|', $sso_url) as $sso_uri) {
			if(substr($sso_uri, -1) != '/') $sso_uri = $sso_uri.'/';
			if($sso_uri == DT_PATH) continue;
			$api_msg .= '<script type="text/javascript" src="'.$sso_uri.'api/sso'.DT_EXT.'?auth='.$sso_auth.'"></script>';
		}
	}
}
?>