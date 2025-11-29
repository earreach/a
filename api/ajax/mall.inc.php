<?php
defined('IN_DESTOON') or exit('Access Denied');
$module == 'mall' or exit;
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($job == 'comment') {
	$itemid or exit;
	$STARS = $L['star_type'];
	$star = isset($star) ? intval($star) : 0;
	in_array($star, array(0, 1, 2, 3, 4, 5)) or $star = 0;
	$thumb = (isset($thumb) && $thumb) ? 1 : 0;
	$video = (isset($video) && $video) ? 1 : 0;
	$lists = array();
	$pages = '';
	$condition = "mallid=$itemid";
	$condition .= $star ? " AND seller_star=$star" : " AND seller_star>0";
	if($thumb) $condition .= " AND seller_thumbs<>''";
	if($video) $condition .= " AND seller_video<>''";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}mall_comment_{$moduleid} WHERE {$condition}");
	$items = $r['num'];
	if($sum && $items != $sum && $condition == "mallid=$itemid AND seller_star>0") $db->query("UPDATE {$DT_PRE}mall_{$moduleid} SET comments=$items WHERE itemid=$itemid");
	$pages = $DT_PC ? pages($items, $page, $pagesize, '#comment" onclick="javascript:comment_load(\'{destoon_page}&star='.$star.'&thumb='.$thumb.'&video='.$video.'\');') : mobile_pages($items, $page, $pagesize, '#comment" onclick="javascript:comment_load(\'{destoon_page}&star='.$star.'&thumb='.$thumb.'&video='.$video.'\');');
	$tmp = explode('<input type="text"', $pages);
	$pages = $tmp[0];
	$result = $db->query("SELECT * FROM {$DT_PRE}mall_comment_{$moduleid} WHERE {$condition} ORDER BY seller_ctime DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['thumbs'] = $r['seller_thumbs'] ? explode('|', $r['seller_thumbs']) : array();
		$r['video'] = $r['seller_video'] ? $r['seller_video'] : '';
		$r['addtime'] = $r['seller_ctime'];
		$r['replytime'] = $r['buyer_ctime'] ? $r['buyer_ctime'] : '';
		$lists[] = $r;
	}
	$stat = $r = $db->get_one("SELECT * FROM {$DT_PRE}mall_stat_{$moduleid} WHERE mallid=$itemid");
	if($stat && $stat['scomment']) {
		$stat['pc1'] = dround($stat['s1']*100/$stat['scomment'], 2, true).'%';
		$stat['pc2'] = dround($stat['s2']*100/$stat['scomment'], 2, true).'%';
		$stat['pc3'] = dround($stat['s3']*100/$stat['scomment'], 2, true).'%';
		$stat['pc4'] = dround($stat['s4']*100/$stat['scomment'], 2, true).'%';
		$stat['pc5'] = dround($stat['s5']*100/$stat['scomment'], 2, true).'%';
	} else {
		$stat['s1'] = $stat['s2'] = $stat['s3'] = $stat['s4'] = $stat['s5'] = 0;
		$stat['pc1'] = $stat['pc2'] = $stat['pc3'] = $stat['pc4'] = $stat['pc5'] = '0%';
	}
	include template('comment', $module);
} else if($job == 'cart') {
	$_userid or exit('-5');
	$itemid or exit('-1');
	require DT_ROOT.'/module/member/cart.class.php';
	$do = new cart();
	$cart = $do->get();
	$max_cart = $MOD['max_cart'];
	$s1 = isset($s1) ? intval($s1) : 0;
	$s2 = isset($s2) ? intval($s2) : 0;
	$s3 = isset($s3) ? intval($s3) : 0;
	$a = isset($a) ? intval($a) : 1;
	echo $do->add($cart, $moduleid, $itemid, $s1, $s2, $s3, $a);
}
?>