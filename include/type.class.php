<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dtype {
	var $item;
	var $table;
	var $cache = 0;

	function __construct() {
		$this->table = DT_PRE.'type';
	}

	function dtype() {
		$this->__construct();
	}

	function get_list() {
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE item='$this->item' ORDER BY listorder ASC,typeid DESC ");
		while($r = DB::fetch_array($result)) {
			$lists[$r['typeid']] = $r;
		}
		return $lists;
	}

	function update($post) {
		if($post[0]['typename']) {
			if(strpos($post[0]['typename'], "\n") === false) {				
				$this->add($post[0]);
			} else {
				foreach(explode("\n", $post[0]['typename']) as $v) {
					$post[0]['typename'] = $v;
					$this->add($post[0]);
				}
			}
		}
		unset($post[0]);
		$this->edit($post);
		if($this->cache) cache_type($this->item);
		return true;
	}

	function add($post) {
		global $TYPE;
		$post['typename'] = dhtmlspecialchars(trim(strip_tags($post['typename'])));
		if(strlen($post['typename']) < 2) return false;
		$post['listorder'] = intval($post['listorder']);
		$post['parentid'] = intval($post['parentid']);
		if($post['parentid'] && !isset($TYPE[$post['parentid']])) $post['parentid'] = 0;
		$post['style'] = dhtmlspecialchars($post['style']);
		DB::query("INSERT INTO {$this->table} (listorder,typename,style,parentid,item,cache) VALUES('$post[listorder]','$post[typename]','$post[style]','$post[parentid]','$this->item','$this->cache')");
	}

	function edit($post) {
		global $TYPE;
		foreach($post as $k=>$v) {
			$v['typename'] = dhtmlspecialchars(trim(strip_tags($v['typename'])));
			if(strlen($v['typename']) < 2) continue;
			$v['listorder'] = intval($v['listorder']);
			$v['parentid'] = intval($v['parentid']);
			if($v['parentid'] == $k) continue;
			if($v['parentid'] && !isset($TYPE[$v['parentid']])) continue;
			$v['style'] = dhtmlspecialchars($v['style']);
			$k = intval($k);
			DB::query("UPDATE {$this->table} SET listorder='$v[listorder]',typename='$v[typename]',style='$v[style]',parentid='$v[parentid]' WHERE typeid='$k' AND item='$this->item'");
		}
	}

	function delete($typeid) {
		$typeid = intval($typeid);
		if($typeid < 1) return;
		DB::query("DELETE FROM {$this->table} WHERE typeid=$typeid AND item='$this->item'");
		DB::query("DELETE FROM {$this->table} WHERE parentid=$typeid AND item='$this->item'");
		if($this->cache) cache_type($this->item);
	}

	function parent_option($TYPE) {
		$s = '';
		foreach($TYPE as $v) {
			if($v['parentid'] == 0) $s .= '<option value="'.$v['typeid'].'">'.$v['typename'].'</option>';
		}
		return $s;
	}
}
?>