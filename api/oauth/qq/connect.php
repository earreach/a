<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
$_SESSION['state'] = md5(uniqid(rand(), true));
dheader(OAUTH_CONNECT.'?response_type=code&client_id='.OAUTH_ID.'&redirect_uri='.urlencode(OAUTH_CALLBACK).'&state='.$_SESSION['state'].'&scope=get_user_info,add_t,add_pic_t,add_share');
?>