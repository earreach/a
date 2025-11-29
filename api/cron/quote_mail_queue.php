<?php
// api/cron/quote_mail_queue.php
// 简单邮件队列发送脚本

// 标记为任务脚本（可选）
define('DT_TASK', true);

// 根据你站点实际目录调整这个路径（这里假设在 api/cron/ 下面）
require dirname(__DIR__, 2).'/common.inc.php';
// require '../../common.inc.php';

// 如有需要可以单独引入邮件函数（有的版本 common.inc.php 已经包含了）
if(!function_exists('send_mail')) {
    @include DT_ROOT.'/include/mail.func.php';
}

$queue_table = $DT_PRE.'quote_mail_queue';
$moduleid    = 21;
$quote_table = $DT_PRE.'article_quote_'.$moduleid; // dt_article_quote_21

// 每次最多处理多少条
$limit = 10;

// 取待发送的队列记录（status=0，尝试次数<3）
$result = $db->query("SELECT * FROM {$queue_table}
                      WHERE status=0 AND try_times<3
                      ORDER BY addtime ASC, id ASC
                      LIMIT {$limit}");

while($q = $db->fetch_array($result)) {
    $qid    = intval($q['id']);
    $itemid = intval($q['itemid']);

    // 先把尝试次数 +1，避免死循环
    $db->query("UPDATE {$queue_table}
                SET try_times = try_times + 1
                WHERE id = {$qid}");

    // 取对应的报价记录
    $r = $db->get_one("SELECT * FROM {$quote_table} WHERE itemid={$itemid}");
    if(!$r || empty($q['to_email'])) {
        $db->query("UPDATE {$queue_table}
                    SET status=2,
                        last_error='quote not found or empty email',
                        sendtime={$DT_TIME}
                    WHERE id={$qid}");
        continue;
    }

    // 模块配置，用于生成回执链接
    if(empty($MOD) || $MOD['moduleid'] != $moduleid) {
        $MOD = cache_read('module-'.$moduleid.'.php');
    }

    // 确保有 receipt_token
    $receipt_token = $r['receipt_token'];
    if(!$receipt_token) {
        $receipt_token = md5($DT_TIME.mt_rand(100000, 999999).$itemid.$r['email']);
        $db->query("UPDATE {$quote_table}
                    SET receipt_token='{$receipt_token}'
                    WHERE itemid={$itemid}");
    }

    // 回执链接
    $receipt_url = $MOD['linkurl'].'quote_view.php?itemid='.$itemid.'&token='.$receipt_token;

    // 标题
    $subject = trim($q['subject']);
    if($subject == '') $subject = '报价回执通知';

    // 金额（队列表里是 decimal，展示用整数 + 千分位）
    $amount_num = floatval($q['amount']);
    if($amount_num < 0) $amount_num = 0;
    $amount_int = round($amount_num);
    $amount_str = number_format($amount_int, 0);

    // 组装正文：上半段 + 报价行 + 下半段
    $content = '';

    if($q['body_top']) {
        $content .= nl2br(dhtmlspecialchars($q['body_top'])).'<br/><br/>';
    }

    $prefix = dhtmlspecialchars($q['body_middle_prefix']);
    if($prefix == '') $prefix = '当前报价金额为：';

    $content .= $prefix.'<strong>'.$amount_str.'</strong> 円<br/><br/>';

    if($q['body_bottom']) {
        $content .= nl2br(dhtmlspecialchars($q['body_bottom'])).'<br/><br/>';
    }

    // 管理员备注（来自报价主表）
    if(!empty($r['admin_note'])) {
        $content .= '管理员备注：<br/>'
                 . nl2br(dhtmlspecialchars($r['admin_note'])).'<br/><br/>';
    }

    // 报价回执链接
    $content .= '请点击下面的链接查看详细报价回执：<br/>'
             .  '<a href="'.$receipt_url.'" target="_blank">'.$receipt_url.'</a><br/>';

    // 真正发信
    $ok = false;
    if(function_exists('send_mail')) {
        $ok = send_mail($q['to_email'], $subject, $content, $q['to_name']);
    }

    // 当前这条队列记录的类型
    $type = isset($q['type']) ? intval($q['type']) : 0;

    if($ok) {
        // 队列表：标记发送成功
        $db->query("UPDATE {$queue_table}
                    SET status=1,
                        sendtime={$DT_TIME},
                        last_error=''
                    WHERE id={$qid}");

        // 只有“发给客户的报价邮件”（type=1=首次，2=调整后）才计入报价次数 & 最后发送时间
        if($type == 1 || $type == 2) {
            $db->query("UPDATE {$quote_table}
                        SET notify_sent=1,
                            receipt_send_count = receipt_send_count + 1,
                            last_receipt_send_time = {$DT_TIME}
                        WHERE itemid={$itemid}");
        }
    } else {
        $db->query("UPDATE {$queue_table}
                    SET status=2,
                        sendtime={$DT_TIME},
                        last_error='send_mail failed'
                    WHERE id={$qid}");
    }
}

exit('OK');
