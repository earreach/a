<?php 
defined('IN_DESTOON') or exit('Access Denied');
class favorite {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'favorite';
		$this->fields = array('listorder','userid','username','typeid','mid','tid','title','style','thumb','url','addtime','note');
    }

    function favorite() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(strlen($post['title']) < 3) return $this->_($L['pass_title']);
		if(!is_url($post['url'])) return $this->_($L['pass_url']);
		return true;
	}

	function set($post) {
		$post['listorder'] = intval($post['listorder']);
		$post['mid'] = intval($post['mid']);
		$post['tid'] = intval($post['tid']);
		$post['thumb'] = is_url($post['thumb']) ? $post['thumb'] : '';
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid $condition");
	}

	function get_list($condition = 'status=3', $order = 'addtime DESC') {
		global $MODULE, $TYPE, $pages, $page, $pagesize, $offset, $L, $items, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['title'] = set_style($r['title'], $r['style']);
			$r['linkurl'] = gourl(fix_link($r['url']));
			$r['type'] = $r['typeid'] && isset($TYPE[$r['typeid']]) ? set_style($TYPE[$r['typeid']]['typename'], $TYPE[$r['typeid']]['style']) : $L['default_type'];
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->update($post);
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table} WHERE itemid IN ($itemids)");
	}

	function update($post) {
		if($post['tid'] > 0 && $post['mid'] > 3) {
			$table = get_table($post['mid']);
			$id = $post['mid'] == 4 ? 'userid' : 'itemid';
			if($table) DB::query("UPDATE {$table} SET favorites=favorites+1 WHERE `{$id}`=$post[tid]");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>