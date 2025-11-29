<?php
/*
	[DESTOON B2B System] Copyright (c) 2008-2018 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
switch($DT['ftptype']) {
    case '1':
        require DT_ROOT.'/include/aliossftp.class.php';
        break;
    default:
        require DT_ROOT.'/include/dtftp.class.php';
        break;
}
?>