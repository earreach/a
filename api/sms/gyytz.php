<?php
#https://market.aliyun.com/apimarket/detail/cmapi00037415
require '../../common.inc.php';
$DT['sms_api'] == 'gyytz' or exit('ko.api');
$DT['sms_code'] or exit('ko.code');
$DT['sms_template'] or exit('ko.template');
$DT['sms_appid'] or exit('ko.appid');
$DT['sms_par'] or exit('ko.par');
$auth = isset($auth) ? decrypt($auth, DT_KEY.'SMS') : '';
if(strpos($auth, '|') === false) exit('ko.auth');
list($mobile, $message) = explode('|', $auth);
is_mobile($mobile) or exit('ko.mobile');
strpos($message, $DT['sms_code']) !== false or exit('ko.code');
$code = preg_match("/[0-9]{4,6}/", $message, $matches) ? $matches[0] : '';
$code or exit('ko.code');
$minute = intval(cutstr($message, '有效期', '分钟'));
$minute > 0 or $minute = 10;
$head = array();
$head[] = "Authorization:APPCODE ".$DT['sms_appid'];
$head[] = "Content-Type:application/x-www-form-urlencoded;charset=UTF-8";
$par = 'mobile='.$mobile.'&templateId='.$DT['sms_template'].'&smsSignId='.$DT['sms_par'].'&param=**code**:'.$code.',**minute**:'.$minute;
$res = dcurl('https://gyytz.market.alicloudapi.com/sms/smsSend', $par, $head);
if($res && strpos($res, '成功') !== false && strpos($res, $DT['sms_ok']) === false) $res = $DT['sms_ok'].'/'.$DT['sms_api'].'/'.$res;
echo $res;
?>