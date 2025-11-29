<?php
defined('IN_DESTOON') or exit('Access Denied');
function nexttime($schedule, $time) {
	if(strpos($schedule, ',') !== false) {
		list($h, $m) = explode(',', $schedule);
		$t = datetotime(timetodate($time, 3).' '.($h < 10 ? '0'.$h : $h).':'.($m < 10 ? '0'.$m : $m).':00');
		return $t > $time ? $t : $t + 86400;
	} else {
		$m = intval($schedule);
		return $time + ($m ? $m : 1800)*60 + mt_rand(10, 20);
	}
}
$result = $db->query("SELECT * FROM {$DT_PRE}cron WHERE status=0 AND nexttime<$DT_TIME ORDER BY nexttime");
while($cron = $db->fetch_array($result)) {
	$nexttime = nexttime($cron['schedule'], $DT_TIME);
	$db->query("UPDATE {$DT_PRE}cron SET lasttime=$DT_TIME,nexttime=$nexttime WHERE itemid=$cron[itemid]");
	$v1 = $cron['v1'];
	$v2 = $cron['v2'];
	$v3 = $cron['v3'];
	include DT_ROOT.'/api/cron/'.$cron['name'].'.inc.php';
}
if($DT['message_email'] && $DT['mail_type'] != 'close' && $_groupid < 5 && $action != 'cron') include DT_ROOT.'/api/cron/message.php';
?>