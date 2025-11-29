<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
if($file == 'file') {
	$menus = array (
		array('文件备份', '?file=patch'),
		array('文件管理', '?file=file'),
		array('文件校验', '?file=md5'),
		array('木马扫描', '?file=scan'),
	);
}
if(!isset($CFG['editfile']) || !$CFG['editfile']) msg('系统禁止了在线管理文件，请修改根目录config.inc.php<br/>$CFG[\'editfile\'] = \'0\'; 修改为 $CFG[\'editfile\'] = \'1\';');
if(strlen($CFG['editfile']) >= 6) {
	if(get_cookie($secretkey.'f') != md5($CFG['editfile'].DT_KEY)) $action = 'login';
} else {
	if($CFG['editfile'] < 2 && !in_array($action, array('play', 'index', ''))) msg('当前为浏览模式，如需在线管理文件，请修改根目录config.inc.php<br/>$CFG[\'editfile\'] = \'1\'; 修改为 $CFG[\'editfile\'] = \'2\';');
}

function dir_fix($dir, $file) {
	if(!$dir) return '';
	$dir = str_replace("\\", '/', $dir);
	if(strpos($dir, '//') !== false || strpos($dir, '..') !== false || strpos($dir, './') !== false) return '';
	if(substr($dir, 0, 1) == '/') $dir = substr($dir, 1);
	if(substr($dir, -1) != '/') $dir = $dir.'/';
	if($file == 'skin') return is_dir(DT_ROOT.'/static/'.$file.'/'.$dir) ? $dir : '';
	if($file == 'template') return is_dir(DT_ROOT.'/'.$file.'/'.$dir) ? $dir : '';
	return is_dir(DT_ROOT.'/'.$dir) ? $dir : '';
}
function dir_nav($dir, $file) {
	$nav = $pre = '';
	if($dir) {
		foreach(explode('/', $dir) as $v) {
			if($v) {
				$pre .= $v.'/';
				$nav .= '<a href="?file='.$file.'&dir='.$pre.'">'.$v.'</a> / ';
			}
		}
	}
	return $nav;
}
function dir_check($name) {
	if(strpos($name, '..') !== false) return false;
	if(substr($name, 0, 1) == '.') return false;
	if(substr($name, -1, 1) == '.') return false;
	return preg_match("/^[a-z0-9_\-\\.\(\)\[\]]{1,}$/i", $name) ? 1 : 0;
}
function file_backup($filepath, $version = 0) {
	$fileext = file_ext($filepath);
	if($version) {
		if(DT_TIME - filemtime($filepath) < 3600) return true;
		return is_file(substr($filepath, 0, -strlen($fileext)-1).'('.$version.').'.$fileext);
	}
	$backpath = file_rename($filepath);
	return file_copy($filepath, $backpath);
}
function file_rename($filepath) {
	if(!is_file($filepath)) return $filepath;
	$fileext = file_ext($filepath);
	$i = 0;
	while(++$i) {
		$newpath = substr($filepath, 0, -strlen($fileext)-1).'('.$i.').'.$fileext;
		if(!is_file($newpath)) return $newpath;
	}
	return $filepath;
}
$names = array();
$dir = isset($dir) ? dir_fix($dir, $file) : '';
switch($file) {
	case 'skin':
		$exts_show = array('css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'bmp');
		$exts_edit = array('css', 'js');
		$exts_upload = array('css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'bmp');
		$root = DT_ROOT.'/static/'.$file;
		$path = DT_PATH.'/static/'.$file.'/';
		$namepath = $root.'/'.($dir ? $dir : '').'these.name.php';
		if(is_file($namepath)) include $namepath;
		$menuid = 1;
	break;
	case 'template':
		$exts_show = array('htm');
		$exts_edit = array('htm');
		$exts_upload = array('htm');
		$root = DT_ROOT.'/'.$file;
		$path = DT_PATH.$file.'/';
		$namepath = $root.'/'.($dir ? $dir : '').'these.name.php';
		if(is_file($namepath)) include $namepath;
		$menuid = 0;
	break;
	default:
		$exts_show = array();
		$exts_edit = array('php', 'htm', 'html', 'css', 'js', 'txt', 'xml', 'sql');
		$exts_upload = explode('|', $DT['uploadtype']);
		if(!in_array('htm', $exts_upload)) $exts_upload[] = 'htm';
		if(!in_array('css', $exts_upload)) $exts_upload[] = 'css';
		if(!in_array('js', $exts_upload)) $exts_upload[] = 'js';
		$root = DT_ROOT;
		$path = DT_PATH;
		$namepath = '';
		$menuid = 1;
	break;
}
$file_hide = array();//隐藏
$file_list = array('config.inc.php', 'license.php', 'admin/index.inc.php', 'admin/database.inc.php', 'admin/file.inc.php', 'admin/skin.inc.php', 'admin/template.inc.php');//只列表
$file_read = array('license.txt');//只读
switch($action) {
	case 'login':
		if($submit) {
			if($pwd != $CFG['editfile']) msg('操作密码填写错误');
			set_cookie($secretkey.'f', md5($CFG['editfile'].DT_KEY));
			msg('验证成功', '?file='.$file);
		} else {
			include tpl('file_login');
		}
	break;
	case 'add':
		if($submit) {
			isset($name) or msg();
			if(!file_ext($name)) $name .= '.htm';
			dmsg('创建成功', '?file='.$file.'&action=edit&dir='.$dir.'&name='.$name);
		} else {
			include tpl('file_edit');
		}
	break;
	case 'edit':
		isset($name) or msg();
		$fileext = file_ext($name);
		in_array($fileext, $exts_edit) or msg('不支持此类型文件编辑');
		$filepath = $root.'/'.($dir ? $dir : '').$name;
		if(!is_file($filepath)) file_put($filepath, '');
		is_file($filepath) or msg('文件创建失败');
		is_write($filepath) or msg('文件不可写，请将其属性设置为可写');
		filesize($filepath) < 1024000 or msg('文件体积较大，不支持在线编辑');
		$filesite = str_replace(DT_ROOT.'/', '', $filepath);
		if(in_array($filesite, $file_list) || in_array($filesite, $file_hide)) msg('此文件不支持编辑或查看');
		if(isset($content)) {
			if(in_array($filesite, $file_read)) msg('此文件不支持修改');
			if(isset($backup)) file_backup($filepath);
			$content = stripslashes($content);
			$content = strip_sql($content, 0);
			file_put($filepath, $content);
			dmsg('保存成功', '?file='.$file.'&action='.$action.'&dir='.$dir.'&name='.$name);
		} else {
			$mode = $fileext;
			if($fileext == 'htm') $mode = 'html';
			if($fileext == 'js') $mode = 'javascript';
			if($fileext == 'txt' || $fileext == 'sql') $mode = 'text';
			$backup = file_backup($filepath, 1) ? 0 : 1;
			$content = file_get($filepath);
			if(in_array($fileext, array('php', 'htm', 'html'))) $content = dhtmlspecialchars($content);
			if(!$content && $fileext == 'htm' && $file == 'template' && strpos($name, '-') !== false) {
				$tplpath = $root.'/'.($dir ? $dir : '').cutstr($name, '', '-').'.htm';
				if(is_file($tplpath)) $content = dhtmlspecialchars(file_get($tplpath));
			}
			$ico = is_file(DT_ROOT.'/file/ext/'.$fileext.'.gif') ? $fileext : 'oth';
			include tpl('file_edit');
		}
	break;
	case 'upload':
		$_FILES or exit('{"error":1,"message":"请选择文件"}');
		require DT_ROOT.'/include/upload.class.php';
		$filepath = $root.'/'.($dir ? $dir : '').$_FILES['file']['name'];
		$filename = basename(file_rename($filepath));
		$upload = new upload($_FILES, (in_array($file, array('skin', 'template')) ? $file.'/' : '').($dir ? $dir : ''), $filename, implode('|', $exts_upload));
		$upload->adduserid = false;
		if($upload->save()) exit('{"error":0,"url":"'.$filename.'"}');
		exit('{"error":1,"message":"'.$upload->errmsg.'"}');
	break;
	case 'mkdir':
		isset($name) or msg();
		dir_check($name) or msg('文件夹名称不规范');
		$filepath = $root.'/'.($dir ? $dir : '').$name;
		if(is_dir($filepath)) msg('文件夹已存在');
		dmsg((dir_create($filepath) ? '文件夹创建成功' : '文件夹创建失败'), '?file='.$file.'&dir='.$dir);
	break;
	case 'rename':
		isset($name) or msg();
		//dir_check($name) or msg('名称不规范');
		dir_check($text) or msg('名称不规范');
		$filepath = $root.'/'.($dir ? $dir : '').$name;
		$newpath = $root.'/'.($dir ? $dir : '').$text;
		$filesite = str_replace(DT_ROOT.'/', '', $filepath);
		if(in_array($filesite, $file_list) || in_array($filesite, $file_read) || in_array($filesite, $file_hide)) msg('此文件不支持此操作');
		if(is_file($filepath)) {
			if(is_file($newpath)) msg('同名文件已存在');
			if(file_ext($filepath) != file_ext($newpath)) msg('文件后缀不允许修改');
			rename($filepath, $newpath);
		} else if(is_dir($filepath)) {
			if(is_dir($newpath)) msg('同名文件夹已存在');
			rename($filepath, $newpath);
		}		
		dmsg('重命名成功', '?file='.$file.'&dir='.$dir);
	break;
	case 'touch':
		isset($name) or msg();
		dir_check($name) or msg('名称不规范');
		is_time($text) or msg('时间不规范');
		$filepath = $root.'/'.($dir ? $dir : '').$name;
		if(is_file($filepath)) {
			touch($filepath, datetotime($text));
		} else if(is_dir($filepath)) {
			msg('文件夹不支持修改时间');
		}		
		dmsg('时间修改成功', '?file='.$file.'&dir='.$dir);
	break;
	case 'chmod':
		isset($name) or msg();
		dir_check($name) or msg('名称不规范');
		preg_match("/^[0-9]{3,4}$/", $text) or msg('属性不规范');
		$filepath = $root.'/'.($dir ? $dir : '').$name;
		if(is_file($filepath)) {
			chmod($filepath, $text);
		} else if(is_dir($filepath)) {
			chmod($filepath, $text);
		}		
		dmsg('属性修改成功', '?file='.$file.'&dir='.$dir);
	break;
	case 'delete':
		isset($name) or msg();
		dir_check($name) or msg('名称不规范');
		$filepath = $root.'/'.($dir ? $dir : '').$name;
		$filesite = str_replace(DT_ROOT.'/', '', $filepath);
		if(in_array($filesite, $file_list) || in_array($filesite, $file_read) || in_array($filesite, $file_hide)) msg('此文件不支持此操作');
		if(is_file($filepath)) {
			file_del($filepath);
		} else if(is_dir($filepath)) {
			dir_delete($filepath);
		}
		dmsg('删除成功', '?file='.$file.'&dir='.$dir);
	break;
	case 'play':
		(isset($url) && is_url($url)) or exit;
		include tpl('header');
		load('player.js');
		exit('<center><script type="text/javascript">document.write(player("'.$url.'", 480, 360, 1));</script></center></body></html>');
	break;
	case 'name':
		($names && $namepath && isset($name) && isset($note)) or exit('ko');
		if($name && $note) $names[$name] = strip_tags($note);
		$namedir = substr($namepath, 0, -14);
		foreach($names as $k => $v) {
			if(!is_dir($namedir.$k) && !is_file($namedir.$k.'.htm')) unset($names[$k]);
		}
		if($names) ksort($names);
		file_put($namepath, "<?php\n\$names = ".var_export($names, true).";\n?>");
		exit('ok');
	break;
	default:
		$sorder  = array('列表排序方式', '文件名称降序', '文件名称升序', '文件大小降序', '文件大小升序', '修改时间降序', '修改时间升序');
		$order = isset($order) ? intval($order) : 0;
		if($order > 6) $order = 0;
		$order_select  = dselect($sorder, 'order', '', $order);
		$dirs = $files = $S = $T = array();
		$i = 0;
		if(is_dir($root.'/'.($dir ? $dir : ''))) {
			foreach(glob($root.'/'.($dir ? $dir : '').'*') as $k=>$v) {
				$name = basename($v);
				if($kw && stripos($name, $kw) === false) continue;
				$filesite = str_replace(DT_ROOT.'/', '', $v);
				if(in_array($filesite, $file_hide)) continue;
				$r = array();
				if(is_dir($v)) {
					$id = $name;
					if(!check_name($id) && $DT['pcharset']) $id = convert($id, $DT['pcharset'], DT_CHARSET);
					$dirs[$i]['id'] = $id;
					$dirs[$i]['dirname'] = $id;
					$T[$i] = filemtime($v);
					$dirs[$i]['mtime'] = timetodate($T[$i], 6);
					$dirs[$i]['mod'] = substr(base_convert(fileperms($v), 10, 8), -4);
				} else {
					$fileext = file_ext($v);
					if($exts_show && !in_array($fileext, $exts_show)) continue;
					$filename = $name;
					if(!check_name($filename) && $DT['pcharset']) $filename = convert($filename, $DT['pcharset'], DT_CHARSET);
					$id = basename($filename, '.'.$fileext);
					$files[$i]['id'] = $id;
					$files[$i]['fileext'] = $fileext;
					$files[$i]['ico'] = is_file(DT_ROOT.'/file/ext/'.$fileext.'.gif') ? $fileext : 'oth';
					$files[$i]['filename'] = $id.($fileext ? '.'.$fileext : '');				
					$S[$i] = filesize($v);
					$files[$i]['filesize'] = round($S[$i]/1024, 2);
					$T[$i] = filemtime($v);
					$files[$i]['mtime'] = timetodate($T[$i], 6);
					$files[$i]['mod'] = substr(base_convert(fileperms($v), 10, 8), -4);
					if(in_array($fileext, $exts_edit)) {
						$files[$i]['url'] = 'javascript:;" onclick="_edit(\''.$filename.'\');';
					} else {
						$files[$i]['url'] = 'javascript:;" onclick="_preview(\''.$path.$dir.$filename.'\');';
					}
				}
				$i++;
			}
		}
		if($dirs) {
			$O = array();
			if($order < 2) {
				ksort($dirs);
			} else if($order == 2) {
				krsort($dirs);
			} else if($order == 3) {
				//
			} else if($order == 4) {
				//
			} else if($order == 5) {
				arsort($T);$O = $T;
			} else if($order == 6) {
				asort($T);$O = $T;
			}
			if($O) {
				$A = array();
				foreach($O as $k=>$v) {
					if(isset($dirs[$k])) $A[$k] = $dirs[$k];
				}
				$dirs = $A;
			}
		}
		if($files) {
			$O = array();
			if($order < 2) {
				ksort($files);
			} else if($order == 2) {
				krsort($files);
			} else if($order == 3) {
				arsort($S);	$O = $S;
			} else if($order == 4) {
				asort($S);$O = $S;
			} else if($order == 5) {
				arsort($T);$O = $T;
			} else if($order == 6) {
				asort($T);$O = $T;
			}
			if($O) {
				$A = array();
				foreach($O as $k=>$v) {
					if(isset($files[$k])) $A[$k] = $files[$k];
				}
				$files = $A;
			}
		}
		include tpl('file');
	break;
}
?>