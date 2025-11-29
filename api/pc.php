<?php
$moduleid = 3;
require '../common.inc.php';
#if(stripos(DT_UA, 'LT_PC') === false) exit('Not in desktop');
$url = '';
if($action == 'biz') {
	if($MG['biz']) $url = $MODULE[2]['linkurl'].'biz'.DT_EXT;
} else if($action == 'member') {
	if($_userid) $url = $MODULE[2]['linkurl'];
} else if($action == 'login') {
	if(!$_userid) $url = $MODULE[2]['linkurl'].$DT['file_login'];
} else if($action == 'register') {
	if(!$_userid) $url = $MODULE[2]['linkurl'].$DT['file_register'];
} else if($action == 'home') {
	$url = DT_PATH;
}
if($url) {
	set_cookie('mobile', 'desktop', 86400*30);
	dheader($url);
}
include template('pc', $module);
?>