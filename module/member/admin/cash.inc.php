<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('提现记录', '?moduleid='.$moduleid.'&file='.$file),
    array('统计报表', '?moduleid='.$moduleid.'&file='.$file.'&action=stats'),
);
$BANKS = explode('|', trim($MOD['cash_banks']));
$dstatus = $L['cash_status'];
$table = $DT_PRE.'finance_cash';
if($action == 'edit' || $action == 'show') {
	$itemid or msg('未指定记录');
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid ");
	$item or msg('记录不存在');
	$item['addtime'] = timetodate($item['addtime'], 5);
	$item['edittime'] = timetodate($item['edittime'], 5);
	$member = userinfo($item['username']);
}
switch($action) {
	case 'stats':
		$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
		$year or $year = date('Y', $DT_TIME);
		$month = isset($month) ? intval($month) : date('n', $DT_TIME);
		$xd = $y0 = $y1 = $y2 = $y3 = '';
		if($month) {
			$L = date('t', datetotime($year.'-'.$month.'-01'));
			for($i = 1; $i <= $L; $i++) {
				if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; $y2 .= ','; $y3 .= ','; }
				$xd .= "'".$i."日'";
				$F = datetotime($year.'-'.$month.'-'.$i.' 00:00:00');
				$T = datetotime($year.'-'.$month.'-'.$i.' 23:59:59');
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=3");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y0 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=0");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y1 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=2");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y2 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=1");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y3 .= $num;
			}
			$title = $year.'年'.$month.'月会员提现统计报表(单位:'.$DT['money_unit'].')';
		} else {
			for($i = 1; $i < 13; $i++) {
				if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; $y2 .= ','; $y3 .= ','; }
				$xd .= "'".$i."月'";
				$F = datetotime($year.'-'.$i.'-01 00:00:00');
				$T = datetotime($year.'-'.$i.'-'.date('t', $F).' 23:59:59');
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=3");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y0 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=0");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y1 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=2");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y2 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE addtime>=$F AND addtime<=$T AND status=1");
				$num = $t['num'] ? dround($t['num']) : 0;
				$y3 .= $num;
			}
			$title = $year.'年会员提现统计报表(单位:'.$DT['money_unit'].')';
		}
		include tpl('cash_stats', $module);
	break;
	case 'edit':
		if($item['status'] > 0) msg('此申请已受理');
		if($submit) {
			isset($status) or msg('请指定受理结果');
			$money = $item['amount'] + $item['fee'];
			if($status == 3) {
				//
			} else if($status == 2 || $status == 1) {
				$note or msg('请填写原因备注');
				money_add($item['username'], $money);
				money_record($item['username'], $money, '站内', 'system', '提现失败', '流水号:'.$itemid);
			} else {
				msg();
			}
			$db->query("UPDATE {$table} SET status=$status,editor='$_username',edittime=$DT_TIME,note='$note' WHERE itemid=$itemid");
			dmsg('受理成功', $forward);
		} else {
			include tpl('cash_edit', $module);
		}
	break;
	case 'show':
		if($item['status'] == 0) msg('申请尚未受理');
		include tpl('cash_show', $module);
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '会员名', '金额', '手续费', '收款方式', '开户网点', '收款户名', '收款帐号', '备注', '受理人');
		$dfields = array('username', 'username', 'amount', 'fee', 'bank', 'branch', 'truename', 'account', 'note', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('排序方式', '提现金额降序', '提现金额升序', '手续费降序', '手续费升序', '申请时间降序', '申请时间升序', '受理时间降序', '受理时间升序', '流水降序', '流水升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'fee DESC', 'fee ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'itemid DESC', 'itemid ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;

		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name($editor)) or $editor = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($bank) or $bank = '';
		isset($mtype) or $mtype = 'amount';
		isset($minamount) or $minamount = '';
		isset($maxamount) or $maxamount = '';
		$itemid or $itemid = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$order_select = dselect($sorder, 'order', '', $order);

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
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		$amount = $fee = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['edittime'] = $r['edittime'] ? timetodate($r['edittime'], 5) : '--';
			$r['dstatus'] = $dstatus[$r['status']];
			$amount += $r['amount'];
			$fee += $r['fee'];
			$lists[] = $r;
		}
		$amount = dround($amount, 2, 1);
		$fee = dround($fee, 2, 1);
		include tpl('cash', $module);
	break;
}
?>