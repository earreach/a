<?php
defined('IN_DESTOON') or exit('Access Denied');
if(!preg_match("/^[0-9]{6}$/", $wxcode)) exit('1');
$db->query("DELETE FROM {$DT_PRE}weixin_code WHERE addtime<$DT_TIME-600");
$t = $db->get_one("SELECT * FROM {$DT_PRE}weixin_code WHERE code='$wxcode'");
if(!$t) exit('3');
exit('0');
?>