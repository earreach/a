<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('添加子账号', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('子账号列表', '?moduleid='.$moduleid.'&file='.$file),
);
include DT_ROOT.'/file/config/child.inc.php';
require DT_ROOT.'/module/'.$module.'/child.class.php';
$do = new child();
function get_menumods() {
	global $MODULE;
	$MENUMODS = array();
	foreach($MODULE as $m) {
		if($m['moduleid'] > 4 && is_file(DT_ROOT.'/module/'.$m['module'].'/my.inc.php')) {
			$MENUMODS[] = $m['moduleid'];
		}
	}
	return $MENUMODS;
}
function is_parent($username) {
	if(!check_name($username)) return false;
	return userinfo($username);
}
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				is_parent($post['parent']) or msg('所属会员不存在');
				$do->parent = $post['parent'];
				$do->add($post);
				dmsg('添加成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$gender = 1;
			$status = 3;
			$permission = array();
			$MENUMODS = get_menumods();
			$menuid = 0;
			include tpl('child_edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				is_parent($post['parent']) or msg('所属会员不存在');
				$do->parent = $post['parent'];
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$permission = explode(',', $permission);
			$MENUMODS = get_menumods();
			$menuid = 1;
			include tpl('child_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择账号');
		isset($recycle) ? $do->recycle($itemid) : $do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '所属会员', '会员名', '昵称', '角色', '部门', '姓名', '手机', '权限');
		$dfields = array('parent', 'parent', 'username', 'nickname', 'role', 'department', 'truename', 'mobile', 'permission');
		$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '修改时间降序', '修改时间升序', '登录时间降序', '登录时间升序', '登录次数降序', '登录次数升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'logintime DESC', 'logintime ASC', 'logintimes DESC', 'logintimes ASC');

		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($username) && check_name($username)) or $username = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime', 'logintime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) ? intval($status) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($username) $condition .= " AND parent='$username'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($username) $condition .= " AND parent='$username'";
		if($status) $condition .= " AND status=$status";
		$lists = $do->get_list($condition, $dorder[$order]);
		$menuid = 1;
		include tpl('child', $module);
	break;
}
?>