<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('一键登录', '?moduleid='.$moduleid.'&file='.$file),
    array('登录记录', '?moduleid='.$moduleid.'&file='.$file.'&action=login'),
    array('接口设置', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file=setting&tab=7\', \'接口设置\');'),
);
$OAUTH = cache_read('oauth.php');
function oauth_area($r) {
	$area = '';
	if($r['city']) $area = '<a href="javascript:;" onclick="Dq(\'fields\',6,0);Dq(\'kw\',\''.$r['city'].'\');">'.$r['city'].'</a>';
	if($r['province'] && strpos($area, $r['province']) === false) $area = '<a href="javascript:;" onclick="Dq(\'fields\',7,0);Dq(\'kw\',\''.$r['province'].'\');">'.$r['province'].'</a>'.($area ? ' '.$area : '');
	if($r['country'] == '中国' || strtoupper($r['country']) == 'CHINA') $r['country'] = '';
	if($r['country'] && strpos($area, $r['country']) === false) $area = '<a href="javascript:;" onclick="Dq(\'fields\',8,0);Dq(\'kw\',\''.$r['country'].'\');">'.$r['country'].'</a>'.($area ? ' '.$area : '');
	return $area ? $area : '未知';
}
$sgender = array('未知', '先生' , '女士');
switch($action) {
	case 'clear':
		$time = $DT_TODAY - 60*86400;
		$db->query("DELETE FROM {$DT_PRE}oauth_login WHERE logintime<$time");
		dmsg('清理成功', $forward);
	break;
	case 'unbind':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth WHERE itemid IN ($itemids)");
		while($user = $db->fetch_array($result)) {
			if($user['site'] == 'weixin' && $user['openid']) $db->query("UPDATE {$DT_PRE}weixin_user SET username='' WHERE openid='$user[openid]'");
		}
		$db->query("UPDATE {$DT_PRE}oauth SET username='' WHERE itemid IN ($itemids)");
		dmsg('解除成功', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth WHERE itemid IN ($itemids)");
		while($user = $db->fetch_array($result)) {
			if($user['site'] == 'weixin' && $user['openid']) $db->query("DELETE FROM {$DT_PRE}weixin_user WHERE openid='$user[openid]'");
		}
		$db->query("DELETE FROM {$DT_PRE}oauth WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'edit':
		$itemid or msg('请选择记录');
		$user = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE itemid=$itemid");
		$user or msg('记录不存在');
		if($submit) {
			if(check_name($name)) {
				$name != $user['username'] or msg('会员名'.$name.'未修改');
				$u = userinfo($name);
				$u or msg('会员'.$name.'不存在');
				$u['groupid'] > 4 or msg('会员'.$name.'所在组不可修改');
				$db->query("UPDATE {$DT_PRE}oauth SET username='' WHERE username='$name' AND site='$user[site]'");
			}
			$db->query("UPDATE {$DT_PRE}oauth SET username='$name' WHERE itemid=$itemid");
			if($user['site'] == 'weixin' && $user['openid']) $db->query("UPDATE {$DT_PRE}weixin_user SET username='$name' WHERE openid='$user[openid]'");
			dmsg('修改成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid);
		} else {
			include tpl('oauth_edit', $module);
		}		
	break;
	case 'login':
		include DT_ROOT.'/include/client.func.php';
		$sfields = array('按条件', '会员', '平台', 'IP', '端口', '客户端');
		$dfields = array('username', 'username', 'site', 'loginip', 'loginport', 'agent');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$ip = isset($ip) ? $ip : '';
		$site = isset($site) ? $site : '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND logintime>=$fromtime";
		if($totime) $condition .= " AND logintime<=$totime";
		if($site) $condition .= " AND site='$site'";
		if($username && $ip) {
			$condition .= " AND (username='$username' OR loginip='$ip')";
		} else {
			if($ip) $condition .= " AND loginip='$ip'";
			if($username) $condition .= " AND username='$username'";
		}
		if($page > 1 && $sum) {
			$items = $sum;
		} else {	
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}oauth_login WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth_login WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['logintime'] = timetodate($r['logintime'], 6);
			$lists[] = $r;
		}
		include tpl('oauth_login', $module);
	break;
	default:
		$sfields = array('按条件', '会员名', '昵称', '平台', '头像', '网址', '城市', '省份', '国家', 'OpenID', 'UnionID', 'IP');
		$dfields = array('username', 'username', 'nickname', 'site', 'avatar', 'url', 'city', 'province', 'country', 'openid', 'unionid', 'loginip');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '绑定时间降序', '绑定时间升序', '登录时间降序', '登录时间升序', '登录次数降序', '登录次数升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'logintime DESC', 'logintime ASC', 'logintimes DESC', 'logintimes ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$gender = isset($gender) ? intval($gender) : -1;

		isset($site) or $site = '';
		$thumb = isset($thumb) ? intval($thumb) : 0;
		$link = isset($link) ? intval($link) : 0;
		isset($datetype) && in_array($datetype, array('addtime', 'logintime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($username) && check_name($username)) or $username = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$gender_select = dselect($sgender, 'gender', '性别', $gender, '', 1, '-1');

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($username) $condition .= " AND username='$username'";	
		if($site) $condition .= " AND site='$site'";
		if($thumb) $condition .= " AND avatar<>''";
		if($link) $condition .= " AND url<>''";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($gender > -1) $condition .= " AND gender=$gender";

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}oauth WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['logindate'] = timetodate($r['logintime'], 5);
			$r['sgender'] = isset($sgender[$r['gender']]) ? $sgender[$r['gender']] : $sgender[0];
			$r['avatar'] or $r['avatar'] ='api/oauth/avatar.png';
			$r['from'] = oauth_area($r);
			$lists[] = $r;
		}
		include tpl('oauth', $module);
	break;
}
?>