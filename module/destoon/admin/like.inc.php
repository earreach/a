<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('表态记录', '?file='.$file),
);
switch($action) {
	case 'clear':
		$time = $DT_TODAY - 60*86400;
		$db->query("DELETE FROM {$DT_PRE}like_record WHERE addtime<$time");
		dmsg('清理成功', $forward);
	break;
	default:
		$menuid = 0;
		if($action == 'hate') {
			$hate = 1;
			$menuid = 1;
		} else if($action == 'like') {
			$hate = 0;
		}
		(isset($hate) && in_array($hate, array(-1, 0, 1))) or $hate = -1;
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$tid = isset($tid) ? intval($tid) : 0;
		$rid = isset($rid) ? intval($rid) : 0;
		$tid or $tid = '';
		$rid or $rid = '';
		$module_select = module_select('mid', '模块', $mid, '', '1,2');
		$condition = '1';
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($hate > -1) $condition .= " AND hate='$hate'";
		if($mid) $condition .= " AND mid='$mid'";
		if($tid) $condition .= " AND tid='$tid'";
		if($rid) $condition .= " AND rid='$rid'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {	
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}like_record WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}like_record WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['url'] = '';
			if($r['mid'] && $r['tid']) $r['url'] = gourl('?mid='.$r['mid'].'&itemid='.$r['tid']);
			$lists[] = $r;
		}
		$rname = '回复/评论ID';
		if($mid) {
			if($mid == 3) {
				$rname = '评论ID';
			} elseif($MODULE[$mid]['module'] == 'know') {			
				$rname = '答案ID';
			} elseif($MODULE[$mid]['module'] == 'club') {			
				$rname = '回复ID';
			}
		}
		include tpl('like');
	break;
}
?>