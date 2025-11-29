<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($action) login();
switch($action) {
	case 'record':
		require DT_ROOT.'/include/post.func.php';
		$sfields = $L['invite_sfields'];
		$dfields = array('username', 'username', 'passport', 'company');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "inviter='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND regtime>=$fromtime";
		if($totime) $condition .= " AND regtime<=$totime";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}member WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = $users = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}member WHERE {$condition} ORDER BY userid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['regdate'] = timetodate($r['regtime'], 5);
			$lists[] = $r;
		}
		$head_title = $L['invite_title_record'];
	break;
	case 'code':
		$head_title = $L['invite_title'];
		$url = $MOD['linkurl'].'invite'.DT_EXT.'?user='.$_username;
	break;
	default:
		$reason = $L['invite_title'];
		$userurl = '';
		if(isset($user) && check_name($user)) {
			$c = $db->get_one("SELECT linkurl,username FROM {$DT_PRE}company WHERE username='$user'");
			if($c) {
				$userurl = $c['linkurl'];
				$user = $username = $c['username'];
				$could_credit = true;
				if($MOD['credit_ip'] <= 0) $could_credit = false;
				if($could_credit) {
					$r = $db->get_one("SELECT itemid FROM {$DT_PRE}finance_credit WHERE note='$DT_IP' AND addtime>$DT_TIME-86400");
					if($r) $could_credit = false;
				}
				if($could_credit && $MOD['credit_maxip'] > 0) {
					$r = $db->get_one("SELECT SUM(amount) AS total FROM {$DT_PRE}finance_credit WHERE username='$username' AND addtime>$DT_TIME-86400 AND reason='$reason'");
					if($r['total'] > $MOD['credit_maxip']) $could_credit = false;
				}
				if($could_credit) {
					credit_add($username, $MOD['credit_ip']);
					credit_record($username, $MOD['credit_ip'], 'system', $reason, $DT_IP);
				}
				set_cookie('inviter', encrypt($username, DT_KEY.'INVITER'), $DT_TIME + 30*86400);
			}
		} else if(isset($uid) && $mid > 4 && $itemid) {
			$uid = intval($uid);
			if($uid > 0) {
				$m = $db->get_one("SELECT username FROM {$DT_PRE}member WHERE userid=$uid");
				if($m) {
					$tb = get_table($mid);
					if($tb) {
						$itemid = intval($itemid);
						$r = $db->get_one("SELECT linkurl,status FROM {$tb} WHERE itemid=$itemid");
						if($r && $r['status'] == 3) {
							set_cookie('inviter', encrypt($m['username'], DT_KEY.'INVITER'), $DT_TIME + 30*86400);
							dheader($MODULE[$mid]['linkurl'].$r['linkurl']);
						}
					}
				}
			}
		}
		$goto = isset($goto) ? trim($goto) : '';
		$URI = DT_PATH;
		if($goto == 'register') {
			$URI = $MODULE[2]['linkurl'].$DT['file_register'];
		} else if($goto == 'homepage') {
			if($userurl) $URI = $userurl;
		} else if(is_url($goto)) {
			if(substr_count($goto, '://') == 1) {
				if(is_uri($goto)) $URI = $goto;
			}
		}
		dheader($URI);
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('invite', $module);
?>