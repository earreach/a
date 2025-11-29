<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('问题列表', '?moduleid='.$moduleid.'&file='.$file),
    array('问题分类', 'javascript:Dwidget(\'?file=type&item='.$file.'\', \'问题分类\');'),
);
$table = $DT_PRE.'ask';
$TYPE = get_type('ask', 1);
$dstatus = $L['ask_status'];
$dstars = $L['ask_star_type'];
$stars = array_map("strip_tags", $dstars);
switch($action) {
	case 'show':
		$itemid or msg();
		$a = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		$a or msg();
		if($submit) {
			if($status > 1 && strlen($content) < 5) msg('请填写回复内容');
			$content = addslashes(save_remote(save_local(stripslashes($content))));
			if($content) {
				$post = array();
				$post['qid'] = $itemid;
				$post['content'] = $content;
				$post['username'] = $post['editor'] = $_username;
				$post['addtime'] = $post['edittime'] = $DT_TIME;
				$db->query("INSERT INTO {$table} ".arr2sql($post, 0));
				clear_upload($content, $itemid, 'ask');
			}
			$db->query("UPDATE {$table} SET status=$status,edittime=$DT_TIME WHERE itemid=$itemid");
			if($status > 1) {
				$msg = isset($msg) ? 1 : 0;
				$eml = isset($eml) ? 1 : 0;
				$sms = isset($sms) ? 1 : 0;
				$wec = isset($wec) ? 1 : 0;
				if($msg == 0) $sms = $wec = 0;
				if($msg || $eml || $sms || $wec) {
					$linkurl = $MOD['linkurl'].'ask'.DT_EXT.'?action=show&itemid='.$itemid;
					$subject = '您的[问题]'.dsubstr($a['title'], 30, '...').'(流水号:'.$a['itemid'].')已经回复';
					$content = '尊敬的会员：<br/>您的[问题]'.$a['title'].'(流水号:'.$a['itemid'].')已经回复！<br/>';
					$content .= '请点击下面的链接查看详情：<br/>';
					$content .= '<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/>';
					$user = userinfo($a['username']);
					if($msg) send_message($user['username'], $subject, $content);
					if($eml) send_mail($user['email'], $subject, $content);
					if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
					if($wec) send_weixin($user['username'], $subject);
				}
			}
			dmsg('受理成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid);
		} else {
			extract($a);
			if($status == 0) {
				$status = 1;
				$db->query("UPDATE {$table} SET status=1,edittime=$DT_TIME WHERE itemid=$itemid");
			}
			if($reply) {
				$post = array();
				$post['qid'] = $itemid;
				$post['content'] = $content;
				$post['username'] = $post['editor'] = $editor;
				$post['addtime'] = $post['edittime'] = $edittime;
				$db->query("INSERT INTO {$table} ".arr2sql($post, 0));
				$db->query("UPDATE {$table} SET reply='' WHERE itemid=$itemid");
				$reply = '';
			}
			$condition = "qid=$itemid";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
			$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);		
			$lists = array();
			$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY edittime ASC LIMIT {$offset},{$pagesize}");
			while($r = $db->fetch_array($result)) {
				$lists[] = $r;
			}
			$adddate = timetodate($addtime, 5);
			$editdate = timetodate($edittime, 5);
			include tpl('ask_show', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$a = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		$a or msg();
		if($submit) {
			$content = addslashes(save_remote(save_local(stripslashes($content))));
			clear_upload($content, $itemid, 'ask');
			$db->query("UPDATE {$table} SET content='$content' WHERE itemid=$itemid");
			dmsg('修改成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid);
		} else {
			$content = $a['content'];
			include tpl('ask_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids) OR qid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			$itemid = $r['itemid'];
			$userid = get_user($r['username']);
			if($r['content']) delete_local($r['content'], $userid);
			$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
		}
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '标题', '内容', '会员名', '回复', '受理人');
		$dfields = array('title', 'title', 'content', 'username', 'reply', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '提交时间降序', '提交时间升序', '更新时间降序', '更新时间升序', '会员评分降序', '会员评分升序', '受理状态降序', '受理状态升序');
		$dorder  = array('edittime DESC', 'itemid DESC', 'itemid ASC', 'edittime DESC', 'edittime ASC', 'star DESC', 'star ASC', 'status DESC', 'status ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;

		isset($typeid) or $typeid = 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : -1;
		$star = isset($star) && isset($dstars[$star]) ? intval($star) : -1;
		isset($datetype) && in_array($datetype, array('edittime', 'addtime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($username) && check_name($username)) or $username = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$type_select   = type_select($TYPE, 1, 'typeid', '请选择分类', $typeid);
		$status_select = dselect($dstatus, 'status', '受理状态', $status, '', 1, '', 1);
		$star_select = dselect($dstars, 'star', '评分', $star, '', 1, '', 1);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = 'qid=0';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > 0) $condition .= " AND typeid=$typeid";
		if($status > -1) $condition .= " AND status=$status";
		if($star > -1) $condition .= " AND star=$star";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($username) $condition .= " AND username='$username'";
		#echo $condition;

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['editdate'] = $r['edittime'] ? timetodate($r['edittime'], 5) : 'N/A';
			$r['dstatus'] = $dstatus[$r['status']];
			$r['type'] = $r['typeid'] && isset($TYPE[$r['typeid']]) ? set_style($TYPE[$r['typeid']]['typename'], $TYPE[$r['typeid']]['style']) : '默认';
			$lists[] = $r;
		}
		include tpl('ask', $module);
	break;
}
?>