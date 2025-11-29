<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
switch($action) {
	case 'line':
		$forward or $forward = $DT_PC ? DT_PATH : DT_MOB;
		$online = $_online ? 0 : 1;
		$db->query("UPDATE {$DT_PRE}member SET online=$online WHERE userid=$_userid");
		$db->query("UPDATE {$DT_PRE}online SET online=$online WHERE userid=$_userid");
		dheader($forward);
	break;
	case 'login':
		$DT['login_log'] == 2 or dheader('?action=index');
		require DT_ROOT.'/include/client.func.php';
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}login WHERE username='$_username'");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}login WHERE username='$_username' ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['logintime'] = timetodate($r['logintime'], 5);
			$r['area'] = ip2area($r['loginip'], 2);
			$r['os'] = get_os($r['agent']);
			$r['bs'] = get_bs($r['agent']);
			$lists[] = $r;
		}
		$head_title = $L['account_login_title'];	
	break;
	case 'promo':
		$code = dhtmlspecialchars(trim($code));
		if($code) {
			$p = $db->get_one("SELECT * FROM {$DT_PRE}finance_promo WHERE number='$code' AND totime>$DT_TIME");
			if($p && ($p['reuse'] || (!$p['reuse'] && !$p['username']))) {
				if($p['type']) {
					exit(lang($L['grade_msg_time_promo'], array($p['amount'])));
				} else {
					exit(lang($L['grade_msg_money_promo'], array($p['amount'])));
				}
			}
		}
		exit($L['grade_msg_bad_promo']);
	break;
	case 'group':
		$GROUP = cache_read('group.php');
		$lists = array();
		$i = 0;
		if($_groupid > 4) {
			foreach($GROUP as $k=>$v) {
				if($i) {					
					$G = cache_read('group-'.$k.'.php');
					if($G['grade']) $lists[$k] = $v;
				}
				if($k == $_groupid) $i = 1;
			}
		}
		$head_title = $L['grade_title'];
	break;
	case 'grade':
		$GROUP = cache_read('group.php');
		$lists = array();
		$i = $j = 0;
		if($_groupid > 4) {
			foreach($GROUP as $k=>$v) {
				if($i) {					
					$G = cache_read('group-'.$k.'.php');
					if($G['grade']) {
						$lists[$k] = $v;
						if(!$j) $j = $k;
					}
				}
				if($k == $_groupid) $i = 1;
			}
		}
		$groupid = isset($groupid) ? intval($groupid) : $j;
		isset($lists[$groupid]) or dheader('?action=index');
		$r = $db->get_one("SELECT status FROM {$DT_PRE}member_upgrade WHERE userid=$_userid ORDER BY itemid DESC");
		if($r && $r['status'] == 2) message($L['grade_msg_checking'], '?action=upgrade', 5);
		isset($month) or $month = 12;
		$TG = cache_read('group-'.$groupid.'.php');		
		$fees = $ends = array();		
		foreach($L['account_month'] as $k=>$v) {
			if($TG['fee_'.$k]) {
				$fees[$k] = $TG['fee_'.$k];
				$ends[$k] = timetodate(datetotime('+'.$k.' month'), 3);
			}
		}
		$fee = $TG['fee'];
		$auto = 0;
		$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
		if($auth && substr($auth, 0, 6) == 'grade|') {
			$t = explode('|', $auth);
			$_gid = intval($t[1]);
			$month = intval($t[2]);
			if($_gid == $groupid) $auto = $submit = 1;
		}
		if($submit) {
			if($fee > 0) {
				isset($fees[$month]) or $month = key($fees);
				$fee = $fees[$month];
				$fee <= $_money or message($L['money_not_enough']);
				if($fee <= $DT['quick_pay']) $auto = 1;
				if(!$auto) {
					is_payword($_username, $password) or message($L['error_payword']);
				}
				money_add($_username, -$fee);
				money_record($_username, -$fee, $L['in_site'], 'system', $L['grade_title'], $GROUP[$groupid]['groupname']);
				$company = dhtmlspecialchars($_company);
			} else {
				if(strlen($company) < 4) message($L['grade_pass_company']);
				$company = dhtmlspecialchars(trim($company));
				$t = $db->get_one("SELECT userid FROM {$DT_PRE}company WHERE company='$company' AND userid<>$_userid");
				if($t) message($L['grade_pass_company_exisits']);
			}
			$status = $TG['upgrade'] ? 2 : 3;
			$db->query("INSERT INTO {$DT_PRE}member_upgrade (userid,username,gid,groupid,company,addtime,month,ip,amount,status) VALUES ('$_userid','$_username','$_groupid','$groupid','$company','$DT_TIME','$month','$DT_IP','$fee','$status')");
			if($TG['upgrade']) message($L['grade_msg_check'], '?action=upgrade', 5);

			$itemid = $db->insert_id();
			$vip = $GROUP[$groupid]['vip'];
			$enterprise = $GROUP[$groupid]['type'] ? 1 : 0;
			$msql = $csql = $fee > 0 ? "groupid=$groupid" : "groupid=$groupid,company='$company'";
			$gsql = "edittime=$DT_TIME,editor='system',reason='$L[grade_auto]',note='$L[grade_title]'";
			$msql .= ",enterprise=$enterprise";
			if($vip) {
				$csql .= ",vip=$vip,vipt=$vip";
				$fromtime = $DT_TIME;
				$totime = datetotime('+'.$month.' month');
				$csql .= ",fromtime=$fromtime,totime=$totime";
				$db->query("INSERT INTO {$DT_PRE}company_vip (username,company,amount,gid,groupid,fromtime,totime,addtime,reason,note,editor) VALUES ('$_username','$company','$fee','$_groupid','$groupid','$fromtime','$totime','$DT_TIME', '$L[grade_title]','','$_username')");	
			}
			if($gsql) $db->query("UPDATE {$DT_PRE}member_upgrade SET $gsql WHERE itemid=$itemid");
			if($msql) $db->query("UPDATE {$DT_PRE}member SET $msql WHERE userid=$_userid");
			if($csql) $db->query("UPDATE {$DT_PRE}company SET $csql WHERE userid=$_userid");
			if($msql || $csql) userclean($_username);
			message($L['grade_msg_success'], '?action=upgrade');
		} else {
			isset($fees[$month]) or $month = key($fees);
			$fee = $TG['fee'] ? $fees[$month] : 0;
			$head_title = $L['grade_title'];
		} 
	break;
	case 'upgrade':
		$GROUP = cache_read('group.php');
		$_status = $L['account_upgrade_status'];
		$condition = "userid=$_userid";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}member_upgrade WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}member_upgrade WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = $r['edittime'] ? timetodate($r['edittime'], 3) : 'N/A';
			$lists[] = $r;
		}
		$head_title = $L['account_upgrade_title'];
	break;
	case 'renew':
		$GROUP = cache_read('group.php');
		$condition = "username='$_username'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}company_vip WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}company_vip WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['fromdate'] = timetodate($r['fromtime'], 3);
			$r['todate'] = timetodate($r['totime'], 3);
			$lists[] = $r;
		}
		$head_title = $L['account_renew_title'];
	break;
	case 'vip':
		$user = userinfo($_username);
		if(!$MG['vip'] || !$MG['fee'] || $user['totime'] < $DT_TIME) dheader('?action=index');
		$groupid = $MG['groupid']; 
		$TG = cache_read('group-'.$groupid.'.php');		
		isset($month) or $month = 12;
		$diff = $user['totime'] > $DT_TIME ? $user['totime'] - $DT_TIME : 0;
		$fees = $ends = array();
		foreach($L['account_month'] as $k=>$v) {
			if($TG['fee_'.$k]) {
				$fees[$k] = $TG['fee_'.$k];
				$ends[$k] = timetodate(datetotime('+'.$k.' month') + $diff, 3);
			}
		}
		$fee = $fees[$month];
		$auto = 0;
		$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
		if($auth && substr($auth, 0, 4) == 'vip|') {
			$auto = $submit = 1;			
			$month = substr($auth, 4);
		}
		if($submit) {
			isset($fees[$month]) or $month = key($fees);
			$fee = $fees[$month];
			$fee > 0 or message($L['vip_msg_fee']);
			$fee <= $_money or message($L['money_not_enough']);
			if($fee <= $DT['quick_pay']) $auto = 1;
			if(!$auto) {
				is_payword($_username, $password) or message($L['error_payword']);
			}
			$totime = datetotime('+'.$month.' month') + $diff;
			money_add($_username, -$fee);
			money_record($_username, -$fee, $L['in_site'], 'system', $L['vip_renew'], $L['account_month'][$month]);
			$db->query("UPDATE {$DT_PRE}company SET totime=$totime WHERE userid=$_userid");
			$db->query("INSERT INTO {$DT_PRE}company_vip (username,company,amount,gid,groupid,fromtime,totime,month,addtime,reason,note,editor) VALUES ('$user[username]','".addslashes($user['company'])."','$fee','$user[groupid]','$user[groupid]','$user[totime]','$totime','$month','$DT_TIME', '$L[account_vip_renew]','','$_username')");
			dmsg($L['vip_msg_success'], '?action=renew');
		} else {
			isset($fees[$month]) or $month = key($fees);
			$havedays = ceil(($user['totime'] - $DT_TIME)/86400);
			$todate = timetodate($user['totime'], 3);
			$fee = dround($fees[$month]);
			$newdate = timetodate(datetotime('+'.$month.' month') + $diff, 3);
			$head_title = $L['vip_renew'];
		}
	break;
	case 'username':
		$edit_username = substr($_username, 0, 4) == 'uid-' ? 1 : 0;
		if(!$edit_username && $MOD['edit_username']) {
			$condition = "username='$_username' AND type='username'";
			if($MOD['edit_username'] == 2) $condition .= "AND addtime>".datetotime(timetodate($DT_TIME, 'Y').'-01-01 00:00:00');
			$t = $db->get_one("SELECT itemid FROM {$DT_PRE}validate WHERE {$condition} ORDER BY itemid DESC");
			if(!$t) $edit_username = 1;
		}
		$edit_username or dheader('?action=index');

		if($submit) {
			isset($nusername) or $nusername = '';
			require DT_ROOT.'/module/'.$module.'/member.class.php';
			$do = new member;
			$do->userid = $_userid;
			if($do->rename($_username, $nusername)) {
				dmsg($L['op_edit_success'], '?action=index');
			} else {
				message($do->errmsg);
			}
		} else {
			$linkurl = $MG['homepage'] ? userurl('<span id="domain" class="f_red">'.$_username.'</span>') : '';
			$head_title = $L['account_username_title'];
		}
	break;
	case 'passport':
		$edit_passport = (substr($_passport, 0, 4) == 'uid-' || $_passport == $_username) ? 1 : 0;
		if(!$edit_passport && $MOD['edit_passport']) {
			$condition = "username='$_username' AND type='passport'";
			if($MOD['edit_username'] == 2) $condition .= "AND addtime>".datetotime(timetodate($DT_TIME, 'Y').'-01-01 00:00:00');
			$t = $db->get_one("SELECT itemid FROM {$DT_PRE}validate WHERE {$condition} ORDER BY itemid DESC");
			if(!$t) $edit_passport = 1;
		}
		$edit_passport or dheader('?action=index');

		if($submit) {
			isset($npassport) or $npassport = '';
			require DT_ROOT.'/module/'.$module.'/member.class.php';
			$do = new member;
			$do->userid = $_userid;
			if($do->rename_passport($_passport, $npassport, $_username)) {
				dmsg($L['op_edit_success'], '?action=index');
			} else {
				message($do->errmsg);
			}
		} else {			
			$head_title = $L['account_passport_title'];
		}
	break;
	case 'close':
		($MOD['account_close'] && $_groupid > 1) or dheader('?action=index');
		$t = $db->get_one("SELECT itemid FROM {$DT_PRE}validate WHERE username='$_username' AND type='close' AND status=2 ORDER BY itemid DESC");
		if($t) message($L['account_close_msg'], '?action=index', 5);
		if($submit) {
			$reason = strip_tags(trim($reason));
			strlen($reason) > 2 or message($L['pass_content']);
			$db->query("INSERT INTO {$DT_PRE}validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$reason','','close','$_username','$DT_IP','$DT_TIME','2','$_username','$DT_TIME')");
			dmsg($L['account_close_msg'], '?action=index');
		} else {			
			$head_title = $L['account_close_title'];
		}
	break;
	case 'qrcode':
		if(strpos($forward, $MOD['mobile']) === false) $forward = '';
		dheader(DT_PATH.'api/qrcode'.DT_EXT.'?size=4&auth='.urlencode($MOD['mobile'].$DT['file_login'].'?action=scan&auth='.encrypt($_username.'|'.$DT_IP.'|'.$_userid.'|'.$job.'|'.$forward, DT_KEY.'SCANPC', 180)));
	break;
	case 'scan':
		if($DT_PC) {
			if(strpos($forward, $MOD['mobile']) === false) $forward = '';
			$head_title = $L['account_scan_title'];
		} else {
			$cid = 0;
			isset($sid) or $sid = '';
			is_md5($sid) or $cid = 1;
			if($cid == 0) {
				$t = $db->get_one("SELECT * FROM {$DT_PRE}app_scan WHERE sid='$sid'");
				if($t) {
					if($t['username'] && $t['username'] != 'sc' && $t['username'] != $_username) $cid = 2;
				} else {
					$cid = 1;
				}
				if($cid == 0) {
					if($job == 'login') {
						$db->query("UPDATE {$DT_PRE}app_scan SET username='$_username' WHERE sid='$sid'");
						$cid = 3;
					} else {
						$db->query("UPDATE {$DT_PRE}app_scan SET username='sc' WHERE sid='$sid'");
					}
				}
			}
			$head_title = '登录确认';
		}
	break;
	case 'setting':
		if($submit) {
			(isset($verify) && in_array($verify, array(0, 1, 2))) or $verify = 0;
			(isset($fmobile) && in_array($fmobile, array(0, 1))) or $fmobile = 1;
			(isset($femail) && in_array($femail, array(0, 1))) or $femail = 1;
			(isset($send) && in_array($send, array(0, 1))) or $send = 1;
			(isset($sound) && in_array($sound, array(0, 1, 2, 3))) or $sound = 0;
			$reply = isset($reply) ? strip_tags(trim($reply)) : '';
			$db->query("UPDATE {$DT_PRE}member SET verify=$verify,fmobile=$fmobile,femail=$femail,verify=$verify,sound=$sound,send=$send WHERE userid=$_userid");
			$db->query("UPDATE {$DT_PRE}member_misc SET reply='$reply' WHERE userid=$_userid");
			userclean($_username);
			dmsg($L['op_set_success'], '?action='.$action);
		} else {
			$user = userinfo($_username);
			extract($user);
			$head_title = $L['account_setting_title'];
		}
	break;
	default:
		$user = userinfo($_username);
		extract($user);
		$expired = $totime && $totime < $DT_TIME ? true : false;
		$havedays = $expired ? 0 : ceil(($totime-$DT_TIME)/86400);
		$GD = cache_read('grade-'.$_gradeid.'.php');

		$edit_username = substr($_username, 0, 4) == 'uid-' ? 1 : 0;
		if(!$edit_username && $MOD['edit_username']) {
			$condition = "username='$_username' AND type='username'";
			if($MOD['edit_username'] == 2) $condition .= "AND addtime>".datetotime(timetodate($DT_TIME, 'Y').'-01-01 00:00:00');
			$t = $db->get_one("SELECT itemid FROM {$DT_PRE}validate WHERE {$condition} ORDER BY itemid DESC");
			if(!$t) $edit_username = 1;
		}

		$edit_passport = (substr($_passport, 0, 4) == 'uid-' || $_passport == $_username) ? 1 : 0;
		if(!$edit_passport && $MOD['edit_passport']) {
			$condition = "username='$_username' AND type='passport'";
			if($MOD['edit_username'] == 2) $condition .= "AND addtime>".datetotime(timetodate($DT_TIME, 'Y').'-01-01 00:00:00');
			$t = $db->get_one("SELECT itemid FROM {$DT_PRE}validate WHERE {$condition} ORDER BY itemid DESC");
			if(!$t) $edit_passport = 1;
		}

		$head_title = $L['account_title'];	
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('account', $module);
?>