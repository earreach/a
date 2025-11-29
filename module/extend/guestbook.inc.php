<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$ext = 'guestbook';
$MOD[$ext.'_enable'] or message($L['closed'], $DT_PC ? DT_PATH : DT_MOB, 6);
$url = $EXT[$ext.'_url'];
$mob = $EXT[$ext.'_mob'];
require DT_ROOT.'/module/'.$module.'/'.$ext.'.class.php';
$do = new $ext();
$destoon_task = rand_task();
$TYPE = explode('|', trim($MOD['guestbook_type']));
$report = (isset($report) && $report) ? 1 : 0;
if($action == 'add') {
	if($MOD[$ext.'_guest'] == 2) {
		require DT_ROOT.'/include/client.func.php';	
		if(area_parse(ip2area($DT_IP)) != $L['gbook_china']) login();
	} else if($MOD[$ext.'_guest'] == 0) {
		login();
	}
	if($submit) {
		captcha($captcha, $MOD['guestbook_captcha']);
		$post['reply'] = '';
		if($do->pass($post)) {
			$post['areaid'] = $cityid;
			$do->add($post);
			if(is_email($MOD[$ext.'_email'])) {
				$r = $do->get_one();
				if($r) send_mail($MOD[$ext.'_email'], $r['title'], $r['content']);
			}
			dheader('index'.DT_EXT.'?action=result&report='.$report);
		} else {
			dheader('index'.DT_EXT.'?action=result&report='.$report.'&auth='.encrypt($do->errmsg, DT_KEY.'GBE', 300));
		}
	} else {
		$rid = isset($rid) ? intval($rid) : 0;
		$content = isset($content) ? dhtmlspecialchars(stripslashes($content)) : '';
		$truename = $telephone = $email = $qq = $wx = $ali = $skype = $video = '';
		$thumbs = array();
		if($_userid) {
			$user = userinfo($_username);
			$truename = $user['truename'];
			$telephone = $user['telephone'] ? $user['telephone'] : $user['mobile'];
			$email = $user['mail'] ? $user['mail'] : $user['email'];
			$qq = $user['qq'];
			$wx = $user['wx'];
			$ali = $user['ali'];
			$skype = $user['skype'];
		}
		$MOD['thumb_width'] = $MOD['thumb_height'] = 200;
	}
} elseif($action == 'result') {
	$code = $forward = '';
	if(isset($auth)) {
		$code = decrypt($auth, DT_KEY.'GBE');
		$code or $code = 'ko';
	}
} else {
	$type = '';
	$condition = "status=3 AND reply<>''";
	if($keyword) $condition .= match_kw('content', $keyword);
	if($cityid) $condition .= ($AREA[$cityid]['child']) ? " AND areaid IN (".$AREA[$cityid]['arrchildid'].")" : " AND areaid=$cityid";
	if($itemid) $condition .= " AND itemid=$itemid";
	$lists = $do->get_list($condition);
}
$head_title = $report ? $L['gbook_report_title'] : $L['gbook_title'];
$template = $ext;
if($DT_PC) {	
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = strpos($DT_URL, '/api/') === false ? str_replace($url, $mob, $DT_URL) : str_replace(DT_PATH, DT_MOB, $DT_URL);
} else {
	if($action == 'add' || $action == 'result') {
		//
	} else {
		$pages = mobile_pages($items, $page, $pagesize);
	}
	$head_name = $head_title;
	if($sns_app) $seo_title = $site_name;
	$foot = '';
}
include template($template, $module);
?>