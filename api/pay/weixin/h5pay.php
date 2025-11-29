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
	$str = isset($auth) ? decrypt($auth, DT_KEY.'H5PAY') : '';
	$str or dheader($MODULE[2]['mobile'].'charge.php?action=record');
	$t = explode('|', $str);
	$itemid = $orderid = intval($t[0]);
	($itemid && $t[2] == $DT_IP) or dheader($MODULE[2]['mobile'].'charge.php?action=record');
	$charge_title = $t[1];
	$r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE itemid=$itemid");
}
if(!$r || $r['bank'] != $bank) dheader($MODULE[2]['mobile'].'charge.php?action=record');
if($r['username'] != $_username && $DT_TIME - $r['sendtime'] < 600) {//APP
	include load('member.lang');
	$MOD = cache_read('module-2.php');
	include DT_ROOT.'/include/post.func.php';
	include DT_ROOT.'/include/module.func.php';
	include DT_ROOT.'/module/member/member.class.php';
	$do = new member;
	$user = $do->login($r['username'], '', 0, 'h5pay');
}
if($r['status'] > 0) dheader($MODULE[2]['mobile'].'charge.php');
if($action == 'app') dheader('h5api.php?auth='.$auth);
$charge = $r['amount'];
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
<div id="head-bar">
<div class="head-bar">
<div class="head-bar-back"><a href="<?php echo $MODULE[2]['mobile'];?>charge.php?action=payed"><img src="<?php echo DM_SKIN;?>icon-back.png" width="24" height="24"/></a></div>
<div class="head-bar-title">微信支付</div>
<div class="head-bar-right"><a href="<?php echo $MODULE[2]['mobile'];?>charge.php?action=record" onclick="return confirm('确定要取消本次支付吗？');"><img src="<?php echo DM_SKIN;?>icon-cancel.png" width="24" height="24"/></a></div>
</div>
<div class="head-bar-fix"></div>
</div>
<div class="main" style="padding:96px 16px 19200px 16px;text-align:center;">	
	<div style="line-height:48px;font-weight:bold;"><span style="font-size:28px;"><?php echo $DT['money_sign'];?></span><span style="font-size:38px;"><?php echo $charge;?></span></div>
	<div style="line-height:48px;color:#999999;"><?php echo $charge_title;?></div>
	<div class="blank-32"></div>
	<input type="button" value="已经支付" class="btn-green" onclick="Go('<?php echo $MODULE[2]['mobile'];?>charge.php?action=payed');"/>
	<div class="blank-32"></div>
	<input type="button" value="重新支付" class="btn" onclick="Go('h5api.php?auth=<?php echo $auth;?>');"/>
</div>
<script type="text/javascript">
var interval = window.setInterval(
	function() {
		$.get('?action=ajax&itemid=<?php echo $itemid;?>', function(data) {
			if(data == 'ok') {
				clearInterval(interval);
				Go('<?php echo $MODULE[2]['mobile'];?>charge.php');
				//Go('destoon://');
				//setTimeout(function(){Go('<?php echo $MODULE[2]['mobile'];?>charge.php');}, 1000);
			}
		});
	}, 
3000);
</script>
</body>
</html>