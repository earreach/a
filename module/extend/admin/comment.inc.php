<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/comment.class.php';
$do = new comment();
$menus = array (
    array('评论禁止', '?moduleid='.$moduleid.'&file='.$file.'&action=ban'),
    array('评论列表', '?moduleid='.$moduleid.'&file='.$file),
    array('评论审核', '?moduleid='.$moduleid.'&file='.$file.'&action=check'),
    array('回收站', '?moduleid='.$moduleid.'&file='.$file.'&action=recycle'),
    array('模块设置', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file=setting&action='.$file.'\', \'模块设置\');'),
);
$MOD['level'] = '精选|置顶';
if(in_array($action, array('', 'check', 'recycle'))) {
	$sfields = array('按条件', '内容', '回复', '引用', '标题', '作者', '会员', '昵称', '回复人', '编辑', 'IP');
	$dfields = array('content', 'content', 'reply', 'quotation', 'item_title', 'item_username', 'username', 'passport', 'replyer', 'editor', 'ip');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '回复时间降序', '回复时间升序', '引用次数降序', '引用次数升序', '支持次数降序', '支持次数升序', '反对次数降序', '反对次数升序', '举报次数降序', '举报次数升序', '评分高低降序', '评分高低升序');
	$dorder  = array('itemid desc', 'addtime DESC', 'addtime ASC', 'replytime DESC', 'replytime ASC', 'quotes DESC', 'quotes ASC', 'likes DESC', 'likes ASC', 'hates DESC', 'hates ASC', 'reports DESC', 'reports ASC', 'star DESC', 'star ASC');
	$sstar = $L['star_type'];

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($datetype) && in_array($datetype, array('addtime', 'replytime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	isset($star) && isset($sstar[$star]) or $star = 0;
	isset($ip) or $ip = '';
	$qid = isset($qid) ? intval($qid) : 0;
	$qid or $qid = '';
	$itemid or $itemid = '';
	$level = isset($level) ? intval($level) : 0;
	$hide = isset($hide) ? intval($hide) : 0;
	$guest = isset($guest) ? intval($guest) : 0;
	$reply = isset($reply) ? intval($reply) : 0;
	$rep = isset($rep) ? intval($rep) : 0;
	(isset($username) && check_name($username)) or $username = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$module_select = module_select('mid', '模块', $mid);
	$order_select  = dselect($sorder, 'order', '', $order);
	$level_select = level_select('level', '级别', $level, 'all');
	$star_select  = dselect($sstar, 'star', '', $star);

	$condition = '';
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($mid) $condition .= " AND item_mid='$mid'";
	if($itemid) $condition .= " AND item_id='$itemid'";
	if($qid) $condition .= " AND qid='$qid'";
	if($ip) $condition .= " AND ip='$ip'";
	if($star) $condition .= " AND star='$star'";
	if($level) $condition .= $level > 9 ? " AND level>0" : " AND level=$level";
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	if($username) $condition .= " AND username='$username'";
	if($hide) $condition .= " AND hidden>0";
	if($guest) $condition .= " AND username=''";
	if($reply == 1) $condition .= " AND reply<>''";
	if($reply == 2) $condition .= " AND reply<>'' AND `replyer`=`item_username`";
	if($reply == 3) $condition .= " AND reply<>'' AND `replyer`<>`item_username`";
}
switch($action) {
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		$item = $do->get_one();
		if($submit) {
			if($item['replyer'] == $item['item_username']) {
				//
			} else {
				if($post['reply']) $post['replyer'] = $_username;
			}
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($item);
			$menuid = $status == 2 ? 2 : 1;
			$adddate = timetodate($addtime);
			$replydate = timetodate($replytime);
			$url = gourl('?mid='.$item_mid.'&itemid='.$item_id);
			include tpl('comment_edit', $module);
		}
	break;
	case 'ban':
		if($job == 'delete') {
			$itemid or msg('请选择项目');
			$do->_delete($itemid);
			dmsg('删除成功', $forward);
		} else if($job == 'update') {
			$do->ban_update($post);
			dmsg('保存成功', '?moduleid='.$moduleid.'&file='.$file.'&action=ban&page='.$page);
		} else {
			$condition = 1;
			if($mid) $condition = "moduleid=$mid";
			$lists = $do->get_ban_list($condition);
			include tpl('comment_ban', $module);
		}
	break;
	case 'level':
		$itemid or msg('请选择评论');
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('级别设置成功', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择评论');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'restore':
		$itemid or msg('请选择评论');
		$do->restore($itemid);
		dmsg('还原成功', $forward);
	break;
	case 'clear':
		$do->clear();
		dmsg('清空成功', $forward);
	break;
	case 'recycle':
		if($itemid) {
			$do->recycle($itemid);
			dmsg('删除成功', $forward);
		} else {
			$lists = $do->get_list('status=0'.$condition, $dorder[$order]);
			$menuid = 3;
			include tpl('comment', $module);
		}
	break;
	case 'check':
		if($itemid) {
			$status = $status == 3 ? 3 : 2;
			$do->check($itemid, $status);
			dmsg($status == 3 ? '审核成功' : '取消成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 2;
			include tpl('comment', $module);
		}
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 1;
		include tpl('comment', $module);
	break;
}
?>