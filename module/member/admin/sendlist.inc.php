<?php
defined('DT_ADMIN') or exit('Access Denied');
function _safecheck($content) {
	if(strpos($content, '{$user[') === false) return false;
	$str = str_replace('{$user[', '', $content);
	foreach(array('$', '(', '{', '[') as $v) {
		if(strpos($str, $v) !== false) return false;
	}
	return true;

}
switch($action) {
	case 'download':
		$file_ext = file_ext($filename);
		if($file_ext != 'txt') msg('只能下载TXT文件');
		if(strpos($filename, '..') !== false) msg('文件名格式错误');
		file_down(DT_ROOT.'/file/'.$path.'/'.$filename);
	break;
	case 'note':
		$file_ext = file_ext($filename);
		if($file_ext != 'txt') exit('ko');;
		if(strpos($filename, '..') !== false) exit('ko');
		$content = file_get(DT_ROOT.'/file/'.$path.'/'.$filename);
		if(substr($content, 0, 1) == '#') {
			$content = ($note ? '#'.strip_tags($note)."\r\n" : '').cutstr($content, "\n", '');
		} else {
			$content = ($note ? '#'.strip_tags($note)."\r\n" : '').$content;
		}
		file_put(DT_ROOT.'/file/'.$path.'/'.$filename, $content);
		exit('ok');
	break;
	case 'unlink':
		 if(is_array($filenames)) {
			 foreach($filenames as $filename) {
				 if(file_ext($filename) == 'txt' && strpos($filename, '..') === false) file_del(DT_ROOT.'/file/'.$path.'/'.$filename);
			 }
		 } else {
			 if(file_ext($filenames) == 'txt' && strpos($filenames, '..') === false) file_del(DT_ROOT.'/file/'.$path.'/'.$filenames);
		 }
		 dmsg('删除成功', $forward);
	break;
	case 'make':
		if(isset($make)) {
			if(isset($first)) {
				$tb or $tb = $DT_PRE.'member';
				$tb = strip_sql($tb, 0);
				$sql or $sql = 'groupid>4';
				$sql = strip_sql($sql, 0);
				$num or $num = 1000;
				$name = timetodate($DT_TIME, 'YmdHi').'-'.strtolower(random(6));
				$item = array();
				$item['tb'] = $tb;
				$item['num'] = $num;
				$item['sql'] = $sql;
				$item['note'] = dhtmlspecialchars($note);
				$item['name'] = $name;
				cache_write('temp-'.$file.'-'.$action.'-'.$_username.'.php', $item);
			} else {
				$item = cache_read('temp-'.$file.'-'.$action.'-'.$_username.'.php');
				$item or msg();
				extract($item);
			}
			$pagesize = $num;
			$offset = ($page-1)*$pagesize;
			$result = $db->query("SELECT {$key} FROM {$tb} WHERE {$sql} ORDER BY userid DESC LIMIT {$offset},{$pagesize}");
			$data = '';
			while($r = $db->fetch_array($result)) {
				if($key == 'email' && is_email($r[$key])) $data .= $r[$key]."\r\n";
				if($key == 'mobile' && is_mobile($r[$key])) $data .= $r[$key]."\r\n";
				if($key == 'username' && check_name($r[$key])) $data .= $r[$key]."\r\n";
			}
			if($data) {
				$filename = $name.'-'.$page.'.txt';
				file_put(DT_ROOT.'/file/'.$path.'/'.$filename, ($item['note'] ? "#".$item['note']."\r\n" : '').trim($data));
				msg('文件'.$filename.'获取成功<br/>请稍候，程序将自动继续...', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&page='.($page+1).'&make=1');
			} else {
				cache_delete('temp-'.$file.'-'.$action.'-'.$_username.'.php');
				msg('列表获取成功', '?moduleid='.$moduleid.'&file='.$file.'&action=list');
			}
		} else {
			include tpl('sendlist_make', $module);
			exit;
		}
	break;
	case 'view':
		$file_ext = file_ext($filename);
		if($file_ext != 'txt') msg('只能预览TXT文件');
		if(strpos($filename, '..') !== false) msg('文件名格式错误');
		$content = file_get(DT_ROOT.'/file/'.$path.'/'.$filename);
		include tpl('sendlist_view', $module);
		exit;
	break;
	case 'list':
		$files = glob(DT_ROOT.'/file/'.$path.'/*.txt');
		$lists = array();
		if(is_array($files)) {
			$files = array_reverse($files);
			foreach($files as $v) {
				$r = array();
				$c = file_get($v);
				$r['filename'] = basename($v);
				$r['filesize'] = round(filesize($v)/(1024), 2);
				$r['mtime'] = timetodate(filemtime($v), 5);
				$r['note'] = substr($c, 0, 1) == '#' ? cutstr($c, '#', "\r\n") : '';
				$r['count'] = substr_count($c, "\n") + ($r['note'] ? 0 : 1);
				$lists[] = $r;
			}
		}
		include tpl('sendlist', $module);
		exit;
	break;
}
?>