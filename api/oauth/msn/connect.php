<?php
require '../../../common.inc.php';
require 'init.inc.php';
set_cookie('bind', '');
dheader(OAUTH_CONSENT.'?client_id='.OAUTH_ID.'&scope=wl.signin%20wl.basic&response_type=code&redirect_uri='.urlencode(OAUTH_CALLBACK));
?>