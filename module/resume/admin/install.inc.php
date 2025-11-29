<?php
defined('DT_ADMIN') or exit('Access Denied');
$setting = include(DT_ROOT.'/file/setting/module-19.php');
update_setting($moduleid, $setting);
$sql = file_get(DT_ROOT.'/file/setting/table_'.$module.'.php');
if(substr($sql, 0, 13) == '<?php exit;?>') $sql = trim(substr($sql, 13));
$sql = str_replace('_19', '_'.$moduleid, $sql);
$sql = str_replace('简历', $modulename, $sql);
sql_execute($sql);
include DT_ROOT.'/module/'.$module.'/admin/remkdir.inc.php';
?>