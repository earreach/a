<?php
defined('IN_DESTOON') or exit('Access Denied');
$DT_MOB == 'ios' or exit;
if(get_cookie('mobile') != 'screen') set_cookie('mobile', 'screen');
?>