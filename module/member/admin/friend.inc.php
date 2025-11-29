<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('好友列表', '?moduleid='.$moduleid.'&file='.$file),
    array('黑名单', '?moduleid='.$moduleid.'&file='.$file.'&action=list'),
);
require DT_ROOT.'/module/'.$module.'/friend.class.php';
$do = new friend();
switch($action) {
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
			include tpl('friend_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择好友');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'remove':
		$table = $DT_PRE.'member_blacklist';
		$itemid or msg('请选择会员');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'list':
		$table = $DT_PRE.'member_blacklist';
		$sfields = array('按条件', '会员名', '拉黑会员名', '拉黑会员昵称', '拉黑原因');
		$dfields = array('username', 'username', 'busername', 'bpassport', 'note');
		$sorder  = array('结果排序方式', '拉黑时间降序', '拉黑时间升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($busername) && check_name($busername)) or $busername = '';
		(isset($username) && check_name($username)) or $username = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($busername) $condition .= " AND busername='$busername'";

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
		include tpl('friend_list', $module);
	break;
	default:
		$sfields = array('按条件', '会员', '昵称', '别名', '姓名', '公司', '职位', '电话', '手机', '主页', '邮箱', 'QQ', '微信', '归属会员', '备注');
		$dfields = array('fusername', 'fusername', 'fpassport', 'alias', 'truename', 'company', 'career', 'telephone', 'mobile', 'homepage', 'email', 'qq', 'wx', 'username', 'note');
		$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC');

		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$userid = isset($userid) ? intval($userid) : '';
		$userid or $userid = '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fusername) && check_name($fusername)) or $fusername = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($userid) $condition .= " AND userid=$userid";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($fusername) $condition .= " AND fusername='$fusername'";
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('friend', $module);
	break;
}
?>