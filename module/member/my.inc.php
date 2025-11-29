<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$spread = ($MG['biz'] && (($MG['spread'] && $EXT['spread_enable']) || ($MG['ad'] && $EXT['ad_enable']))) ? 1 : 0;
if($action == 'spread') {
	$spread or dheader('?action=index');
	$sid = isset($sid) ? intval($sid) : 0;
	if($sid == 4) {
		//
	} else {
		in_array($sid, $MYMODS) or $sid = 0;
	}
	if($sid) {
		$pid = $rid = 0;
		if($MG['ad'] && $EXT['ad_enable']) {
			$t = $db->get_one("SELECT pid FROM {$DT_PRE}ad_place WHERE moduleid=$sid AND typeid=6 AND open=1");
			if($t) $pid = $t['pid'];
		}
		if($MG['spread'] && $EXT['spread_enable']) {
			$rid = 1;
		}
	} else {
		//
	}
	$head_title = $L['info_spread'];
} else {
	$head_title = $action == 'add' ? $L['info_add'] : $L['info_manage'];
}
if($DT_PC) {
	$menu_id = 1;
} else {
	$head_name = $head_title;
	$foot = 'my';
}
include template('my', $module);
?>