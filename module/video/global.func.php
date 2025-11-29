<?php
defined('IN_DESTOON') or exit('Access Denied');
function playlist($item, $num = 2) {
	global $table;
	$condition = 'status=3';
	if($item['album']) {
		$condition .= " AND album='".$item['album']."'";
	} else if($item['username']) {		
		$condition .= " AND username='".$item['username']."'";
	} else {		
		$condition .= " AND catid='".$item['catid']."'";
	}
	$addtime = $item['addtime'];
	$pagesize = $num*2;
	$P = $N = array();
	$result = DB::query("SELECT * FROM {$table} WHERE {$condition} AND addtime>$addtime ORDER BY addtime ASC LIMIT $pagesize", 'CACHE');
	while($r = DB::fetch_array($result)) {
		$P[] = $r;
	}
	$P = array_reverse($P);
	$result = DB::query("SELECT * FROM {$table} WHERE {$condition} AND addtime<$addtime ORDER BY addtime DESC LIMIT $pagesize", 'CACHE');
	while($r = DB::fetch_array($result)) {
		$N[] = $r;
	}
	$cp = count($P);
	$cn = count($N);
	$p = $n = 0;
	if($cp >= $num) {
		if($cn >= $num) {
			$p = $n = $num;
		} else {
			$p = $num - $cn + $num;
			if($p > $cp) $p = $cp;
			$n = $cn;
		}
	} else {
		if($cn > $num) {
			$p = $cp;
			$n = $num - $cp + $num;
			if($n > $cn) $n = $cn;
		} else {
			$p = $cp;
			$n = $cn;
		}
	}
	$tags = array();
	if($p) {
		for($i = $cp - 1; $i >= $cp - $p; $i--) {
			$tags[] = $P[$i];
		}
		$tags = array_reverse($tags);
	}
	$tags[] = $item;
	if($n) {
		for($i = 0; $i < $n; $i++) {
			$tags[] = $N[$i];
		}
	}
	return $tags;
}

function albumlist($item) {
	global $table;
	$tags = array();
	$j = 0;
	$index = 1;
	if($item['album'] && $item['username']) {
		$result = DB::query("SELECT * FROM {$table} WHERE album='".addslashes($item['album'])."' AND username='$item[username]' AND status=3 ORDER BY addtime DESC LIMIT 1000", 'CACHE');
		while($r = DB::fetch_array($result)) {
			$j++;
			if($r['itemid'] == $item['itemid']) $index = $j;
			$tags[] = $r;
		}
	}
	return array($tags, $index, count($tags));
}
?>