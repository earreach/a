<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('代理分销', '?moduleid='.$moduleid.'&file=agent'),
);
$table = $DT_PRE.'agent';
switch($action) {
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '公司', '代理', '手机', '折扣', '理由', '备注');
		$dfields = array('company', 'company', 'pcompany', 'mobile', 'discount', 'reason', 'note');
		$sorder  = array('结果排序方式', '加入时间降序', '加入时间升序', '享受折扣降序', '享受折扣升序', '订单数量降序', '订单数量升序', '分销订单降序', '分销订单升序', '总销售额降序', '总销售额升序', '年销售额降序', '年销售额升序', '月销售额降序', '月销售额升序', '代理状态降序', '代理状态升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'discount DESC', 'discount ASC', 'orders DESC', 'orders ASC', 'trades DESC', 'trades ASC', 'amount DESC', 'amount ASC', 'amounty DESC', 'amounty ASC', 'amountm DESC', 'amountm ASC', 'status DESC', 'status ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($pusername) && check_name($pusername)) or $pusername = '';
		(isset($username) && check_name($username)) or $username = '';
		$status = isset($status) ? intval($status) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($pusername) $condition .= " AND pusername='$pusername'";
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
			$r['adddate'] = timetodate($r['addtime'], 3);
			$lists[] = $r;
		}
		include tpl('agent', $module);
	break;
}
?>