<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('浏览历史', '?moduleid='.$moduleid.'&file='.$file),
);
$table = $DT_PRE.'history';
switch($action) {
	case 'clear':
		$time = $DT_TODAY - 30*86400;
		$db->query("DELETE FROM {$table} WHERE lasttime<$time");
		dmsg('清理成功', '?moduleid='.$moduleid.'&file='.$file);
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '标题', '会员名', '作者');
		$dfields = array('title', 'title', 'username', 'author');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$itemid or $itemid = '';
		isset($username) or $username = '';
		isset($author) or $author = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$module_select = module_select('mid', '模块', $mid);
		$condition = "1";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($mid) $condition .= " AND mid=$mid";
		if($fromtime) $condition .= " AND lasttime>=$fromtime";
		if($totime) $condition .= " AND lasttime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($author) $condition .= " AND author='$author'";
		if($itemid) $condition .= " AND tid=$itemid";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = $tags = $views = $ids = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY lasttime DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['itemid']);
			$lists[] = $r;
		}
		include tpl('history', $module);
	break;
}
?>