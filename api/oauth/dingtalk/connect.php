<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
$_SESSION['state'] = md5(uniqid(rand(), true));
#https://open.dingtalk.com/document/orgapp-server/use-dingtalk-account-to-log-on-to-third-party-websites-1
dheader(OAUTH_CONNECT.'?response_type=code&scope=openid&prompt=consent&client_id='.OAUTH_ID.'&redirect_uri='.urlencode(OAUTH_CALLBACK).'&state='.$_SESSION['state']);
?>