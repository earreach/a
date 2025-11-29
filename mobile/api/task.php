<?php
@set_time_limit(0);
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
#check_referer() or exit;
if($DT_BOT) exit;
@header("Content-type:text/javascript");	
include template('line', 'chip');
$db->linked or exit;
isset($html) or $html = '';
if($html) {
	$task_index = $task_list = $task_item = 0;
	if($moduleid == 1) {
		//
	} else {
		$MOD['index_html'] = 0;
		include DT_ROOT.'/module/'.$module.'/common.inc.php';
		include DT_ROOT.'/module/'.$module.'/task.inc.php';
	}
}
#include DT_ROOT.'/api/cron.inc.php';
if($DT['stats']) {
	$DT_URL = $DT_REF;
	$DT_REF = (isset($refer) && is_url($refer)) ? $refer : '';
	include DT_ROOT.'/api/stats.inc.php';
}
?>