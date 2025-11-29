<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array(
    array('词语过滤', '?file='.$file),
    array('词语分类', 'javascript:Dwidget(\'?file=type&item='.$file.'\', \'词语分类\');'),
);
$TYPE = get_type($file, 1);
$do = new banword;
switch($action) {
	case 'update':	
		$do->update($post);
		dmsg('保存成功', '?file='.$file.'&item='.$item);
	break;
	case 'delete':
		$itemid or msg('请选择词语');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	default:
		isset($typeid) or $typeid = 0;
		$type_select = type_select($file, 1, 'typeid', '请选择分类', $typeid);
		$type_select_post = type_select($file, 1, 'post[0][typeid]', '未分类', 0);
		$condition = "1";
		if($keyword) $condition .= " AND (replacefrom LIKE '%$keyword%' OR replaceto LIKE '%$keyword%')";
		if($typeid) $condition .= " AND typeid IN (".type_child($typeid, $TYPE).")";
		$lists = $do->get_list($condition);
		include tpl('banword');
	break;
}

class banword {
	var $table;	

	function __construct() {
		$this->table = DT_PRE.'banword';
	}

	function banword() {
		$this->__construct();
	}

	function get_list($condition) {
		global $pages, $page, $offset, $pagesize, $file;
		$type_select = type_select($file, 1, 'post[0][typeid]', '未分类', 0);
		$pages = pages(DB::count($this->table, $condition), $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY bid DESC LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['type_select'] = str_replace(array('selected="selected"', 'post[0]', '="'.$r['typeid'].'"'), array('', 'post['.$r['bid'].']', '="'.$r['typeid'].'" selected="selected"'), $type_select);
			$lists[] = $r;
		}
		return $lists;
	}

	function update($post) {
		$this->add($post[0]);
		unset($post[0]);
		$this->edit($post);
		cache_banword();
	}

	function add($post) {
		if(!$post['replacefrom']) return false;
		$post['deny'] = in_array($post['deny'], array(0, 1, 2)) ? $post['deny'] : 0;
		$post['typeid'] = intval($post['typeid']);
		$F = explode("\n", $post['replacefrom']);
		$T = explode("\n", $post['replaceto']);
		foreach($F as $k=>$f) {
			$f = trim($f);
			if($f) {
				$t = isset($T[$k]) ? trim($T[$k]) : '';
				if($f != $t) DB::query("INSERT INTO {$this->table} (typeid,replacefrom,replaceto,deny) VALUES('$post[typeid]','$f','$t','$post[deny]')");
			}
		}
	}

	function edit($post) {
		foreach($post as $k=>$v) {
			if(!$v['replacefrom']) continue;
			$v['deny'] = in_array($v['deny'], array(0, 1, 2)) ? $v['deny'] : 0;
			$v['typeid'] = intval($v['typeid']);
			if($v['replacefrom'] != $v['replaceto']) DB::query("UPDATE {$this->table} SET typeid='$v[typeid]',replacefrom='$v[replacefrom]',replaceto='$v[replaceto]',deny='$v[deny]' WHERE bid='$k'");
		}
	}

	function delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table} WHERE bid IN ($itemids)");
	}
}
?>