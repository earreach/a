<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/content.class.php';
$could_comment = in_array($moduleid, explode(',', $EXT['comment_module'])) ? 1 : 0;
$modurl = $DT_PC ? $MOD['linkurl'] : $MOD['mobile'];
$itemid or dheader($modurl);
if(!check_group($_groupid, $MOD['group_show'])) include load('403.inc');
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if($item && $item['status'] > 2) {
	if($MOD['show_html'] && is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$item['linkurl'])) d301($modurl.$item['linkurl']);
	extract($item);
} else {
	include load('404.inc');
}
$CAT = get_cat($catid);
if(!check_group($_groupid, $CAT['group_show'])) include load('403.inc');
$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
$content = $t['content'];
if($content) {
	if($MOD['keylink']) $content = DC::keylink($content, $moduleid, $DT_PC);
	if($lazy && $DT_PC) $content = DC::lazy($content);
	$content = DC::format($content, $DT_PC);
}
$CP = $MOD['cat_property'] && $CAT['property'];
if($CP) {
	require DT_ROOT.'/include/property.func.php';
	$options = property_option($catid);
	$values = property_value($moduleid, $itemid);
}
$adddate = timetodate($addtime, 3);
$editdate = timetodate($edittime, 3);
$keytags = $tag ? explode(' ', trim($tag)) : array();
$update = '';
$member = array();
$fee = DC::fee($item['fee'], $MOD['fee_view']);
if($DT_PC) {
	if($fee) {
		$user_status = 4;
		$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
		$description = '';
	} else {
		$user_status = 3;
	}
	if($user_status != 3 && $_username && $item['username'] == $_username) {
		$user_status = 3;
		$destoon_task = '';
	}
	$linkurl = $MOD['linkurl'].$linkurl;
	$maincat = get_maincat(0, $moduleid);
	if($width > 872) {
		$height = $width > $height ? intval(872*$height/$width) : intval(872*$width/$height);
		$width = 872;
	}
	$player = DC::player($video, $width, $height, $MOD['autostart'], 0, ($poster ? ' poster="'.$poster.'"' : ''));
	$audio = substr($player, 0, 6) == '<audio' ? 1 : 0;
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $linkurl);
} else {
	$user_status = 3;
	include DT_ROOT.'/mobile/api/content.inc.php';
	$player = DC::player($video, '100%', '100%', $MOD['autostart'], 0, ($poster ? ' poster="'.$poster.'"' : '').' playsinline -webkit-playsinline webkit-playsinline');
	$audio = substr($player, 0, 6) == '<audio' ? 1 : 0;
	if($share_icon) $share_icon = DC::icon($thumb, $content);
	$head_title = $head_name = $CAT['catname'];
	$js_item = 1;
	$foot = '';
}
if(!$DT_BOT) include DT_ROOT.'/include/update.inc.php';
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
include template($template, $module);
?>