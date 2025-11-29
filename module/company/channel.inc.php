<?php 
defined('IN_DESTOON') or exit('Access Denied');
$M = array();
foreach($MENU as $k=>$v) {
	$i = $D_MENU[$k];
	if(is_numeric($i)) {
		$v['icon'] = $MODULE[$i]['icon'] ? $MODULE[$i]['icon'] : DM_SKIN.'mod-'.$MODULE[$i]['module'].'.png';
	} else {
		$v['icon'] = DM_SKIN.'my-'.$k.'.png';
	}
	$M[] = $v;
}
$seo_title = isset($HOME['seo_title']) && $HOME['seo_title'] ? $HOME['seo_title'] : '';
$head_title = $L['channel'];
if($DT_PC) {
	//
} else {
	$head_name = $head_title;
}
include template('channel', $template);
?>