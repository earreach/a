<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('分类管理', '?file='.$file),
);
switch($action) {
	case 'cache':
	break;
	default:
		include tpl('cate');
	break;
}
?>