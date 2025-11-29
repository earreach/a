<?php
defined('IN_DESTOON') or exit('Access Denied');
$v1 = intval($v1);
if($v1 > 0) {
	$time = $DT_TODAY - $v1*86400;
	$db->query("DELETE FROM {$DT_PRE}message WHERE isread=1 AND addtime<$time");
	$db->query("DELETE FROM {$DT_PRE}message WHERE status IN (2,4) AND addtime<$time");
}
?>