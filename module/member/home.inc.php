<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
($MG['biz'] && $MG['homepage'] && $MG['home']) or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('homepage.lang');
$need_check =  $MOD['home_check'] == 2 ? $MG['home_check'] : $MOD['home_check'];
$tab = isset($tab) ? intval($tab) : 0;
$menu_id = 2;
switch($action) {
	case 'reset':
		isset($item) or $item = '';
		$dc->remove("SELECT * FROM ".DT_PRE."company_setting WHERE userid=$_userid");
		$dc->remove("SELECT * FROM ".DT_PRE."company_home WHERE userid=$_userid AND status>0 ORDER BY listorder ASC");
		if(in_array($item, array('menu', 'side', 'main'))) {
			$db->query("DELETE FROM {$DT_PRE}company_home WHERE userid=$_userid AND type='$item'");
			$tabs = array('menu'=>1, 'side'=>2, 'main'=>3);
			dmsg($L['home_msg_reset'], '?tab='.$tabs[$item]);
		} else {
			delete_upload($setting['background'], $_userid);
			delete_upload($setting['logo'], $_userid);
			delete_upload($setting['video'], $_userid);
			delete_upload($setting['banner'], $_userid);
			delete_upload($setting['bannerf'], $_userid);
			delete_upload($setting['banner1'], $_userid);
			delete_upload($setting['banner2'], $_userid);
			delete_upload($setting['banner3'], $_userid);
			delete_upload($setting['banner4'], $_userid);
			delete_upload($setting['banner5'], $_userid);
			if($need_check) $db->query("DELETE FROM {$DT_PRE}company_check WHERE username='$_username'");
			$db->query("DELETE FROM {$DT_PRE}company_setting WHERE userid=$_userid");
			$db->query("DELETE FROM {$DT_PRE}company_home WHERE userid=$_userid");
			dmsg($L['home_msg_reset'], '?tab='.$tab);
		}
	break;
	default:
		$need_captcha = $MOD['captcha_home'] == 2 ? $MG['captcha'] : $MOD['captcha_home'];
		$CK = $need_check ? $db->get_one("SELECT * FROM {$DT_PRE}company_check WHERE username='$_username' AND edittime=0") : array();
		if($CK) $submit = 0;
		if($submit) {
			captcha($captcha, $need_captcha);
			foreach(array('background', 'logo', 'video', 'banner', 'bannerf', 'banner1', 'banner2', 'banner3', 'banner4', 'banner5', 'bannerlink1', 'bannerlink2', 'bannerlink3', 'bannerlink4', 'bannerlink5') as $v) {
				is_url($setting[$v]) or $setting[$v] = '';
			}
			foreach(array('css', 'announce', 'seo_title', 'seo_keywords', 'seo_description') as $v) {
				$setting[$v] = banword($BANWORD, $setting[$v], true, 'goback');
			}
			$HOME = get_company_setting($_userid);
			if(!$need_check) {
				if($HOME['background'] != $setting['background']) delete_upload($HOME['background'], $_userid);
				if($HOME['logo'] != $setting['logo']) delete_upload($HOME['logo'], $_userid);
				if($HOME['video'] != $setting['video']) delete_upload($HOME['video'], $_userid);
				if($HOME['banner'] != $setting['banner']) delete_upload($HOME['banner'], $_userid);
				if($HOME['bannerf'] != $setting['bannerf']) delete_upload($HOME['bannerf'], $_userid);
				if($HOME['banner1'] != $setting['banner1']) delete_upload($HOME['banner1'], $_userid);
				if($HOME['banner2'] != $setting['banner2']) delete_upload($HOME['banner2'], $_userid);
				if($HOME['banner3'] != $setting['banner3']) delete_upload($HOME['banner3'], $_userid);
				if($HOME['banner4'] != $setting['banner4']) delete_upload($HOME['banner4'], $_userid);
				if($HOME['banner5'] != $setting['banner5']) delete_upload($HOME['banner5'], $_userid);
			}
			clear_upload($setting['background'].$setting['logo'].$setting['video'].$setting['banner'].$setting['bannerf'].$setting['banner1'].$setting['banner2'].$setting['banner3'].$setting['banner4'].$setting['banner5'], $_userid, 'company_setting');
			$announce = $setting['announce'];
			unset($setting['announce']);
			$setting['stats'] = $setting['stats_type'] ? $stats[$setting['stats_type']] : '';
			$setting['kf'] = $setting['kf_type'] ? $kf[$setting['kf_type']] : '';
			$setting = dhtmlspecialchars($setting);
			$home = dhtmlspecialchars($home);
			$setting['announce'] = dsafe($announce);
			if($need_check) {
				$content = addslashes(serialize($setting));
				$homepage = addslashes(serialize($home));
				$db->query("INSERT INTO {$DT_PRE}company_check (username,company,ip,content,homepage,addtime) VALUES ('$_username','$_company','$DT_IP','$content','$homepage','$DT_TIME')");
				dmsg($L['home_msg_check'], '?tab='.$tab);
			} else {
				update_company_setting($_userid, $setting, $home);
			}
			$dc->remove("SELECT * FROM ".DT_PRE."company_setting WHERE userid=$_userid");
			$dc->remove("SELECT * FROM ".DT_PRE."company_home WHERE userid=$_userid AND status>0 ORDER BY listorder ASC");
			dmsg($L['home_msg_save'], '?tab='.$tab);
		} else {
			$CS = cache_read('module-4.php');
			$api_map = $CS['map'];
			$api_stats = $CS['stats'] ? explode(',', $CS['stats']) : array();
			$api_kf = $CS['kf'] ? explode(',', $CS['kf']) : array();
			$HOME = $CK ? unserialize($CK['content']) : get_company_setting($_userid);
			extract($HOME);
			
			$G_HOME = cache_read('home-'.$_groupid.'.php');
			$G_MENU = isset($G_HOME['menu']) ? $G_HOME['menu'] : array();
			$G_SIDE = isset($G_HOME['side']) ? $G_HOME['side'] : array();
			$G_MAIN = isset($G_HOME['main']) ? $G_HOME['main'] : array();

			$U_HOME = $CK ? unserialize($CK['homepage']) : get_company_home($_userid);
			$U_MENU = isset($U_HOME['menu']) ? $U_HOME['menu'] : array();
			$U_SIDE = isset($U_HOME['side']) ? $U_HOME['side'] : array();
			$U_MAIN = isset($U_HOME['main']) ? $U_HOME['main'] : array();

			isset($HOME['side_pos']) or $side_pos = 0;
			isset($HOME['side_width']) or $side_width = 200;
			isset($HOME['show_stats']) or $show_stats = 1;
			isset($HOME['intro_length']) or $intro_length = 1000;
			isset($HOME['stats']) or $stats = '';
			isset($HOME['stats_type']) or $stats_type = '';
			isset($HOME['kf']) or $kf = '';
			isset($HOME['kf_type']) or $kf_type = '';
			isset($HOME['map']) or $map = '';
			isset($HOME['background']) or $background = '';
			isset($HOME['bgcolor']) or $bgcolor = '';
			isset($HOME['bannert']) or $bannert = 0;
			isset($HOME['banner']) or $banner = '';
			isset($HOME['bannerf']) or $bannerf = '';
			isset($HOME['bannerw']) or $bannerw = $MG['bannerw'] ? $MG['bannerw'] : 1200;
			isset($HOME['bannerh']) or $bannerh = $MG['bannerh'] ? $MG['bannerh'] : 300;
			isset($HOME['banner1']) or $banner1 = '';
			isset($HOME['banner2']) or $banner2 = '';
			isset($HOME['banner3']) or $banner3 = '';
			isset($HOME['banner4']) or $banner4 = '';
			isset($HOME['banner5']) or $banner5 = '';
			isset($HOME['bannerlink1']) or $bannerlink1 = '';
			isset($HOME['bannerlink2']) or $bannerlink2 = '';
			isset($HOME['bannerlink3']) or $bannerlink3 = '';
			isset($HOME['bannerlink4']) or $bannerlink4 = '';
			isset($HOME['bannerlink5']) or $bannerlink5 = '';
			isset($HOME['logo']) or $logo = '';
			isset($HOME['video']) or $video = '';
			isset($HOME['css']) or $css = '';
			isset($HOME['announce']) or $announce = '';
			isset($HOME['seo_title']) or $seo_title = '';
			isset($HOME['seo_keywords']) or $seo_keywords = '';
			isset($HOME['seo_description']) or $seo_description = '';
			$head_title = $L['home_title'];
		}
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	$js_pull = 0;
	$head_name = $head_title;
	$seo_title = '';
}
include template('home', $module);
?>