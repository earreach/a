<?php
defined('DT_ADMIN') or exit('Access Denied');
$MCFG['module'] = 'company';
$MCFG['name'] = '公司';
$MCFG['author'] = 'DESTOON';
$MCFG['homepage'] = 'www.destoon.com';
$MCFG['copy'] = false;
$MCFG['uninstall'] = false;

$RT = array();
$RT['file']['index'] = '公司列表';
$RT['file']['html'] = '更新网页';

$RT['action']['index']['add'] = '开通'.VIP;
$RT['action']['index']['edit'] = '修改'.VIP;
$RT['action']['index']['renew'] = '续费'.VIP;
$RT['action']['index']['delete'] = '撤销'.VIP;
$RT['action']['index']['vip'] = VIP.'列表';
$RT['action']['index']['record'] = VIP.'记录';
$RT['action']['index']['move'] = '移动地区';
$RT['action']['index']['update'] = '更新指数';

$CT = false;
?>