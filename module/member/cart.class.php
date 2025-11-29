<?php 
defined('IN_DESTOON') or exit('Access Denied');
class cart {
	var $table;
	var $userid;
	var $errid;
	var $max;

    function __construct() {
		global $_userid;
		$this->userid = $_userid;
		$this->errid = 0;
		$this->table = DT_PRE.'cart';
    }

    function cart() {
		$this->__construct();
    }

	function set($cart) {
		set_cookie('cart', count($cart), DT_TIME + 30*86400);
		$data = addslashes(serialize($cart));
		DB::query("REPLACE INTO {$this->table} (userid,data,edittime) VALUES ('$this->userid','$data','".DT_TIME."')");
	}

	function add($cart, $mid, $itemid, $s1, $s2, $s3, $a) {
		global $_username;
		if(is_array($itemid) && count($itemid) == 1) {
			$id = $itemid[0];
			$itemid = $id;
		}
		$id = 0;
		if(is_array($itemid)) {
			$tags = array();
			$itemids = implode(',', $itemid);
			$result = DB::query("SELECT * FROM ".get_table($mid)." WHERE itemid IN ($itemids)");
			while($r = DB::fetch_array($result)) {
				$this->errid = $r['itemid'];
				if(!check_name($r['username'])) return 11;
				if($r['username'] == $_username) return 12;
				if($r['status'] != 3) return 13;
				$r['amount'] = get_amount($r);
				if($r['amount'] < 1) return 14;
				$r['price'] = get_price($r);
				if($r['price'] < 0.01) return 15;
				$tags[$r['itemid']] = $r;
			}
			foreach($itemid as $v) {
				$this->errid = $v;
				if(!isset($tags[$v])) return 10;
				$k = $mid.'-'.$v.'-0-0-0';
				if(isset($cart[$k])) {
					$cart[$k] = $cart[$k] + 1;
				} else {
					$cart[$k] = 1;
				}
				$id = $v;
			}
			if($id == 0) return 10;
		} else {
			$r = DB::get_one("SELECT * FROM ".get_table($mid)." WHERE itemid=$itemid");
			$this->errid = $itemid;
			if(!$r) return 10;
			if(!check_name($r['username'])) return 11;
			if($r['username'] == $_username) return 12;
			if($r['status'] != 3) return 13;
			$r['amount'] = get_amount($r);
			if($r['amount'] < 1) return 14;
			$k = $mid.'-'.$itemid.'-'.$s1.'-'.$s2.'-'.$s3;
			if(isset($cart[$k])) {
				$cart[$k] = $cart[$k] + $a;
			} else {
				$cart[$k] = $a;
			}
		}
		$max = $this->max > 1 ? $this->max : 30;
		while(count($cart) > $max) {
			array_shift($cart);
		}
		$this->set($cart);
		return 1;
	}

	function get() {
		$r = DB::get_one("SELECT data FROM {$this->table} WHERE userid=$this->userid");
		return ($r && $r['data']) ? unserialize($r['data']) : array();
	}

	function clear() {
		set_cookie('cart', '0', DT_TIME + 30*86400);
		DB::query("DELETE FROM {$this->table} WHERE userid=$this->userid");
	}

	function fav($key) {
		global $MODULE, $_username, $_userid;
		$t = array_map('intval', explode('-', $key));
		$mid = $t[0];
		$tid = $t[1];
		if($mid > 4 && $tid > 0 && in_array($MODULE[$mid]['module'], array('mall', 'sell'))) {
			$r = DB::get_one("SELECT itemid FROM ".DT_PRE."favorite WHERE userid=$_userid AND mid=$mid AND tid=$tid");
			if($r) return;
			$r = DB::get_one("SELECT title,thumb,linkurl FROM ".get_table($mid)." WHERE itemid=$tid");
			if($r) {
				$title = addslashes($r['title']);
				$thumb = $r['thumb'];
				$url = $MODULE[$mid]['linkurl'].$r['linkurl'];
				DB::query("INSERT INTO ".DT_PRE."favorite (mid,tid,userid,username,title,thumb,url,addtime) VALUES ('$mid','$tid','$_userid','$_username','$title','$thumb','$url','".DT_TIME."')");
			}
		}
	}

