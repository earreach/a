<?php
defined('IN_DESTOON') or exit('Access Denied');
$v1 = intval($v1);
if($v1 > 0) {
	$time = $DT_TODAY - $v1*86400;
	$db->query("DELETE FROM {$DT_PRE}admin_log WHERE logtime<$time");
}
$v2 = intval($v2);
if($v2 > 0) {
	$time = $DT_TODAY - $v2*86400;
	$db->query("DELETE FROM {$DT_PRE}login WHERE logintime<$time");
}
$v3 = intval($v3);
if($v3 > 0) {
	$time = $DT_TODAY - $v3*86400;
	$db->query("DELETE FROM {$DT_PRE}404 WHERE addtime<$time");
}
?>