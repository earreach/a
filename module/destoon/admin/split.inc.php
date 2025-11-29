<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$mid > 3 or msg();
$fd = $mid == 4 ? 'userid' : 'itemid';
$table = get_table($mid);
$table_data = get_table($mid, 1);
function table_exists($table) {
	$result = DB::query("SHOW COLUMNS FROM `{$table}`");
	while($r = DB::fetch_array($result)) {
		if($r) return true;
	}
	return false;
}
if($action == 'merge') {
	isset($num) or $num = 5000;
	if(!isset($fid)) {
		if(DB::version() > '4.1' && $CFG['db_charset']) {
			$type = " ENGINE=MyISAM DEFAULT CHARSET=".$CFG['db_charset'];
		} else {
			$type = " TYPE=MyISAM";
		}	
		DB::query("CREATE TABLE IF NOT EXISTS `{$table_data}` (`{$fd}` bigint(20) unsigned NOT NULL default '0',`content` mediumtext NOT NULL,PRIMARY KEY  (`{$fd}`))".$type." COMMENT='".$MODULE[$mid]['name']."内容'");
		$r = $db->get_one("SELECT MIN(`$fd`) AS fid FROM {$table}");
		$fid = $r['fid'] ? $r['fid'] : 0;
	}
	isset($sid) or $sid = $fid;
	if(!isset($tid)) {
		$r = $db->get_one("SELECT MAX(`$fd`) AS tid FROM {$table}");
		$tid = $r['tid'] ? $r['tid'] : 0;
		$part = split_id($tid);
		for($i = 1; $i <= $part; $i++) {
			split_content($mid, $i);
		}
	}
	if($fid <= $tid) {
		$result = $db->query("SELECT `$fd` FROM {$table} WHERE `$fd`>=$fid ORDER BY `$fd` LIMIT 0,$num");
		if($db->affected_rows($result)) {
			while($r = $db->fetch_array($result)) {
				$itemid = $r[$fd];
				$t = $db->get_one("SELECT content FROM ".split_table($mid, $itemid)." WHERE `$fd`=$itemid");
				if($t) {
					$content = addslashes($t['content']);
					$db->query("REPLACE INTO {$table_data} ($fd,content) VALUES ('$itemid','$content')");
				} else {
					$t = $db->get_one("SELECT `$fd` FROM {$table_data} WHERE `$fd`=$itemid");
					if(!$t) $db->query("REPLACE INTO {$table_data} ($fd,content) VALUES ('$itemid','')");
				}
			}
			$itemid += 1;
		} else {
			$itemid = $fid + $num;
		}
	} else {
		$db->halt = 0;
		$part = split_id($tid);
		for($i = 1; $i < $part+3; $i++) {
			$tb = $DT_PRE.$mid.'_'.$i;
			$db->query("DROP TABLE IF EXISTS `{$tb}`");
		}
		msg($MODULE[$mid]['name'].'内容合并成功，请保存模块设置<script>window.setTimeout(function(){window.parent.Dd("split_1").disabled=true;window.parent.Dd("split_0").checked=true;window.parent.cDialog();}, 2000);</script>');
	}
	msg('ID从'.$fid.'至'.($itemid-1).'合并成功'.progress($sid, $fid, $tid), "?mid=$mid&file=$file&action=$action&sid=$sid&fid=$itemid&tid=$tid&num=$num");
} else if($action == 'split') {
	isset($num) or $num = 5000;
	if(!isset($fid)) {
		table_exists($table_data) or msg('表'.$table_data.'不存在，请检查是否已经做过分表');
		$r = $db->get_one("SELECT MIN(`$fd`) AS fid FROM {$table}");
		$fid = $r['fid'] ? $r['fid'] : 0;
	}
	isset($sid) or $sid = $fid;
	if(!isset($tid)) {
		$r = $db->get_one("SELECT MAX(`$fd`) AS tid FROM {$table}");
		$tid = $r['tid'] ? $r['tid'] : 0;
		$part = split_id($tid);
		for($i = 1; $i < $part+2; $i++) {
			split_content($mid, $i);
		}
	}
	if($fid <= $tid) {
		$result = $db->query("SELECT `$fd` FROM {$table} WHERE `$fd`>=$fid ORDER BY `$fd` LIMIT 0,$num");
		if($db->affected_rows($result)) {
			while($r = $db->fetch_array($result)) {
				$itemid = $r[$fd];
				$t = $db->get_one("SELECT content FROM {$table_data} WHERE `$fd`=$itemid");
				if($t) {
					$content = addslashes($t['content']);
					$db->query("REPLACE INTO ".split_table($mid, $itemid)." ($fd,content) VALUES ('$itemid','$content')");
				} else {
					$t = $db->get_one("SELECT `$fd` FROM ".split_table($mid, $itemid)." WHERE `$fd`=$itemid");
					if(!$t) $db->query("REPLACE INTO ".split_table($mid, $itemid)." ($fd,content) VALUES ('$itemid','')");
				}
			}
			$itemid += 1;
		} else {
			$itemid = $fid + $num;
		}
	} else {
		$name = $MODULE[$mid]['name'].'内容分表备份';
		$table_back = $table_data.'_'.timetodate($DT_TIME, 'Ymd');
		$db->query("RENAME TABLE `{$table_data}` TO `{$table_back}`");
		$db->query("ALTER TABLE `{$table_back}` COMMENT='{$name}'");
		msg($MODULE[$mid]['name'].'内容拆分成功，请保存模块设置<script>window.setTimeout(function(){window.parent.Dd("split_1").checked=true;window.parent.Dd("split_0").disabled=true;window.parent.cDialog();}, 2000);</script>');
	}
	msg('ID从'.$fid.'至'.($itemid-1).'拆分成功'.progress($sid, $fid, $tid), "?mid=$mid&file=$file&action=$action&sid=$sid&fid=$itemid&tid=$tid&num=$num");
} else {
	$split = isset($split) && $split ? 1 : 0;
	include tpl('split');
}
?>