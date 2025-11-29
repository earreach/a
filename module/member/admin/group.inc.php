<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('会员组添加', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file='.$file.'&action=add\', \'会员组添加\');'),
    array('会员组管理', '?moduleid='.$moduleid.'&file='.$file),
    array('积分组管理', '?moduleid='.$moduleid.'&file='.$file.'&action=credit'),
    array('更新缓存', '?moduleid='.$moduleid.'&file='.$file.'&action=cache'),
);
$do = new group;
$gradeid = isset($gradeid) ? intval($gradeid) : 0;
$groupid = isset($groupid) ? intval($groupid) : 0;
$groupname = isset($groupname) ? trim($groupname) : '';
$vip = isset($vip) ? intval($vip) : 0;
$do->groupid = intval($groupid);
$do->gradeid = intval($gradeid);
$do->groupname = $groupname;
$do->vip = $vip;
$tab = isset($tab) ? intval($tab) : 0;
$all = isset($all) ? intval($all) : 0;
$this_forward = '?moduleid='.$moduleid.'&file='.$file;
if($action == 'add') {
	if($submit) {
		if(!$groupname) msg('会员组名称不能为空');
		if($setting['fee_mode']) {//收费会员
			if($vip > 9) $do->vip = $vip = 9;
			if($vip < 1) $do->vip = $vip = 1;
			$setting['fee'] = intval($setting['fee']);
			if($setting['fee'] < 1) $setting['fee'] = 3000;
		} else {
			$do->vip = $vip = $setting['fee'] = 0;
		}
		$setting['fee_12'] = $setting['fee'];
		$do->add($setting, $home);
		dmsg('添加成功', $this_forward);
	} else {
		include load('homepage.lang');
		$do->groupid = 7;
		extract($do->get_one());
		$D_MENU = $do->get_home('menu');
		$G_MENU = $do->get_home('menu', 7);
		$D_SIDE = $do->get_home('side');
		$G_SIDE = $do->get_home('side', 7);
		$D_MAIN = $do->get_home('main');
		$G_MAIN = $do->get_home('main', 7);
		$groupname = '';
		$discount = 100;
		include tpl('group_edit', $module);
	}
} else if($action == 'edit') {
	$groupid or msg();
	$r = $do->get_one();
	$r or msg('会员组不存在');
	if($submit) {
		if(!$groupname) msg('会员组名称不能为空');
		if($setting['fee_mode']) {//收费会员
			if($vip > 9) $do->vip = $vip = 9;
			if($vip < 1) $do->vip = $vip = 1;
			$setting['fee'] = intval($setting['fee']);
			if($setting['fee'] < 1) $setting['fee'] = 3000;
			$setting['reg'] = 0;
		} else {
			$do->vip = $vip = $setting['fee'] = 0;
		}
		$setting['fee_12'] = $setting['fee'];
		$do->listorder = intval($listorder);
		$do->edit($setting, $home);
		dmsg('保存成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&groupid='.$groupid.'&tab='.$tab);
	} else {
		include load('homepage.lang');
		extract($r);
		$D_MENU = $do->get_home('menu');
		$G_MENU = $do->get_home('menu', $groupid);
		$D_SIDE = $do->get_home('side');
		$G_SIDE = $do->get_home('side', $groupid);
		$D_MAIN = $do->get_home('main');
		$G_MAIN = $do->get_home('main', $groupid);
		if($kw) {
			$all = 1;
			ob_start();
		}
		include tpl('group_edit', $module);
		if($kw) {
			$data = $content = ob_get_contents();
			ob_clean();
			$data = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$kw.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', '<span class=highlight>'.$kw.'</span>', $data);
			$data = preg_replace('/<span class=highlight>/', '<a name=high></a><span class=highlight>', $data, 1);
			echo $data ? $data : $content;
		}
	}
} else if($action == 'delete') {
	$groupid or msg();
	$do->delete();
	dmsg('删除成功', $this_forward);
} else if($action == 'order') {	
	$do->order($listorder);
	dmsg('排序成功', $forward);
} else if($action == 'cache') {	
	cache_module();
	cache_group();
	cache_grade();
	dmsg('更新成功', $forward);
} else if($action == 'grade') {
	$gradeid or msg();
	$r = $do->grade_one();
	$r or msg('积分组不存在');
	if($submit) {
		$do->grade_setting($setting);
		dmsg('保存成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&gradeid='.$gradeid.'&tab='.$tab);
	} else {
		extract($r);
		include tpl('group_grade', $module);
	}
} else if($action == 'credit') {
	if($job == 'delete') {
		$itemid or msg('请选择积分组');
		$do->grade_delete($itemid);
		dmsg('删除成功', $forward);
	} else if($job == 'update') {
		$do->grade_update($post);
		dmsg('更新成功', $forward);
	} else {
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}member_grade ORDER BY gradeid ASC");
		while($r = $db->fetch_array($result)) {
			$r['style_select'] = dstyle('post['.$r['gradeid'].'][style]', $r['style']);
			$lists[] = $r;
		}
		$new_style = dstyle('post[0][style]');
		include tpl('group_credit', $module);
	}
} else {
	$GROUP = cache_read('group.php');
	$lists = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}member_group ORDER BY listorder ASC,groupid ASC");
	while($r = $db->fetch_array($result)) {
		$r['diy'] = $r['groupid'] > 7 ? 1 : 0;
		$r['type'] = $GROUP[$r['groupid']]['type'];
		$r['fee'] = $GROUP[$r['groupid']]['fee'];
		$lists[] = $r;
	}
	include tpl('group', $module);
}

