<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/module/'.$module.'/member.class.php';
$do = new member;
$do->logout();
$session = new dsession();
session_destroy();
if($MOD['oauth']) {
	set_cookie('oauth_site', '');
	set_cookie('oauth_user', '');
}
if(strpos($forward, '/logout'.DT_EXT) !== false || strpos($forward, 'forward=') !== false) $forward = '';
$forward or $forward = $DT_PC ? DT_PATH : DT_MOB;
$action = 'logout';
$api_msg = $api_url = '';
if($MOD['passport']) {
	include DT_ROOT.'/api/'.$MOD['passport'].'.inc.php';
	if($api_url) $forward = $api_url;
}
#if($MOD['sso']) include DT_ROOT.'/api/sso.inc.php';
if($api_msg) message($api_msg, $forward, -1);
message($api_msg, $forward);
?>