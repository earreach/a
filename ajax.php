<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
#define('DT_ADMIN', true);
require 'common.inc.php';
if($DT_BOT) dhttp(403);
if($action != 'mobile') {
	//check_referer() or exit;
}
// var_dump($action);

require DT_ROOT.'/include/post.func.php';
// die();
@include DT_ROOT.'/api/ajax/'.$action.'.inc.php';

?>