<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(!check_group($_groupid, $MOD['group_compare'])) {
	login();
	dalert(lang('message->without_permission'), 'goback');
}
if($action == 'remove') {
	$str = '';
	if($itemid > 0) {
		$ids = get_cookie('compare_'.$moduleid);
		if($ids) $str = substr(str_replace(','.$itemid.',', ',', ','.$ids.','), 1, -1);
	}
	set_cookie('compare_'.$moduleid, $str);
	dheader('?reload='.DT_TIME);
}

$arr = array();
if($itemid) {
	if(is_array($itemid)) {
		foreach($itemid as $id) {
			$id = intval($id);
			if($id > 0) $arr[] = $id;
		}
	} else {
		if($itemid > 0) $arr[] = $itemid;
	}
}
$ids = get_cookie('compare_'.$moduleid);
$tmp = $ids ? explode(',', $ids) : array();
if($tmp) {
	foreach($tmp as $id) {
		$id = intval($id);
		if($id > 0) $arr[] = $id;
	}
}
$itemids = array();
if($arr) {
	$arr = array_unique($arr);
	$j = 0;
	foreach($arr as $id) {
		if($j++ < 6) $itemids[] = $id;
	}
}
$tags = $lists = array();
if($itemids) {
	$str = implode(',', $itemids);
	if($str != $ids) set_cookie('compare_'.$moduleid, $str);
	$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($str) ORDER BY itemid DESC LIMIT 6");
	while($r = $db->fetch_array($result)) {
		if($r['status'] < 2) continue;
		$r['editdate'] = timetodate($r['edittime'], 3);
		$r['adddate'] = timetodate($r['addtime'], 3);
		$r['alt'] = $r['title'];
		$r['title'] = set_style($r['title'], $r['style']);
		$r['userurl'] = userurl($r['username']);
		$r['linkurl'] = $MOD['linkurl'].$r['linkurl'];
		$r['mobile'] = $MOD['mobile'].$r['linkurl'];
		$lists[$r['itemid']] = $r;
	}
	if($lists) {
		foreach($itemids as $id) {
			if(isset($lists[$id])) $tags[] = $lists[$id];
		}
	}
}
$head_title = $L['compare_title'].$DT['seo_delimiter'].$MOD['name'];
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	$forward = $MOD['mobile'];
	$head_name = $L['compare_title'];
	$foot = '';
}
include template($MOD['template_compare'] ? $MOD['template_compare'] : 'compare', $module);
?>