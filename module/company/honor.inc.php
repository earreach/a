<?php 
defined('IN_DESTOON') or exit('Access Denied');
$table = $DT_PRE.'honor';
if($itemid) {
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if(!$item || $item['status'] < 3 || $item['username'] != $username) dheader($MENU[$menuid]['linkurl']);
	require DT_ROOT.'/include/content.class.php';
	extract($item);
	$image = str_replace('.thumb.'.file_ext($thumb), '', $thumb);
	$content = DC::format($content, $DT_PC);
	if(!$DT_BOT) $db->query("UPDATE LOW_PRIORITY {$table} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
	$head_title = $title.$DT['seo_delimiter'].$head_title;
	$head_keywords = $title.','.$COM['company'];
	$head_description = dsubstr(strip_tags($content), 200);
	if($DT_PC) {
		//
	} else {
		$js_item = 1;
	}
} else {
	$url = "file=$file";
	$condition = "username='$username' AND status=3";
	if($kw) {
		$condition .= match_kw('title', $keyword);
		$url .= "&kw=$kw";
		$head_title = $kw.$DT['seo_delimiter'].$head_title;
	}
	$demo_url = userurl($username, $url.'&page={destoon_page}', $domain);
	$pagesize = intval($MENU[$menuid]['pagesize']);
	if(!$pagesize || $pagesize > 100) $pagesize = 10;
	$offset = ($page-1)*$pagesize;
	$items = $db->count($table, $condition, $DT['cache_search']);
	$pages = $DT_PC ? home_pages($items, $page, $pagesize, $demo_url) : mobile_pages($items, $page, $pagesize, $demo_url);
	$lists = array();
	if($items) {
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['linkurl'] = userurl($username, "file=$file&itemid=$r[itemid]", $domain);
			$r['image'] = str_replace('.thumb.'.file_ext($r['thumb']), '', $r['thumb']);
			if($kw) {
				$r['title'] = str_replace($kw, '<span class="f_red">'.$kw.'</span>', $r['title']);
			}
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($DT_PC) {
		//
	} else {
	}
}
include template('honor', $template);
?>