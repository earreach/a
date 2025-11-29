<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
$_COOKIE = array();
require '../common.inc.php';
function get_express_home($name) {
	$name = strtolower($name);
	if(strpos($name, '顺丰') !== false) return 'https://www.sf-express.com/cn/sc/';
	if(strpos($name, '京东') !== false) return 'https://www.jdl.com/';
	return '';
}
function get_express_code($name) {
	$name = strtolower($name);
	if(strpos($name, '顺丰') !== false) return 'shunfeng';
	if(strpos($name, '京东') !== false) return 'jingdong';
	return '';
}
$e = isset($e) ? trim($e) : '';
$n = isset($n) ? trim($n) : '';
if($action == 'home') {
	if($e) {
		$u = get_express_home($e);
		$c = get_express_code($e);
		if($u) {
			if($c == 'shunfeng') dheader($u.'dynamic_function/waybill/#search/bill-number/'.$n);
			if($c == 'jingdong') dheader($u.'order/search?waybillCodes='.$n);
			dheader($u.'?no='.$n);
		}
	}	
}
dheader('https://www.baidu.com/s?wd='.($e ? urlencode($e).' ' : '').$n);
?>