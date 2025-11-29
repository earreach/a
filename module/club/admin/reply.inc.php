<?php
defined('DT_ADMIN') or exit('Access Denied');
$tid = isset($tid) ? intval($tid) : 0;
$gid = isset($gid) ? intval($gid) : 0;
$rid = isset($rid) ? intval($rid) : 0;
require DT_ROOT.'/module/'.$module.'/reply.class.php';
$do = new reply($moduleid);
$menus = array (
    array('回复列表', '?moduleid='.$moduleid.'&file='.$file.'&tid='.$tid),
    array('待审核', '?moduleid='.$moduleid.'&file='.$file.'&tid='.$tid.'&action=check'),
    array('未通过', '?moduleid='.$moduleid.'&file='.$file.'&tid='.$tid.'&action=reject'),
    array('回收站', '?moduleid='.$moduleid.'&file='.$file.'&tid='.$tid.'&action=recycle'),
);
if(in_array($action, array('', 'check', 'reject', 'recycle'))) {
	$sfields = array('内容', '会员名', '昵称', '编辑', 'IP');
	$dfields = array('content', 'username', 'passport', 'editor', 'ip');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '回复次数降序', '回复次数升序', '支持次数降序', '支持次数升序', '反对次数降序', '反对次数升序', '举报次数降序', '举报次数升序');
	$dorder  = array('itemid desc', 'addtime DESC', 'addtime ASC', 'reply DESC', 'reply ASC', 'likes DESC', 'likes ASC', 'hates DESC', 'hates ASC', 'reports DESC', 'reports ASC');

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($ip) or $ip = '';
	$guest = isset($guest) ? 1 : 0;
	$level = isset($level) ? intval($level) : 0;
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$tid or $tid = '';
	$gid or $gid = '';
	$rid or $rid = '';
	(isset($username) && check_name($username)) or $username = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$level_select = level_select('level', '级别', $level);
	$order_select  = dselect($sorder, 'order', '', $order);

	$condition = '';
	if($keyword) $condition .= in_array($dfields[$fields], array('gid', 'itemid', 'ip')) ? " AND $dfields[$fields]='$kw'" : match_kw($dfields[$fields], $keyword);
	if($tid) $condition .= " AND tid='$tid'";
	if($gid) $condition .= " AND gid='$gid'";
	if($rid) $condition .= " AND rid='$rid'";
	if($ip) $condition .= " AND ip='$ip'";
	if($guest) $condition .= " AND username=''";
	if($level) $condition .= " AND level=$level";
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	if($username) $condition .= " AND username='$username'";
}
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
			$history = history($moduleid, $file.'-'.$itemid);
			$addtime = timetodate($addtime);
			include tpl('reply_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择回复');		
		isset($recycle) ? $do->recycle($itemid) : $do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'recycle':
		$lists = $do->get_list('status=0'.$condition, $dorder[$order]);
		$menuid = 3;
		include tpl('reply', $module);
	break;
	case 'reject':
		if($itemid && !$psize) {
			$do->reject($itemid);
			dmsg('拒绝成功', $forward);
		} else {
			$lists = $do->get_list('status=1'.$condition, $dorder[$order]);
			$menuid = 2;
			include tpl('reply', $module);
		}
	break;
	case 'check':
		if($itemid) {
			$do->check($itemid, 3);
			dmsg('审核成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 1;
			include tpl('reply', $module);
		}
	break;
	case 'level':
		$itemid or msg('请选择回复');
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('级别设置成功', $forward);
	break;
	case 'cancel':
		$itemid or msg('请选择回复');
		$do->check($itemid, 2);
		dmsg('取消成功', $forward);
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 0;
		include tpl('reply', $module);
	break;
}
?>