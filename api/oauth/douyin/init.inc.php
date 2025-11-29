<?php
defined('IN_DESTOON') or exit('Access Denied');
$OAUTH = cache_read('oauth.php');
$site = 'douyin';
define('OAUTH_LOGIN', (DT_TOUCH ? $MODULE[2]['mobile'] : $MODULE[2]['linkurl']).$DT['file_login']);
$OAUTH[$site]['enable'] or dheader(OAUTH_LOGIN);
$session = new dsession();

define('OAUTH_ID', $OAUTH[$site]['id']);
define('OAUTH_SECRET', $OAUTH[$site]['key']);
define('OAUTH_CALLBACK', DT_PATH.'api/oauth/'.$site.'/callback.php');
define('OAUTH_CONNECT', 'https://open.douyin.com/platform/oauth/connect/');
define('OAUTH_TOKEN', 'https://open.douyin.com/oauth/access_token/');
define('OAUTH_USERINFO', 'https://open.douyin.com/oauth/userinfo/');
?>