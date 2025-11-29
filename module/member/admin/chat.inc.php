<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('在线交谈', '?moduleid='.$moduleid.'&file=chat'),
);
$table = $DT_PRE.'chat';
switch($action) {
	case 'clear':
		$time = $DT_TODAY - 365*86400;
		$db->query("DELETE FROM {$table} WHERE lasttime<$time");
		for($i = 0; $i < 10; $i++) {
			$db->query("DELETE FROM {$table}_data_{$i} WHERE addtime<$time");
		}
		dmsg('清理成功', $forward);
	break;
	case 'del':
		if(is_array($chatid)) {
			foreach($chatid as $cid) {
				if(is_md5($cid)) $db->query("DELETE FROM {$table} WHERE chatid='$cid'");
			}
		} else {
			if(is_md5($chatid)) $db->query("DELETE FROM {$table} WHERE chatid='$chatid'");
		}
		dmsg('删除成功', $forward);
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		is_md5($chatid) or msg('未指定聊天ID');
		$table = get_chat_tb($chatid);
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'view':
		$lists = array();
		if(is_md5($chatid)) {
			$table = get_chat_tb($chatid);
			$sfields = array('按条件', '内容', '会员', '昵称');
			$dfields = array('content', 'content', 'username', 'nickname');
			isset($fields) && isset($dfields[$fields]) or $fields = 0;
			$sorder  = array('结果排序方式', '发言时间降序', '发言时间升序');
			$dorder  = array('addtime DESC', 'addtime DESC', 'addtime ASC');
			isset($order) && isset($dorder[$order]) or $order = 0;
			(isset($username) && check_name($username)) or $username = '';
			(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
			$fromtime = $fromdate ? datetotime($fromdate) : 0;
			(isset($todate) && is_time($todate)) or $todate = '';
			$totime = $todate ? datetotime($todate) : 0;
			$fields_select = dselect($sfields, 'fields', '', $fields);
			$order_select  = dselect($sorder, 'order', '', $order);
			$condition = "chatid='$chatid'";
			if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
			if($fromtime) $condition .= " AND addtime>=$fromtime";
			if($totime) $condition .= " AND addtime<=$totime";
			if($username) $condition .= " AND username='$username'";
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
				$word = $r['content'];
				if($MOD['chat_url'] || $MOD['chat_img']) {
					if(preg_match_all("/([http|https]+)\:\/\/([a-z0-9\/\-\_\.\,\?\&\#\=\%\+\;]{4,})/i", $word, $m)) {
						foreach($m[0] as $u) {
							if($MOD['chat_img'] && preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
								$word = str_replace($u, '<img src="'.$u.'" onload="if(this.width>320)this.width=320;" onclick="window.open(this.src);"/>', $word);
							} else if($MOD['chat_img'] && preg_match("/^(mp4)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
								$word = str_replace($u, '<video src="'.$u.'" width="200" height="150" controls="controls"></video>', $word);
							} else if($MOD['chat_url']) {
								$word = str_replace($u, '<a href="'.$u.'" target="_blank">'.$u.'</a>', $word);
							}
						}
					}
				}
				if(strpos($word, ')') !== false) $word = parse_face($word);
				if(strpos($word, '[emoji]') !== false) $word = emoji_decode($word);
				$r['word'] = $word;
				$r['date'] = timetodate($r['addtime'], 6);
				$lists[] = $r;
			}
		}
		include tpl('chat_view', $module);
	break;
	default:
		$sfields = array('按条件', '发起人', '接收人', '来源', '聊天ID');
		$dfields = array('fromuser', 'fromuser', 'touser', 'forward', 'chatid');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '开始时间降序', '开始时间升序');
		$dorder  = array('freadtime DESC', 'freadtime DESC', 'freadtime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('freadtime', 'fgettime', 'treadtime', 'tgettime')) or $datetype = 'freadtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($fromuser) && check_name($fromuser)) or $fromuser = '';
		(isset($touser) && check_name($touser)) or $touser = '';
		(isset($username) && check_name($username)) or $username = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($fromuser) $condition .= " AND fromuser='$fromuser'";
		if($touser) $condition .= " AND touser='$touser'";
		if($username) $condition .= " AND (fromuser='$username' OR touser='$username')";

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
			if($r['forward'] && strpos($r['forward'], '://') === false) $r['forward'] = 'http://'.$r['forward'];
			$lists[] = $r;
		}
		include tpl('chat', $module);
	break;
}
?>