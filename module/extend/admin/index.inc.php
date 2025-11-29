<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('扩展功能', '?moduleid='.$moduleid),
);
include DT_ROOT.'/module/'.$module.'/admin/menu.inc.php';
include tpl('index', $module);
?>