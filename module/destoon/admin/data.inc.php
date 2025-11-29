<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/include/module.func.php';
$menus = array (
    array('数据备份', '?file=database'),
    array('数据恢复', '?file=database&action=import'),
    array('执行SQL', '?file=database&action=execute'),
    array('显示进程', '?file=database&action=process'),
    array('字段校验', '?file=database&action=verify" onclick="Dtoast(\'校验中...\', 0, 30);'),
    array('字符替换', '?file=database&action=replace'),
    array('数据互转', '?file=data&action=move'),
    array('数据导入', '?file=data&action=import'),
    array('数据导出', '?file=data'),
);
$this_forward = '?file='.$file;
isset($table) or $table = '';
if($table) $table = strip_sql($table, 0);
isset($name) or $name = '';
if($name && !preg_match("/^[0-9a-z_\-\.]+$/i", $name)) msg('不是一个有效的文件名');
function table_get_key($table) {
	$key = '';
	if($table) {
		$result = DB::query("SHOW COLUMNS FROM `$table`");
		while($r = DB::fetch_array($result)) {
			if($r['Key'] == 'PRI' && stripos($r['Type'], 'int') !== false) {
				$key = $r['Field'];
				break;
			}
		}
	}
	return $key;
}
function parse_dict($table) {
	global $MODULE;
	$fds = array();
	if(strpos($table, DT_PRE) === false) {
		$rtable = $table;
	} else {
		$rtable = substr($table, strlen(DT_PRE));
		$fds = cache_read('fields-'.$rtable.'.php');
		$rtable = preg_replace("/_[0-9]{1,}/", '', $rtable);
		if(is_numeric($rtable) && isset($MODULE[$rtable])) $rtable = $MODULE[$rtable]['module'].'_data';
	}
	$names = array();	
	if(is_file(DT_ROOT.'/file/setting/dict_'.$rtable.'.php')) {
		$tmp = file_get(DT_ROOT.'/file/setting/dict_'.$rtable.'.php');
		if(substr($tmp, 0, 13) == '<?php exit;?>') $tmp = trim(substr($tmp, 13));
		$arr = explode("\n", $tmp);
		foreach($arr as $v) {
			$t = explode(',', $v);
			$names[$t[0]] = $t[1];
		}
	}
	if($fds) {
		foreach($fds as $v) {
			if(isset($names[$v['name']]) && $names[$v['name']]) continue;
			$names[$v['name']] = $v['title'];
		}
	}
	return $names;
}
switch($action) {
	case 'move':
		if($submit) {
			($fmid > 0 && $tmid > 0 && $fmid != $tmid) or msg('来源模块或目标模块设置错误');
			$catid or msg('请选择新分类');
			$condition = trim($condition);
			if(strtolower(substr($condition, 0, 3)) != 'and') $condition = "AND itemid IN ($condition)";
			$post = array();
			$post['fmid'] = $fmid;
			$post['tmid'] = $tmid;
			$post['condition'] = $condition;
			$post['catid'] = $catid;
			$post['delete'] = $delete;
			$post = dstripslashes($post);
			cache_write('table-move-'.$_userid.'.php', $post);
			msg('正在开始转移', '?file='.$file.'&action=move_table');
		} else {
			include tpl('data_move');
		}
	break;
	case 'move_table':
		$post = cache_read('table-move-'.$_userid.'.php');
		$post or msg('数据配置不存在', '?file='.$file.'&action=move');
		$fmid = $post['fmid'];
		$tmid = $post['tmid'];
		$ftb = get_table($fmid);
		$ftb_data = get_table($fmid, 1);
		$ttb = get_table($tmid);
		$ttb_data = get_table($tmid, 1);
		$table = $ftb;
		$id = 'itemid';
		$condition = $post['condition'];
		$catid = $post['catid'];
		$delete = $post['delete'];
		isset($num) or $num = 1000;
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min({$id}) AS fid FROM {$table}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max({$id}) AS tid FROM {$table}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		isset($$id) or $$id = 1;
		$fs = array();
		$result = $db->query("SHOW COLUMNS FROM `$ttb`");
		while($r = $db->fetch_array($result)) {
			$fs[] = $r['Field'];
		}
		if($fid <= $tid) {
			$result = $db->query("SELECT * FROM {$table} WHERE `{$id}`>=$fid {$condition} ORDER BY `{$id}` LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$$id = $fitemid = $r[$id];
					unset($r[$id]);
					$r['catid'] = $catid;
					$r = daddslashes($r);
					if(is_file(DT_CACHE.'/'.$fmid.'.part')) $ftb_data = split_table($fmid, $fitemid);
					$t = $db->get_one("SELECT content FROM {$ftb_data} WHERE itemid=$fitemid");
					$content = daddslashes($t['content']);
					$db->query("INSERT INTO {$ttb} ".arr2sql($r, 0, $fs));
					$titemid = $db->insert_id();
					if(is_file(DT_CACHE.'/'.$tmid.'.part')) $ttb_data = split_table($tmid, $titemid);
					$db->query("INSERT INTO {$ttb_data} (itemid,content)  VALUES ('$titemid','$content')");
					$linkurl = str_replace($fitemid, $titemid, $r['linkurl']);
					$db->query("UPDATE {$ttb} SET linkurl='$linkurl' WHERE itemid=$titemid");
					if($delete) {
						$db->query("UPDATE {$ftb} SET status=0 WHERE itemid=$fitemid");
						$html = DT_ROOT.'/'.$MODULE[$fmid]['moduledir'].'/'.$r['linkurl'];
						if(is_file($html)) file_del($html);
					}
				}
				$$id += 1;
			} else {
				$$id = $fid + $num;
			}
		} else {
			cache_delete('table-move-'.$_userid.'.php');
			msg('转移成功', '?file='.$file.'&action=move');
		}
		msg('ID '.$fid.'~'.($$id-1).'转移成功', '?file='.$file.'&action='.$action.'&fid='.$$id.'&tid='.$tid.'&num='.$num);
	break;
	case 'save':
		$table or msg('请选择导入目标表');
		$name or msg('数据文件不存在');
		$xlsfile = DT_ROOT.'/file/temp/'.$name.'.xls';
		is_file($xlsfile) or msg('数据文件不存在');
		function table_get_fields($table) {
			$arr = array();
			$result = DB::query("SHOW COLUMNS FROM `{$table}`");
			while($r = DB::fetch_array($result)) {
				$arr[] = $r['Field'];
			}
			return $arr;
		}
		function table_get_query($fields, $arr) {
			$sqlk = $sqlv = '';
			foreach($arr as $k=>$v) {
				if(!in_array($k, $fields)) continue;
				$sqlk .= ',`'.$k.'`'; $sqlv .= ",'$v'";
			}
			if($sqlk) $sqlk = substr($sqlk, 1);
			if($sqlv) $sqlv = substr($sqlv, 1);
			return array($sqlk, $sqlv);
		}
		function data_get_name($fields, $lists) {
			$arr = array();
			foreach($fields as $k=>$v) {
				if(isset($lists[$k])) {
					if(strpos($v, 'time') === false) {
						$arr[$v] = convert($lists[$k], 'GBK', 'UTF-8');
					} else {
						$arr[$v] = is_numeric($lists[$k]) ? $lists[$k] : datetotime($lists[$k]);
					}
				}
			}
			return $arr;
		}
		$type = 'table';
		$tb = cutstr($table, DT_PRE);
		$table_data = '';
		if($tb == 'member') {
			$type = 'member';
			$split = is_file(DT_CACHE.'/4.part') ? 1 : 0;
			$table_member_misc = DT_PRE.'member_misc';
			$fields_member_misc = table_get_fields($table_member_misc);
			$table_company = DT_PRE.'company';
			$fields_company = table_get_fields($table_company);
			$table_data = DT_PRE.'company_data';
			$fields_data = table_get_fields($table_data);
			include DT_ROOT.'/module/member/member.class.php';
			$do = new member;
		} else if(substr_count($tb, '_') == 1) {
			list($mod, $mid) = explode('_', $tb);
			if(is_numeric($mid) && isset($MODULE[$mid]) && $MODULE[$mid]['module'] == $mod) {
				$type = 'module';
				$split = is_file(DT_CACHE.'/'.$mid.'.part') ? 1 : 0;
				$table_data = get_table($mid, 1);
				$fields_data = table_get_fields($table_data);
			}
		}
		if($type == 'table') {
			if($tb == 'news' || $tb == 'page') $table_data = $table.'_data';
			if($table_data) $fields_data = table_get_fields($table_data);
		}
		require DT_ROOT.'/api/excel/loader.inc.php';
		$xls = new ExcelParser(DT_ROOT.'/file/temp/'.$name.'.xls');
		$arr = $xls->main();
		isset($arr[1][0]) or msg('未读取到有效数据');
		$lists = $arr[1][0];
		$names = $lists[1];
		$j = 0;
		$fields = table_get_fields($table);
		for($i = 2; $i < count($lists); $i++) {
			if(isset($lists[$i]) && $lists[$i]) {
				$data = data_get_name($names, $lists[$i]);
				if($type == 'member') {
					$data['groupid'] = isset($data['groupid']) ? intval($data['groupid']) : 0;
					if($data['groupid'] < 5) $data['groupid'] = 5;
					$data['regid'] = isset($data['regid']) ? intval($data['regid']) : 0;
					if($data['regid'] < 5) $data['regid'] = $data['groupid'];
					$data['username'] = isset($data['username']) ? trim($data['username']) : '';
					if(!check_name($data['username'])) $data['username'] = 'uid-'.$do->get_uid();
					$data['passport'] = isset($data['passport']) ? trim($data['passport']) : '';
					if(!$data['passport'] || !$do->is_passport($data['passport'], 1)) $data['passport'] = $data['username'];
					$data['email'] = isset($data['email']) ? trim($data['email']) : '';
					if(!is_email($data['email'])) $data['email'] = $data['username'].'@data.sns';
					$data['password'] = isset($data['password']) ? trim($data['password']) : '';
					$data['passsalt'] = isset($data['passsalt']) ? trim($data['passsalt']) : '';
					$data['payword'] = isset($data['payword']) ? trim($data['payword']) : '';
					$data['paysalt'] = isset($data['paysalt']) ? trim($data['paysalt']) : '';
					if(strlen($data['password']) == 32 && strlen($data['passsalt']) == 8) {
					} else {
						if(strlen($data['password']) < 6 || strlen($data['password']) > 32) $data['password'] = $do->get_pwd();
						if(strlen($data['passsalt']) != 8) $data['passsalt'] = random(8);
						$data['password'] = dpassword($data['password'], $data['passsalt']);
					}
					if(strlen($data['payword']) == 32 && strlen($data['paysalt']) == 8) {
					} else {
						$data['payword'] = $data['password'];
						$data['paysalt'] = $data['passsalt'];
					}
					$data['regtime'] = isset($data['regtime']) ? trim($data['regtime']) : '';
					$data['regtime'] = is_date($data['regtime']) || is_time($data['regtime']) ? datetotime($data['regtime']) : DT_TIME;
					$data['logintime'] = isset($data['logintime']) ? trim($data['logintime']) : '';
					$data['logintime'] = is_date($data['logintime']) || is_time($data['logintime']) ? datetotime($data['logintime']) : DT_TIME;
				}
				list($sqlk, $sqlv) = table_get_query($fields, $data);
				if($sqlk && $sqlv) {
					$db->query("INSERT INTO {$table} ($sqlk) VALUES ($sqlv)");
					$id = $db->insert_id();
					if($id) {
						$j++;
						if($type == 'table') {							
							if($table_data) {
								$data['itemid'] = $id;
								list($sqlk, $sqlv) = table_get_query($fields_data, $data);
								if($sqlk && $sqlv) $db->query("INSERT INTO {$table_data} ($sqlk) VALUES ($sqlv)");
							}
						} else if($type == 'member') {
							$data['userid'] = $id;

							list($sqlk, $sqlv) = table_get_query($fields_member_misc, $data);
							if($sqlk && $sqlv) $db->query("INSERT INTO {$table_member_misc} ($sqlk) VALUES ($sqlv)");

							list($sqlk, $sqlv) = table_get_query($fields_company, $data);
							if($sqlk && $sqlv) $db->query("INSERT INTO {$table_company} ($sqlk) VALUES ($sqlv)");

							list($sqlk, $sqlv) = table_get_query($fields_data, $data);
							if($sqlk && $sqlv) {
								$tb_data = content_table(4, $id, $split, $table_data);
								$db->query("INSERT INTO {$tb_data} ($sqlk) VALUES ($sqlv)");
							}
						} else if($type == 'module') {
							$data['itemid'] = $id;
							list($sqlk, $sqlv) = table_get_query($fields_data, $data);
							if($sqlk && $sqlv) {
								$tb_data = content_table($mid, $id, $split, $table_data);
								$db->query("INSERT INTO {$tb_data} ($sqlk) VALUES ($sqlv)");
							}
						}
					}
				}
			}
		}
		file_del($xlsfile);
		msg('成功导入'.$j.'条数据', '?file='.$file.'&action=import');
	break;
	case 'upload':
		$table or msg('请选择导入目标表');
		$_FILES['uploadfile']['size'] or msg('请上传xls数据文件');
		require DT_ROOT.'/include/upload.class.php';
		$name = date('YmdHis').mt_rand(10, 99).$_userid;
		$upload = new upload($_FILES, 'file/temp/', $name.'.xls', 'xls');
		$upload->adduserid = false;
		if($upload->save()) {
			require DT_ROOT.'/api/excel/loader.inc.php';
			$xls = new ExcelParser(DT_ROOT.'/file/temp/'.$name.'.xls');
			$arr = $xls->main();
			isset($arr[1][0]) or msg('未读取到有效数据');
			$lists = $arr[1][0];
			$T = $D = array();
			$T = $lists[0];
			for($i = 1; $i < 12; $i++) {
				if(isset($lists[$i]) && $lists[$i]) $D[] = $lists[$i];
			}
			$t1 = count($lists) - 2;
			$t2 = count($D) - 1;
			include tpl('data_view');
		} else {
			msg($upload->errmsg);
		}
	break;
	case 'import':
		$tables = array();
		$i = 0;
		$result = $db->query("SHOW TABLE STATUS FROM `".$CFG['db_name']."`");
		while($r = $db->fetch_array($result)) {
			if(preg_match('/^'.$DT_PRE.'/', $r['Name'])) {
				$tables[$i]['name'] = $r['Name'];
				$tables[$i]['note'] = $r['Comment'];
				$i++;
			}
		}
		include tpl('data_import');
	break;
	case 'fields':
		$table or exit;
		$N = parse_dict($table);
		$fields_select = $time_select = '';		
		$result = $db->query("SHOW COLUMNS FROM `$table`");
		while($r = $db->fetch_array($result)) {
			$fields_select .= '<option value="'.$r['Field'].'">'.$r['Field'].(isset($N[$r['Field']]) ? ' ('.$N[$r['Field']].')' : '').'</option>';
			if(strpos($r['Field'], 'time') !== false && stripos($r['Type'], 'int(10)') !== false) $time_select .= '<option value="'.$r['Field'].'"'.($r['Field'] == 'addtime' ? ' selected' : '').'>'.$r['Field'].(isset($N[$r['Field']]) ? ' ('.$N[$r['Field']].')' : '').'</option>';
		}
		$select = '<select name="fields[]" id="fd" multiple="multiple" size="2" style="height:500px;width:300px;"><option value="">选择字段(按Ctrl多选)</option>'.$fields_select.'</select>';
		$time = $time_select ? '<select name="timetype">'.$time_select.'</select>' : '';
		$key = table_get_key($table);
		$order = $key ? $key.' DESC' : '';
		exit(json_encode(array('select' => $select, 'time' => $time, 'order' => $order)));
	break;
	case 'pages':
		$psize > 0 or $psize = 5000;
		$total = $db->count($table, '1 '.$condition);
		$page = ceil(intval($total)/$psize);
		exit('{"page":"'.$page.'","total":"'.$total.'","ok":"1"}');
	break;
	case 'download':
		$table or msg('请选择数据表');
		$ismember = $table == DT_PRE.'member' ? 1 : 0;
		isset($fields) or $fields = array();
		$fields = $fields ? implode(',', $fields) : '*';
		$condition = '1 '.$condition;
		if(strpos($condition, DT_PRE) !== false) $condition = '1';
		if($ismember) $condition .= ' AND groupid>1';
		if(isset($timetype) && strpos($timetype, 'time') !== false) {
			(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
			$fromtime = $fromdate ? datetotime($fromdate) : 0;
			(isset($todate) && is_time($todate)) or $todate = '';
			$totime = $todate ? datetotime($todate) : 0;
			if($fromtime) $condition .= " AND `$timetype`>=$fromtime";
			if($totime) $condition .= " AND `$timetype`<=$totime";
		}
		if(!$order) {
			$key = table_get_key($table);
			if($key) $order = $key.' DESC';
		}
		$order = $order ? 'ORDER BY '.$order : '';
		in_array($ext, array('csv', 'xml', 'json')) or $ext = 'csv';
		$data = '';
		$lists = $list = array();
		$result = $db->query("SELECT {$fields} FROM {$table} WHERE {$condition} {$order} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			if($ismember) {
				foreach(array('password', 'passsalt', 'payword', 'paysalt') as $v) {
					if(isset($r[$v])) unset($r[$v]);
				}
			}
			if(!$data) $list = $r;
			if($ext == 'csv') {
				foreach($r as $k=>$v) {
					if(strpos($k, 'time') !== false) $v = ' '.timetodate($v, 6).' ';
					$data .= '"'.$v.'",';
				}
				$data .= "\n";
			} else if($ext == 'xml') {
				$data .= "\t".'<item>'."\n";
				foreach($r as $k=>$v) {
					if(strpos($k, 'time') !== false) $v = timetodate($v, 6);
					if(strpos($v, '<') !== false || strpos($v, "\n") !== false) {
						$data .= "\t\t".'<'.$k.'><![CDATA['.$v.']]></'.$k.'>'."\n";
					} else {
						$data .= "\t\t".'<'.$k.'>'.$v.'</'.$k.'>'."\n";
					}
				}
				$data .= "\t".'</item>'."\n";
			} else {
				$data = 'json';
				foreach($r as $k=>$v) {
					if(strpos($k, 'time') !== false) $r[$k] = timetodate($v, 6);
				}
				$lists[] = $r;
			}
		}
		if($list) {
			if($ext == 'csv') {
				$N = parse_dict($table);
				$T = '';
				foreach($list as $k=>$v) {
					$T .= '"'.(isset($N[$k]) ? $N[$k] : $k).'",';
				}
				$T .= "\n";
				foreach($list as $k=>$v) {
					$T .= '"'.$k.'",';
				}
				$data = $T."\n".$data;
				$data = convert($data, DT_CHARSET, 'GBK');
			} else if($ext == 'xml') {
				$N = parse_dict($table);
				$T = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
				$T .= '<'.$table.'>'."\n";
				$T .= "\t".'<item>'."\n";
				foreach($list as $k=>$v) {
					$T .= "\t\t".'<'.$k.'>'.(isset($N[$k]) ? $N[$k] : $k).'</'.$k.'>'."\n";
				}
				$T .= "\t".'</item>'."\n";
				$data = $T.$data;
				$data .= '</'.$table.'>'."\n";
				$data .= '</xml>';
			} else {
				$data = json_encode($lists);
			}
		}
		if($data) file_down('', $table.'_'.$page.'.'.$ext, $data);
		msg('没有符合条件的数据');
	break;
	default:
		$table_select = '';
		$tables = array();
		$result = $db->query("SHOW TABLE STATUS FROM `".$CFG['db_name']."`");
		while($r = $db->fetch_array($result)) {
			$tb = $r['Name'];
			$table_select .= '<option value="'.$tb.'"'.($tb == $table ? ' selected' : '').'>'.$tb.' ('.$r['Comment'].')</option>';
			$tables[] = $tb;
		}
		include tpl('data');
	break;
}
?>