<?php
defined('IN_DESTOON') or exit('Access Denied');
function get_group($gid) {
	global $table_group;
	return $gid > 0 ? DB::get_one("SELECT * FROM {$table_group} WHERE itemid=$gid") : array();
}

function list_group($tags) {
	global $MOD, $DT_PC, $table_group;
	$key = 'gid';
	if(!$tags || !isset($tags[0][$key])) return $tags;
	$arr1 = $arr2 = $arr3 = array();
	foreach($tags as $k=>$v) {
		if(!$v[$key]) continue;
		$arr1[$k] = $v[$key];
		$arr2[$v[$key]] = $v[$key];
	}
	if(!$arr2) return $tags;
	$condition = "itemid IN (".implode(',', $arr2).")";
	$result = DB::query("SELECT itemid,title,thumb,post,fans,linkurl FROM {$table_group} WHERE {$condition}");
	while($r = DB::fetch_array($result)) {
		$r['linkurl'] = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$r['linkurl'];
		$arr3[$r['itemid']] = $r;
	}
	foreach($arr1 as $k=>$v) {
		if(!isset($arr3[$v])) continue;
		foreach($arr3[$v] as $kk=>$vv) {
			if($kk == 'itemid') continue;
			$tags[$k][$kk] = $vv;
		}
	}
	return $tags;
}

function is_fans($GRP) {
	global $table_fans, $_username, $_passport;
	if($_username) {
		if($GRP['username'] == $_username) return true;
		if($GRP['manager'] && in_array($_passport, explode('|', $GRP['manager']))) return true;
		$t = DB::get_one("SELECT * FROM {$table_fans} WHERE gid=$GRP[itemid] AND username='$_username' AND status=3");
		if($t) return $t;
	}
	return false;
}

function is_admin($GRP) {
	global $_admin, $_username, $_passport;
	if($_username) {
		if($_admin == 1) return 'admin';
		if($GRP['username'] == $_username) return 'founder';
		if($GRP['manager'] && in_array($_passport, explode('|', $GRP['manager']))) return 'manager';
	}
	return '';
}

function last_gid() {
	global $_username, $table, $table_group, $table_fans, $table_reply;
	$t = DB::get_one("SELECT gid FROM {$table} WHERE username='$_username' ORDER BY itemid DESC");
	if($t) return $t['gid'];
	$t = DB::get_one("SELECT itemid FROM {$table_group} WHERE status=3 AND username='$_username' ORDER BY itemid DESC");
	if($t) return $t['itemid'];
	$t = DB::get_one("SELECT gid FROM {$table_fans} WHERE username='$_username' ORDER BY itemid DESC");
	if($t) return $t['gid'];
	$t = DB::get_one("SELECT gid FROM {$table_reply} WHERE username='$_username' ORDER BY itemid DESC");
	if($t) return $t['gid'];
	$t = DB::get_one("SELECT itemid FROM {$table_group} WHERE status=3 ORDER BY itemid DESC");
	if($t) return $t['itemid'];
	return 0;
}
?>