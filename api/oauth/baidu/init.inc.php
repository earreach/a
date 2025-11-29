<?php
defined('IN_DESTOON') or exit('Access Denied');
$OAUTH = cache_read('oauth.php');
$site = 'baidu';
define('OAUTH_LOGIN', (DT_TOUCH ? $MODULE[2]['mobile'] : $MODULE[2]['linkurl']).$DT['file_login']);
$OAUTH[$site]['enable'] or dheader(OAUTH_LOGIN);
$session = new dsession();

define('OAUTH_ID', $OAUTH[$site]['id']);
define('OAUTH_SECRET', $OAUTH[$site]['key']);
define('OAUTH_CALLBACK', DT_PATH.'api/oauth/'.$site.'/callback.php');
define('OAUTH_CONNECT', 'https://openapi.baidu.com/oauth/2.0/authorize');
define('OAUTH_TOKEN', 'https://openapi.baidu.com/oauth/2.0/token');
define('OAUTH_USERINFO', 'https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser');//https://openapi.baidu.com/rest/2.0/passport/users/getInfo
?>