<?php
defined('DT_ADMIN') or exit('Access Denied');
$TYPE = get_type('style', 1);
require DT_ROOT.'/module/'.$module.'/style.class.php';
$do = new style();
$menus = array (
    array('安装模板', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('模板列表', '?moduleid='.$moduleid.'&file='.$file),
    array('订单列表', '?moduleid='.$moduleid.'&file='.$file.'&action=order'),
    array('模板分类', 'javascript:Dwidget(\'?file=type&item='.$file.'\', \'模板分类\');'),
);

switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$currency = 'money';
			$typeid = 0;
			$addtime = timetodate($DT_TIME);
			$menuid = 0;
			include tpl('style_edit', $module);
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
			$groupid = substr($groupid, 1, -1);
			$addtime = timetodate($addtime);
			$menuid = 1;
			include tpl('style_edit', $module);
		}
	break;
	case 'show':
		$itemid or msg();
		$u = $db->get_one("SELECT c.username FROM {$DT_PRE}company c,{$DT_PRE}member m WHERE c.userid=m.userid AND c.vip>0 AND m.edittime>0 ORDER BY m.logintimes DESC");
		if($u) dheader(DT_PATH.'index'.DT_EXT.'?homepage='.$u['username'].'&preview='.$itemid);
		msg('暂无符合条件的会员');
	break;
	case 'update':
		$do->order($listorder);
		dmsg('更新成功', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择模板');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'del':
		$itemid or msg('请选择订单');
		$do->del($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'order':
		$sfields = array('按条件', '模板名称', '风格目录', '会员');
		$dfields = array('title', 'title', 'skin', 'username');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '模板价格降序', '模板价格升序', '购买时长降序', '购买时长升序', '订单总额降序', '订单总额升序', '购买时间降序', '购买时间升序', '到期时间降序', '到期时间升序');
		$dorder  = array('itemid DESC', 'fee DESC', 'fee ASC', 'number DESC', 'number ASC', 'amount DESC', 'amount ASC', 'addtime DESC', 'addtime ASC', 'totime DESC', 'totime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
	
		isset($currency) && in_array($currency, array('money', 'credit')) or $currency = '';
		isset($mtype) && in_array($mtype, array('fee', 'number', 'amount')) or $mtype = 'fee';
		isset($minfee) or $minfee = '';
		isset($maxfee) or $maxfee = '';
		isset($datetype) && in_array($datetype, array('addtime', 'totime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$itemid or $itemid = '';
		(isset($username) && check_name($username)) or $username = '';
	
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
	
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($currency) $condition .= " AND currency='$currency'";
		if($minfee) $condition .= " AND $mtype>=$minfee";
		if($maxfee) $condition .= " AND $mtype<=$maxfee";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($itemid) $condition .= " AND styleid=$itemid";
		if($username) $condition .= " AND username='$username'";

		$lists = $do->get_order($condition, $dorder[$order]);
		include tpl('style_order', $module);
	break;
	default:
		$sfields = array('按条件', '模板名称', '风格目录', '模板目录', '作者', '编辑');
		$dfields = array('title', 'title', 'skin', 'template', 'author', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '模板价格降序', '模板价格升序', $DT['money_name'].'收益降序', $DT['money_name'].'收益升序', $DT['credit_name'].'收益降序', $DT['credit_name'].'收益升序', '使用人数降序', '使用人数升序', '订单数量降序', '订单数量升序', '安装时间降序', '安装时间升序', '修改时间降序', '修改时间升序');
		$dorder  = array('listorder DESC,addtime DESC', 'fee DESC', 'fee ASC', 'money DESC', 'money ASC', 'credit DESC', 'credit ASC', 'hits DESC', 'hits ASC', 'orders DESC', 'orders ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
	
		$groupid = isset($groupid) ? intval($groupid) : 0;
		$typeid = isset($typeid) ? intval($typeid) : 0;
		isset($currency) && in_array($currency, array('free', 'money', 'credit')) or $currency = '';
		isset($mtype) && in_array($mtype, array('fee', 'money', 'credit', 'hits', 'orders')) or $mtype = 'fee';
		isset($minfee) or $minfee = '';
		isset($maxfee) or $maxfee = '';
	
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$type_select = type_select($TYPE, 1, 'typeid', '请选择分类', $typeid);
	
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($groupid) $condition .= " AND groupid LIKE '%,$groupid,%'";
		if($typeid) $condition .= " AND typeid=$typeid";
		if($currency) $condition .= $currency == 'free' ? " AND fee=0" : " AND currency='$currency' AND fee>0";
		if($minfee) $condition .= " AND $mtype>=$minfee";
		if($maxfee) $condition .= " AND $mtype<=$maxfee";
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('style', $module);
	break;
}
?>