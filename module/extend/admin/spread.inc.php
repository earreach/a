<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/spread.class.php';
$do = new spread();
$menus = array (
    array('添加排名', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('排名列表', '?moduleid='.$moduleid.'&file='.$file),
    array('排名审核', '?moduleid='.$moduleid.'&file='.$file.'&action=check'),
    array('起价设置', '?moduleid='.$moduleid.'&file='.$file.'&action=price'),
    array('生成排名', '?moduleid='.$moduleid.'&file='.$file.'&action=html'),
    array('模块设置', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file=setting&action='.$file.'\', \'模块设置\');'),
);
if(in_array($action, array('', 'check'))) {
	$sfields = array('关键词', '会员名', '公司名');
	$dfields = array('word', 'username', 'company');
	$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '开始时间降序', '开始时间升序', '到期时间降序', '到期时间升序', '会员出价降序', '会员出价升序');
	$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC', 'price DESC', 'price ASC');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($datetype) && in_array($datetype, array('addtime', 'edittime', 'fromtime', 'totime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	(isset($username) && check_name($username)) or $username = '';
	$itemid or $itemid = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order_select  = dselect($sorder, 'order', '', $order);
	$condition = '';
	if($keyword) $condition .= in_array($dfields[$fields], array('tid', 'price')) ? " AND $dfields[$fields]='$kw'" : match_kw($dfields[$fields], $keyword);
	if($mid) $condition .= " AND mid=$mid";
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($itemid) $condition .= " AND tid=$itemid";
	if($username) $condition .= " AND username='$username'";
}
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&typeid='.$post['typeid']);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$status = 3;
			$mid = 5;
			$fromtime = timetodate($DT_TIME);
			$menuid = 0;
			$currency = $MOD['spread_currency'];
			include tpl('spread_edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$addtime = timetodate($addtime);
			$fromtime = $fromtime ? timetodate($fromtime) : '';
			$totime = $totime ? timetodate($totime) : '';
			$menuid = $status == 3 ? 1 : 2;
			include tpl('spread_edit', $module);
		}
	break;
	case 'html':
		$all = (isset($all) && $all) ? 1 : 0;
		$one = (isset($one) && $one) ? 1 : 0;
		if(!isset($num)) {
			$num = 50;			
			cache_clear_htm('m', 1);
		}
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(itemid) AS fid FROM {$DT_PRE}spread WHERE totime>$DT_TIME");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		isset($sid) or $sid = $fid;
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(itemid) AS tid FROM {$DT_PRE}spread WHERE totime>$DT_TIME");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		if($fid <= $tid) {
			$result = $db->query("SELECT itemid,mid FROM {$DT_PRE}spread WHERE totime>$DT_TIME AND itemid>=$fid ORDER BY itemid LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$itemid = $r['itemid'];
					$MOD = cache_read('module-'.$r['mid'].'.php');
					tohtml('spread', $module);
				}
				$itemid += 1;
			} else {
				$itemid = $fid + $num;
			}
		} else {
			if($all) dheader('?moduleid=3&file=ad&action=html&all=1&one='.$one);
			dmsg('生成成功', "?moduleid=$moduleid&file=$file");
		}
		msg('ID从'.$fid.'至'.($itemid-1).'[排名推广]生成成功'.progress($sid, $fid, $tid), "?moduleid=$moduleid&file=$file&action=$action&sid=$sid&fid=$itemid&tid=$tid&num=$num&all=$all&one=$one", 0);
	break;
	case 'price':
		if($job == 'delete') {
			$itemid or msg('请选择项目');
			$do->_delete($itemid);
			dmsg('删除成功', $forward);
		} else if($job == 'update') {
			$do->price_update($post);
			dmsg('保存成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&page='.$page);
		} else {
			$sfields = array('按条件', '关键词', '操作人');
			$dfields = array('word', 'word', 'editor');
			isset($fields) && isset($dfields[$fields]) or $fields = 0;
			$fields_select = dselect($sfields, 'fields', '', $fields);
			$sorder  = array('结果排序方式', '起价金额降序', '起价金额升序', '更新时间降序', '更新时间升序');
			$dorder  = array('edittime DESC', 'price DESC', 'price ASC', 'edittime DESC', 'edittime ASC');
			isset($order) && isset($dorder[$order]) or $order = 0;
			(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
			$fromtime = $fromdate ? datetotime($fromdate) : 0;
			(isset($todate) && is_time($todate)) or $todate = '';
			$totime = $todate ? datetotime($todate) : 0;
			$minprice = isset($minprice) ? dround($minprice) : '';
			$minprice or $minprice = '';
			$maxprice = isset($maxprice) ? dround($maxprice) : '';
			$maxprice or $maxprice = '';
			$empty = isset($empty) ? intval($empty) : 0;
			$order_select  = dselect($sorder, 'order', '', $order);
			$condition = 1;
			if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
			if($mid) $condition .= " AND mid=$mid";
			if($minprice)  $condition .= " AND price>=$minprice";
			if($maxprice)  $condition .= " AND price<=$maxprice";
			if($fromtime) $condition .= " AND edittime>=$fromtime";
			if($totime) $condition .= " AND edittime<=$totime";
			if($empty) $condition .= " AND word=''";
			$lists = $do->get_price_list($condition, $dorder[$order]);
			include tpl('spread_price', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择排名');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'level':
		$itemid or msg('请选择排名');
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('级别设置成功', $forward);
	break;
	case 'check':
		if($itemid) {
			$status = $status == 3 ? 3 : 2;
			$do->check($itemid, $status);
			dmsg($status == 3 ? '审核成功' : '取消成功', $forward);
		} else {
			$lists = $do->get_list('status=2'.$condition, $dorder[$order]);
			$menuid = 2;
			include tpl('spread', $module);
		}
	break;
	default:
		$lists = $do->get_list('status=3'.$condition, $dorder[$order]);
		$menuid = 1;
		include tpl('spread', $module);
	break;
}
?>