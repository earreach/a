<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
$_SERVER['REQUEST_URI'] = '';
require '../common.inc.php';
header("Content-type:text/javascript");
check_referer() or exit('document.write("Invalid Referer");');
$tag = isset($auth) ? decrypt($auth) : '';
if(strpos($tag, '=') === false || strpos($tag, '&') === false) exit('document.write("Invalid Parameter");');
if(strpos($tag, 'table=') === false && strpos($tag, 'moduleid=') === false) exit('document.write("Invalid Parameter");');
$tag = strip_sql($tag);
foreach(array($DT_PRE, '#', '$', '%', '&amp;', 'table', 'fields', 'password', 'payword', 'debug') as $v) {
	strpos($tag, $v) === false or exit('document.write("Invalid Tag");');
}
ob_start();
tag($tag);
$data = ob_get_contents();
ob_clean();
echo 'document.write(\''.dwrite($data ? $data : 'No Data').'\');';
?>