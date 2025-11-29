<?php 
defined('IN_DESTOON') or exit('Access Denied');
class promo {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'finance_promo';
		$this->fields = array('title','price','cost','amount','mid','catid','itemids','fromtime','totime','username','open','addtime','editor','edittime','note');
    }

    function promo() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!trim($post['title'])) return $this->_($L['promo_msg_title']);
		if(dround($post['price']) < 0.01) return $this->_($L['promo_msg_price']);
		if($post['cost'] > 0.01 && dround($post['price']) > dround($post['cost'])) return $this->_($L['promo_msg_cost']);
		if(intval($post['amount']) < 1) return $this->_($L['promo_msg_amount']);
		if(!is_time($post['fromtime']) || !is_time($post['totime'])) return $this->_($L['promo_msg_date']);
		if(datetotime($post['fromtime']) > datetotime($post['totime'])) return $this->_($L['promo_msg_date']);
		return true;
	}

	function set($post) {
		global $_username, $_cname, $MODULE;
		$post['price'] = dround($post['price']);
		$post['cost'] = dround($post['cost']);
		$post['amount'] = intval($post['amount']);
		$post['fromtime'] = datetotime($post['fromtime']);
		$post['totime'] = datetotime($post['totime']);
		$post['open'] = $post['open'] ? 1 : 0;
		$post['editor'] = $_cname ? $_cname : $_username;
		$post['edittime'] = DT_TIME;
		$post['mid'] = intval($post['mid']);
		if($post['mid'] && $MODULE[$post['mid']]['module'] != 'mall') $post['mid'] = 0;
		if($post['mid']) {
			$arr = array();
			if($post['itemids']) {
				$tb = get_table($post['mid']);
				foreach(explode(',', $post['itemids']) as $id) {
					$id = intval($id);
					if(!$id) continue;
					$t = DB::get_one("SELECT * FROM {$tb} WHERE itemid=$id");
					if(!$t) continue;
					if($t['status'] != 3) continue;
					if($post['username'] && $t['username'] != $post['username']) continue;
					$arr[] = $id;
				}
			}
			$post['itemids'] = $arr ? implode(',', $arr) : '';
			$post['catid'] = intval($post['catid']);
			$CAT = get_cat($post['catid']);
			if(!$CAT or $CAT['moduleid'] != $post['mid']) $post['catid'] = 0;
		} else {
			$post['catid'] = 0;
			$post['itemids'] = '';
		}
		if($this->itemid) {
			//
		} else {
			$post['addtime'] = DT_TIME;
		}
		$post = dhtmlspecialchars($post);		
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid $condition");
	}

	function get_list($condition, $order = 'itemid DESC') {
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
			$r['process'] = get_process($r['fromtime'], $r['totime']);
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();		
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function del($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->del($v); }
		} else {
			DB::query("DELETE FROM ".DT_PRE."finance_coupon WHERE itemid=$itemid");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>