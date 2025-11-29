<?php
defined('IN_DESTOON') or exit('Access Denied');
$str = '';
if($DT['split_appcode'] && isset($text)) $str = cloud_split($text, $DT['split_appcode']);
echo $str;
?>