<?php
#https://www.juhe.cn/docs/api/id/486
require '../../common.inc.php';
$DT['sms_api'] == 'juhe' or exit('ko.api');
$DT['sms_code'] or exit('ko.code');
$DT['sms_template'] or exit('ko.template');
$DT['sms_appid'] or exit('ko.appid');
$auth = isset($auth) ? decrypt($auth, DT_KEY.'SMS') : '';
if(strpos($auth, '|') === false) exit('ko.auth');
list($mobile, $message) = explode('|', $auth);
is_mobile($mobile) or exit('ko.mobile');
strpos($message, $DT['sms_code']) !== false or exit('ko.code');
$code = preg_match("/[0-9]{4,6}/", $message, $matches) ? $matches[0] : '';
$code or exit('ko.code');
$data = 'mobile='.$mobile.'&tpl_id='.$DT['sms_template'].'&tplValue='.urlencode('#code#='.$code).'&key='.$DT['sms_appid'];
$res = dcurl('http://v.juhe.cn/vercodesms/send', $data);
$arr = json_decode($res, true);
if($arr['reason'] && $arr['reason'] == 'success' && strpos($res, $DT['sms_ok']) === false) $res = $DT['sms_ok'].'/'.$DT['sms_api'].'/'.$res;
echo $res;
?>