<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$DT['stats'] or message($L['feature_close']);
($MG['biz'] && $MG['homepage'] && $MG['stats_view']) or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('my.lang');
$menu_id = 2;
switch($action) {
	case 'update':
		$tb = $DT_PRE.'stats_pv';
		isset($fid) or $fid = '';
		isset($tid) or $tid = '';
		if(!is_date($fid)) {
			$r = $db->get_one("SELECT max(id) AS fid FROM {$DT_PRE}stats_user WHERE username='$_username'");
			if($r) {
				$fid = substr($r['fid'], 0, 4).'-'.substr($r['fid'], 4, 2).'-'.substr($r['fid'], 6, 2);
			} else {
				$r = $db->get_one("SELECT min(addtime) AS fid FROM {$tb} WHERE homepage='$_username'");
				if($r) $fid = timetodate($r['fid'], 3);
			}
			is_date($fid) or $fid = timetodate(DT_TIME - 86400*30, 3);
		}
		if(!is_date($tid)) {
			$tid = timetodate(DT_TIME - 86400, 3);
		}
		if($fid >= $tid) message($L['op_update_success'], '?action=index');
		$id = str_replace('-', '', $tid);
		$t = $db->get_one("SELECT itemid FROM {$DT_PRE}stats_user WHERE id='$id' AND username='$_username'");
		if(!$t) {
			$ftime = datetotime($tid.' 00:00:00');
			$ttime = datetotime($tid.' 23:59:59');
			$ip = $ip_pc = $ip_mb = $pv = $pv_pc = $pv_mb = $rb = $rb_pc = $rb_mb = 0;
			$pv = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime");
			if($pv) {
				$pv_pc = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime AND pc=1");
				$pv_mb = $pv - $pv_pc;
				$rb = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime AND robot<>''");
				if($rb) {
					$rb_pc = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime AND robot<>'' AND pc=1");
					$rb_mb = $rb - $rb_pc;
				}
				$ip = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime", 0, 'DISTINCT `ip`');
				$ip_pc = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime AND pc=1", 0, 'DISTINCT `ip`');
				$ip_mb = $db->count($tb, "homepage='$_username' AND addtime>=$ftime AND addtime<=$ttime AND pc=0", 0, 'DISTINCT `ip`');
			}
			$db->query("INSERT INTO {$DT_PRE}stats_user (username,id,ip,ip_pc,ip_mb,pv,pv_pc,pv_mb,rb,rb_pc,rb_mb) VALUES ('$_username','$id','$ip','$ip_pc','$ip_mb','$pv','$pv_pc','$pv_mb','$rb','$rb_pc','$rb_mb')");
		}
		$tid = timetodate((datetotime($tid) - 86400), 3);
		message($id.$L['op_update_success'], "?action=$action&fid=$fid&tid=$tid");
	break;
	case 'report':
		$job or $job = 'pv';
		if(in_array($job, array('pv', 'ip', 'rb'))) {
			(isset($todate) && is_time($todate)) or $todate = '';
			$totime = is_date($todate) ? datetotime($todate) : DT_TIME;
			if($totime > DT_TIME) $totime = DT_TIME;
			$fromtime = timetodate($totime - 86400*30, 'Ymd');
			$totime = timetodate($totime, 'Ymd');
		}
		if($job == 'pv') {
			$data = $pv = $pv_pc = $pv_mb = '';
			$result = $db->query("SELECT * FROM {$DT_PRE}stats_user WHERE username='$_username' AND id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
			while($r = $db->fetch_array($result)) {
				$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
				$pv .= $r['pv'].',';
				$pv_pc .= $r['pv_pc'].',';
				$pv_mb .= $r['pv_mb'].',';
			}
			if($data) {
				$data = substr($data, 0, -1);
				$pv = substr($pv, 0, -1);
				$pv_pc = substr($pv_pc, 0, -1);
				$pv_mb = substr($pv_mb, 0, -1);
			}
		} else if($job == 'ip') {
			$data = $ip = $ip_pc = $ip_mb = '';
			$result = $db->query("SELECT * FROM {$DT_PRE}stats_user WHERE username='$_username' AND id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
			while($r = $db->fetch_array($result)) {
				$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
				$ip .= $r['ip'].',';
				$ip_pc .= $r['ip_pc'].',';
				$ip_mb .= $r['ip_mb'].',';
			}
			if($data) {
				$data = substr($data, 0, -1);
				$ip = substr($ip, 0, -1);
				$ip_pc = substr($ip_pc, 0, -1);
				$ip_mb = substr($ip_mb, 0, -1);
			}
		} else if($job == 'rb') {
			$data = $rb = $rb_pc = $rb_mb = '';
			$result = $db->query("SELECT * FROM {$DT_PRE}stats_user WHERE username='$_username' AND id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
			while($r = $db->fetch_array($result)) {
				$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
				$rb .= $r['rb'].',';
				$rb_pc .= $r['rb_pc'].',';
				$rb_mb .= $r['rb_mb'].',';
			}
			if($data) {
				$data = substr($data, 0, -1);
				$rb = substr($rb, 0, -1);
				$rb_pc = substr($rb_pc, 0, -1);
				$rb_mb = substr($rb_mb, 0, -1);
			}
		} else if(in_array($job, array('domain', 'username', 'itemid'))) {
			(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
			$fromtime = $fromdate ? datetotime($fromdate) : 0;
			(isset($todate) && is_time($todate)) or $todate = '';
			$totime = $todate ? datetotime($todate) : 0;
			$module_select = '';
			if(in_array($job, array('catid', 'itemid'))) {
				if($mid < 5) {
					foreach($MODULE as $v) {
						if($v['islink'] || $v['moduleid'] < 5) continue;
						$mid = $v['moduleid'];
						break;
					}
				}
				$module_select = module_select('mid', $L['module_name'], $mid, '', '1,2,3,4');
			}
			$condition = "homepage='$_username'";
			if($fromtime) $condition .= " AND addtime>=$fromtime";
			if($totime) $condition .= " AND addtime<=$totime";
			if($mid > 4) $condition .= " AND mid=$mid";
			$key = $job;
			$xd = $yd = '';
			$lists = array();
			$result = $db->query("SELECT COUNT(`{$key}`) AS num,`{$key}` FROM {$DT_PRE}stats_pv WHERE {$condition} GROUP BY `{$key}` ORDER BY num DESC LIMIT 0,50", 'CACHE');
			while($r = $db->fetch_array($result)) {
				if(!$r[$key]) continue;
				$r['url'] = '?action=record&'.$job.'='.urlencode($r[$key]);
				if($job == 'username') {
					$r['url'] = gourl('?username='.$r[$key]);
				} else if($job == 'itemid') {
					$itemid = $r[$key];
					$t = $db->get_one("SELECT title FROM ".get_table($mid)." WHERE itemid=$itemid");
					if($t) {
						$r[$key] = dsubstr($t['title'], 30, '...');
						$r['url'] = gourl('?mid='.$mid.'&itemid='.$itemid);
					}
				}
				$lists[] = $r;
			}
			$max = 0;
			if($lists) {
				$max = count($lists);
				for($i = $max - 1; $i >= 0; $i--) {			
					$xd .= "'".$lists[$i][$key]."'".($i ? "," : "");
					$yd .= "{value:".$lists[$i]['num'].",name:'".$lists[$i][$key]."',url:'".$lists[$i]['url']."'}".($i ? "," : "");
				}
			}
			$height = $max ? ($max*32)+100 : 600;
		}
		$head_title = $L['stats_title_report'];
	break;
	case 'record':
		$sfields = $L['stats_record_sfields'];
		$dfields = array('url', 'url', 'refer', 'domain', 'robot', 'username', 'homepage', 'ip');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($robot) or $robot = '';
		$pc = isset($pc) ? intval($pc) : -1;
		$islink = isset($islink) ? intval($islink) : -1;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$catid or $catid = '';
		$itemid or $itemid = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$module_select = module_select('mid', $L['module_name'], $mid);
		$condition = "homepage='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($mid) $condition .= " AND mid=$mid";
		if($catid) $condition .= ($CAT['child']) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		if($itemid) $condition .= " AND itemid=$itemid";
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
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}stats_pv WHERE {$condition} ORDER BY sid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			if(strpos($r['refer'], 'file=') !== false || strpos($r['refer'], 'moduleid=') !== false) $r['refer'] = '';//Hide Admin
			if($r['refer'] && strpos($r['refer'], '://') === false) $r['refer'] = DT_PATH.($r['refer'] == '/' ? '' : $r['refer']);
			if(strpos($r['url'], '://') === false) $r['url'] = DT_PATH.$r['url'];
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		$head_title = $L['stats_title_record'];
	break;
	default:
		$id = timetodate(DT_TIME - 86400, 'Ymd');
		$t = $db->get_one("SELECT itemid FROM {$DT_PRE}stats_user WHERE id='$id' AND username='$_username'");
		$t or message($L['stats_msg_update'], '?action=update');
		$sorder  = $L['stats_sorder'];
		$dorder  = array('id DESC', 'ip DESC', 'ip ASC', 'ip_pc DESC', 'ip_pc ASC', 'ip_mob DESC', 'ip_mob ASC', 'pv DESC', 'pv ASC', 'pv_pc DESC', 'pv_pc ASC', 'pv_mob DESC', 'pv_mob ASC', 'rb DESC', 'rb ASC', 'rb_pc DESC', 'rb_pc ASC', 'rb_mob DESC', 'rb_mob ASC', 'id DESC', 'id ASC');
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = is_date($fromdate) ? str_replace('-', '', $fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = is_date($todate) ? str_replace('-', '', $todate) : 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = "username='$_username'";
		if($fromtime) $condition .= " AND id>=$fromtime";
		if($totime) $condition .= " AND id<=$totime";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stats_user WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}stats_user WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['time'] = datetotime($r['id']);
			$r['date'] = timetodate($r['time'], 3);
			$r['week'] = $L['stats_week'].$L['stats_weeks'][date('w', $r['time'])];
			$lists[] = $r;
		}
		$head_title = $L['stats_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	$head_name = $head_title;
}
include template('stats', $module);
?>