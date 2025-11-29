<?php
defined('IN_DESTOON') or exit('Access Denied');
$v1 = intval($v1);
if($v1 > 0) {
	$time = $DT_TODAY - $v1*86400;
	$db->query("DELETE FROM {$DT_PRE}finance_sms WHERE addtime<$time");
}
$v2 = intval($v2);
if($v2 > 0) {
	$time = $DT_TODAY - $v2*86400;
	$db->query("DELETE FROM {$DT_PRE}sms WHERE sendtime<$time");
}
?>