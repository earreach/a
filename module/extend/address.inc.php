<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
isset($auth) or $auth = '';
$auth = $auth ? decrypt($auth, DT_KEY.'MAP') : '';
$t = explode('|', $auth);
$address = isset($t[0]) ? $t[0] : '';
$company = isset($t[1]) ? $t[1] : '';
$mapmid = isset($t[2]) ? $t[2] : '';
$MAP = cache_read('module-4.php');
$map = $MAP['map'] ? $MAP['map'] : 'baidu';
include DT_ROOT.'/api/map/'.$map.'/config.inc.php';
$mapurl = 'https://api.map.baidu.com/geocoder?address='.urlencode($address).'&output=html&scr='.urlencode($company);
if($map == 'qq') {
	$mapurl = 'https://map.qq.com/?type=poi&what='.urlencode($address);
} else if($map == 'google') {
	$mapurl = 'https://www.google.com/maps/search/'.$address.'/';
}
$map_key or dheader($mapurl);
$template = 'address';
$head_title = $L['address_title'];
$head_keywords = $head_description = '';
if($DT_PC) {
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace(DT_PATH, DT_MOB, $DT_URL);
} else {
	$head_name = $L['address_title'];
	if($sns_app) $seo_title = $site_name;
	$foot = '';
}
include template($template, $module);
?>