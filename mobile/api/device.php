<?php
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
require DT_ROOT.'/include/post.func.php';
set_cookie('mobile', 'pc');
$uri = isset($uri) && is_url($uri) ? $uri : DT_PATH;
$head_title = $head_name = $L['device_title'];
if($sns_app) $seo_title = $site_name;;
$foot = '';
include template('device', 'mobile');
?>