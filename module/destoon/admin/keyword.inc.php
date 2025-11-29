<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('已启用', '?file='.$file),
    array('待审核', '?file='.$file.'&status=2'),
    array('已禁止', '?file='.$file.'&status=1'),
    array('会员记录', '?file='.$file.'&action=record'),

);
$status = isset($status) ? intval($status) : 3;
$do = new keyword;
switch($action) {
	case 'cls':
		$time = $DT_TODAY - 60*86400;
		$db->query("DELETE FROM {$DT_PRE}keyword_record WHERE addtime<$time");
		dmsg('清理成功', $forward);
	break;
	case 'del':
		$itemid or msg();
		$itemid or msg('请选择项目');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$DT_PRE}keyword_record WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'record':
		$sorder  = array('结果排序方式', '搜索时间降序', '搜索时间升序', '搜索结果降序', '搜索结果升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'items DESC', 'items ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($username) && check_name($username)) or $username = '';
		
		$module_select = module_select('mid', '模块', $mid);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = "1";
		if($keyword) $condition .= match_kw('keyword', $keyword);
		if($mid) $condition .= " AND moduleid=$mid";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		$lists = $do->get_list_record($condition, $dorder[$order]);
		include tpl('keyword_record');
	break;
	case 'letter':
		if(!$word) exit('');
		exit(gb2py($word));
	break;
	case 'update':
		$do->update($post);
		dmsg('保存成功', '?file='.$file.'&status='.$status);
	break;
	case 'delete':
		$itemid or msg('请选择关键词');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'status':
		$itemid or msg('请选择关键词');
		in_array($status, array(1, 2, 3)) or msg('状态错误');
		$do->status($itemid, $status);
		dmsg('操作成功', $forward);
	break;
	default:
		$menuid = 0;
		if($status == 2) $menuid = 1;
		if($status == 1) $menuid = 2;
		$sfields = array('按条件', '关键词', '相关词', '拼音');
		$dfields = array('word', 'word', 'keyword', 'letter');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$sorder  = array('结果排序方式', '总搜索量降序', '总搜索量升序', '本月搜索降序', '本月搜索升序', '本周搜索降序', '本周搜索升序', '今日搜索降序', '今日搜索升序', '搜索结果降序', '搜索结果升序', '更新时间降序', '更新时间升序');
		$dorder  = array('itemid DESC', 'total_search DESC', 'total_search ASC', 'month_search DESC', 'month_search ASC', 'week_search DESC', 'week_search ASC', 'today_search DESC', 'today_search ASC', 'items DESC', 'items ASC', 'updatetime DESC', 'updatetime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$order_select  = dselect($sorder, 'order', '', $order);
		$condition = "status=$status";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($mid) $condition .= " AND moduleid=$mid";
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('keyword');
	break;
}

class keyword {
	var $table;

	function __construct() {
		$this->table = DT_PRE.'keyword';
	}

	function keyword() {
		$this->__construct();
	}

	function get_list($condition, $order) {
		global $pages, $page, $offset, $pagesize;
		$pages = pages(DB::count($this->table, $condition), $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function get_list_record($condition, $order) {
		global $pages, $page, $offset, $pagesize;
		$pages = pages(DB::count($this->table.'_record', $condition), $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table}_record WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function update($post) {
		$this->add($post[0]);
		unset($post[0]);
		$this->edit($post);
		cache_bansearch();
	}

	function add($post) {
		if(!$post['word']) return false;
		in_array($post['status'], array(1, 2, 3)) or $post['status'] = 3;
		DB::query("INSERT INTO {$this->table} (moduleid,word,keyword,letter,items,total_search,month_search,week_search,today_search,updatetime,status) VALUES('$post[moduleid]','$post[word]','$post[keyword]','$post[letter]','$post[items]','$post[total_search]','$post[month_search]','$post[week_search]','$post[today_search]','".DT_TIME."', '$post[status]')");
	}

	function edit($post) {
		foreach($post as $k=>$v) {
			if(!$v['word']) continue;
			in_array($v['status'], array(1, 2, 3)) or $v['status'] = 3;
			DB::query("UPDATE {$this->table} SET word='$v[word]',keyword='$v[keyword]',letter='$v[letter]',total_search='$v[total_search]',month_search='$v[month_search]',week_search='$v[week_search]',today_search='$v[today_search]',status='$v[status]' WHERE itemid='$k'");
		}
	}

	function delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table} WHERE itemid IN ($itemids)");
	}

	function status($itemid, $status) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("UPDATE {$this->table} SET status=$status WHERE itemid IN ($itemids)");
	}
}
?>