<?php
defined('DT_ADMIN') or exit('Access Denied');
$qid = isset($qid) ? intval($qid) : 0;
require DT_ROOT.'/module/'.$module.'/answer.class.php';
$do = new answer($moduleid);
$menus = array (
    array('答案列表', '?moduleid='.$moduleid.'&file='.$file.'&qid='.$qid),
    array('待审核', '?moduleid='.$moduleid.'&file='.$file.'&qid='.$qid.'&action=check'),
    array('已拒绝', '?moduleid='.$moduleid.'&file='.$file.'&qid='.$qid.'&action=reject'),
    array('投票记录', '?moduleid='.$moduleid.'&file='.$file.'&qid='.$qid.'&action=vote'),
);
$this_forward = '?moduleid='.$moduleid.'&file='.$file;
if(in_array($action, array('', 'check', 'reject'))) {
	$sfields = array('内容', '会员名', '昵称', 'IP', '问题ID', '答案ID', '参考资料');
	$dfields = array('content', 'username', 'passport', 'ip', 'qid', 'itemid', 'linkurl');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '投票次数降序', '投票次数升序', '支持次数降序', '支持次数升序', '反对次数降序', '反对次数升序', '举报次数降序', '举报次数升序');
	$dorder  = array('itemid desc', 'addtime DESC', 'addtime ASC', 'vote DESC', 'vote ASC', 'likes DESC', 'likes ASC', 'hates DESC', 'hates ASC', 'reports DESC', 'reports ASC');

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($ip) or $ip = '';
	$guest = isset($guest) ? 1 : 0;
	$expert = isset($expert) ? 1 : 0;
	$hidden = isset($hidden) ? 1 : 0;
	$qid or $qid = '';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	(isset($username) && check_name($username)) or $username = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order_select  = dselect($sorder, 'order', '', $order);

	$condition = '';
	if($keyword) $condition .= in_array($dfields[$fields], array('qid', 'itemid', 'ip')) ? " AND $dfields[$fields]='$kw'" : match_kw($dfields[$fields], $keyword);
	if($qid) $condition .= " AND qid='$qid'";
	if($ip) $condition .= " AND ip='$ip'";
	if($guest) $condition .= " AND username=''";
	if($expert) $condition .= " AND expert>0";
	if($hidden) $condition .= " AND hidden>0";
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	if($username) $condition .= " AND username='$username'";
}
switch($action) {
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			$content = stripslashes(trim($post['content']));
			if(!$content) msg('请填写答案');
			$content = save_local($content);
			if($MOD['clear_alink']) $content = clear_link($content);
			if($MOD['save_remotepic']) $content = save_remote($content);
			$content = dsafe($content);
			$post['content'] = addslashes($content);
			clear_upload($content, $itemid, $table_answer);
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
			include tpl('answer_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择答案');
		$do->delete($itemid);
		dmsg('删除成功', $this_forward);
	break;
	case 'vote':
		(isset($username) && check_name($username)) or $username = '';
		isset($ip) or $ip = '';
		$aid = isset($aid) ? intval($aid) : 0;
		$qid or $qid = '';
		$aid or $aid = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$condition = '1';
		if($keyword) $condition .= in_array($dfields[$fields], array('qid', 'itemid', 'ip')) ? " AND $dfields[$fields]='$kw'" : match_kw($dfields[$fields], $keyword);
		if($qid) $condition .= " AND qid='$qid'";
		if($aid) $condition .= " AND aid='$aid'";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($ip) $condition .= " AND ip='$ip'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {	
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_vote} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$table_vote} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		include tpl('answer_vote', $module);
	break;
	case 'reject':
		if($itemid && !$psize) {
			$do->reject($itemid);
			dmsg('拒绝成功', $forward);
		} else {
			$lists = $do->get_list('status=1'.$condition, $dorder[$order]);
			$menuid = 2;
			include tpl('answer', $module);
		}
	break;
	case 'check':
		if($itemid && !$psize) {
			$status = $status == 3 ? 3 : 2;
			$do->check($itemid, $status);
			dmsg($status == 3 ? '审核成功' : '取消成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 1;
			include tpl('answer', $module);
		}
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 0;
		include tpl('answer', $module);
	break;
}
?>