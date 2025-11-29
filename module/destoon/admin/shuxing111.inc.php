<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('地区添加', '?file='.$file.'&action=add'),
    array('地区管理', '?file='.$file),
    array('更新缓存', '?file='.$file.'&action=cache'),
	array('导入省市', '?file='.$file.'&action=import','onclick="return confirm(\'确定导入中国省市数据吗？ 当前数据将被覆盖 \');"'),

);
$shuxing = cache_read('shuxing.php');
$shuxingid = isset($shuxingid) ? intval($shuxingid) : 0;
$do = new shuxing($shuxingid);
$type = $do->get_type();
if($type) $menus[] = array('导入省市县', '?file='.$file.'&action=import&job=shuxing','onclick="return confirm(\'确定导入中国省市县数据吗？ 当前数据将被覆盖 \');"');
$parentid = isset($parentid) ? intval($parentid) : 0;
$table = $DT_PRE.'shuxing';
$this_forward = '?file='.$file.'&parentid='.$parentid;

switch($action) {
	case 'add':
		if($submit) {
			if(!$shuxing['shuxingname']) msg('地区名不能为空');
			$shuxing['shuxingname'] = trim($shuxing['shuxingname']);
			if(strpos($shuxing['shuxingname'], "\n") === false) {
				$do->add($shuxing);
			} else {
				$shuxingnames = explode("\n", $shuxing['shuxingname']);
				foreach($shuxingnames as $shuxingname) {
					$shuxingname = trim($shuxingname);
					if(!$shuxingname) continue;
					$shuxing['shuxingname'] = $shuxingname;
					$do->add($shuxing);
				}
			}
			$do->repair();
			dmsg('添加成功', $this_forward);
		} else {
			include tpl('shuxing_add');
		}
	break;
	case 'import':
		if($type) {
			$name = $job == 'shuxing' ? 'shuxing2021' : 'city2021';
		} else {
			$name = 'shuxing';
		}
		$file = DT_ROOT.'/file/backup/'.$name.'.sql';
		is_file($file) or msg('数据文件不存在，请上传程序包内 file/backup/'.$name.'.sql 文件至 file/backup 目录');
		require DT_ROOT.'/include/sql.func.php';
		sql_execute(file_get($file));
		cache_shuxing();
		dmsg('导入成功', $this_forward);
	break;
	case 'cache':
		$do->repair();
		dmsg('更新成功', $forward);
	break;
	case 'delete':
		if($shuxingid) $shuxingids = $shuxingid;
		$shuxingids or msg();
		$do->delete($shuxingids);
		dmsg('删除成功', $this_forward);
	break;
	case 'update':
		if(!$shuxing || !is_array($shuxing)) msg();
		$do->update($shuxing);
		dmsg('更新成功', $this_forward);
	break;
	default:
		$Dshuxing = array();
		$condition = $keyword ? "shuxingname LIKE '%$keyword%'" : "parentid=$parentid";
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY listorder,shuxingid");
		while($r = $db->fetch_array($result)) {
			$r['childs'] = substr_count($r['arrchildid'], ',');
			$Dshuxing[$r['shuxingid']] = $r;
		}
		include tpl('shuxing');
	break;
}

class shuxing {
	var $shuxingid;
	var $shuxing = array();
	var $table;

	function __construct($shuxingid = 0)	{
		global $shuxing;
		$this->shuxingid = $shuxingid;
		$this->shuxing = $shuxing;
		$this->table = DT_PRE.'shuxing';
	}

	function shuxing($shuxingid = 0)	{
		$this->__construct($shuxingid);
	}

