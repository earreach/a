<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
if($DT['baidu_push']) {
	if(isset($item) && $item['hits'] == 10 && $item['linkurl'] && strpos($item['linkurl'], '://') === false) {
		baidu_push($MOD['linkurl'].$item['linkurl'], $DT['baidu_push']);
		if($EXT['mobile_enable']) baidu_push($MOD['mobile'].$item['linkurl'], $DT['baidu_push']);
	}
}
if($DT_BOT) return;
if($DT['history_module'] && isset($item)) history_log($item, $moduleid, $DT['history_module']);
if($page == 1 && $MOD['hits']) {
	if($DT['cache_hits']) {
		 cache_hits($moduleid, $itemid);
	} else {
		$update .= ',hits=hits+1';
	}
}
if($update) $db->query("UPDATE LOW_PRIORITY {$table} SET ".substr($update, 1)." WHERE itemid=$itemid", 'UNBUFFERED');	
?>