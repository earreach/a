<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$itemid or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
$A = $db->get_one("SELECT * FROM {$table_answer} WHERE itemid=$itemid");
$A or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
$qid = $A['qid'];
$T = $db->get_one("SELECT * FROM {$table} WHERE itemid=$qid");
($T && $T['status'] == 3) or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
if($MOD['answer_pagesize']) $pagesize = $MOD['answer_pagesize'];
if($T['aid'] == $itemid) {
	$page = 1;
} else {
	$page = $A['fid'] ? ceil($A['fid']/$pagesize) : ceil(($T['answer'] + 1)/$pagesize);
}
$linkurl = $page == 1 ? $T['linkurl'] : itemurl($T, $page);
dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$linkurl.'#H'.$itemid);
?>