<?php
// module/article/quote.inc.php
// 设备故障报价页面：
// - 第一次（从 list-4 提交）只带 devices，用于展示确认页
// - 第二次（从报价表单提交）带 devices_detail + 其它字段，用于入库

defined('IN_DESTOON') or exit('Access Denied');
// 在 defined('IN_DESTOON') 之后，其他 require 之前
error_reporting(E_ALL & ~E_NOTICE); // 屏蔽 Notice，保留 Warning/Error

// 补充需要的函数库
//require_once DT_ROOT.'/include/global.func.php'; // 里面有 get_intro 等
require_once DT_ROOT.'/include/module.func.php'; // 里面有 get_intro 等
require_once DT_ROOT.'/include/post.func.php';   // 里面有 is_email / is_mobile 等
require_once DT_ROOT.'/module/member/member.class.php'; // 会员类
global $db, $DT_PRE, $DT_TIME, $MODULE;


// -----------------------------------------------------
// 工具函数：根据邮箱/手机获取或创建会员，返回 userid
// -----------------------------------------------------
function quote_get_or_create_member($email, $mobile, $first_name = '', $last_name = '') {
    global $db, $DT_PRE, $DT_TIME;

    $email  = trim($email);
    $mobile = trim($mobile);

    // 没有任何联系方式，就不建账号
    if($email === '' && $mobile === '') return 0;

    // 1) 优先按手机号找
    if($mobile !== '') {
        $r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE mobile='$mobile'");
        if($r) return intval($r['userid']);
    }

    // 2) 再按邮箱找
    if($email !== '') {
        $r = $db->get_one("SELECT userid FROM {$DT_PRE}member WHERE email='$email'");
        if($r) return intval($r['userid']);
    }
    // 先加载表单验证函数（is_email / is_mobile / is_qq 等）
//    require_once DT_ROOT.'/include/post.func.php';
    // 3) 都没有 -> 自动注册一个个人会员（groupid=5）
//    require_once DT_ROOT.'/module/member/member.class.php';
    $do = new member;

    // 生成基础用户名
    if($mobile !== '') {
        $base = 'm'.preg_replace('/\D/', '', $mobile); // 去掉非数字
    } elseif($email !== '') {
        $pos  = strpos($email, '@');
        $base = $pos !== false ? substr($email, 0, $pos) : $email;
    } else {
        $base = 'u'.timetodate($DT_TIME, 'ymdHis');
    }

    // 避免 username 冲突
    $username = $base;
    $i = 0;
    while($db->get_one("SELECT userid FROM {$DT_PRE}member WHERE username='$username'")) {
        $i++;
        $username = $base.$i;
    }

    // 随机密码（以后可以走验证码登录，不强迫用户记这个）
    $password = random(8);

    $truename = trim($first_name.$last_name);
    if($truename === '') $truename = $username;

    $member = array();
    $member['username']  = $username;
    $member['password']  = $password;
    $member['email']     = $email;
    $member['mobile']    = $mobile;
    $member['truename']  = $truename;
    $member['groupid']   = 5; // 个人会员组
    $member['regid']     = 5;
    $member['regip']     = DT_IP;
    $member['regtime']   = $DT_TIME;
    $member['edittime']  = $DT_TIME;
    if($email  !== '') $member['vemail']  = 1; // 邮箱已验证
    if($mobile !== '') $member['vmobile'] = 1; // 手机已验证（如果你以后也做手机验证码）

    // 这里不再调用 $do->pass()，因为邮箱/手机在前面流程已经校验过了
    $do->add($member);

    return $do->userid ? intval($do->userid) : 0;
}


// ----------------- 模块与表 -----------------
$moduleid = 21; // 文章模块
$MOD = isset($MODULE[$moduleid]) ? $MODULE[$moduleid] : cache_read('module-'.$moduleid.'.php');

$table_quote   = $DT_PRE.'article_quote_'.$moduleid; // 报价表 dt_article_quote_21
$table_cat     = $DT_PRE.'category';
$table_company = $DT_PRE.'company';


