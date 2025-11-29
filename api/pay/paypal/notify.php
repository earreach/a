<?php
#https://developer.paypal.com/api/nvp-soap/ipn/ht-ipn/
$_SERVER['REQUEST_URI'] = '';
$_DPOST = $_POST;
$_DGET = $_GET;
require '../../../common.inc.php';
$_POST = $_DPOST;
$_GET = $_DGET;
if(!$_POST && !$_GET) exit('fail');
$bank = 'paypal';
$PAY = cache_read('pay.php');
if(!$PAY[$bank]['enable']) exit('fail');
if(!$PAY[$bank]['partnerid']) exit('fail');
$editor = 'N'.$bank;
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	// Handle escape characters, which depends on setting of magic quotes
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$charge_status = 0;
$item_number = intval($_POST['item_number']);
$r = $db->get_one("SELECT * FROM {$DT_PRE}finance_charge WHERE itemid=$item_number");
if($r) {
	if($r['status'] == 0) {
		$charge_orderid = $r['itemid'];
		$charge_money = $r['amount'] + $r['fee'];
		$charge_amount = $r['amount'];
		$res = dcurl('https://ipnpb.paypal.com/cgi-bin/webscr', $req, array('Connection: Close'));
		if(strcmp($res, "VERIFIED") == 0) {
			if(dround($payment_amount) != dround($charge_money)) {
				$charge_status = 2;
				$charge_errcode = '充值金额不匹配';
			} else if($payment_currency != $PAY[$bank]['currency']) {
				$charge_status = 2;
				$charge_errcode = '充值币种不匹配';
			} else if($receiver_email != $PAY[$bank]['partnerid']) {
				$charge_status = 2;
				$charge_errcode = '收款账号不匹配';
			} else if($payment_status == 'Completed') {
				$charge_status = 1;
			}
		} else if(strcmp($res, "INVALID") == 0) {		
			$charge_status = 2;
			$charge_errcode = '支付失败';
		}
		if($charge_status == 1) {
			$tno = $_POST['txn_id'];
			require DT_ROOT.'/api/pay/success.inc.php';
			exit('success');
		} else {
			$note = $charge_errcode;
			$db->query("UPDATE {$DT_PRE}finance_charge SET receivetime='$DT_TIME',editor='$editor',note='$note' WHERE itemid=$charge_orderid");//支付失败
			exit('fail');
		}
	} else if($r['status'] == 1) {
		exit('fail');
	} else if($r['status'] == 2) {
		exit('fail');
	} else {
		exit('success');
	}
} else {
	exit('fail');
}
?>