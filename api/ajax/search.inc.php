<?php
defined('IN_DESTOON') or exit('Access Denied');
(isset($MODULE[$mid]) && $mid > 3) or exit;
if($job == 'hot') {
	tag("moduleid=$mid&table=keyword&condition=moduleid=$mid and status=3&pagesize=10&order=total_search desc&template=list-search-kw");
} else if($job == 'del') {
	if($_userid) $db->query("DELETE FROM {$DT_PRE}keyword_record WHERE moduleid=$mid AND username='$_username'");
	exit('ok');
} else if($job == 'tip') {
	if(!$DT['search_tips']) exit;
	if(!$word || strlen($word) < 1 || strlen($word) > 30) exit;
	foreach(array('&', '=', '(', ',') as $v) {
		strpos($word, $v) === false or exit;
	}
	$word = str_replace(array(' ','*', "\'"), array('%', '%', ''), $word);
	if(preg_match("/^[a-z0-9A-Z]+$/", $word)) {			
		tag("moduleid=$mid&table=keyword&condition=moduleid=$mid and letter like '%$word%'&pagesize=10&order=total_search desc&template=list-search-tip", -2);
	} else {
		tag("moduleid=$mid&table=keyword&condition=moduleid=$mid and keyword like '%$word%'&pagesize=10&order=total_search desc&template=list-search-tip", -2);
	}
} else {
	$lists = $tags = array();
	if($_userid) {
		$result = $db->query("SELECT keyword FROM {$DT_PRE}keyword_record WHERE moduleid=$mid AND username='$_username' ORDER BY addtime DESC LIMIT 30");
		while($r = $db->fetch_array($result)) {
			if(in_array($r['keyword'], $lists) || !$r['keyword']) continue;
			$lists[] = $r['keyword'];
		}
	}
	$result = $db->query("SELECT keyword FROM {$DT_PRE}keyword WHERE moduleid=$mid AND status=3 ORDER BY total_search DESC LIMIT 20", 'CACHE');
	while($r = $db->fetch_array($result)) {
		if(in_array($r['keyword'], $tags) || !$r['keyword']) continue;
		$tags[] = $r['keyword'];
	}
	include template('search', 'chip');
}
?>