	function add($shuxing)	{
		if(!is_array($shuxing)) return false;
		$sql1 = $sql2 = $s = '';
		foreach($shuxing as $key=>$value) {
			$sql1 .= $s.$key;
			$sql2 .= $s."'".$value."'";
			$s = ',';
		}
		DB::query("INSERT INTO {$this->table} ($sql1) VALUES($sql2)");		
		$this->shuxingid = DB::insert_id();
		if($shuxing['parentid']) {
			$shuxing['shuxingid'] = $this->shuxingid;
			$this->shuxing[$this->shuxingid] = $shuxing;
			$arrparentid = $this->get_arrparentid($this->shuxingid);
		} else {
			$arrparentid = 0;
		}
		DB::query("UPDATE {$this->table} SET arrchildid='$this->shuxingid',listorder=$this->shuxingid,arrparentid='$arrparentid' WHERE shuxingid=$this->shuxingid");
		return true;
	}

	function delete($shuxingids) {
		if(is_array($shuxingids)) {
			foreach($shuxingids as $shuxingid) {
				if(isset($this->shuxing[$shuxingid])) {
					$arrchildid = $this->shuxing[$shuxingid]['arrchildid'];
					DB::query("DELETE FROM {$this->table} WHERE shuxingid IN ($arrchildid)");
				}
			}
		} else {
			$shuxingid = $shuxingids;
			if(isset($this->shuxing[$shuxingid])) {
				$arrchildid = $this->shuxing[$shuxingid]['arrchildid'];
				DB::query("DELETE FROM {$this->table} WHERE shuxingid IN ($arrchildid)");
			}
		}
		$this->repair();
		return true;
	}

	function update($shuxing) {
	    if(!is_array($shuxing)) return false;
		foreach($shuxing as $k=>$v) {
			if(!$v['shuxingname']) continue;
			$v['parentid'] = intval($v['parentid']);
			if($k == $v['parentid']) continue;
			if($v['parentid'] > 0 && !isset($this->shuxing[$v['parentid']])) continue;
			$v['listorder'] = intval($v['listorder']);
			DB::query("UPDATE {$this->table} SET shuxingname='$v[shuxingname]',parentid='$v[parentid]',listorder='$v[listorder]' WHERE shuxingid=$k");
		}
		cache_shuxing();
		return true;
	}

	function repair() {		
		$query = DB::query("SELECT * FROM {$this->table} ORDER BY listorder,shuxingid");
		$shuxing = array();
		while($r = DB::fetch_array($query)) {
			$shuxing[$r['shuxingid']] = $r;
		}
		$childs = array();
		foreach($shuxing as $shuxingid => $shuxing) {
			$arrparentid = $this->get_arrparentid($shuxingid);
			DB::query("UPDATE {$this->table} SET arrparentid='$arrparentid' WHERE shuxingid=$shuxingid");
			if($arrparentid) {
				$arr = explode(',', $arrparentid);
				foreach($arr as $a) {
					if($a == 0) continue;
					isset($childs[$a]) or $childs[$a] = '';
					$childs[$a] .= ','.$shuxingid;
				}
			}
		}
		foreach($shuxing as $shuxingid => $shuxing) {
			if(isset($childs[$shuxingid])) {
				$arrchildid = $shuxingid.$childs[$shuxingid];
				DB::query("UPDATE {$this->table} SET arrchildid='$arrchildid',child=1 WHERE shuxingid='$shuxingid'");
			} else {
				DB::query("UPDATE {$this->table} SET arrchildid='$shuxingid',child=0 WHERE shuxingid='$shuxingid'");
			}
		}
		cache_shuxing();
        return true;
	}

	function get_arrparentid($shuxingid) {
		$ARE = get_shuxing($shuxingid);
		if($ARE['parentid'] && $ARE['parentid'] != $shuxingid) {
			$parents = array();
			$cid = $shuxingid;
			$i = 1;
			while($i++ < 10) {
				$ARE = get_shuxing($cid);
				if($ARE['parentid']) {
					$parents[] = $cid = $ARE['parentid'];
				} else {
					break;
				}
			}
			$parents[] = 0;
			return implode(',', array_reverse($parents));
		} else {
			return '0';
		}
	}

	function get_type() {
		$t = DB::get_one("SELECT * FROM {$this->table} ORDER BY shuxingid");
		if($t) return $t['shuxingid'] < 110000 ? false : true;
		return true;
	}
}
?>