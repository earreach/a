<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/include/module.func.php';
(isset($username) && check_name($username)) or $username = '';
$member = userinfo($username);
if($member) include template('usercard', 'chip');
?>