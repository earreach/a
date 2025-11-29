<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/api/pay/'.$bank.'/'.($PAY[$bank]['public'] ? 'rsa2' : 'md5').'/receive.inc.php';
?>