<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
if($_cid) {
	switch($action) {
		case 'password':
			if($submit) {
				if($_child['password'] != dpassword($post['opassword'], $_child['passsalt'])) message($L['error_password']);
				require DT_ROOT.'/module/'.$module.'/child.class.php';
				$do = new child();
				$do->itemid = $_cid;
				if($do->password($post)) {
					dmsg($L['op_edit_success'], '?action=home');
				} else {
					message($do->errmsg);
				}
			}
			$head_title = $L['child_title_password'];
		break;
		default:
			$action = 'home';
			extract($_child);
			$head_title = $L['child_title_home'];
		break;
	}
} else {
	$MG['child_limit'] > -1 or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
	$menu_id = 2;
	include DT_ROOT.'/file/config/child.inc.php';
	require DT_ROOT.'/module/'.$module.'/child.class.php';
	$do = new child();
	$do->itemid = $itemid;
	$do->parent = $_username;
	switch($action) {
		case 'add':
			if($MG['child_limit']) {
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}member_child WHERE parent='$_username'");
				if($r['num'] >= $MG['child_limit']) dalert(lang($L['limit_add'], array($MG['child_limit'], $r['num'])), 'goback');
			}
			if($submit) {
				if($do->pass($post)) {
					$do->add($post);
					dmsg($L['op_add_success'], '?action=index');
				} else {
					message($do->errmsg);
				}
			} else {
				foreach($do->fields as $v) {
					$$v = '';
				}
				$gender = 1;
				$status = 3;
				$permission = array();
				$head_title = $L['child_title_add'];
			}
		break;
		case 'edit':
			$itemid or message();
			$r = $do->get_one();
			if(!$r || $r['parent'] != $_username) message();
			if($submit) {
				if($do->pass($post)) {
					$do->edit($post);
					dmsg($L['op_edit_success'], $forward);
				} else {
					message($do->errmsg);
				}
			} else {
				extract($r);
				$permission = explode(',', $permission);
				$head_title = $L['child_title_edit'];
			}
		break;
		case 'delete':
			$itemid or message();
			$r = $do->get_one();
			if(!$r || $r['parent'] != $_username) message();
			$do->delete($itemid);
			dmsg($L['op_del_success'], $forward);
		break;
		default:
			$condition = "parent='$_username'";
			$lists = $do->get_list($condition);
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}member_child WHERE parent='$_username'");
			$limit_used = $r['num'];
			$limit_free = $MG['child_limit'] && $MG['child_limit'] > $limit_used ? $MG['child_limit'] - $limit_used : 0;
			$head_title = $L['child_title'];
		break;
	}
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	if($pages) $pages = mobile_pages($items, $page, $pagesize);
	$head_name = $head_title;
}
include template('child', $module);
?>