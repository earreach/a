<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('支付记录', '?moduleid='.$moduleid.'&file='.$file),
    array('统计报表', '?moduleid='.$moduleid.'&file='.$file.'&action=stats'),
);
$PAY = cache_read('pay.php');
$PAY['card']['name'] = '充值卡';
$table = $DT_PRE.'finance_charge';
switch($action) {
	case 'stats':
		$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
		$year or $year = date('Y', $DT_TIME);
		$month = isset($month) ? intval($month) : date('n', $DT_TIME);
		$xd = $y0 = $y1 = $y2 = $y3 = '';
		$t0 = $t1 = $t2 = $t3 = 0;
		if($month) {
			$L = date('t', datetotime($year.'-'.$month.'-01'));
			for($i = 1; $i <= $L; $i++) {
				if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; $y2 .= ','; $y3 .= ','; }
				$xd .= "'".$i."日'";
				$F = datetotime($year.'-'.$month.'-'.$i.' 00:00:00');
				$T = datetotime($year.'-'.$month.'-'.$i.' 23:59:59');
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status>2");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y0 .= $num; $t0 += $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status=0");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y1 .= $num; $t1 += $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status=1");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y2 .= $num; $t2 += $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status=2");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y3 .= $num; $t3 += $num;
			}
			$title = $year.'年'.$month.'月会员支付统计报表(单位:'.$DT['money_unit'].')';
		} else {
			for($i = 1; $i < 13; $i++) {
				if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; $y2 .= ','; $y3 .= ','; }
				$xd .= "'".$i."月'";
				$F = datetotime($year.'-'.$i.'-01 00:00:00');
				$T = datetotime($year.'-'.$i.'-'.date('t', $F).' 23:59:59');
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status>2");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y0 .= $num; $t0 += $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status=0");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y1 .= $num; $t1 += $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status=1");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y2 .= $num; $t2 += $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE sendtime>=$F AND sendtime<=$T AND status=2");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y3 .= $num; $t3 += $num;
			}
			$title = $year.'年会员支付统计报表(单位:'.$DT['money_unit'].')';
		}
		include tpl('charge_stats', $module);
	break;
	case 'check':	
		$itemid or msg('请选择记录');
		$itemid = implode(',', $itemid);
		$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemid) AND status<2");
		$i = 0;
		while($r = $db->fetch_array($result)) {
			$money = $r['amount'] + $r['fee'];
			money_add($r['username'], $r['amount']);
			money_record($r['username'], $r['amount'], $PAY[$r['bank']]['name'], $_username, '在线支付', '人工');
			$db->query("UPDATE {$table} SET money='$money',status=4,editor='$_username',receivetime=$DT_TIME WHERE itemid=$r[itemid]");
			$i++;
		}
		dmsg('审核成功'.$i.'条记录', $forward);
	break;
	case 'recycle':
		$itemid or msg('请选择记录');
		$itemid = implode(',', $itemid);
		$db->query("UPDATE {$table} SET status=2,editor='$_username',receivetime=$DT_TIME WHERE itemid IN ($itemid) AND status=0");
		dmsg('作废成功'.$db->affected_rows().'条记录', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择记录');
		$itemid = implode(',', $itemid);
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemid) AND status=0");
		dmsg('删除成功'.$db->affected_rows().'条记录', $forward);
	break;
	default:
		$sfields = array('按条件', '会员名', '支付金额', '手续费', '实收金额', '事由', '备注', '操作人');
		$dfields = array('username', 'username', 'amount', 'fee', 'money', 'reason', 'note', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '支付金额降序', '支付金额升序', '手续费降序', '手续费升序', '实收金额降序', '实收金额升序', '下单时间降序', '下单时间升序', '支付时间降序', '支付时间升序', '流水号降序', '流水号升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'fee DESC', 'fee ASC', 'money DESC', 'money ASC', 'sendtime DESC', 'sendtime ASC', 'receivetime DESC', 'receivetime ASC', 'itemid DESC', 'itemid ASC');
		$dstatus = array('<span style="color:blue;">等待支付</span>', '<span style="color:red;">支付失败</span>', '<span style="color:#FF00FF;">记录作废</span>', '<span style="color:green;">支付成功</span>', '<span style="color:green;">人工审核</span>');

		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name(strtolower($editor))) or $editor = '';
		isset($datetype) && in_array($datetype, array('sendtime', 'receivetime')) or $datetype = 'sendtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($bank) or $bank = '';
		isset($mtype) && in_array($mtype, array('amount', 'fee', 'money')) or $mtype = 'amount';
		isset($minamount) or $minamount = '';
		isset($maxamount) or $maxamount = '';
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		$itemid or $itemid = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($bank) $condition .= " AND bank='$bank'";
		if($fromtime) $condition .= " AND $datetype>=$fromtime";
		if($totime) $condition .= " AND $datetype<=$totime";
		if($status !== '') $condition .= " AND status=$status";
		if($username) $condition .= " AND username='$username'";
		if($editor) $condition .= " AND editor='$editor'";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($minamount != '') $condition .= " AND $mtype>=$minamount";
		if($maxamount != '') $condition .= " AND $mtype<=$maxamount";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$amount = $fee = $money = 0;
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['sendtime'] = timetodate($r['sendtime'], 5);
			$r['receivetime'] = $r['receivetime'] ? timetodate($r['receivetime'], 5) : '--';
			$r['editor'] or $r['editor'] = 'system';
			$r['dstatus'] = $dstatus[$r['status']];
			$amount += $r['amount'];
			$fee += $r['fee'];
			$money += $r['money'];
			$lists[] = $r;
		}
		$amount = dround($amount, 2, 1);
		$fee = dround($fee, 2, 1);
		$money = dround($money, 2, 1);
		include tpl('charge', $module);
	break;
}
?>