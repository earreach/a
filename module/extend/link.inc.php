<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$ext = 'link';
$MOD[$ext.'_enable'] or message($L['closed'], $DT_PC ? DT_PATH : DT_MOB, 6);
$url = $EXT[$ext.'_url'];
$mob = $EXT[$ext.'_mob'];
$TYPE = get_type($ext, 1);
$_TP = sort_type($TYPE);
require DT_ROOT.'/module/'.$module.'/'.$ext.'.class.php';
$do = new dlink();
$typeid = isset($typeid) ? intval($typeid) : 0;
if($action == 'reg') {
	$MOD['link_reg'] or message($L['link_reg_close']);
	if($submit) {
		captcha($captcha, 1);
		$post = dhtmlspecialchars($post);
		if($do->pass($post)) {
			$r = $db->get_one("SELECT itemid FROM {$DT_PRE}link WHERE linkurl='$post[linkurl]' AND username=''");
			if($r) message($L['link_url_repeat']);
			$post['status'] = 2;
			$post['level'] = 0;
			$post['areaid'] = $cityid;
			$do->add($post);
			dheader('index'.DT_EXT.'?action=result');
			message($L['link_check'], $DT_PC ? $url : $mob);
		} else {
			dheader('index'.DT_EXT.'?action=result&auth='.encrypt($do->errmsg, DT_KEY.'GBE', 300));
		}
	} else {
		$type_select = type_select($TYPE, 1, 'post[typeid]', $L['link_choose_type'], 0, 'id="typeid"');
		$head_title = $L['link_reg'].$DT['seo_delimiter'].$L['link_title'];
	}
} elseif($action == 'result') {
	$code = '';
	if(isset($auth)) {
		$code = decrypt($auth, DT_KEY.'LRE');
		$code or $code = 'ko';
	}
	$head_title = $L['link_title'];
} else {
	$head_title = $L['link_title'];
	if($catid) $typeid = $catid;
	if($typeid) {
		isset($TYPE[$typeid]) or dheader($DT_PC ? $url : $mob);
		$head_title = $TYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
	}
}
$template = $ext;
if($DT_PC) {
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace($url, $mob, $DT_URL);
} else {
	$head_name = $L['link_title'];
	if($sns_app) $seo_title = $site_name;
	$foot = '';
}
include template($template, $module);
?>