<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('发送信件', '?moduleid='.$moduleid.'&file='.$file.'&action=send'),
    array('会员信件', '?moduleid='.$moduleid.'&file='.$file),
    array('获取列表', '?moduleid='.$moduleid.'&file='.$file.'&action=make'),
    array('用户列表', '?moduleid='.$moduleid.'&file='.$file.'&action=list'),
    array('系统广播', '?moduleid='.$moduleid.'&file='.$file.'&action=system'),
    array('邮件转发', '?moduleid='.$moduleid.'&file='.$file.'&action=mail'),
    array('信件清理', '?moduleid='.$moduleid.'&file='.$file.'&action=clear'),
);
require DT_ROOT.'/module/'.$module.'/message.class.php';
$do = new message;
$NAME = array('普通', '询价', '报价', '留言', '信使');
$path = 'username';
$key = $path;
include DT_ROOT.'/module/'.$module.'/admin/sendlist.inc.php';
switch($action) {
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			$do->_edit($message);
			dmsg('修改成功', '?moduleid='.$moduleid.'&file='.$file.'&action=system');
		} else {
			extract($do->get_one());
			include tpl('message_edit', $module);
		}
	break;
	case 'clear':
		if($submit) {
			if($do->_clear($message)) {
				dmsg('清理成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$todate = timetodate(datetotime('-1 year'), 3).' 23:23:59';
			include tpl('message_clear', $module);
		}
	break;
	case 'mail':
		if(isset($send)) {
			isset($num) or $num = 0;
			$hour = intval($hour);
			if(!$hour) $hour = 48;
			$pertime = isset($pertime) ? intval($pertime) : 0;
			if($pertime < 0) $pertime = 0;
			$pernum = isset($pernum) ? intval($pernum) : 5;
			if($pernum < 1) $pernum = 5;
			$pagesize = $pernum;
			$offset = ($page-1)*$pagesize;
			$time = $DT_TIME - $hour*3600;
			$result = $db->query("SELECT * FROM {$DT_PRE}message WHERE isread=0 AND issend=0 AND addtime<$time AND status=3 ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
			$i = false;
			while($r = $db->fetch_array($result)) {
				$m = $db->get_one("SELECT email FROM {$DT_PRE}member WHERE username='$r[touser]' AND groupid>4");
				if(!$m) continue;
				$linkurl = $MODULE[2]['linkurl'].'message'.DT_EXT.'?action=show&itemid='.$r['itemid'];
				$r['fromuser'] or $r['fromuser'] = '系统信使';
				$r['content'] = $r['fromuser'].' 于 '.timetodate($r['addtime'], 5).' 向您发送一封站内信，内容如下：<br/><br/>'.$r['content'].'<br/><br/>原始地址：<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/><br/>此邮件通过 <a href="'.DT_PATH.'" target="_blank">'.$DT['sitename'].'</a> 邮件系统发出<br/><br/>如果您不希望收到类似邮件，请经常登录网站查收站内信件或将未读信件标记为已读<br/><br/>';
				send_mail($m['email'], $r['title'], $r['content']);
				$db->query("UPDATE {$DT_PRE}message SET issend=1 WHERE itemid=$r[itemid]");
				$i = true;
				$num++;
			}
			if($i) {
				$page++;
				msg('已发送 '.$num.' 封邮件，系统将自动继续，请稍候...', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&page='.$page.'&hour='.$hour.'&pernum='.$pernum.'&pertime='.$pertime.'&num='.$num.'&send=1', $pertime);
			} else {
				file_put(DT_CACHE.'/message.dat', $DT_TIME);
				msg('邮件发送成功 共发送 '.$num.' 封邮件', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action, 5);
			}
		} else {
			$lasttime = is_file(DT_CACHE.'/message.dat') ? file_get(DT_CACHE.'/message.dat') : 0;
			$lasttime = $lasttime ? timetodate($lasttime, 5) : '';
			include tpl('message_mail', $module);
		}
	break;
	case 'remove':
		$itemid or msg('请选择信件');
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->_delete($itemid);
		}
		dmsg('删除成功', $forward);
	break;
	case 'system':
		$messages = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}message WHERE groupids<>'' ORDER BY itemid DESC");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['group'] = '<select>';
			$groupids = explode(',', $r['groupids']);
			foreach($groupids as $groupid) {
				$r['group'] .= '<option>'.$GROUP[$groupid]['groupname'].'</option>';
			}
			$r['group'] .= '</select>';
			$messages[] = $r;
		}
		include tpl('message_system', $module);
	break;
	case 'delete':
		$itemid or msg('请选择信件');
		$do->itemid = $itemid;
		$do->delete();
		dmsg('删除成功', $forward);
	break;
	case 'show':
		$itemid or msg('请选择信件');
		$do->itemid = $itemid;
		$item = $do->get_one();
		$item or msg();
		extract($item);
		include tpl('message_show', $module);		
	break;
	case 'send':
		if(isset($send)) {
			if($sendtype == 1) {
				$touser or msg('请填写收件会员');
				$title or msg('请填写信件标题');
				$content or msg('请填写信件内容');
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				clear_upload($content, $_userid, 'message');
				$_title = $title;
				$_content = $content;
				$s = $f = 0;
				foreach(explode(' ', $touser) as $username) {
					if(!check_name($username)) continue;
					$title = $_title;
					$content = $_content;
					if(strpos($title, '{$') !== false || strpos($content, '{$') !== false) {
						$user = userinfo($username);
						if($user && _safecheck($title)) eval("\$title = \"$title\";");
						if($user && _safecheck($content)) eval("\$content = \"$content\";");
					}
					$code = send_message($username, $title, $content);
					if($code) {
						$s++;
					} else {
						$f++;
					}
				}
				dmsg('发送成功('.$s.'),发送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
			} else if($sendtype == 2) {
				$groupids or msg('请选择会员组');
				$title or msg('请填写信件标题');
				$content or msg('请填写信件内容');
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				clear_upload($content, $_userid, 'message');
				$s = 0;
				$post = array();
				$post['groupids'] = $groupids;
				$post['title'] = $title;
				$post['content'] = $content;
				$code = $do->_send($post);
				if($code) $s++;
				dmsg($s ? '广播发送成功' : '广播发送失败', '?moduleid='.$moduleid.'&file='.$file.'&action=system');
			} else if($sendtype == 3) {
				if(isset($id)) {
					$data = cache_read('temp-'.$file.'-'.$_username.'.php');
					$title = $data['title'];
					$content = $data['content'];
					$list = $data['list'];
				} else {
					$id = $s = $f = 0;
					$list or msg('请选择会员列表');
					$title or msg('请填写信件标题');
					$content or msg('请填写信件内容');
					$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
					clear_upload($content, $_userid, 'message');
					$data = array();
					$data['list'] = $list;
					$data['title'] = $title;
					$data['content'] = $content;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
				$_title = $title;
				$_content = $content;
				$pertime = isset($pertime) ? intval($pertime) : 0;
				if($pertime < 0) $pertime = 0;
				$pernum = isset($pernum) ? intval($pernum) : 100;
				if($pernum < 1) $pernum = 100;
				$usernames = file_get(DT_ROOT.'/file/'.$path.'/'.$list);
				$usernames = explode("\n", $usernames);
				for($i = 1; $i <= $pernum; $i++) {
					$username = trim($usernames[$id++]);
					if(check_name($username)) {
						$title = $_title;
						$content = $_content;
						if(strpos(strpos($title, '{$') !== false || $content, '{$') !== false) {
							$user = userinfo($username);
							if($user && _safecheck($title)) eval("\$title = \"$title\";");
							if($user && _safecheck($content)) eval("\$content = \"$content\";");
						}
						$code = send_message($username, $title, $content);
						//$code = 1;
						if($code) {
							$s++;
						} else {
							$f++;
						}
					}
				}
				$tt = count($usernames);
				if($id < $tt) {
					msg('发送成功('.$s.')，发送失败('.$f.')<br/>系统将自动继续，请稍候...'.progress(0, $id, $tt), '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&sendtype='.$sendtype.'&id='.$id.'&s='.$s.'&f='.$f.'&pernum='.$pernum.'&pertime='.$pertime.'&send=1', $pertime);
				}
				cache_delete('temp-'.$file.'-'.$_username.'.php');
				msg('发送成功('.$s.')，发送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record', 3);
			} else if($sendtype == 4) {
				if(isset($id)) {
					$data = cache_read('temp-'.$file.'-'.$_username.'.php');
					$tt = $data['tt'];
					$tb = $data['tb'];
					$sql = $data['sql'];
					$title = $data['title'];
					$content = $data['content'];
				} else {
					$id = $s = $f = 0;
					$title or msg('请填写信件标题');
					$content or msg('请填写信件内容');
					$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
					clear_upload($content, $_userid, 'message');
					$tb or $tb = $DT_PRE.'member';
					$tb = strip_sql($tb, 0);
					$sql or $sql = 'groupid>4';
					$sql = strip_sql($sql, 0);
					$tt = $db->count($tb, $sql);
					$data = array();
					$data['tt'] = $tt;
					$data['tb'] = $tb;
					$data['sql'] = $sql;
					$data['title'] = $title;
					$data['content'] = $content;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
				$_title = $title;
				$_content = $content;
				$pertime = isset($pertime) ? intval($pertime) : 0;
				if($pertime < 0) $pertime = 0;
				$pernum = isset($pernum) ? intval($pernum) : 100;
				if($pernum < 1) $pernum = 100;
				$res = 0;
				$pagesize = $pernum;
				$offset = ($page-1)*$pagesize;
				$result = $db->query("SELECT * FROM {$tb} WHERE {$sql} ORDER BY userid DESC LIMIT {$offset},{$pagesize}");
				while($r = $db->fetch_array($result)) {
					$id++; $res = 1;
					$username = $r['username'];
					if(check_name($username)) {
						$title = $_title;
						$content = $_content;
						if(strpos(strpos($title, '{$') !== false || $content, '{$') !== false) {
							$user = userinfo($r['username']);
							if($user && _safecheck($title)) eval("\$title = \"$title\";");
							if($user && _safecheck($content)) eval("\$content = \"$content\";");
						}
						//$code = send_message($username, $title, $content);
						$code = 1;echo $username.'<br/>';
						if($code) {
							$s++;
						} else {
							$f++;
						}
					}
				}
				if($res) {
					msg('发送成功('.$s.')，发送失败('.$f.')<br/>系统将自动继续，请稍候...'.progress(0, $id, $tt), '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&sendtype='.$sendtype.'&id='.$id.'&s='.$s.'&f='.$f.'&pernum='.$pernum.'&pertime='.$pertime.'&page='.($page+1).'&send=1', $pertime);
				}
				cache_delete('temp-'.$file.'-'.$_username.'.php');
				msg('发送成功('.$s.')，发送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record', 3);
			}
			dmsg('发送完成', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
		} else {
			$sendtype = isset($sendtype) ? intval($sendtype) : 1;
			isset($touser) or $touser = '';
			$tousers = '';
			if(isset($userid)) {
				if($userid) {
					$userids = is_array($userid) ? implode(',', $userid) : $userid;					
					$result = $db->query("SELECT username FROM {$DT_PRE}member WHERE userid IN ($userids)");
					while($r = $db->fetch_array($result)) {
						if($r['username']) $tousers .= $r['username']."\n";
					}
				}
			}
			if($touser) {
				if(strpos($touser, ',') !== false) $touser = explode(',', $touser);
				$tousers .= is_array($touser) ? implode("\n", $touser) : $touser."\n";
			}
			if($tousers) $touser = str_replace("\n", ' ', trim($tousers));
			include tpl('message_send', $module);
		}
	break;
	default:
		$sfields = array('标题', '发件人', '收件人', 'IP', '内容');
		$dfields = array('title', 'fromuser', 'touser', 'ip', 'content');
		$S = array('状态', '草稿箱', '发件箱', '收件箱', '回收站');

		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$typeid = isset($typeid) ? intval($typeid) : -1;
		$read = isset($read) ? intval($read) : -1;
		$send = isset($send) ? intval($send) : -1;
		$status = isset($status) ? intval($status) : 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($fromuser) && check_name($fromuser)) or $fromuser = '';
		(isset($touser) && check_name($touser)) or $touser = '';
		(isset($username) && check_name($username)) or $username = '';
		$tid = isset($tid) ? intval($tid) : 0;
		$tid or $tid = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($S, 'status', '', $status);

		$condition = "groupids=''";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($status) $condition .= " AND status=$status";
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($read > -1) $condition .= " AND isread=$read";
		if($send > -1) $condition .= " AND issend=$send";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($fromuser) $condition .= " AND fromuser='$fromuser'";
		if($touser) $condition .= " AND touser='$touser'";
		if($username) $condition .= " AND (fromuser='$username' OR touser='$username')";
		if($mid) $condition .= " AND mid=$mid";
		if($tid) $condition .= " AND tid=$tid";

		$lists = $do->get_list($condition);
		include tpl('message', $module);
	break;
}
?>