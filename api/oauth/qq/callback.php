<?php
require '../../../common.inc.php';
require 'init.inc.php';
$_REQUEST['code'] or dalert('Error Code', OAUTH_LOGIN.'?step=callback&site='.$site);
$_REQUEST['state'] == $_SESSION['state'] or dalert('Error State', OAUTH_LOGIN.'?step=state&site='.$site);
$par = 'grant_type=authorization_code'
	 . '&client_id='.OAUTH_ID
	 . '&client_secret='.OAUTH_SECRET
	 . '&code='.$_REQUEST['code']
	 . '&redirect_uri='.urlencode(OAUTH_CALLBACK);
$rec = dcurl(OAUTH_TOKEN, $par);
if(strpos($rec, 'access_token') !== false) {
	parse_str($rec, $arr);
	$_SESSION['access_token'] = $arr['access_token'];
	dheader('index.php?time='.$DT_TIME);
} else {
	dalert('Error Token', OAUTH_LOGIN.'?step=token&site='.$site);
}
?>