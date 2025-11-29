<?php 
defined('IN_DESTOON') or exit('Access Denied');
class company {
	var $userid;
	var $username;
	var $table_member;
	var $table_company;
	var $table_company_data;
	var $errmsg = errmsg;

    function __construct($username = '') {
		$this->table_member = DT_PRE.'member';
		$this->table_company = DT_PRE.'company';
		$this->table_company_data = DT_PRE.'company_data';
    }

    function company($username = '') {
		$this->__construct($username);
    }

	function get_one($username = '') {
		$condition = $username ? "username='$username'" : "userid=$this->userid";
		$r1 = DB::get_one("SELECT * FROM {$this->table_member} WHERE {$condition}");
		if(!$r1) return array();
		$r2 = DB::get_one("SELECT * FROM {$this->table_member}_misc WHERE {$condition}");
		$r3 = DB::get_one("SELECT * FROM {$this->table_company} WHERE {$condition}");
        return array_merge($r1, $r2, $r3);
	}

	function get_list($condition, $order = 'userid DESC', $cache = '') {
		global $pages, $page, $pagesize, $offset, $items, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table_company} WHERE {$condition}", $cache);
			$items = $r['num'];
		}
		$pages = defined('CATID') ? listpages(1, CATID, $items, $page, $pagesize, 10, $MOD['linkurl']) : pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = $userids = array();
		$result = DB::query("SELECT * FROM {$this->table_company} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}", $cache);
		while($r = DB::fetch_array($result)) {
			if($r['totime'] < DT_TIME && $r['vip'] > 0) $userids[] = $r['userid'];
			$lists[] = $r;
		}
		if($userids) $this->vip_delete($userids);
		return $lists;
	}

	function update($userid) {
		global $DT, $MOD;
		$this->userid = $userid;
		$r = $this->get_one();
		if(!$r) return false;
		$linkurl = userurl($r['username'], '', $r['domain']);
		$keyword = $r['company'];
		if($r['business'] && strpos($keyword, $r['business']) === false) $keyword .= ','.$r['business'];
		if($r['sell'] && strpos($keyword, $r['sell']) === false) $keyword .= ','.$r['sell'];
		if($r['buy'] && strpos($keyword, $r['buy']) === false) $keyword .= ','.$r['buy'];
		if($r['mode'] && strpos($keyword, $r['mode']) === false) $keyword .= ','.$r['mode'];
		if($r['areaid']) $keyword .= ','.strip_tags(area_pos($r['areaid'], ','));		
		$catids = '';
		if($r['catid']) {
			$catids = explode(',', substr($r['catid'], 1, -1));
			$cids = '';
			foreach($catids as $catid) {
				$C = get_cat($catid);
				if($C) {
					$catid = $C['parentid'] ? $C['arrparentid'].','.$catid : $catid;
					$cids .= $catid.',';
					if(strpos($keyword, $C['catname']) === false) $keyword .= ','.$C['catname'];
				}
			}
			$cids = array_unique(explode(',', substr(str_replace(',0,', ',', ','.$cids), 1, -1)));
			$catids = ','.implode(',', $cids).',';
			$r['catids'] = $catids;
		}
		$keyword = addslashes($keyword);
		if($r['vip']) {
			$vipt = $this->get_vipt($r['username']);
			$vip = $this->get_vip($vipt, $r['vipr']);
			$r['vip'] = $vip;
			DB::query("UPDATE {$this->table_company} SET linkurl='$linkurl',keyword='$keyword',catids='$catids',vip='$vip',vipt='$vipt' WHERE userid=$userid");
		} else {
			DB::query("UPDATE {$this->table_company} SET linkurl='$linkurl',keyword='$keyword',catids='$catids' WHERE userid=$userid");
		}
		return true;
	}

	function get_vipt($username) {
		global $MOD, $GROUP;
		$GROUP or $GROUP = cache_read('group.php');
		$r = $this->get_one($username);
		$_groupvip = $GROUP[$r['groupid']]['vip'] > $MOD['vip_maxgroupvip'] ? $MOD['vip_maxgroupvip'] : $GROUP[$r['groupid']]['vip'];
		$_cominfo = $r['validated'] ? intval($MOD['vip_cominfo']) : 0;
		$_year = $r['fromtime'] ? (date('Y', DT_TIME) - date('Y', $r['fromtime']))*$MOD['vip_year'] : 0;
		$_year = $_year > $MOD['vip_maxyear'] ? $MOD['vip_maxyear'] : $_year;
		$m = DB::get_one("SELECT COUNT(*) AS num FROM ".DT_PRE."honor WHERE username='$username' AND status=3");
		$_honor = $m['num'] > 4 ? $MOD['vip_honor'] : 0;
		$total = intval($_groupvip + $_cominfo + $_year + $_honor);
		if($total > 10) $total = 10;
		if($total < 1) $total = 1;
		return $total;
	}

	function get_vip($vipt, $vipr) {
		$vip = intval($vipt + ($vipr));
		if($vip > 10) $vip = 10;
		if($vip < 1) $vip = 1;
		return $vip;
	}

	function vip_edit($post, $user = array()) {
		global $_username;
		if(!is_array($post)) return false;
		if(!$post['username']) return $this->_(lang('message->pass_company_username'));
		$user or $user = $this->get_one($post['username']);
		if(!$user) return $this->_(lang('message->pass_company_notuser'));
		$userid = $user['userid'];
		if($user['groupid'] < 5) return $this->_(lang('message->pass_company_badgroup'));
		if(!$post['groupid']) return $this->_(lang('message->pass_company_group'));
		if(!$post['fromtime'] || !is_date($post['fromtime'])) return $this->_(lang('message->pass_company_fromdate'));
		if(!$post['totime'] || !is_date($post['totime'])) return $this->_(lang('message->pass_company_todate'));
		if(datetotime($post['fromtime'].' 00:00:00') > datetotime($post['totime'].' 23:59:59')) return $this->_(lang('message->pass_company_baddate'));
		$post['fromtime'] = datetotime($post['fromtime'].' 00:00:00');
		$post['totime'] = datetotime($post['totime'].' 23:59:59');
		$post['validated'] = $post['validated'] ? 1 : 0;
		$post['validtime'] = is_date($post['validtime']) ? datetotime($post['validtime']) : 0;
		$post['vipr'] = isset($post['vipr']) ? $post['vipr'] : 0;
		DB::query("UPDATE {$this->table_company} SET groupid='$post[groupid]',validated='$post[validated]',validator='$post[validator]',validtime='$post[validtime]',vipr='$post[vipr]',fromtime='$post[fromtime]',totime='$post[totime]' WHERE userid=$userid");
		$post['vipt'] = $this->get_vipt($post['username']);
		$post['vip'] = $this->get_vip($post['vipt'], $post['vipr']);
		DB::query("UPDATE {$this->table_company} SET vip='$post[vip]',vipt='$post[vipt]' WHERE userid=$userid");
		$UG = cache_read('group-'.$post['groupid'].'.php');
		$validate = $post['validated'] ? ($UG['type'] ? 2 : 1) : 0;
		DB::query("UPDATE {$this->table_member} SET groupid='$post[groupid]' WHERE userid=$userid");
		userclean($post['username']);
		return true;
	}

	function vip_delete($userid) {
		$userids = is_array($userid) ? implode(',', $userid) : intval($userid);
		$result = DB::query("SELECT * FROM {$this->table_member} WHERE userid IN ($userids)");
		while($r = DB::fetch_array($result)) {
			$regid = $r['regid'] ? $r['regid'] : 6;
			$userid = $r['userid'];
			DB::query("UPDATE {$this->table_company} SET groupid=$regid,vip=0,vipr=0,vipt=0 WHERE userid=$userid");
			DB::query("UPDATE {$this->table_member} SET groupid=$regid,regid=$regid WHERE userid=$userid");
			userclean($r['username']);
		}
		return true;
	}

	function level($userid, $level) {
		if(is_array($userid)) {
			foreach($userid as $v) { $this->level($v, $level); }
		} else {
			$this->userid = $userid;
			$user = $this->get_one();
			if($user) {
				DB::query("UPDATE {$this->table_company} SET level=$level WHERE userid=$userid");
				userclean($user['username']);
			}
			
		}
		return true;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>