<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$UG['space']) {
	userclean($username);
	$head_title = $head_name = $L['not_company'];
	if($DT_BOT) dhttp(404, $DT_BOT);
	$foot = 'channel';
	include template('com-notfound', 'message');
	exit;
}
$member = $COM;
$CSS[] = 'space';
$JS[] = 'space';
$passport = $member['passport'] ? $member['passport'] : $username;
$head_title = $passport.$L['space_title'];
$member['cover'] = $member['cover'] ? $member['cover'] : DT_STATIC.'image/cover.png';
$typeid = isset($typeid) ? intval($typeid) : 0;

$mids = explode(',', $UG['moduleids']);
$sids = explode(',', $UG['spaceids']);
$MENU = array();
foreach($sids as $v) {
	$MENU[$v] = $MODULE[$v]['name'];
}
if(isset($MENU[20])) $MENU = array(20 => $MODULE[20]['name']) + $MENU;
$MENU = array(2 => $L['space_about']) + $MENU;
if($mid && isset($MENU[$mid])) {
	$head_title = $MENU[$mid].$DT['seo_delimiter'].$head_title;
	$demo_url = userurl($username, 'file=space&mid='.$mid.'&page={destoon_page}', $domain);
} else {
	$mid = isset($MENU[20]) ? 20 : $sids[0];
	$demo_url = userurl($username, 'file=space&page={destoon_page}', $domain);
}
$table = get_table($mid);
$moduleid = $mid;
$module = $MODULE[$mid]['module'];
$MOD = cache_read('module-'.$mid.'.php');
$name = $MENU[$mid];
$tag = 'list-space';
$sub = $pages = '';
$tags = $SUBMENU = array();
$condition = "username='$username' AND status=3";
$order = 'addtime DESC';
$foot = '';
switch($module) {
	case 'member':
		$SUBMENU = $DT['follow'] ? $L['space_menu_1'] : $L['space_menu_0'];
		if($typeid == 3) {
			$table = $DT_PRE.'follow';
			$condition = "fuserid=$userid";
			if($keyword) $condition .= match_kw('passport', $keyword);
			$keyword = '';
			$sub = 'fans';
		} else if($typeid == 2) {
			$table = $DT_PRE.'follow';
			$condition = "userid=$userid";
			if($keyword) $condition .= match_kw('fpassport', $keyword);
			$keyword = '';
			$sub = 'follow';
		} else if($typeid == 1) {
			$table = $DT_PRE.'comment';
			if($keyword) $condition .= match_kw('content', $keyword);
			$keyword = '';
			$sub = 'comment';
		} else {
			$typeid = 0;
			require DT_ROOT.'/include/content.class.php';
			$table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $DT_PRE.'company_data');
			$item = $db->get_one("SELECT * FROM {$table} WHERE userid=$userid");
			$content = $item ? $item['content'] : '';
			$content = DC::format($content, $DT_PC);
			$tags[0] = $content;
			$keyword = $table = '';
			$sub = 'about';
		}
		$name = $SUBMENU[$typeid];
		$head_title = $name.$DT['seo_delimiter'].$head_title;
	break;
	case 'club':
		$SUBMENU = $L['space_menu_2'];
		if($typeid == 2) {
			require DT_ROOT.'/module/club/global.func.php';
			$table = str_replace($module, $module.'_fans', $table);
			$table_group = str_replace('_fans', '_group', $table);
			$sub = 'group';
		} else if($typeid == 1) {
			$table = str_replace($module, $module.'_reply', $table);
			if($keyword) $condition .= match_kw('content', $keyword);
			$keyword = '';
			$sub = 'reply';
		} else {
			$typeid = 0;
		}
		$name = $SUBMENU[$typeid];
		$head_title = $name.$DT['seo_delimiter'].$head_title;
	break;
	case 'know':
		$SUBMENU = $L['space_menu_3'];
		if($typeid == 2) {
			$condition = "ask='$username' AND status=3 AND process=3";
			$order = 'updatetime DESC';
		} else if($typeid == 1) {
			$table = str_replace($module, $module.'_answer', $table);
			if($keyword) $condition .= match_kw('content', $keyword);
			$keyword = '';
			$sub = 'answer';
		} else {
			$typeid = 0;
		}
		$condition .= " AND hidden=0";
		$name = $SUBMENU[$typeid];
		$head_title = $name.$DT['seo_delimiter'].$head_title;
	break;
	case 'moment':
		require DT_ROOT.'/module/moment/global.func.php';
		$followed = followed($userid);
		$friended = friended($userid);
		if($followed && $friended) {
			$condition .= " AND open>0";
		} else if($followed) {
			$condition .= " AND open IN (1,2)";
		} else if($friended) {
			$condition .= " AND open IN (1,3)";
		} else {
			$condition .= " AND open=1";
		}
		$CSS[] = $module;
		$JS[] = $module;
		$JS[] = 'player';
		$comment_url = $EXT['comment_url'];
		$showpage = 0;
		$tag = 'list-'.$module;
	break;
	case 'resume':
		$condition .= " AND open=3";
	break;
}
if($table) {
	if($keyword) $condition .= match_kw('keyword', $keyword);
	$items = $db->count($table, $condition, $DT['cache_search']);
	$pages = $DT_PC ? home_pages($items, $page, $pagesize, $demo_url) : mobile_pages($items, $page, $pagesize, $demo_url);
	if($items) {
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
		while($r = $db->fetch_array($result)) {
			if(isset($r['linkurl'])) {
				if(isset($r['islink'])) {
					if(!$r['islink']) $r['linkurl'] = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$r['linkurl'];
				} else {
					$r['islink'] = 0;
					$r['linkurl'] = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$r['linkurl'];
				}
			}
			if($module == 'moment' && !$DT_PC) $r['introduce'] = parse_mob($r['introduce']);
			$tags[] = $r;
		}
	}
}
include template($UG['template_space'] ? $UG['template_space'] : 'space', 'company');
?>