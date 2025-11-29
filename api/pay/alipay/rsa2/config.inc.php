<?php
defined('IN_DESTOON') or exit('Access Denied');
$config = array (	
		//应用ID,您的APPID。
		'app_id' => trim($PAY[$bank]['partnerid']),

		//商户私钥
		'merchant_private_key' => trim($PAY[$bank]['keycode']),
		
		//异步通知地址
		'notify_url' => DT_PATH.'api/pay/'.$bank.'/rsa2/'.($PAY[$bank]['notify'] ? $PAY[$bank]['notify'] : 'notify.php'),
		
		//同步跳转
		'return_url' => $receive_url,

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => trim($PAY[$bank]['public']),
);