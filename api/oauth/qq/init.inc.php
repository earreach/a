<?php
defined('IN_DESTOON') or exit('Access Denied');
$OAUTH = cache_read('oauth.php');
$site = 'qq';
define('OAUTH_LOGIN', (DT_TOUCH ? $MODULE[2]['mobile'] : $MODULE[2]['linkurl']).$DT['file_login']);
$OAUTH[$site]['enable'] or dheader(OAUTH_LOGIN);
$session = new dsession();
define('OAUTH_ID', $OAUTH[$site]['id']);
define('OAUTH_SECRET', $OAUTH[$site]['key']);
define('OAUTH_CALLBACK', DT_PATH.'api/oauth/'.$site.'/callback.php');
define('OAUTH_CONNECT', 'https://graph.qq.com/oauth2.0/authorize');
define('OAUTH_TOKEN', 'https://graph.qq.com/oauth2.0/token');
define('OAUTH_ME', 'https://graph.qq.com/oauth2.0/me');
define('OAUTH_USERINFO', 'https://graph.qq.com/user/get_user_info');
?>