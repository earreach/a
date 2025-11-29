<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MG['biz'] or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$TYPE = get_type('agent-'.$_userid);
require DT_ROOT.'/module/'.$module.'/agent.class.php';
$do = new agent();
$menu_id = 2;
$table = $DT_PRE.'agent';
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$post['username'] = $_username;
				$post['addtime'] = $DT_TIME;
				$do->add($post);
				dmsg($L['op_add_success'], '?action=index');
			} else {
				message($do->errmsg);
			}
		} else {
			$pusername = $mobile = $note = '';
			$discount = 99;
			$typeid = 0;
			$status = 3;
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type']);
			$head_title = $L['agent_title_add'];
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();
		if($submit) {
			if($do->pass($post)) {
				$post['username'] = $_username;
				$do->edit($post);
				dmsg($L['op_edit_success'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			extract($r);
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type'], $typeid);
			$head_title = $L['agent_title_edit'];
		}
	break;
	case 'delete':
		$itemid or message($L['agent_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if(!$item || $item['username'] != $_username) message();
			$do->delete($itemid);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'check':
		$itemid or message($L['agent_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if(!$item || $item['username'] != $_username) message();
			$do->check($itemid, $status);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	default:
		$sfields = $L['agent_sfields'];
		$dfields = array('pusername', 'pusername', 'pcompnay', 'mobile', 'discount', 'reason', 'note');
		$sorder  = $L['agent_sorder'];
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'discount DESC', 'discount ASC', 'orders DESC', 'orders ASC', 'trades DESC', 'trades ASC', 'amount DESC', 'amount ASC', 'amounty DESC', 'amounty ASC', 'amountm DESC', 'amountm ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		(isset($username) && check_name($username)) or $username = '';
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(2, 3)) or $status = 3;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);
		$condition = "username='$_username' AND status=$status";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($username) $condition .= " AND pusername='$username'";
		$lists = $do->get_list($condition, $dorder[$order]);
		$head_title = $L['agent_title'];
	break;
}
$nums = array();
for($i = 2; $i < 4; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE username='$_username' AND status=$i");
	$nums[$i] = $r['num'];
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	$head_name = $head_title;
}
include template('agent', $module);
?>