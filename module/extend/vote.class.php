<?php 
defined('IN_DESTOON') or exit('Access Denied');
class vote {
	var $itemid;
	var $table;
	var $table_record;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'vote';
		$this->table_record = DT_PRE.'vote_record';
		$this->fields = array('typeid','areaid', 'title','style','level','linkto','content','groupid','verify','addtime','fromtime','totime','editor','edittime','template_vote','template', 'vote_min', 'vote_max', 'linkurl', 'choose', 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 's10');
    }

    function vote() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['typeid']) return $this->_($L['vote_pass_type']);
		if(!$post['title']) return $this->_($L['vote_pass_title']);
		return true;
	}

	function set($post) {
		global $MOD, $_username, $_userid;
		$post['addtime'] = (isset($post['addtime']) && is_time($post['addtime'])) ? datetotime($post['addtime']) : DT_TIME;
		$post['edittime'] = DT_TIME;
		$post['editor'] = $_username;
		$post['content'] = addslashes(save_remote(save_local(stripslashes($post['content']))));
		if($this->itemid) {
			$new = $post['content'];
			$r = $this->get_one();
			$old = $r['content'];
			delete_diff($new, $old, $this->itemid);
		}
		$post['fromtime'] = is_time($post['fromtime']) ? datetotime($post['fromtime']) : 0;
		$post['totime'] = is_time($post['totime']) ? datetotime($post['totime']) : 0;
		$post['groupid'] = (isset($post['groupid']) && is_array($post['groupid'])) ? implode(',', $post['groupid']) : '';
		$post['verify'] = intval($post['verify']);
		return array_map("trim", $post);
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid");
	}

	function get_list($condition = '1', $order = 'addtime DESC') {
		global $MOD, $TYPE, $pages, $page, $pagesize, $offset, $L, $sum, $items;
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
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['fromdate'] = $r['fromtime'] ? timetodate($r['fromtime'], 3) : $L['timeless'];
			$r['todate'] = $r['totime'] ? timetodate($r['totime'], 3) : $L['timeless'];
			$r['typename'] = $TYPE[$r['typeid']]['typename'];
			$r['typeurl'] = $MOD['vote_url'].list_url($r['typeid']);
			$lists[] = $r;
		}
		return $lists;
	}

	function get_list_record($condition = '1', $order = 'rid DESC') {
		global $MOD, $TYPE, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table_record} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table_record} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['votedate'] = timetodate($r['votetime'], 6);
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		global $DT, $MOD, $module;
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		$linkurl = $this->linkurl($this->itemid);
		DB::query("UPDATE {$this->table} SET linkurl='$linkurl' WHERE itemid=$this->itemid");
		clear_upload($post['content'], $this->itemid, $this->table);
		tohtml('vote', $module, "itemid=$this->itemid");
		return $this->itemid;
	}

	function edit($post) {
		global $DT, $MOD, $module;
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		$linkurl = $this->linkurl($this->itemid);
		DB::query("UPDATE {$this->table} SET linkurl='$linkurl' WHERE itemid=$this->itemid");
		clear_upload($post['content'], $this->itemid, $this->table);
		tohtml('vote', $module, "itemid=$this->itemid");
		return true;
	}

	function linkurl($itemid) {
		global $MOD;
		$linkurl = show_url($itemid);
		return $MOD['vote_url'].$linkurl;
	}

	function delete($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v, $all); 
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			$userid = get_user($r['editor']);
			if($r['content']) delete_local($r['content'], $userid);
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
			DB::query("DELETE FROM {$this->table_record} WHERE itemid=$itemid");
			file_del(DT_CACHE.'/htm/vote_'.$r['itemid'].'.htm');
		}
	}

	function del($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->del($v); 
			}
		} else {
			$r = DB::get_one("SELECT * FROM {$this->table_record} WHERE rid=$itemid");
			$vid = $r['itemid'];
			foreach(explode(',', $r['votes']) as $v) {
				$v = intval($v);
				if($v > 10 || $v < 1) continue;
				$vv = 'v'.$v;
				DB::query("UPDATE {$this->table} SET `votes`=`votes`-1,`{$vv}`=`{$vv}`-1 WHERE itemid=$vid");
			}
			DB::query("DELETE FROM {$this->table_record} WHERE rid=$itemid");
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