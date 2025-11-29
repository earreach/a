<?php
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
require DT_ROOT.'/include/post.func.php';
$minprice = isset($minprice) ? dround($minprice) : '';
$minprice or $minprice = '';
$maxprice = isset($maxprice) ? dround($maxprice) : '';
$maxprice or $maxprice = '';
$head_title = $head_name = $L['search_title'];
$sid = $mid > 3 ? $mid : $MOB_MODULE[0]['moduleid'];
$mod = $MODULE[$sid]['module'];
if($sns_app) $seo_title = $site_name;;
$foot = '';
include template('search', 'mobile');
?>