<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
@set_time_limit(0);
define('DT_ADMIN', true);
require 'common.inc.php';
if($DT_BOT) dhttp(403);
$_areaids = '';
$_areaid = array();
if($_aid) {
	$AREA or $AREA = cache_read('area.php');
	$_areaids = $AREA[$_aid]['child'] ? $AREA[$_aid]['arrchildid'] : $_aid;
	$_areaid = explode(',', $_areaids);
}
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/include/admin.func.php';
require DT_ROOT.'/include/crypt.func.php';
require_once DT_ROOT.'/include/cache.func.php';
if(isset($file)) {
	 check_name($file) or $file = isset($_GET['file']) ? $_GET['file'] : 'index';//Fix webuploader
} else {
	$file = 'index';
}
$secretkey = 'a'.strtolower(substr(md5(DT_KEY), -6));
if($CFG['authadmin'] == 'cookie') {
	$_destoon_admin = get_cookie($secretkey);
	$_destoon_admin = $_destoon_admin ? intval($_destoon_admin) : 0;
} else {
	$session = new dsession();
	$_destoon_admin = isset($_SESSION[$secretkey]) ? intval($_SESSION[$secretkey]) : 0;
}
$_founder = is_founder($_userid) ? $_userid : 0;
$_catids = $_childs = $_self = '';
$_catid = $_child = array();
if($file != 'login') {
	if($_groupid != 1 || $_admin < 1 || !$_destoon_admin) msg('', '?file=login&forward='.urlencode($DT_URL));
	if(!admin_check()) {
		admin_log(1);
		$db->query("DELETE FROM {$DT_PRE}admin WHERE userid=$_userid AND url='?".$DT_QST."'");
		msg('警告！您无权进行此操作 Error(00)');
	}
}
if($DT['admin_log'] && $action != 'import') admin_log();
if($DT['admin_online']) admin_online();
if(isset($reason) && is_array($itemid)) admin_notice();
$search = isset($search) ? intval($search) : 0;
$widget = isset($widget) ? intval($widget) : 0;
$psize = isset($psize) ? intval($psize) : 0;
if($psize > 0 && $psize != $pagesize) {
	$pagesize = $psize;
	$offset = ($page-1)*$pagesize;
}
include DT_ROOT.'/module/'.$module.'/common.inc.php';
include DT_ROOT.'/module/'.$module.'/admin/'.$file.'.inc.php';
?>