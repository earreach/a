<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('上传记录', '?file='.$file),
    array('转码记录', '?file='.$file.'&action=convert'),
    array('文件预览', '?file='.$file.'&action=preview'),
);
$id = isset($id) ? intval($id) : -1;
($id > -1 && $id < 10) or $id = -1;
if($id == -1 && !in_array($action, array('part', 'delete_user', 'find', 'convert', 'reset', 'queue', 'convert_delete', 'preview', 'clear'))) $action = 'part';
if($id > -1) $table = $DT_PRE.'upload_'.$id;
switch($action) {
	case 'delete':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$result = $db->query("SELECT fileurl FROM {$table} WHERE pid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			 delete_upload($r['fileurl'], 0);
		}
		$db->query("DELETE FROM {$table} WHERE pid IN ($itemids)");
		if(isset($ajax)) {
			exit('1');
		} else {
			dmsg('删除成功', $forward);
		}
	break;
	case 'delete_record':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$table} WHERE pid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'delete_user':
		check_name($username) or msg('请填写会员名');
		$u = $db->get_one("SELECT userid,groupid FROM {$DT_PRE}member WHERE username='$username'");
		if($u && $u['groupid'] == 1) msg('管理组不可删除');
		if($id > -1) {
			if(!isset($fid)) {
				$r = $db->get_one("SELECT min(pid) AS fid FROM {$table} WHERE username='$username'");
				$fid = $r['fid'] ? $r['fid'] : 0;
			}
			if(!isset($tid)) {
				$r = $db->get_one("SELECT max(pid) AS tid FROM {$table} WHERE username='$username'");
				$tid = $r['tid'] ? $r['tid'] : 0;
			}
			isset($num) or $num = 2;
			isset($sid) or $sid = $fid;
			isset($itemid) or $itemid = 1;
			if($fid <= $tid) {
				$result = $db->query("SELECT * FROM {$table} WHERE pid>=$fid AND username='$username' ORDER BY pid LIMIT 0,$num ");
				if($db->affected_rows($result)) {
					while($r = $db->fetch_array($result)) {
						$itemid = $r['pid'];
						delete_upload($r['fileurl'], 0);
					}
					$itemid += 1;
				} else {
					$itemid = $fid + $num;
				}
				msg('ID从'.$fid.'至'.($itemid-1).'删除成功'.progress($sid, $fid, $tid), "?file=$file&action=$action&username=$username&id=$id&sid=$sid&fid=$itemid&tid=$tid&num=$num");
			} else {
				dmsg('删除成功', "?file=$file");
			}
		} else {
			if($u) {
				$id = $u['userid']%10;
			} else {
				for($i = 0; $i < 10; $i++) {
					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}upload_{$i} WHERE username='$username'");
					if($t) {
						$id = $i;
						break;
					}
				}
				if($id == -1) msg('会员['.$username.']没有上传记录');
			}
			msg('正在开始删除..', "?file=$file&action=$action&username=$username&id=$id");
		}
	break;
	case 'part':
		$lists = array();
		for($i = 0; $i < 10; $i++) {
			$r = array();
			$r['table'] = $DT_PRE.'upload_'.$i;
			$t = $db->get_one("SHOW TABLE STATUS FROM `".$CFG['db_name']."` LIKE '".$r['table']."'");
			$r['rows'] = $t['Rows'];
			$r['name'] = $t['Comment'];
			$lists[] = $r;
		}
		include tpl('upload_part');
	break;
	case 'find':
		if(check_name($url)) {
			$user = userinfo($url);
			$user or msg('会员不存在');
			dheader('?file='.$file.'&id='.($user['userid']%10).'&fields=2&kw='.$url);
		} else {
			is_url($url) or msg('网址错误');
			$t = parse_url($url);
			$kw = $t['path'];
			if(strpos($url, '/file/upload/') !== false) $kw = str_replace('/file/upload/', '', $kw);
			dheader('?file='.$file.'&id='.(match_userid($url)%10).'&kw='.$kw);
		}
	break;
	case 'delete_convert':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$result = $db->query("SELECT * FROM {$DT_PRE}upload_convert WHERE itemid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			 delete_upload($r['fileurl'], 0);
			 delete_upload(substr($r['fileurl'], 0, -strlen($r['fileext'])).$r['toext'], 0);
		}
		$db->query("DELETE FROM {$DT_PRE}upload_convert WHERE itemid IN ($itemids)");
		if(isset($ajax)) {
			exit('1');
		} else {
			dmsg('删除成功', $forward);
		}
	break;
	case 'reset':
		$itemid or msg('请选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$result = $db->query("SELECT * FROM {$DT_PRE}upload_convert WHERE itemid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			$filepath = DT_ROOT.'/file/upload/'.cutstr($r['fileurl'], '/file/upload/', '');
			$topath = substr($filepath, 0, -strlen($r['fileext'])).$r['toext'];
			if(!is_file($topath)) file_copy(DT_ROOT.'/static/image/convert.'.$r['toext'], $topath); 
			@touch($topath, $r['addtime']);
			$db->query("UPDATE {$DT_PRE}upload_convert SET status=0,edittime=$r[addtime] WHERE itemid=$r[itemid]");
		}
		dmsg('修改成功', $forward);
	break;
	case 'queue':
		$files or msg('请填写文件地址');
		include DT_ROOT.'/file/config/convert.inc.php';
		$num = 0;
		foreach(explode("\n", $files) as $fileurl) {
			$fileurl = trim($fileurl);
			if(strpos($fileurl, '/file/upload/') === false) continue;
			$fileext = file_ext($fileurl);
			$toext = isset($CV[$fileext]) ? $CV[$fileext] : '';
			if(!$toext) continue;
			$filepath = DT_ROOT.'/file/upload/'.cutstr($fileurl, '/file/upload/', '');
			if(!is_file($filepath)) continue;
			$t = $db->get_one("SELECT itemid FROM {$DT_PRE}upload_convert WHERE fileurl='$fileurl'");
			if($t) continue;
			$filesize = filesize($filepath);
			$filetime = filemtime($filepath);
			$topath = substr($filepath, 0, -strlen($fileext)).$toext;
			if(!is_file($topath)) file_copy(DT_ROOT.'/static/image/convert.'.$toext, $topath); 
			@touch($topath, $filetime);
			$username = $_username;
			$userid = match_userid($fileurl);
			if($userid > 0 && $userid != $_userid) {
				$t = $db->get_one("SELECT username FROM {$DT_PRE}member WHERE userid=$userid");
				if($t && $t['username']) $username = $t['username'];
			}
			$db->query("INSERT INTO {$DT_PRE}upload_convert (fileurl,filesize,fileext,toext,addtime,edittime,username) VALUES ('$fileurl','$filesize','$fileext','$toext','$filetime','$filetime','$username')");
			$num++;
		}
		if($num > 0) dmsg('成功加入'.$num.'文件', $forward);
		msg('未匹配到有效的文件');
	break;
	case 'convert':
		include DT_ROOT.'/file/config/convert.inc.php';
		$sfields = array('按条件', '文件名', '会员', '源格式', '新格式');
		$dfields = array('fileurl', 'fileurl', 'username', 'fileext', 'toext');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('排序方式', '上传时间降序', '上传时间升序', '转换时间降序', '转换时间升序', '文件大小降序', '文件大小升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'filesize DESC', 'filesize ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$dstatus = array('<span class="f_gray">待转码</span>', '<span class="f_blue">转码中</span>', '<span class="f_red">转码失败</span>', '<span class="f_green">转码成功</span>');
		isset($status) && isset($dstatus[$status]) or $status = -1;
		(isset($username) && check_name($username)) or $username = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '-1');

		$condition = '1';
		if($keyword) $condition .= $fields < 2 ? match_kw($dfields[$fields], $keyword) : " AND $dfields[$fields]='$keyword'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($status > -1) $condition .= " AND status=$status";

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}upload_convert WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}upload_convert WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['ext'] = $fileext = $r['toext'];
			is_file(DT_ROOT.'/file/ext/'.$fileext.'.gif') or $r['ext'] = 'oth';
			if($r['filesize'] > 1024*1024*1024) {
				$r['size'] = dround($r['filesize']/1024/1024/1024, 2).'G';
			} else if($r['filesize'] > 1024*1024) {
				$r['size'] = dround($r['filesize']/1024/1024, 2).'M';
			} else {
				$r['size'] = dround($r['filesize']/1024, 2).'K';
			}
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['edittime'] = timetodate($r['edittime'], 6);
			$r['fileurl'] = str_replace('.thumb.'.$fileext, '', $r['fileurl']);
			$r['fileuri'] = substr($r['fileurl'], 0, -strlen($r['fileext'])).$r['toext'];
			$r['url'] = 'javascript:;" onclick="_preview(\''.$r['fileuri'].'\');';
			$lists[] = $r;
		}
		include tpl('upload_convert');
	break;
	case 'clear':
		function dir_list($dir, $ds = array()) {
			$files = glob($dir.'/*');
			if(!is_array($files)) return $ds;
			foreach($files as $file) {
				if(is_dir($file)) {
					$ds[] = $file;
					$ds = dir_list($file, $ds);
				}
			}
			return $ds;
		}
		function dir_zero($ds) {
			$zs = array();
			foreach($ds as $k=>$v) {
				if(is_file($v)) continue;
				$ff = 0;
				foreach(glob($v.'/*') as $kk=>$vv) {
					if(is_file($vv)) {
						if(basename($vv) == 'index.html') continue;
						$ff = 1;
						break;
					}
					if(is_dir($vv)) {
						$ff = 1;
						break;
					}
				}
				if($ff == 0) $zs[] = $v;
			}
			return $zs;
		}
		$fid = isset($fid) ? intval($fid) : 0;
		$num = isset($num) ? intval($num) : 0;
		$root = DT_ROOT.'/file/upload';
		$dirs = array();
		foreach(glob($root.'/*') as $k=>$v) {
			if(is_dir($v)) $dirs[] = $v;
		}
		if(isset($dirs[$fid])) {
			foreach(dir_zero(dir_list($dirs[$fid])) as $v) {
				dir_delete($v);
				$num++;
			}
			foreach(dir_zero($dirs) as $v) {
				dir_delete($v);
				$num++;
			}
			$fid++;
			msg('已清理'.$num.'个空目录，扫描中...'.progress(1, $fid, count($dirs)), "?file=$file&action=$action&fid=$fid&num=$num", 0);
		} else {
			dmsg('清理成功'.$num.'个', "?file=$file&action=preview");
		}
	break;
	case 'preview':
		function dir_nav($dir) {
			$nav = $pre = '';
			if($dir) {
				foreach(explode('/', $dir) as $v) {
					if($v) {
						$pre .= $v.'/';
						$nav .= '<a href="?file=upload&action=preview&dir='.$pre.'">'.$v.'</a> / ';
					}
				}
			}
			return $nav;
		}
		isset($datetype) && in_array($datetype, array('ymd', 'ym', 'y')) or $datetype = 'ymd';
		(isset($fromdate) && is_date($fromdate)) or $fromdate = '';
		$year = '';
		if($fromdate) {
			if($datetype == 'ymd') {
				$dir = substr($fromdate, 0, 4).substr($fromdate, 5, 2).'/'.substr($fromdate, 8, 2);
			} else if($datetype == 'ym') {
				$dir = substr($fromdate, 0, 4).substr($fromdate, 5, 2);
			} else if($datetype == 'y') {
				$year = substr($fromdate, 0, 4);
			}
		}
		(isset($dir) && preg_match("/^[a-z0-9\/\-]{2,20}$/i", $dir)) or $dir = '';
		if(substr($dir, 0, 1) == '/') $dir = substr($dir, 1);
		if($dir && substr($dir, -1) != '/') $dir = $dir.'/';
		$root = DT_ROOT.'/file/upload';
		$path = DT_PATH.'file/upload/';
		if($job == 'delete') {
			(isset($name) && preg_match("/^[a-z0-9\.\-]{4,30}$/i", $name)) or $name = '';
			if($name) delete_upload($path.($dir ? $dir : '').$name, 0);
			dmsg('删除成功', "?file=$file&action=$action&dir=$dir");
		}
		$dirs = $files = $S = $T = $P = $thumbs = array();
		$exts = array('zip' => 'rar', 'mp4' => 'mov', 'docx' => 'doc', 'xlsx' => 'xls', 'pptx' => 'ppt');
		$i = 0;
		if(is_dir($root.'/'.($dir ? $dir : ''))) {
			foreach(glob($root.'/'.($dir ? $dir : '').'*') as $k=>$v) {
				$name = basename($v);
				if($year && stripos($name, $year) === false) continue;
				$filesite = str_replace(DT_ROOT.'/', '', $v);
				#if(in_array($filesite, $file_hide)) continue;
				$r = array();
				if(is_dir($v)) {
					$id = $name;
					if(!check_name($id) && $DT['pcharset']) $id = convert($id, $DT['pcharset'], DT_CHARSET);
					$dirs[$i]['id'] = $id;
					$dirs[$i]['dirname'] = $id;
				} else {
					if($kw && stripos($v, $kw) === false) continue;
					$fileext = file_ext($v);
					if(in_array($fileext, array('html', 'htm'))) continue;
					$filename = $name;
					if(!check_name($filename) && $DT['pcharset']) $filename = convert($filename, $DT['pcharset'], DT_CHARSET);
					$id = basename($filename, '.'.$fileext);
					if(substr($id, -6) == '.thumb') {
						$kk = cutstr($id, '', '.'.$fileext);
						$thumbs[] = $kk;
						$thumbs[] = $kk.'.'.$fileext.'.middle';
					}
					$files[$i]['id'] = $id;
					$files[$i]['fileext'] = $fileext;
					$files[$i]['ico'] = is_file(DT_ROOT.'/file/ext/'.$fileext.'.gif') ? $fileext : 'oth';
					$files[$i]['filename'] = $id.($fileext ? '.'.$fileext : '');				
					$S[$i] = filesize($v);
					$files[$i]['filesize'] = round($S[$i]/1024, 2);
					$T[$i] = filemtime($v);
					$files[$i]['mtime'] = timetodate($T[$i], 6);
					$url = $path.$dir.$filename;
					if(is_image($filename)) {
						$files[$i]['url'] = 'javascript:;" onclick="PhotoShow(\''.cutstr($url, '', '.thumb.').'\');';
						$files[$i]['src'] = $url;
						if(strpos($filename, '.thumb.') === false && strpos($filename, '.middle.') === false) {
							$middle = is_file($root.'/'.$dir.$filename.'.middle.'.$fileext) ? $url.'.middle.'.$fileext : $url;
							$P[] = array('big' => $url, 'middle' => $middle);
						}
					} else {
						$files[$i]['url'] = 'javascript:;" onclick="_preview(\''.$url.'\');';
						$ext = is_file(DT_ROOT.'/file/ext/icon_'.$fileext.'.gif') ? $fileext : 'oth';
						if(isset($exts[$fileext])) $ext = $exts[$fileext];
						$files[$i]['src'] = 'file/ext/icon_'.$ext.'.gif';
					}
				}
				$i++;
			}
		}
		if($dirs) krsort($dirs);
		if($files) krsort($files);
		if($P) krsort($P);
		if($dir && !$dirs && !$files) dir_delete($root.'/'.$dir);//自动删除空目录
		$items = count($P);
		include tpl('upload_preview');
	break;
	default:
		$sfields = array('按条件', '文件名', '会员', '来源', '后缀', '信息ID', '表名');
		$dfields = array('fileurl', 'fileurl', 'username', 'upfrom', 'fileext', 'itemid', 'tb');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('排序方式', '上传时间降序', '上传时间升序', '文件大小降序', '文件大小升序', '图片宽度降序', '图片宽度升序', '图片高度降序', '图片高度升序');
		$dorder  = array('pid DESC', 'addtime DESC', 'addtime ASC', 'filesize DESC', 'filesize ASC', 'width DESC', 'width ASC', 'height DESC', 'height ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;

		(isset($username) && check_name($username)) or $username = '';
		$thumb = isset($thumb) ? intval($thumb) : 0;
		$width = isset($width) ? intval($width) : 0;
		$height = isset($height) ? intval($height) : 0;
		$filesize = isset($filesize) ? intval($filesize) : 0;
		$upfrom = isset($upfrom) ? $upfrom : '';
		$tb = isset($tb) ? $tb : '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$itemid or $itemid = '';

		$module_select = module_select('mid', '模块', $mid, '');
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($keyword) $condition .= $fields < 2 ? match_kw($dfields[$fields], $keyword) : " AND $dfields[$fields]='$keyword'";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($mid) $condition .= " AND moduleid='$mid'";	
		if($itemid) $condition .= " AND itemid='$itemid'";	
		if($username) $condition .= " AND username='$username'";
		if($upfrom) $condition .= " AND upfrom='$upfrom'";
		if($tb) $condition .= " AND tb='$tb'";
		if($thumb) $condition .= " AND width>0";
		if($width) $condition .= " AND width=$width";
		if($height) $condition .= " AND height=$height";
		if($filesize) $condition .= " AND filesize=$filesize";

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['ext'] = $fileext = file_ext($r['fileurl']);
			is_file(DT_ROOT.'/file/ext/'.$fileext.'.gif') or $r['ext'] = 'oth';
			if($r['filesize'] > 1024*1024*1024) {
				$r['size'] = dround($r['filesize']/1024/1024/1024, 2).'G';
			} else if($r['filesize'] > 1024*1024) {
				$r['size'] = dround($r['filesize']/1024/1024, 2).'M';
			} else {
				$r['size'] = dround($r['filesize']/1024, 2).'K';
			}
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['fileurl'] = str_replace('.thumb.'.$fileext, '', $r['fileurl']);
			$r['img_w'] = $r['width'] > 100 ? 100 : $r['width'];
			$lists[] = $r;
		}
		include tpl('upload');
	break;
}
?>