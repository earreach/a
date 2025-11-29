<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
require '../common.inc.php';
if($DT_BOT) dhttp(403);
if($action != 'mobile') {
	check_referer() or exit;
}
require DT_ROOT.'/include/mobile.inc.php';
require DT_ROOT.'/include/post.func.php';
@include DT_ROOT.'/api/ajax/'.$action.'.inc.php';
?>