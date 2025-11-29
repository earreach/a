<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
if($DT['close']) {
	if($DT_BOT) dhttp(503);
	message($DT['close_reason'].'&nbsp;');
}
if($DT['defend_cc']) {
	if(!DT_WIN && file_exists('/proc/loadavg')) {
		if($fp = @fopen('/proc/loadavg', 'r')) {
			list($loadaverage) = explode(' ', fread($fp, 6));
			fclose($fp);
			if(dround($loadaverage) > $DT['defend_cc']) {
				header("HTTP/1.0 503 Service Unavailable");
				exit(include(DT_ROOT.'/api/503.php'));
			}
		}
	}
}
?>