class group {
	var $gradeid;
	var $groupid;
	var $groupname;
	var $vip;
	var $listorder;
	var $table;
	var $table_grade;

	function __construct() {
		$this->table = DT_PRE.'member_group';
		$this->table_grade = DT_PRE.'member_grade';
	}

	function group() {
		$this->__construct();
	}

	function add($setting, $home) {
		if(!is_array($setting) || !is_array($home)) return false;
		DB::query("INSERT INTO {$this->table} (groupname,vip) VALUES('$this->groupname','$this->vip')");
		$this->groupid = DB::insert_id();
		DB::query("UPDATE {$this->table} SET `listorder`=`groupid` WHERE groupid=$this->groupid");
		update_setting('group-'.$this->groupid, $setting);
		$this->home($home);
		cache_group();
		return $this->groupid;
	}

	function edit($setting, $home) {
		if(!is_array($setting) || !is_array($home)) return false;
		DB::query("UPDATE {$this->table} SET groupname='$this->groupname',vip='$this->vip',listorder='$this->listorder' WHERE groupid=$this->groupid");
		update_setting('group-'.$this->groupid, $setting);
		$this->home($home);
		cache_group();
		return true;
	}

	function home($home) {
		$table = DT_PRE.'member_home';
		DB::query("DELETE FROM {$table} WHERE groupid=$this->groupid");
		foreach($home as $k1=>$v1) {
			if(!in_array($k1, array('menu', 'side', 'main'))) continue;
			foreach($v1 as $k2=>$v2) {
				if(!check_name($k2)) continue;
				$name = trim(htmlspecialchars($v2['name']));
				$pagesize = intval($v2['pagesize']);
				if($pagesize > 100) $pagesize = 100;
				if($pagesize < 1) $pagesize = 1;
				$listorder = intval($v2['listorder']);
				$status = intval($v2['status']);
				DB::query("INSERT INTO {$table} (groupid,type,file,name,pagesize,listorder,status) VALUES('$this->groupid','$k1','$k2','$name','$pagesize','$listorder','$status')");
			}
		}
	}

	function get_home($type, $groupid = 0) {
		$arr = array();
		if($groupid) {
			$result = DB::query("SELECT * FROM ".DT_PRE."member_home WHERE groupid=$groupid AND type='$type' ORDER BY listorder ASC");
			while($r = DB::fetch_array($result)) {
				$arr[$r['file']] = $r;
			}
		} else {
			//include load('homepage.lang');
			$HM = cache_read('home.php');
			if($type == 'side' || $type == 'main') return $HM[$type];
			global $MODULE;
			foreach($HM['menu'] as $k=>$v) {
				$arr[$k] = is_numeric($v) ? $MODULE[$v]['name'] : $v;
			}
		}
		return $arr;
	}

	function order($listorder) {
		if(!is_array($listorder)) return false;
		foreach($listorder as $k=>$v) {
			$k = intval($k);
			$v = intval($v);
			if($v > 6) DB::query("UPDATE {$this->table} SET listorder=$v WHERE groupid=$k");
		}
		cache_group();
		return true;
	}

	function delete() {
		if($this->groupid < 8) return false;
		DB::query("DELETE FROM {$this->table} WHERE groupid=$this->groupid");
		cache_delete('group-'.$this->groupid.'.php');
		cache_group();
		return true;
	}

	function get_one() {
		$r = DB::get_one("SELECT * FROM {$this->table} WHERE groupid=$this->groupid");
		$tmp = get_setting('group-'.$this->groupid);
		if($tmp) {
			foreach($tmp as $k=>$v) {
				isset($r[$k]) or $r[$k] = $v;
			}
		}
		return $r;
	}

	function grade_one() {
		$r = DB::get_one("SELECT * FROM {$this->table_grade} WHERE gradeid=$this->gradeid");
		$tmp = get_setting('grade-'.$this->gradeid);
		if($tmp) {
			foreach($tmp as $k=>$v) {
				isset($r[$k]) or $r[$k] = $v;
			}
		}
		return $r;
	}

	function grade_setting($setting) {
		if(!is_array($setting)) return false;
		update_setting('grade-'.$this->gradeid, $setting);
		cache_grade();
		return true;
	}

	function grade_update($post) {
		foreach($post as $k=>$v) {
			$k = intval($k);
			$name = dhtmlspecialchars(trim(strip_tags($v['name'])));
			$credit = intval($v['credit']);
			$style = dhtmlspecialchars($v['style']);
			if($k > 0) {
				$icon = is_file(DT_ROOT.'/static/image/grade-'.$k.'.png') ? 1 : 0;
				DB::query("UPDATE {$this->table_grade} SET name='$name',style='$style',credit='$credit',icon='$icon' WHERE gradeid=$k");
			} else {
				if($name && $credit) DB::query("INSERT INTO {$this->table_grade} SET name='$name',style='$style',credit='$credit'");
			}
		}
		cache_grade();
		return true;
	}

	function grade_delete($itemid) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("DELETE FROM {$this->table_grade} WHERE gradeid IN ($itemids)");
		cache_grade();
		return true;
	}
}
?>