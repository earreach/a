<?php
// /api/quote_login_email_code.php
// 作用：邮箱 + 验证码登录报价系统，设置 $_SESSION['quote_userid']

//define('IN_DESTOON', true);

error_reporting(E_ALL & ~E_NOTICE); // 屏蔽 Notice，保留 Warning/Error

require '../common.inc.php';

header('Content-type: application/json; charset=UTF-8');

global $db, $DT_PRE, $DT_TIME;

// 1. 取参数
$email      = isset($_POST['email']) ? trim($_POST['email']) : '';
$email_code = isset($_POST['email_code']) ? trim($_POST['email_code']) : '';

if($email == '' || $email_code == '') {
    echo json_encode(array(
        'success' => false,
        'message' => '邮箱或验证码为空',
    ));
    exit;
}

// 2. 邮箱格式简单校验（和你原来的逻辑保持一致）
if(function_exists('is_email')) {
    if(!is_email($email)) {
        echo json_encode(array(
            'success' => false,
            'message' => '邮箱格式不正确',
        ));
        exit;
    }
} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array(
        'success' => false,
        'message' => '邮箱格式不正确',
    ));
    exit;
}

// 3. 验证码校验：直接复用 quote_verify 这一套
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

                   ORDER BY id DESC");

if(!$r) {
    echo json_encode(array(
        'success' => false,
        'message' => '验证码错误或不存在，请重新获取',
    ));
    exit;
}

if($r['expiretime'] < $DT_TIME) {
    echo json_encode(array(
        'success' => false,
        'message' => '验证码已过期，请重新获取',
    ));
    exit;
}

// 消费掉这条验证码（和 quote.inc.php 里的思路一致）
$db->query("UPDATE {$table_verify} SET status=1 WHERE id='{$r['id']}'");

// 4. 按邮箱查 member，拿 userid
$table_member = $DT_PRE.'member';
$member = $db->get_one("SELECT userid,username,email FROM {$table_member} WHERE email='$email_sql' LIMIT 1");

if(!$member) {
    // 理论上只要提交过报价就会有会员，这里只是兜底
    echo json_encode(array(
        'success' => false,
        'message' => '该邮箱暂无对应账号，请先提交一次报价',
    ));
    exit;
}

$userid   = intval($member['userid']);
$username = $member['username'];

// 5. 写入 session（报价系统自己的登录态，不影响全站登录）
$session = new dsession();
$_SESSION['quote_userid']   = $userid;
$_SESSION['quote_email']    = $member['email'];
$_SESSION['quote_username'] = $username;
$_SESSION['quote_login_at'] = $DT_TIME;

// 6. 返回成功
echo json_encode(array(
    'success'  => true,
    'message'  => '登录成功',
    'userid'   => $userid,
    'username' => $username,
));
exit;
