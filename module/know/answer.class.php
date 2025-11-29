<?php 
defined('IN_DESTOON') or exit('Access Denied');
class answer {
	var $moduleid;
	var $itemid;
	var $table;
	var $errmsg = errmsg;

    function __construct($moduleid) {
		global $table_answer;
		$this->moduleid = $moduleid;
		$this->table = $table_answer;
    }

    function answer($moduleid) {
		$this->__construct($moduleid);
    }

	function pass($post) {
		if(!is_array($post)) return false;
		if(!$post['content']) return $this->_(lang('message->pass_know_answer'));
		return true;
	}

	function set($post) {
		global $_username, $_cname;
		in_array($post['status'], array(1, 2, 3)) or $post['status'] = 2;
		$post['editor'] = $_cname ? $_cname : $_username;
		$post['edittime'] = DT_TIME;
		return array_map("trim", $post);
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = 'status=3', $order = 'itemid DESC') {
		global $MOD, $TYPE, $moduleid, $pages, $page, $pagesize, $offset, $items, $sum;
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
			$r['title'] = get_intro($r['content'], 50);
			$r['alt'] = get_intro($r['content'], 500);
			$r['linkurl'] = gourl('?mid='.$moduleid.'&itemid='.$r['qid']);
			$lists[] = $r;
		}
		return $lists;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1)." WHERE itemid=$this->itemid");
		clear_upload($post['content'], $this->itemid, $this->table);
		if($post['status'] > 2) history($this->moduleid, 'answer-'.$this->itemid, 'del');
		return true;
	}

	function delete($itemid) {
		global $MOD, $table_vote;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->delete($v); 
			}
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			if($r) {
				DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
				DB::query("DELETE FROM {$table_vote} WHERE aid=$itemid");
				if($r['content']) delete_local($r['content'], get_user($r['username']));
				if($r['username'] && $MOD['credit_del_answer']) {
					credit_add($r['username'], -$MOD['credit_del_answer']);
					credit_record($r['username'], -$MOD['credit_del_answer'], 'system', lang('my->credit_record_answer_del'), 'ID:'.$r['qid']);
				}
				history($this->moduleid, 'answer-'.$itemid, 'del');
			}
		}
	}

	function check($itemid, $status = 3) {
		global $MOD;
		if(is_array($itemid)) {
			foreach($itemid as $v) { 
				$this->check($v, $status); 
			}
		} else {
			if($MOD['credit_answer'] && $status == 3) {
				$this->itemid = $itemid;
				$item = $this->get_one();
				if($item['username']) {					
					$could_credit = true;
					$reason = lang('my->credit_record_answer_add');
					if($MOD['credit_maxanswer'] > 0) {					
						$r = DB::get_one("SELECT SUM(amount) AS total FROM ".DT_PRE."finance_credit WHERE username='$item[username]' AND addtime>".DT_TIME."-86400  AND reason='".$reason."'");
						if($r['total'] >= $MOD['credit_maxanswer']) $could_credit = false;
					}
					if($could_credit) {
						credit_add($item['username'], $MOD['credit_answer']);
						credit_record($item['username'], $MOD['credit_answer'], 'system', $reason, 'ID:'.$itemid);
					}
				}
			}
			DB::query("UPDATE {$this->table} SET status=$status WHERE itemid=$itemid");
			history($this->moduleid, 'answer-'.$itemid, 'del');
		}
	}

	function reject($itemid) {
		global $_username;
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->reject($v); }
		} else {
			DB::query("UPDATE {$this->table} SET status=1,editor='$_username' WHERE itemid=$itemid");
			return true;
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>