// ===================================================
// 一、提交分支：从 quote-form 提交回来，入库
// 条件：POST 且存在非空 devices_detail
// ===================================================
if($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['devices_detail'])
    && $_POST['devices_detail'] !== '') {


    // -------- 1）接收字段 --------
    $devices_json   = stripslashes(trim($_POST['devices_detail']));
    $model_catids   = trim(isset($_POST['model_catids']) ? $_POST['model_catids'] : '');

    // 颜色：支持 color[catid] 多设备颜色，也兼容旧版单一 color
    $color_raw  = isset($_POST['color']) ? $_POST['color'] : '';
    $color      = '';
    $color_map  = array(); // 每个设备 catid => 颜色

    if(is_array($color_raw)) {
        foreach($color_raw as $cid => $c) {
            $c = trim($c);
            if($c === '') continue;
            $cid = intval($cid);
            if($cid) $color_map[$cid] = $c;
        }
        if($color_map) {
            // 写入报价表 color 字段：多个颜色用逗号隔开
            $color = implode(',', array_values($color_map));
        }
    } else {
        $color = trim($color_raw);
    }

    $fault_desc     = trim(isset($_POST['fault_desc']) ? $_POST['fault_desc'] : '');


    $images_arr     = isset($_POST['images']) && is_array($_POST['images']) ? $_POST['images'] : array();

    $company_id     = intval(isset($_POST['company_id']) ? $_POST['company_id'] : 0);

    $appoint_date   = trim(isset($_POST['appoint_date']) ? $_POST['appoint_date'] : '');
    $appoint_hour   = trim(isset($_POST['appoint_hour']) ? $_POST['appoint_hour'] : '');
//    var_dump($appoint_date);
//    var_dump($appoint_hour);
//    die();


    $discount_code  = trim(isset($_POST['discount_code']) ? $_POST['discount_code'] : '');

    $first_name     = trim(isset($_POST['first_name']) ? $_POST['first_name'] : '');
    $last_name      = trim(isset($_POST['last_name']) ? $_POST['last_name'] : '');

    $mobile         = trim(isset($_POST['mobile']) ? $_POST['mobile'] : '');
    $email          = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $email_code     = trim(isset($_POST['email_code']) ? $_POST['email_code'] : '');

    // captcha 模板里的 name 一般是 captcha 或 verify
    $captcha        = '';
    if(isset($_POST['captcha'])) {
        $captcha = trim($_POST['captcha']);
    } else if(isset($_POST['verify'])) {
        $captcha = trim($_POST['verify']);
    }

    // -------- 2）基础校验 --------
    if($devices_json == '') {
        message('设备故障数据丢失，请返回重新选择');
    }

    $devices = json_decode($devices_json, true);
    if(!$devices || !is_array($devices)) {
        message('设备故障数据解析失败，请返回重新选择');
    }
    // 把颜色写回 devices 结构，便于后续保存和回执页展示
    if(!empty($color_map)) {
        foreach($color_map as $cid => $c) {
            if(isset($devices[$cid])) {
                $devices[$cid]['color'] = $c;
            }
        }
    } elseif($color !== '') {
        // 兼容旧数据：只有一个颜色时，所有设备共用
        foreach($devices as $cid => &$d) {
            $d['color'] = $color;
        }
        unset($d);
    }
    if(!$company_id) {
        message('请选择维修门店');
    }

    if($first_name == '' || $last_name == '') {
        message('请填写姓和名');
    }

    if($email == '') {
        message('请填写邮箱地址');
    }

    // 邮箱验证码必填
    if($email_code == '') {
        message('请填写邮箱验证码');
    }

    // 预约日期和时间必填
    if($appoint_date == '' || $appoint_hour === '') {
        message('请选择预约日期和时间');
    }

    // 邮箱格式简单判断
    if($email != '') {
        if(function_exists('is_email')) {
            if(!is_email($email)) message('邮箱格式不正确');
        } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            message('邮箱格式不正确');
        }
    }

    // 手机简单判断（可选填）
    if($mobile != '') {
        $m = preg_replace('/[\\s\\-\\+]/', '', $mobile);
        if(!preg_match('/^\\d{6,20}$/', $m)) {
            message('电话号码格式不正确');
        }
    }

    // -------- 3）图片验证码 --------
    if($captcha == '') {
        message('请填写图片验证码');
    }
    if(function_exists('captcha')) {
        // 你那边已经确认：captcha() 返回空字符串表示正确
        if(captcha($captcha) !== '') {
            message('图片验证码不正确');
        }
    }

    // -------- 4）邮箱验证码校验（quote_verify 表）--------
    $verify_status = 0;
    $table_verify  = $DT_PRE.'quote_verify';
    $scene         = 'quote';

    // 防 SQL 注入
    if(function_exists('daddslashes')) {
        $email_sql = daddslashes($email);
        $code_sql  = daddslashes($email_code);
    } else {
        $email_sql = addslashes($email);
        $code_sql  = addslashes($email_code);
    }

    // 取最近一条未使用的验证码记录
    $r = $db->get_one("SELECT * FROM {$table_verify}
                   WHERE contact='$email_sql'
                     AND type='email'
                     AND scene='$scene'
                     AND code='$code_sql'
                     AND status=0
                   ORDER BY id DESC");
    if(!$r) {
        message('邮箱验证码错误或不存在，请重新获取');
    }

    if($r['expiretime'] < $DT_TIME) {
        message('邮箱验证码已过期，请重新获取');
    }

    // 标记验证码已使用
    $db->query("UPDATE {$table_verify} SET status=1 WHERE id='{$r['id']}'");

    // 联系方式已验证
    $verify_status = 1;

    // -------- 5）处理图片路径 --------
    $images_clean = array();
    if($images_arr) {
        foreach($images_arr as $p) {
            $p = trim($p);
            if($p == '') continue;
            // 简单防目录穿越
            if(strpos($p, '..') !== false) continue;
            $images_clean[] = $p;
        }
    }
    $images_str = $images_clean ? implode(',', $images_clean) : '';

    // 6) 计算金额（故障原价合计 = 所有选中故障的 price 之和）
    $total_fault_amount = 0.00;
    foreach($devices as $catid => $d) {
        if(!isset($d['faults']) || !is_array($d['faults'])) continue;
        foreach($d['faults'] as $f) {
            $p = isset($f['price']) ? floatval($f['price']) : 0;
            $total_fault_amount += $p;
        }
    }

    // 7) 优惠码逻辑：优惠码抵扣金额是固定的，绑定在优惠码上
    //    这里先简单写死：只要填写了优惠码，就减 1000（之后可以改成查优惠码表）
//    暂时不减
    $coupon_amount = 0.00;
    if($discount_code !== '') {
        $coupon_amount = 0.00;
    }
    if($coupon_amount < 0) $coupon_amount = 0;
    if($coupon_amount > $total_fault_amount) $coupon_amount = $total_fault_amount;

    // 7.1 报价金额（quote_amount）：只减“优惠码抵扣金额”之后的金额
    $quote_amount = $total_fault_amount - $coupon_amount;

    // 7.2 人工优惠金额（discount_amount）：后台审核时填写，这里先置 0
    $manual_discount = 0.00;

    // 7.3 最终报价金额 = 故障原价合计 - 优惠码抵扣金额 - 人工优惠金额
    $final_amount = $total_fault_amount - $coupon_amount - $manual_discount;
//    var_dump($final_amount);
//    var_dump(9999999999);
//    die();
    if($final_amount < 0) $final_amount = 0;

    // 8) 预约时间（现在仍然只做时间戳，不入库，后面要存的话表里加字段）
    $appoint_time = 0;
    if($appoint_date && $appoint_hour !== '') {
        $appoint_time = strtotime($appoint_date.' '.$appoint_hour.':00:00');
        // 这里不强制校验是否早于当前时间，避免影响测试
    }


    // -------- 6.x）自动绑定/注册会员账号（用原始联系方式） --------
    // 注意：这里用的是还没 daddslashes 的 $email / $mobile
    $userid = quote_get_or_create_member($email, $mobile, $first_name, $last_name);

    // 9) 组装并入库 dt_article_quote_21
    $devices_save  = daddslashes(json_encode($devices));
    $color         = daddslashes($color);
    $fault_desc    = daddslashes($fault_desc);
    $discount_code = daddslashes($discount_code);
    $first_name    = daddslashes($first_name);
    $last_name     = daddslashes($last_name);
    $email         = daddslashes($email);
    $mobile        = daddslashes($mobile);

    $company_id    = intval($company_id);
    $addtime       = $DT_TIME;
    $edittime      = $DT_TIME;
    $status        = 0; // 0=待审核

    $sql = "INSERT INTO {$table_quote}
        (`devices`,`color`,`fault_desc`,`images`,
         `company_id`,
         `first_name`,`last_name`,
         `email`,`mobile`,
         `discount_code`,`quote_amount`,`discount_amount`,
         `total_fault_amount`,`final_amount`,
         `verify_status`,`status`,
         `addtime`,`edittime`,
         `userid`
        ) VALUES (
         '$devices_save','$color','$fault_desc','$images_str',
         '$company_id',
         '$first_name','$last_name',
         '$email','$mobile',
         '$discount_code',
         '".number_format($quote_amount,      2, '.', '')."',
         '".number_format($manual_discount,   2, '.', '')."',
         '".number_format($total_fault_amount,2, '.', '')."',
         '".number_format($final_amount,      2, '.', '')."',
         '$verify_status','$status',
         '$addtime','$edittime',
         '".intval($userid)."'
        )";


    $ok = $db->query($sql);

// 统一判断是否为 AJAX 请求（前端会加 X-Requested-With 头）
    $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if(!$ok) {
        if($is_ajax) {
            // AJAX：返回 JSON，不跳转
            echo json_encode(array(
                'success' => false,
                'message' => '保存报价失败：'.$db->errno.' '.$db->error,
            ));
            exit;
        } else {
            // 普通表单：保持原有 message 行为
            message('保存报价失败：'.$db->errno.' '.$db->error);
        }
    }

// ====== 提交成功后：给当前浏览器建立报价系统的登录态 ======
// 这里不要再 new dsession() 了，前面框架已经启动过 session
    $_SESSION['quote_userid'] = intval($userid);
    $_SESSION['quote_email']  = $email;

    // ====== 提交成功后：通知管理员
    $itemid = $db->insert_id();

    // ====== 提交成功后：通知管理员 + 门店 ======
    $itemid = $db->insert_id();

    // 门店信息
    $company_name  = '';
    $company_email = '';
    if($company_id) {
        $c = $db->get_one("SELECT company,mail FROM {$table_company} WHERE userid={$company_id}");
        if($c) {
            $company_name  = $c['company'];
            $company_email = $c['mail'];
        }
    }

    // 管理员邮箱：从 member 表中 userid=1 读取
    $admin_email  = '';
    $table_member = $DT_PRE.'member';
    $m = $db->get_one("SELECT email FROM {$table_member} WHERE userid=1");
    if($m && !empty($m['email'])) {
        $admin_email = $m['email'];
    }

    // 组装邮件内容
    $customer_name = $first_name.$last_name;
    $subject = '【报价提交】'.$customer_name.' 的维修报价';

    $content = "有新的维修报价提交：\n\n".
        "报价ID：{$itemid}\n".
        "客户：{$customer_name}\n".
        "邮箱：{$email}\n".
        "手机：{$mobile}\n".
        "门店：".($company_name ? $company_name : '未选择')."\n".
        "故障原价合计：".number_format($total_fault_amount)." 円\n".
        "优惠后报价：".number_format($final_amount)." 円\n".
        "提交时间：".timetodate($addtime, 5)."\n\n".
        "请登录后台在报价管理中查看详情。";

    // === 改为入队列，不在这里直接发邮件 ===
    $queue_table = $DT_PRE.'quote_mail_queue';
    $now_time    = $DT_TIME;

    // 前台提交报价 → 通知管理员/门店
    // 建议 type 取一个前台专用值，例如 3
    $mail_type = 3;

    // 把原来的内容当成“上半段正文”存进去
    $subject_sql = addslashes($subject);
    $top_sql     = addslashes($content);
    $middle_sql  = '';
    $bottom_sql  = '';

    // 金额：用当前表单里的 final_amount，保留两位小数
    $amount_val = floatval($final_amount);
    if($amount_val < 0) $amount_val = 0;
    $amount_sql = number_format($amount_val, 2, '.', '');

    // 按原来逻辑：一封给管理员，一封给门店（如果有且和管理员邮箱不同）
    if($admin_email) {
        $to_email_sql = addslashes($admin_email);
        $to_name_sql  = addslashes('管理员');

        $db->query("INSERT INTO {$queue_table}
                                (itemid, type, to_email, to_name, subject,
                                 body_top, body_middle_prefix, body_bottom,
                                 amount, status, try_times, addtime, sendtime, last_error)
                                VALUES
                                ({$itemid}, {$mail_type}, '{$to_email_sql}', '{$to_name_sql}', '{$subject_sql}',
                                 '{$top_sql}', '{$middle_sql}', '{$bottom_sql}',
                                 '{$amount_sql}', 0, 0, {$now_time}, 0, '')");
    }

    if($company_email && $company_email != $admin_email) {
        $to_email_sql = addslashes($company_email);
        $to_name_sql  = addslashes($company_name ? $company_name : '门店');

        $db->query("INSERT INTO {$queue_table}
                                (itemid, type, to_email, to_name, subject,
                                 body_top, body_middle_prefix, body_bottom,
                                 amount, status, try_times, addtime, sendtime, last_error)
                                VALUES
                                ({$itemid}, {$mail_type}, '{$to_email_sql}', '{$to_name_sql}', '{$subject_sql}',
                                 '{$top_sql}', '{$middle_sql}', '{$bottom_sql}',
                                 '{$amount_sql}', 0, 0, {$now_time}, 0, '')");
    }


// 成功
    if($is_ajax) {
        // AJAX：返回 JSON，前端自行弹窗+倒计时+跳转
        echo json_encode(array(
            'success' => true,
            'message' => '提交成功，我们会在2个小时内联系您。',
        ));
        exit;
    } else {
        // 兜底：如果不是 AJAX，保持原来的 message 行为
        message('提交成功！我们会在2个小时内通过邮箱发送报价确认信息。');
    }

}

// ===================================================
// 二、展示报价表单：从 list-4 跳转过来
// 条件：POST 且存在非空 devices
// ===================================================
if(isset($_POST['devices']) && $_POST['devices'] !== '') {

    $devices_raw = stripslashes(trim($_POST['devices']));

    if($devices_raw == '') {
        message('设备数据为空，请返回重新选择');
    }

    $input_devices = json_decode($devices_raw, true);
    if(!$input_devices || !is_array($input_devices)) {
        message('设备数据格式错误，请返回重新选择');
    }

    // 预期结构：
    // $input_devices = [
    //   "205" => ["name" => "iPhone 16", "faults" => ["1","2","3"]],
    //   "210" => ["name" => "iPhone 15", "faults" => ["1","3"]]
    // ];

    // 1）收集 catid，并查询分类表（带 color & fua）
    $catids_int = array();
    foreach($input_devices as $catid_key => $d) {
        $cid = intval($catid_key);
        if($cid) $catids_int[] = $cid;
    }
    $catids_int = array_unique($catids_int);
    if(!$catids_int) {
        message('设备数据格式错误，请返回重新选择');
    }
    $catids_str = implode(',', $catids_int);

    // 取分类：型号名 catname + 颜色 color + 故障信息 fua
    $cats = array();
    $result = $db->query("SELECT catid,catname,color,fua FROM {$table_cat} WHERE catid IN($catids_str)");
    while($r = $db->fetch_array($result)) {
        $cats[$r['catid']] = $r;
    }

    // 2）重新组装 devices 结构：把 list-4 选中的故障 num，映射到 fua 里的 name+price
    $devices        = array();
    $color_options  = array();
    $model_catids   = array(); // 后面隐藏字段用

    foreach($input_devices as $catid_key => $d) {
        $catid = intval($catid_key);
        if(!$catid || !isset($cats[$catid])) continue;
        $cat = $cats[$catid];

        $device_name = $cat['catname'];

        // 颜色选项：dt_category.color 逗号分隔
        $color_arr = array();
        if(!empty($cat['color'])) {
            $tmp = explode(',', $cat['color']);
            foreach($tmp as $c) {
                $c = trim($c);
                if($c !== '') $color_arr[] = $c;
            }
        }
        if($color_arr) $color_options[$catid] = $color_arr;

        // 故障信息：dt_category.fua 是 JSON，结构类似：
        // [
        //   {"bi":"543534","num":"4","ming":"%E5%A4%A7%E6%96%B9"},
        //   ...
        // ]
        $fault_map = array();
        if(!empty($cat['fua'])) {
            $fua_arr = json_decode($cat['fua'], true);
            if($fua_arr && is_array($fua_arr)) {
                foreach($fua_arr as $row) {
                    $num  = isset($row['num']) ? (string)$row['num'] : '';
                    if($num === '') continue;
                    $ming_raw = isset($row['ming']) ? $row['ming'] : '';
                    $ming     = urldecode($ming_raw);
                    $price    = isset($row['bi']) ? floatval($row['bi']) : 0;

                    $fault_map[$num] = array(
                        'name'  => $ming,
                        'price' => $price,
                    );
                }
            }
        }

        // 用户在 list-4 选中的 num 列表
        $fault_nums = (isset($d['faults']) && is_array($d['faults'])) ? $d['faults'] : array();
        $faults     = array();
        foreach($fault_nums as $num) {
            $num = (string)$num;
            if(isset($fault_map[$num])) {
                $faults[] = array(
                    'num'   => $num,
                    'name'  => $fault_map[$num]['name'],
                    'price' => $fault_map[$num]['price'],
                );
            }
        }

        if(!$faults) continue; // 如果这个型号没有有效故障，就跳过

        $devices[$catid] = array(
            'name'   => $device_name,
            'faults' => $faults,
        );
        $model_catids[] = $catid;
    }

    if(!$devices) {
        message('没有有效的设备故障数据，请返回重新选择');
    }

    // 3）准备给模板的变量
    $devices_detail_json = json_encode($devices);        // 给隐藏字段 devices_detail
    $model_catids_str    = implode(',', $model_catids);  // 给隐藏字段 model_catids

    // 4）读取公司列表（userid/company/areaid/thumb/linkurl/validated）
    $companies = array();
    $result = $db->query("SELECT userid,company,areaid,thumb,linkurl,validated FROM {$table_company} WHERE company<>'' ORDER BY userid DESC");
    while($r = $db->fetch_array($result)) {
        $companies[] = $r;
    }

    // 5）进入模板
    // 模板里会用到：
    // - $devices             设备+故障明细（展示）
    // - $color_options       每个设备可选颜色
    // - $companies           公司列表 + 地区
    // - $devices_detail_json 隐藏字段，二次提交用
    // - $model_catids_str    隐藏字段，后续扩展用
    $title = '设备故障报价';
    include template('quote-form', 'article');
    exit;
}

// 既没有 devices_detail，也没有 devices，说明入口不对
message('参数错误，请从设备选择页面重新进入');
