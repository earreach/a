<?php 
defined('IN_DESTOON') or exit('Access Denied');
//买家团购订单管理
login();
($mid && isset($MODULE[$mid]) && $MODULE[$mid]['module'] == 'group') or dheader($MODULE[2]['linkurl']);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('order.lang');
$dstatus = $L['group_status'];
$dsend_status = $L['send_status'];
$step = isset($step) ? trim($step) : '';
$timenow = timetodate($DT_TIME, 3);
$memberurl = $MOD['linkurl'];
$myurl = userurl($_username);
$table = $DT_PRE.'group_order_'.$mid;
$table_log = $DT_PRE.'group_order_log_'.$mid;
if($action == 'update') {
	$itemid or message();
	$O = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	$O or message($L['group_msg_null']);
	if($O['buyer'] != $_username) message($L['group_msg_deny']);
	$O['adddate'] = timetodate($O['addtime'], 5);
	$O['updatedate'] = timetodate($O['updatetime'], 5);
	$O['total'] = $O['amount'];
	$O['linkurl'] = gourl('?mid='.$mid.'&itemid='.$O['gid']);
	$O['fee_name'] = $O['fee'] = $O['par'] = '';
	$lists = array($O);
	$gid = $O['gid'];
	switch($step) {
		case 'edit'://修改地址
			($O['status'] == 6 && $O['logistic']) or message($L['trade_msg_deny']);
			if($submit) {
				$aid = isset($aid) ? intval($aid) : 0;
				$aid > 0 or message($L['group_edit_aid']);
				$addr = get_address($_username, $aid);
				$addr or message($L['group_edit_addr']);
				$buyer_name = addslashes($addr['truename']);
				$buyer_address = addslashes($addr['address']);
				$buyer_postcode = $addr['postcode'];
				$buyer_mobile = $addr['mobile'];
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET buyer_name='$buyer_name',buyer_address='$buyer_address',buyer_postcode='$buyer_postcode',buyer_mobile='$buyer_mobile' WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['group_edit_title']."','')");
				dmsg($L['group_edit_success'], $forward);
			}
			$address = get_address($_username);
			$head_title = $L['group_edit_title'];
		break;
		case 'detail':
			$logs = array();
			$result = $db->query("SELECT * FROM {$table_log} WHERE oid=$itemid ORDER BY itemid DESC");
			while($r = $db->fetch_array($result)) {
				$r['adddate'] = timetodate($r['addtime'], 5);
				$logs[] = $r;
			}
			$auth = encrypt('group|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$head_title = $L['group_detail_title'];
		break;
		case 'express':
			($O['send_type'] && $O['send_no']) or dheader('?action=update&step=detail&itemid='.$itemid);
			$auth = encrypt('group|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$head_title = $L['group_express_title'];
		break;
		case 'used':
			if($O['status'] != 2 || $O['logistic']) message();
			//交易成功
			$money = $O['amount'];
			money_add($O['seller'], $money);
			money_record($O['seller'], $money, $L['in_site'], 'system', $L['group_record_pay'], $L['group_order_id'].$itemid);
			//网站服务费
			$G = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE username='".$O['seller']."'");
			$SG = cache_read('group-'.$G['groupid'].'.php');
			if($SG['commission']) {
				$fee = dround($money*$SG['commission']/100);
				if($fee > 0) {
					money_add($O['seller'], -$fee);
					money_record($O['seller'], -$fee, $L['in_site'], 'system', $L['trade_fee'], $L['trade_order_id'].$itemid);	
				}
			}
			$db->query("UPDATE {$table} SET status=3,updatetime=$DT_TIME WHERE itemid=$itemid");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_success]','')");
			dmsg($L['group_success'], '?mid='.$mid.'&action=order&page='.$page);
		break;
		case 'receive':
			if($O['status'] != 1 || !$O['logistic']) message();
			//交易成功
			$money = $O['amount'];
			money_add($O['seller'], $money);
			money_record($O['seller'], $money, $L['in_site'], 'system', $L['group_record_pay'], $L['group_order_id'].$itemid);
			//网站服务费
			$G = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE username='".$O['seller']."'");
			$SG = cache_read('group-'.$G['groupid'].'.php');
			if($SG['commission']) {
				$fee = dround($money*$SG['commission']/100);
				if($fee > 0) {
					money_add($O['seller'], -$fee);
					money_record($O['seller'], -$fee, $L['in_site'], 'system', $L['trade_fee'], $L['trade_order_id'].$itemid);	
				}
			}
			$db->query("UPDATE {$table} SET status=3,updatetime=$DT_TIME WHERE itemid=$itemid");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_get]','')");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_success]','')");
			dmsg($L['group_success'], '?mid='.$mid.'&action=order&page='.$page);
		break;
		case 'pay'://买家付款
			if($O['status'] == 0) dmsg($L['group_pay_order_success'], '?action=order&nav=0&itemid='.$itemid);
			if($O['status'] != 6) message($L['group_msg_deny']);
			$money = $O['amount'];
			$money > 0 or message($L['group_msg_deny']);
			$seller = userinfo($O['seller']);
			$auto = 0;
			$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
			if($auth && substr($auth, 0, 6) == 'group|') {				
				$_itemid = intval(substr($auth, 6));
				if($_itemid == $itemid) $auto = $submit = 1;
			}
			if($submit) {
				$money <= $_money or message($L['money_not_enough']);
				if($money <= $DT['quick_pay']) $auto = 1;
				if(!$auto) {
					is_payword($_username, $password) or message($L['error_payword']);
				}
				money_add($_username, -$money);
				money_record($_username, -$money, $L['in_site'], 'system', $L['group_order_credit'], $L['trade_order_id'].$itemid);
				$password = $O['logistic'] ? '' : random(6, '0-9');
				$db->query("UPDATE {$table} SET status=0,password='$password',updatetime=$DT_TIME WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_pay]','')");
				if($password) {
					//send sms
					if($DT['sms']) {
						$message = lang('sms->ord_group', array($O['title'], $itemid, $password));
						$message = strip_sms($message);
						send_sms($O['buyer_mobile'], $message);
					}
					//send sms
				}
				$db->query("UPDATE ".get_table($mid)." SET orders=orders+1,sales=sales+$O[number] WHERE itemid=$O[gid]");
				dmsg($L['group_pay_order_success'], '?mid='.$mid.'&action=order&nav=0&itemid='.$itemid);
			} else {
				$head_title = $L['group_pay_order_title'];
			}
		break;
		case 'refund'://买家退款
			$gone = $DT_TIME - $O['updatetime'];
			if(!in_array($O['status'], array(0, 1, 2))) message($L['group_msg_deny']);
			if(in_array($O['status'], array(1, 2)) && $gone > ($MOD['trade_day']*86400 + $O['add_time']*3600)) message($L['group_msg_deny']);
			$money = $O['amount'];
			if($submit) {
				$content or message($L['trade_refund_reason']);
				clear_upload($content, $itemid, $table);
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				is_payword($_username, $password) or message($L['error_payword']);
				$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME,buyer_reason='$content' WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_refund]','')");
				message($L['trade_refund_success'], $forward, 3);
			} else {
				$head_title = $L['trade_refund_title'];
			}
		break;
		case 'remind'://买家提醒卖家发货
			if($O['status'] != 0 || !$O['logistic']) message($L['group_msg_deny']);
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_remind]','')");
		break;
	}
} else if($action == 'express') {//我的快递
	$sfields = $L['express_sfields'];
	$dfields = array('title', 'title', 'send_type ', 'send_no', 'buyer_mobile', 'buyer_address');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$status = isset($status) && isset($dsend_status[$status]) ? intval($status) : '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dsend_status, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "send_no<>'' AND buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($status !== '') $condition .= " AND send_status='$status'";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['addtime'] = timetodate($r['addtime'], 5);
		$r['updatetime'] = timetodate($r['updatetime'], 5);
		$r['dstatus'] = $dsend_status[$r['send_status']];
		$lists[] = $r;
	}
	$head_title = $L['express_title'];
} else {
	$sfields = $L['group_order_sfields'];
	$dfields = array('title', 'title ', 'amount', 'password', 'seller', 'send_type', 'send_no', 'note');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$gid = isset($gid) ? intval($gid) : 0;
	$gid or $gid = '';
	$itemid or $itemid = '';
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	(isset($seller) && check_name($seller)) or $seller = '';
	(isset($pass) && preg_match("/^[a-z0-9]{6}$/", $pass)) or $pass = '';
	$nav = isset($nav) ? intval($nav) : -1;
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($status !== '') $condition .= " AND status=$status";
	if($itemid) $condition .= " AND itemid='$itemid'";
	if($gid) $condition .= " AND gid='$gid'";
	if($seller) $condition .= " AND seller='$seller'";
	if($pass) $condition .= " AND password='$pass'";
	if(in_array($nav, array(0,1,2,3,4,5,6))) $condition .= " AND status=$nav";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	$amount = $fee = $money = 0;
	while($r = $db->fetch_array($result)) {
		$r['gone'] = $DT_TIME - $r['updatetime'];
		if($r['status'] == 1 || $r['status'] == 2) {
			if($r['gone'] > ($MOD['trade_day']*86400 + $r['add_time']*3600)) {
				$r['lefttime'] = 0;
			} else {
				$r['lefttime'] = sectoread($MOD['trade_day']*86400 + $r['add_time']*3600 - $r['gone']);
			}
		}
		$r['addtime'] = timetodate($r['addtime'], $DT_PC ? 5 : 3);
		$r['updatetime'] = timetodate($r['updatetime'], 5);
		$r['linkurl'] = gourl('?mid='.$mid.'&itemid='.$r['gid']);
		$r['dstatus'] = $dstatus[$r['status']];
		$r['money'] = $r['amount'];
		$r['money'] = number_format($r['money'], 2, '.', '');
		$amount += $r['amount'];
		$lists[] = $r;
	}
	$money = $amount + $fee;
	$money = number_format($money, 2, '.', '');
	$forward = urlencode($DT_URL);
	$head_title = $L['group_title'];
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('deal', $module);
?>