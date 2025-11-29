<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
class admin {
	var $userid;
	var $username;
	var $table;
	var $errmsg = errmsg;

	function __construct() {
		$this->table = DT_PRE.'admin';
	}

	function admin() {
		$this->__construct();
	}

	function is_member($username) {
		return DB::get_one("SELECT userid FROM ".DT_PRE."member WHERE username='$username'");
	}

	function count_admin() {
		$r = DB::get_one("SELECT COUNT(*) AS num FROM ".DT_PRE."member WHERE groupid=1 AND admin=1 ");
		return $r['num'];
	}

	function set_admin($username, $admin, $role, $aid) {
		$username = trim($username);
		$r = $this->is_member($username);
		if(!$r) return $this->_('会员不存在');
		$this->userid = $userid = $r['userid'];
		$this->username = $username;
		if(is_founder($userid)) {
			$admin = 1;
			$aid = 0;
		}
		if($admin == 1) $aid = 0;
		DB::query("UPDATE ".DT_PRE."member SET groupid=1,admin=$admin,role='$role',aid=$aid WHERE userid=$userid");
		DB::query("UPDATE ".DT_PRE."company SET groupid=1 WHERE userid=$userid");
		return true;
	}

	function move_admin($username) {
		$r = $this->get_one($username);
		if($r && $r['admin'] > 0) {			
			if(is_founder($r['userid'])) return $this->_('创始人不可改变级别');
			if($r['admin'] == 1 && $this->count_admin() < 2) return $this->_('系统最少需要保留一位超级管理员');
			$admin = $r['admin'] == 1 ? 2 : 1;
			DB::query("UPDATE ".DT_PRE."member SET admin=$admin WHERE username='$username'");
			return true;
		} else {
			return $this->_('管理员不存在');
		}
	}

	function delete_admin($username) {
		$r = $this->get_one($username);
		if($r) {
			if(is_founder($r['userid'])) return $this->_('创始人不可删除');
			if($r['admin'] == 1 && $this->count_admin() < 2) return $this->_('系统最少需要保留一位超级管理员');
			$userid = $r['userid'];
			$groupid = $r['regid'] ? $r['regid'] : 6;
			if($groupid < 5) $groupid = 5;
			DB::query("UPDATE ".DT_PRE."member SET groupid=$groupid,admin=0,role='',aid=0 WHERE userid=$userid");
			DB::query("UPDATE ".DT_PRE."company SET groupid=$groupid WHERE userid=$userid");
			DB::query("DELETE FROM {$this->table} WHERE userid=$userid");
			cache_delete('admin-right-'.$userid.'.php');
			cache_delete('admin-panel-'.$userid.'.php');
			cache_delete('admin-menus-'.$userid.'.php');
			return true;
		} else {
			return $this->_('会员不存在');
		}
	}

	function get_one($user, $type = 1) {
		$fields = $type ? 'username' : 'userid';
        return DB::get_one("SELECT * FROM ".DT_PRE."member WHERE `$fields`='$user'");
	}

