<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/extend/spider.class.php';
$sp = new spider();
$table = $DT_PRE.'spider';
$num = intval($v1);
$num > 0 or $num = 10;
$result = $db->query("SELECT * FROM {$table}_data WHERE status=2 ORDER BY itemid ASC LIMIT 0,$num");
while($r = $db->fetch_array($result)) {	
	$itemid = $r['itemid'];
	$s = $sp->get($r['sid']);
	$setting = $s['setting'];
	$config = $s['config'];
	if($config['func']) include_once DT_ROOT.'/api/spider/'.$config['func'].'.func.php';
	$post = $sp->get_data($r['html'], $r['sid']);
	if($config['inc']) include DT_ROOT.'/api/spider/'.$config['inc'].'.inc.php';
	$post = daddslashes($post);
	$id = 0;
	$note = '';
	$status = 3;
	if($s['mid']) {
		$_table = $table;
		$_moduleid = $moduleid;

		$moduleid = $s['mid'];
		$module = $MODULE[$moduleid]['module'];
		$MOD = cache_read('module-'.$moduleid.'.php');
		if($config['save']) $MOD['save_remotepic'] = $post['save_remotepic'] = $post['thumb_no'] = 1;
		if($config['clear']) $MOD['clear_link'] = $post['clear_link'] = 1;
		require_once DT_ROOT.'/module/'.$module.'/global.func.php';
		if($moduleid > 4) {
			$table = $DT_PRE.$module.'_'.$moduleid;
			$table_data = $DT_PRE.$module.'_data_'.$moduleid;
		}
		include_once DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
		$po = new $module($moduleid);
		if(in_array('thumbs', $po->fields)) {
			$thumbs = array();
			if(isset($post['thumb']) && is_url($post['thumb'])) $thumbs[] = $post['thumb'];
			if(isset($post['thumb1']) && is_url($post['thumb1'])) $thumbs[] = $post['thumb1'];
			if(isset($post['thumb2']) && is_url($post['thumb2'])) $thumbs[] = $post['thumb2'];
			if(isset($post['thumbs']) && $post['thumbs']) {
				if(is_array($post['thumbs'])) {
					foreach($post['thumbs'] as $v) {
						if(is_url($v) && !in_array($v, $thumbs)) $thumbs[] = $v;
					}
				} else {
					foreach(explode('|', $post['thumbs']) as $v) {
						if(is_url($v) && !in_array($v, $thumbs)) $thumbs[] = $v;
					}
				}
			}
			$post['thumbs'] = $thumbs;
		}
		if($po->pass($post)) $id = $po->add($post);
		if($id) {
			if($moduleid == 2) {
				$FD = cache_read('fields-member.php');
				if($FD) {
					$fields = array();
					foreach($FD as $k=>$v) {
						$kk = $v['name'];
						if(isset($post[$kk])) $fields[$kk] = $post[$kk];
					}
					if($fields) $db->query("UPDATE {$DT_PRE}member SET ".arr2sql($fields, 1)." WHERE userid=$id");
				}
				$FD = cache_read('fields-company.php');
				if($FD) {
					$fields = array();
					foreach($FD as $k=>$v) {
						$kk = $v['name'];
						if(isset($post[$kk])) $fields[$kk] = $post[$kk];
					}
					if($fields) $db->query("UPDATE {$DT_PRE}company SET ".arr2sql($fields, 1)." WHERE userid=$id");
				}
			} else {
				$FD = cache_read('fields-'.substr($table, strlen($DT_PRE)).'.php');
				if($FD) {
					$fields = array();
					foreach($FD as $k=>$v) {
						$kk = $v['name'];
						if(isset($post[$kk])) $fields[$kk] = $post[$kk];
					}
					if($fields) $db->query("UPDATE {$table} SET ".arr2sql($fields, 1)." WHERE itemid=$id");
				}
			}
		} else {
			$note = addslashes($po->errmsg);
			$status = 4;
		}				

		$moduleid = $_moduleid;
		$table = $_table;
	} else {
		if($post) {
			$db->query("INSERT INTO {$s[tb]} ".arr2sql($post, 0));
			$id = $db->insert_id();
		} else {
			//
		}
	}
	$db->query("UPDATE {$table}_data SET status=$status,posttime=$DT_TIME,tid=$id,note='$note' WHERE itemid=$itemid");
	$db->query("UPDATE {$table}_url SET status=$status,posttime=$DT_TIME WHERE itemid=$itemid");
}
?>