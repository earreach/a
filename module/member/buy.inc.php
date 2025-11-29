<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
if($mid < 5) {
	foreach($MODULE as $v) {
		if(in_array($v['module'], array('mall', 'sell'))) {
			$mid = $v['moduleid'];
			break;
		}
	}
}
if(isset($MODULE[$mid]) && in_array($MODULE[$mid]['module'], array('mall', 'sell'))) {
	$moduleid = $mid;
	$MOD = cache_read('module-'.$moduleid.'.php');
	$module = $MOD['module'];
} else {
	dheader($DT_PC ? DT_PATH : DT_MOB);
}
if(is_array($itemid)) {
	$DT_URL = $MODULE[2]['linkurl'].'buy'.DT_EXT.'?action=add&mid='.$mid;
	foreach($itemid as $id) {
		$DT_URL .= '&itemid[]='.$id;
	}
}
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/module/mall/global.func.php';
require DT_ROOT.'/module/member/global.func.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');

if($submit) {
	require DT_ROOT.'/module/member/cart.class.php';
	$do = new cart();
	$cart = $do->get();
	$ids = '';
	$success = 0;
	if($post) {
		$aid = isset($aid) ? intval($aid) : 0;
		$aid > 0 or message($L['msg_buy_addr']);
		$addr = get_address($_username, $aid);
		($addr && $addr['truename'] && is_mobile($addr['mobile']) && $addr['address']) or message($L['msg_buy_addrerr']);
		$addr = daddslashes($addr);
		$buyer_address = $addr['address'];
		$buyer_postcode = $addr['postcode'];
		$buyer_name = $addr['truename'];
		$buyer_mobile = $addr['mobile'];
		$user_inviter = get_cookie('inviter');
		check_name($user_inviter) or $user_inviter = '';
		$N = $M = array();
		foreach($post as $k=>$v) {
			$t1 = array_map('intval', explode('-', $k));
			$_mid = $t1[0];
			$itemid = $t1[1];
			$s1 = $t1[2];
			$s2 = $t1[3];
			$s3 = $t1[4];
			$t = $db->get_one("SELECT * FROM ".get_table($mid)." WHERE itemid=$itemid");
			if($t && $t['status'] == 3 && check_name($t['username']) && $t['username'] != $_username) {
				$M[] = $t;
				$t['a1'] = 1;
				if($MODULE[$_mid]['module'] == 'sell') {
					$t['step'] = $t['stock'] = $t['prices'] = '';
					$t['cod'] = 0;
					$t['express_1'] = $t['express_name_1'] = $t['fee_start_1'] = $t['fee_step_1'] = '';
					$t['express_2'] = $t['express_name_2'] = $t['fee_start_2'] = $t['fee_step_2'] = '';
					$t['express_3'] = $t['express_name_3'] = $t['fee_start_3'] = $t['fee_step_3'] = '';
					$t['a1'] = intval($t['minamount']);
				}
				if($t['step']) {
					foreach(unserialize($t['step']) as $_k=>$_v) {
						$t[$_k] = $_v;
					}
				} else {
					$t['a2'] = $t['a3'] = 0;
					$t['p1'] = $t['price'];
					$t['p2'] = $t['p3'] = 0.00;
				}
				if($t['a1'] < 1) $t['a1'] = 1;
				$number = intval($v['number']);
				$t['s1'] = $s1;
				$t['s2'] = $s2;
				$t['s3'] = $s3;
				if($t['stock']) {
					foreach(get_stock($t) as $kk=>$vv) {
						$t[$kk] = $vv;
					}
				}
				$t['amount'] = get_amount($t);
				if($number < $t['a1']) $number = $t['a1'];

				$t['minamount'] = intval($t['minamount']);
				$t['maxamount'] = intval($t['maxamount']);
				$t['mina'] = 1;
				if($t['minamount'] > $t['mina'] && $t['minamount'] > 0) $t['mina'] = $t['minamount'];
				$t['maxa'] = $t['amount'];
				if($t['maxamount'] < $t['maxa'] && $t['maxamount'] > 0) $t['maxa'] = $t['maxamount'];
				if($number < $t['mina']) $number = $t['mina'];
				if($number > $t['maxa']) $number = $t['maxa'];

				$t['a'] = $number;
				$price = get_price($t);
				if($t['amount'] < 1 || $price < 0.01) continue;
				$amount = $number*$price;
				$note = '';
				$t['P1'] = get_nv($t['n1'], $t['v1']);
				$t['P2'] = get_nv($t['n2'], $t['v2']);
				$t['P3'] = get_nv($t['n3'], $t['v3']);
				$t['m1'] = isset($t['P1'][$t['s1']]) ? $t['P1'][$t['s1']] : '';
				$t['m2'] = isset($t['P2'][$t['s2']]) ? $t['P2'][$t['s2']] : '';
				$t['m3'] = isset($t['P3'][$t['s3']]) ? $t['P3'][$t['s3']] : '';
				if($t['m1']) $note .= $t['n1'].':'.$t['m1'].' ';
				if($t['m2']) $note .= $t['n2'].':'.$t['m2'].' ';
				if($t['m3']) $note .= $t['n3'].':'.$t['m3'].' ';
				$v['note'] = str_replace('|', '-', $v['note']);
				$note = dhtmlspecialchars($v['note'].'|'.$note);
				$title = addslashes($t['title']);
				$MOD = cache_read('module-'.$_mid.'.php');
				$linkurl = $MOD['linkurl'].$t['linkurl'];
				$S = userinfo($t['username']);
				$status = $S['checkorder'] ? 0 : 1;
				$cod = 0;
				if($t['cod'] == 2) {
					if(isset($v['cod']) && $v['cod']) $cod = 1;
				} else if($t['cod'] == 1) {
					$cod = 1;
				}
				if($cod) $status = 7;
				if($t['express_name_1'] == $L['post_free']) {
					if($t['fee_start_1'] > 0) {
						if($amount >= $t['fee_start_1']) $v['express'] = 0;
					} else {
						$v['express'] = 0;
					}
				}
				$express = intval($v['express']);
				if($express && in_array($express, array(1,2,3)) && $MOD['module'] == 'mall') {
					$i = $express;
					$fee_name = $t['express_name_'.$i];
					$fee = dround($t['fee_start_'.$i] + $t['fee_step_'.$i]*($number-1));
					$express_id = intval($t['express_'.$i]);
					$area_id = $addr['areaid'];
					if($express_id && $area_id) {
						$E = $db->get_one("SELECT * FROM {$DT_PRE}mall_express_{$_mid} WHERE itemid=$express_id");
						if($E && $E['items'] > 0) {
							$AREA = cache_read('area.php');
							$aid = $area_id;
							$ii = 0;
							do {
								$E = $db->get_one("SELECT * FROM {$DT_PRE}mall_express_{$_mid} WHERE parentid=$express_id AND areaid=$aid");
								if($E) {
									$fee = dround($E['fee_start'] + $E['fee_step']*($number-1));
									break;
								} else {
									$aid = $AREA[$aid]['parentid'];
								}
								if($ii++ > 5) break;
							} while($aid > 0);
						}
					}
				} else {
					$fee_name = '';
					$fee = 0;
				}
				$inviter = ($user_inviter && agented($user_inviter, $t['username'])) ? $user_inviter : '';
				$shop = addslashes($S['shop'] ? $S['shop'] : $S['company']);
				if(isset($N[$t['username'].$cod])) {
					$pid = $N[$t['username'].$cod];
					$db->query("INSERT INTO {$DT_PRE}order (mid,mallid,pid,buyer,seller,inviter,shop,title,thumb,skuid,price,number,amount,addtime,updatetime,note, buyer_passport,buyer_postcode,buyer_address,buyer_name,buyer_mobile,status,fee_name,fee,cod) VALUES ('$moduleid','$itemid','$pid','$_username','$t[username]','$inviter','$shop','$title','$t[thumb]','$t[skuid]','$price','$number','$amount','$DT_TIME','$DT_TIME','$note','$_passport','$buyer_postcode','$buyer_address','$buyer_name','$buyer_mobile','$status','$fee_name','$fee','$cod')");
					$oid = $db->insert_id();
					$db->query("UPDATE {$DT_PRE}order SET `amount`=`amount`+$amount,`fee`=`fee`+$fee WHERE itemid=$pid");
					$db->query("INSERT INTO {$DT_PRE}order_log (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','$L[log_buy]','')");
					if($fee_name) $db->query("UPDATE {$DT_PRE}order SET `fee_name`='$fee_name' WHERE itemid=$pid");
				} else {
					$db->query("INSERT INTO {$DT_PRE}order (mid,mallid,buyer,seller,inviter,shop,title,thumb,skuid,price,number,amount,addtime,updatetime,note, buyer_passport,buyer_postcode,buyer_address,buyer_name,buyer_mobile,status,fee_name,fee,cod) VALUES ('$moduleid','$itemid','$_username','$t[username]','$inviter','$shop','$title','$t[thumb]','$t[skuid]','$price','$number','$amount','$DT_TIME','$DT_TIME','$note','$_passport','$buyer_postcode','$buyer_address','$buyer_name','$buyer_mobile','$status','$fee_name','$fee','$cod')");
					$pid = $oid = $db->insert_id();
					if($pid) $success = 1;
					$db->query("INSERT INTO {$DT_PRE}order_log (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','$L[log_buy]','')");
					$N[$t['username'].$cod] = $pid;
					if(!$cod) $ids .= ','.$pid;
					//send message
					$touser = $t['username'];
					$_title = $title;
					$title = lang($L['trade_message_t6'], array($pid));
					$url = $MODULE[2]['linkurl'].'trade'.DT_EXT.'?itemid='.$pid;
					$goods = '<a href="'.$linkurl.'" target="_blank" class="t"><strong>'.$_title.'</strong></a>';
					$content = lang($L['trade_message_c6'], array(userurl($_username), $_username, timetodate($DT_TIME, 3), $goods, $pid, $amount, $url));
					$content = ob_template('messager', 'mail');
					send_message($touser, $title, $content);
				}
				if($MODULE[$_mid]['module'] == 'mall') {
					$db->query("REPLACE INTO {$DT_PRE}mall_comment_{$_mid} (itemid,mallid,buyer,seller) VALUES ('$oid','$itemid','$_username','$t[username]')");
					$tmp = $db->get_one("SELECT mallid FROM {$DT_PRE}mall_stat_{$_mid} WHERE mallid=$itemid");
					if(!$tmp) $db->query("REPLACE INTO {$DT_PRE}mall_stat_{$_mid} (mallid,buyer,seller) VALUES ('$itemid','$_username','$t[username]')");
				}
				unset($cart[$k]);
			}
		}
		if($N) {
			foreach($N as $seller=>$oid) {
				$seller = substr($seller, 0, -1);
				if($coupon) {
					if(isset($coupon[$seller])) {
						$cid = $coupon[$seller];
						if(!$cid) continue;
						$c = $db->get_one("SELECT * FROM {$DT_PRE}finance_coupon WHERE itemid=$cid");
						if($c && $c['username'] == $_username && ($c['seller'] == $seller || $c['seller'] == '') && $c['oid'] == 0 && $c['fromtime'] < $DT_TIME && $c['totime'] > $DT_TIME) {
							$o = $db->get_one("SELECT * FROM {$DT_PRE}order WHERE itemid=$oid");
							if($o && $o['buyer'] == $_username && ($o['seller'] == $seller || $o['seller'] == '') && $o['cid'] == 0 && $o['discount'] < 0.01) {
								$discount = $c['price'];
								if($c['mid'] || $c['catid'] || $c['itemids']) {
									foreach($M as $k=>$v) {
										if($v['username'] != $seller) continue;
										if($c['mid'] && $mid == $c['mid']) $c['mid'] = 0;
										if($c['catid'] && $v['catid'] == $c['catid']) $c['catid'] = 0;
										if($c['itemids'] && in_array($v['itemid'], explode(',', $c['itemids']))) $c['itemids'] = '';
									}
									if($c['mid'] || $c['catid'] || $c['itemids']) continue;
								}
								if($c['cost'] <= ($o['amount'] + $o['fee']) && $c['price'] < $o['amount']) {
									$db->query("UPDATE {$DT_PRE}order SET `amount`=`amount`-$discount,discount=$discount,cid=$cid WHERE itemid=$oid");
									$db->query("UPDATE {$DT_PRE}finance_coupon SET oid=$oid WHERE itemid=$cid");
								}
							}
						}
					}
				}
			}
			foreach($N as $seller=>$oid) {
				$seller = substr($seller, 0, -1);
				$discount = get_discount($seller);
				if($discount > 0) {
					$o = $db->get_one("SELECT * FROM {$DT_PRE}order WHERE itemid=$oid");
					if($o) {
						$discount = dround(($o['amount']+$o['discount'])*(100-$discount)/100);
						$db->query("UPDATE {$DT_PRE}order SET `amount`=`amount`-$discount,discount=`discount`+$discount WHERE itemid=$oid");
					}
				}
			}
		}
	}
	$do->set($cart);
	$payurl = 'action=order';
	if(!$S['checkorder']) {
		if($ids) {
			$ids = substr($ids, 1);
			if(is_numeric($ids)) {
				$payurl = 'action=update&step=pay&itemid='.$ids;
			} else {
				$payurl = 'action=muti&ids='.$ids;
			}
		}
	}
	dheader('buy'.DT_EXT.'?action=result&code='.($success ? 1 : 99).'&auth='.encrypt($payurl, DT_KEY.'PURL', 300));
} else {
	$lists = $address = array();
	if($action == 'result') {
		$payurl = isset($auth) ? decrypt($auth, DT_KEY.'PURL') : '';
		$payurl = 'order'.DT_EXT.'?'.($payurl ? $payurl : 'action=order');
		$code = isset($code) ? intval($code) : 0;
		$url = gourl('?mid='.$mid.'&itemid='.$itemid);
	} else {
		isset($cart) or $cart = array();
		$lists = $tags = $data = $ids = array();
		$num = 0;
		$itemids = '';
		if($itemid) {
			if(is_array($itemid)) {
				foreach($itemid as $id) {
					$ids[$mid] = isset($ids[$mid]) ? $ids[$mid].','.$id : $id;
					$k = $mid.'-'.$id.'-0-0-0';
					$r = array();
					$r['itemid'] = $id;
					$r['s1'] = $r['s2'] = $r['s3'] = $r['a'] = 0;
					$data[$k] = $r;
				}
			} else {
				$s1 = isset($s1) ? intval($s1) : 0;
				$s2 = isset($s2) ? intval($s2) : 0;
				$s3 = isset($s3) ? intval($s3) : 0;
				$a = isset($a) ? intval($a) : 1;
				$ids[$mid] = isset($ids[$mid]) ? $ids[$mid].','.$itemid : $itemid;
				$k = $mid.'-'.$itemid.'-'.$s1.'-'.$s2.'-'.$s3;
				$r = array();
				$r['itemid'] = $itemid;
				$r['s1'] = $s1;
				$r['s2'] = $s2;
				$r['s3'] = $s3;
				$r['a'] = $a;
				$data[$k] = $r;
			}
		} else if($cart) {
			isset($amounts) or $amounts = array();
			foreach($cart as $v) {
				$t = array_map('intval', explode('-', $v));
				$mid = $t[0];
				$ids[$mid] = isset($ids[$mid]) ? $ids[$mid].','.$t[1] : $t[1];
				$r = array();
				$r['itemid'] = $t[1];
				$r['s1'] = $t[2];
				$r['s2'] = $t[3];
				$r['s3'] = $t[4];
				$r['a'] = isset($amounts[$v]) ? $amounts[$v] : 1;
				$data[$v] = $r;
			}
		}
		if($ids) {
			foreach($ids as $_mid=>$itemids) {
				$result = $db->query("SELECT * FROM ".get_table($_mid)." WHERE itemid IN ($itemids)");
				while($r = $db->fetch_array($result)) {
					if(!check_name($r['username'])) dheader('buy'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$r['itemid'].'&code=11');
					if($r['username'] == $_username) dheader('buy'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$r['itemid'].'&code=12');
					if($r['status'] != 3) dheader('buy'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$r['itemid'].'&code=13');
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
						$s = unserialize($r['step']);
						foreach(unserialize($r['step']) as $k=>$v) {
							$r[$k] = $v;
						}
					} else {
						$r['a2'] = $r['a3'] = 0;
						$r['p1'] = $r['price'];
						$r['p2'] = $r['p3'] = 0.00;
					}
					if($r['a1'] < 1) $r['a1'] = 1;
					$tags[$r['itemid']] = $r;
				}
			}
			if($tags) {
				foreach($data as $k=>$v) {
					if(isset($tags[$v['itemid']])) {
						$r = $tags[$v['itemid']];
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
						$r['price'] = $r['p1'] = get_price($r);
						if($r['amount'] < 1) dheader('buy'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$r['itemid'].'&code=14');
						if($r['price'] < 0.01) dheader('buy'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$r['itemid'].'&code=15');
						$r['m1'] = isset($r['P1'][$r['s1']]) ? $r['P1'][$r['s1']] : '';
						$r['m2'] = isset($r['P2'][$r['s2']]) ? $r['P2'][$r['s2']] : '';
						$r['m3'] = isset($r['P3'][$r['s3']]) ? $r['P3'][$r['s3']] : '';
						$lists[$r['username']][] = $r;
						$num++;
					}
				}
			}
			$lists or dheader('buy'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$r['itemid'].'&code=10');
		}
		if($lists) {
			$address = get_address($_username);
			$address or message($L['msg_buy_address'], ($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'address'.DT_EXT.'?action=add&forward='.urlencode(strpos($forward, 'cart') === false ? $DT_URL : $forward));
		}
	}
}
$CSS = array('cart');
$head_title = $L['buy_title'];
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MODULE[2]['linkurl'], $MODULE[2]['mobile'], $DT_URL);
} else {
	$foot = '';
	$head_name = $head_title;
	if($sns_app) $seo_title = $site_name;
	$js_pull = 0;
}
include template('buy', 'member');
?>