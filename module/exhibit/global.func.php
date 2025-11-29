<?php
defined('IN_DESTOON') or exit('Access Denied');
function list_top($key = 'city', $pagesize = 10) {
	global $table;
	$lists = array();
	$result = DB::query("SELECT COUNT(`$key`) AS num,`$key` FROM {$table} WHERE status=3 GROUP BY `$key` ORDER BY num DESC LIMIT 0,$pagesize", 'CACHE');
	while($r = DB::fetch_array($result)) {
		if($r['num'] < 2 || strlen(trim($r[$key])) < 2) continue;
		$r['kw'] = urlencode($r[$key]);
		$lists[] = $r;
	}
	return $lists;
}

function list_month($max = 12) {
	global $table;
	$lists = array();
	$t = DB::get_one("SELECT max(fromtime) AS fromtime FROM {$table}", 'CACHE');
	$f = $t['fromtime'];
	$y = timetodate(DT_TIME, 'Y');
	$m = timetodate(DT_TIME, 'n');
	$M = array();
	for($i = $m; $i < 13; $i++) {
		$M[$i] = $y;
	}
	for($i = 1; $i < $m; $i++) {
		$M[$i] = $y + 1;
	}
	$j = 1;
	foreach($M as $k=>$v) {
		if($j++ > $max) continue;
		$r = array();
		$fromtime = datetotime($v.'-'.$k.'-1');
		$d = timetodate($fromtime, 't');
		$totime = datetotime($v.'-'.$k.'-'.$d.' 23:59:59');
		$r['year'] = $v;
		$r['month'] = $k;
		$r['from'] = $v.($k > 9 ? $k : '0'.$k).'01';
		$r['to'] = $v.($k > 9 ? $k : '0'.$k).$d;
		if($fromtime > $f) {
			$r['num'] = 0;
		} else {
			$t = DB::get_one("SELECT COUNT(itemid) AS num FROM {$table} WHERE status=3 AND fromtime>=$fromtime AND totime<=$totime", 'CACHE');
			$r['num'] = $t['num'];
		}
		$r['total'] = $r['num'] > 99 ? '99+' : $r['num'];
		$lists[] = $r;
	}
	return $lists;
}
?>