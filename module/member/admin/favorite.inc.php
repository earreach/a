<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/favorite.class.php';
$do = new favorite();
$menus = array (
    array('收藏列表', '?moduleid='.$moduleid.'&file='.$file),
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
			include tpl('favorite_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择收藏');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '标题', '网址', '会员');
		$dfields = array('title', 'title', 'url', 'username');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;

		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$tid = isset($tid) ? intval($tid) : 0;
		$tid or $tid = '';
		$userid = isset($userid) ? intval($userid) : 0;
		$userid or $userid = '';
		(isset($username) && check_name($username)) or $username = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$module_select = module_select('mid', '模块', $mid);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($mid) $condition .= " AND mid='$mid'";
		if($tid) $condition .= " AND tid='$tid'";
		if($userid) $condition .= " AND userid=$userid";
		if($username) $condition .= " AND username='$username'";
		$lists = $do->get_list($condition);
		include tpl('favorite', $module);
	break;
}
?>