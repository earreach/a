<?php
defined('DT_ADMIN') or exit('Access Denied');
$gid = isset($gid) ? intval($gid) : 0;
require DT_ROOT.'/module/'.$module.'/fans.class.php';
$do = new fans();
$menus = array (
    array('粉丝列表', '?moduleid='.$moduleid.'&file='.$file.'&gid='.$gid),
    array('待审核', '?moduleid='.$moduleid.'&file='.$file.'&gid='.$gid.'&action=check'),
    array('未通过', '?moduleid='.$moduleid.'&file='.$file.'&gid='.$gid.'&action=reject'),
    array('回收站', '?moduleid='.$moduleid.'&file='.$file.'&gid='.$gid.'&action=recycle'),
);
if(in_array($action, array('', 'check', 'reject', 'recycle'))) {
	$sfields = array('加入理由', '会员名', '昵称');
	$dfields = array('content', 'username', 'passport');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$sorder  = array('结果排序方式', '加入时间降序', '加入时间升序', '发言状态降序', '发言状态升序');
	$dorder  = array('itemid desc', 'addtime DESC', 'addtime ASC', 'ban DESC', 'ban ASC');
	isset($order) && isset($dorder[$order]) or $order = 0;
	$gid or $gid = '';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	(isset($username) && check_name($username)) or $username = '';
	$ban = isset($ban) ? intval($ban) : -1;

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order_select  = dselect($sorder, 'order', '', $order);

	$condition = '';
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($gid) $condition .= " AND gid='$gid'";
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	if($username) $condition .= " AND username='$username'";
	if($ban > -1) $condition .= " AND ban=$ban";
}
switch($action) {
	case 'delete':
		$itemid or msg('请选择粉丝');
		isset($recycle) ? $do->recycle($itemid) : $do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'cancel':
		$itemid or msg('请选择粉丝');
		$do->check($itemid, 2);
		dmsg('取消成功', $forward);
	break;
	case 'ban':
		$itemid or msg('请选择粉丝');
		$do->ban($itemid, 1);
		dmsg('禁言成功', $forward);
	break;
	case 'unban':
		$itemid or msg('请选择粉丝');
		$do->ban($itemid, 0);
		dmsg('取消成功', $forward);
	break;
	case 'restore':
		$itemid or msg('请选择粉丝');
		$do->restore($itemid);
		dmsg('还原成功', $forward);
	break;
	case 'clear':
		$do->clear();
		dmsg('清空成功', $forward);
	break;
	case 'recycle':
		$lists = $do->get_list('status=0'.$condition, $dorder[$order]);
		$menuid = 3;
		include tpl('fans', $module);
	break;
	case 'reject':
		if($itemid && !$psize) {
			$do->reject($itemid);
			dmsg('拒绝成功', $forward);
		} else {
			$lists = $do->get_list('status=1'.$condition, $dorder[$order]);
			$menuid = 2;
			include tpl('fans', $module);
		}
	break;
	case 'check':
		if($itemid) {
			$do->check($itemid);
			dmsg('审核成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 1;
			include tpl('fans', $module);
		}
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 0;
		include tpl('fans', $module);
	break;
}
?>