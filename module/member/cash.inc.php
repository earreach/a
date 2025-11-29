<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MG['cash'] or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$user = userinfo($_username);
$BANKS = explode('|', trim($MOD['cash_banks']));
switch($action) {
	case 'add':
		$MOD['cash_enable'] or message($L['feature_close'], $DT_PC ? $MOD['linkurl'] : $MOD['mobile'], 3);
		$head_title = $L['cash_title'];
	break;
	case 'confirm':
		$amount or message($L['cash_pass_amount']);
		if($amount > $_money) message($L['cash_pass_amount_large']);
		if($MOD['cash_min'] && $amount < $MOD['cash_min']) message($L['cash_pass_amount_min'].$MOD['cash_min']);
		if($MOD['cash_max'] && $amount > $MOD['cash_max']) message($L['cash_pass_amount_max'].$MOD['cash_max']);
		if($MOD['cash_times']) {
			$r = $db->get_one("SELECT COUNT(*) as num FROM {$DT_PRE}finance_cash WHERE username='$_username' AND addtime>$DT_TODAY-86400");
			if($r['num'] >= $MOD['cash_times']) message(lang($L['cash_pass_amount_day'], array($MOD['cash_times'])), '?action=record', 5);
		}
		$amount = dround($amount);
		$fee = 0;
		if($MOD['cash_fee']) {
			$fee = dround($amount*$MOD['cash_fee']/100);
			if($MOD['cash_fee_min'] && $fee < $MOD['cash_fee_min']) $fee = $MOD['cash_fee_min'];
			if($MOD['cash_fee_max'] && $fee > $MOD['cash_fee_max']) $fee = $MOD['cash_fee_max'];
		}
		$money = $amount - $fee;
		if($submit) {
			is_payword($_username, $password) or message($L['error_payword']);
			$user = daddslashes($user);
			$name = $MG['type'] ? $user['company'] : $user['truename'];
			$db->query("INSERT INTO {$DT_PRE}finance_cash (username,bank,branch,account,truename,amount,fee,addtime,ip) VALUES ('$_username','$user[bank]','$user[branch]','$user[account]','$name','$money','$fee','$DT_TIME','$DT_IP')");
			$cid = $db->insert_id();
			money_add($_username, -$amount);
			money_record($_username, -$amount, $L['in_site'], 'system', $L['cash_title'], $L['charge_id'].$cid);
			message($L['cash_msg_success'], '?action=record', 5);
		} else {
			$head_title = $L['cash_title_confirm'];
		}
	break;
	default:
		$condition = "username='$_username'";
		$dstatus = $L['cash_status'];
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($type) or $type = 0;
		isset($bank) or $bank = '';
		if($bank) $condition .= " AND bank='$bank'";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_cash WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_cash WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$amount = $fee = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['edittime'] = $r['edittime'] ? timetodate($r['edittime'], 5) : '--';
			$r['dstatus'] = $dstatus[$r['status']];
			$amount += $r['amount'];
			$fee += $r['fee'];
			$lists[] = $r;
		}
		$head_title = $L['cash_title_record'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw)$back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('cash', $module);
?>