<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('添加产品', '?file='.$file.'&moduleid='.$moduleid.'&action=add'),
    array('产品管理', '?file='.$file.'&moduleid='.$moduleid),	
    array('报价管理', 'javascript:Dwidget(\'?file=price&moduleid='.$moduleid.'\', \'报价管理\');'),
);
$MOD['level'] = '';
$do = new product;
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&catid='.$post['catid']);
			} else {
				msg($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$content = '';
			$username = $_username;
			$status = 3;
			$addtime = timetodate($DT_TIME);
			$menuid = 0;
			include tpl('product_edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$addtime = timetodate($addtime);
			$menuid = 1;
			include tpl('product_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择产品');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'level':
		$itemid or msg('请选择产品');
		$level = intval($level);
		$do->level($itemid, $level);
		dmsg('级别设置成功', $forward);
	break;
	default:
		$sfields = array('按条件', '标题', '简介', '计量单位', '主要市场', '编辑', '参数名1', '参数名2', '参数名3', '参数值1', '参数值2', '参数值3');
		$dfields = array('title', 'title', 'content', 'unit', 'market', 'editor', 'n1', 'n2', 'n3', 'v1', 'v2', 'v3');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '报价时间降序', '报价时间升序', '报价数量降序', '报价数量升序', '浏览次数降序', '浏览次数升序', '最新价格降序', '最新价格升序', '参考低价降序', '参考低价升序', '参考高价降序', '参考高价升序', '推荐级别降序', '推荐级别升序', '信息ID降序', '信息ID升序');
		$dorder  = array('addtime DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'item DESC', 'item ASC', 'hits DESC', 'hits ASC', 'price DESC', 'price ASC', 'minprice DESC', 'minprice ASC', 'maxprice DESC', 'maxprice ASC', 'level DESC', 'level ASC', 'itemid DESC', 'itemid ASC');			
		isset($order) && isset($dorder[$order]) or $order = 0;
		$level = isset($level) ? intval($level) : 0;
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'edittime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$itemid or $itemid = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$level_select = level_select('level', '级别', $level, 'all');
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($catid) $condition .= ($CAT['child']) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		if($level) $condition .= $level > 9 ? " AND level>0" : " AND level=$level";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($itemid) $condition .= " AND itemid=$itemid";
		$timetype = strpos($dorder[$order], 'edit') === false ? 'add' : '';

		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('product', $module);
	break;
}

class product {
	var $itemid;
	var $table;
	var $fields;

	function __construct() {
		global $table_product;
		$this->table = $table_product;
		$this->fields = array('title','catid','level','style','unit','minprice','maxprice','n1','n2','n3','v1','v2','v3','market','addtime','editor','edittime','seo_title','seo_keywords','seo_description','content');
	}

	function product() {
		$this->__construct();
	}

	function pass($post) {
		if(!is_array($post)) return false;
		if(!$post['catid']) return $this->_('请选择所属分类');
		if(!$post['title']) return $this->_('请填写产品标题');
		if(!$post['unit']) return $this->_('请填写计量单位');
		return true;
	}

	function set($post) {
		global $MOD, $_username;
		$post['addtime'] = (isset($post['addtime']) && is_time($post['addtime'])) ? datetotime($post['addtime']) : DT_TIME;
		$post['editor'] = $_username;
		$post['edittime'] = DT_TIME;
		$post['minprice'] = dround($post['minprice']);
		$post['maxprice'] = dround($post['maxprice']);
		$post['content'] = addslashes(save_remote(save_local(stripslashes($post['content']))));
		if($this->itemid) {
			$new = $post['content'];
			$r = $this->get_one();
			$old = $r['content'];
			delete_diff($new, $old, $this->itemid);
		}
		return array_map("trim", $post);
	}

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid' $condition");
	}

	function get_list($condition = '1', $order = 'addtime DESC') {
		global $pages, $page, $offset, $pagesize, $MOD, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = $catids = $CATS = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			$r['linkurl'] = $MOD['linkurl'].rewrite('price'.DT_EXT.'?itemid='.$r['itemid']);
			$catids[$r['catid']] = $r['catid'];
			$lists[] = $r;
		}
		if($catids) {
			$result = DB::query("SELECT catid,catname,linkurl FROM ".DT_PRE."category WHERE catid IN (".implode(',', $catids).")");
			while($r = DB::fetch_array($result)) {
				$CATS[$r['catid']] = $r;
			}
			if($CATS) {
				foreach($lists as $k=>$v) {
					$lists[$k]['catname'] = $v['catid'] ? $CATS[$v['catid']]['catname'] : '';
					$lists[$k]['caturl'] = $v['catid'] ? $MOD['linkurl'].rewrite('product'.DT_EXT.'?catid='.$v['catid']) : '';
				}
			}
		}
		return $lists;
	}

	function add($post) {
		$post = $this->set($post);
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0, $this->fields));
		$this->itemid = DB::insert_id();
		clear_upload($post['content'], $this->itemid, $this->table);
		return $this->itemid;
	}

	function edit($post) {
		$post = $this->set($post);
	    DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1, $this->fields)." WHERE itemid=$this->itemid");
		clear_upload($post['content'], $this->itemid, $this->table);
		return true;
	}

	function delete($itemid) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			$this->itemid = $itemid;
			$r = $this->get_one();
			$userid = get_user($r['username']);
			if($r['content']) delete_local($r['content'], $userid);
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function level($itemid, $level) {
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		DB::query("UPDATE {$this->table} SET level=$level WHERE itemid IN ($itemids)");
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>