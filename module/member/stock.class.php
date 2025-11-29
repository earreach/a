<?php 
defined('IN_DESTOON') or exit('Access Denied');
class stock {
	var $itemid;
	var $table;
	var $table_data;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'stock';
		$this->table_data = DT_PRE.'stock_data';
		$this->fields = array('typeid','skuid','level','title','style','fee','brand','price','cost','amount','unit','location','thumb','n1','n2','n3','v1','v2','v3','username','editor','addtime','edittime','ip','note');
    }

    function stock() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(strlen($post['title']) < 2) return $this->_(lang('message->pass_title'));
		if(dround($post['price']) < 0.01) return $this->_(lang('message->pass_mall_price'));
		if(!is_url($post['thumb'])) return $this->_(lang('message->pass_thumb'));
		if(DT_MAX_LEN && strlen(clear_img($post['content'])) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
		if(!is_skuid($post['skuid'])) return $this->_(lang('message->pass_skuid'));
		$condition = "skuid='$post[skuid]' AND username='$post[username]'";
		if($this->itemid) $condition .= " AND itemid!=$this->itemid";
		$r = DB::get_one("SELECT itemid FROM {$this->table} WHERE {$condition}");
		if($r) return $this->_(lang('message->pass_skuid_exists'));
		return true;
	}

	function set($post) {
		global $MOD, $_username, $_userid, $_cname;
		is_url($post['thumb']) or $post['thumb'] = '';
		$post['addtime'] = (isset($post['addtime']) && is_time($post['addtime'])) ? datetotime($post['addtime']) : DT_TIME;
		$post['typeid'] = intval($post['typeid']);
		$post['price'] = dround($post['price']);
		$post['cost'] = dround($post['cost']);
		$post['amount'] = intval($post['amount']);
		$post['editor'] = $_cname ? $_cname : $_username;
		$post['edittime'] = DT_TIME;
		if($this->itemid) {
			$new = $post['content'];
			if($post['thumb']) $new .= '<img src="'.$post['thumb'].'"/>';
			$r = $this->get_one();
			$old = $r['content'];
			if($r['thumb']) $old .= '<img src="'.$r['thumb'].'"/>';
			delete_diff($new, $old, $this->itemid);
		}
		$content = $post['content'];
		unset($post['content']);
		$post = dhtmlspecialchars($post);
		$post['content'] = dsafe($content);
		$post['content'] = stripslashes($post['content']);
		$post['content'] = save_local($post['content']);
		$post['content'] = save_remote($post['content']);
		$post['content'] = addslashes($post['content']);
		return array_map("trim", $post);
	}

	function get_one($sql = '') {
		$condition = $sql ? $sql : "itemid=$this->itemid"; 
		$r = DB::get_one("SELECT * FROM {$this->table} WHERE {$condition}");
		if($r) {
			if($sql) return $r;
			$t = DB::get_one("SELECT content FROM {$this->table_data} WHERE {$condition}");
			$r['content'] = $t ? $t['content'] : '';
			return $r;
		} else {
			return array();
		}
	}

	function get_list($condition = 'status=3', $order = 'addtime DESC') {
		global $MOD, $pages, $page, $pagesize, $offset, $items, $sum;
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
			$r['adddate'] = timetodate($r['addtime'], 6);
			$r['editdate'] = timetodate($r['edittime'], 6);
			$r['profit'] = dround($r['price'] - $r['cost'], 2, 1);
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		global $MOD, $L;
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		DB::query("REPLACE INTO {$this->table_data} (itemid,content) VALUES ('$this->itemid', '$post[content]')");		
		clear_upload($post['content'].$post['thumb'], $this->itemid, $this->table);
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		DB::query("REPLACE INTO {$this->table_data}  (itemid,content) VALUES ('$this->itemid', '$post[content]')");
		clear_upload($post['content'].$post['thumb'], $this->itemid, $this->table);
		return true;
	}

	function delete($itemid, $all = true) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			$userid = get_user($r['username']);
			if($r['thumb'] && $userid) delete_upload($r['thumb'], $userid, $itemid);
			if($r['content'] && $userid) delete_local($r['content'], $userid, $itemid);
			DB::query("DELETE FROM {$this->table}_record WHERE stockid=$itemid");
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
			DB::query("DELETE FROM {$this->table_data} WHERE itemid=$itemid");
		}
	}
	
	function level($itemid, $level) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("UPDATE {$this->table} SET level=$level WHERE itemid IN ($itemids)");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>