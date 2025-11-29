<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('粉丝关注', '?moduleid='.$moduleid.'&file=follow'),
);
$table = $DT_PRE.'follow';
switch($action) {
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '会员名', '昵称', '关注会员名', '关注昵称');
		$dfields = array('username', 'username', 'passport', 'fusername', 'fpassport');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '关注时间降序', '关注时间升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($fusername) && check_name($fusername)) or $fusername = '';
		(isset($username) && check_name($username)) or $username = '';
		$status = isset($status) ? intval($status) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($fusername) $condition .= " AND fusername='$fusername'";
		if($status) $condition .= " AND status=$status";

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
			$lists[] = $r;
		}
		include tpl('follow', $module);
	break;
}
?>