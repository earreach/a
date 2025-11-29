<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array(
    array('问题验证', '?file='.$file),
);
$do = new question;
switch($action) {
	case 'update':	
		$do->update($post);
		dmsg('保存成功', '?file='.$file);
	break;
	case 'delete':
		$itemid or msg('请选择问题');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	default:
		$condition = "1";
		if($kw) $condition .= " AND (question LIKE '%$keyword%' OR answer LIKE '%$keyword%')";
		$lists = $do->get_list($condition);
		include tpl('question');
	break;
}

class question {
	var $table;

	function __construct() {
		$this->table = DT_PRE.'question';
	}

	function question() {
		$this->__construct();
	}

	function get_list($condition) {
		global $pages, $page, $offset, $pagesize;
		$pages = pages(DB::count($this->table, $condition), $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY qid DESC LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function update($post) {
		$this->add($post[0]);
		unset($post[0]);
		$this->edit($post);
		return true;
	}

	function add($post) {
		if(!$post['question'] || !$post['answer']) return false;
		$Q = explode("\n", $post['question']);
		$A = explode("\n", $post['answer']);
		foreach($Q as $k=>$q) {
			$q = trim($q);
			if($q) {
				$a = isset($A[$k]) ? trim($A[$k]) : '';
				if($q && $a) DB::query("INSERT INTO {$this->table} (question,answer) VALUES('$q','$a')");
			}
		}
	}

	function edit($post) {
		foreach($post as $k=>$v) {
			if(!$v['question'] || !$v['answer']) continue;
			DB::query("UPDATE {$this->table} SET question='$v[question]',answer='$v[answer]' WHERE qid='$k'");
		}
	}

	function delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table} WHERE qid IN ($itemids)");
	}
}
?>