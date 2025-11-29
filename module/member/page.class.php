<?php 
defined('IN_DESTOON') or exit('Access Denied');
class page {
	var $itemid;
	var $table;
	var $table_data;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'page';
		$this->table_data = DT_PRE.'page_data';
		$this->fields = array('title','style','status','username','addtime','editor','edittime','ip','linkurl','listorder','note');
    }

    function page() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['title']) return $this->_($L['pass_title']);
		if(!$post['content']) return $this->_($L['pass_content']);
		if(DT_MAX_LEN && strlen(clear_img($post['content'])) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
		return true;
	}

	function set($post) {
		global $MOD, $_username, $_userid, $_cname;
		$post['editor'] = $_cname ? $_cname : $_username;
		$post['edittime'] = DT_TIME;
		$post['title'] = trim($post['title']);
		$post['listorder'] = intval($post['listorder']);
		if($this->itemid) {
			$new = $post['content'];
			$r = $this->get_one();
			$old = $r['content'];
			delete_diff($new, $old, $this->itemid);
		} else {			
			$post['addtime'] = DT_TIME;
			$post['ip'] = DT_IP;
		}
		$content = $post['content'];
		unset($post['content']);
		$post = dhtmlspecialchars($post);
		$post['content'] = dsafe($content);
		if($MOD['page_clear'] || $MOD['page_save']) {
			$post['content'] = stripslashes($post['content']);
			$post['content'] = save_local($post['content']);
			if($MOD['page_clear']) $post['content'] = clear_link($post['content']);
			if($MOD['page_save']) $post['content'] = save_remote($post['content']);
			$post['content'] = addslashes($post['content']);
		}
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} n,{$this->table_data} c WHERE n.itemid=c.itemid AND n.itemid='$this->itemid' $condition");
	}

	function get_list($condition = 'status=3', $order = 'listorder DESC,addtime DESC') {
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
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
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
		DB::query("INSERT INTO {$this->table_data} (itemid,content) VALUES ('$this->itemid', '$post[content]')");		
		$this->update($this->itemid);
		if($post['username'] && $MOD['credit_add_page']) {
			credit_add($post['username'], $MOD['credit_add_page']);
			credit_record($post['username'], $MOD['credit_add_page'], 'system', $L['page_record_add'], 'ID:'.$this->itemid);
		}
		clear_upload($post['content'], $this->itemid, $this->table);
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
	    DB::query("UPDATE {$this->table_data} SET content='$post[content]' WHERE itemid=$this->itemid");
		$this->update($this->itemid);
		clear_upload($post['content'], $this->itemid, $this->table);
		if($post['status'] > 2) history(2, 'page-'.$this->itemid, 'del');
		return true;
	}

	function update($itemid) {
		$r = DB::get_one("SELECT username FROM {$this->table} WHERE itemid=$itemid");
		$linkurl = userurl($r['username'], 'file=introduce&itemid='.$itemid); 
		return DB::query("UPDATE {$this->table} SET linkurl='$linkurl' WHERE itemid=$itemid");
	}

	function recycle($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->recycle($v); }
		} else {
			DB::query("UPDATE {$this->table} SET status=0 WHERE itemid=$itemid");
			return true;
		}		
	}

	function restore($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->restore($v); }
		} else {
			DB::query("UPDATE {$this->table} SET status=3 WHERE itemid=$itemid");
			return true;
		}		
	}

	function delete($itemid, $all = true) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			$userid = get_user($r['username']);
			if($r['content']) delete_local($r['content'], $userid);
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
			DB::query("DELETE FROM {$this->table_data} WHERE itemid=$itemid");
			if($r['username'] && $MOD['credit_del_page']) {
				credit_add($r['username'], -$MOD['credit_del_page']);
				credit_record($r['username'], -$MOD['credit_del_page'], 'system', $L['page_record_del'], 'ID:'.$this->itemid);
			}
			history(2, 'page-'.$itemid, 'del');
		}
	}

	function check($itemid) {
		global $_username;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v); }
		} else {
			DB::query("UPDATE {$this->table} SET status=3,editor='$_username',edittime=".DT_TIME." WHERE itemid=$itemid");
			history(2, 'page-'.$itemid, 'del');
			return true;
		}
	}

	function reject($itemid) {
		global $_username;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->reject($v); }
		} else {
			DB::query("UPDATE {$this->table} SET status=1,editor='$_username',edittime=".DT_TIME." WHERE itemid=$itemid");
			return true;
		}
	}

	function clear() {		
		$result = DB::query("SELECT itemid FROM {$this->table} WHERE status=0");
		while($r = DB::fetch_array($result)) {
			$this->delete($r['itemid']);
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>