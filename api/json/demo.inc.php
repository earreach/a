<?php
defined('IN_DESTOON') or exit('Access Denied');
#JSON数据输出示例
$lists = array();
$result = $db->query("SELECT itemid,title FROM {$DT_PRE}webpage ORDER BY listorder");
while($r = $db->fetch_array($result)) {
	$lists[] = $r;
}
echo json_encode($lists);
?>