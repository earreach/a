<?php 
defined('IN_DESTOON') or exit('Access Denied');
class partner {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'agent';
		$this->fields = array('typeid','username','company','pusername','pcompany','areaid','mobile','discount','addtime','status','reason','note');
    }

    function partner() {
		$this->__construct();
    }

	function pass($post) {
		global $_username, $L;
		if(!is_array($post)) return false;
		if(!check_name($post['username']) || $post['username'] == $_username || !userinfo($post['username'])) return $this->_($L['partner_pass_username']);
		if(!is_mobile($post['mobile'])) return $this->_($L['partner_pass_mobile']);
		if(strlen($post['reason']) < 10) return $this->_($L['partner_pass_reason']);
		$condition = "pusername='$_username' AND username='$post[username]'";
		$t = DB::get_one("SELECT * FROM {$this->table} WHERE $condition");
		if($t) return $this->_($L['partner_pass_exists']);

		return true;
	}

	function set($post) {
		$post['discount'] = intval($post['discount']);
		if($post['discount'] > 99 || $post['discount'] < 1) $post['discount'] = 99;
		$user = userinfo($post['username']);
		$post['company'] = addslashes($user['company']);
		$post['status'] = 2;
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid {$condition}");
	}

	function get_list($condition = 'status=3', $order = 'itemid DESC') {
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
			$r['adddate'] = timetodate($r['addtime'], 3);
			if(DT_TIME - $r['statstime'] > 3600 && $r['status'] == 3) $this->stats($r);
			$lists[] = $r;
		}
		return $lists;
	}

	function get_goods($mid, $condition = 'status=3', $order = 'itemid DESC') {
		global $MODULE, $pages, $page, $pagesize, $offset, $items, $sum, $DT_PC;
		$table = get_table($mid);
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['adddate'] = timetodate($r['addtime'], 3);
			$r['linkurl'] = ($DT_PC ? $MODULE[$mid]['linkurl'] : $MODULE[$mid]['mobile']).$r['linkurl'];
			$lists[] = $r;
		}
		return $lists;
	}

	function stats($r) {
		$amount = $amounty = $amountm = 0;
		$table = DT_PRE.'order';
		$fromtime = $r['addtime'];
		$orders = DB::count($table, "addtime>$fromtime AND buyer='$r[pusername]'");
		$trades = DB::count($table, "addtime>$fromtime AND inviter='$r[pusername]'");
		$t = DB::get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE status=4 AND addtime>$fromtime AND (buyer='$r[pusername]' OR inviter='$r[pusername]')");
		$amount = $t['num'] ? $t['num'] : 0;
		$fromtime = datetotime(timetodate('', 'Y').'-01-01 00:00:00');
		if($fromtime < $r['addtime']) {
			$amounty = $amount;
		} else {			
			$t = DB::get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE status=4 AND addtime>$fromtime AND (buyer='$r[pusername]' OR inviter='$r[pusername]')");
			$amounty = $t['num'] ? $t['num'] : 0;
		}
		$fromtime = datetotime(timetodate('', 'Y-m').'-01 00:00:00');
		if($fromtime < $r['addtime']) {
			$amountm = $amount;
		} else {			
			$t = DB::get_one("SELECT SUM(`amount`) AS num FROM {$table} WHERE status=4 AND addtime>$fromtime AND (buyer='$r[pusername]' OR inviter='$r[pusername]')");
			$amountm = $t['num'] ? $t['num'] : 0;
		}
		DB::query("UPDATE {$this->table} SET orders=$orders,amount=$amount,amounty=$amounty,amountm=$amountm,statstime=".DT_TIME." WHERE itemid=$r[itemid]");
	}

	function add($post) {
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		return $this->itemid;
	}

	function delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table} WHERE itemid IN ($itemids)");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>