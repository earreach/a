<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/include/mobile.func.php';
#if(is_pc() && strpos($DT_URL, 'device'.DT_EXT) === false) dheader(DT_PATH.'api/mobile'.DT_EXT.'?action=device&uri='.urlencode($head_pc));
include load('mobile.lang');
$EXT['mobile_enable'] or message($L['msg_mobile_close']);
if($DT_BOT) $EXT['mobile_ajax'] = 0;
$dmobile = get_cookie('mobile');
if($dmobile == '' || $dmobile == 'pc') set_cookie('mobile', 'touch');
$back_link = $pages = '';
$areaid = isset($areaid) ? intval($areaid) : 0;
$site_name = $EXT['mobile_sitename'] ? $EXT['mobile_sitename'] : $DT['sitename'].$L['mobile_version'];
$DT_PC = 0;
$MURL = $MODULE[2]['linkurl'];
$wh = $DT['color_mw'] ? '-wh' : '';
if($DT_MBS == 'screen' && $_username) $MURL = DT_PATH.'api/mobile'.DT_EXT.'?action=sync&auth='.encrypt($_username.'|'.$DT_IP.'|'.$DT_TIME, DT_KEY.'SCREEN').'&goto=';
$_cart = ($DT['max_cart'] && $_userid) ? intval(get_cookie('cart')) : 0;
$share_icon = ($DT_MBS == 'weixin' || $DT_MBS == 'qq') ? DT_PATH.'apple-touch-icon-precomposed.png' : '';
$sns_app = in_array($DT_MBS, array('weixin', 'wxmini', 'wxwork', 'qq', 'tim', 'alipay', 'dingtalk', 'weibo')) ? 1 : 0;
$MOB_MODULE = array();
foreach($MODULE as $v) {
	if($v['moduleid'] > 3 && $v['ismenu'] && !$v['islink']) $MOB_MODULE[] = $v;
}
$js_pageid = random(4, 'a-Z').substr(DT_TIME, -6);
$js_load = '';
$js_pull = in_array($action, array('add', 'edit')) ? 0 : 1;
$js_item = $js_album = 0;
$foot = 'channel';
?>