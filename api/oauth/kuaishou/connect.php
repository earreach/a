<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
$_SESSION['state'] = md5(uniqid(rand(), true));
//https://open.kuaishou.com/platform/openApi?group=GROUP_OPEN_PLATFORM&menu=12
dheader(OAUTH_CONNECT.'?response_type=code&scope=user_info&app_id='.OAUTH_ID.'&redirect_uri='.urlencode(OAUTH_CALLBACK).'&state='.$_SESSION['state']);
?>