<?php 
defined('IN_DESTOON') or exit('Access Denied');
$itemid or dheader('product.php');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(!check_group($_groupid, $MOD['group_show_price'])) include load('403.inc');
require DT_ROOT.'/include/post.func.php';
$P = $db->get_one("SELECT * FROM {$table_product} WHERE itemid=$itemid");
$P or dheader('product.php');
$could_add = check_group($_groupid, $MOD['group_add_price']);
$MARKET = $P['market'] ? explode('|', $L['price_market'].'|'.$P['market']) : array();
if($could_add) {
	$need_captcha = $MOD['captcha_add'] == 2 ? $MG['captcha'] : $MOD['captcha_add'];
	if($submit) {
		$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
		$today = $DT_TODAY - 86400;
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_price} WHERE $sql AND pid=$itemid AND addtime>$today");
		$limit_used = $r['num'];
		if($limit_used > 0) dalert($L['msg_added']);
		$msg = captcha($captcha, $need_captcha, true);
		if($msg) dalert($msg);
		$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_add'];
		$post['addtime'] = 0;
		$post['status'] = get_status(3, $need_check);
		$post['pid'] = $itemid;
		$post['username'] = $_username;
		if($_userid) $post['company'] = $_company;
		require DT_ROOT.'/module/'.$module.'/price.class.php';
		$do = new price;
		if($do->pass($post)) {
			$do->add($post);
			$msg = $post['status'] == 2 ? $L['msg_check'] : $L['msg_ok'];
			dalert($msg, '', 'parent.window.location=parent.window.location;');
		} else {
			dalert($do->errmsg, '', ($need_captcha ? reload_captcha() : '').($need_question ? reload_question() : ''));
		}
	}
}
$dorder  = array('addtime DESC', 'price DESC', 'price ASC', 'addtime DESC', 'addtime ASC');
isset($order) && isset($dorder[$order]) or $order = 0;
$market = isset($market) ? intval($market) : 0;
$areaid = isset($areaid) ? intval($areaid) : 0;
(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
$fromtime = $fromdate ? datetotime($fromdate) : 0;
(isset($todate) && is_time($todate)) or $todate = '';
$totime = $todate ? datetotime($todate) : 0;
$items = 0;
$tags = array();
$showpage = 1;
$condition = "status=3 AND pid=$itemid";
$_condition = $condition;
if($keyword) $condition .= match_kw('company', $keyword);
if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
if($market) $condition .= " AND market=$market";
if($fromtime) $condition .= " AND edittime>=$fromtime";
if($totime) $condition .= " AND edittime<=$totime";
$items = $db->count($table_price, $condition, $DT['cache_search']);
$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
$tags = array();
$result = $db->query("SELECT * FROM {$table_price} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
while($r = $db->fetch_array($result)) {
	$tags[] = $r;
}
if(!$DT_BOT && $page == 1) {
	$update = 'hits=hits+1';
	if($_condition == $condition && $items != $P['item']) $update .= ",item=$items";
	$db->query("UPDATE LOW_PRIORITY {$table_product} SET $update WHERE itemid=$itemid", 'UNBUFFERED');
}
if($totime) {
	$_totime = $totime;
} else {
	$r = $db->get_one("SELECT MAX(addtime) as maxtime FROM {$table_price} WHERE status=3 AND pid=$itemid");
	$_totime = $r['maxtime'] ? $r['maxtime'] : $P['edittime'];
}
$_fromtime = datetotime(timetodate($_totime, 3)) - 15*86400;
$condition = "status=3 AND pid=$itemid AND addtime>=$_fromtime AND addtime<=$_totime";
if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
$lists = $dates = array();
$result = $db->query("SELECT * FROM {$table_price} WHERE {$condition} ORDER BY addtime ASC", $areaid ? '' : 'CACHE');
while($r = $db->fetch_array($result)) {
	$r['date'] = timetodate($r['addtime'], 3);
	$dates[$r['date']] = 0;
	$lists[] = $r;
}
$chart_title = $P['title'].$L['price_chart'];
$name = $areaid ? $ARE['areaname'] : $L['allcity'];
$xd = $yd = $sd = '';
if(count($dates) > 1) {
	foreach($dates as $k=>$v) {
		$price = get_all($k, $lists);
		$xd .= ($xd == '' ? '' : ',')."'".$k."'";
		$yd .= ($yd == '' ? '' : ',')."'".$price."'";
	}
	$ld = "'".$name."'";
	$sd = "{name:'".$name."',type:'line',data: [".$yd."]}";
	if($MARKET) {
		$i = 0;
		foreach($MARKET as $kk=>$vv) {
			if($kk) {
				$yd = '';
				foreach($dates as $k=>$v) {
					$price = get_mkt($k, $kk, $lists);
					$yd .= ($yd == '' ? '' : ',')."'".$price."'";
				}
				$ld .= ",'".$vv."'";
				$sd .= ",{name:'".$vv."',type:'line',data: [".$yd."]}";
			}
		}
	}
}
if($P['seo_title']) {
	$head_title = $P['seo_title'];
} else {
	$head_title = $P['title'].$L['price_title'];
	if($P['v1']) $head_title = $head_title.' '.$P['v1'];
	if($P['v2']) $head_title = $head_title.' '.$P['v2'];
	if($P['v3']) $head_title = $head_title.' '.$P['v3'];
}
$head_title = $head_title.$DT['seo_delimiter'].$MOD['name'];
$head_keywords = $P['seo_keywords'];
$head_description = $P['seo_description'];
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	if($job == 'ajax') {
		if($tags) include template('list-quote-price', 'tag');
		exit;
	}
	$js_load = $MOD['mobile'].'price'.DT_EXT.'?itemid='.$itemid.'&job=ajax';
	$head_name = $L['price_title'];
	if($sns_app) $seo_title = $MOD['name'];
	$EXT['mobile_ajax'] = 0;
}
include template($MOD['template_price'] ? $MOD['template_price'] : 'price', $module);
?>