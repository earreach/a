<?php
defined('DT_ADMIN') or exit('Access Denied');
$tb = isset($tb) ? strip_sql(trim($tb), 0) : '';
if(!$tb && $mid) $tb = get_table($mid);
$tb or msg();
$len = strlen($DT_PRE);
if(substr($tb, 0, $len) == $DT_PRE) $tb = substr($tb, $len);
$do = new fields();
$do->tb = $tb;
$menus = array (
    array('添加字段', '?file='.$file.'&tb='.$tb.'&action=add'),
    array('字段列表', '?file='.$file.'&tb='.$tb),
);
$this_forward = '?moduleid='.$moduleid.'&file='.$file.'&tb='.$tb;
switch($action) {
	case 'add':
		if($submit) {
			$post['tb'] = $tb;
			if($do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', $this_forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$name = $title = $note = $default_value = $input_limit = $width = $height = '';
			$option_value = "选项值A|选项名A*\n选项值B|选项名B*\n选项值C|选项名C*";
			$display = $front = $search = 1;
			$type = 'varchar';
			$length = 255;
			$html = 'text';
			$addition = 'size="30"';
			include tpl('fields_edit');
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $this_forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			include tpl('fields_edit');
		}
	break;
	case 'delete':
		$itemid or msg('请选择字段');
		foreach($itemid as $id) {
			$do->delete($id);
		}
		dmsg('删除成功', $forward);
	break;
	case 'update':
		$do->update($post);
		dmsg('更新成功', $this_forward);
	break;
	default:
		$lists = $do->get_list("tb='$tb'");
		cache_fields($tb);
		include tpl('fields');
	break;
}

class fields {
	var $itemid;
	var $tb;
	var $table;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'fields';
    }

    function fields() {
		$this->__construct();
    }

	function pass($post) {
		if(!is_array($post)) return false;
		if(!$post['name']) return $this->_('请填写字段');
		if(!check_name($post['name'])) return $this->_('字段名不合法');
		if(!$post['title']) return $this->_('请填写字段名称');
		if(in_array($post['html'], array('select', 'radio', 'checkbox'))) {
			if(!$post['option_value']) return $this->_('请填写选项值');
			if(strpos($post['option_value'], '|') === false) return $this->_('请填写正确的选项值');
		}
		return true;
	}

	function set($post) {
		$post['search'] = $post['search'] ? 1 : 0;
		if(!in_array($post['html'], array('select', 'radio', 'checkbox'))) {
			$post['option_value'] = '';
			$post['search'] = 0;
		}
		$post['length'] = intval($post['length']);
		if($post['html'] == 'textarea') {
			if($post['type'] != 'varchar' && $post['type'] != 'text') $post['type'] = 'text';
		} else if($post['html'] == 'checkbox' || $post['html'] == 'thumb' || $post['html'] == 'file') {
			$post['type'] = 'varchar';
			$post['length'] = 255;
		} else if($post['html'] == 'editor') {
			$post['type'] = 'text';
		} else if($post['html'] == 'area') {
			$post['type'] = 'int';
			$post['length'] = 10;
		}
		return $post;
	}

	function get_one() {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid'");
	}

	function get_list($condition = '', $order = 'listorder ASC,itemid ASC') {
		global $MOD, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		$length = 0;
		if($post['type'] == 'varchar') {
			$length = min($post['length'], 255);
		} else if($post['type'] == 'int') {
			$length = min($post['length'], 10);
		}
		$type = strtoupper($post['type']);
		if($length) $type .= "($length)";
		$name = '`'.$post['name'].'`';
        DB::query("ALTER TABLE ".DT_PRE."{$this->tb} ADD $name $type NOT NULL");
		unset($post['cname']);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0));
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
		$length = 0;
		if($post['type'] == 'varchar') {
			$length = min($post['length'], 255);
		} else if($post['type'] == 'int') {
			$length = min($post['length'], 10);
		}
		$type = strtoupper($post['type']);
		if($length) $type .= "($length)";
		$cname = '`'.$post['cname'].'`';
		unset($post['cname']);
		$name = '`'.$post['name'].'`';
        DB::query("ALTER TABLE ".DT_PRE."{$this->tb} CHANGE $cname $name $type NOT NULL");
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1)." WHERE itemid=$this->itemid");
		return true;
	}

	function delete($itemid) {
		$this->itemid = $itemid;
		$r = $this->get_one();
		$name = '`'.$r['name'].'`';
		DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
	    DB::query("ALTER TABLE ".DT_PRE."{$this->tb} DROP $name");
	}
	
	function update($post) {
		foreach($post as $k=>$v) {
			$k = intval($k);
			$listorder = intval($v['listorder']);
			$title = $v['title'];
			$display = $v['display'] ? 1 : 0;
			$front = $v['front'] ? 1 : 0;
			$search = $v['search'] ? 1 : 0;
			if(!in_array($v['html'], array('select', 'radio', 'checkbox'))) $search = 0;
			DB::query("UPDATE {$this->table} SET listorder=$listorder,display=$display,front=$front,search=$search,title='$title' WHERE itemid=$k");
		}
		return true;
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>