	function get_list($cart) {
		global $MODULE, $_username;
		$lists = $tags = $ids = $data = $_cart = array();
		foreach($cart as $k=>$v) {
			$t = array_map('intval', explode('-', $k));
			$mid = $t[0];
			if(!isset($MODULE[$mid])) continue;
			$ids[$mid] = isset($ids[$mid]) ? $ids[$mid].','.$t[1] : $t[1];
			$r = array();
			$r['itemid'] = $t[1];
			$r['s1'] = $t[2];
			$r['s2'] = $t[3];
			$r['s3'] = $t[4];
			$r['a'] = $v;
			$r['mid'] = $mid;
			$data[$k] = $r;
		}
		if($ids) {
			foreach($ids as $_mid=>$itemids) {
				$result = DB::query("SELECT * FROM ".get_table($_mid)." WHERE itemid IN ($itemids)");
				while($r = DB::fetch_array($result)) {
					if($r['username'] == $_username || $r['status'] != 3) continue;
					$r['valid'] = 1;
					$r['mid'] = $_mid;
					$r['alt'] = $r['title'];
					$r['title'] = set_style($r['title'], $r['style']);
					$r['mobile'] = $MODULE[$_mid]['mobile'].$r['linkurl'];
					$r['linkurl'] = $MODULE[$_mid]['linkurl'].$r['linkurl'];
					$r['P1'] = get_nv($r['n1'], $r['v1']);
					$r['P2'] = get_nv($r['n2'], $r['v2']);
					$r['P3'] = get_nv($r['n3'], $r['v3']);
					$r['a1'] = 1;
					if($MODULE[$_mid]['module'] == 'sell') {
						$r['step'] = $r['stock'] = $r['prices'] = '';
						$r['cod'] = 0;
						$r['express_1'] = $r['express_name_1'] = $r['fee_start_1'] = $r['fee_step_1'] = '';
						$r['express_2'] = $r['express_name_2'] = $r['fee_start_2'] = $r['fee_step_2'] = '';
						$r['express_3'] = $r['express_name_3'] = $r['fee_start_3'] = $r['fee_step_3'] = '';
						$r['a1'] = intval($r['minamount']);
					}
					if($r['step']) {
						foreach(unserialize($r['step']) as $k=>$v) {
							$r[$k] = $v;
						}
					} else {
						$r['p1'] = $r['price'];
						$r['a2'] = $r['a3'] = 0;
						$r['p2'] = $r['p3'] = 0.00;
					}
					if($r['a1'] < 1) $r['a1'] = 1;
					$tags[$r['mid'].'-'.$r['itemid']] = $r;
				}
			}
			if($tags) {
				foreach($data as $k=>$v) {
					if(isset($tags[$v['mid'].'-'.$v['itemid']])) {
						$r = $tags[$v['mid'].'-'.$v['itemid']];
						$r['key'] = $k;
						$r['s1'] = $v['s1'];
						$r['s2'] = $v['s2'];
						$r['s3'] = $v['s3'];
						$r['a'] = $v['a'];
						if($r['stock']) {
							foreach(get_stock($r) as $kk=>$vv) {
								$r[$kk] = $vv;
							}
						}
						$r['amount'] = get_amount($r);
						if($r['a'] > $r['amount']) $r['a'] = $r['amount'];
						if($r['a'] < $r['a1']) $r['a'] = $r['a1'];
						$r['minamount'] = intval($r['minamount']);
						$r['maxamount'] = intval($r['maxamount']);
						$r['mina'] = 1;
						if($r['minamount'] > $r['mina'] && $r['minamount'] > 0) $r['mina'] = $r['minamount'];
						$r['maxa'] = $r['amount'];
						if($r['maxamount'] < $r['maxa'] && $r['maxamount'] > 0) $r['maxa'] = $r['maxamount'];
						if($r['a'] < $r['mina']) $r['a'] = $r['mina'];
						if($r['a'] > $r['maxa']) $r['a'] = $r['maxa'];
						$r['p1'] = $r['price'] = get_price($r);
						if($r['amount'] < 1 || $r['price'] < 0.01) $r['valid'] = 0;
						$r['m1'] = isset($r['P1'][$r['s1']]) ? $r['P1'][$r['s1']] : '';
						$r['m2'] = isset($r['P2'][$r['s2']]) ? $r['P2'][$r['s2']] : '';
						$r['m3'] = isset($r['P3'][$r['s3']]) ? $r['P3'][$r['s3']] : '';
						$_cart[$k] = $r['a'];
						$lists[$r['username']][] = $r;
					}
				}
			}
		}
		if(count($_cart) != count($cart) || count($_cart) != get_cookie('cart')) $this->set($_cart);
		return $lists;
	}
}
?>