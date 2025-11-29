<?php
defined('IN_DESTOON') or exit('Access Denied');
require_once DT_ROOT.'/api/pay/'.$bank.'/rsa2/config.inc.php';
require_once DT_ROOT.'/api/pay/'.$bank.'/rsa2/AlipayTradeService.php';
/* *
 * 功能：支付宝页面跳转同步通知页面
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 */
$arr=$_GET;
$alipaySevice = new AlipayTradeService($config); 
$result = $alipaySevice->check($arr);

/* 实际验证过程建议商户添加以下校验。
1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
4、验证app_id是否为该商户本身。
*/
if($result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号
	$out_trade_no = intval($_GET['out_trade_no']);

	//支付宝交易号
	$trade_no = $_GET['trade_no'];

	//支付金额
	$total_fee = dround($_GET['total_amount']);


	if($out_trade_no != $charge_orderid) {
		$charge_status = 2;
		$charge_errcode = '订单号不匹配';
		$note = $charge_errcode.'S:'.$charge_orderid.'R:'.$out_trade_no;
	} else if($total_fee != $charge_money) {
		$charge_status = 2;
		$charge_errcode = '充值金额不匹配';
		$note = $charge_errcode.'S:'.$charge_money.'R:'.$total_fee;
	} else {
		$charge_status = 1;
	}
		
	#echo "验证成功<br />外部订单号：".$out_trade_no;

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    #echo "验证失败";
}
?>