<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
$_SESSION['state'] = md5(uniqid(rand(), true));
//https://open.douyin.com/platform/doc?doc=docs/openapi/account-permission/douyin-get-permission-code
dheader(OAUTH_CONNECT.'?response_type=code&scope=user_info&client_key='.OAUTH_ID.'&redirect_uri='.urlencode(OAUTH_CALLBACK).'&state='.$_SESSION['state']);
?>