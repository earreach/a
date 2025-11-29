<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
require_once DT_ROOT.'/include/mobile.func.php';
include load('mobile.lang');
$back_link = $pages = '';
$areaid = isset($areaid) ? intval($areaid) : 0;
$site_name = $EXT['mobile_sitename'] ? $EXT['mobile_sitename'] : $DT['sitename'].$L['mobile_version'];
$DT_PC = $GLOBALS['DT_PC'] = 0;
$MURL = $MODULE[2]['linkurl'];
$wh = $DT['color_mw'] ? '-wh' : '';
$_cart = 0;
$share_icon = 0;
$sns_app = 0;
$MOB_MODULE = array();
foreach($MODULE as $v) {
	if($v['moduleid'] > 3 && $v['ismenu'] && !$v['islink']) $MOB_MODULE[] = $v;
}
$js_pageid = random(4, 'a-Z').substr(DT_TIME, -4);
$js_load = '';
$js_pull = 1;
$js_item = $js_album = 0;
$foot = 'channel';
?>