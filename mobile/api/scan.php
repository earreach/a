<?php
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
$content = isset($content) ? trim($content) : '';
$url = is_url($content) ? $content : '';
if($url && strpos($url, DT_MOB) !== false) dheader($url);
$head_title = '扫一扫';
$head_name = $head_title;
$foot = '';
if($sns_app) $seo_title = $site_name;
include template('scan', 'mobile');
?>