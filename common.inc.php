<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
define('DT_DEBUG', 1);
if(DT_DEBUG) {
	error_reporting(E_ALL);
	$mtime = explode(' ', microtime());
	$debug_starttime = $mtime[1] + $mtime[0];
} else {
	error_reporting(0);
}
if(isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) exit('Request Denied');
if(function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(0);
$MQG = function_exists('get_magic_quotes_gpc') ? @get_magic_quotes_gpc() : @ini_set('magic_quotes_runtime', 0);
foreach(array('_POST', '_GET') as $__R) {
	if($$__R) { 
		foreach($$__R as $__k => $__v) {
			if(substr($__k, 0, 1) == '_') if($__R == '_POST') { unset($_POST[$__k]); } else { unset($_GET[$__k]); }
			if(isset($$__k) && $$__k == $__v) unset($$__k);
		}
	}
}
define('IN_DESTOON', true);
define('IN_ADMIN', defined('DT_ADMIN') ? true : false);
define('DT_ROOT', str_replace("\\", '/', dirname(__FILE__)));
if(defined('DT_REWRITE')) include DT_ROOT.'/include/rewrite.inc.php';
$CFG = array();
require DT_ROOT.'/config.inc.php';
define('DT_PATH', $CFG['url']);
define('DT_STATIC', $CFG['static'] ? $CFG['static'] : $CFG['url'].'static/');
define('DT_DOMAIN', $CFG['cookie_domain'] ? substr($CFG['cookie_domain'], 1) : '');
define('DT_WIN', strpos(strtoupper(PHP_OS), 'WIN') !== false ? true: false);
define('DT_UA', $_SERVER['HTTP_USER_AGENT']);
define('DT_CHMOD', ($CFG['file_mod'] && !DT_WIN) ? $CFG['file_mod'] : 0);
define('DT_LANG', $CFG['language']);
define('DT_KEY', $CFG['authkey']);
define('DT_PRE', $CFG['tb_pre']);
define('DT_EDITOR', $CFG['editor']);
define('DT_CDN', $CFG['cdn'] ? 1 : 0);
define('DT_EXT', $CFG['ext']);
define('DT_CLOUD_SSL', $CFG['cloud_ssl']);
define('DT_CLOUD_UID', $CFG['cloud_uid']);
define('DT_CLOUD_KEY', $CFG['cloud_key']);
define('DT_CHARSET', strtoupper($CFG['charset']));
define('DT_CACHE', $CFG['cache_dir'] ? $CFG['cache_dir'] : DT_ROOT.'/file/cache');
define('DT_SKIN', DT_STATIC.'skin/'.$CFG['skin'].'/');
define('DM_SKIN', DT_STATIC.'skin/'.$CFG['skin_mobile'].'/');
define('VIP', $CFG['com_vip']);
define('errmsg', 'Invalid Request');
$L = array();
include DT_ROOT.'/lang/'.DT_LANG.'/lang.inc.php';
require DT_ROOT.'/version.inc.php';
require DT_ROOT.'/include/global.func.php';
require DT_ROOT.'/include/safe.func.php';
require DT_ROOT.'/include/cloud.func.php';
require DT_ROOT.'/include/tag.func.php';
require DT_ROOT.'/api/im.func.php';
require DT_ROOT.'/api/extend.func.php';
if(!$MQG) {
	if($_POST) $_POST = daddslashes($_POST);
	if($_GET) $_GET = daddslashes($_GET);
	if($_COOKIE) $_COOKIE = daddslashes($_COOKIE);
}
if(function_exists('date_default_timezone_set')) date_default_timezone_set($CFG['timezone']);
$DT_PRE = $CFG['tb_pre'];
$DT_QST = addslashes($_SERVER['QUERY_STRING']);
$DT_TIME = time() + $CFG['timediff'];
$DT_IP = get_env('ip');
$DT_URL = get_env('url');
$DT_REF = get_env('referer');
$DT_MOB = get_env('mobile');
$DT_MBS = get_env('browser');
$DT_BOT = is_robot();
$DT_TOUCH = is_touch();
$DT_TODAY = datetotime(date('Y-m-d', $DT_TIME).' 23:59:59');
$DT_PC = $GLOBALS['DT_PC'] = 1;
define('DT_TIME', $DT_TIME);
define('DT_IP', $DT_IP);
define('DT_TOUCH', $DT_TOUCH);
define('DT_REFRESH', substr(md5(DT_RELEASE.DT_KEY.DT_REVISOR), 0, 10));
header("Content-Type:text/html;charset=".DT_CHARSET);
require DT_ROOT.'/include/db_'.$CFG['database'].'.class.php';
require DT_ROOT.'/include/cache_'.$CFG['cache'].'.class.php';
require DT_ROOT.'/include/session_'.$CFG['session'].'.class.php';
require DT_ROOT.'/include/file.func.php';
if(!empty($_SERVER['REQUEST_URI'])) strip_uri($_SERVER['REQUEST_URI']);
if($_POST) { $_POST = strip_sql($_POST); strip_key($_POST); }
if($_GET) { $_GET = strip_sql($_GET); strip_key($_GET); }
if($_COOKIE) { $_COOKIE = strip_sql($_COOKIE); strip_key($_COOKIE); }
if(!IN_ADMIN) banip();
if($_POST) extract($_POST, EXTR_SKIP);
if($_GET) extract($_GET, EXTR_SKIP);
$db_class = 'db_'.$CFG['database'];
$db = new $db_class;
$db->halt = (DT_DEBUG || IN_ADMIN) ? 1 : 0;
$db->connect($CFG['db_host'], $CFG['db_user'], $CFG['db_pass'], $CFG['db_name'], $CFG['db_expires'], $CFG['db_charset'], $CFG['pconnect']);
$dc = new dcache();
$dc->pre = $CFG['cache_pre'];
require DT_ROOT.'/include/db.class.php';
$DT = $MOD = $EXT = $CSS = $JS = $DTMP = $CAT = $ARE = $AREA = array();
$CACHE = cache_read('module.php');
if(!$CACHE) {
	require_once DT_ROOT.'/include/admin.func.php';
	require_once DT_ROOT.'/include/post.func.php';
	require_once DT_ROOT.'/include/cache.func.php';
    cache_all();
	$CACHE = cache_read('module.php');
}
$DT = $CACHE['dt'];
$MODULE = $CACHE['module'];
$EXT = cache_read('module-3.php');
define('DT_MAX_LEN', $DT['max_len']);
define('RE_WRITE', $DT['rewrite'] ? ($DT['search_rewrite'] ? 2 : 1) : 0);
$lazy = $DT['lazy'] ? 1 : 0;
if(!IN_ADMIN && ($DT['close'] || $DT['defend_cc'])) include DT_ROOT.'/include/defend.inc.php';
unset($CACHE, $CFG['db_host'], $CFG['db_user'], $CFG['db_pass'], $db_class, $db_file);
$moduleid = isset($moduleid) ? intval($moduleid) : 1;
//var_dump($moduleid);die();
$searchid = $moduleid > 3 ? $moduleid : $DT['searchid'];
if($moduleid > 1) {
	isset($MODULE[$moduleid]) or dheader(DT_PATH);
	$module = $MODULE[$moduleid]['module'];
	$MOD = $moduleid == 3 ? $EXT : cache_read('module-'.$moduleid.'.php');
	include DT_ROOT.'/lang/'.DT_LANG.'/'.$module.'.inc.php';
} else {
	$moduleid = 1;
	$module = 'destoon';
}
$cityid = 0;
$city_name = $L['allcity'];
$city_domain = $city_template = $city_sitename = '';
if($DT['city']) include DT_ROOT.'/include/city.inc.php';
define('DT_MOB', $EXT['mobile_url']);
($DT['gzip_enable'] && !$_POST && !defined('DT_MOBILE')) ? ob_start('ob_gzhandler') : ob_start();
if(isset($forward)) {
	if(isset($_GET['forward'])) $forward = urldecode($forward);
} else if($DT_REF) {
	$forward = is_uri($DT_REF) ? $DT_REF : DT_PATH;
} else {
	$forward = DT_PATH;
}
strip_uri($forward);
$destoon_task = '';
(isset($action) && check_name($action)) or $action = '';
(isset($job) && check_name($job)) or $job = '';
$submit = isset($_POST['submit']) ? 1 : 0;
if($submit) {
	isset($captcha) or $captcha = '';
	isset($answer) or $answer = '';
}
$mid = isset($mid) ? intval($mid) : 0;
$sum = isset($sum) ? intval($sum) : 0;
$page = isset($page) ? max(intval($page), 1) : 1;
$catid = isset($catid) ? intval($catid) : 0;
$areaid = isset($areaid) ? intval($areaid) : 0;
$itemid = isset($itemid) ? (is_array($itemid) ? array_map('intval', $itemid) : intval($itemid)) : 0;
$pagesize = $DT['pagesize'] ? $DT['pagesize'] : 30;
$offset = ($page-1)*$pagesize;
$kw = isset($_GET['kw']) ? strip_kw($_GET['kw'], $DT['max_kw']) : '';
$keyword = $kw ? str_replace(array(' ', '*'), array('%', '%'), $kw) : '';
$seo_file = $seo_title = $head_title = $head_keywords = $head_description = $head_canonical = $head_mobile = $head_name = '';
$head_pc = $moduleid > 1 ? str_replace(DT_MOB.$MODULE[$moduleid]['moduledir'].'/', $MODULE[$moduleid]['linkurl'], $DT_URL) : str_replace(DT_MOB, DT_PATH, $DT_URL);
if($catid) $CAT = get_cat($catid);
if($areaid) $ARE = get_area($areaid);
$_userid = $_admin = $_aid = $_cid = $_message = $_chat = $_sound = $_online = $_money = $_credit = $_sms = $_fans = $_follows = $_moments = $_validate = 0;
$_username = $_company = $_passport = $_truename = $_mobile = $_cname = $_role = ''; $_groupid = 3; $_gradeid = 1;
$destoon_auth = get_cookie('auth');
if($destoon_auth) $destoon_auth = decrypt($destoon_auth, DT_KEY.'USER');
if($destoon_auth) {	
	$_dauth = explode('|', $destoon_auth);
	$_userid = isset($_dauth[0]) ? intval($_dauth[0]) : 0;
	if($_userid) {
		$_password = isset($_dauth[1]) ? trim($_dauth[1]) : '';
		$_cid = isset($_dauth[2]) ? intval($_dauth[2]) : 0;
		$USER = $db->get_one("SELECT username,passport,company,truename,mobile,password,groupid,gradeid,fans,follows,moments,validate,email,message,chat,sound,online,sms,credit,money,loginip,admin,role,aid,edittime FROM {$DT_PRE}member WHERE userid=$_userid");
		if($USER && $USER['password'] == $_password) {
			if($USER['groupid'] == 2) message(lang('message->common_forbidden'));
			if($USER['loginip'] != $DT_IP && ($DT['ip_login'] == 2 || ($DT['ip_login'] == 1 && IN_ADMIN))) {
				$_userid = 0; set_cookie('auth', '');
				message(lang('message->common_login', array($USER['loginip'].'-'.ip2area($USER['loginip']))), $DT_PC ? DT_PATH : DT_MOB, 10);
			}
			extract($USER, EXTR_PREFIX_ALL, '');
		} else {
			$_userid = 0;
			if($db->linked) set_cookie('auth', '');
		}
		unset($destoon_auth, $USER, $_dauth, $_password);
	}
}
if(!IN_ADMIN) {
	if($DT['max_guest'] > 0 && !$DT_BOT && $_userid < 1 && $moduleid > 3 && $page > $DT['max_guest']) $job == 'ajax' ? exit : login();
	if($_groupid == 1) include DT_ROOT.'/module/member/admin.inc.php';
	if($_userid) $db->query("REPLACE INTO {$DT_PRE}online (userid,username,ip,moduleid,online,lasttime) VALUES ('$_userid','$_username','$DT_IP','$moduleid','$_online','$DT_TIME')");
}
$MG = cache_read('group-'.$_groupid.'.php');
?>