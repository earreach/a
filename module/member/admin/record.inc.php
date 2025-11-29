<?php
defined('DT_ADMIN') or exit('Access Denied');
isset($username) or $username = '';
$menus = array (
    array($DT['money_name'].'增减', '?moduleid='.$moduleid.'&file='.$file.'&username='.$username.'&action=add'),
    array($DT['money_name'].'流水', '?moduleid='.$moduleid.'&file='.$file.'&username='.$username),
);
$table = $DT_PRE.'finance_record';
$BANKS = explode('|', trim($MOD['pay_banks']));
switch($action) {
	case 'clear':
		$time = $DT_TODAY - 90*86400;
		$db->query("DELETE FROM {$table} WHERE addtime<$time");
		dmsg('清理成功', $forward);
	break;
	case 'add':
		if($submit) {
			$username or msg('请填写会员名');
			$amount or msg('请填写金额');
			$bank or msg('请选择支付方式');
			$reason or msg('请填写事由');
			$amount = dround($amount);
			if($amount <= 0) msg('金额格式错误');
			$bank = trim($bank);
			if(!$type) $amount = -$amount;
			$error = '';
			$success = 0;
			$usernames = explode("\n", trim($username));
			foreach($usernames as $username) {
				$username = trim($username);
				if(!$username) continue;
				$r = $db->get_one("SELECT username,money FROM {$DT_PRE}member WHERE username='$username'");
				if(!$r) {
					$error .= '<br/>会员['.$username.']不存在';
					continue;
				}
				if(!$type && $r['money'] < abs($amount)) {
					$error .= '<br/>会员['.$username.']余额不足，当前余额为:'.$r['money'];
					continue;
				}
				$reason or $reason = '现金';
				$note or $note = '手工';
				money_add($username, $amount);
				money_record($username, $amount, $bank, $_username, $reason, $note);
				$success++;
			}
			if($error) msg('操作成功 '.$success.' 位会员，发生以下错误：'.$error);
			dmsg('操作成功', '?moduleid='.$moduleid.'&file='.$file);
		} else {
			if(isset($userid)) {
				if($userid) {
					$userids = is_array($userid) ? implode(',', $userid) : $userid;					
					$result = $db->query("SELECT username FROM {$DT_PRE}member WHERE userid IN ($userids)");
					while($r = $db->fetch_array($result)) {
						$username .= $r['username']."\n";
					}
				}
			}
			include tpl('record_add', $module);
		}
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '会员名', '金额', '银行', '事由', '备注', '操作人');
		$dfields = array('username', 'username', 'amount', 'bank', 'reason', 'note', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('排序方式', '金额降序', '金额升序', '余额降序', '余额升序', '时间降序', '时间升序', '流水降序', '流水升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'balance DESC', 'balance ASC', 'addtime DESC', 'addtime ASC', 'itemid DESC', 'itemid ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;

		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($bank) or $bank = '';
		isset($type) or $type = 0;
		isset($mtype) && in_array($mtype, array('amount', 'balance')) or $mtype = 'amount';
		$minamount = isset($minamount) ? dround($minamount, 2, 1) : '';
		$minamount != 0.00 or $minamount = '';
		$maxamount = isset($maxamount) ? dround($maxamount, 2, 1) : '';
		$maxamount != 0.00 or $maxamount = '';
		$itemid or $itemid = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($bank) $condition .= " AND bank='$bank'";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0";
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
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$income = dround($income, 2, 1);
		$expense = dround($expense, 2, 1);
		include tpl('record', $module);
	break;
}
?>