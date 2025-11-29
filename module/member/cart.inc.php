<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($mid < 5) {
	foreach($MODULE as $v) {
		if(in_array($v['module'], array('mall', 'sell'))) {
			$mid = $v['moduleid'];
			break;
		}
	}
}
if(isset($MODULE[$mid]) && in_array($MODULE[$mid]['module'], array('mall', 'sell'))) {
	$moduleid = $mid;
	$MOD = cache_read('module-'.$moduleid.'.php');
	$module = $MOD['module'];
} else {
	$action = 'close';
}
if(is_array($itemid) && !$_userid) {
	$DT_URL = $MODULE[2]['linkurl'].'cart'.DT_EXT.'?action=add&mid='.$mid;
	foreach($itemid as $id) {
		$DT_URL .= '&itemid[]='.$id;
	}
}
require DT_ROOT.'/module/mall/global.func.php';
require DT_ROOT.'/module/member/cart.class.php';
include load('misc.lang');
if($_userid) {
	$do = new cart();
	$do->max = intval($DT['max_cart']);
	$cart = $do->get();
	if($itemid && $action != 'result') $action = 'add';
} else {
	$action = 'guest';
}
$lists = array();
switch($action) {
	case 'close':
	break;
	case 'guest':
	break;
	case 'add':
		$s1 = isset($s1) ? intval($s1) : 0;
		$s2 = isset($s2) ? intval($s2) : 0;
		$s3 = isset($s3) ? intval($s3) : 0;
		$a = isset($a) ? intval($a) : 1;
		$code = $do->add($cart, $mid, $itemid, $s1, $s2, $s3, $a);
		dheader('cart'.DT_EXT.'?action=result&mid='.$mid.'&itemid='.$do->errid.'&code='.$code);
	break;
	case 'result':
		$code = isset($code) ? intval($code) : 0;
		$url = gourl('?mid='.$mid.'&itemid='.$itemid);
	break;
	case 'clear':
		$do->clear();
		dheader('cart'.DT_EXT.'?mid='.$mid.'&rand='.$DT_TIME);
	break;
	case 'delete':
		isset($key) or $key = '';
		$fav = (isset($fav) && $fav) ? 1 : 0;
		$keys = is_array($key) ? $key : array($key);
		foreach($keys as $key) {
			if(isset($cart[$key])) {
				unset($cart[$key]);
				if($fav) $do->fav($key);
			}
		}
		$do->set($cart);
		if(isset($ajax)) exit('ok');
		dheader('cart'.DT_EXT.'?mid='.$mid.'&rand='.$DT_TIME);
	break;
	default:
		$lists = $do->get_list($cart);
	break;
}
$CSS = array('cart');
$head_title = $L['cart_title'];
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MODULE[2]['linkurl'], $MODULE[2]['mobile'], $DT_URL);
} else {
	$foot = $lists ? '' : 'cart';
	$head_name = $head_title;
	if($sns_app) $seo_title = $site_name;
}
include template('cart', 'member');
?>