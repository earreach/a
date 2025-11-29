<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MG['biz'] or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
$MG['ad'] or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('extend.lang');
$adurl = $EXT['ad_url'];
$TYPE = $L['ad_type'];
$PTYPE = $L['ad_ptype'];
function ad_price($r) {
	global $PTYPE;
	$prices = array();
	if($r['price'] > 0) $prices['m1'] = $r['price'];
	if($r['setting']) {
		$arr = unserialize($r['setting']);
		foreach($arr as $k=>$v) {
			if(isset($PTYPE[$k]) && dround($v) > 0) $prices[$k] = dround($v);
		}
	}
	return $prices;
}
$menu_id = 2;
if($action == 'add') {
	$pid = isset($pid) ? intval($pid) : 0;
	if($pid) {
		$EXT['ad_buy'] or dheader('?action=index');
		$currency = $EXT['ad_currency'];
		$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
		$p = $db->get_one("SELECT * FROM {$DT_PRE}ad_place WHERE pid=$pid");
		$p or message($L['not_ad_place'], $adurl);
		$typeid = $p['typeid'];
		$price = $p['price'];
		$prices = ad_price($p);
		$months = $prices ? array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 24, 36, 48, 60) : array(1, 2, 3, 6, 12, 24, 36, 48, 60);
		$t = $db->get_one("SELECT MAX(totime) AS totime FROM {$DT_PRE}ad WHERE pid=$pid AND totime>$DT_TIME");
		$fromtime = ($t['totime'] && $typeid < 5) ? $t['totime'] : $DT_TIME + 86400;
		$fromdate = timetodate($fromtime, 3);
		if($submit) {
			(is_date($post['fromtime']) && $post['fromtime'] >= $fromdate) or message($L['pass_ad_from']);
			in_array($month, $months) or message($L['pass_ad_month']);
			if($prices) {
				isset($prices[$ptype]) or message($L['pass_ad_ptype']);
				$amount = $prices[$ptype]*$month;
			} else {
				$ptype = 'm1';
				$amount = 0;
			}
			if($amount) {
				if($currency == 'money') {
					if($amount > $_money) message($L['money_not_enough']);
					if($amount > $DT['quick_pay']) {
						is_payword($_username, $password) or message($L['error_payword']);
					}
				} else {
					if($amount > $_credit) message($L['credit_not_enough']);
				}
			}
			$ad = array();
			$ad['image_src'] = $ad['video_src'] = $ad['code'] = '';
			if($typeid == 1 || $typeid == 7) {
				if(strlen($post['code']) < 10) message($L['pass_ad_code']);
				$ad['code'] = $post['code'];
			} else if($typeid == 2) {
				if(strlen($post['text_name']) < 2) message($L['pass_ad_text_name']);
				if(strlen($post['text_url']) < 10) message($L['pass_ad_text_url']);
				$ad['text_name'] = $post['text_name'];
				$ad['text_url'] = fix_link($post['text_url']);
				$ad['text_title'] = $post['text_title'];
			} else if($typeid == 3 || $typeid == 5) {
				if(strlen($post['image_src']) < 15) message($L['pass_ad_image_src']);
				$ad['image_src'] = $post['image_src'];
				$ad['image_url'] = fix_link($post['image_url']);
				$ad['image_alt'] = $post['image_alt'];
			} else if($typeid == 4) {
				if(strlen($post['video']) < 15 || strpos($post['video'], '.mp4') === false) message($L['pass_ad_video_src']);
				$ad['video_src'] = $post['video'];
				$ad['video_auto'] = $post['video_auto'] ? 1 : 0;
				$ad['video_loop'] = $post['video_loop'] ? 1 : 0;
				$ad['video_url'] = fix_link($post['video_url']);
			} else if($typeid == 6) {
				$post['key_id'] = intval($post['key_id']);
				$post['key_id'] or message($L['pass_ad_infoid']);
				$ad['key_id'] = $post['key_id'];
			}
			if($typeid == 6 || $typeid == 7) {
				$ad['key_moduleid'] = $p['moduleid'];
				$ad['key_catid'] = $post['catid'];
				$ad['key_word'] = trim(strip_tags($post['word']));
			}
			$ad['addtime'] = $ad['edittime'] = $DT_TIME;
			$ad['fromtime'] = datetotime($post['fromtime']);
			$ad['totime'] = datetotime('+'.($month*intval(substr($ptype, 1))).' '.(substr($ptype, 0, 1) == 'd' ? 'day' : 'month')) + ($ad['fromtime'] - $DT_TIME);
			$ad['pid'] = $pid;
			$ad['typeid'] = $typeid;
			$ad['amount'] = $amount;
			$ad['currency'] = $currency;
			$ad['title'] = $post['fromtime'].'('.$_username.')';
			$ad['introduce'] = $L['ad_buy_note'].($prices ? ' '.$prices[$ptype].$unit.'/'.$PTYPE[$ptype] : '').' x '.$month.$PTYPE[$ptype].' '.$L['ad_buy_paid'].$amount.$unit;
			$ad['note'] = $post['note'];
			$ad['status'] = 2;
			$ad['username'] = $_username;
			$post = dhtmlspecialchars($ad);
			if($amount) {
				if($currency == 'money') {
					money_add($_username, -$amount);
					money_record($_username, -$amount, $L['in_site'], 'system', $L['pay_in_site'], $p['name'].$L['ad_buy_title'].$month.$PTYPE[$ptype]);
				} else {
					credit_add($_username, -$amount);
					credit_record($_username, -$amount, 'system', $p['name'].$L['ad_buy_title'], $month.$PTYPE[$ptype]);
				}
			}
			$db->query("INSERT INTO {$DT_PRE}ad ".arr2sql($post, 0));
			$db->query("UPDATE {$DT_PRE}ad_place SET ads=ads+1 WHERE pid=$pid");
			dmsg($L['ad_buy_success'], '?status=2');
		}
		$video = '';
		$head_title = $L['ad_buy_title'];
	} else {
		dheader('?action=list');
	}
} else if($action == 'list') {
	$currency = $EXT['ad_currency'];
	$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
	$condition = "open=1";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}ad_place WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}ad_place WHERE {$condition} ORDER BY listorder DESC,pid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['alt'] = $r['name'];
		$r['name'] = set_style($r['name'], $r['style']);
		$r['adddate'] = timetodate($r['addtime'], 5);
		$r['editdate'] = timetodate($r['edittime'], 5);
		$r['width'] or $r['width'] = '--';
		$r['height'] or $r['height'] = '--';
		$r['typename'] = $TYPE[$r['typeid']];
		$r['prices'] = ad_price($r);
		$lists[] = $r;
	}
	$head_title = $L['ad_buy_title'];
} else {
	$status = isset($status) ? intval($status) : 3;
	in_array($status, array(2, 3)) or $status = 3;
	$condition = "username='$_username' AND status=$status";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}ad WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}ad WHERE {$condition} ORDER BY aid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		if($r['totime'] < $DT_TIME) {
			$r['process'] = $L['status_expired'];
			$r['days'] = 0;
		} else if($r['fromtime'] > $DT_TIME) {
			$r['process'] = $L['status_not_start'];
			$r['days'] = intval(($r['totime'] - $r['fromtime'])/86400);
		} else {
			$r['process'] = $L['status_displaying'];
			$r['days'] = intval(($r['totime'] - $DT_TIME)/86400);
		}
		$lists[] = $r;
	}
	$head_title = $L['ad_list_title'];
}
$nums = array();
for($i = 2; $i < 4; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}ad WHERE username='$_username' AND status=$i");
	$nums[$i] = $r['num'];
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	$head_name = $head_title;
}
include template('ad', $module);
?>