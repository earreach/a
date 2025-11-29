<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
($MG['biz'] && $MG['homepage'] && $MG['style']) or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/module/'.$module.'/style.class.php';
$do = new style();
$table = $DT_PRE.'style';
$user = userinfo($_username);
$domain = $user['domain'];
$menu_id = 2;
switch($action) {
	case 'buy':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		$r or message($L['style_msg_not_exist']);
		if($r['groupid']) {
			$groupids = explode(',', $r['groupid']);
			if(!in_array($_groupid, $groupids)) message($L['style_msg_group']);
		}
		if(!$r['fee'] || $itemid == $MG['styleid']) {
			$styleid = $itemid == $MG['styleid'] ? 0 : $itemid;
			$db->query("UPDATE {$table} SET hits=hits+1 WHERE itemid=$itemid");
			$db->query("UPDATE {$DT_PRE}company SET styletime=0,styleid=$styleid WHERE userid=$_userid");
			userclean($_username);
			dmsg($L['style_msg_use_success'], '?action=order');
		}
		$currency = $r['currency'];
		$months = array(1, 2, 3, 6, 12, 24, 36, 48, 60);
		$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
		$auto = 0;
		$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
		if($auth && substr($auth, 0, 6) == 'style|') {
			$auto = $submit = 1;
			$tmp = explode('|', $auth);
			$month = intval($tmp[2]);
		}
		if($submit) {			
			in_array($month, $months) or message($L['style_msg_month']);
			$amount = $r['fee']*$month;
			if($currency == 'money') {
				$amount <= $_money or message($L['money_not_enough']);
				if($amount <= $DT['quick_pay']) $auto = 1;
				if(!$auto) {
					is_payword($_username, $password) or message($L['error_payword']);
				}
				money_add($_username, -$amount);
				money_record($_username, -$amount, $L['in_site'], 'system', $L['style_title_buy'], lang($L['style_record_buy'], array($r['title'].'('.$r['itemid'].')', $month)));
				$fd = 'money';
			} else {
				$amount <= $_credit or message($L['credit_not_enough'], 'credit'.DT_EXT.'?action=buy&amount='.($amount-$_credit));
				credit_add($_username, -$amount);
				credit_record($_username, -$amount, 'system', lang($L['style_record_buy'], array($r['title'].'('.$r['itemid'].')', $month)));
				$fd = 'credit';
			}
			$t = $db->get_one("SELECT * FROM {$table}_order WHERE username='$_username' AND styleid=$itemid ORDER BY totime DESC");
			$styletime = (($t && $t['totime'] > $DT_TIME) ? $t['totime'] : $DT_TIME) + 86400*30*$month;
			$title = addslashes($r['title']);
			$db->query("INSERT INTO {$table}_order (styleid,title,skin,fee,amount,currency,number,username,addtime,totime) VALUES ('$itemid','$title','$r[skin]','$r[fee]','$amount','$fd','$month','$_username','$DT_TIME','$styletime')");
			$db->query("UPDATE {$table} SET hits=hits+1,orders=orders+1,`$fd`=`$fd`+$amount WHERE itemid=$itemid");
			$db->query("UPDATE {$DT_PRE}company SET styletime=$styletime,styleid=$itemid WHERE userid=$_userid");
			userclean($_username);
			dmsg($L['style_msg_buy_success'], '?action=order');
		} else {
			$r['thumb'] = is_file(DT_ROOT.'/static/home/'.$r['skin'].'/thumb.gif') ? DT_STATIC.'home/'.$r['skin'].'/thumb.gif' : DT_STATIC.'home/image/thumb.gif';
			extract($r);
			$head_title = $L['style_title_buy'];
		}
	break;
	case 'choose':
		$itemid or message();
		$r = $db->get_one("SELECT * FROM {$table}_order WHERE itemid=$itemid");
		$r or message($L['style_msg_not_exist']);		
		$r['username'] == $_username or message($L['style_msg_not_exist']);
		$r['totime'] > $DT_TIME or message($L['style_msg_expired'], '?action=buy&itemid='.$r['styleid']);
		$db->query("UPDATE {$DT_PRE}company SET styletime='$r[totime]',styleid='$r[styleid]' WHERE userid=$_userid");
		userclean($_username);
		dmsg($L['style_msg_use_success'], '?action=order');
	case 'clear':
		$db->query("UPDATE {$DT_PRE}company SET styletime=0,styleid=0 WHERE userid=$_userid");
		userclean($_username);
		dmsg($L['style_msg_use_success'], '?action=order');
	case 'order':
		$styletime = $user['styletime'];
		$styleid = $user['styleid'];
		$gsid = intval($MG['styleid']);
		$c = array();
		$c['skin'] = 'default';
		$c['title'] = $c['fee'] = $c['currency'] = '';
		$c['number'] = $c['amount'] = $c['adddate'] = 'N/A';
		if($styleid) {			
			$t = $db->get_one("SELECT * FROM {$table} WHERE itemid=$styleid");
			if($t) {
				$c['skin'] = $t['skin'];
				$c['title'] = $t['title'];
				$c['fee'] = $t['fee'];
				$c['currency'] = $t['currency'];
			}
			$t = $db->get_one("SELECT * FROM {$table}_order WHERE username='$_username' AND styleid=$styleid AND totime=$styletime");
			if($t) {
				$c['skin'] = $t['skin'];
				$c['title'] = $t['title'];
				$c['fee'] = $t['fee'];
				$c['currency'] = $t['currency'];
				$c['number'] = $t['number'];
				$c['amount'] = $t['amount'];
				$c['adddate'] = timetodate($t['addtime'], 5);
			}
		} else if($gsid) {
			$t = $db->get_one("SELECT * FROM {$table} WHERE itemid=$gsid");
			if($t) {
				$c['skin'] = $t['skin'];
				$c['title'] = $t['title'];
				$c['fee'] = 0;
				$c['currency'] = $t['currency'];
			}
		}
		$c['thumb'] = is_file(DT_ROOT.'/static/home/'.$c['skin'].'/thumb.gif') ? DT_STATIC.'home/'.$c['skin'].'/thumb.gif' : DT_STATIC.'home/image/thumb.gif';
		$c['days'] = $styletime ? ($styletime > $DT_TIME ? ceil(($user['styletime']-$DT_TIME)/86400) : 0) : 'N/A';
		$c['todate'] = $styletime ? timetodate($styletime, 5) : 'N/A';

		$dfields = array('title', 'title', 'skin', 'template');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$dorder  = array('itemid desc', 'fee DESC', 'fee ASC', 'number DESC', 'number ASC', 'amount DESC', 'amount ASC', 'addtime DESC', 'addtime ASC', 'totime DESC', 'totime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		
		isset($currency) && in_array($currency, array('money', 'credit')) or $currency = '';

		$condition = "username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($currency) $condition .= " AND currency='$currency'";
		$lists = $do->get_order($condition, $dorder[$order]);
		$head_title = $L['style_title_order'];
	break;
	default:
		$TYPE = get_type('style', 1);
		$sfields = $L['style_sfields'];
		$dfields = array('title', 'title', 'author');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = $L['style_sorder'];
		$dorder  = array('listorder desc,addtime desc', 'fee DESC', 'fee ASC', 'hits DESC', 'hits ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;

		$all = isset($all) ? intval($all) : 1;
		$typeid = isset($typeid) ? intval($typeid) : 0;
		isset($currency) or $currency = '';
		$minfee = isset($minfee) ? dround($minfee) : '';
		$minfee or $minfee = '';
		$maxfee = isset($maxfee) ? dround($maxfee) : '';
		$maxfee or $maxfee = '';
		isset($currency) && in_array($currency, array('free', 'money', 'credit')) or $currency = '';
		$gsid = intval($MG['styleid']);

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$type_select = type_select($TYPE, 1, 'typeid', $L['choose_type'], $typeid);

		$condition = "1";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if(!$all) $condition .= " AND groupid LIKE '%,$_groupid,%'";
		if($typeid) $condition .= " AND typeid=$typeid";
		if($minfee) $condition .= " AND fee>=$minfee";
		if($maxfee) $condition .= " AND fee<=$maxfee";
		if($currency) {
			if($gsid) {
				$condition .= $currency == 'free' ? " AND (fee=0 OR itemid=$gsid)" : " AND currency='$currency' AND fee>0 AND itemid<>$gsid";
			} else {
				$condition .= $currency == 'free' ? " AND fee=0" : " AND currency='$currency' AND fee>0";
			}
		}
		$num0 = $db->count($table, $gsid ? "fee=0 OR itemid=$gsid" : "fee=0", $CFG['db_expires']);
		$num1 = $db->count($table, $gsid ? "currency='money' AND fee>0 AND itemid<>$gsid" : "currency='money' AND fee>0", $CFG['db_expires']);
		$num2 = $db->count($table, $gsid ? "currency='credit' AND fee>0 AND itemid<>$gsid" : "currency='credit' AND fee>0", $CFG['db_expires']);

		$lists = $do->get_list($condition, $dorder[$order]);
		$head_title = $L['style_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	if($pages) $pages = mobile_pages($items, $page, $pagesize);
	$head_name = $head_title;
}
include template('style', $module);
?>