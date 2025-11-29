<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(DT_TOUCH && $EXT['mobile_enable']) require_once DT_ROOT.'/include/mobile.inc.php';
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = $DT_URL;
} else {	
	$foot = 'channel';
}
isset($file) or $file = 'homepage';
if(isset($update) || isset($preview)) {
	$db->cids = 1;
	userclean($username);
}
$COM = userinfo($username);
$userid = $COM['userid'];
$groupid = $COM['groupid'];
$UG = cache_read('group-'.$groupid.'.php');
if(!$COM || !$UG || ($groupid < 5 && $groupid > 1)) {
	userclean($username);
	$head_title = $head_name = $L['not_company'];
	if($DT_BOT) dhttp(404, $DT_BOT);
	$foot = 'channel';
	include template('com-notfound', 'message');
	exit;
}
if(!$COM['edittime'] || (!$COM['validated'] && $MOD['homecheck'])) {
	if($DT_BOT) dhttp(404, $DT_BOT);
	$head_title = $head_name = $COM['company'];
	$foot = 'channel';
	include template('com-opening', 'message');
	exit;
}
$domain = $COM['domain'];
if($domain) {
	if(!isset($preview) && !isset($update) && !isset($key)) {
		if($CFG['com_domain']) {
			if(strpos($DT_URL, $domain) === false) {
				$subdomain = userurl($username);
				if(strpos($DT_URL, $subdomain) === false) {
					dheader('http://'.$domain.'/');
				} else {
					if($DT_URL == $subdomain.'index.php' || $DT_URL == $subdomain) dheader('http://'.$domain.'/');
					dheader(str_replace($subdomain, 'http://'.$domain.'/', $DT_URL));
				}
			}
		} else {
			if(strpos($DT_URL, $domain) === false) dheader(userurl($username, ($file && $file != 'homepage') ? 'file='.$file : '', $domain));
		}
	}
	$DT['rewrite'] = intval($CFG['com_rewrite']);
}
$linkurl = userurl($username, '', $domain);
$enterprise = $UG['type'] ? 1 : 0;
$clean = 0;
if($COM['linkurl'] != $linkurl) {
	$COM['linkurl'] = $linkurl;
	$db->query("UPDATE {$table} SET linkurl='$linkurl' WHERE userid=$userid");
	$clean = 1;
}
if($COM['enterprise'] != $enterprise) {
	$COM['enterprise'] = $enterprise;
	$db->query("UPDATE {$DT_PRE}member SET enterprise=$enterprise WHERE userid=$userid");
	$clean = 1;
}
if($MOD['delvip'] && $COM['vip'] && $COM['totime'] && $COM['totime'] < $DT_TIME) {//VIP Expired
	$COM['vip'] = 0;
	$COM['groupid'] = $gid = $COM['regid'] == 5 ? 5 : 6;
	$COM['styleid'] = $COM['styletime'] = 0;
	$db->query("UPDATE {$table} SET groupid=$gid,vip=0,styletime=0,styleid=0 WHERE userid=$userid");
	$db->query("UPDATE {$DT_PRE}member SET groupid=$gid WHERE userid=$userid");
	$clean = 1;
}
if($COM['styletime'] && $COM['styletime'] < $DT_TIME) {//SKIN Expired
	$COM['styleid'] = $COM['styletime'] = 0;
	$db->query("UPDATE {$table} SET styletime=0,styleid=0 WHERE userid=$userid");
	$clean = 1;
}
if($clean) userclean($username);
$COM['year'] = vip_year($COM['fromtime']);
$COM['space'] = userurl($username, 'file=space', $domain);
is_url($COM['thumb']) or $COM['thumb'] = DT_STATIC.'image/company.png';

