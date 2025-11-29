<?php
define('IN_DESTOON', true);
require '../common.inc.php';

header('Content-Type: application/json; charset=utf-8');

// 1. 取参数
$contact = trim(isset($_POST['contact']) ? $_POST['contact'] : '');
$captcha = trim(isset($_POST['captcha']) ? $_POST['captcha'] : '');
$scene   = 'quote';

if($contact == '') {
    echo json_encode(['success' => false, 'message' => '请先填写手机或邮箱']);
    exit;
}
if($captcha == '') {
    echo json_encode(['success' => false, 'message' => '请先输入图片验证码']);
    exit;
}

// 2. 校验图片验证码（复用系统逻辑）
// 这里假设系统已经有 captcha() 函数（在 include/global.func.php 里）
// 有些版本的第三个参数是 $return=0/1，
// 我这里用 true 让它“返回”而不是直接 message()，如果报参数错误，你根据本地函数签名微调下即可。
if(function_exists('captcha')) {
    $check = captcha($captcha, 1, true);
    if($check !== true) {
        // 有些版本返回 true/false，有些返回错误信息，你本地调一次就知道
        echo json_encode([
            'success' => false,
            'message' => is_string($check) && $check ? $check : '图片验证码错误或已过期'
        ]);
        exit;
    }
} else {
    // 万一你本地没有 captcha()，先留一个兜底，后面你可以按会员注册时的写法改这里
    // echo json_encode(['success' => false, 'message' => '系统未配置验证码校验函数']);
    // exit;
}

// 3. 判断联系方式类型：邮箱 or 手机
if(strpos($contact, '@') !== false) {
    $type = 'email';
} else {
    $type = 'mobile';
}

// 简单格式校验（优先用 Destoon 自带函数）
if($type == 'email') {
    if(function_exists('is_email')) {
        if(!is_email($contact)) {
            echo json_encode(['success' => false, 'message' => '邮箱格式不正确']);
            exit;
        }
    } else if(!filter_var($contact, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => '邮箱格式不正确']);
        exit;
    }
} else { // mobile
    if(function_exists('is_mobile')) {
        if(!is_mobile($contact)) {
            echo json_encode(['success' => false, 'message' => '手机号格式不正确']);
            exit;
        }
    } else {
        // 兜底规则：允许数字、+、- 和空格，长度大于等于6
        $pure = preg_replace('/[\s\-+]/', '', $contact);
        if(!preg_match('/^\d{6,20}$/', $pure)) {
            echo json_encode(['success' => false, 'message' => '手机号格式不正确']);
            exit;
        }
    }
}

// 4. 发送频率限制
$table = $DT_PRE.'quote_verify';
$now   = $DT_TIME;
$ip    = $DT_IP;

$contact_sql = daddslashes($contact);

$r = $db->get_one("SELECT * FROM {$table} WHERE contact='$contact_sql' AND scene='$scene' ORDER BY id DESC");
if($r && $now - $r['addtime'] < 60) { // 60秒内只允许发一次
    echo json_encode(['success' => false, 'message' => '发送过于频繁，请稍后再试']);
    exit;
}

// 5. 生成验证码并入库
$code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // 6位数字
$expire = $now + 300; // 5分钟

$db->query("INSERT INTO {$table} (contact,type,code,scene,ip,addtime,expiretime,status) VALUES ('$contact_sql','$type','$code','$scene','$ip','$now','$expire',0)");

// 6. 发送短信或邮件
$sitename = isset($DT['sitename']) ? $DT['sitename'] : '网站';
$msg = '您的验证码为：'.$code.'，5分钟内有效。【'.$sitename.'】';

$send_ok = false;
if($type == 'mobile') {
    if(function_exists('send_sms')) {
        $send_ok = send_sms($contact, $msg);
    }
} else { // email
    if(function_exists('send_mail')) {
        $send_ok = send_mail($contact, '报价验证码', $msg);
    }
}

if(!$send_ok) {
    echo json_encode([
        'success' => false,
        'message' => '验证码发送失败，请联系管理员检查短信/邮箱配置'
    ]);
    exit;
}

// 7. 返回成功
echo json_encode([
    'success' => true,
    'message' => ($type == 'mobile' ? '短信验证码已发送，请注意查收' : '邮件验证码已发送，请注意查收'),
    'type'    => $type,
]);
exit;
