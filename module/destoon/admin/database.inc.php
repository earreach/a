<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/include/sql.func.php';
$menus = array (
    array('数据备份', '?file='.$file),
    array('数据恢复', '?file='.$file.'&action=import'),
    array('执行SQL', '?file='.$file.'&action=execute'),
    array('显示进程', '?file='.$file.'&action=process'),
    array('字段校验', '?file='.$file.'&action=verify" onclick="Dtoast(\'校验中...\', 0, 30);'),
    array('字符替换', '?file='.$file.'&action=replace'),
    array('数据互转', '?file=data&action=move'),
    array('数据导入', '?file=data&action=import'),
    array('数据导出', '?file=data'),
);
$this_forward = '?file='.$file;
$D = DT_ROOT.'/file/backup/';
isset($dir) or $dir = '';
isset($table) or $table = '';
if($table) $table = strip_sql($table, 0);
function table_get_key($table) {
	$key = '';
	if($table) {
		$result = DB::query("SHOW COLUMNS FROM `{$table}`");
		while($r = DB::fetch_array($result)) {
			if($r['Key'] == 'PRI' && stripos($r['Type'], 'int') !== false) {
				$key = $r['Field'];
				break;
			}
		}
	}
	return $key;
}
function table_get_next($table) {
	$tbs = cache_read('table.php');
	$tb = '';
	if($tbs) {
		if($table) {
			$k = array_search($table, $tbs);
			if(is_numeric($k)) {
				$m = count($tbs);
				for($i = $k + 1; $i < $m; $i++) {
					if(table_get_key($tbs[$i])) {
						$tb = $tbs[$i];
						break;
					}
				}
			}
		} else {
			$tb = $tbs[0];
		}
	}
	return $tb;
}
function table_get_default() {
	$V = array();
	$tmp = file_get(DT_ROOT.'/file/setting/table.php');
	$tmp or msg('配置文件不存在，请上传 file/setting/table.php');
	if(substr($tmp, 0, 13) == '<?php exit;?>') $tmp = trim(substr($tmp, 13));
	foreach(explode('DROP TABLE', $tmp) as $tb) {
		if(strpos($tb, 'CREATE TABLE') != false) {
			$table = cutstr($tb, 'CREATE TABLE `', '`');
			$table = substr($table, strlen(DT_PRE));
			$table = preg_replace("/_[0-9]{1,}/", '', $table);
			if($table) {
				foreach(explode("\n", $tb) as $t) {
					$t = trim($t);
					if(substr($t, 0, 1) != '`' || substr($t, -1, 1) != ',') continue;
					$V[$table][trim(cutstr($t, '`', '`'))] = substr(trim(cutstr($t, '` ')), 0, -1);
				}
			}
		}
	}
	return $V;
}
function parse_dict($table, $job = '') {
	global $MODULE;
	$fds =  $names = $notes = array();	
	if(strpos($table, DT_PRE) === false) {
		$rtable = $table;
	} else {
		$rtable = substr($table, strlen(DT_PRE));
		$fds = cache_read('fields-'.$rtable.'.php');
		$rtable = preg_replace("/_[0-9]{1,}/", '', $rtable);
		if(is_numeric($rtable) && isset($MODULE[$rtable])) $rtable = $MODULE[$rtable]['module'].'_data';
	}
	if($job == 'table') return $rtable;
	$file = DT_ROOT.'/file/setting/dict_'.$rtable.'.php';
	if($job == 'file') return $file;
	if(is_file($file)) {
		$tmp = file_get($file);
		if(substr($tmp, 0, 13) == '<?php exit;?>') $tmp = trim(substr($tmp, 13));
		$arr = explode("\n", $tmp);
		foreach($arr as $v) {
			$t = explode(',', $v);
			$names[$t[0]] = $t[1];
			$notes[$t[0]] = $t[2];
		}
	}
	if($fds) {
		foreach($fds as $v) {
			if(isset($names[$v['name']]) && $names[$v['name']]) continue;
			$names[$v['name']] = $v['title'];
			$notes[$v['name']] = '';
		}
	}
	if($job == 'note') return $notes;
	return $names;
}
switch($action) {
	case 'repair':
		$DT['close'] or msg('为了数据安全，此操作必须先在<a href="?file=setting">网站设置</a>里临时关闭网站');
		isset($tables) or msg('请指定数据表');
		is_array($tables) or $tables = array($tables);
		count($tables) < 10 or msg('此操作比较耗费服务器资源，请控制在10个表内');
		foreach($tables as $table) {
			$table = strip_sql($table, 0);
			$db->query("REPAIR TABLE `{$table}`");
		}
		dmsg('修复成功', $forward);
	break;
	case 'optimize':
		$DT['close'] or msg('为了数据安全，此操作必须先在<a href="?file=setting">网站设置</a>里临时关闭网站');
		isset($tables) or msg('请指定数据表');
		is_array($tables) or $tables = array($tables);
		count($tables) < 10 or msg('此操作比较耗费服务器资源，请控制在10个表内');
		foreach($tables as $table) {
			$table = strip_sql($table, 0);
			$db->query("OPTIMIZE TABLE `{$table}`");
		}
		dmsg('优化成功', $forward);
	break;
	case 'drop':
		isset($tables) or msg('请指定数据表');
		is_array($tables) or $tables = array($tables);
		foreach($tables as $table) {
			$table = strip_sql($table, 0);
			if(strpos($table, $DT_PRE) === false) $db->query("DROP TABLE `{$table}`");
		}
		dmsg('删除成功', $forward);
	break;
	case 'execute':
		if(!isset($CFG['executesql']) || !$CFG['executesql']) msg('系统禁止了执行SQL，请FTP修改根目录config.inc.php<br/>$CFG[\'executesql\'] = \'0\'; 修改为 $CFG[\'executesql\'] = \'1\';');
		isset($sql) or $sql = '';
		$lists = array();
		if($table) {
			$sql = "SELECT * FROM ".$table;
			$submit = 1;
		}
		if($submit) {
			$sql = trim($sql);
			if(!$sql) {
				msg('SQL语句为空');
			} else {
				$sql = stripslashes($sql);
				$sql = strip_sql($sql, 0);
				if(strtoupper(substr($sql, 0, 7)) == 'SELECT ' && strpos($sql, "\n") === false) {
					if(substr($sql, -1) == ';') $sql = substr($sql, 0, -1);
					if(stripos($sql, 'LIMIT ') === false) $sql .= " LIMIT 0,$pagesize";
					$result = $db->query($sql);
					while($r = $db->fetch_array($result)) {
						$lists[] = $r;
					}
					$fds = parse_dict($table ? $table : $DT_PRE.cutstr($sql, ' '.$DT_PRE, ' '));
				} else {
					sql_execute($sql);
					dmsg('执行成功', '?file='.$file.'&action=execute');
				}
			}
		}
		include tpl('database_execute');
	break;
	case 'process':
		$i = 0;
		$lists = $tags = array();
		$result = $db->query("SHOW FULL PROCESSLIST");
		while($r = $db->fetch_array($result)) {
			if($r['db'] == $CFG['db_name']) {
				$lists[$i++] = $r;
			} else {				
				$tags[$i++] = $r;
			}
		}
		$lists = $lists + $tags;
		include tpl('database_process');
	break;
	case 'kill':
		$db->halt = 0;
		if($itemid) {
			if(is_array($itemid)) {
				foreach($itemid as $id) {
					$db->query("KILL $id");
				}
			} else {
				$db->query("KILL $itemid");
			}
		}
		dmsg('结束成功', '?file='.$file.'&action=process');
	break;
	case 'comments':
		$db->halt = 0;
		$C = include(DT_ROOT.'/file/setting/comment.php');
		$C or msg('配置文件不存在，请上传 file/setting/comment.php');
		foreach($C as $k=>$v) {
			$sql = "ALTER TABLE `{$DT_PRE}{$k}` COMMENT='{$v}'";
			$db->query($sql);
		}
		foreach($MODULE as $k=>$v) {
			if(is_file(DT_ROOT.'/file/setting/table_'.$v['module'].'.php')) {
				$sql = "ALTER TABLE `".$DT_PRE.$v['module']."_".$v['moduleid']."` COMMENT='".$v['name']."'";
				$db->query($sql);
				$sql = "ALTER TABLE `".$DT_PRE.$v['module']."_data_".$v['moduleid']."` COMMENT='".$v['name']."内容'";
				$db->query($sql);
			}
		}
		dmsg('重建成功', '?file='.$file);
	break;
	case 'comment':
		$table or msg('Table为空');
		if($submit) {
			$name = trim($name);
			$db->query("ALTER TABLE `{$table}` COMMENT='{$name}'");
			dmsg('修改成功', '?file='.$file.'&action='.$action.'&table='.$table.'&note='.urlencode($name));
		} else {
			include tpl('database_comment');
		}
	break;
	case 'dict':
		$table or msg('Table为空');
		$dict_file = parse_dict($table, 'file');
		if($submit) {
			$csv = "<?php exit;?>\n";
			foreach($name as $k=>$v) {
				$v = str_replace(',', '，', $v);
				$n = str_replace(',', '，', $note[$k]);
				$csv .= $k.','.$v.','.$n."\n";
			}
			file_put($dict_file, trim($csv));
			dmsg('更新成功', '?file='.$file.'&action='.$action.'&job='.$job.'&table='.$table.'&note='.urlencode($nt));
		} else {
			$names = parse_dict($table);
			$notes = parse_dict($table, 'note');
			$fields = $F = $R = array();
			$result = $db->query("SHOW COLUMNS FROM `{$table}`");
			while($r = $db->fetch_array($result)) {
				$r['Type'] = str_replace(' unsigned', '', $r['Type']);
				$F[$r['Field']] = $r['Type'];
				if(isset($names[$r['Field']])) {
					$r['cn_name'] = $names[$r['Field']];
					$r['cn_note'] = $notes[$r['Field']];
				} else {
					$r['cn_name'] = $r['cn_note'] = '';
				}
				$fields[] = $r;
			}
			if($job == 'verify') {
				$rtable = parse_dict($table, 'table');
				$V = table_get_default();
				$V = isset($V[$rtable]) ? $V[$rtable] : array();
				if($V) {
					//自定义字段移除
					$tn = substr($table, strlen(DT_PRE));
					if(preg_match("/[0-9]{1,}/", $tn) && is_file(DT_CACHE.'/fields-'.$tn.'.php')) {
						foreach(cache_read('fields-'.$tn.'.php') as $f) {
							if(isset($F[$f['name']])) unset($F[$f['name']]);
							$R[$f['name']] = '<span class="f_blue">自定义字段</span>';
						}
					}
					foreach($F as $k=>$v) {
						if(isset($V[$k])) {
							$v = str_replace(array('longtext', 'mediumtext'), array('text', 'text'), strtolower($v));
							if(stripos($V[$k], $v) === false) {
								$R[$k] = '<span class="f_red" title="应为 '.cutstr($V[$k], '', ' ').'">类型错误</span>';
							} else {
								$R[$k] = '<span class="f_green"><img src="'.DT_STATIC.'image/yes.png" title="校验一致" align="absmiddle"/> 通过</span>';
							}
							unset($V[$k]);
						} else {
							$R[$k] = '<span class="f_orange" title="如果是自行添加的字段，可忽略">多余字段</span>';
						}
					}
				} else {
					foreach($F as $k=>$v) {
						$R[$k] = '<span class="f_gray" title="非系统表，无法校验">未知</span>';
					}
				}
			}
			include tpl('database_dict');
		}
	break;
	case 'export':
		if(!$table) msg();
		//$memory_limit = trim(@ini_get('memory_limit'));
		$sizelimit = 1024*1024;//Max 1G
		file_down('', $table.'.sql', sql_dumptable($table));
	break;
	case 'download':
		$file_ext = file_ext($filename);
		$file_ext == 'sql' or msg('只能下载SQL文件');
		file_down($dir ? $D.$dir.'/'.$filename : $D.$filename);
	break;
	case 'view':
		$file_ext = file_ext($filename);
		$file_ext == 'sql' or msg('只能查看SQL文件');
		$file_path = $dir ? $D.$dir.'/'.$filename : $D.$filename;
		is_file($file_path) or msg('SQL文件不存在');
		$file_size = round(filesize($file_path)/(1024*1024), 2);
		$file_size < 20 or msg('文件体积过大，不支持在线查看');
		$content = file_get($file_path);
		include tpl('database_view');
	break;
	case 'delete':
		if(!is_array($filenames)) {
			$tmp = $filenames;
			$filenames = array();
			$filenames[0] = $tmp;
		}
		foreach($filenames as $filename) {
			if(file_ext($filename) == 'sql' || substr($filename, -8) == '.sql.php') {
				file_del($dir ? $D.$dir.'/'.$filename : $D.$filename);
			} else if(is_dir($D.$filename)) {
				dir_delete($D.$filename);
			}
		}
		dmsg('删除成功', $forward);
	break;
	case 'fields':
		$table or exit;
		$N = parse_dict($table);
		$fields_select = '';
		$result = $db->query("SHOW COLUMNS FROM `{$table}`");
		while($r = $db->fetch_array($result)) {
			$fields_select .= '<option value="'.$r['Field'].'">'.$r['Field'].(isset($N[$r['Field']]) ? ' ('.$N[$r['Field']].')' : '').'</option>';
		}
		echo '<select name="post[fields]" id="fd"><option value="">选择字段</option>'.$fields_select.'</select>';
		exit;
	break;
	case 'replace':
		if($submit) {
			$post['table'] = strip_sql($post['table'], 0);
			$post['key'] = table_get_key($post['table']);
			$post['num'] = intval($post['num']);
			$post = dstripslashes($post);
			cache_write('table-replace.php', $post);
			if($post['type'] == 1) {
				if(!$post['from']) msg('请填写查找内容');
				if($post['table']) {
					if(!$post['key']) message('表'.$post['table'].'无主键，无法完成操作');
					if($post['key'] == $post['fields']) msg('无法完成对主键操作，请更换字段');
				}
				msg('正在开始替换', '?file='.$file.'&action=replace_table');
			} else {
				if(!$post['table'] || !$post['fields']) msg('请选择字段');
				if(!$post['add']) msg('请填写追加内容');
				if(!$post['key']) message('表'.$post['table'].'无主键，无法完成操作');
				if($post['key'] == $post['fields']) msg('无法完成对主键操作，请更换字段');
				msg('正在开始追加', '?file='.$file.'&action=replace_add');
			}
		} else {
			$table_select = '';
			$tables = array();
			$query = $db->query("SHOW TABLE STATUS FROM `".$CFG['db_name']."`");
			while($r = $db->fetch_array($query)) {
				$table = $r['Name'];
				if(preg_match("/^".$DT_PRE."/i", $table)) {
					$table_select .= '<option value="'.$table.'">'.$table.' ('.$r['Comment'].')</option>';
					$tables[] = $table;
				}
			}
			cache_write('table.php', $tables);
			$sql_select = '';
			$sqlfiles = glob($D.'*');
			if(is_array($sqlfiles)) {				
				$sqlfiles = array_reverse($sqlfiles);
				foreach($sqlfiles as $id=>$sqlfile)	{
					$tmp = basename($sqlfile);
					if(is_dir($sqlfile)) $sql_select .= '<option value="'.$tmp.'">'.$tmp.'</option>'; 
				}
			}
			include tpl('database_replace');
		}
	break;
	case 'replace_table':
		$post = cache_read('table-replace.php');
		$post or msg('数据配置不存在', '?file='.$file.'&action=replace');
		if($post['table']) {
			$table = $post['table'];
			$id = $post['key'];
		} else {
			if($table) {
				$id or msg('替换成功', '?file=database&action=replace');
			} else {
				$table = DT_PRE.'404';
				$id = 'itemid';
			}
		}
		$fields = $post['fields'];
		$fds = $fields ? "`{$id}`,`{$fields}`" : "*";
		$condition = $post['condition'];
		$num = $post['num'];
		$num > 0 or $num = 1000;
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min({$id}) AS fid FROM {$table}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max({$id}) AS tid FROM {$table}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		isset($$id) or $$id = 1;
		if($fid <= $tid) {
			$result = $db->query("SELECT {$fds} FROM {$table} WHERE `{$id}`>=$fid {$condition} ORDER BY `{$id}` LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$$id = $r[$id];
					$sql = '';
					foreach($r as $k=>$v) {
						if(strpos($v, $post['from']) !== false) {
							$v = addslashes(str_replace($post['from'], $post['to'], $v));
							$sql .= ",`$k`='$v'";
						}
					}
					if($sql) {
						$sql = substr($sql, 1);
						$db->query("UPDATE {$table} SET {$sql} WHERE `{$id}`={$$id}");
					}
				}
				$$id += 1;
			} else {
				$$id = $fid + $num;
			}
		} else {
			if($post['table']) {
				msg('替换成功', '?file=database&action=replace');
			} else {
				$tb = table_get_next($table);
				if($tb) {
					$id = table_get_key($tb);
					msg('表 '.$table.' 替换成功', '?file='.$file.'&action='.$action.'&table='.$tb.'&id='.$id, 0);
				} else {
					msg('替换成功', '?file=database&action=replace');
				}
			}
		}
		msg('ID '.$fid.'~'.($$id-1).'替换成功', '?file='.$file.'&action='.$action.'&table='.$table.'&id='.$id.'&fid='.$$id.'&tid='.$tid.'&num='.$num, 0);
	break;
	case 'replace_add':
		$post = cache_read('table-replace.php');
		$post or msg('数据缓存不存在', '?file='.$file.'&action=replace');
		$table = $post['table'];
		$id = $post['key'];
		$fields = $post['fields'];
		$condition = $post['condition'];
		$num = $post['num'];
		$num > 0 or $num = 1000;
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min({$id}) AS fid FROM {$table}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max({$id}) AS tid FROM {$table}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}		
		isset($$id) or $$id = 1;
		if($fid <= $tid) {
			$result = $db->query("SELECT `{$id}`,`{$fields}` FROM {$table} WHERE `{$id}`>=$fid {$condition} ORDER BY `{$id}` LIMIT 0,$num ");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$$id = $r[$id];
					$data = addslashes($post['type'] == 2 ? $post['add'].$r[$fields] : $r[$fields].$post['add']);
					$db->query("UPDATE {$table} SET `{$fields}`='{$data}' WHERE `{$id}`={$$id}");
				}
				$$id += 1;
			} else {
				$$id = $fid + $num;
			}
		} else {
			msg('追加成功', '?file='.$file.'&action=replace');
		}
		msg('ID '.$fid.'~'.($$id-1).'追加成功', '?file='.$file.'&action='.$action.'&fid='.$$id.'&tid='.$tid.'&num='.$num, 0);
	break;
	case 'replace_file':
		if(!$file_pre) msg('请选择备份系列');
		if(!$file_from) msg('请请填写查找内容');
		isset($tid) or $tid = count(glob($D.$file_pre.'/*.sql'));
		$fileid = isset($fileid) ? $fileid : 1;
		$filename = $file_pre.'/'.$fileid.'.sql';
		$dfile = $D.$filename;
		$file_from = urldecode($file_from);
		$file_to = urldecode($file_to);
		if(is_file($dfile)) {
			$sql = file_get($dfile);
			$sql = str_replace($file_from, $file_to, $sql);
			file_put($dfile, $sql);
			$fid = $fileid;
			msg('分卷 <strong>#'.$fileid++.'</strong> 替换成功 程序将自动继续...'.progress(0, $fid, $tid), '?file='.$file.'&action='.$action.'&file_pre='.$file_pre.'&fileid='.$fileid.'&tid='.$tid.'&file_from='.urlencode($file_from).'&file_to='.urlencode($file_to), 0);
		} else {
			msg('文件内容替换成功', '?file='.$file.'&action=replace');
		}
	break;
	case 'open':
		if(!$dir) msg('请选择备份系列');
		if(!is_dir($D.$dir)) msg('备份系列不存在');
		$sql = $sqls = array();
		$sqlfiles = glob($D.$dir.'/*.sql');
		if(!$sqlfiles) msg('备份系列文件不存在');
		$tid = count($sqlfiles);
		foreach($sqlfiles as $id=>$sqlfile)	{
			$tmp = basename($sqlfile);
			$size = filesize($sqlfile);
			$sql['filename'] = $tmp;
			$sql['filesize'] = round($size/(1024*1024), 2);
			$sql['filesize'] = $sql['filesize'] < 0.01 ? round($size/1024, 2).'K' : $sql['filesize'].'M';
			$sql['pre'] = $dir;
			$sql['number'] = str_replace('.sql', '', $tmp);
			$sql['mtime'] = timetodate(filemtime($sqlfile), 5);
			$sql['btime'] = substr(str_replace('.', ':', $dir), 0, -3);
			$sqls[$sql['number']] = $sql;
		}
		include tpl('database_open');
	break;
	case 'note':
		if(!$dir) exit('ko');
		if(!is_dir($D.$dir)) exit('ko');
		file_put($D.$dir.'/0.txt', strip_tags($note));
		exit('ok');
	break;
	case 'verify':
		$sfields = array('按条件', '表名', '字段');
		$sorder  = array('结果排序方式', '表名降序', '表名升序', '大小降序', '大小升序', '记录降序', '记录升序', '字段数降序', '字段数升序');
		$dstatus  = array('未知', '异常', '通过');
		isset($fields) && isset($sfields[$fields]) or $fields = 0;
		isset($order) && isset($sorder[$order]) or $order = 0;
		isset($status) && isset($dstatus[$status]) or $status = -1;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$status_select  = dselect($dstatus, 'status', '状态', $status, '', 1, '-1');
		$dtables = $C = $T = $S = array();
		$i = 0;
		$V = table_get_default();
		$result = $db->query("SHOW TABLES FROM `".$CFG['db_name']."`");
		while($r = $db->fetch_row($result)) {
			if(!$r[0]) continue;
			$T[$r[0]] = $r[0];
		}
		if($order < 2) {
			uksort($T, 'strnatcasecmp');
		} else if($order == 2) {
			krsort($T);
		}
		$O = array();
		$result = $db->query("SHOW TABLE STATUS FROM `".$CFG['db_name']."`");
		while($r = $db->fetch_array($result)) {
			$S[$r['Name']] = $r;
			if($order == 3 || $order == 4) {
				$O[$r['Name']] = $r['Data_length'] + $r['Index_length'];
			} else if($order == 5 || $order == 6) {
				$O[$r['Name']] = $r['Rows'];
			}
		}
		if($O) {
			if($order == 3 || $order == 5) {				
				arsort($O);
			} else if($order == 4 || $order == 6) {
				asort($O);
			}
			$T = array();
			foreach($O as $k=>$v) {
				$T[$k] = $k;
			}
		}
		$cols = array();
		foreach($T as $t) {
			$r = $S[$t];
			$r['Name'] = $t;
			if($kw) {
				if($fields == 2) {
					if(strpos($t, $DT_PRE) === false) continue;
					$tmp = '';
					$names = parse_dict($t);
					foreach($names as $kk => $vv) {
						$tmp .= ' '.$kk;
					}
					if(stripos($tmp, $kw) === false) continue;
				} else {						
					if(stripos($r['Name'], $kw) === false && stripos($r['Comment'], $kw) === false) continue;
				}
			}
			if(preg_match('/^'.$DT_PRE.'/', $t)) {
				$verify = 0;
				$F = array();
				$result = $db->query("SHOW COLUMNS FROM `$t`");
				while($rr = $db->fetch_array($result)) {
					$F[$rr['Field']] = str_replace(' unsigned', '', $rr['Type']);
				}				
				$tb = $tn = substr($t, strlen($DT_PRE));
				$tb = preg_replace("/_[0-9]{1,}/", '', $tb);
				if(is_numeric($tb) && isset($MODULE[$tb])) $tb = $MODULE[$tb]['module'].'_data';
				if(isset($V[$tb])) {
					//自定义字段移除
					if(preg_match("/[0-9]{1,}/", $tn) && is_file(DT_CACHE.'/fields-'.$tn.'.php')) {
						foreach(cache_read('fields-'.$tn.'.php') as $f) {
							if(isset($F[$f['name']])) unset($F[$f['name']]);
						}
					}
					if(count($V[$tb]) == count($F)) {
						$verify = 2;
						foreach($F as $k=>$v) {
							if(!isset($V[$tb][$k])) {$verify = 1; break;}
							$v = str_replace(array('longtext', 'mediumtext'), array('text', 'text'), strtolower($v));
							if(stripos($V[$tb][$k], $v) === false) {$verify = 1; break;}
						}
					} else {
						$verify = 1;
					}
				}
				if($status > -1 && $verify != $status) continue;
				$dtables[$i]['verify'] = $verify;
				$dtables[$i]['cols'] = $cols[$i] = count($F);
				$dtables[$i]['name'] = $r['Name'];
				$dtables[$i]['rows'] = $r['Rows'];
				$dtables[$i]['size'] = round($r['Data_length']/1024/1024, 2);
				$dtables[$i]['index'] = round($r['Index_length']/1024/1024, 2);
				$dtables[$i]['tsize'] = round(($r['Data_length']+$r['Index_length'])/1024/1024, 2);
				$dtables[$i]['auto'] = $r['Auto_increment'];
				$dtables[$i]['updatetime'] = $r['Update_time'];
				$dtables[$i]['note'] = $r['Comment'];
				$dtables[$i]['chip'] = $r['Data_free'];
				$C[str_replace($DT_PRE, '', $r['Name'])] = $r['Comment'];
				$i++;
			}
		}
		if($order == 7) {
			array_multisort($cols, SORT_DESC, $dtables);
		} else if($order == 8) {
			array_multisort($cols, SORT_ASC, $dtables);
		}
		include tpl('database_verify');
	break;
	case 'structure':
		if(!isset($tables) || !is_array($tables)) msg('请选择需要导出的表');
		$dumpcharset = $sqlcharset ? $sqlcharset : $CFG['db_charset'];
		if($db->version() > '4.1') {
			if($sqlcharset) $db->query("SET NAMES '".$sqlcharset."';\n\n");
			if($sqlcompat == 'MYSQL40')	{
				$db->query("SET SQL_MODE='MYSQL40'");
			} else if($sqlcompat == 'MYSQL41') {
				$db->query("SET SQL_MODE=''");
			}
		}
		$sqldump = "# DESTOON V".DT_VERSION." R".DT_RELEASE." https://www.destoon.com\n# ".timetodate($DT_TIME, 6)."\n# --------------------------------------------------------\n\n\n";
		foreach($tables as $table) {
			$table = strip_sql($table, 0);
			$sqldump .= sql_dumptable($table, 0, 0, 1);
		}
		$sqldump = preg_replace("/AUTO_INCREMENT\=([0-9]+)\s/", "", $sqldump);
		$name = count($tables) == 1 ? $table : 'destoon_'.$action;
		file_down('', $name.'.sql', $sqldump);
	break;
	case 'import':
		if(isset($import)) {
			if(isset($filename) && $filename && (file_ext($filename) == 'sql' ||  substr($filename, -8) == '.sql.php')) {
				$dfile = $D.$filename;
				if(!is_file($dfile)) msg('文件不存在，请检查');
				if(substr($filename, -8) == '.sql.php') {
					@include $dfile;
					file_del($dfile);
				} else {
					$sql = file_get($dfile);
					sql_execute($sql);
				}
				msg($filename.' 导入成功', '?file='.$file.'&action=import');
			} else {
				$fileid = isset($fileid) ? $fileid : 1;
				$tid = isset($tid) ? intval($tid) : 0;
				$filename = is_dir($D.$filepre) ? $filepre.'/'.$fileid : $filepre.$fileid;
				$filename = $D.$filename.'.sql';
				if(is_file($filename)) {
					$sql = file_get($filename);
					if(substr($sql, 0, 11) == '# DESTOON V') {
						$v = substr($sql, 11, 3);
						if(DT_VERSION != $v) msg('由于数据结构存在差异，备份数据不可以跨版本导入<br/>备份版本：V'.$v.'<br/>当前系统：V'.DT_VERSION);
					}
					sql_execute($sql);
					$prog = $tid ? progress(1, $fileid, $tid) : '';
					msg('分卷 <strong>#'.$fileid++.'</strong> 导入成功 程序将自动继续...'.$prog, '?file='.$file.'&action='.$action.'&filepre='.$filepre.'&fileid='.$fileid.'&tid='.$tid.'&import=1', 0);
				} else {
					msg('数据库恢复成功', '?file='.$file.'&action=import');
				}
			}
		} else {
			$dbak = $dbaks = $dsql = $dsqls = $sql = $sqls = array();
			$sqlfiles = glob($D.'*');
			if(is_array($sqlfiles)) {
				$class = 1;
				foreach($sqlfiles as $id=>$sqlfile)	{
					$tmp = basename($sqlfile);
					if(is_dir($sqlfile)) {
						$dbak['filename'] = $tmp;
						$size = $number = 0;
						$ss = glob($D.$tmp.'/*.sql');
						foreach($ss as $s) {
							$size += filesize($s);
							$number++;
						}
						$dbak['filesize'] = round($size/(1024*1024), 2);
						$dbak['filesize'] = $dbak['filesize'] < 0.01 ? round($size/1024, 2).'K' : $dbak['filesize'].'M';
						$dbak['pre'] = $tmp;
						$dbak['number'] = $number;
						$dbak['mtime'] = str_replace('.', ':', substr($tmp,	0, 19));
						$dbak['btime'] = substr($dbak['mtime'], 0, -3);
						$dbak['note'] = file_get($D.$tmp.'/0.txt');
						$dbaks[] = $dbak;
					} else {
						if(preg_match("/([a-z0-9_]+_[0-9]{8}_[0-9a-z]{8}_)([0-9]+)\.sql/i", $tmp, $num)) {
							$size = filesize($sqlfile);
							$dsql['filename'] = $tmp;
							$dsql['filesize'] = round($size/(1024*1024), 2);
							$dsql['note'] = $dsql['filesize'] < 3 ? trim(cutstr(file_get($sqlfile), '#', "\n")) : '';
							$dsql['filesize'] = $dsql['filesize'] < 0.01 ? round($size/1024, 2).'K' : $dsql['filesize'].'M';
							$dsql['pre'] = $num[1];
							$dsql['number'] = $num[2];
							$dsql['mtime'] = timetodate(filemtime($sqlfile), 5);	
							if(preg_match("/[a-z0-9_]+_([0-9]{4})([0-9]{2})([0-9]{2})_([0-9]{2})([0-9]{2})([0-9a-z]{4})_/i", $num[1], $tm)) {
								$dsql['btime'] = $tm[1].'-'.$tm[2].'-'.$tm[3].' '.$tm[4].':'.$tm[5];
							} else {
								$dsql['btime'] = $dsql['mtime'];
							}
							if($dsql['number'] == 1) $class = $class  ? 0 : 1;
							$dsql['class'] = $class;
							$dsqls[] = $dsql;
						} else {
							if(file_ext($tmp) != 'sql' && substr($tmp, -8) != '.sql.php') continue;
							$size = filesize($sqlfile);
							$sql['filename'] = $tmp;
							$sql['filesize'] = round($size/(1024*1024), 2);
							$sql['note'] = $sql['filesize'] < 3 ? trim(cutstr(file_get($sqlfile), '#', "\n")) : '';
							$sql['filesize'] = $sql['filesize'] < 0.01 ? round($size/1024, 2).'K' : $sql['filesize'].'M';
							$sql['mtime'] = timetodate(filemtime($sqlfile), 5);
							$sqls[] = $sql;
						}
					}
				}
			}
		}
		if($dbaks) $dbaks = array_reverse($dbaks);
		include tpl('database_import');
	break;
	default:
		if(isset($backup)) {
			$fileid = isset($fileid) ? intval($fileid) : 1;
			$sizelimit = $sizelimit ? intval($sizelimit) : 2048;
			if($fileid == 1 && $tables) {
				if(!isset($tables) || !is_array($tables)) msg('请选择需要备份的表');
				$random = timetodate($DT_TIME, 'Y-m-d H.i.s').' '.random(10, 'a-z');
				$tsize = 0;
				foreach($tables as $k=>$v) {
					$v = strip_sql($v, 0);
					$tables[$k] = $v;
					$tsize += $sizes[$v];
				}
				$tid = ceil($tsize*1024/$sizelimit);
				if($note) {
					$note = trim(dhtmlspecialchars(strip_tags($note)));
					file_put($D.$random.'/0.txt', $note);
				}
				cache_write($_username.'_backup.php', $tables);
			} else {
				if(!$tables = cache_read($_username.'_backup.php')) msg('请选择需要备份的表');
			}
			$dumpcharset = $sqlcharset ? $sqlcharset : $CFG['db_charset'];
			if($db->version() > '4.1') {
				if($sqlcharset) $db->query("SET NAMES '".$sqlcharset."';\n\n");
				if($sqlcompat == 'MYSQL40')	{
					$db->query("SET SQL_MODE='MYSQL40'");
				} else if($sqlcompat == 'MYSQL41') {
					$db->query("SET SQL_MODE=''");
				}
			}
			$sqldump = '';
			$tableid = isset($tableid) ? $tableid - 1 : 0;
			$startfrom = isset($startfrom) ? intval($startfrom) : 0;
			$tablenumber = count($tables);
			for($i = $tableid; $i < $tablenumber && strlen($sqldump) < $sizelimit * 1000; $i++) {
				$sqldump .= sql_dumptable($tables[$i], $startfrom, strlen($sqldump));
				$startfrom = 0;
			}
			if(trim($sqldump)) {
				$sqldump = "# DESTOON V".DT_VERSION." R".DT_RELEASE." https://www.destoon.com\n# ".timetodate($DT_TIME, 6)."\n# --------------------------------------------------------\n\n\n".$sqldump;
				$tableid = $i;
				$filename = $random.'/'.$fileid.'.sql';
				file_put($D.$filename, $sqldump);
				$fid = $fileid;
				msg('分卷 <strong>#'.$fileid++.'</strong> 备份成功.. 程序将自动继续...'.progress(0, $fid, $tid), '?file='.$file.'&sizelimit='.$sizelimit.'&sqlcompat='.$sqlcompat.'&sqlcharset='.$sqlcharset.'&tableid='.$tableid.'&fileid='.$fileid.'&fileid='.$fileid.'&tid='.$tid.'&startfrom='.$startrow.'&random='.$random.'&backup=1', 0);
			} else {
			   cache_delete($_username.'_backup.php');
			   $db->query("DELETE FROM {$DT_PRE}setting WHERE item='destoon' AND item_key='backtime'");
			   $db->query("INSERT INTO {$DT_PRE}setting (item,item_key,item_value) VALUES('destoon','backtime','$DT_TIME')");
			   msg('数据库备份成功', '?file='.$file.'&action=import');
			}
		} else {
			$sfields = array('按条件', '表名', '字段');
			$sorder  = array('结果排序方式', '表名降序', '表名升序', '大小降序', '大小升序', '记录降序', '记录升序');
			isset($fields) && isset($sfields[$fields]) or $fields = 0;
			isset($order) && isset($sorder[$order]) or $order = 0;
			$fields_select = dselect($sfields, 'fields', '', $fields);
			$order_select  = dselect($sorder, 'order', '', $order);
			$dtables = $tables = $C = $T = $S = array();
			$i = $j = $dtotalsize = $totalsize = 0;
			$result = $db->query("SHOW TABLES FROM `".$CFG['db_name']."`");
			while($r = $db->fetch_row($result)) {
				if(!$r[0]) continue;
				$T[$r[0]] = $r[0];
			}
			if($order < 2) {
				uksort($T, 'strnatcasecmp');
			} else if($order == 2) {
				krsort($T);
			}
			$O = array();
			$result = $db->query("SHOW TABLE STATUS FROM `".$CFG['db_name']."`");
			while($r = $db->fetch_array($result)) {
				$S[$r['Name']] = $r;
				if($order == 3 || $order == 4) {
					$O[$r['Name']] = $r['Data_length'] + $r['Index_length'];
				} else if($order == 5 || $order == 6) {
					$O[$r['Name']] = $r['Rows'];
				}
			}
			if($O) {
				if($order == 3 || $order == 5) {				
					arsort($O);
				} else if($order == 4 || $order == 6) {
					asort($O);
				}
				$T = array();
				foreach($O as $k=>$v) {
					$T[$k] = $k;
				}
			}
			foreach($T as $t) {
				$r = $S[$t];
				$r['Name'] = $t;
				if($kw) {
					if($fields == 2) {
						if(strpos($t, $DT_PRE) === false) continue;
						$tmp = '';
						$names = parse_dict($t);
						foreach($names as $kk => $vv) {
							$tmp .= ' '.$kk;
						}
						if(stripos($tmp, $kw) === false) continue;
					} else {						
						if(stripos($r['Name'], $kw) === false && stripos($r['Comment'], $kw) === false) continue;
					}
				}
				if(strpos($r['Comment'], 'is marked as crashed and should be repaired') !== false) {
					$r['Comment'] = '';
					$db->query("REPAIR TABLE `{$t}`");
				}
				if(preg_match('/^'.$DT_PRE.'/', $t)) {
					$dtables[$i]['name'] = $r['Name'];
					$dtables[$i]['rows'] = $r['Rows'];
					$dtables[$i]['size'] = round($r['Data_length']/1024/1024, 2);
					$dtables[$i]['index'] = round($r['Index_length']/1024/1024, 2);
					$dtables[$i]['tsize'] = round(($r['Data_length']+$r['Index_length'])/1024/1024, 2);
					$dtables[$i]['auto'] = $r['Auto_increment'];
					$dtables[$i]['updatetime'] = $r['Update_time'];
					$dtables[$i]['note'] = $r['Comment'];
					$dtables[$i]['chip'] = $r['Data_free'];
					$dtotalsize += $r['Data_length']+$r['Index_length'];
					$C[str_replace($DT_PRE, '', $r['Name'])] = $r['Comment'];
					$i++;
				} else {
					$tables[$j]['name'] = $r['Name'];
					$tables[$j]['rows'] = $r['Rows'];
					$tables[$j]['size'] = round($r['Data_length']/1024/1024, 2);
					$tables[$j]['index'] = round($r['Index_length']/1024/1024, 2);
					$tables[$j]['tsize'] = round(($r['Data_length']+$r['Index_length'])/1024/1024, 2);
					$tables[$j]['auto'] = $r['Auto_increment'];
					$tables[$j]['updatetime'] = $r['Update_time'];
					$tables[$j]['note'] = $r['Comment'];
					$tables[$j]['chip'] = $r['Data_free'];
					$totalsize += $r['Data_length']+$r['Index_length'];
					$j++;
				}
			}
			//cache_write('comment.php', $C);
			$dtotalsize = round($dtotalsize/1024/1024, 2);
			$totalsize = round($totalsize/1024/1024, 2);
			include tpl('database');
		}
	break;
}
?>