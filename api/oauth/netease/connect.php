<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
dheader(OAUTH_CONNECT.'?response_type=code&client_id='.OAUTH_ID.'&redirect_uri='.urlencode(OAUTH_CALLBACK));
?>