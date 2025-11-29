<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/stock.class.php';
$do = new stock();
$menus = array (
    array('添加商品', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('商品列表', '?moduleid='.$moduleid.'&file='.$file),
    array('库存记录', '?moduleid='.$moduleid.'&file='.$file.'&action=record'),
    array('商品入库', '?moduleid='.$moduleid.'&file='.$file.'&action=update&type=0'),
    array('商品出库', '?moduleid='.$moduleid.'&file='.$file.'&action=update&type=1'),
    array('公用数据', '?moduleid='.$moduleid.'&file='.$file.'&action=open'),
);

switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$amount = $post['amount'];
				$post['amount'] = 0;
				$do->add($post);
				if($post['username']) stock_update($do->itemid, $post['skuid'], $post['username'], $amount, $_cname ? $_cname : $_username, '商品添加', '');
				dmsg('添加成功', '?moduleid='.$moduleid.'&file='.$file);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$content = '';
			$username = '';
			$status = 3;
			$addtime = timetodate($DT_TIME);
			$menuid = 0;
			include tpl('stock_edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if($submit) {
			$amount = $post['amount'];
			if($post['username']) $post['amount'] = $r['amount'];
			if($do->pass($post)) {
				$do->edit($post);
				if($r['amount'] != $amount && $post['username']) stock_update($do->itemid, $post['skuid'], $post['username'], $amount - $r['amount'], $_cname ? $_cname : $_username, '商品修改', '');
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($r);
			$addtime = timetodate($addtime);
			$menuid = $username ? 1 : 5;
			include tpl('stock_edit', $module);
		}
	break;
	case 'clear':
		$time = $DT_TODAY - 365*86400;
		$db->query("DELETE FROM {$DT_PRE}stock_record WHERE addtime<$time");
		dmsg('清理成功', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择商品');
		isset($recycle) ? $do->recycle($itemid) : $do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'level':
		$itemid or msg('请选择商品');
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('级别设置成功', $forward);
	break;
	case 'update':
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		$type = isset($type) && $type ? 1 : 0;
		$title = '';
		if($itemid) {
			$do->itemid = $itemid;
			$r = $do->get_one();
			if(!$r) msg();
			$skuid = $r['skuid'];
			$title = $r['title'];
			$username = $r['username'];
		} else if($skuid) {
			(isset($username) && check_name($username)) or $username = '';
			$username or msg('会员名不能为空');
			$r = $do->get_one("skuid='$skuid' AND username='$username'");
			if(!$r) msg('条形编码不存在对应商品');
			$title = $r['title'];
		}
		if($submit) {
			$amount = intval($amount);
			if($amount < 1) msg('请填写数量');
			if(!is_skuid($skuid)) msg('请填写条形编码');
			$forward = $itemid ? '?moduleid='.$moduleid.'&file='.$file.'&action=record' : '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&type='.$type.'&amount='.$amount.'&reason='.urlencode($reason).'&note='.urlencode($note);
			$reason = dhtmlspecialchars(trim($reason));
			$note = dhtmlspecialchars(trim($note));
			$itemid = $r['itemid'];
			$username = $r['username'];
			$editor = $_cname ? $_cname : $_username;
			if($type) $amount = -$amount;
			stock_update($itemid, $skuid, $username, $amount, $editor, $reason, $note);
			dmsg($L['op_success'], $forward);
		} else {
			$reason = isset($reason) ? strip_tags($reason) : '';
			$note = isset($note) ? strip_tags($note) : '';
			$amount = isset($amount) ? intval($amount) : 1;
			isset($username) or $username = $_username;
			$menuid = $type ? 4 : 3;
			include tpl('stock_update', $module);
		}
	break;
	case 'record':
		$sfields = array('按条件', '商品名称', '条形编码', '操作事由', '备注信息', '会员名', '操作人');
		$dfields = array('title', 'title', 'skuid', 'reason', 'note', 'username', 'editor');
		$sorder  = array('排序方式', '数量降序', '数量升序', '库存降序', '库存升序', '时间降序', '时间升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'balance DESC', 'balance ASC', 'addtime DESC', 'addtime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($type) or $type = 0;
		$itemid or $itemid = '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($order) && isset($dorder[$order]) or $order = 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);

		$condition = "1";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0";
		if($itemid) $condition .= " AND stockid=$itemid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($editor) $condition .= " AND editor='$editor'";
		if($username) $condition .= " AND username='$username'";

		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stock_record WHERE {$condition}");
		$items = $r['num'];
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}stock_record WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$menuid = 2;
		include tpl('stock_record', $module);
	break;
	case 'open':
		$sfields = array('按条件', '商品名称', '条形编码', '仓储货位', '商品品牌', '计量单位', '属性名1', '属性名2', '属性名3', '属性值1', '属性值2', '属性值3', '备注', '会员名', '操作人');
		$dfields = array('title', 'title', 'skuid', 'location', 'brand', 'unit', 'n1', 'n2', 'n3', 'v1', 'v2', 'v3', 'note', 'username', 'editor');
		$sorder  = array('排序方式', '复制次数降序', '复制次数升序', '商品售价降序', '商品售价升序', '商品进价降序', '商品进价升序', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'price DESC', 'price ASC', 'cost DESC', 'cost ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$level = isset($level) ? intval($level) : 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($order) && isset($dorder[$order]) or $order = 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$level_select = level_select('level', '级别', $level);

		$condition = "username=''";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($editor) $condition .= " AND editor='$editor'";
		if($username) $condition .= " AND username='$username'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";

		$lists = $do->get_list($condition, $dorder[$order]);
		$menuid = 5;
		include tpl('stock_open', $module);
	break;
	default:
		$sfields = array('按条件', '商品名称', '条形编码', '仓储货位', '商品品牌', '计量单位', '属性名1', '属性名2', '属性名3', '属性值1', '属性值2', '属性值3', '备注', '会员名', '操作人');
		$dfields = array('title', 'title', 'skuid', 'location', 'brand', 'unit', 'n1', 'n2', 'n3', 'v1', 'v2', 'v3', 'note', 'username', 'editor');
		$sorder  = array('排序方式', '库存数量降序', '库存数量升序', '商品售价降序', '商品售价升序', '商品进价降序', '商品进价升序', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序');
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'price DESC', 'price ASC', 'cost DESC', 'cost ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$level = isset($level) ? intval($level) : 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($order) && isset($dorder[$order]) or $order = 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$level_select = level_select('level', '级别', $level);

		$condition = "username<>''";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($editor) $condition .= " AND editor='$editor'";
		if($username) $condition .= " AND username='$username'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";

		$lists = $do->get_list($condition, $dorder[$order]);
		$menuid = 1;
		include tpl('stock', $module);
	break;
}
?>