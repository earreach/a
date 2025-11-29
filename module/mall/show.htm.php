<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$MOD['show_html'] || !$itemid) return false;
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if(!$item || $item['status'] < 3) return false;
$could_comment = false;
require_once DT_ROOT.'/include/content.class.php';
extract($item);
$CAT = get_cat($catid);
$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
$content = $_content =  $t['content'];
if($content) {
	if($MOD['keylink']) $content = DC::keylink($content, $moduleid);
	if($lazy) $content = DC::lazy($content);
	$content = DC::format($content, 1);
}
$CP = $MOD['cat_property'] && $CAT['property'];
if($CP) {
	require_once DT_ROOT.'/include/property.func.php';
	$options = property_option($catid);
	$values = property_value($moduleid, $itemid);
}
$sec = get_sec($item);
$jsdate = $stotime ? timetodate($stotime, 'Y,').(timetodate($stotime, 'n')-1).timetodate($stotime, ',j,H,i,s') : '';
$RL = $item['relate_id'] ? get_relate($item) : array();
$P1 = get_nv($n1, $v1);
$P2 = get_nv($n2, $v2);
$P3 = get_nv($n3, $v3);
if($step) {
	@extract(unserialize($step), EXTR_SKIP);
	$mode = 2;
} else {
	$a1 = 1;
	$p1 = $item['price'];
	$a2 = $a3 = $p2 = $p3 = '';
	$mode = $prices ? 1 : 0;
}
$stocks = '';
if($stock) {
	$stocks = json_encode(get_stocks($stock));
	$mode = 3;
}
if($subtext && $sublink) {
	if(strpos($subtitle, $subtext) === false) {
		$subtitle .= '<a href="'.$sublink.'" target="_blank"><span>'.$subtext.'</span></a>';
	} else {
		$subtitle = str_replace($subtext, '<a href="'.$sublink.'" target="_blank"><span>'.$subtext.'</span></a>', $subtitle);
	}
}
$mina = $a1;
if($minamount > $mina && $minamount > 0) $mina = $minamount;
$maxa = $amount;
if($maxamount < $maxa && $maxamount > 0) $maxa = $maxamount;
$unit or $unit = $L['unit'];
$adddate = timetodate($addtime, 3);
$editdate = timetodate($edittime, 3);
$fileurl = $linkurl;
$linkurl = $MOD['linkurl'].$linkurl;
$albums = get_albums($item);
$pics = count($albums);
$pics_width = $pics*70;
$promos = get_promos($username, $moduleid, $itemid);
$fee = DC::fee($item['fee'], $MOD['fee_view']);
$user_status = 4;
$sku_amount = get_amount($item);
if($sku_amount != $amount) $amount = $sku_amount;
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid";
if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $linkurl);
$DT_PC = $GLOBALS['DT_PC'] = 1;
ob_start();
include template($template, $module);
$data = ob_get_contents();
ob_clean();
$filename = DT_ROOT.'/'.$MOD['moduledir'].'/'.$fileurl;
if($DT['pcharset']) $filename = convert($filename, DT_CHARSET, $DT['pcharset']);
file_put($filename, $data);
if($EXT['mobile_enable']) {
	include DT_ROOT.'/include/mobile.htm.php';
	$head_pc = $linkurl;
	$head_title = $head_name = $CAT['catname'];
	$js_item = $js_album = 1;
	$foot = '';
	if($_content) {
		$content = $_content;
		if($MOD['keylink']) $content = DC::keylink($content, $moduleid, 0);
		$content = DC::format($content, 0);
	}
	ob_start();
	include template($template, $module);
	$data = ob_get_contents();
	ob_clean();
	file_put(str_replace(DT_ROOT, DT_ROOT.'/mobile', $filename), $data);
}
return true;
?>