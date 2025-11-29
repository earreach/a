<?php 
defined('IN_DESTOON') or exit('Access Denied');
class guestbook {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'guestbook';
		$this->fields = array('type','title','areaid','mid','tid','rid','content','thumbs','video','truename','telephone','email','qq','wx','ali','skype','hidden','status','username','addtime', 'ip', 'reply','editor','edittime');
    }

    function guestbook() {
		$this->__construct();
    }

	function pass($post) {
		global $L;
		if(!is_array($post)) return false;
		if(!$post['content']) return $this->_($L['gbook_pass_content']);
		return true;
	}

	function set($post) {
		global $_username, $_cname, $TYPE;
		$thumbs = array();
		foreach($post['thumbs'] as $v) {
			if(is_url($v)) $thumbs[] = $v;
		}
		$post['thumbs'] = $thumbs ? implode('|', $thumbs) : '';
		is_url($post['video']) or $post['video'] = '';
		save_poster($post['video'], cutstr($post['thumbs'], '', '|'));
		$post['content'] = strip_tags($post['content']);
		in_array($post['type'], $TYPE) or $post['type'] = '';
		$post['title'] = daddslashes(dsubstr($post['content'], 30));
		$post['hidden'] = (isset($post['hidden']) && $post['hidden']) ? 1 : 0;
		$post['mid'] = intval($post['mid']);
		$post['tid'] = intval($post['tid']);
		$post['rid'] = intval($post['rid']);
		$post['reply'] = stripslashes($post['reply']);
		if($this->itemid) {
			$new = $post['reply'];
			foreach($thumbs as $v) {
				$new .= '<img src="'.$v.'"/>';
			}
			$r = $this->get_one();
			$old = $r['reply'];
			foreach(explode('|', $r['thumbs']) as $v) {
				$old .= '<img src="'.$v.'"/>';
			}
			delete_diff($new, $old, $this->itemid);
			if($r['video'] != $post['video']) delete_upload($r['video'], match_userid($r['video']), $this->itemid);
			$post['status'] = $post['status'] == 2 ? 2 : 3;
			$post['editor'] = $_cname ? $_cname : $_username;
			$post['edittime'] = is_time($post['edittime']) ? strtotime($post['edittime']) : DT_TIME;
		} else {
			$post['username'] = $_username;
			$post['addtime'] =  DT_TIME;
			$post['ip'] =  DT_IP;
			$post['edittime'] = 0;
			$post['reply'] = '';
			$post['status'] = 2;
		}
		$reply = $post['reply'];
		unset($post['reply']);
		$post = dhtmlspecialchars($post);
		$post['reply'] = addslashes(dsafe($reply));
		return array_map("trim", $post);
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = 'status=3', $order = 'itemid DESC') {
		global $MOD, $pages, $page, $pagesize, $offset, $sum, $items;
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
			$r['content'] = nl2br($r['content']);
			$r['editdate'] = '--';
			if($r['reply']) {
				if(strpos($r['reply'], '<') === false) $r['reply'] = nl2br($r['reply']);
				$r['editdate'] = timetodate($r['edittime'], 5);
			}
			if(!$r['type'] && substr($r['title'], 0, 1) == '[') {//TEMP
				$type = cutstr($r['title'], '[', ']');
				if($type && strpos($MOD['guestbook_type'], $type) !== false) {
					$r['title'] = cutstr($r['title'], ']');
					$r['type'] = $type;
					DB::query("UPDATE {$this->table} SET type='".addslashes($r['type'])."',title='".addslashes($r['title'])."' WHERE itemid=$r[itemid]");
				}
			}
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		$this->update($post);
		clear_upload($post['reply'].$post['thumbs'].$post['video'], $this->itemid);
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		clear_upload($post['reply'].$post['thumbs'].$post['video'], $this->itemid);
		return true;
	}

	function delete($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function check($itemid, $status) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->check($v, $status); }
		} else {
			DB::query("UPDATE {$this->table} SET status=$status WHERE itemid=$itemid");
		}
	}

	function update($post) {
		global $MODULE;
		$mid = $post['mid'];
		$tid = $post['tid'];
		$rid = $post['rid'];
		if($mid > 2) {
			if($rid) {
				if($mid == 3) {
					$table = DT_PRE.'comment';
				} else if($MODULE[$mid]['module'] == 'know') {
					$table = DT_PRE.'know_answer_'.$mid;
				} else if($MODULE[$mid]['module'] == 'club') {
					$table = DT_PRE.'club_reply_'.$mid;
				}
				$id = $rid;
			} else {
				$table = get_table($mid);		
				$id = $tid;
			}
			$fd = $mid == 4 ? 'userid' : 'itemid';
			if($table) DB::query("UPDATE {$table} SET reports=reports+1 WHERE `{$fd}`=$id");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>