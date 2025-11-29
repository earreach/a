<?php
defined('IN_DESTOON') or exit('Access Denied');
$_child = $db->get_one("SELECT * FROM {$DT_PRE}member_child WHERE itemid=$_cid");
$_child_self = basename(get_env('self'));
if($_child_self != 'logout'.DT_EXT) {
	($_child && $_child['status'] == 3 && $_child['parent'] == $_username && $_child['permission']) or dalert(lang('message->without_permission'), 'logout'.DT_EXT);
}
$_cname = $_child['username'];
include DT_ROOT.'/file/config/child.inc.php';
$_child_p = explode(',' , $_child['permission']);
$_child_menu = $_child_mids = array();
$_child_file = array('index'.DT_EXT, 'logout'.DT_EXT, 'child'.DT_EXT, $DT['file_login'], $DT['file_register']);
foreach($_child_p as $v) {
	$r = array();
	if(is_numeric($v)) {
		if(!in_array($v, $MENUMODS)) continue;
		$r['name'] = $MODULE[$v]['name'];
		$r['en'] = $MODULE[$v]['module'];
		$r['url'] = $DT['file_my'].'?mid='.$v;
		$r['id'] = 'mid_'.$v;
		$r['mid'] = $v;
		$_child_mids[] = $v;
	} else {
		$r['name'] = $CHILD[$v];
		$r['en'] = $v;
		$r['url'] = $v.DT_EXT;
		$r['id'] = $v;
		$r['mid'] = 0;
		$_child_file[] = $v.DT_EXT;
		if($v == 'im') $_child_file[] = 'chat'.DT_EXT;
	}
	$_child_menu[] = $r;
}
if($_child_self == $DT['file_my']) {
	in_array($mid, $_child_mids) or dheader('child'.DT_EXT);
} else {
	in_array($_child_self, $_child_file) or dheader('child'.DT_EXT);
}
?>