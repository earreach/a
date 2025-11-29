<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$ext = 'feed';
$MOD[$ext.'_enable'] or message($L['closed'], $DT_PC ? DT_PATH : DT_MOB, 6);
$url = $EXT[$ext.'_url'];
$mob = $EXT[$ext.'_mob'];
$FD = array();
foreach($MODULE as $m) {
	if($m['islink'] || !$m['ismenu'] || $m['moduleid'] < 5) continue;
	$m['rssurl'] = $MOD['feed_url'].'rss'.DT_EXT.'?mid='.$m['moduleid'];
	$FD[] = $m;
}
if($action == 'diy') {
	$areaid = isset($areaid) ? intval($areaid) : 0;
	$feed_code = '';
	$category_select = '';
	$area_select = '';
	if($mid && $mid > 4 && isset($MODULE[$mid]) && !$MODULE[$mid]['islink']) {
		$feed_code .= $MOD['feed_url'].'rss'.DT_EXT.'?mid='.$mid;
		if($kw == $L['keyword']) $kw = '';
		if($kw && strlen($kw) > 2 && strlen($kw) < 30) $feed_code .= '&kw='.urlencode($kw);
		if($catid) $feed_code .= '&catid='.urlencode($catid);
		if($areaid) $feed_code .= '&areaid='.urlencode($areaid);
		$category_select = category_select('catid', $L['category'], $catid, $mid);
		if(in_array($MODULE[$mid]['module'], array('sell','buy', 'exhibit', 'info', 'job', 'mall', 'group'))) $area_select = ajax_area_select('areaid', $L['rss_area'], $areaid);
	}
} else {
	//
}
$template = $ext;
$head_title = $L['rss_title'];
$head_keywords = $head_description = '';
if($DT_PC) {	
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace($url, $mob, $DT_URL);
} else {
	$head_name = $L['rss_title'];
	if($sns_app) $seo_title = $site_name;
	$foot = '';
}
include template($template, $module);
?>