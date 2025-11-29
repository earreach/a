<?php
defined('IN_DESTOON') or exit('Access Denied');
function item_moburl($url, $mid) {
	global $MODULE;
	if(strpos($url, DT_PATH) !== false) return str_replace(DT_PATH, DT_MOB, $url);
	if($mid) return str_replace($MODULE[$mid]['linkurl'], $MODULE[$mid]['mobile'], $url);
	foreach($MODULE as $m) {
		if(strpos($url, $m['linkurl']) !== false) return str_replace($m['linkurl'], $m['mobile'], $url);
	}
	return $url;
}
?>