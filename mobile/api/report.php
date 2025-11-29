<?php
$moduleid = 3;
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
$EXT['guestbook_report'] or message($L['closed'], $DT_PC ? DT_PATH : DT_MOB, 6);
$action = 'add';
$report = 1;
$content = isset($content) ? stripslashes($content) : '';
if($content) $content = strip_tags($content);
require DT_ROOT.'/module/'.$module.'/guestbook.inc.php';
?>