<?php
require '../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
$app = '';
if(!in_array($DT_MBS, array('web', 'cms', 'app'))) {
	if($DT_MOB == 'ios') {
		if($EXT['mobile_ios']) $app = DT_PATH.'api/app.php';
	} else if($DT_MOB == 'android') {
		if($EXT['mobile_adr']) $app = DT_PATH.'api/app.php';
	}
}
$head_title = $head_name = $L['more_title'];
if($sns_app) $seo_title = $site_name;;
$foot = $DT['max_cart'] > 0 ? 'my' : 'more';
include template('more', 'mobile');
?>