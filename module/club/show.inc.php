<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$could_comment = in_array($moduleid, explode(',', $EXT['comment_module'])) ? 1 : 0;
if($DT_PC) {
	$itemid or dheader($MOD['linkurl']);
	if(!check_group($_groupid, $MOD['group_show'])) include load('403.inc');
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if($item && $item['status'] > 2) {
		if($MOD['show_html'] && is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$item['linkurl'])) d301($MOD['linkurl'].$item['linkurl']);
		require DT_ROOT.'/include/content.class.php';
		extract($item);
	} else {
		include load('404.inc');
	}
	$CAT = get_cat($catid);
	if(!check_group($_groupid, $CAT['group_show'])) include load('403.inc');
	$GRP = get_group($gid);
	$GRP['managers'] = $GRP['manager'] ? explode('|', $GRP['manager']) : array();
	if($GRP['show_type'] && !is_fans($GRP)) {
		$action = 'show';
		$head_title = lang('message->without_permission');
		exit(include template('nofans', $module));
	}
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t['content'];
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
	$adddate = timetodate($addtime, 3);
	$editdate = timetodate($edittime, 3);
	$linkurl = $MOD['linkurl'].$linkurl;
	$admin = is_admin($GRP);
	$member = userinfo($username);
	$update = '';
	$fee = DC::fee($item['fee'], $MOD['fee_view']);
	if($fee) {
		$user_status = 4;
		$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid&page=$page";
		$description = DC::description($content, $MOD['pre_view']);
	} else {
		$user_status = 3;
	}
	if($user_status != 3 && $_username && $item['username'] == $_username) {
		$user_status = 3;
		$destoon_task = '';
	}
	$F = explode('|', $MOD['floor']);
	$lists = array();
	$pages = '';
	if($MOD['reply_pagesize']) {
		$pagesize = $MOD['reply_pagesize'];
		$offset = ($page-1)*$pagesize;
	}
	if($page == 1) {
		$items = $db->count($table_reply, "tid=$itemid AND status=3");
		if($items != $reply) $update .= ",reply='$items'";
		if($GRP['areaid'] != $item['areaid']) $update .= ",areaid='$GRP[areaid]'";
		if($GRP['catid'] != $item['catid']) $update .= ",catid='$GRP[catid]'";
	} else {
		$items = $reply;
	}
	if($items > 0) {
		$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
		$pages = pages($items, $page, $pagesize, $MOD['linkurl'].itemurl($item, '{destoon_page}'));
		$result = $db->query("SELECT * FROM {$table_reply} WHERE tid=$itemid AND status=3 ORDER BY itemid ASC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['fname'] = isset($F[$floor]) ? $F[$floor] : '';
			$r['floor'] = ++$floor;
			if($r['fid'] != $r['floor']) $db->query("UPDATE {$table_reply} SET fid=$r[floor] WHERE itemid=$r[itemid]");
			$lists[] = $r;
		}
		$lists = list_user($lists, 'validate,gradeid');	
	}
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $linkurl);
} else {
	$itemid or dheader($MOD['mobile']);
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	($item && $item['status'] > 2) or message($L['msg_not_exist']);
	require DT_ROOT.'/include/content.class.php';
	extract($item);
	$CAT = get_cat($catid);
	if(!check_group($_groupid, $MOD['group_show']) || !check_group($_groupid, $CAT['group_show'])) message($L['msg_no_right']);
	$GRP = get_group($gid);
	$GRP['managers'] = $GRP['manager'] ? explode('|', $GRP['manager']) : array();
	if($GRP['show_type'] && !is_fans($GRP)) {
		$action = 'show';
		$head_title = lang('message->without_permission');
		exit(include template('nofans', $module));
	}
	$member = userinfo($username);
	$fee = DC::fee($item['fee'], $MOD['fee_view']);
	include DT_ROOT.'/mobile/api/content.inc.php';
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t['content'];
	if($content) {
		if($MOD['keylink']) $content = DC::keylink($content, $moduleid, $DT_PC);
		if($share_icon) $share_icon = DC::icon($thumb, $content);
		$content = DC::format($content, 0);
	}
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	$editdate = timetodate($edittime, 5);
	$F = explode('|', $MOD['floor']);
	$lists = array();
	$pages = '';
	if($MOD['reply_pagesize']) {
		$pagesize = $MOD['reply_pagesize'];
		$offset = ($page-1)*$pagesize;
	}
	if($page == 1) {
		$items = $db->count($table_reply, "tid=$itemid AND status=3");
		if($items != $reply) $update .= ",reply='$items'";
		if($GRP['areaid'] != $item['areaid']) $update .= ",areaid='$GRP[areaid]'";
	} else {
		$items = $reply;
	}
	if($items > 0) {
		$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
		$pages = mobile_pages($items, $page, $pagesize, $MOD['mobile'].itemurl($item, '{destoon_page}'));
		$result = $db->query("SELECT * FROM {$table_reply} WHERE tid=$itemid AND status=3 ORDER BY itemid ASC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['fname'] = isset($F[$floor]) ? $F[$floor] : '';
			$r['floor'] = ++$floor;
			if($r['fid'] != $r['floor']) $db->query("UPDATE {$table_reply} SET fid=$r[floor] WHERE itemid=$r[itemid]");
			$lists[] = $r;
		}
		$lists = list_user($lists, 'validate,gradeid');	
	}
	$update = '';
	$head_title = $head_name = $GRP['title'].$MOD['seo_name'];
	$js_item = 1;
	$foot = '';
}
if(!$DT_BOT) include DT_ROOT.'/include/update.inc.php';
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($GRP['show_template'] ? $GRP['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
include template($template, $module);
?>