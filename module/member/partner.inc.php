<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/module/'.$module.'/partner.class.php';
$do = new partner();
$table = $DT_PRE.'agent';
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$post['pusername'] = $_username;
				$post['pcompany'] = $_company;
				$post['addtime'] = $DT_TIME;
				$do->add($post);
				dmsg($L['op_checking'], '?status=2');
			} else {
				message($do->errmsg);
			}
		} else {
			(isset($username) && check_name($username)) or $username = '';
			$discount = 99;
			$mobile = $_mobile;
			$reason = '';
			$head_title = $L['partner_title_add'];
		}
	break;
	case 'delete':
		$itemid or message($L['partner_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if(!$item || $item['pusername'] != $_username) message();
			$do->delete($itemid);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'list':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['pusername'] != $_username || $r['status'] != 3) message();
		$MODS = array();
		$fid = 0;
		foreach($MODULE as $k=>$v) {
			if($v['module'] == 'sell' || $v['module'] == 'mall') {
				$MODS[$k] = $v;
				if(!$fid) $fid = $k;
			}
		}
		($MODS && $fid) or message();
		isset($MODS[$mid]) or $mid = $fid;
		$username = $r['username'];
		$company = $r['company'];

		$sorder  = $L['partner_goods_sorder'];
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'price DESC', 'price ASC', 'orders DESC', 'orders ASC', 'amount DESC', 'amount ASC', 'comments DESC', 'comments ASC', 'hits DESC', 'hits ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(2, 3)) or $status = 3;
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = "username='$username' AND status=3 AND price>0 AND amount>0";
		if($keyword) $condition .= match_kw('keyword', $keyword);
		$lists = $do->get_goods($mid, $condition, $dorder[$order]);
		$head_title = $L['partner_title_list'];
	break;
	default:
		$sfields = $L['partner_sfields'];
		$dfields = array('username', 'username', 'compnay', 'discount', 'reason', 'note');
		$sorder  = $L['partner_sorder'];
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'discount DESC', 'discount ASC', 'orders DESC', 'orders ASC', 'trades DESC', 'trades ASC', 'amount DESC', 'amount ASC', 'amounty DESC', 'amounty ASC', 'amountm DESC', 'amountm ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($username) && check_name($username)) or $username = '';
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(2, 3)) or $status = 3;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = "pusername='$_username' AND status=$status";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($username) $condition .= " AND username='$username'";
		$lists = $do->get_list($condition, $dorder[$order]);
		$head_title = $L['partner_title'];
	break;
}
$nums = array();
for($i = 2; $i < 4; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE pusername='$_username' AND status=$i");
	$nums[$i] = $r['num'];
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('partner', $module);
?>