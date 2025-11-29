<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
$itemid or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if(!check_group($_groupid, $MOD['group_talent'])) include load('403.inc');
($MOD['jid'] && isset($MODULE[$MOD['jid']]) && $MODULE[$MOD['jid']]['module'] == 'job') or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
$table_talent = $DT_PRE.'job_talent_'.$MOD['jid'];
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
$item or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
if($item['status'] != 3 || $item['open'] != 3) message($L['msg_resume_close'], $DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
$linkurl = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$item['linkurl'];
if($item['username'] == $_username) message($L['msg_add_self'], $linkurl);
$item = $db->get_one("SELECT * FROM {$table_talent} WHERE resumeid=$itemid AND username='$_username'");
if($item) message($L['msg_talent_exist'], $linkurl);
$db->query("UPDATE {$table} SET messages=messages+1 WHERE itemid=$itemid");
$db->query("INSERT INTO {$table_talent} (resumeid,username,jointime) VALUES ('$itemid','$_username','$DT_TIME')");
message($L['msg_talent_success'], $linkurl);
?>