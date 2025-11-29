<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/address.class.php';
$do = new address();
$menus = array (
    array('地址列表', '?moduleid='.$moduleid.'&file='.$file),
);

switch($action) {
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			include tpl('address_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择地址');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '姓名', '地址', '邮编', '手机', '电话', '标签', '会员', '备注');
		$dfields = array('address', 'truename', 'address', 'postcode', 'mobile', 'telephone', 'typename', 'username', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;

		(isset($username) && check_name($username)) or $username = '';
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		if($username) $condition .= " AND username='$username'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($mobile) $condition .= " AND mobile='$mobile'";
		$lists = $do->get_list($condition, 'itemid DESC');
		include tpl('address', $module);
	break;
}
?>