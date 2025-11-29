<?php
defined('IN_DESTOON') or exit('Access Denied');
$OAUTH = cache_read('oauth.php');
$site = 'msn';
define('OAUTH_LOGIN', (DT_TOUCH ? $MODULE[2]['mobile'] : $MODULE[2]['linkurl']).$DT['file_login']);
$OAUTH[$site]['enable'] or dheader(OAUTH_LOGIN);
$session = new dsession();

// Application Specific Globals
define('OAUTH_ID', $OAUTH[$site]['id']);
define('OAUTH_SECRET', $OAUTH[$site]['key']);
define('OAUTH_CALLBACK', DT_PATH.'api/oauth/'.$site.'/callback.php');

// Live URLs required for making requests.
define('OAUTH_CONSENT', 'https://login.live.com/oauth20_authorize.srf');
define('OAUTH_ACCESS', 'https://login.live.com/oauth20_token.srf');
define('OAUTH_USERINFO', 'https://apis.live.net/v5.0/me');
?>