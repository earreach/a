<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('登录日志', '?moduleid='.$moduleid.'&file='.$file),
    array('我的记录', '?moduleid='.$moduleid.'&file='.$file.'&action=my'),
);
switch($action) {
	case 'clear':
		$time = $DT_TODAY - 60*86400;
		$db->query("DELETE FROM {$DT_PRE}login WHERE logintime<$time");
		dmsg('清理成功', $forward);
	break;
	default:
		include DT_ROOT.'/include/client.func.php';
		$sfields = array('按条件', '结果', '会员', '密码', 'IP', '端口', '入口', '客户端');
		$dfields = array('message', 'message', 'username', 'password', 'loginip', 'loginport', 'type', 'agent');
		isset($admin) or $admin = -1;
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$ip = isset($ip) ? $ip : '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($action == 'my') {
			$username = $_username;
			$menuid = 1;
		} else {
			$menuid = 0;
		}
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND logintime>=$fromtime";
		if($totime) $condition .= " AND logintime<=$totime";
		if($ip) $condition .= " AND loginip='$ip'";
		if($username) $condition .= " AND username='$username'";
		if($admin > -1) $condition .= " AND admin='$admin'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {	
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}login WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}login WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			//$r['password'] = substr($r['password'], 0, 10).'************'.substr($r['password'], 20);
			$r['logintime'] = timetodate($r['logintime'], 6);
			$r['os'] = $r['agent'] ? get_os($r['agent']) : '';
			$r['bs'] = $r['agent'] ? get_bs($r['agent']) : '';
			if(!$r['type']) {
				$r['type'] = in_array($r['os'], array('ios', 'andriod')) ? 'mob' : 'pc';
				$db->query("UPDATE {$DT_PRE}login SET type='$r[type]' WHERE itemid=$r[itemid]");
			}
			$lists[] = $r;
		}
		include tpl('loginlog', $module);
	break;
}
?>