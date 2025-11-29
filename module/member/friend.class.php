<?php 
defined('IN_DESTOON') or exit('Access Denied');
class friend {
	var $itemid;
	var $table;
	var $table_black;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'friend';
		$this->table_black = DT_PRE.'member_blacklist';
    }

    function friend() {
		$this->__construct();
    }

	function pass($post) {
		global $_userid, $_username, $L;
		if(!is_array($post)) return false;
		if($this->itemid) {
			//
		} else {
			if(!isset($post['userid'])) $post['userid'] = $_userid;
			if(!isset($post['username'])) $post['username'] = $_username;			
			if(!check_name($post['username'])) return $this->_($L['friend_pass_username']);
			if(!check_name($post['fusername'])) return $this->_($L['friend_pass_username']);
			if($post['username'] == $post['fusername']) return $this->_($L['friend_pass_self']);
			if(blacked($post['fusername'], $post['username'])) return $this->_($L['friend_pass_black']);
			if($this->friended($post['fusername'], $post['userid'])) return $this->_($L['friend_pass_again']);
		}
		return true;
	}

	function set($post) {
		global $_userid, $_username;
		if($this->itemid) {
			if($post['mobile'] && !is_mobile($post['mobile'])) $post['mobile'] = '';
			if($post['email'] && !is_email($post['email'])) $post['email'] = '';
			if($post['telephone'] && !is_tel($post['telephone'])) $post['telephone'] = '';
			if($post['qq'] && !is_qq($post['qq'])) $post['qq'] = '';
			if($post['wx'] && !is_wx($post['wx'])) $post['wx'] = '';
		} else {
			if(!isset($post['userid'])) $post['userid'] = $_userid;
			if(!isset($post['username'])) $post['username'] = $_username;
			$post['addtime'] = DT_TIME;
		}
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid {$condition}");
	}

	function get_list($condition, $order = 'itemid DESC') {
		global $TYPE, $pages, $page, $pagesize, $offset, $L, $items, $sum;
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
			$r['dcompany'] = set_style($r['company'], $r['style']);
			$r['type'] = $r['typeid'] && isset($TYPE[$r['typeid']]) ? set_style($TYPE[$r['typeid']]['typename'], $TYPE[$r['typeid']]['style']) : $L['default_type'];
			$r['url'] = $r['homepage'] ? gourl($r['homepage']) : '';
			$lists[] = $r;
		}
		return $lists;
	}

	function get_list_black($condition, $order = 'itemid DESC') {
		global $pages, $page, $pagesize, $offset, $L, $items, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table_black} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table_black} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {		
		$this->fields = array('typeid','userid','username','fuserid','fusername','fpassport','addtime');
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		return $this->itemid;
	}

	function edit($post) {		
		$this->fields = array('listorder','typeid','alias','truename','style','company','career','telephone','mobile','homepage','email','qq','wx','ali','skype','note');
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table} WHERE itemid IN ($itemids)");
	}

	function friended($username, $userid = 0) {
		global $_userid;
		$userid or $userid = $_userid;
		$t = DB::get_one("SELECT * FROM {$this->table} WHERE userid=$userid AND fusername='$username'");
		return $t ? $t['itemid'] : 0;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>