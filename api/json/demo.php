<?php
#JSON数据输出示例
require '../../common.inc.php';
$lists = array();
$result = $db->query("SELECT itemid,title FROM {$DT_PRE}webpage ORDER BY listorder");
while($r = $db->fetch_array($result)) {
	$lists[] = $r;
}
echo json_encode($lists);
?>