<?php
//https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=9_20&index=1
require '../../../common.inc.php';
$bank = 'weixin';
$PAY = cache_read('pay.php');
$PAY[$bank]['enable'] or dheader($MODULE[2]['mobile'].'charge.php?action=record');
$charge_title = '';
if($action == 'ajax') {
	$itemid or exit('ko');
	$r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE itemid=$itemid");
	if($r && $_username == $r['username'] && $r['status'] > 0) exit('ok');
	exit('ko');
} else {
	$auth = isset($auth) ? decrypt($auth, DT_KEY.'H5PAY') : '';
	$auth or dheader($MODULE[2]['mobile'].'charge.php?action=record');
	$t = explode('|', $auth);
	$itemid = $orderid = intval($t[0]);
	($itemid && $t[2] == $DT_IP) or dheader($MODULE[2]['mobile'].'charge.php?action=record');
	$charge_title = $t[1];
	$r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE itemid=$itemid");
}
if(!$r || $r['username'] != $_username || $r['status'] > 0 || $r['bank'] != $bank) dheader($MODULE[2]['mobile'].'charge.php?action=record');
function make_sign($arr, $key) {
	ksort($arr);
	$str = '';
	foreach($arr as $k=>$v) {
		if($v) $str .= $k.'='.$v.'&';
	}
	$str .= 'key='.$key;
	return strtoupper(md5($str));
}
function make_xml($arr) {
	$str = '<xml>';
	foreach($arr as $k=>$v) {
		if(is_numeric($v)) {
			$str .= '<'.$k.'>'.$v.'</'.$k.'>';
		} else {
			$str .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
		}
	}
	$str .= '</xml>';
	return $str;
}
$charge = $r['amount'] + $r['fee'];
$total_fee = $charge*100;
$post = array();
$post['appid'] = $PAY[$bank]['appid'];
$post['mch_id'] = $PAY[$bank]['partnerid'];
$post['nonce_str'] = md5(md5($itemid.$PAY[$bank]['keycode'].$total_fee));
$post['body'] = $charge_title ? $charge_title : '会员('.$_username.')充值(流水号:'.$orderid.')';
$post['out_trade_no'] = $itemid;
$post['total_fee'] = $total_fee;
$post['spbill_create_ip'] = $DT_IP;
$post['notify_url'] = DT_PATH.'api/pay/'.$bank.'/'.($PAY[$bank]['notify'] ? $PAY[$bank]['notify'] : 'notify.php');
$post['trade_type'] = 'MWEB';
$post['product_id'] = $itemid;
$post['scene_info'] = '{"h5_info": {"type": "Wap","wap_url": "'.DT_MOB.'","wap_name": "'.$EXT['mobile_sitename'].'"}}';
$post['sign'] = make_sign($post, $PAY[$bank]['keycode']);
$rec = dcurl('https://api.mch.weixin.qq.com/pay/unifiedorder', make_xml($post));
if(strpos($rec, 'mweb_url') !== false) {
	if(function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);
	$x = simplexml_load_string($rec, 'SimpleXMLElement', LIBXML_NOCDATA);
} else {
	if(strpos($rec, 'return_msg') !== false) {
		if(function_exists('libxml_disable_entity_loader')) libxml_disable_entity_loader(true);
		$x = simplexml_load_string($rec, 'SimpleXMLElement', LIBXML_NOCDATA);
		dalert(convert($x->return_msg, 'UTF-8', DT_CHARSET), $MODULE[2]['mobile'].'charge.php?action=record');
	} else {
		dalert('Can Not Connect weixin', $MODULE[2]['mobile'].'charge.php?action=record');
	}
}
$pay = DT_PATH.'api/pay/'.$bank.'/h5pay.php?auth='.encrypt($orderid.'|'.$charge_title.'|'.$DT_IP, DT_KEY.'H5PAY', 600);
$url = $x->mweb_url.'&redirect_url='.urlencode($pay);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=<?php echo DT_CHARSET;?>"/>
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width"/>
<title>微信支付<?php echo $DT['seo_delimiter'];?><?php echo $DT['sitename'];?></title>
<link rel="stylesheet" type="text/css" href="<?php echo DM_SKIN;?>style.css?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"/>
<script type="text/javascript" src="<?php echo DT_PATH;?>lang/<?php echo DT_LANG;?>/lang.mobile.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_MOB;?>script/jquery-2.2.4.min.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript">var Dbrowser = '<?php echo $DT_MBS;?>';</script>
<script type="text/javascript" src="<?php echo DT_MOB;?>script/common.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_MOB;?>script/reset.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
</head>
<body>
<?php if($DT_MBS != 'app') {?>
<div id="head-bar">
<div class="head-bar">
<div class="head-bar-back"><a href="<?php echo $MODULE[2]['mobile'];?>charge.php?action=payed"><img src="<?php echo DM_SKIN;?>icon-back.png" width="24" height="24"/></a></div>
<div class="head-bar-title">微信支付</div>
<div class="head-bar-right"><a href="<?php echo $MODULE[2]['mobile'];?>charge.php?action=record" onclick="return confirm('确定要取消本次支付吗？');"><img src="<?php echo DM_SKIN;?>icon-cancel.png" width="24" height="24"/></a></div>
</div>
<div class="head-bar-fix"></div>
</div>
<?php } ?>
<div class="main" style="padding:96px 16px 19200px 16px;text-align:center;">
	<div style="line-height:48px;font-weight:bold;"><span style="font-size:28px;"><?php echo $DT['money_sign'];?></span><span style="font-size:38px;"><?php echo $charge;?></span></div>
	<div style="line-height:48px;color:#999999;"><?php echo $charge_title;?></div>
	<div class="blank-32"></div>
	<input type="button" value="立即支付" class="btn-green" onclick="Go('<?php echo $url;?>');"/>
	<div class="blank-32"></div>
	<input type="button" value="已经支付" class="btn" onclick="Go('<?php echo $MODULE[2]['mobile'];?>charge.php?action=payed');"/>
</div>
<script type="text/javascript">
var interval = window.setInterval(
	function() {
		$.get('?action=ajax&itemid=<?php echo $itemid;?>', function(data) {
			if(data == 'ok') {
				clearInterval(interval);
				Go('<?php echo $MODULE[2]['mobile'];?>charge.php');
			}
		});
	}, 
3000);
</script>
<?php if($DT_TIME - $r['sendtime'] < 5 && $DT_MBS != 'app') {?><meta http-equiv="refresh" content="0;url=<?php echo $url;?>"/><?php } ?>
</body>
</html>