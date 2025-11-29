<?php
//define('IN_DESTOON', true);
require '../common.inc.php';

header('Content-Type: application/json; charset=utf-8');

// 调试时可以打开
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$scene = 'quote';

if($email == '') {
    echo json_encode(array('success' => false, 'message' => '请先填写邮箱地址'));
    exit;
}

// 邮箱格式
if(function_exists('is_email')) {
    if(!is_email($email)) {
        echo json_encode(array('success' => false, 'message' => '邮箱格式不正确'));
        exit;
    }
} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array('success' => false, 'message' => '邮箱格式不正确'));
    exit;
}

$table = $DT_PRE.'quote_verify';
$now   = $DT_TIME;
$ip    = $DT_IP;
$email_sql = daddslashes($email);

// 60秒内同一个邮箱只发一次
$r = $db->get_one("SELECT * FROM {$table} WHERE contact='$email_sql' AND scene='$scene' ORDER BY id DESC");
if($r && $now - $r['addtime'] < 2) {
    echo json_encode(array('success' => false, 'message' => '发送过于频繁，请稍后再试'));
    exit;
}

// 生成 6 位验证码
$code   = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
$expire = $now + 300; // 5分钟

$db->query("INSERT INTO {$table} (contact,type,code,scene,ip,addtime,expiretime,status)
            VALUES('$email_sql','email','$code','$scene','$ip','$now','$expire',0)");

// 发邮件
$sitename = isset($DT['sitename']) ? $DT['sitename'] : '网站';
$title    = $sitename.' 报价验证码';
$content  = '您的验证码为：'.$code.'，5分钟内有效。如非本人操作，请忽略本邮件。';

$send_ok = false;
if(function_exists('send_mail')) {
    $send_ok = send_mail($email, $title, $content);
}

if(!$send_ok) {
    echo json_encode(array(
        'success' => false,
        'message' => '验证码发送失败，请联系管理员检查邮件配置'
    ));
    exit;
}

echo json_encode(['success' => true, 'message' => '（测试模式）伪装成已发送']);
exit;
echo json_encode(array(
    'success' => true,
    'message' => '验证码已发送，请查收邮箱'
));
exit;
