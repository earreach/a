<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
require '../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
if($action == 'cms' || $action == 'b2b') {
	if(get_cookie('mobile') != 'cms') set_cookie('mobile', 'cms');
	$DT_MBS = 'cms';
	if(isset($url) && strpos($url, $EXT['mobile_url']) === 0) dheader($url);
} else {
	if(get_cookie('mobile') != 'web') set_cookie('mobile', 'web');
	$DT_MBS = 'web';
}
($DT_MOB == 'ios' || $DT_MOB == 'android') or dheader('index.php?reload='.$DT_TIME);
require DT_ROOT.'/module/'.$module.'/index.inc.php';
?>