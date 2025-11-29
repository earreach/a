<?php
defined('IN_DESTOON') or exit('Access Denied');
$_userid or exit('请登录');
$mid = intval($mid);
$rid = isset($rid) ? intval($rid) : 0;
if($mid > 2) {
	if($rid) {
		if($MODULE[$mid]['module'] == 'moment') {
			$table = get_table($mid);		
			$id = $itemid;
			$rid = 0;
		} else {
			$itemid = 0;
			if($mid == 3) {
				$table = $DT_PRE.'comment';
			} else if($MODULE[$mid]['module'] == 'know') {
				$table = $DT_PRE.'know_answer_'.$mid;
			} else if($MODULE[$mid]['module'] == 'club') {
				$table = $DT_PRE.'club_reply_'.$mid;
			}
			$id = $rid;
		}
	} else {
		$table = get_table($mid);		
		$id = $itemid;
	}
	$fd = $mid == 4 ? 'userid' : 'itemid';
	if($table) {
		$tid = $itemid;
		$kid = $_userid.'-'.$mid.'-'.$tid.'-'.$rid;
		$t = $db->get_one("SELECT itemid,hate FROM {$DT_PRE}like_record WHERE kid='$kid' LIMIT 0,1", 'CACHE');
		if($t) {
			$dc->remove("SELECT itemid,hate FROM {$DT_PRE}like_record WHERE kid='$kid' LIMIT 0,1");//移除缓存
			if($t['hate']) {
				if($job == 'hate') {//取消反对
					$db->query("DELETE FROM {$DT_PRE}like_record WHERE itemid=$t[itemid]");
					$db->query("UPDATE LOW_PRIORITY {$table} SET hates=hates-1 WHERE `{$fd}`=$id AND hates>0", 'UNBUFFERED');
					exit('ko');
				} else {//改为点赞
					$db->query("UPDATE {$DT_PRE}like_record SET hate=0 WHERE itemid=$t[itemid]");
					$db->query("UPDATE LOW_PRIORITY {$table} SET hates=hates-1 WHERE `{$fd}`=$id AND hates>0", 'UNBUFFERED');
					$db->query("UPDATE LOW_PRIORITY {$table} SET likes=likes+1 WHERE `{$fd}`=$id", 'UNBUFFERED');
					exit('ok0');
				}
			} else {
				if($job == 'hate') {//改为反对
					$db->query("UPDATE {$DT_PRE}like_record SET hate=1 WHERE itemid=$t[itemid]");
					$db->query("UPDATE LOW_PRIORITY {$table} SET hates=hates+1 WHERE `{$fd}`=$id", 'UNBUFFERED');
					$db->query("UPDATE LOW_PRIORITY {$table} SET likes=likes-1 WHERE `{$fd}`=$id AND likes>0", 'UNBUFFERED');
					exit('ok1');
				} else {//取消点赞
					$db->query("DELETE FROM {$DT_PRE}like_record WHERE itemid=$t[itemid]");
					$db->query("UPDATE LOW_PRIORITY {$table} SET likes=likes-1 WHERE `{$fd}`=$id AND likes>0", 'UNBUFFERED');
					exit('ko');
				}
			}
		}
		$hate = $job == 'hate' ? 1 : 0;
		$key = $hate ? 'hates' : 'likes';
		$db->query("INSERT INTO {$DT_PRE}like_record (kid,mid,tid,rid,hate,username,addtime) VALUES ('$kid','$mid','$tid','$rid','$hate','$_username','$DT_TIME')");
		$db->query("UPDATE LOW_PRIORITY {$table} SET `{$key}`=`{$key}`+1 WHERE `{$fd}`=$id", 'UNBUFFERED');
		exit('ok');
	}
}
exit('参数错误');
?>