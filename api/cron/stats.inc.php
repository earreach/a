<?php
defined('IN_DESTOON') or exit('Access Denied');
$tb_pv = DT_PRE.'stats_pv';
$tb_uv = DT_PRE.'stats_uv';
$time = DT_TIME;
$i = 0;
while($i++ < 3) {
	$date = timetodate($time, 3);
	$id = str_replace('-', '', $date);
	$ftime = datetotime($date.' 00:00:00');
	$ttime = datetotime($date.' 23:59:59');
	$t = $db->get_one("SELECT id FROM {$DT_PRE}stats WHERE id='$id'");
	if($t && $i > 1) {
		//
	} else {
		$ip = $ip_pc = $ip_mb = $pv = $pv_pc = $pv_mb = $rb = $rb_pc = $rb_mb = 0;
		$pv = $db->count($tb_pv, "addtime>=$ftime AND addtime<=$ttime");
		if($pv) {
			$pv_pc = $db->count($tb_pv, "addtime>=$ftime AND addtime<=$ttime AND pc=1");
			$pv_mb = $pv - $pv_pc;
			$rb = $db->count($tb_pv, "addtime>=$ftime AND addtime<=$ttime AND robot<>''");
			if($rb) {
				$rb_pc = $db->count($tb_pv, "addtime>=$ftime AND addtime<=$ttime AND robot<>'' AND pc=1");
				$rb_mb = $rb - $rb_pc;
			}
			$uv = $db->count($tb_uv, "addtime>=$ftime AND addtime<=$ttime");
			$uv_pc = $db->count($tb_uv, "addtime>=$ftime AND addtime<=$ttime AND pc=1");
			$uv_mb = $db->count($tb_uv, "addtime>=$ftime AND addtime<=$ttime AND pc=0");
			$ip = $db->count($tb_uv, "addtime>=$ftime AND addtime<=$ttime", 0, 'DISTINCT `ip`');
			$ip_pc = $db->count($tb_uv, "addtime>=$ftime AND addtime<=$ttime AND pc=1", 0, 'DISTINCT `ip`');
			$ip_mb = $db->count($tb_uv, "addtime>=$ftime AND addtime<=$ttime AND pc=0", 0, 'DISTINCT `ip`');
			$db->query("REPLACE INTO {$DT_PRE}stats (id,uv,uv_pc,uv_mb,ip,ip_pc,ip_mb,pv,pv_pc,pv_mb,rb,rb_pc,rb_mb) VALUES ('$id','$uv','$uv_pc','$uv_mb','$ip','$ip_pc','$ip_mb','$pv','$pv_pc','$pv_mb','$rb','$rb_pc','$rb_mb')");
		}
	}
	$time = $time - 86400;
}
if(timetodate(DT_TIME, 'H') == '01') {
	$v1 = intval($v1);
	$v2 = intval($v2);
	if($v1 > 0) {
		$time = $DT_TODAY - $v1*86400;
		$db->query("DELETE FROM {$tb_pv} WHERE addtime<$time");
		$db->query("OPTIMIZE TABLE `{$tb_pv}`");
	}
	if($v2 > 0) {
		$time = $DT_TODAY - $v2*86400;
		$db->query("DELETE FROM {$tb_uv} WHERE addtime<$time");
		$db->query("OPTIMIZE TABLE `{$tb_uv}`");
	}
}
$result = $db->query("SELECT * FROM {$DT_PRE}stats_screen ORDER BY itemid ASC");
while($r = $db->fetch_array($result)) {
	$ip = $r['ip'];
	$ua = md5($r['ua']);
	$condition = "ip='$ip'";
	$res = $db->query("SELECT * FROM {$DT_PRE}stats_uv WHERE {$condition} ORDER BY itemid DESC LIMIT 100");
	while($rr = $db->fetch_array($res)) {
		if($rr['screen']) continue;
		if(md5($rr['ua']) == $ua) {
			$screen = $r['width'].'*'.$r['height'];
			$db->query("UPDATE {$DT_PRE}stats_uv SET screen='$screen' WHERE itemid=$rr[itemid]");
		}
	}
	$db->query("DELETE FROM {$DT_PRE}stats_screen WHERE itemid=$r[itemid]");
}
?>