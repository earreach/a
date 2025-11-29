<?php
$moduleid = 2;
require '../common.inc.php';
if($DT_BOT) dhttp(403);
if($mid) {
	isset($MODULE[$mid]) or dheader($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']);
	if(!$_userid) $action = 'add';//Guest
	if($MG['type'] && !$_edittime && $action == 'add') dheader('edit.php?tab=2');	
	if($_credit < 0 && $MOD['credit_less'] && $action == 'add') dheader('credit.php?action=less');
	require DT_ROOT.'/module/member/global.func.php';
	check_validate();
	$menu_id = 1;
	$group_editor = $MG['editor'];
	in_array($group_editor, array('Default', 'Destoon', 'Simple', 'Basic')) or $group_editor = 'Destoon';
	isset($admin_user) or $admin_user = false;
	$show_oauth = $MOD['oauth'];
	$viewport = 0;

	if($submit) {
		check_post() or dalert($L['bad_data']);//safe
		$BANWORD = cache_read('banword.php');
		if($BANWORD && isset($post)) {
			$keys = array('title', 'tag', 'introduce', 'content');
			foreach($keys as $v) {
				if(isset($post[$v])) $post[$v] = banword($BANWORD, $post[$v]);
			}
		}
		if($DT['spam_appcode'] && isset($post)) cloud_spam($post, $DT['spam_appcode']);
	}

	$MYMODS = array();
	if(isset($MG['moduleids']) && $MG['moduleids']) {
		$MYMODS = explode(',', $MG['moduleids']);
	}
	if($MYMODS) {
		in_array($mid, $MYMODS) or dheader($DT['file_my']);
		foreach($MYMODS as $k=>$v) {
			if(!isset($MODULE[$v])) unset($MYMODS[$k]);
		}
	}
	$MENUMODS = $MYMODS;
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
	if($_cid) require DT_ROOT.'/include/child.inc.php';
	$vid = $mid;
	$IMVIP = isset($MG['vip']) && $MG['vip']; 
	$moduleid = $mid;
	$module = $MODULE[$moduleid]['module'];
	$MOD = cache_read('module-'.$moduleid.'.php');
	$my_file = DT_ROOT.'/module/'.$module.'/my.inc.php';
	if(is_file($my_file)) {
		require $my_file;
	} else {
		dheader($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']);
	}
} else {
	require DT_ROOT.'/module/'.$module.'/my.inc.php';
}
?>