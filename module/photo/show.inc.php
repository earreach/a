<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$could_comment = in_array($moduleid, explode(',', $EXT['comment_module'])) ? 1 : 0;
$modurl = $DT_PC ? $MOD['linkurl'] : $MOD['mobile'];
$itemid or dheader($modurl);
if(!check_group($_groupid, $MOD['group_show'])) include load('403.inc');
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if($item && $item['status'] > 2) {
	if($MOD['show_html'] && $item['open'] == 3 && is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$item['linkurl'])) d301($modurl.$item['linkurl']);
	require DT_ROOT.'/include/content.class.php';
	extract($item);
} else {
	include load('404.inc');
}
$CAT = get_cat($catid);
if(!check_group($_groupid, $CAT['group_show'])) include load('403.inc');
if($open < 3) {
	$_key = $open == 2 ? $password : $answer;
	$str = get_cookie('photo_'.$itemid);
	$pass = $str == md5(md5(DT_IP.$open.$_key.DT_KEY.'PHOTO'));	
	if($_username && $_username == $username) $pass = true;
} else {
	$pass = true;
}
if($page > $items) $page = 1;
$pass or dheader($modurl.'private'.DT_EXT.'?itemid='.$itemid.'&page='.$page);
$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
$content = $t['content'];
$adddate = timetodate($addtime, 3);
$editdate = timetodate($edittime, 3);
$keytags = $tag ? explode(' ', trim($tag)) : array();
$T = array();
$result = $db->query("SELECT itemid,thumb,introduce FROM {$table_item} WHERE item=$itemid ORDER BY listorder ASC,itemid ASC");
while($r = $db->fetch_array($result)) {
	$r['middle'] = str_replace('.thumb.', '.middle.', $r['thumb']);
	$r['big'] = str_replace('.thumb.'.file_ext($r['thumb']), '', $r['thumb']);
	$T[] = $r;
}
if(!$T) include load('404.inc');
$demo_url = $modurl.itemurl($item, '{destoon_page}');
$next_photo = $items > 1 ? next_photo($page, $items, $demo_url) : $linkurl;
$prev_photo = $items > 1 ? prev_photo($page, $items, $demo_url) : $linkurl;
$P = $T[$page-1];
$P['src'] = str_replace('.thumb.'.file_ext($P['thumb']), '', $P['thumb']);
if($DT_PC) {
	if($content) {
		if($MOD['keylink']) $content = DC::keylink($content, $moduleid, $DT_PC);
		if($lazy) $content = DC::lazy($content);
		$content = DC::format($content, $DT_PC);
	}
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	$linkurl = $MOD['linkurl'].$linkurl;
	if($T) {
		$S = side_photo($T, $page, $demo_url);
	} else {
		$S = array();
		$T[0]['thumb'] = DT_SKIN.'spacer.gif';
		$T[0]['introduce'] = $L['no_picture'];
	}
	$user_status = 3;
	$update = '';
	$fee = DC::fee($item['fee'], $MOD['fee_view']);
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
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $linkurl);
} else {
	$description = '';
	$user_status = 3;
	$fee = DC::fee($item['fee'], $MOD['fee_view']);
	if($content) {
		if($MOD['keylink']) $content = DC::keylink($content, $moduleid, $DT_PC);
		if($share_icon) $share_icon = DC::icon($thumb, $content);
		if($user_status == 2) $description = DC::description($content, $MOD['pre_view']);
		DC::format($content, $DT_PC);
	}
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	include DT_ROOT.'/mobile/api/content.inc.php';
	$item['template'] = $CAT['show_template'] = '';
	$update = '';
	$head_title = $head_name = $CAT['catname'];
	$js_item = $js_album = 1;
	$foot = '';
}
if(!$DT_BOT) include DT_ROOT.'/include/update.inc.php';
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
include template($template, $module);
?>