<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('文件备份', '?file=patch'),
    array('文件管理', '?file=file'),
    array('文件校验', '?file=md5'),
    array('木马扫描', '?file=scan'),
);
$sys = array('admin', 'api', 'file', 'include', 'install', 'lang', 'mobile', 'module', 'skin', 'template', 'tool', 'upgrade', $MODULE[2]['moduledir']);
$ext = 'php|htm|css|js|jpg|jpeg|gif|png';
switch($action) {
	case 'view':
		isset($fid) or msg();
		preg_match("/^[0-9\.\-\s~]{33}$/", $fid) or msg();
		is_dir(DT_ROOT.'/file/patch/'.$fid) or msg();
		$lists = get_file(DT_ROOT.'/file/patch/'.$fid);
		include tpl('patch_view');
	break;
	case 'delete':
		isset($fid) or msg();
		preg_match("/^[0-9\.\-\s~]{33}$/", $fid) or msg();
		is_dir(DT_ROOT.'/file/patch/'.$fid) or msg();
		dir_delete(DT_ROOT.'/file/patch/'.$fid);
		dmsg('删除成功', '?file='.$file);
		case 'clear':
		foreach(glob(DT_ROOT.'/file/patch/*') as $d) {
			if(is_dir($d)) dir_delete($d);
		}
		dmsg('删除成功', '?file='.$file);
	break;
	case 'note':
		isset($fid) or exit('ko');
		preg_match("/^[0-9\.\-\s~]{33}$/", $fid) or exit('ko');
		is_dir(DT_ROOT.'/file/patch/'.$fid) or exit('ko');
		file_put(DT_ROOT.'/file/patch/'.$fid.'/file/temp/note.txt', strip_tags($note));
		exit('ok');
	break;
	case 'list':
		isset($pid) or $pid = '';
		$pid > DT_RELEASE or exit('ERR:只能备份当前版本之后的更新');
		if(preg_match("/^[0-9]{8}$/", $pid)) echo dcloud('patch->pid='.$pid);
		exit;
	break;
	default:
		if($submit) {
			is_time($fd) or msg('开始时间设置错误');
			$ft = datetotime($fd);
			is_time($td) or msg('结束时间设置错误');
			$tt = datetotime($td);		
			$tt >= $ft or msg('时间范围设置错误');
			isset($filedir) or $filedir = $sys;
			$fileext or $fileext = $ext;
			$lists = array();
			cache_write('prefer-patch.php', array('filedir' => $filedir, 'fileext' => $fileext, 'totime' => $td));
			if($type) {
				$files = isset($files) ? trim($files) : '';
				foreach(explode("\n", $files) as $f) {
					$f = trim($f);
					$f = str_replace("\\", '/', $f);
					if(strpos($f, '..') !== false || strpos($f, ':') !== false) continue;
					if(substr($f, 0, 1) == '/') continue;
					if(!is_file(DT_ROOT.'/'.$f) && !is_dir(DT_ROOT.'/'.$f)) continue;
					$lists[] = DT_ROOT.'/'.$f;
				}
				$ft = $tt = $DT_TIME;
			} else {
				$files = array();
				foreach(glob(DT_ROOT.'/*.*') as $f) {
					if(is_dir($f)) continue;
					if(in_array(basename($f), array('config.inc.php', 'index.html', 'sitemaps.xml'))) continue;
					$files[] = $f;
				}
				foreach($filedir as $d) {
					if($d == 'file') {
						$files = array_merge($files, get_file(DT_ROOT.'/'.$d.'/image', $fileext));
						$files = array_merge($files, get_file(DT_ROOT.'/'.$d.'/config', $fileext));
						$files = array_merge($files, get_file(DT_ROOT.'/'.$d.'/setting', $fileext));
					} else {
						$files = array_merge($files, get_file(DT_ROOT.'/'.$d, $fileext));
					}
				}
				foreach($files as $f) {
					if(in_array($f, array(DT_ROOT.'/file/script/config.js'))) continue;
					$n = basename($f);
					if(file_ext($n) == 'js') {
						if(in_array(substr($n, 0, 1), array('A', '0'))) continue;
					}
					$t = filemtime($f);
					if($t >= $ft && $t <= $tt) {
						$lists[] = $f;
					}
				}
			}
			$find = count($lists);
			if($find) {
				$dir = DT_ROOT.'/file/patch/'.timetodate($ft, 'Y-m-d H.i').'~'.timetodate($tt, 'Y-m-d H.i').'/';
				if(!is_dir($dir)) dir_delete($dir);
				if($note) {
					$note = trim(dhtmlspecialchars(strip_tags($note)));
					file_put($dir.'file/temp/note.txt', $note);
				}
				foreach($lists as $f) {
					if(is_file($f)) {
						file_copy($f, $dir.str_replace(DT_ROOT.'/', '', $f));
						@touch($dir.str_replace(DT_ROOT.'/', '', $f), filemtime($f));
					} else if(is_dir($f)) {
						dir_copy($f, $dir.str_replace(DT_ROOT.'/', '', $f));
					}
				}
				cache_write('patch.php', array($td));
				msg('备份成功 '.$find.' 个文件或目录，已保存于file/patch', '?file='.$file, 5);
			}
			msg('没有符合条件的文件');
		} else {
			$files = glob(DT_ROOT.'/*');
			$dirs = $rfiles = $baks = $ups = array();
			foreach($files as $f) {
				$bn = basename($f);
				if(is_file($f)) {
					$rfiles[] = $bn;
				} else {
					$dirs[] = $bn;
				}
			}
			$fd = substr(DT_RELEASE, 0, 4).'-'.substr(DT_RELEASE, 4, 2).'-'.substr(DT_RELEASE, 6, 2).' 00:00:00';
			$td = timetodate($DT_TIME, 6);
			$ds = $dirs;
			$pf = cache_read('prefer-patch.php');
			if($pf) {
				if($pf['filedir']) $ds = $pf['filedir'];
				if($pf['fileext']) $ext = $pf['fileext'];
				if(is_time($pf['totime'])) $fd = $pf['totime'];
			} else {
				foreach($ds as $k=>$v) {
					if(in_array($v, array('install', 'upgrade', 'file', '9.0', '8.0', '7.0'))) $ds[$k] = '';
					if(is_file(DT_ROOT.'/'.$v.'/LICENSE')) $ds[$k] = '';
					if(is_file(DT_ROOT.'/'.$v.'/license.txt')) $ds[$k] = '';
				}
			}
			$files = glob(DT_ROOT.'/file/patch/*');
			foreach($files as $f) {
				if(is_dir($f)) {
					$n = basename($f);
					if(preg_match("/^[0-9\.\-\s~]{33}$/", $n)) {
						$r = array();
						$r['file'] = $n;
						$r['num'] = count(get_file($f));
						$r['time'] = timetodate(filemtime($f), 5);
						$r['note'] = file_get($f.'/file/temp/note.txt');
						$baks[] = $r;
					}
				}
			}
			$date1 = timetodate($DT_TIME, 3);
			$date2 = timetodate($DT_TIME - 86400, 3);
			$date3 = timetodate($DT_TIME - 86400*2, 3);
			$date4 = timetodate($DT_TIME - 86400*(intval(timetodate($DT_TIME, 'N'))-1), 3);
			$date5 = timetodate($DT_TIME, 'Y-m').'-01';
			$release = isset($release) ? intval($release) : 0;
		}
		include tpl('patch');
	break;
}
?>