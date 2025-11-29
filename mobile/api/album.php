<?php
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
login();
$head_title = $head_name = $L['album_title'];
if($sns_app) $seo_title = $site_name;;
$foot = '';
include template('album', 'mobile');
?>