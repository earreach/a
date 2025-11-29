<?php
// /api/quote_check_email_code.php
//define('IN_DESTOON', true);
require '../common.inc.php'; // 路径按你现在 quote_send_email_code.php 的写法来改

header('Content-type: application/json; charset=UTF-8');

global $db, $DT_PRE, $DT_TIME;

// 1. 取参数
$email      = isset($_POST['email']) ? trim($_POST['email']) : '';
$email_code = isset($_POST['email_code']) ? trim($_POST['email_code']) : '';

if($email == '' || $email_code == '') {
    echo json_encode([
        'success' => false,
        'message' => '邮箱或验证码为空'
    ]);
    exit;
}

// 2. 简单邮箱格式检查
if(function_exists('is_email')) {
    if(!is_email($email)) {
        echo json_encode([
            'success' => false,
            'message' => '邮箱格式不正确'
        ]);
        exit;
    }
} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => '邮箱格式不正确'
    ]);
    exit;
}

// 3. 查询验证码（注意：这里只“检查”，不改 status）
$table_verify = $DT_PRE.'quote_verify';
$scene        = 'quote';

if(function_exists('daddslashes')) {
    $email_sql = daddslashes($email);
    $code_sql  = daddslashes($email_code);
} else {
    $email_sql = addslashes($email);
    $code_sql  = addslashes($email_code);
}

$r = $db->get_one("SELECT * FROM {$table_verify}
                   WHERE contact='$email_sql'
                     AND type='email'
                     AND scene='$scene'
                     AND code='$code_sql'
                     AND status=0
                   ORDER BY id DESC");

if(!$r) {
    echo json_encode([
        'success' => false,
        'message' => '验证码错误或不存在'
    ]);
    exit;
}

if($r['expiretime'] < $DT_TIME) {
    echo json_encode([
        'success' => false,
        'message' => '验证码已过期，请重新获取'
    ]);
    exit;
}

// 不修改 status，让真正提交时 quote.inc.php 再“消费”验证码
echo json_encode([
    'success' => true,
    'message' => '验证码正确'
]);
exit;
