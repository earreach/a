<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$MOD['show_html'] || !$itemid) return false;
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if(!$item || $item['status'] < 3) return false;
require_once DT_ROOT.'/include/content.class.php';
$could_comment = in_array($moduleid, explode(',', $EXT['comment_module'])) ? 1 : 0;
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
$adddate = timetodate($addtime, 3);
$editdate = timetodate($edittime, 3);
$keytags = $tag ? explode(' ', trim($tag)) : array();
$fileurl = $linkurl;
$linkurl = $MOD['linkurl'].$linkurl;
$fee = DC::fee($item['fee'], $MOD['fee_view']);
if($fee) {
	$description = '';
	$user_status = 4;
} else {
	$user_status = 3;
}
$pass = $open == 3 ? true : false;
$T = array();
$result = $db->query("SELECT itemid,thumb,introduce FROM {$table_item} WHERE item=$itemid ORDER BY listorder ASC,itemid ASC");
while($r = $db->fetch_array($result)) {
	$r['middle'] = str_replace('.thumb.', '.middle.', $r['thumb']);
	$r['big'] = str_replace('.thumb.'.file_ext($r['thumb']), '', $r['thumb']);
	$T[] = $r;
}
if(!$T) return false;
$demo_url = $MOD['linkurl'].itemurl($item, '{destoon_page}');
$total = $items = count($T);
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
$total = $items;
for(; $page <= $total; $page++) {
	$next_photo = $items > 1 ? next_photo($page, $items, $demo_url) : $linkurl;
	$prev_photo = $items > 1 ? prev_photo($page, $items, $demo_url) : $linkurl;
	if($T) {
		$S = side_photo($T, $page, $demo_url);
	} else {
		$S = array();
		$T[0]['thumb'] = DT_SKIN.'spacer.gif';
		$T[0]['introduce'] = $L['no_picture'];
	}
	$P = $T[$page-1];
	$P['src'] = str_replace('.thumb.'.file_ext($P['thumb']), '', $P['thumb']);
	$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
	if($EXT['mobile_enable']) {
		$head_mobile = $MOD['mobile'].($page > 1 ? itemurl($item, $page) : $item['linkurl']);
		$head_pc = str_replace($MOD['mobile'], $MOD['linkurl'], $head_mobile);
	}
	$filename = DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, $page);
	$DT_PC = $GLOBALS['DT_PC'] = 1;
	if($pass) {
		ob_start();
		include template($template, $module);
		$data = ob_get_contents();
		ob_clean();
	} else {
		$data = '<meta http-equiv="refresh" content="0;url='.$MOD['linkurl'].'private'.DT_EXT.'?itemid='.$itemid.'&page='.$page.'"/>';
	}
	if($DT['pcharset']) $filename = convert($filename, DT_CHARSET, $DT['pcharset']);
	file_put($filename, $data);
	if($page == 1) {
		$indexname = DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, 0);
		if($DT['pcharset']) $indexname = convert($indexname, DT_CHARSET, $DT['pcharset']);
		file_copy($filename, $indexname);
	}
	if($EXT['mobile_enable']) {
		include DT_ROOT.'/include/mobile.htm.php';
		$head_pc = $linkurl;
		$js_item = $js_album = 1;
		$head_title = $head_name = $CAT['catname'];
		$foot = '';
		if($total > 1) $pages = mobile_pages($total, $page, 1, $MOD['mobile'].itemurl($item, '{destoon_page}'));
		if($_content) {
			$content = $_content;
			if($MOD['keylink']) $content = DC::keylink($content, $moduleid, 0);
			$content = DC::format($content, 0);
		}
		$filename = str_replace(DT_ROOT, DT_ROOT.'/mobile', $filename);
		if($pass) {
			ob_start();
			include template('show', $module);
			$data = ob_get_contents();
			ob_clean();
		} else {
			$data = '<meta http-equiv="refresh" content="0;url='.$MOD['mobile'].'private'.DT_EXT.'?itemid='.$itemid.'&page='.$page.'"/>';
		}
		file_put($filename, $data);
		if($page == 1 && $total > 1) file_copy($filename, str_replace(DT_ROOT, DT_ROOT.'/mobile', $indexname));
	}
}
return true;
?>