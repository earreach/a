<?php 
defined('IN_DESTOON') or exit('Access Denied');
class child {
	var $itemid;
	var $parent;
	var $db;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'member_child';
		$this->fields = array('parent','username','password','passsalt','nickname','gender','truename','mobile','department','role','addtime','edittime','loginip','logintime','status','permission');
    }

    function child() {
		$this->__construct();
    }

	function is_username($username) {
		global $MOD, $L;
		if(!check_name($username)) return $this->_($L['member_username_match']);
		$MOD['minusername'] or $MOD['minusername'] = 4;
		$MOD['maxusername'] or $MOD['maxusername'] = 20;
		if(strlen($username) < $MOD['minusername'] || strlen($username) > $MOD['maxusername']) return $this->_(lang($L['member_username_len'], array($MOD['minusername'], $MOD['maxusername'])));
		if($MOD['banusername']) {
			$tmp = explode('|', $MOD['banusername']);
			foreach($tmp as $v) {
				if($MOD['banmodeu']) {
					if($username == $v) return $this->_($L['member_username_ban']);
				} else {
					if(strpos($username, $v) !== false) return $this->_($L['member_username_ban']);
				}
			}
		}
		return true;
	}
	
	function is_password($password, $cpassword = '') {
		global $MOD, $L;
		if(!$password) return $this->_($L['member_password_null']);
		if($cpassword && $password != $cpassword) return $this->_($L['member_password_match']);
		if(is_md5($password)) return true;
		if(!$MOD['minpassword'] || $MOD['minpassword'] < 6) $MOD['minpassword'] = 6;
		if(!$MOD['maxpassword'] || $MOD['maxpassword'] > 30) $MOD['maxpassword'] = 30;
		if($MOD['minpassword'] > $MOD['maxpassword'] || $MOD['maxpassword'] - $MOD['minpassword'] < 6) $MOD['maxpassword'] = $MOD['minpassword'] + 6;
		if(strlen($password) < $MOD['minpassword'] || strlen($password) > $MOD['maxpassword']) return $this->_(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
		if(strpos(','.$MOD['mixpassword'].',', ',1,') !== false && !preg_match("/[0-9]/", $password)) return $this->_($L['member_password_1']);
		if(strpos(','.$MOD['mixpassword'].',', ',2,') !== false && !preg_match("/[a-z]/", $password)) return $this->_($L['member_password_2']);
		if(strpos(','.$MOD['mixpassword'].',', ',3,') !== false && !preg_match("/[A-Z]/", $password)) return $this->_($L['member_password_3']);
		if(strpos(','.$MOD['mixpassword'].',', ',4,') !== false && !preg_match("/[[:punct:]]/", $password)) return $this->_($L['member_password_4']);
		return true;
	}

	function username_exists($username) {
		$condition = "username='{$username}'";
		if($this->itemid) $condition .= " AND itemid<>{$this->itemid}";
		return DB::get_one("SELECT itemid FROM {$this->table} WHERE {$condition}");
	}

	function nickname_exists($nickname) {
		$condition = "parent='{$this->parent}' AND nickname='{$nickname}'";
		if($this->itemid) $condition .= " AND itemid<>{$this->itemid}";
		return DB::get_one("SELECT itemid FROM {$this->table} WHERE {$condition}");
	}

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$this->is_username($post['username'])) return false;
		if($this->username_exists($post['username'])) return $this->_($L['member_username_reg']);
		if($post['nickname'] && $this->nickname_exists($post['nickname'])) return $this->_($L['member_passport_reg']);
		if($this->itemid) {
			if($post['password'] && !$this->is_password($post['password'])) return false;
		} else {
			if(!$this->is_password($post['password'])) return false;
		}
		if(!is_array($post['permission'])) return false;
		if(count($post['permission']) == 0) return $this->_($L['child_permission']);
		return true;
	}

	function set($post) {
		if($post['password']) {
			$post['passsalt'] = random(8);
			$post['password'] = dpassword($post['password'], $post['passsalt']);
		} else {
			unset($post['password']);
		}
		$post['parent'] = $this->parent;
		$post['permission'] = implode(',', $post['permission']);
		$post['gender'] = $post['gender'] == 2 ? 2 : 1;
		$post['status'] = $post['status'] == 3 ? 3 : 2;
		if($this->itemid) {
			$post['edittime'] = DT_TIME;
		} else {
			$post['addtime'] =  DT_TIME;
		}
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid=$this->itemid $condition");
	}

	function get_list($condition, $order = 'itemid ASC') {
		global $MOD, $pages, $page, $pagesize, $offset, $sum;
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
			$r['logindate'] = $r['logintime'] ? timetodate($r['logintime'], 5) : 'N/A';
			$r['loginip'] or $r['loginip'] = 'N/A';
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

	function password($post) {
		if($this->is_password($post['npassword'], $post['cpassword'])) {
			$passsalt = random(8);
			$password = dpassword($post['npassword'], $passsalt);
			DB::query("UPDATE {$this->table} SET password='$password',passsalt='$passsalt' WHERE itemid=$this->itemid");
			return true;
		} else {
			return false;
		}
	}

	function delete($itemid, $all = true) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->itemid = $itemid;
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>