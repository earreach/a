<?php
require '../../../common.inc.php';
require 'init.inc.php';
$_REQUEST['code'] or dalert('Error Code', OAUTH_LOGIN.'?step=callback&site='.$site);
#https://open.dingtalk.com/document/isvapp/obtain-user-token
$headers = array();
$headers[] = "Content-Type:application/json";
$par = array(
	'clientId' => OAUTH_ID,
	'clientSecret' => OAUTH_SECRET, 
	'code' => $_REQUEST['code'],
	'grantType' => 'authorization_code',
);
$rec = dcurl(OAUTH_TOKEN, json_encode($par), $headers);
if(strpos($rec, 'accessToken') !== false) {
	$arr = json_decode($rec, true);
	$_SESSION['access_token'] = $arr['accessToken'];
	dheader('index.php?time='.$DT_TIME);
} else {
	dalert('Error Token', OAUTH_LOGIN.'?step=token&site='.$site);
}
?>