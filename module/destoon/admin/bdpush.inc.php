<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
include DT_ROOT.'/include/module.func.php';
$menus = array (
    array('手动推送', '?file='.$file.'&action=add'),
    array('推送记录', '?file='.$file),
);
switch($action) {
	case 'add':
		$DT['baidu_push'] or msg('请先设置准入密钥', '?file=setting&tab=1');
		if($submit) {
			if(!$content) msg('请输入网址列表');
			foreach(explode("\n", $content) as $url) {
				$url = trim($url);
				if(is_url($url)) {
					$t = $db->get_one("SELECT * FROM {$DT_PRE}baidu_push WHERE url='$url'");
					if($t) {
						if($t['error']) {
							$db->query("DELETE FROM {$DT_PRE}baidu_push WHERE itemid=$t[itemid]");
						} else {
							continue;
						}
					}
					baidu_push($url, $DT['baidu_push']);
				}
			}
			dmsg('提交成功', $forward);
		} else {
			include tpl('bdpush_edit');
		}
	break;
	case 'clear':
		$time = $DT_TODAY - 30*86400;
		$db->query("DELETE FROM {$DT_PRE}baidu_push WHERE addtime<$time");
		dmsg('清理成功', '?file='.$file);
	break;
	case 'push':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$t = $db->get_one("SELECT * FROM {$DT_PRE}baidu_push WHERE itemid=$itemid");
			if($t) {
				if($t['error']) {
					$db->query("DELETE FROM {$DT_PRE}baidu_push WHERE itemid=$itemid");
					baidu_push($t['url'], $DT['baidu_push']);
				}
			}
		}
		dmsg('提交成功', '?file='.$file);
	break;
	default:
		$sfields = array('按条件', 'URL', '错误');
		$dfields = array('url', 'url', 'error');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$status = isset($status) ? intval($status) : 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `addtime`>=$fromtime";
		if($totime) $condition .= " AND `addtime`<=$totime";
		if($status == 1) $condition .= " AND error=''";
		if($status == 2) $condition .= " AND error<>''";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}baidu_push WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}baidu_push WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		include tpl('bdpush');
	break;
}
?>