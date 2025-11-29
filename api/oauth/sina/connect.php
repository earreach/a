<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
//https://open.weibo.com/wiki/%E6%8E%88%E6%9D%83%E6%9C%BA%E5%88%B6
dheader(OAUTH_CONNECT.'?client_id='.OAUTH_ID.'&response_type=code&&redirect_uri='.urlencode(OAUTH_CALLBACK));
?>