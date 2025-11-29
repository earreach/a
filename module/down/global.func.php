<?php
defined('IN_DESTOON') or exit('Access Denied');
function ext_select($name, $value, $extend = '', $title = '') {
	include DT_ROOT.'/file/config/filetype.inc.php';
	if(!$value && !$title) $value = 'oth';
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value=""'.('' == $value ? ' selected' : '').'>'.$title.'</option>';
	foreach($FILETYPE as $k=>$v) {
		$select .= '<option value="'.$k.'"'.($k == $value ? ' selected' : '').'>'.$v.'</option>';
	}
	$select .= '</select>';
	return $select;
}

function unit_select($name, $value, $extend = '') {
	$UNIT = array('K', 'M', 'G', 'T');
	$value or $value = 'M';
	$select = '<select name="'.$name.'" '.$extend.'>';
	foreach($UNIT as $k=>$v) {
		$select .= '<option value="'.$v.'"'.($v == $value ? ' selected' : '').'>'.$v.'</option>';
	}
	$select .= '</select>';
	return $select;
}

function albumlist($item) {
	global $table;
	$tags = array();
	$j = 0;
	$index = 1;
	if($item['album'] && $item['username']) {
		$result = DB::query("SELECT * FROM {$table} WHERE album='".addslashes($item['album'])."' AND username='$item[username]' AND status=3 ORDER BY addtime DESC LIMIT 1000", 'CACHE');
		while($r = DB::fetch_array($result)) {
			$j++;
			if($r['itemid'] == $item['itemid']) $index = $j;
			$tags[] = $r;
		}
	}
	return array($tags, $index, count($tags));
}
?>