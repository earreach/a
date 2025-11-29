<?php
require '../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
if(strpos($_SERVER['QUERY_STRING'], '404;') !== false) {
	$DT_URL = str_replace('404;', '', $_SERVER['QUERY_STRING']);
	$DT_URL = str_replace(':80', '', $DT_URL);
}
if($DT['log_404'] && strpos($DT_URL, '/404'.DT_EXT) === false) {
	$url = addslashes(dhtmlspecialchars($DT_URL));
	$refer = addslashes(dhtmlspecialchars($DT_REF));
	$time = $DT_TIME - 86400;
	$r = $db->get_one("SELECT itemid FROM {$DT_PRE}404 WHERE addtime>$time AND url='$url'");
	if(!$r) {
		require DT_ROOT.'/include/client.func.php';
		$robot = is_robot() ? get_robot() : '';
		$ua = addslashes(dhtmlspecialchars(strip_sql(strip_tags(DT_UA))));
		$os = get_os();
		$bs = get_bs();
		$pt = get_env('port');
		$pc = $DT_PC;
		$db->query("INSERT INTO {$DT_PRE}404 (url,refer,robot,username,ip,port,pc,ua,os,bs,addtime) VALUES ('$url','$refer','$robot','$_username','$DT_IP','$pt','$pc','$ua','$os','$bs','$DT_TIME')");
	}
}
if($DT_BOT) dhttp(404, $DT_BOT);
$head_title = '404 Not Found';
if($sns_app) $seo_title = $site_name;
$foot = '';
include template('404', 'message');
?>