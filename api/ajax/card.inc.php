<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/include/module.func.php';
$lists = array();
if($job == 'message') {
	if($_userid) {
		$result = $db->query("SELECT itemid,title,style,typeid,fromuser,fpassport,addtime,ip,isread,issend,feedback,status,groupids,mid,tid FROM {$DT_PRE}message WHERE touser='$_username' AND status=3 AND isread=0 ORDER BY itemid DESC LIMIT 0,5");
		while($r = $db->fetch_array($result)) {
			$lists[] = $r;
		}
	}
} else if($job == 'chat') {
	$new = 0;
	if($_userid) {
		$result = $db->query("SELECT * FROM {$DT_PRE}chat WHERE fromuser='$_username' OR touser='$_username' ORDER BY lasttime DESC LIMIT 5");
		while($r = $db->fetch_array($result)) {
			if($r['fromuser'] == $_username) {
				$r['username'] = $r['touser'];
				$r['user'] = $r['tpassport'] ? $r['tpassport'] : $r['touser'];
				$r['name'] = $r['talias'] ? $r['talias'] : $r['user'];
				$r['new'] = $r['fnew'];
			} else {
				$r['username'] = $r['fromuser'];
				$r['user'] = $r['fpassport'] ? $r['fpassport'] : $r['fromuser'];
				$r['name'] = $r['falias'] ? $r['falias'] : $r['user'];
				$r['new'] = $r['tnew'];
			}
			$new += $r['new'];
			if($r['new'] > 99) $r['new'] = 99;
			$r['last'] = timetoread($r['lasttime'], $r['lasttime'] > $DT_TODAY - 86400 ? 'H:i:s' : 'y/m/d');
			$lists[] = $r;
		}
	}
} else if($job == 'cart') {
	if($_userid) {
		if($mid < 5) {
			foreach($MODULE as $v) {
				if(in_array($v['module'], array('mall', 'sell'))) {
					$mid = $v['moduleid'];
					break;
				}
			}
		}
		require DT_ROOT.'/module/mall/global.func.php';
		require DT_ROOT.'/module/member/cart.class.php';
		$do = new cart();
		$do->max = intval($DT['max_cart']);
		$cart = $do->get();
		$lists = $do->get_list($cart);
	}
} else if($job == 'member') {
	if($_userid) {
		$lists = userinfo($_username);
	}
} else if($job == 'user') {
	(isset($username) && check_name($username)) or $username = '';
	if($username) $lists = userinfo($username);
}
include template('card', 'chip');
?>