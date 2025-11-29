<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('系统体检', '?file='.$file),
    array('系统日志', '?file='.$file.'&action=log'),
    array('服务器状态', '?file='.$file.'&action=server'),
    array('MySQL进程', 'javascript:Dwidget(\'?file=database&action=process\', \'MySQL进程\');'),
    array('PHP信息', '?file='.$file.'&action=phpinfo', ' target="_blank"'),
);
if(!is_file(DT_ROOT.'/file/log/'.timetodate(DT_TIME, 'Ym/d').'/server.php')) unset($menus[2]);
if($CFG['cache'] == 'memcache' || $CFG['session'] == 'memcache') $menus[] = array('Memcache', '?file='.$file.'&action=memcache', ' target="_blank"');
switch($action) {
	case 'phpinfo':
		exit(phpinfo());
	break;
	case 'memcache':
		dheader(DT_PATH.'api/memcache.php');
	break;
	case 'view':
		$filepath = decrypt($auth);
		strpos($filepath, DT_ROOT.'/file/') !== false or msg('参数错误');
		is_file($filepath) or msg('日志不存在');
		strpos($filepath, './') === false or msg('参数错误');
		if($job == 'down') file_down($filepath);
		$fileext = file_ext($filepath);
		$mode = $fileext;
		if($fileext == 'htm') $mode = 'html';
		if($fileext == 'js') $mode = 'javascript';
		if($fileext == 'txt' || $fileext == 'sql') $mode = 'text';
		$ico = file_ico($filepath);
		$content = file_get($filepath);
		$content = trim(str_replace('<?php exit;?>', '', $content));
		include tpl('doctor_view');
	break;
	case 'log':
		$sorder  = array('列表排序方式', '文件名称降序', '文件名称升序', '文件大小降序', '文件大小升序', '修改时间降序', '修改时间升序');
		$order = isset($order) ? intval($order) : 0;
		if($order > 6) $order = 0;
		$order_select  = dselect($sorder, 'order', '', $order);
		$root = DT_ROOT.'/file/log';
		$jobs = $days = array();
		foreach(glob($root.'/*') as $v) {
			$n = basename($v);
			if(is_file($v)) continue;
			$jobs[$n] = $n;
		}
		krsort($jobs);
		isset($jobs[$job]) or $job = timetodate(DT_TIME, 'Ym');
		(isset($day) && preg_match("/^[0-9]{2}$/", $day)) or $day = timetodate(DT_TIME, 'd');
		(isset($date) && is_date($date) && strpos($date, '-') !== false) or $date = timetodate(DT_TIME, 3);
		$order = isset($order) ? intval($order) : 0;
		if(preg_match("/^[0-9]{6}$/", $job)) {
			foreach(glob($root.'/'.$job.'/*') as $v) {
				$n = basename($v);
				if(is_file($v)) continue;
				$days[$n] = $n;
			}
			if($days) {
				krsort($days);
				if(!isset($days[$day])) $day = current($days);
			} else {
				$days[$day] = $day;
			}
			$year = substr($job, 0, 4);
			$month = substr($job, 5, 2);
			$dir = $root.'/'.$job.'/'.$day.'/';
		} else {
			list($year, $month, $day) = explode('-', $date);
			$dir = $root.'/'.$job.'/';
		}
		$dirs = glob($dir.'*');
		$files = glob($dir.'*.*');
		if($dirs) {
			foreach(array($year.$month.'/'.$day.'/', $year.$month.'/', $day.'/') as $v) {
				if(is_dir($dir.$v)) {
					$files += glob($dir.$v.'*.*');
				}
			}
		}
		$lists = $S = $T = array();
		if($files) {
			$i = 0;
			foreach($files as $v) {
				$r = array();
				if($kw && stripos($v, $kw) === false) continue;
				$r['file'] = $v;
				$r['auth'] = encrypt($v);
				$r['time'] = timetodate(filemtime($v), 6);
				$n = basename($v);
				if($n == 'index.html') continue;
				$r['name'] = $n;
				$r['ico'] = file_ico($n);
				$T[$i] = filemtime($v);
				$S[$i] = filesize($v);
				$r['size'] = round($S[$i]/1024, 2);
				$lists[$i] = $r;
				$i++;
			}
			$files = $lists;
			$O = array();
			if($order == 1) {
				ksort($files);
			} else if($order == 3) {
				arsort($S);	$O = $S;
			} else if($order == 4) {
				asort($S);$O = $S;
			} else if($order == 5) {
				arsort($T);$O = $T;
			} else if($order == 6) {
				asort($T);$O = $T;
			} else {
				krsort($files);
			}
			if($O) {
				$A = array();
				foreach($O as $k=>$v) {
					if(isset($files[$k])) $A[$k] = $files[$k];
				}
				$files = $A;
			}
		}
		$menuid = 1;
		include tpl('doctor_log');
	break;
	case 'server':
		(isset($date) && is_date($date) && strpos($date, '-') !== false) or $date = timetodate(DT_TIME, 3);
		list($year, $month, $day) = explode('-', $date);
		$filepath = DT_ROOT.'/file/log/'.$year.$month.'/'.$day.'/server.php';
		$lists = $tags = array();
		if(is_file($filepath)) {
			$data = trim(file_get($filepath));
			$time = intval(cutstr($data, "<?php exit;?>", "\n"));
			$TIME = array(
				5  => array('00:00', '05:00', '10:00', '15:00', '20:00', '25:00', '30:00', '35:00', '40:00', '45:00', '50:00', '55:00'),
				10 => array('00:00', '10:00', '20:00', '30:00', '40:00', '50:00'),
				15 => array('00:00', '15:00', '30:00', '45:00'),
				20 => array('00:00', '20:00', '40:00'),
				30 => array('00:00', '30:00'),
				60 => array('00:00'),
			);
			isset($TIME[$time]) or $time = 15;
			$arr = $TIME[$time];
			$max = timetodate(DT_TIME, 'YmdHis');
			for($i = 0; $i < 24; $i++) {
				foreach($arr as $v) {
					$time = $date.' '.($i < 10 ? '0'.$i : $i).':'.$v;
					$key  = str_replace(array('-', ' ', ':'), array('', '', ''), $time);
					$lists[$key]['time'] = $time;
					$lists[$key]['code'] = $key > $max ? '' : '-';
				}
			}
			foreach(explode("\n", $data) as $v) {
				if(strpos($v, "\t") === false) continue;
				$t = explode("\t", trim($v));
				if($t[0] && $t[1]) {
					$key = str_replace(array('-', ' ', ':'), array('', '', ''), $t[0]);
					$key = substr($key, 0, -2).'00';//忽略秒
					if($key && is_time($t[0])) {
						if(isset($lists[$key])) {
							$lists[$key]['code'] = trim($t[1]);
						} else {
							$tags[$key]['time'] = trim($t[0]);
							$tags[$key]['code'] = trim($t[1]);
						}
					}
				}
			}
			if($tags) $lists = $tags + $lists;
		}
		$menuid = 2;
		include tpl('doctor_server');
	break;
	default:
		foreach(glob(DT_CACHE.'/update-*.php') as $v) {
			include $v;
			file_del($v);
		}
		include tpl('doctor');
	break;
}
?>