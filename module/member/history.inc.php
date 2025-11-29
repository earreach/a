<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$table = $DT_PRE.'history';
switch($action) {
	case 'delete':		
		$itemid or message();
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids) AND username='$_username'");
		dmsg($L['op_del_success'], $forward);
	break;
	case 'visit':
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($username) && check_name($username)) or $username = '';
		$condition = "author='$_username'";
		if($mid) $condition .= " AND mid=$mid";
		if($keyword) $condition .= match_kw('title', $keyword);
		if($fromtime) $condition .= " AND lasttime>=$fromtime";
		if($totime) $condition .= " AND lasttime<=$totime";
		if($username) $condition .= " AND username='$username'";
		$lists = array();
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		if($items) {
			$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY lasttime DESC LIMIT {$offset},{$pagesize}");
			while($r = $db->fetch_array($result)) {
				$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['tid']);
				$lists[] = $r;
			}
		}
		$head_title = $L['history_title_visit'];
	break;
	default:
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($author) && check_name($author)) or $author = '';
		$condition = "username='$_username'";
		if($mid) $condition .= " AND mid=$mid";
		if($keyword) $condition .= match_kw('title', $keyword);
		if($fromtime) $condition .= " AND lasttime>=$fromtime";
		if($totime) $condition .= " AND lasttime<=$totime";
		if($author) $condition .= " AND author='$author'";
		$lists = array();
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		if($items) {
			$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY lasttime DESC LIMIT {$offset},{$pagesize}");
			while($r = $db->fetch_array($result)) {
				$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['tid']);
				$lists[] = $r;
			}
		}
		$head_title = $L['history_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('history', $module);
?>