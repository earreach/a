<?php
defined('IN_DESTOON') or exit('Access Denied');
if($job == 'win11') {
	$uk = get_cookie('uk');
	is_numeric($uk) or $uk = '';
	if($uk) {
		$t =$db->get_one("SELECT * FROM {$DT_PRE}stats_uv WHERE ip='$DT_IP' AND uk='$uk' ORDER BY itemid DESC");
		if($t && $DT_TIME - $t['addtime'] < 1800 && $t['os'] == 'Windows 10') $db->query("UPDATE {$DT_PRE}stats_uv SET os='Windows 11' WHERE itemid=$t[itemid]");
	}
	exit;
}
@header("Content-type:text/javascript");	
if($DT['stats']) {
	$DT_URL = $DT_REF;
	$DT_REF = (isset($refer) && is_url($refer)) ? $refer : '';
	include DT_ROOT.'/api/stats.inc.php';
}
?>