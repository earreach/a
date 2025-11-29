<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$condition = "username='$_username'";
switch($action) {
	case 'pay':
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($currency) or $currency = '';
		$module_select = module_select('mid', $L['module_name'], $mid);
		if($keyword) $condition .= match_kw('title', $keyword);
		if($fromtime) $condition .= " AND paytime>=$fromtime";
		if($totime) $condition .= " AND paytime<=$totime";
		if($mid) $condition .= " AND mid=$mid";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($currency) $condition .= " AND currency='$currency'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_pay WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_pay WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$fee = 0;
		while($r = $db->fetch_array($result)) {
			$r['paytime'] = timetodate($r['paytime'], 5);
			$r['url'] = gourl('?mid='.$r['mid'].'&itemid='.$r['tid'].'&page=2');
			$fee += $r['fee'];
			$lists[] = $r;
		}
		$head_title = $L['record_title_pay'];	
	break;
	case 'award':
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$module_select = module_select('mid', $L['module_name'], $mid);
		if($keyword) $condition .= match_kw('title', $keyword);
		if($fromtime) $condition .= " AND paytime>=$fromtime";
		if($totime) $condition .= " AND paytime<=$totime";
		if($mid) $condition .= " AND mid=$mid";
		if($itemid) $condition .= " AND itemid=$itemid";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_award WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_award WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$fee = 0;
		while($r = $db->fetch_array($result)) {
			$r['paytime'] = timetodate($r['paytime'], 5);
			$r['url'] = gourl('?mid='.$r['mid'].'&itemid='.$r['tid'].'&page=2');
			$fee += $r['fee'];
			$lists[] = $r;
		}
		$head_title = $L['record_title_award'];	
	break;
	default:
		$BANKS = explode('|', trim($MOD['pay_banks']));
		$sfields = $L['record_sfields'];
		$dfields = array('reason', 'amount', 'bank', 'reason', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($type) or $type = 0;
		(isset($agent) && check_name($agent)) or $agent = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0";
		if($agent) $condition .= " AND editor='$agent' AND reason='".$L['fee_agent']."'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_record WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_record WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$head_title = $L['record_title'];	
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('record', $module);
?>