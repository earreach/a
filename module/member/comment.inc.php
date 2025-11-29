<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$table = $DT_PRE.'comment';
switch($action) {
	case 'delete':
		$itemid or message($L['comment_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($job == 'reply') {
				if($r['item_username'] == $_username && $r['replyer'] == $_username) $db->query("UPDATE {$table} SET reply='',replyer='',replytime=0 WHERE itemid=$itemid");
			} else {
				if($r['username'] == $_username || $r['item_username'] == $_username) $db->query("UPDATE {$table} SET status=0 WHERE itemid=$itemid");
			}
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'level':
		$itemid or message($L['comment_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		$level = isset($level) ? intval($level) : 0;
		$level = $job == 'cancel' ? 0 : ($level == 2 ? 2 : 1);
		foreach($itemids as $itemid) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($r['item_username'] == $_username) $db->query("UPDATE {$table} SET level=$level WHERE itemid=$itemid");
		}
		dmsg($L['op_set_success'], $forward);
	break;
	case 'reply':
		$itemid or exit('ko');
		$content = isset($content) ? dhtmlspecialchars(trim($content)) : '';
		$content = preg_replace("/&([a-z]{1,});/", '', $content);
		$len = word_count($content);
		if($len < $EXT['comment_min']) exit('min');
		if($len > $EXT['comment_max']) exit('max');
		$BANWORD = cache_read('banword.php');
		if($BANWORD) {
			$tmp = banword($BANWORD, $content, 2);
			if(is_array($tmp)) exit('bad');
			$content = $tmp;
		}
		if($DT['spam_appcode'] && cloud_spam($content, $DT['spam_appcode'], 2)) exit('bad');
		$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		if(!$r || $r['status'] != 3 || $r['reply'] || $r['item_username'] != $_username) exit('ko');
		$db->query("UPDATE {$table} SET reply='$content',replyer='$_username',replytime='$DT_TIME' WHERE itemid=$itemid");
		exit('ok');
	break;
	case 'my':
		$sfields = $L['comment_sfields_my'];
		$dfields = array('content','content','reply', 'item_title', 'item_username');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'replytime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$level = isset($level) ? intval($level) : 0;
		$hide = isset($hide) ? intval($hide) : 0;
		$star = isset($star) ? intval($star) : 0;
		$reply = isset($reply) ? intval($reply) : 0;
		$rep = isset($rep) ? intval($rep) : 0;
		(isset($username) && check_name($username)) or $username = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "status=3 AND username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($hide) $condition .= " AND hidden>0";
		if($star) $condition .= " AND star=$star";
		if($reply == 1) $condition .= " AND reply<>''";
		if($reply == 2) $condition .= " AND reply<>'' AND `replyer`=`item_username`";
		if($reply == 3) $condition .= " AND reply<>'' AND `replyer`<>`item_username`";
		if($level) $condition .= " AND level>0";
		if($mid) $condition .= " AND item_mid=$mid";
		if($itemid) $condition .= " AND item_id=$itemid";
		if($username) $condition .= " AND item_username='$username'";
		if($fields == 4 || $fields == 5) $condition .= " AND hidden=0";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = $users = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['replydate'] = $r['replytime'] ? timetodate($r['replytime'], 5) : '';
			if(strpos($r['content'], ')') !== false) $r['content'] = parse_face($r['content']);
			if(strpos($r['quotation'], ')') !== false) $r['quotation'] = parse_face($r['quotation']);
			$r['linkurl'] = gourl('?mid='.$r['item_mid'].'&itemid='.$r['item_id']);
			$r['linkurl'] = ($DT_PC ? $EXT['comment_url'] : $EXT['comment_mob']).rewrite('index'.DT_EXT.'?mid='.$r['item_mid'].'&itemid='.$r['item_id']);
			$lists[] = $r;
		}
		$head_title = $L['comment_title_my'];
	break;
	default:
		$sfields = $L['comment_sfields'];
		$dfields = array('content','content','reply', 'item_title', 'username', 'passport');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'replytime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$level = isset($level) ? intval($level) : 0;
		$hide = isset($hide) ? intval($hide) : 0;
		$guest = isset($guest) ? intval($guest) : 0;
		$star = isset($star) ? intval($star) : 0;
		$reply = isset($reply) ? intval($reply) : 0;
		$rep = isset($rep) ? intval($rep) : 0;
		(isset($username) && check_name($username)) or $username = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "status=3 AND item_username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($hide) $condition .= " AND hidden>0";
		if($guest) $condition .= " AND username=''";
		if($star) $condition .= " AND star=$star";
		if($reply == 1) $condition .= " AND reply<>''";
		if($reply == 2) $condition .= " AND reply<>'' AND replyer='$_username'";
		if($reply == 3) $condition .= " AND reply<>'' AND replyer<>'$_username'";
		if($level) $condition .= " AND level>0";
		if($mid) $condition .= " AND item_mid=$mid";
		if($itemid) $condition .= " AND item_id=$itemid";
		if($username) $condition .= " AND username='$username' AND hidden=0";
		if($fields == 4 || $fields == 5) $condition .= " AND hidden=0";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = $users = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['replydate'] = $r['replytime'] ? timetodate($r['replytime'], 5) : '';
			if(strpos($r['content'], ')') !== false) $r['content'] = parse_face($r['content']);
			if(strpos($r['quotation'], ')') !== false) $r['quotation'] = parse_face($r['quotation']);
			$r['linkurl'] = ($DT_PC ? $EXT['comment_url'] : $EXT['comment_mob']).rewrite('index'.DT_EXT.'?mid='.$r['item_mid'].'&itemid='.$r['item_id']);
			$lists[] = $r;
		}
		$head_title = $L['comment_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('comment', $module);
?>