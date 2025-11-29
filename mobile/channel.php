<?php
define('DT_REWRITE', true);
require '../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
$sid = $mid > 3 ? $mid : $MOB_MODULE[0]['moduleid'];
$head_title = $head_name = $L['channel_title'];
$foot = 'channel';
if($mid > 3) {
	$child = get_maincat(0, $mid, 1);
	$head_name = $MODULE[$mid]['name'].$L['cat_title'];
	$head_title = $MODULE[$mid]['name'].$L['cat_title'].$DT['seo_delimiter'].$L['channel_title'];
	$js_pull = 0;
	$foot = '';
}
include template('channel', 'mobile');
?>