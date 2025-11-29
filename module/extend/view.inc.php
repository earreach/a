<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
isset($url) or $url = '';
if(isset($img) && is_url($img)) {
	$url = $img;
} else if(isset($auth)) {
	$auth = decrypt($auth, DT_KEY.'URL');
	if(is_url($auth)) $url = $auth;
}
$src = cutstr($url, '', '?');
is_url($src) or dheader($DT_PC ? DT_PATH : DT_MOB);
$pass = 0;
if(is_uri($src)) {
	$pass = 1;
} else {
	if($DT['remote_url'] && strpos($src, $DT['remote_url']) !== false) $pass = 1;
}
$ext = file_ext($src);
$type = '';
if(in_array($ext, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
	$type = 'image';
	$src = str_replace(array('.thumb.'.$ext, '.middle.'.$ext), array('', ''), $src);
	$head_title = $L['view_title'];
} else if(in_array($ext, array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'))) {
	$pdf = '';
	if(strpos($src, 'file/upload') !== false) {
		$_pdf = 'file/upload/'.substr(cutstr($src, 'file/upload', ''), 0, -strlen($ext)).'pdf';
		if(is_file(DT_ROOT.'/'.$_pdf)) $pdf = DT_PATH.$_pdf;
	}
	$head_title = $L['view_file_title'];
	if($pdf) {
		$src = $pdf.'?v='.DT_TIME;
		$type = 'pdf';
	} else {
		$src = 'https://view.officeapps.live.com/op/embed.aspx?src='.urlencode($src);
		$type = 'doc';
	}
	if($job == 'preview') dheader($src);
} else if(in_array($ext, array('pdf'))) {
	$src .= '?v='.DT_TIME;
	$type = 'pdf';
	$head_title = $L['view_file_title'];
} else if(in_array($ext, array('mp4'))) {
	$src .= '?v='.DT_TIME;
	$type = 'video';
	$head_title = $L['view_video_title'];
} else if(in_array($ext, array('mp3'))) {
	$src .= '?v='.DT_TIME;
	$type = 'audio';
	$head_title = $L['view_audio_title'];
}
if($type) {	
	$pass or dheader($url);
} else {	
	$pass or dheader($DT_PC ? DT_PATH : DT_MOB);
}
$template = 'view';
$head_keywords = $head_description = '';
if($DT_PC) {	
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace(DT_PATH, DT_MOB, $DT_URL);
} else {
	$head_name = $head_title;
	if($sns_app) $seo_title = $site_name;
	$foot = '';
}
include template($template, $module);
?>