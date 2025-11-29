<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$MOD['show_html'] || !$itemid) return false;
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
if(!$item || $item['status'] < 3) return false;
require_once DT_ROOT.'/include/content.class.php';
$could_comment = in_array($moduleid, explode(',', $EXT['comment_module'])) ? 1 : 0;
extract($item);
$CAT = get_cat($catid);
$GRP = get_group($gid);
$GRP['managers'] = $GRP['manager'] ? explode('|', $GRP['manager']) : array();
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
$fileurl = $linkurl;
$linkurl = $MOD['linkurl'].$linkurl;
$admin = '';
$member = userinfo($username);
$fee = DC::fee($item['fee'], $MOD['fee_view']);
if($fee) {
	$description = DC::description($content, $MOD['pre_view']);
	$user_status = 4;
} else {
	$user_status = 3;
}
$F = explode('|', $MOD['floor']);
$pages = '';
$pagesize = $MOD['reply_pagesize'];
if($page == 1) {
	$items = $db->count($table_reply, "tid=$itemid AND status=3");
	if($items != $reply) {
		$item['reply'] = $reply = $items;
		$db->query("UPDATE {$table} SET reply=$reply WHERE itemid=$itemid");
	}
} else {
	$items = $reply;
}
$total = max(ceil($items/$pagesize), 1);
if(isset($fid) && isset($num) && $fid > 0) {
	$page = $fid;
	$topage = $fid + $num - 1;
	$total = $topage < $total ? $topage : $total;
}
$template = $item['template'] ? $item['template'] : ($GRP['show_template'] ? $GRP['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
for(; $page <= $total; $page++) {
	$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
	if($EXT['mobile_enable']) $head_mobile = $MOD['mobile'].($page > 1 ? itemurl($item, $page) : $item['linkurl']);
	$filename = $total == 1 ? DT_ROOT.'/'.$MOD['moduledir'].'/'.$fileurl : DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, $page);
	$lists = array();
	if($items) {
		$offset = ($page-1)*$pagesize;
		$pages = pages($items, $page, $pagesize, $MOD['linkurl'].itemurl($item, '{destoon_page}'));
		$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
		$result = $db->query("SELECT * FROM {$table_reply} WHERE tid=$itemid AND status=3 ORDER BY itemid ASC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['fname'] = isset($F[$floor]) ? $F[$floor] : '';
			$r['floor'] = ++$floor;
			if($r['fid'] != $r['floor']) $db->query("UPDATE {$table_reply} SET fid=$r[floor] WHERE itemid=$r[itemid]");
			$lists[] = $r;
		}
		$lists = list_user($lists, 'validate,gradeid');	
	}
	$seo_file = 'show';
	include DT_ROOT.'/include/seo.inc.php';
	$DT_PC = $GLOBALS['DT_PC'] = 1;
	ob_start();
	include template($template, $module);
	$data = ob_get_contents();
	ob_clean();
	if($DT['pcharset']) $filename = convert($filename, DT_CHARSET, $DT['pcharset']);
	file_put($filename, $data);
	if($page == 1 && $total > 1) {
		$indexname = DT_ROOT.'/'.$MOD['moduledir'].'/'.itemurl($item, 0);
		if($DT['pcharset']) $indexname = convert($indexname, DT_CHARSET, $DT['pcharset']);
		file_copy($filename, $indexname);
	}
	if($EXT['mobile_enable']) {
		include DT_ROOT.'/include/mobile.htm.php';		
		$head_pc = str_replace($MOD['mobile'], $MOD['linkurl'], $head_mobile);
		$head_title = $head_name = $GRP['title'].$MOD['seo_name'];
		$js_item = 1;
		$foot = '';
		if($total > 1) $pages = mobile_pages($total, $page, 1, $MOD['mobile'].itemurl($item, '{destoon_page}'));
		if($_content) {
			$content = $_content;
			if($MOD['keylink']) $content = DC::keylink($content, $moduleid, 0);
			$content = DC::format($content, 0);
		}
		$filename = str_replace(DT_ROOT, DT_ROOT.'/mobile', $filename);
		ob_start();
		include template($template, $module);
		$data = ob_get_contents();
		ob_clean();
		file_put($filename, $data);
		if($page == 1 && $total > 1) file_copy($filename, str_replace(DT_ROOT, DT_ROOT.'/mobile', $indexname));
	}
}
return true;
?>