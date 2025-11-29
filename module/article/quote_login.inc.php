<?php
// module/article/quote_login.inc.php
// 用邮箱验证码登录，查看“我的报价”

defined('IN_DESTOON') or exit('Access Denied');

require DT_ROOT.'/module/'.$module.'/common.inc.php';

// 标题简单点，避免 Undefined index: name 之类的 Notice
$head_title = '查看我的报价';

include template('quote_login', $module);
