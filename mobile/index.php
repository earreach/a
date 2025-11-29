<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
define('DT_REWRITE', true);
require '../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
if($moduleid > 3) m301($moduleid, $catid, $itemid, $page);
require DT_ROOT.'/module/'.$module.'/index.inc.php';
?>