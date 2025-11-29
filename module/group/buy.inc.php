<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/module/member/global.func.php';
require DT_ROOT.'/include/post.func.php';
include load('misc.lang');
include load('member.lang');
include load('order.lang');
if($action == 'result') {
	$payurl = isset($auth) ? decrypt($auth, DT_KEY.'GURL') : '';
	$payurl = ($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'deal'.DT_EXT.'?'.($payurl ? $payurl : 'action=order');
	$code = isset($code) ? intval($code) : 0;
	$url = gourl('?mid='.$moduleid.'&itemid='.$itemid);
} else {
	$itemid or dheader('buy'.DT_EXT.'?action=result&code=10');
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if($item) {
		if(!check_name($item['username'])) dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&code=11');
		if($item['username'] == $_username) dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&code=12');
		if($item['status'] != 3) dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&code=13');
		if($item['process'] == 2) dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&code=14');
		if($item['price'] < 0.01) dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&code=15');
		$item['mobile'] = $MOD['mobile'].$item['linkurl'];
		$item['linkurl'] = $MOD['linkurl'].$item['linkurl'];
		$t = $item;
	} else {
		dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&code=10');
	}
	if($submit) {
		if($item['logistic']) {
			$aid = isset($aid) ? intval($aid) : 0;
			$aid > 0 or message($L['msg_buy_addr']);
			$addr = get_address($_username, $aid);
			($addr && $addr['truename'] && is_mobile($addr['mobile']) && $addr['address']) or message($L['msg_buy_addrerr']);
			$addr = daddslashes($addr);
			$buyer_address = $addr['address'];
			$buyer_postcode = $addr['postcode'];
			$buyer_name = $addr['truename'];
			$buyer_mobile = dhtmlspecialchars($addr['mobile']);
		} else {
			is_mobile($mobile) or message($L['msg_type_mobile']);
			$buyer_mobile = $mobile;
			$buyer_address = $buyer_postcode = $buyer_name = '';
		}
		$number = intval($number);
		if($number < 1) $number = 1;
		$amount = $number*$item['price'];
		$note = dhtmlspecialchars($note);
		$title = addslashes($item['title']);
		$S = userinfo($t['username']);
		$shop = addslashes($S['shop'] ? $S['shop'] : $S['company']);
		$db->query("INSERT INTO {$table_order} (gid,buyer,seller,shop,title,thumb,price,number,amount,logistic,addtime,updatetime,note,buyer_passport,buyer_postcode,buyer_address,buyer_name,buyer_mobile,status) VALUES ('$itemid','$_username','$item[username]','$shop','$title','$item[thumb]','$item[price]','$number','$amount','$item[logistic]','$DT_TIME','$DT_TIME','$note','$_passport','$buyer_postcode','$buyer_address','$buyer_name','$buyer_mobile', 6)");
		$oid = $db->insert_id();
		$db->query("INSERT INTO {$DT_PRE}group_order_log_{$moduleid} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','$L[log_buy]','')");
		dheader('buy'.DT_EXT.'?action=result&itemid='.$itemid.'&auth='.encrypt('mid='.$moduleid.'&action=update&step=pay&itemid='.$oid, DT_KEY.'GURL', 300));
	}
	$address = array();
	if($item['logistic']) {
		$address = get_address($_username);
		$address or message($L['msg_buy_address'], ($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'address'.DT_EXT.'?action=add');
	}
}
$head_title = $L['buy_title'];
if($DT_PC) {
	$CSS = array('cart');
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	$forward = $item['mobile'];
	$js_pull = 0;
	$foot = '';
}
$template = $MOD['template_buy'] ? $MOD['template_buy'] : 'buy';
include template($template, $module);
?>