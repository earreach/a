<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('模板管理', '?file=template'),
    array('风格管理', '?file=skin'),
    array('标签向导', '?file=tag'),
);
if(!isset($CFG['edittpl']) || !$CFG['edittpl']) msg('系统禁止了在线修改模板，请FTP修改根目录config.inc.php<br/>$CFG[\'edittpl\'] = \'0\'; 修改为 $CFG[\'edittpl\'] = \'1\';');
$CFG['editfile'] = 2;
include DT_ROOT.'/module/destoon/admin/file.inc.php';
?>