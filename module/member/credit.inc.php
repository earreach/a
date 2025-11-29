<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
switch($action) {
	case 'exchange':
		if($MOD['credit_exchange'] && $MOD['ex_host'] && $MOD['ex_user'] && $MOD['ex_data'] && $MOD['ex_type']) {
			include DT_ROOT.'/config.inc.php';
			$db_class = 'db_'.$CFG['database'];
			$ex = new $db_class;
			$ex->connect($MOD['ex_host'], $MOD['ex_user'], $MOD['ex_pass'], $MOD['ex_data'], $CFG['db_expires'], $CFG['db_charset'], $CFG['pconnect']);
			$fd = $MOD['ex_fdnm'];
			$px = $MOD['ex_prex'];
			if($MOD['ex_type'] == 'PW') {
				$tb = $px.'memberdata';
				$r = $ex->get_one("SELECT `uid` FROM `{$px}members` WHERE `username`='$_passport'");
				if($r) {
					$uid = $r['uid'];
					$r = $ex->get_one("SELECT `$fd` FROM `{$tb}` WHERE `uid`=$uid");
					$num = intval($r[$fd]);
				} else {
					message($L['credit_msg_active'], '?action=index');
				}
			} elseif($MOD['ex_type'] == 'DZX') {
				$tb = $px.'common_member_count';
				$r = $ex->get_one("SELECT `uid` FROM `{$px}common_member` WHERE `username`='$_passport'");
				if($r) {
					$uid = $r['uid'];
					$r = $ex->get_one("SELECT `$fd` FROM `{$tb}` WHERE `uid`=$uid");
					$num = intval($r[$fd]);
				} else {
					message($L['credit_msg_active'], '?action=index');
				}
			} else {
				$tb = $px.'members';
				$r = $ex->get_one("SELECT `uid`,`$fd` FROM `{$tb}` WHERE `username`='$_passport'");
				if($r) {
					$uid = $r['uid'];
					$num = intval($r[$fd]);
				} else {
					message($L['credit_msg_active'], '?action=index');
				}
			}
			if($submit) {
				$num > 0 or message($L['credit_pass_ex_min']);
				$amount = intval($amount);
				if($amount > 0 && $amount <= $num) {
					$ex->query("UPDATE `{$tb}` SET `{$fd}`=`{$fd}`-{$amount} WHERE `uid`=$uid");
					$db = new $db_class;
					$db->halt = DT_DEBUG ? 1 : 0;
					$db->connect($CFG['db_host'], $CFG['db_user'], $CFG['db_pass'], $CFG['db_name'], $CFG['db_expires'], $CFG['db_charset'], $CFG['pconnect']);
					credit_add($_username, $amount*$MOD['ex_rate']);
					credit_record($_username, $amount*$MOD['ex_rate'], 'system', $L['credit_exchange_title'], $amount.$MOD['ex_name']);
					dmsg($L['credit_msg_exchange'], '?action=index');
				} else {
					message($L['credit_pass_ex_max'].$num);
				}
			}
		} else {
			message($L['feature_close'], '?action=index');
		}
		$head_title = $L['credit_exchange_title'];
	break;
	case 'buy':
		if($MOD['credit_buy'] && $MOD['credit_price']) {
			$C = explode('|', trim($MOD['credit_buy']));
			$P = explode('|', trim($MOD['credit_price']));
			$auto = 0;
			$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
			if($auth && substr($auth, 0, 7) == 'credit|') {
				$auto = $submit = 1;
				$type = intval(substr($auth, 7));
			}
			if($submit) {
				isset($C[$type]) or message($L['credit_msg_buy_amount']);
				$amount = $P[$type];
				$credit = $C[$type];
				if($amount > 0) {
					$amount <= $_money or message($L['money_not_enough']);
					if($amount <= $DT['quick_pay']) $auto = 1;
					if(!$auto) {
						is_payword($_username, $password) or message($L['error_payword']);
					}
					money_add($_username, -$amount);
					money_record($_username, -$amount, $L['in_site'], 'system', $L['buy'].$DT['credit_name'], $credit.$DT['credit_unit']);
					if($credit > 0) {
						credit_add($_username, $credit);
						credit_record($_username, $credit, 'system', $L['buy'].$DT['credit_name'], $amount.$DT['money_unit']);
					}
				}
				dmsg($L['credit_msg_buy_success'], '?action=index');
			} else {
				$type = isset($type) ? intval($type) : 0;
				isset($C[$type]) or $type = 0;
				$money = $P[$type];
			}
		} else {
			message($L['feature_close'], '?action=index');
		}
		$head_title = $L['credit_buy_title'];
	break;
	case 'grade':
		$GRADE = cache_read('grade.php');
		require DT_ROOT.'/module/'.$module.'/member.class.php';
		$do = new member;
		$gid = $do->get_gradeid($_credit);
		if($gid != $_gradeid) {
			$_gradeid = $gid;
			$db->query("UPDATE {$DT_PRE}member SET gradeid=$gid WHERE userid=$_userid");
			userclean($_username);
		}
		$maxid = $minid = 0;
		foreach($GRADE as $k=>$v) {
			if(!$minid) $minid = $k;
			if($k > $maxid) $maxid = $k;
		}
		if($_gradeid >= $maxid) {
			$left = 0;
		} else if($_gradeid <= $minid) {
			$left = $GRADE[$minid]['credit'] - $_credit;
		} else {
			$left = $GRADE[$_gradeid]['credit'] - $_credit;
		}
		$head_title = $L['credit_grade_title'];
	break;
	case 'less':		
		$head_title = $L['credit_less_title'];
	break;
	default:
		$sfields = $L['credit_fields'];
		$dfields = array('reason', 'amount', 'reason', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($type) or $type = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0" ;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}finance_credit WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}finance_credit WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$head_title = $L['credit_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('credit', $module);
?>