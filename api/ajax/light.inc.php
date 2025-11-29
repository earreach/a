<?php
defined('IN_DESTOON') or exit('Access Denied');
$_userid or exit;
isset($tps) or $tps = '';
if(strpos($tps, ',') === false) exit;
isset($ids) or $ids = '';
if(strpos($ids, ',') === false) exit;
$tmp = explode(',', $ids);
$mid = intval($tmp[0]);
$tid = intval($tmp[1]);
$rid = isset($tmp[2]) ? intval($tmp[2]) : 0;
$uid = isset($uid) ? intval($uid) : 0;
if(!isset($MODULE[$mid])) exit;
if($tid < 1) exit;
$str = '';
if(strpos($tps, 'favorite') !== false) {
	$t = $db->get_one("SELECT itemid FROM {$DT_PRE}favorite WHERE userid=$_userid AND mid=$mid AND tid=$tid", 'CACHE');
	if($t) $str .= 'favorite,';
}
if(strpos($tps, 'like') !== false || strpos($tps, 'hate') !== false) {
	$kid = $_userid.'-'.$mid.'-'.$tid.'-'.$rid;
	$t = $db->get_one("SELECT itemid,hate FROM {$DT_PRE}like_record WHERE kid='$kid'", 'CACHE');
	if($t) $str .= $t['hate'] ? 'hate,' : 'like,';
}
if(strpos($tps, 'follow') !== false || $uid > 0) {
	include DT_ROOT.'/include/module.func.php';
	if(followed($uid)) $str .= 'follow,';
}
if($str) echo $str;
?>