<?php
// module/article/quote_my.inc.php
// “我的报价列表”：只显示当前登录用户（验证码登录/会员登录）的报价记录

defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';

global $db, $DT_PRE, $DT_TIME, $MODULE, $MOD, $_userid;

// 报价表：dt_article_quote_21
$table = $DT_PRE.'article_quote_'.$moduleid;

// 1. 取当前用户（优先用验证码登录的 quote_userid，其次用正常会员登录的 $_userid）
$session = new dsession();
$quote_userid = isset($_SESSION['quote_userid']) ? intval($_SESSION['quote_userid']) : 0;

if(!$quote_userid && !empty($_userid)) {
    $quote_userid = intval($_userid);
}

if(!$quote_userid) {
    // 这里你可以改成 dheader() 跳转到“验证码登录入口页”
    message('请先通过邮箱验证码登录后再查看报价记录');
}

// 2. 分页参数
$page     = isset($page) ? intval($page) : 1;
if($page < 1) $page = 1;

$pagesize = 20;
$offset   = ($page - 1) * $pagesize;

// 3. 条件：只看当前用户的报价
$condition = "userid={$quote_userid}";

// 4. 统计总数
$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
$items = $r ? intval($r['num']) : 0;
$pages = pages($items, $page, $pagesize);

$lists = array();

// 5. 取列表
if($items > 0) {
    $result = $db->query("SELECT itemid, addtime, edittime,
                                 status, user_confirm_status, repair_status,
                                 total_fault_amount, final_amount,
                                 receipt_token
                          FROM {$table}
                          WHERE {$condition}
                          ORDER BY addtime DESC
                          LIMIT {$offset},{$pagesize}");
    while($row = $db->fetch_array($result)) {
        // 时间
        $row['adddate']  = timetodate($row['addtime'], 5);
        $row['editdate'] = $row['edittime'] ? timetodate($row['edittime'], 5) : '';

        // 审核状态
        $status = isset($row['status']) ? intval($row['status']) : 0;
        switch($status) {
            case 1:  $row['status_text'] = '审核通过'; break;
            case 2:  $row['status_text'] = '审核未通过'; break;
            default: $row['status_text'] = '待审核'; break;
        }

        // 用户确认状态
        $u = isset($row['user_confirm_status']) ? intval($row['user_confirm_status']) : 0;
        switch($u) {
            case 1:  $row['user_confirm_text'] = '已接受报价'; break;
            case 2:  $row['user_confirm_text'] = '已拒绝报价'; break;
            default: $row['user_confirm_text'] = '未确认'; break;
        }

        // 维修状态
        $repair_map = array(
            0 => '未处理',
            1 => '维修中',
            2 => '已维修',
            3 => '已返还',
            4 => '已取消',
        );
        $rs = isset($row['repair_status']) ? intval($row['repair_status']) : 0;
        $row['repair_status_text'] = isset($repair_map[$rs]) ? $repair_map[$rs] : '状态'.$rs;

        // 金额（格式化为字符串，避免模板里再写 number_format）
        $row['total_fault_amount'] = isset($row['total_fault_amount']) ? floatval($row['total_fault_amount']) : 0.00;
        $row['final_amount']       = isset($row['final_amount'])       ? floatval($row['final_amount'])       : 0.00;

        $row['total_fault_amount_str'] = number_format($row['total_fault_amount'], 2);
        $row['final_amount_str']       = number_format($row['final_amount'], 2);

        $lists[] = $row;
    }
}
//var_dump($MODULE);
//die();
// 页面标题
$head_title = '我的报价';

// 先用 quote_my 这个模板名，第二步我们再把样式做漂亮
include template('quote_my', $module);
