<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('流量统计', '?file='.$file),
    array('UV记录', '?file='.$file.'&action=uv'),
    array('PV记录', '?file='.$file.'&action=pv'),
    array('统计报表', '?file='.$file.'&action=report'),
    array('在线会员', '?file='.$file.'&action=online'),
    array('404日志', '?file='.$file.'&action=404'),
);
switch($action) {
	case 'clear_pv':
		$time = $DT_TODAY - 30*86400;
		$db->query("DELETE FROM {$DT_PRE}stats_pv WHERE addtime<$time");
		dmsg('清理成功', '?file='.$file.'&action=pv');
	break;
	case 'clear_uv':
		$time = $DT_TODAY - 365*86400;
		$db->query("DELETE FROM {$DT_PRE}stats_uv WHERE addtime<$time");
		dmsg('清理成功', '?file='.$file.'&action=uv');
	break;
	case 'clear':
		$time = $DT_TODAY - 30*86400;
		$db->query("DELETE FROM {$DT_PRE}404 WHERE addtime<$time");
		dmsg('清理成功', '?file='.$file.'&action=404');
	break;
	case '404':
		$sfields = array('按条件', '网址', '来源', '搜索引擎', '会员', 'IP', '端口', '客户端', '操作系统', '浏览器');
		$dfields = array('url', 'url', 'refer', 'robot', 'username', 'ip', 'port', 'ua', 'os', 'bs');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$ip = isset($ip) ? $ip : '';
		$os = isset($os) ? $os : '';
		$bs = isset($bs) ? $bs : '';
		$pc = isset($pc) ? intval($pc) : -1;
		$robot = isset($robot) ? $robot : '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($ip) $condition .= " AND ip='$ip'";
		if($os) $condition .= " AND os='$os'";
		if($bs) $condition .= " AND bs='$bs'";
		if($robot) $condition .= $robot == 'all' ? " AND robot<>''" : " AND robot='$robot'";
		if($pc > -1) $condition .= " AND pc=$pc";
		if($username) $condition .= " AND username='$username'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}404 WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}404 WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		include tpl('stats_404');
	break;
	case 'online':
		$sfields = array('按条件', '会员名', '会员ID');
		$dfields = array('username', 'username', 'userid');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '访问时间降序', '访问时间升序', '会员ID降序', '会员ID升序');
		$dorder  = array('lasttime DESC', 'lasttime DESC', 'lasttime ASC', 'userid DESC', 'userid ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$online = isset($online) ? intval($online) : 2;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= " AND $dfields[$fields]='$kw'";
		if($mid) $condition .= " AND moduleid=$mid";
		if($online < 2) $condition .= " AND online=$online";
		$lastime = $DT_TIME - $DT['online'];
		$db->query("DELETE FROM {$DT_PRE}online WHERE lasttime<$lastime");
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}online WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}online WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['lasttime'] = timetodate($r['lasttime'], 'H:i:s');
			$lists[] = $r;
		}
		include tpl('stats_online');
	break;
	case 'update':
		require DT_ROOT.'/include/client.func.php';
		$table = $DT_PRE.'stats_uv';
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(itemid) AS fid FROM {$table}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(itemid) AS tid FROM {$table}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		isset($num) or $num = 1000;
		$itemid or $itemid = 1;
		if($fid <= $tid) {
			$result = $db->query("SELECT * FROM {$table} WHERE itemid>=$fid ORDER BY itemid LIMIT 0,$num ");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$itemid = $r['itemid'];
					$sql = '';

					$os = get_os($r['ua']);
					if($os != $r['os']) $sql .= "os='".$os."',";

					$bs = get_bs($r['ua']);
					if($bs != $r['bs']) $sql .= "bs='".$bs."',";

					$bd = get_bd($r['ua']);
					if($bd != $r['bd']) $sql .= "bd='".$bd."',";

					$robot = get_robot($r['ua']);
					if($robot != $r['robot']) $sql .= "robot='".$robot."',";

					if($sql) {
						$sql = substr($sql, 0, -1);
						$db->query("UPDATE {$table} SET {$sql} WHERE itemid=$itemid");
					}
				}
				$itemid += 1;
			} else {
				$itemid = $fid + $num;
			}
		} else {
			msg("更新成功", '?file='.$file.'&action=uv');
		}
		msg('ID从'.$fid.'至'.($itemid-1).'转换成功', "?file=$file&action=$action&fid=$itemid&tid=$tid&num=$num", 0);
	break;
	case 'report':
		$job or $job = 'pvs';
		include tpl('stats_report');
	break;
	case 'pv':
		$sfields = array('按条件', '网址', '来源', '来源域名', '搜索引擎', '会员', '所属商家', 'IP', '识别号', '端口');
		$dfields = array('url', 'url', 'refer', 'domain', 'robot', 'username', 'homepage', 'ip', 'uk', 'port');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$dorder  = array('sid DESC', 'addtime DESC', 'addtime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($robot) or $robot = '';
		isset($url) or $url = '';
		isset($refer) or $refer = '';
		isset($domain) or $domain = '';
		$pc = isset($pc) ? intval($pc) : -1;
		$islink = isset($islink) ? intval($islink) : -1;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$catid or $catid = '';
		$itemid or $itemid = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$module_select = module_select('mid', '模块', $mid, '', '');

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($mid) $condition .= " AND mid=$mid";
		if($catid) $condition .= ($CAT['child']) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($url) $condition .= " AND url='$url'";
		if($refer) $condition .= " AND refer='$refer'";
		if($domain) $condition .= " AND domain='$domain'";
		if($robot) $condition .= $robot == 'all' ? " AND robot<>''" : " AND robot='$robot'";
		if($pc > -1) $condition .= " AND pc=$pc";
		if($islink > -1) $condition .= $islink ? " AND domain<>''" : " AND domain=''";
		foreach($dfields as $v) {
			if(in_array($v, array('url', 'robot'))) continue;
			isset($$v) or $$v = '';
			if($$v) $condition .= " AND $v='".$$v."'";
		}

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stats_pv WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}stats_pv WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			if($r['refer'] && substr($r['refer'], 0, 4) != 'http') $r['refer'] = DT_PATH.($r['refer'] == '/' ? '' : $r['refer']);
			if(substr($r['url'], 0, 4) != 'http') $r['url'] = DT_PATH.$r['url'];
			$lists[] = $r;
		}
		include tpl('stats_pv');
	break;
	case 'uv':
		require DT_ROOT.'/include/client.func.php';
		$sfields = array('按条件', '搜索引擎', 'IP', '识别号', '端口', '地区', '国家', '省份', '城市', '网络', '客户端', '操作系统', '设备品牌', '浏览器', '分辨率');
		$dfields = array('ua', 'robot', 'ip', 'uk', 'port', 'area', 'country', 'province', 'city', 'network', 'ua', 'os', 'bd', 'bs', 'screen');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$pc = isset($pc) ? intval($pc) : -1;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($robot) or $robot = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($pc > -1) $condition .= " AND pc=$pc";
		if($robot) $condition .= $robot == 'all' ? " AND robot<>''" : " AND robot='$robot'";
		foreach($dfields as $v) {
			if(in_array($v, array('robot'))) continue;
			isset($$v) or $$v = '';
			if($$v) $condition .= " AND $v='".$$v."'";
		}

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stats_uv WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}stats_uv WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$sql = '';
			if(!$r['screen']) {
				$r['screen'] = $dc->get('sn-'.md5($r['ip'].$DT_TODAY.strip_sql($r['ua'], 0)).$r['uk']);
				if($r['screen']) $sql .= "screen='".$r['screen']."',";
			}
			$os = get_os($r['ua']);
			if($os != $r['os']) {
				$r['os'] = $os;
				$sql .= "os='".$os."',";
			}
			$bs = get_bs($r['ua']);
			if($bs != $r['bs']) {
				$r['bs'] = $bs;
				$sql .= "bs='".$bs."',";
			}
			$bd = get_bd($r['ua']);
			if($bd != $r['bd']) {
				$r['bd'] = $bd;
				$sql .= "bd='".$bd."',";
			}
			$robot = is_robot($r['ua']) ? get_robot($r['ua']) : '';
			if($robot != $r['robot']) {
				$r['robot'] = $robot;
				$sql .= "robot='".$robot."',";
			}
			if($sql) $db->query("UPDATE {$DT_PRE}stats_uv SET ".substr($sql, 0, -1)." WHERE itemid=$r[itemid]");
			$r['location'] = $r['province'] ? '<a href="javascript:;" onclick="Dq(\'province\',this.innerHTML);">'.$r['province'].'</a>'.($r['city'] == $r['province'] ? '' : ' <a href="javascript:;" onclick="Dq(\'city\',this.innerHTML);">'.$r['city'].'</a>') : '<a href="javascript:;" onclick="Dq(\'country\',\''.$r['country'].'\');">'.$r['country'].'</a> <a href="javascript:;" onclick="Dq(\'city\',this.innerHTML);">'.$r['city'].'</a>';
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		include tpl('stats_uv');
	break;
	default:
		$W = array('天', '一', '二', '三', '四', '五', '六');
		$sorder  = array('排序方式', '总UV降序', '总UV升序', '电脑UV降序', '电脑UV升序', '手机Uv降序', '手机UV升序', '总IP降序', '总IP升序', '电脑IP降序', '电脑IP升序', '手机IP降序', '手机IP升序', '总PV降序', '总PV升序', '电脑PV降序', '电脑PV升序', '手机PV降序', '手机PV升序', '爬虫PV降序', '爬虫PV升序', '电脑爬虫PV降序', '电脑爬虫PV升序', '手机爬虫PV降序', '手机爬虫PV升序', '日期降序', '日期升序');
		$dorder  = array('id DESC', 'uv DESC', 'uv ASC', 'uv_pc DESC', 'uv_pc ASC', 'uv_mb DESC', 'uv_mb ASC', 'ip DESC', 'ip ASC', 'ip_pc DESC', 'ip_pc ASC', 'ip_mb DESC', 'ip_mb ASC', 'pv DESC', 'pv ASC', 'pv_pc DESC', 'pv_pc ASC', 'pv_mb DESC', 'pv_mb ASC', 'rb DESC', 'rb ASC', 'rb_pc DESC', 'rb_pc ASC', 'rb_mb DESC', 'rb_mb ASC', 'id DESC', 'id ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;

		isset($fromdate) or $fromdate = '';
		$fromtime = is_date($fromdate) ? str_replace('-', '', $fromdate) : '';
		isset($todate) or $todate = '';
		$totime = is_date($todate) ? str_replace('-', '', $todate) : '';
		(isset($username) && check_name($username)) or $username = '';

		$order_select = dselect($sorder, 'order', '', $order);

		if($username) {
			$condition = "username='$username'";
			$table = $DT_PRE.'stats_user';
		} else {
			$condition = '1';
			$table = $DT_PRE.'stats';
		}
		if($fromtime) $condition .= " AND id>=$fromtime";
		if($totime) $condition .= " AND id<=$totime";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$d0 = timetodate(DT_TIME, 3);
		$d1 = timetodate(DT_TIME - 86400, 3);
		$d2 = timetodate(DT_TIME - 86400*2, 3);
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['time'] = datetotime($r['id']);
			$r['date'] = timetodate($r['time'], 3);
			$r['week'] = '星期'.$W[date('w', $r['time'])];
			if($r['date'] == $d0) {
				$r['week'] = '今日';
			} elseif($r['date'] == $d1) {
				$r['week'] = '昨日';
			} elseif($r['date'] == $d2) {
				$r['week'] = '前日';
			}
			$lists[] = $r;
		}
		include tpl('stats');
	break;
}
?>