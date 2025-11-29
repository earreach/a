<?php
require '../../../common.inc.php';
require 'init.inc.php';
$_REQUEST['code'] or dalert('Error Code', OAUTH_LOGIN.'?step=callback&site='.$site);
$par = 'client_id='.urlencode(OAUTH_ID)
	 . '&redirect_uri='.urlencode(OAUTH_CALLBACK)
	 . '&client_secret='.urlencode(OAUTH_SECRET)
	 . '&code='.urlencode($_REQUEST['code'])
	 . '&grant_type=authorization_code';
$rec = dcurl(OAUTH_ACCESS, $par);
if(strpos($rec, 'access_token') !== false) {
	$arr = json_decode($rec, true);
	$_SESSION['access_token'] = $arr['access_token'];
	dheader('index.php?time='.$DT_TIME);
} else {
	dalert('Error Token', OAUTH_LOGIN.'?step=token&site='.$site);
}
?>