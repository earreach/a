<?php 
defined('IN_DESTOON') or exit('Access Denied');
class style {
	var $itemid;
	var $table;
	var $fields;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'style';
		$this->fields = array('typeid','title','skin','template','author','groupid','fee','currency','mobile','hits', 'addtime','editor','edittime');
    }

    function style() {
		$this->__construct();
    }

	function pass($post) {
		global $CFG, $MODULE, $L;
		if(!is_array($post)) return false;
		if(!$post['title']) return $this->_($L['style_pass_title']);
		if(!$post['skin']) return $this->_($L['style_pass_skin']);
		if(!preg_match("/^[a-z0-9\-_]+$/i", $post['skin'])) return $this->_($L['style_pass_skin_match']);
		if(!is_file(DT_ROOT.'/static/home/'.$post['skin'].'/style.css')) return $this->_($L['style_pass_css']);
		if(!$post['template']) return $this->_($L['style_pass_template']);
		if(!preg_match("/^[a-z0-9\-_]+$/i", $post['template'])) return $this->_($L['style_pass_template_match']);
		if(!is_dir(DT_ROOT.'/template/'.$CFG['template'].'/'.$post['template'])) return $this->_($L['style_pass_dir']);
		if($post['mobile'] && !is_dir(DT_ROOT.'/template/'.$CFG['template_mobile'].'/'.$post['template'])) return $this->_($L['style_pass_mdir']);
		if(!isset($post['groupid'])) return $this->_($L['style_pass_groupid']);
		return true;
	}

	function set($post) {
		global $MOD, $_username, $_userid, $_cname;
		$post['addtime'] = (isset($post['addtime']) && is_time($post['addtime'])) ? datetotime($post['addtime']) : DT_TIME;
		$post['editor'] = $_cname ? $_cname : $_username;
		$post['edittime'] = DT_TIME;
		$post['groupid'] = (isset($post['groupid']) && $post['groupid']) ? ','.implode(',', $post['groupid']).',' : '';
		$post['fee'] = dround($post['fee']);
		$post['mobile'] = $post['mobile'] ? 1 : 0;
		$post = dhtmlspecialchars($post);
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid' $condition");
	}

	function get_list($condition = '1', $order = 'listorder DESC, itemid DESC') {
		global $MODULE, $MOD, $MG, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$GROUP = cache_read('group.php');
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['thumb'] = is_file(DT_ROOT.'/static/home/'.$r['skin'].'/thumb.gif') ? DT_STATIC.'home/'.$r['skin'].'/thumb.gif' : DT_STATIC.'home/image/thumb.gif';
			$groupid = explode(',', substr($r['groupid'], 1, -1));
			$group = array();
			foreach($groupid as $gid) {
				$group[] = '<span class="c_p" onclick="Dq(\'groupid\','.$gid.');">'.$GROUP[$gid]['groupname'].'</span>';
			}
			$r['groups'] = implode(' , ', $group);
			$r['group'] = strip_tags($r['groups']);
			if($MG['styleid'] == $r['itemid']) $r['fee'] = 0;
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
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
			DB::query("UPDATE ".DT_PRE."company SET styletime=0,styleid=0 WHERE styleid=$itemid");
			DB::query("DELETE FROM {$this->table}_order WHERE styleid=$itemid");
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function order($listorder) {
		if(!is_array($listorder)) return false;
		foreach($listorder as $k=>$v) {
			$k = intval($k);
			$v = intval($v);
			DB::query("UPDATE {$this->table} SET listorder=$v WHERE itemid=$k");
		}
		return true;
	}

	function del($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			DB::query("DELETE FROM {$this->table}_order WHERE itemid=$itemid");
		}
	}
	
	function get_order($condition = '1', $order = 'itemid DESC') {
		global $MODULE, $MOD, $TYPE, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table}_order WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table}_order WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['todate'] = timetodate($r['totime'], 5);
			$r['days'] = $r['totime'] > DT_TIME ? ceil(($r['totime']-DT_TIME)/86400) : 0;
			$r['thumb'] = is_file(DT_ROOT.'/static/home/'.$r['skin'].'/thumb.gif') ? DT_STATIC.'home/'.$r['skin'].'/thumb.gif' : DT_STATIC.'home/image/thumb.gif';
			$lists[] = $r;
		}
		return $lists;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>