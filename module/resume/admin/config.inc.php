<?php
defined('DT_ADMIN') or exit('Access Denied');
$MCFG['module'] = 'resume';
$MCFG['name'] = '简历';
$MCFG['author'] = 'DESTOON';
$MCFG['homepage'] = 'www.destoon.com';
$MCFG['copy'] = true;
$MCFG['uninstall'] = true;
$MCFG['moduleid'] = 0;

$RT = array();
$RT['file']['index'] = '简历管理';
$RT['file']['html'] = '更新网页';

$RT['action']['index']['add'] = '添加简历';
$RT['action']['index']['edit'] = '修改简历';
$RT['action']['index']['delete'] = '删除简历';
$RT['action']['index']['check'] = '审核简历';
$RT['action']['index']['expire'] = '过期简历';
$RT['action']['index']['reject'] = '未通过';
$RT['action']['index']['recycle'] = '回收站';
$RT['action']['index']['move'] = '移动简历';
$RT['action']['index']['level'] = '信息级别';
$CT = 1;
?>