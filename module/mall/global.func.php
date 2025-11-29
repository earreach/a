<?php
defined('IN_DESTOON') or exit('Access Denied');
function get_relate($M) {
	global $table, $MOD;
	$lists = $tags = array();
	if($M['relate_id'] && $M['relate_name']) {
		$ids = $M['relate_id'];
		$result = DB::query("SELECT itemid,title,linkurl,thumb,username,status,relate_id,relate_name,relate_title FROM {$table} WHERE itemid IN ($ids)");
		while($r = DB::fetch_array($result)) {
			if($r['username'] != $M['username']) continue;
			if($r['relate_id'] != $M['relate_id']) continue;
			if($r['relate_name'] != $M['relate_name']) continue;
			if($r['status'] != 3) continue;
			if(!$r['relate_title']) $r['relate_title'] = $r['title'];
			$tags[$r['itemid']] = $r;
		}
		foreach(explode(',', $ids) as $v) {
			if(isset($tags[$v])) $lists[] = $tags[$v];
		}
		return count($lists) > 1 ? $lists : array();
	}
}

function get_nv($n, $v) {
	$p = array();
	if($n && $v) $p = explode('|', $v);
	return count($p) > 1 ? $p : array();
}

function get_price($r) {
	if($r['step']) {
		$s = unserialize($r['step']);
		if($s['a3'] && $r['a'] > $s['a3']) return $s['p3'];
		if($s['a2'] && $r['a'] > $s['a2']) return $s['p2'];
		return $s['p1'];
	}
	if($r['prices']) {
		$s = explode('|', $r['prices']);
		if(isset($s[$r['s1']])) return $s[$r['s1']];
		return 0.00;
	}
	if($r['sprice'] > 0 && $r['sprice'] < $r['price'] && $r['sfromtime'] < DT_TIME && $r['stotime'] > DT_TIME) return $r['sprice'];
	if($r['fprice'] > 0 && $r['fprice'] < $r['price']) {
		global $_userid;
		$T = DB::get_one("SELECT itemid FROM ".DT_PRE."follow WHERE userid=$_userid AND fusername='$r[username]'");
		if($T) return $r['fprice'];
	}
	return $r['price'];
}

function get_sec($r) {
	global $table;
	if($r['sprice'] > 0 && $r['sprice'] < $r['price']) {
		if($r['sfromtime'] > DT_TIME) return 2;
		if($r['stotime'] > DT_TIME) return 1;
		#DB::query("UPDATE {$table} SET sprice='0.00',sfromtime=0,stotime=0 WHERE itemid=$r[itemid]");
	}
	return 0;
}

function get_amount($r) {
	if($r['skuid']) {
		$s = get_sku($r['skuid'], $r['username']);
		return $s['amount'];
	}
	return $r['amount'];
}

function get_sku($skuid, $username) {
	return DB::get_one("SELECT itemid,title,style,price,amount,unit,skuid,location,thumb FROM ".DT_PRE."stock WHERE skuid='$skuid' AND username='$username'");
}

function get_stock($r) {
	$s = unserialize($r['stock']);
	if(!$s) return array();
	$k = $r['s1'].'-'.$r['s2'].'-'.$r['s3'];
	if(!isset($s[$k])) return array('valid' => 0);
	$sid = $s[$k];
	$t = DB::get_one("SELECT itemid,title,style,price,amount,unit,skuid,thumb FROM ".DT_PRE."stock WHERE itemid=$sid");
	if(!$t) return array('valid' => 0);
	return array('p1' => $t['price'], 'price' => $t['price'], 'amount' => $t['amount'], 'thumb' => $t['thumb'], 'skuid' => $t['skuid']);
}

function get_stocks($stock) {
	$stks = unserialize($stock);
	$sids = implode(',', $stks);
	$lists = $tags = array();
	if(preg_match("/^[0-9\,]{1,}$/", $sids)) {
		$result = DB::query("SELECT itemid,title,style,price,amount,unit,skuid,thumb FROM ".DT_PRE."stock WHERE itemid IN ($sids)");
		while($r = DB::fetch_array($result)) {
			$lists[$r['itemid']] = $r;
		}
		foreach($stks as $k=>$v) {
			if($v > 0) $tags[$k] = $lists[$v];
		}
	}
	return $tags;
}

function get_promos($username, $moduleid = 0, $itemid = 0) {
	$lists = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."finance_promo WHERE username='$username' AND fromtime<".DT_TIME." AND totime>".DT_TIME." AND number<amount ORDER BY price ASC LIMIT 10", 'CACHE');
	while($r = DB::fetch_array($result)) {
		if($r['mid'] && $moduleid && $r['mid'] != $moduleid) continue;
		if($r['itemids'] && $itemid && !in_array($itemid, explode(',', $r['itemids']))) continue;
		$lists[] = $r;
	}
	return $lists;
}

function get_coupons($username, $seller) {
	$lists = array();
	$result = DB::query("SELECT * FROM ".DT_PRE."finance_coupon WHERE username='$username' AND (seller='$seller' OR seller='') AND fromtime<".DT_TIME." AND totime>".DT_TIME." AND oid=0 ORDER BY price ASC LIMIT 10", 'CACHE');
	while($r = DB::fetch_array($result)) {
		$lists[] = $r;
	}
	return $lists;
}

function get_discount($username) {
	global $_username;
	if(!check_name($username) || !check_name($_username)) return 0;
	$t = DB::get_one("SELECT discount FROM ".DT_PRE."agent WHERE username='$username' AND pusername='$_username' AND status=3");
	return ($t && $t['discount'] < 100 && $t['discount'] > 0) ? $t['discount'] : 0;
}
?>