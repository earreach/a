<?php
require '../../../common.inc.php';
require 'init.inc.php';
$_REQUEST['code'] or dalert('Error Code', OAUTH_LOGIN.'?step=callback&site='.$site);
$par = 'grant_type=authorization_code'
	 . '&code='.$_REQUEST['code']
	 . '&app_id='.OAUTH_ID
	 . '&app_secret='.OAUTH_SECRET;
$rec = dcurl(OAUTH_TOKEN, $par);
//https://open.kuaishou.com/platform/openApi?group=GROUP_OPEN_PLATFORM&menu=13
if(strpos($rec, 'access_token') !== false) {
	$arr = json_decode($rec, true);
	$_SESSION['access_token'] = $arr['access_token'];
	$_SESSION['openid'] = $arr['open_id'];
	dheader('index.php?time='.$DT_TIME);
} else {
	dalert('Error Token', OAUTH_LOGIN.'?step=token&site='.$site);
}
?>