$api_map = ($MOD['map'] && $UG['map']) ? $MOD['map'] : '';
$api_stats = ($MOD['stats'] && $UG['stats']) ? $MOD['stats'] : '';
$api_kf = ($MOD['kf'] && $UG['kf']) ? $MOD['kf'] : '';
isset($rewrite) or $rewrite = '';
if($rewrite) {
	$r = explode('-', $rewrite);
	$rc = count($r);
	if($rc%2 == 0) {
		for($i = 0; $i < $rc; $i++) {
			if(in_array($r[$i], array('mid', 'itemid', 'typeid', 'page', 'view', 'kw', 'preview', 'update'))) {
				${$r[$i]} = $r[++$i];
			} else {
				++$i;
			}
		}
	}
	$page = isset($page) ? max(intval($page), 1) : 1;
	$catid = isset($catid) ? intval($catid) : 0;
	$itemid = isset($itemid) ? (is_array($itemid) ? $itemid : intval($itemid)) : 0;
	$kw = isset($kw) ? strip_kw($kw, $DT['max_kw']) : '';
	if(strlen($kw) < $DT['min_kw'] || strlen($kw) > $DT['max_kw']) $kw = '';
	$keyword = $kw ? str_replace(array(' ', '*'), array('%', '%'), $kw) : '';
}

$could_contact = check_group($_groupid, $MOD['group_contact']);
$could_showbuy = check_group($_groupid, $MOD['group_buy']);
$hitkey = $_userid.DT_IP.$userid;
if(!$DT_BOT && $MOD['hits'] && !$dc->get($hitkey)) {
	if($DT['cache_hits']) {
		 cache_hits($moduleid, $userid);
	} else {
		$db->query("UPDATE LOW_PRIORITY {$table} SET hits=hits+1 WHERE userid=$userid", 'UNBUFFERED');
	}
	$dc->set($hitkey, '1', 1800);
}
if(!$DT['homepage'] || $file == 'space') {
	include DT_ROOT.'/module/company/space.inc.php';
	exit;
}
if(!$UG['homepage']) {
	if(!$UG['type']) {
		include DT_ROOT.'/module/company/space.inc.php';
		exit;
	}
	$head_title = $head_name = $COM['company'];
	$head_keywords = $COM['keyword'];
	$head_description = $COM['introduce'];
	$member = $COM;
	$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $DT_PRE.'company_data');
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE userid=$userid", 'CACHE');
	$content = $t['content'];	
	if($content) {
		require DT_ROOT.'/include/content.class.php';
		$content = DC::format($content, $DT_PC);
	}
	$could_comment = $COM['domain'] ? 0 : $MOD['comment'];
	$itemid = $COM['userid'];
	$title = $COM['company'];
	$likes = $COM['likes'];
	$hates = $COM['hates'];
	$reports = $COM['reports'];
	$favorites = $COM['favorites'];
	$comments = $COM['comments'];
	$shares = $COM['shares'];
	$foot = '';
	include template($UG['template_show'] ? $UG['template_show'] : 'show', $module);
	exit;
}

include load('homepage.lang');
$HM = cache_read('home.php');
$D_MENU = $HM['menu'];
$D_SIDE = $HM['side'];
$D_MAIN = $HM['main'];

$G_HOME = cache_read('home-'.$groupid.'.php');
$G_MENU = isset($G_HOME['menu']) ? $G_HOME['menu'] : array();
$G_SIDE = isset($G_HOME['side']) ? $G_HOME['side'] : array();
$G_MAIN = isset($G_HOME['main']) ? $G_HOME['main'] : array();

$U_HOME = get_company_home($userid, '', 'CACHE');
$U_MENU = isset($U_HOME['menu']) ? $U_HOME['menu'] : array();
$U_SIDE = isset($U_HOME['side']) ? $U_HOME['side'] : array();
$U_MAIN = isset($U_HOME['main']) ? $U_HOME['main'] : array();

