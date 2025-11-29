<?php 
defined('IN_DESTOON') or exit('Access Denied');
class dlink {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'link';
		$this->fields = array('listorder', 'title','style','username','addtime','editor','edittime','ip','status', 'linkurl');
    }

    function dlink() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['username']) return $this->_($L['link_pass_username']);
		if(!$post['title']) return $this->_($L['link_pass_title']);
		if(!is_url($post['linkurl'])) return $this->_($L['link_pass_linkurl']);
		return true;
	}

	function set($post) {
		global $MOD, $_username, $_userid, $_cname;
		if($this->itemid) {
			//
		} else {
			$post['addtime'] = DT_TIME;
			$post['ip'] = DT_IP;
		}
		$post['editor'] = $_cname ? $_cname : $_username;
		$post['edittime'] = DT_TIME;
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = '1', $order = 'listorder DESC, itemid DESC') {
		global $pages, $page, $pagesize, $offset, $items, $sum;
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
			$r['title'] = set_style($r['title'], $r['style']);
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['url'] = gourl(fix_link($r['linkurl']));
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		global $MOD, $L;
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		if($post['username'] && $MOD['credit_add_link']) {
			credit_add($post['username'], $MOD['credit_add_link']);
			credit_record($post['username'], $MOD['credit_add_link'], 'system', $L['link_reward_reason'], 'ID:'.$this->itemid);
		}
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		if($post['status'] > 2) history(2, 'link-'.$this->itemid, 'del');
		return true;
	}

	function delete($itemid, $all = true) {
		global $MOD, $L;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v, $all); 
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
			if($r['username'] && $MOD['credit_del_link']) {
				credit_add($r['username'], -$MOD['credit_del_link']);
				credit_record($r['username'], -$MOD['credit_del_link'], 'system', $L['link_punish_reason'], 'ID:'.$this->itemid);
			}
			history(2, 'link-'.$itemid, 'del');
		}
	}

	function check($itemid, $status = 3) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->check($v, $status); 
			}
		} else {
			DB::query("UPDATE {$this->table} SET status=$status WHERE itemid=$itemid");
			history(2, 'link-'.$itemid, 'del');
			return true;
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>