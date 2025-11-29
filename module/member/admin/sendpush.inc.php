<?php
defined('DT_ADMIN') or exit('Access Denied');
isset($username) or $username = '';
$menus = array (
    array('新建推送', '?moduleid='.$moduleid.'&file='.$file.'&username='.$username),
    array('推送记录', '?moduleid='.$moduleid.'&file='.$file.'&username='.$username.'&action=record'),
    array('获取列表', '?moduleid='.$moduleid.'&file='.$file.'&action=make'),
    array('用户列表', '?moduleid='.$moduleid.'&file='.$file.'&action=list'),
    array('设备绑定', '?moduleid='.$moduleid.'&file='.$file.'&action=bind'),
);
$path = 'username';
$key = $path;
include DT_ROOT.'/module/'.$module.'/admin/sendlist.inc.php';
switch($action) {
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$DT_PRE}app_push WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'clear':
		$time = $DT_TODAY - 90*86400;
		$db->query("DELETE FROM {$DT_PRE}app_push WHERE sendtime<$time");
		dmsg('清理成功', $forward);
	break;
	case 'delete_bind':
		is_array($uuids) or msg('未选择记录');
		foreach($uuids as $uuid) {
			if(is_uuid($uuid)) $db->query("DELETE FROM {$DT_PRE}app_bind WHERE uuid='$uuid'");
		}
		dmsg('删除成功', $forward);
	break;
	case 'unbind':
		is_array($uuids) or msg('未选择记录');
		foreach($uuids as $uuid) {
			if(is_uuid($uuid)) $db->query("UPDATE {$DT_PRE}app_bind SET username='' WHERE uuid='$uuid'");
		}
		dmsg('解绑成功', $forward);
	break;
	case 'bind':
		require DT_ROOT.'/include/client.func.php';
		$sfields = array('按条件', '会员名', '设备标识', '手机系统', 'APP版本', '手机品牌', 'IP', '客户端');
		$dfields = array('username', 'username', 'uuid', 'os', 'vn', 'bd', 'ip', 'ua');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;

		(isset($uuid) && is_uuid($uuid)) or $uuid = '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND lasttime>=$fromtime";
		if($totime) $condition .= " AND lasttime<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($uuid) $condition .= " AND uuid='$uuid'";

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}app_bind WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}app_bind WHERE $condition ORDER BY lasttime DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			if(!$r['bd']) {
				$r['bd'] = $bd = get_bd($r['ua']);
				if($bd) $db->query("UPDATE {$DT_PRE}app_bind SET bd='$bd' WHERE uuid='$r[uuid]'");
			}
			$r['lasttime'] = timetodate($r['lasttime'], 6);
			$lists[] = $r;
		}
		include tpl('sendpush_bind', $module);
	break;
	case 'record':
		$sfields = array('按条件', '标题', '内容', '链接', '会员名', '设备标识', '操作人', 'IP', '返回代码');
		$dfields = array('title', 'title', 'message', 'link', 'username', 'uuid', 'editor', 'ip', 'code');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		
		(isset($ip) && is_ip($ip)) or $ip = '';
		(isset($uuid) && is_uuid($uuid)) or $uuid = '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) ? intval($status) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);

		$condition = '1';
		if($keyword) $condition .= $fields < 3 ? match_kw($dfields[$fields], $keyword) : " AND $dfields[$fields]='$keyword'";
		if($fromtime) $condition .= " AND sendtime>=$fromtime";
		if($totime) $condition .= " AND sendtime<=$totime";
		if($status) $condition .= " AND status=$status";
		if($username) $condition .= " AND username='$username'";
		if($ip) $condition .= " AND ip='$ip'";
		if($uuid) $condition .= " AND uuid='$uuid'";

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}app_push WHERE $condition");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}app_push WHERE $condition ORDER BY itemid DESC LIMIT $offset,$pagesize");
		while($r = $db->fetch_array($result)) {
			$r['senddate'] = timetodate($r['sendtime'], 6);
			$lists[] = $r;
		}
		include tpl('sendpush_record', $module);
	break;
	default:
		if(!$DT['push_appkey']) msg('消息推送功能未开启', '?file=setting&tab=8');
		$GROUP = cache_read('group.php');
		if(isset($send)) {
			if(isset($preview) && $preview) {
				if($sendtype == 2) {
					$usernames = explode("\n", $usernames);
					$username = trim($usernames[0]);
				} else if($sendtype == 3) {
					$usernames = explode("\n", file_get(DT_ROOT.'/file/'.$path.'/'.$list));
					foreach($usernames as $v) {
						if(check_name($v)) {
							$username = trim($v);
							break;
						}
					}
				}
				$user = userinfo($username);
				if($user && _safecheck($title)) eval("\$title = \"$title\";");
				if($user && _safecheck($content)) eval("\$content = \"$content\";");
				echo '<br/><b>标题：</b>'.$title.'<br/>';
				echo '<br/><b>内容：</b><br/>';
				echo $content;
				echo '<br/><b>链接：</b>'.$linkurl;
				exit();
			}
			if($sendtype == 1) {
				$touser or msg('请填写会员名');
				$content or msg('请填写消息内容');
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
					$code = send_push($username, $content, $title, $linkurl);
					if(strpos($code, $DT['push_ok']) !== false) {
						$s++;
					} else {
						$f++;
					}
				}
				dmsg('推送成功('.$s.'),推送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
			} else if($sendtype == 2) {
				$content or msg('请填写消息内容');
				$s = 0;
				if(strpos($title, '{$') !== false || strpos($content, '{$') !== false) msg('分组群发不支持变量');
				if(isset($GROUP[$groupid])) {
					$username = $GROUP[$groupid]['groupname'];
					$uuid = 'group-'.$groupid;
				} else {
					$username = '全部会员';
					$uuid = 'all';
				}
				$code = send_push($username, $content, $title, $linkurl, $uuid);
				if(strpos($code, $DT['push_ok']) !== false) $s++;
				dmsg($s ? '消息推送成功' : '消息推送失败', $forward);
			} else if($sendtype == 3) {
				if(isset($id)) {
					$data = cache_read('temp-'.$file.'-'.$_username.'.php');
					$title = $data['title'];
					$content = $data['content'];
					$linkurl = $data['linkurl'];
					$list = $data['list'];
				} else {
					$id = $s = $f = 0;
					$content or msg('请填写消息内容');
					$list or msg('请选择会员列表');
					$data = array();
					$data['list'] = $list;
					$data['title'] = $title;
					$data['content'] = $content;
					$data['linkurl'] = $linkurl;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
				$_title = $title;
				$_content = $content;
				$pertime = isset($pertime) ? intval($pertime) : 3;
				if($pertime < 0) $pertime = 3;
				$pernum = isset($pernum) ? intval($pernum) : 5;
				if($pernum < 1) $pernum = 5;
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
						$code = send_push($username, $content, $title, $linkurl);
						if(strpos($code, $DT['push_ok']) !== false) {
							$s++;
						} else {
							$f++;
						}
					}
				}
				$tt = count($usernames);
				if($id < $tt) {
					msg('推送成功('.$s.')，推送失败('.$f.')<br/>系统将自动继续，请稍候...'.progress(0, $id, $tt), '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&sendtype='.$sendtype.'&id='.$id.'&s='.$s.'&f='.$f.'&pernum='.$pernum.'&pertime='.$pertime.'&send=1', $pertime);
				}
				cache_delete('temp-'.$file.'-'.$_username.'.php');
				msg('推送成功('.$s.')，推送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record', 3);
			} else if($sendtype == 4) {
				if(isset($id)) {
					$data = cache_read('temp-'.$file.'-'.$_username.'.php');
					$title = $data['title'];
					$content = $data['content'];
					$linkurl = $data['linkurl'];
					$tt = $data['tt'];
					$tb = $data['tb'];
					$sql = $data['sql'];
				} else {
					$id = $s = $f = 0;
					$content or msg('请填写消息内容');
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
					$data['linkurl'] = $linkurl;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
				$_title = $title;
				$_content = $content;
				$pertime = isset($pertime) ? intval($pertime) : 3;
				if($pertime < 0) $pertime = 3;
				$pernum = isset($pernum) ? intval($pernum) : 5;
				if($pernum < 1) $pernum = 5;
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
						$code = send_push($username, $content, $title, $linkurl);
						//$code = 1;echo $username.'<br/>';
						if(strpos($code, $DT['push_ok']) !== false) {
							$s++;
						} else {
							$f++;
						}
					}
				}
				if($res) {
					msg('推送成功('.$s.')，推送失败('.$f.')<br/>系统将自动继续，请稍候...'.progress(0, $id, $tt), '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&sendtype='.$sendtype.'&id='.$id.'&s='.$s.'&f='.$f.'&pernum='.$pernum.'&pertime='.$pertime.'&page='.($page+1).'&send=1', $pertime);
				}
				cache_delete('temp-'.$file.'-'.$_username.'.php');
				msg('推送成功('.$s.')，推送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record', 3);
			}
			dmsg('推送完成', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
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
			include tpl('sendpush', $module);
		}
	break;
}
?>