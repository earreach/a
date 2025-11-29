<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
$_COOKIE = array();
require '../common.inc.php';
$url = DT_PATH;
if($wd) $url = 'https://www.tianyancha.com/search?key='.urlencode(strip_tags($wd));
dheader($url);
?>