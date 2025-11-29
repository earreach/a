<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$itemid) return false;
$item = $db->get_one("SELECT * FROM {$DT_PRE}webpage WHERE itemid=$itemid");
if(!$item || $item['islink']) return false;
require_once DT_ROOT.'/include/content.class.php';
$DT_PC = $GLOBALS['DT_PC'] = 1;
$_item = $item['item'];
$cityid = $item['areaid'];
unset($item['item']);
extract($item);
$_content = $content;
$template = $item['template'] ? $item['template'] : 'webpage';
if(in_array($template, array('webpage-agreement', 'webpage-copyright', 'webpage-privacy'))) {
	ob_start();
	include template($template, $module);
	$_content = $content = ob_get_contents();
	ob_clean();
	$template = 'webpage';
}
if($content) $content = DC::format($content, 1);
$head_title = $seo_title ? $seo_title : $title;
$head_keywords = $seo_keywords;
$head_description = $seo_description;
$destoon_task = "moduleid=$moduleid&html=webpage&itemid=$itemid";
if($EXT['mobile_enable']) $head_mobile = DT_MOB.$linkurl;
ob_start();
include template($template, $module);
$data = ob_get_contents();
ob_clean();
file_put(DT_ROOT.'/'.$linkurl, $data);
if($EXT['mobile_enable']) {
	include DT_ROOT.'/include/mobile.htm.php';
	$head_pc = DT_PATH.$linkurl;
	if($content) $content = DC::format($_content, 0);
	$js_item = 1;
	$foot = '';
	ob_start();
	include template($template, $module);
	$data = ob_get_contents();
	ob_clean();
	file_put(DT_ROOT.'/mobile/'.$linkurl, $data);
}
return true;
?>