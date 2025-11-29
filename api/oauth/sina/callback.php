<?php
require '../../../common.inc.php';
require 'init.inc.php';
$_REQUEST['code'] or dalert('Error Code', OAUTH_LOGIN.'?step=callback&site='.$site);
$par = 'grant_type=authorization_code'
	 . '&client_id='.OAUTH_ID
	 . '&client_secret='.OAUTH_SECRET
	 . '&code='.$_REQUEST['code']
	 . '&redirect_uri='.urlencode(OAUTH_CALLBACK);
$rec = dcurl(OAUTH_TOKEN, $par);
if(strpos($rec, 'access_token') !== false) {
	$arr = json_decode($rec, true);
	$_SESSION['token'] = $arr['access_token'];
	$_SESSION['sina_uid'] = $arr['uid'];
	if($OAUTH[$site]['sync']) set_cookie('sina_token', $arr['access_token'], $DT_TIME + $arr['expires_in']);
	dheader('index.php?time='.$DT_TIME);
} else {
	dalert('Error Token', OAUTH_LOGIN.'?step=token&site='.$site);
}
?>