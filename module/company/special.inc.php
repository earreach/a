<?php 
defined('IN_DESTOON') or exit('Access Denied');
$elite = isset($elite) ? intval($elite) : 0;
(isset($orderby) && in_array($orderby, array('dorders', 'dhits', 'dcomments', 'dlikes'))) or $orderby = '';
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if(!$item || $item['status'] < 3 || $item['username'] != $username) dheader($MENU[$menuid]['linkurl']);
	unset($item['template']);
	require DT_ROOT.'/include/content.class.php';
	extract($item);
	$CAT = get_cat($catid);
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t['content'];
	$content = DC::format($content, $DT_PC);
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	$adddate = timetodate($addtime, 3);
	$editdate = timetodate($edittime, 3);
	$linkurl = $MOD['linkurl'].$linkurl;
	$TYPE = array();
	foreach(get_type($module.'-'.$moduleid.'-'.$itemid) as $v) {
		$v['linkurl'] = $modurl.rewrite('type'.DT_EXT.'?tid='.$v['typeid']);
		$TYPE[] = $v;
	}
	$_TP = sort_type($TYPE);
	$typeid = 0;
	$update = '';
	if(!$DT_BOT) include DT_ROOT.'/include/update.inc.php';
	$head_canonical = $linkurl;
	$head_title = $title.$DT['seo_delimiter'].$head_title;
	$head_keywords = $keyword;
	$head_description = $introduce ? $introduce : $title;
	if($DT_PC) {
		//
	} else {
		$member = array();
		$user_status = 3;
		$foot = '';
	}
} else {
	$url = "file=$file";
	$condition = "username='$username' AND status=3";
	if($kw) {
		$condition .= match_kw('keyword', $keyword);
		$url .= "&kw=$kw";
		$head_title = $kw.$DT['seo_delimiter'].$head_title;
	}
	if($elite) $condition .= " AND level>0";
	$demo_url = $action == 'search' ? '' : userurl($username, $url.'&page={destoon_page}', $domain);
	$pagesize = intval($MENU[$menuid]['pagesize']);
	if(!$pagesize || $pagesize > 100) $pagesize = 16;
	$offset = ($page-1)*$pagesize;
	$items = $db->count($table, $condition, $DT['cache_search']);
	$pages = $DT_PC ? home_pages($items, $page, $pagesize, $demo_url) : mobile_pages($items, $page, $pagesize, $demo_url);
	$lists = array();
	if($items) {
		$order = 'itemid DESC';
		if($orderby) $order = substr($orderby, 0, 1) == 'd' ? substr($orderby, 1).' DESC' : substr($orderby, 1).' ASC';
		$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['linkurl'] = $homeurl ? ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$r['linkurl'] : userurl($username, "file=$file&itemid=$r[itemid]", $domain);
			if($kw) {
				$r['title'] = str_replace($kw, '<span class="f_red">'.$kw.'</span>', $r['title']);
				$r['introduce'] = str_replace($kw, '<span class="f_red">'.$kw.'</span>', $r['introduce']);
			}
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($DT_PC) {
		//
	} else {
		$tags = $lists;
	}
}
include template($module, $template);
?>