$HOME = get_company_setting($COM['userid'], '', 'CACHE');
$MENU = $SIDE = $MAIN = array();
if($U_MENU) {
	foreach($U_MENU as $k=>$v) {
		$v['linkurl'] = userurl($username, 'file='.$k, $domain);
		if(isset($G_MENU[$k]) && isset($D_MENU[$k])) $MENU[$k] = $v;
	}
} else {
	foreach($G_MENU as $k=>$v) {
		$v['linkurl'] = userurl($username, 'file='.$k, $domain);
		if($v['status'] && isset($D_MENU[$k])) $MENU[$k] = $v;
	}
}
if($U_SIDE) {
	foreach($U_SIDE as $k=>$v) {
		if(isset($G_SIDE[$k]) && isset($D_SIDE[$k])) $SIDE[$k] = $v;
	}
} else {
	foreach($G_SIDE as $k=>$v) {
		if($v['status'] && isset($D_SIDE[$k])) $SIDE[$k] = $v;
	}
}
if($U_MAIN) {
	foreach($U_MAIN as $k=>$v) {
		if(isset($G_MAIN[$k]) && isset($D_MAIN[$k])) $MAIN[$k] = $v;
	}
} else {
	foreach($G_MAIN as $k=>$v) {
		if($v['status'] && isset($D_MAIN[$k])) $MAIN[$k] = $v;
	}
}

