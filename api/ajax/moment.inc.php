<?php
defined('IN_DESTOON') or exit('Access Denied');
$module == 'moment' or exit;
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($job == 'topic') {
	$condition = "1";
	if($keyword) $condition .= match_kw('title', $keyword);
	$lists = array();
	$result = $db->query("SELECT itemid,title,hits,item FROM {$table_topic} WHERE {$condition} ORDER BY itemid DESC,catid DESC LIMIT 10");
	while($r = $db->fetch_array($result)) {
		$lists[] = $r;
	}
	echo json_encode($lists);
} else if($job == 'followed') {
	$userid = isset($userid) ? intval($userid) : 0;
	if($_userid < 1 || $_userid == $userid) exit('ko');
	echo followed($userid) ? 'ok' : 'ko';
} else if($job == 'more') {
	$itemid or exit;
	if($_groupid == 1) {
		//
	} else {
		$m = $db->get_one("SELECT userid,open,status FROM {$table} WHERE itemid=$itemid");
		if($m) {
			if($_userid && $_userid == $m['userid']) {
				//
			} else {
				if($m['status'] != 3 || $m['open'] == 0) exit;
				if($m['open'] == 2 && !followed($m['userid'])) exit;
			}
		} else {
			exit;
		}
	}
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t ? nl2br($t['content']) : '';
	if(strpos($content, ')') !== false) $content = parse_face($content);
	if($DT_PC) {
		//
	} else {
		require DT_ROOT.'/include/content.class.php';
		$content = DC::format($content, 0);
	}
	echo $content;
}
?>