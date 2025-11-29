<?php 
defined('IN_DESTOON') or exit('Access Denied');
$TYPE = get_type($module.'-'.$moduleid.'-'.$userid);
$_TP = sort_type($TYPE);
$elite = isset($elite) ? intval($elite) : 0;
(isset($orderby) && in_array($orderby, array('dcomments', 'dorders', 'dprice', 'aprice'))) or $orderby = '';
$head_name = $head_title;
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
	$sec = get_sec($item);
	$jsdate = $stotime ? timetodate($stotime, 'Y,').(timetodate($stotime, 'n')-1).timetodate($stotime, ',j,H,i,s') : '';
	$RL = $relate_id ? get_relate($item) : array();
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
	$linkurl = $MOD['linkurl'].$linkurl;
	$albums = get_albums($item);
	$pics = count($albums);
	$pics_width = $pics*70;
	$promos = get_promos($username);
	$album_js = 1;
	$typeid = $mycatid;
	$update = '';
	$sku_amount = get_amount($item);
	if($sku_amount != $amount) {
		$amount = $sku_amount;
		$update .= ",amount=$amount";
	}
	if(!$DT_BOT) include DT_ROOT.'/include/update.inc.php';
	$head_canonical = $linkurl;
	$head_title = $title.$DT['seo_delimiter'].$head_title;
	$head_keywords = $keyword;
	$head_description = $introduce ? $introduce : $title;
	if($DT_PC) {
		//
	} else {
		$member = array();
		$fee = DC::fee($item['fee'], $MOD['fee_view']);
		include DT_ROOT.'/mobile/api/contact.inc.php';
		$js_item = $js_album = 1;
		$foot = '';
	}
} else {
	$typeid = isset($typeid) ? intval($typeid) : 0;
	$view = isset($view) ? 1 : 0;
	$url = "file=$file";
	$condition = "username='$username' AND status=3";
	if($typeid) {
		if($TYPE[$typeid]['parentid']) {
			$condition .= " AND mycatid='$typeid'";
		} else {
			$cids = $typeid.',';
			foreach($TYPE as $k=>$v) {
				if($v['parentid'] == $typeid) $cids .= $k.',';
			}
			$cids = substr($cids, 0, -1);
			$condition .= " AND mycatid IN ($cids)";
		}
		$url .= "&typeid=$typeid";
		$head_title = $TYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
	}
	if($kw) {
		$condition .= match_kw('keyword', $keyword);
		$url .= "&kw=$kw";
		$head_title = $kw.$DT['seo_delimiter'].$head_title;
	}
	if($view) {
		$url .= "&view=$view";
	}
	if($elite) $condition .= " AND elite>0";
	$demo_url = $action == 'search' ? '' : userurl($username, $url.'&page={destoon_page}', $domain);
	$pagesize = intval($MENU[$menuid]['pagesize']);
	if(!$pagesize || $pagesize > 100) $pagesize = 16;
	if($view) $pagesize = ceil($pagesize/2);
	$offset = ($page-1)*$pagesize;
	$items = $db->count($table, $condition, $DT['cache_search']);
	$pages = $DT_PC ? home_pages($items, $page, $pagesize, $demo_url) : mobile_pages($items, $page, $pagesize, $demo_url);
	$lists = array();
	if($items) {
		$order = 'edittime DESC';
		if($orderby) $order = substr($orderby, 0, 1) == 'd' ? substr($orderby, 1).' DESC' : substr($orderby, 1).' ASC';
		$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}", $DT['cache_search'] && $page <= $DT['cache_page'] ? 'CACHE' : '', $DT['cache_search']);
		while($r = $db->fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			if($kw) $r['title'] = str_replace($kw, '<span class="f_red">'.$kw.'</span>', $r['title']);;
			$r['linkurl'] = $homeurl ? ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$r['linkurl'] : userurl($username, "file=$file&itemid=$r[itemid]", $domain);
			$lists[] = $r;
		}
		$db->free_result($result);
	}
	if($DT_PC) {
		//
	} else {
		$tags = $lists;
		if($typeid) $head_name = $TYPE[$typeid]['typename'];
	}
}
include template($module, $template);
?>