	function get_list($condition, $order = 'admin ASC,userid ASC') {
		global $pages, $page, $offset, $pagesize, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM ".DT_PRE."member WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM ".DT_PRE."member WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['logintime'] = timetodate($r['logintime'], 5);
			$r['adminname'] = $r['admin'] == 1 ? (is_founder($r['userid']) ? '<span class="f_red">网站创始人</span>' : '<span class="f_blue">超级管理员</span>') : '普通管理员';
			$lists[] = $r;
		}
		return $lists;
	}

	function get_right($userid) {
		global $MODULE;
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE userid=$userid AND url='' ORDER BY moduleid DESC,file DESC,itemid DESC ");
		while($r = DB::fetch_array($result)) {
			@include DT_ROOT.'/module/'.$MODULE[$r['moduleid']]['module'].'/admin/config.inc.php';
			$r['name'] = isset($RT['file'][$r['file']]) ? '('.$RT['file'][$r['file']].')' : '';
			$r['module'] = '('.$MODULE[$r['moduleid']]['name'].')';
			$lists[] = $r;
		}
		return $lists;
	}

	function get_panel($userid) {
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE userid=$userid AND url<>'' ORDER BY listorder ASC,itemid ASC ");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function menu_log($userid, $title, $url) {
		if(strlen($title) < 6 || strlen($title) > 24) return;
		if(strlen($url) < 8 || substr($url, 0, 1) != '?') return;
		foreach(array('panel', 'logout', 'dashboard', 'main') as $v) {
			if(strpos($url, '='.$v) !== false) return;
		}
		$title = dhtmlspecialchars($title);
		$url = dhtmlspecialchars($url);
		DB::query("INSERT INTO {$this->table}_hit (userid,title,url,addtime) VALUES('$userid','$title','$url',".DT_TIME.")");
	}

	function menu_list($condition, $order = 'itemid DESC') {
		global $pages, $page, $offset, $pagesize, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table}_hit WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table}_hit WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		return $lists;
	}

	function update($userid, $right, $admin) {
		if(isset($right[-1])) {
			$this->add($userid, $right[-1], $admin);
			unset($right[-1]);
			$type = 1;
		} else {
			$type = 0;
		}
		$this->add($userid, $right[0], $admin);
		unset($right[0]);
		$this->edit($right, $type);
		if($admin == 1) DB::query("DELETE FROM {$this->table} WHERE userid=$userid AND url=''");
		$this->cache_right($this->userid);
		$this->cache_panel($this->userid);
		$this->cache_menus($this->userid);
		return true;
	}

	function add($userid, $right, $admin) {
		global $MODULE;
		if(isset($right['url'])) {
			if(!$right['title'] || !$right['url']) return false;
			$r = DB::get_one("SELECT * FROM {$this->table} WHERE userid=$userid AND url='".$right['url']."'");
			if($r) return false;
			if($admin == 2 && defined('MANAGE_ADMIN')) {
				$r = $this->url_right($right['url']);
				if($r) $this->add($userid, $r, $admin);
			}
		} else {
			$mid = $right['moduleid'] = intval($right['moduleid']);
			if(!$mid) return false;
			$_right = $this->get_right($userid);			
			foreach($_right as $v) {
				if($v['file'] == '' && $v['moduleid'] == $mid) return false;
			}
			if($right['file']) {
				foreach($_right as $v) {
					if($v['file'] == $right['file'] && $v['moduleid'] == $mid) return false;
				}
			} else {
				unset($right['action'], $right['catid']);
				DB::query("DELETE FROM {$this->table} WHERE userid=$this->userid AND moduleid=$mid AND url=''");
			}
			$title = $MODULE[$mid]['name'].'管理';
			$url = '';
			if($mid == 1) {
				$url = $right['file'] ? '?file='.$right['file'] : '?moduleid='.$mid;
			} else {
				$url = '?moduleid='.$mid;
				if($right['file']) $url .= '&file='.$right['file'];
			}
			if($right['file']) {
				include DT_ROOT.'/module/'.$MODULE[$mid]['module'].'/admin/config.inc.php';
				if(isset($RT['file'][$right['file']])) $title = $RT['file'][$right['file']];
			}
		}
		$right['userid'] = $userid;
		DB::query("INSERT INTO {$this->table} ".arr2sql($right, 0));
		if($url) {
			$r = DB::get_one("SELECT * FROM {$this->table} WHERE userid=$userid AND url LIKE '%".$url."%'");
			if(!$r) DB::query("INSERT INTO {$this->table} (userid,title,url) VALUES ('$userid','$title','$url')");
		}
	}

	function edit($right, $type = 0) {
		if($type) {
			$moduleids = $itemids = array();
			foreach($right as $k=>$v) {
				if(!$v['file']) { 
					$moduleids[] = $v['moduleid'];
					$itemids[$v['moduleid']] = $k;
					$right[$k]['action'] = $right[$k]['catid'] = '';
				}
			}
			if($moduleids) {
				foreach($right as $k=>$v) {
					if(in_array($v['moduleid'], $moduleids) && !in_array($k, $itemids)) {
						unset($right[$k]);
						$this->delete($k, $v);
					}
				}
			}
		}
		foreach($right as $key=>$value) {
			if(isset($value['title'])) {
				if(!$value['title'] || !$value['url']) continue;
			} else {
				$value['moduleid'] = intval($value['moduleid']);
				if(!$value['moduleid']) continue;
			}
			DB::query("UPDATE {$this->table} SET ".arr2sql($value, 1)." WHERE itemid='$key'");
		}
	}

	function url_right($url) {
		if(substr($url, 0, 1) == '?') $url = substr($url, 1);
		$arr = array();
		parse_str($url, $tmp);
		$arr['moduleid'] = isset($tmp['moduleid']) ? $tmp['moduleid'] : 1;
		$arr['file'] = isset($tmp['file']) ? $tmp['file'] : 'index';
		$arr['action'] = isset($tmp['action']) ? $tmp['action'] : '';
		return $arr;
	}

	function cache_right($userid) {
		$rights = $this->get_right($userid);
		$right = $moduleids = array();
		foreach($rights as $v) {
			isset($moduleids[$v['moduleid']]) or $moduleids[$v['moduleid']] = $v['moduleid'];
		}
		foreach($moduleids as $m) {
			foreach($rights as $r) {
				if($r['moduleid'] == $m) {
					$r['file'] = $r['file'] ? $r['file'] : 'NA';
					$right[$m][$r['file']]['action'] = $r['action'] ? explode('|', $r['action']) : array();
					$right[$m][$r['file']]['catid'] = $r['catid'] ? explode('|', $r['catid']) : array();
					$right[$m][$r['file']]['self'] = $r['self'] ? '1' : '';
				}
			}
		}
		foreach($right as $k=>$v) {
			if(isset($v['NA'])) $right[$k] = '';
		}
		foreach($right as $k=>$v) {
			if($v) {
				foreach($v as $i=>$j) {
					if(!$j['action'] && !$j['catid'] && !$j['self']) $right[$k][$i] = '';
				}
			}
		}
		cache_write('admin-right-'.$userid.'.php', $right);		
	}

	function cache_panel($userid) {
		$lists = $this->get_panel($userid);
		$arr = $r = array();
		foreach($lists as $k=>$v) {
			$r['title'] = $v['title'];
			$r['style'] = $v['style'];
			$r['url'] = $v['url'];
			$arr[] = $r;
		}
		cache_write('admin-panel-'.$userid.'.php', $arr);
	}

	function cache_menus($userid) {
		$result = DB::query("SELECT itemid FROM {$this->table}_hit WHERE userid=$userid ORDER BY itemid DESC LIMIT 1000,1");
		while($r = DB::fetch_array($result)) {
			DB::query("DELETE FROM {$this->table}_hit WHERE userid=$userid AND itemid<=$r[itemid]");
			break;
		}
		$lists = array();
		$result = DB::query("SELECT COUNT(`url`) AS num,`url`,`title`,`style` FROM {$this->table}_hit WHERE userid=$userid GROUP BY `url` ORDER BY num DESC,itemid DESC LIMIT 0,9");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		cache_write('admin-menus-'.$userid.'.php', $lists);
	}

	function delete($itemid, $post) {
		if(!isset($post['url']) && isset($post['moduleid']) && $this->userid) {
			if($post['moduleid'] == 1) {
				$url = '?file='.$post['file'];
			} else {
				$url = '?moduleid='.$post['moduleid'];
				if($post['file'] && $post['file'] != 'index') $url .= '&file='.$post['file'];
			}
			DB::query("DELETE FROM {$this->table} WHERE userid=$this->userid AND url='$url'");
		}
		DB::query("DELETE FROM {$this->table} WHERE userid=$this->userid AND itemid=$itemid");
		$this->cache_right($this->userid);
		$this->cache_panel($this->userid);
		$this->cache_menus($this->userid);
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>