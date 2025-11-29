<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
isset($releases) or $releases = '';
$release = isset($release) ? intval($release) : 0;
$release or msg();
$release_dir = DT_ROOT.'/file/update/'.$release;
switch($action) {
	case 'ignore':
		cache_write('ignore.php', array('time' => timetodate(DT_TIME, 6), 'release' => $release));
		exit('ok');
	break;
	case 'new':
		cache_delete('ignore.php');
		exit('ok');
	break;
	case 'download':
		$url = base64_decode('aHR0cDovL3d3dy5kZXN0b29uLmNvbS91cGRhdGUucGhwP3Byb2R1Y3Q9YjJiJnJlbGVhc2U9').$release.'&version='.DT_VERSION.'&charset='.DT_CHARSET.'&lang='.DT_LANG.'&domain='.(DT_DOMAIN ? DT_DOMAIN : DT_PATH);
		$code = dcurl($url);
		if($code) {
			if(substr($code, 0, 8) == 'StatusOk') {
				$code = substr($code, 8);
			} else {
				msg($code);
			}
		} else {
			msg('无法连接官方服务器，请重试或手动更新');
		}
		dir_create($release_dir);
		if(@copy($code, $release_dir.'/'.$release.'.zip')) {
			file_copy(DT_ROOT.'/file/index.html', $release_dir.'/index.html');
			dir_create($release_dir.'/source/');
			dir_create($release_dir.'/backup/');
			msg('更新下载成功，开始解压缩..', '?file='.$file.'&action=unzip&release='.$release.'&releases='.$releases);
		} else {
			msg('更新下载失败，请重试..');
		}
	break;
	case 'unzip':
		require DT_ROOT.'/module/destoon/admin/unzip.class.php';
		$zip = new unzip;
		$zip->extract_zip($release_dir.'/'.$release.'.zip', $release_dir.'/source/');
		if(is_file($release_dir.'/source/destoon/version.inc.php')) {			
			msg('更新解压缩成功，开始更新文件..', '?file='.$file.'&action=copy&release='.$release.'&releases='.$releases);
		} else {
			msg('更新解压缩失败，请重试..');
		}
	break;
	case 'copy':
		if($CFG['template'] != 'default' && is_dir($release_dir.'/source/destoon/template/default')) @rename($release_dir.'/source/destoon/template/default', $release_dir.'/source/destoon/template/'.$CFG['template']);
		if($CFG['skin'] != 'default' && is_dir($release_dir.'/source/destoon/skin/default')) @rename($release_dir.'/source/destoon/skin/default', $release_dir.'/source/destoon/skin/'.$CFG['skin']);
		$files = file_list($release_dir.'/source/destoon');
		foreach($files as $v) {
			$file_a = str_replace('file/update/'.$release.'/source/destoon/', '', $v);
			$file_b = str_replace('source/destoon/', 'backup/', $v);
			if(is_file($file_a)) file_copy($file_a, $file_b);
		}
		foreach($files as $v) {
			$file_a = str_replace('file/update/'.$release.'/source/destoon/', '', $v);
			file_copy($v, $file_a) or msg('因文件权限不可写，系统无法覆盖'.str_replace(DT_ROOT.'/', '', $file_a).'<br/>请通过FTP工具移动file/update/'.$release.'/source/destoon/目录内所有文件覆盖到站点根目录(Windows独立服务器可以直接复制->粘贴)<br/>Linux独立服务器执行\cp -rf '.DT_ROOT.'/file/update/'.$release.'/source/destoon/* '.DT_ROOT.'/');
		}
		msg('文件更新成功，开始运行更新..', '?file='.$file.'&action=cmd&release='.$release.'&releases='.$releases);
	break;
	case 'cmd':
		@include $release_dir.'/source/cmd.inc.php';
		$update = DT_CACHE.'/update-'.$release.'.php';
		if(is_file($update)) {
			include $update;
			file_del($update);
		}
		if($releases) {
			$releases = str_replace($release.',', '', $releases);
			$releases = str_replace($release, '', $releases);
			$next = intval(cutstr($releases, '', ','));
			if($next > $release) msg($release.'更新运行成功，继续下个更新..', '?file='.$file.'&release='.$next.'&releases='.$releases);
		}
		msg('更新运行成功', '?file='.$file.'&action=finish&release='.$release);
	break;
	case 'backup':
		$release > DT_RELEASE or msg('当前版本不需要备份', '?file=cloud&action=update');
		$data = dcloud('patch->pid='.$release);
		if(strpos($data, 'version.inc.php') !== false) {
			foreach(explode("\n", $data) as $fn) {
				$fn = trim($fn);
				if($fn) {
					if(is_file(DT_ROOT.'/'.$fn)) file_copy(DT_ROOT.'/'.$fn, $release_dir.'/backup/'.$fn);
					if($CFG['template'] != 'default' && strpos($fn, 'template/default') !== false) {
						$ft = str_replace('template/default', 'template/'.$CFG['template'], $fn);
						if(is_file(DT_ROOT.'/'.$ft)) file_copy(DT_ROOT.'/'.$ft, $release_dir.'/backup/'.$ft);
					}
					if($CFG['skin'] != 'default' && strpos($fn, 'skin/default') !== false) {
						$fs = str_replace('skin/default', 'skin/'.$CFG['skin'], $fn);
						if(is_file(DT_ROOT.'/'.$fs)) file_copy(DT_ROOT.'/'.$fs, $release_dir.'/backup/'.$fs);
					}
				}
			}
		} else {
			msg($data ? $data : '无法连接官方云服务');
		}
		if($releases) {
			$releases = str_replace($release.',', '', $releases);
			$releases = str_replace($release, '', $releases);
			$next = intval(cutstr($releases, '', ','));
			if($next > $release) msg($release.'备份成功，继续下个更新..', '?file='.$file.'&action=backup&release='.$next.'&releases='.$releases);
		}
		msg('更新文件备份成功，存于file/update', '?file=cloud&action=update');
	break;
	case 'finish':
		msg('系统更新成功 当前版本V'.DT_VERSION.' R'.DT_RELEASE, '?file=cloud&action=update', 3);
	break;
	case 'undo':
		is_file($release_dir.'/backup/version.inc.php') or msg('此版本备份文件不存在，无法还原');
		@include $release_dir.'/source/cmd.inc.php';
		$files = file_list($release_dir.'/backup');
		foreach($files as $v) {
			file_copy($v, str_replace('file/update/'.$release.'/backup/', '', $v));
		}
		msg('系统还原成功', '?file=cloud&action=update');
	break;
	default:
		$release > DT_RELEASE or msg('当前版本不需要运行此更新', '?file=cloud&action=update');
		msg('在线更新已经启动，开始下载更新..', '?file='.$file.'&action=download&release='.$release.'&releases='.$releases);
	break;
}
?>