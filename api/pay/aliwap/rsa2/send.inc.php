<?php 
defined('IN_DESTOON') or exit('Access Denied');
require_once DT_ROOT.'/api/pay/'.$bank.'/rsa2/config.inc.php';
require_once DT_ROOT.'/api/pay/'.$bank.'/rsa2/AlipayTradeService.php';
require_once DT_ROOT.'/api/pay/'.$bank.'/rsa2/AlipayTradeWapPayContentBuilder.php';
/* *
 * 功能：支付宝手机网站支付接口(alipay.trade.wap.pay)接口调试入口页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 请确保项目文件有可写权限，不然打印不了日志。
 */
//商户订单号，商户网站订单系统中唯一订单号，必填
$out_trade_no = $orderid;

//订单名称，必填
$subject = $charge_title ? $charge_title : '会员('.$_username.')充值(流水号:'.$orderid.')';

//付款金额，必填
$total_amount = $charge;

//商品描述，可空
$body = $DT['sitename'].'会员充值';

//超时时间
$timeout_express="1m";

$payRequestBuilder = new AlipayTradeWapPayContentBuilder();
$payRequestBuilder->setBody($body);
$payRequestBuilder->setSubject($subject);
$payRequestBuilder->setOutTradeNo($out_trade_no);
$payRequestBuilder->setTotalAmount($total_amount);
$payRequestBuilder->setTimeExpress($timeout_express);

$payResponse = new AlipayTradeService($config);
$result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);
var_dump($result);
?>