$side_pos = isset($HOME['side_pos']) && $HOME['side_pos'] ? 1 : 0;
$side_width = isset($HOME['side_width']) && $HOME['side_width'] ? $HOME['side_width'] : 200;
$show_stats = isset($HOME['show_stats']) && $HOME['show_stats'] == 0 ? 0 : 1;
$mstyle = 0;
$skin = 'default';
$template = 'homepage';
if($COM['styleid']) {
	$t = $db->get_one("SELECT * FROM {$DT_PRE}style WHERE itemid=$COM[styleid]", 'CACHE');
	if($t) {
		$skin = $t['skin'];
		$template = $t['template'];
		$mstyle = $t['mobile'];
	}
} else if($UG['styleid']) {
	$gsid = intval($UG['styleid']);
	$t = $db->get_one("SELECT * FROM {$DT_PRE}style WHERE itemid=$gsid", 'CACHE');
	if($t) {
		$skin = $t['skin'];
		$template = $t['template'];
		$mstyle = $t['mobile'];
	}
}
if($file == 'homepage') {
	$preview = isset($preview) ? intval($preview) : 0;
	if($preview) {
		$t = $db->get_one("SELECT * FROM {$DT_PRE}style WHERE itemid={$preview}", 'CACHE');
		if($t) {
			$skin = $t['skin'];
			$template = $t['template'];
			$mstyle = $t['mobile'];
		}
	}
}
$bannert = isset($HOME['bannert']) ? $HOME['bannert'] : 0;
$banner = isset($HOME['banner']) ? $HOME['banner'] : '';
$bannerf = isset($HOME['bannerf']) ? $HOME['bannerf'] : '';
$banner1 = isset($HOME['banner1']) ? $HOME['banner1'] : '';
$banner2 = isset($HOME['banner2']) ? $HOME['banner2'] : '';
$banner3 = isset($HOME['banner3']) ? $HOME['banner3'] : '';
$banner4 = isset($HOME['banner4']) ? $HOME['banner4'] : '';
$banner5 = isset($HOME['banner5']) ? $HOME['banner5'] : '';
$bannerlink1 = isset($HOME['bannerlink1']) ? $HOME['bannerlink1'] : '';
$bannerlink2 = isset($HOME['bannerlink2']) ? $HOME['bannerlink2'] : '';
$bannerlink3 = isset($HOME['bannerlink3']) ? $HOME['bannerlink3'] : '';
$bannerlink4 = isset($HOME['bannerlink4']) ? $HOME['bannerlink4'] : '';
$bannerlink5 = isset($HOME['bannerlink5']) ? $HOME['bannerlink5'] : '';
if($bannert == 2) {
	if($banner1) {
		if(!$banner2) {
			$bannert = 0;
			$banner = $banner1;
		}
	} else {
		$bannert = 0;
	}
} else if($bannert == 1) {
	if($bannerf) {
		if(preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($bannerf))) {
			$bannert = 0;
			$banner = $bannert;
		}
	} else {
		$bannert = 0;
	}
}
$bannerw = (isset($HOME['bannerw']) && $HOME['bannerw']) ? intval($HOME['bannerw']) : ($UG['bannerw'] ? $UG['bannerw'] : 1200);
$bannerh = (isset($HOME['bannerh']) && $HOME['bannerh']) ? intval($HOME['bannerh']) : ($UG['bannerh'] ? $UG['bannerh'] : 300);
$could_comment = $domain ? 0 : $MOD['comment'];
$homeurl = $MOD['homeurl'];
if($username == $_username || $domain) $could_contact = true;
$HSPATH = DT_STATIC.'home/'.$skin.'/';
if(!$banner) $banner = is_file(DT_ROOT.'/static/home/'.$skin.'/banner.jpg') ? $HSPATH.'banner.jpg' : '';
$background = isset($HOME['background']) ? $HOME['background'] : '';
$bgcolor = isset($HOME['bgcolor']) ? $HOME['bgcolor'] : '';
$logo = isset($HOME['logo']) ? $HOME['logo'] : '';
$video = isset($HOME['video']) ? $HOME['video'] : '';
$css = isset($HOME['css']) ? $HOME['css'] : '';
$announce = isset($HOME['announce']) ? $HOME['announce'] : '';
$map = isset($HOME['map']) ? $HOME['map'] : '';
$stats = isset($HOME['stats']) ? $HOME['stats'] : '';
$kf = isset($HOME['kf']) ? $HOME['kf'] : '';
$comment_proxy = '';
if($domain) {
	$comment_proxy = 'http://'.$domain.'/';
} else {
	if($CFG['com_domain']) {
		$comment_proxy = $linkurl;
		$comment_proxy = substr($CFG['com_domain'], 0, 1) == '.' ? $linkurl : 'http://'.$CFG['com_domain'].'/';
	} else {
		$comment_proxy = DT_PATH;
	}
}
$comment_proxy = encrypt($comment_proxy, DT_KEY.'PROXY');
$home_name = ($COM['shop'] && $COM['vshop']) ? $L['com_shop'] : $L['com_home'];
$album_js = 0;
$_cart = ($DT['max_cart'] && $_userid) ? intval(get_cookie('cart')) : 0;
$menuid = isset($MENU[$file]) ? $file : 'homepage';
$head_title = $head_name = isset($MENU[$file]) ? $MENU[$file]['name'] : (isset($HOME['seo_title']) ? $HOME['seo_title'] : $COM['company']);
$seo_keywords = isset($HOME['seo_keywords']) ? $HOME['seo_keywords'] : '';
$seo_description = isset($HOME['seo_description']) ? $HOME['seo_description'] : '';
$head_keywords = strip_tags($seo_keywords ? $seo_keywords : $COM['company'].','.str_replace('|', ',', $COM['business']));
$head_description = strip_tags($seo_description ? $seo_description : $COM['introduce']);
if($DT['history_module']) history_log($COM, $moduleid, $DT['history_module']);
if(isset($MENU[$file]) && isset($D_MENU[$file])) {
	if(is_numeric($D_MENU[$file])) {		
		$moduleid = $D_MENU[$file];
		if(isset($MODULE[$moduleid])) {
			$module = $MODULE[$moduleid]['module'];
			$MOD = cache_read('module-'.$moduleid.'.php');
			include DT_ROOT.'/lang/'.DT_LANG.'/'.$module.'.inc.php';
			$table = $DT_PRE.$module.'_'.$moduleid;
			$table_data = $DT_PRE.$module.'_data_'.$moduleid;
			include DT_ROOT.'/module/'.$module.'/global.func.php';
			include DT_ROOT.'/module/company/'.$module.'.inc.php';
			exit;
		}
	} else {
		include DT_ROOT.'/module/company/'.$file.'.inc.php';
		exit;
	}
}
if($file == 'channel') {
	include DT_ROOT.'/module/company/'.$file.'.inc.php';
} else {
	include DT_ROOT.'/module/company/homepage.inc.php';
}
?>