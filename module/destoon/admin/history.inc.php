<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$itemid or msg('记录不存在');
$cache = DT_ROOT.'/file/history/'.$mid.'/'.($action ? $action.'-' : '').$itemid.'.php';
if($mid > 4) {
	if($action) {
		$table = str_replace('_'.$mid, '_'.$action.'_'.$mid, get_table($mid));
		$table_data = '';
	} else {
		$table = get_table($mid);
		$table_data = get_table($mid, 1);
	}
	$csv = cutstr($table, $DT_PRE, '_'.$mid);
} else if($mid == 2) {
	$table = $DT_PRE.$action;
	$table_data = $DT_PRE.$action.'_data';
	$csv = $action;
}
is_file($cache) or msg('记录不存在');
$arr = unserialize(substr(file_get($cache), 13));
$arr or msg('记录不存在');
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
$item or msg('记录不存在');
$lists = $tags = $names = array();
$text = file_get(DT_ROOT.'/file/setting/dict_'.$csv.'.php');
if($text) {
	if(substr($text, 0, 13) == '<?php exit;?>') $text = trim(substr($text, 13));
	foreach(explode("\n", $text) as $v) {
		$t = explode(',', trim($v));
		if($t[0]) $names[$t[0]] = $t[1];
	}
} else {
	msg('记录不存在');
}
foreach($item as $k=>$v) {
	if(!isset($arr[$k])) continue;
	$o = $arr[$k];
	if(strlen($v) < 32) {
		if($o == $v) continue;
	} else {
		if(md5($o) == md5($v)) continue;
	}
	if(in_array($k, array('keyword', 'status', 'content'))) {
		continue;
	} else if($k == 'catid') {
		if($v) $v = cat_pos(get_cat($v), ' - ');
		if($o) $o = cat_pos(get_cat($o), ' - ');
	} else if($k == 'areaid') {
		if($v) $v = area_pos($v, ' - ');
		if($o) $o = area_pos($o, ' - ');
	} else if($k == 'thumb') {
		if(is_url($v)) $v = '<img src="'.$v.'" onerror="this.src=\''.DT_STATIC.'image/nopic100.png\'" onclick="_preview(this.src);" class="c_p"/>';
		if(is_url($o)) $o = '<img src="'.$o.'" onerror="this.src=\''.DT_STATIC.'image/nopic100.png\'" onclick="_preview(this.src);" class="c_p"/>';
	} else if($k == 'thumbs') {
		if($v) {
			$t = '';
			foreach(explode('|', $v) as $s) {
				if(is_url($s)) $t .= '<img src="'.$s.'" onerror="this.src=\''.DT_STATIC.'image/nopic100.png\'" onclick="_preview(this.src);" class="c_p" style="float:left;margin:0 10px 10px 0;"/>';
			}
			$v = $t;
		}
		if($o) {
			$t = '';
			foreach(explode('|', $o) as $s) {
				if(is_url($s)) $t .= '<img src="'.$s.'" onerror="this.src=\''.DT_STATIC.'image/nopic100.png\'" onclick="_preview(this.src);" class="c_p" style="float:left;margin:0 10px 10px 0;"/>';
			}
			$o = $t;
		}
	} else if(strpos($k, 'time') !== false && is_numeric($v)) {
		$v = timetodate($v, 6);
		$o = timetodate($o, 6);
	} else {
		if(is_url($v)) $v = '<a href="'.$v.'" target="_blank" class="t">'.$v.'</a>';
		if(is_url($o)) $o = '<a href="'.$o.'" target="_blank" class="t">'.$o.'</a>';
	}
	$name = isset($names[$k]) ? $names[$k] : $k;
	$lists[$k] = array('name' => $name, 'new' => $v, 'old' => $o);
}
$new = $old = '';
if(isset($arr['content'])) {
	if($table_data) {
		$t = $db->get_one("SELECT * FROM {$table_data} WHERE itemid=$itemid");
		if($t) {
			$new = $t['content'];
			$old = $arr['content'];
			if(md5($old) == md5($t['content'])) $new = '';
		}
	} else {
		if(isset($item['content'])) {
			$new = $item['content'];
			$old = $arr['content'];
			if(md5($old) == md5($item['content'])) $new = '';
		}
	}
}
include tpl('history');
?>