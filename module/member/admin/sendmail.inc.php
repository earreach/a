<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('发送邮件', '?moduleid='.$moduleid.'&file='.$file),
    array('发送记录', '?moduleid='.$moduleid.'&file='.$file.'&action=record'),
    array('获取列表', '?moduleid='.$moduleid.'&file='.$file.'&action=make'),
    array('邮件列表', '?moduleid='.$moduleid.'&file='.$file.'&action=list'),
);
function _userinfo($fields, $email) {
	if($fields == 'mail') {
		return DB::get_one("SELECT * FROM ".DT_PRE."member m,".DT_PRE."company c WHERE m.userid=c.userid AND c.mail='$email'");
	} else {
		return DB::get_one("SELECT * FROM ".DT_PRE."member m,".DT_PRE."company c WHERE m.userid=c.userid AND m.email='$email'");
	}
}
$path = 'email';
$key = $path;
include DT_ROOT.'/module/'.$module.'/admin/sendlist.inc.php';
switch($action) {
	case 'record':
		$sfields = array('按条件', '标题', '邮箱', '内容', 'IP', '备注');
		$dfields = array('title', 'title', 'email', 'content', 'ip', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;

		(isset($ip) && is_ip($ip)) or $ip = '';
		(isset($email) && is_email($email)) or $email = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) ? intval($status) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($status) $condition .= " AND status=$status";
		if($email) $condition .= " AND email='$email'";
		if($ip) $condition .= " AND ip='$ip'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}mail_log WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$records = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}mail_log WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$records[] = $r;
		}
		include tpl('sendmail_record', $module);
	break;
	case 'show':
		$itemid or msg();
		$item = $db->get_one("SELECT * FROM {$DT_PRE}mail_log WHERE itemid=$itemid");
		$item or msg();
		extract($item);
		include tpl('sendmail_show', $module);		
	break;
	case 'resend':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$DT['mail_log'] = $i = 0;		
		$result = $db->query("SELECT * FROM {$DT_PRE}mail_log WHERE itemid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			if($r['status'] == 3) continue;
			if(send_mail($r['email'], $r['title'], $r['content'])) {
				$db->query("UPDATE {$DT_PRE}mail_log SET status=3,edittime='".DT_TIME."',editor='$_username',note='' WHERE itemid=$r[itemid]");
				$i++;
			}
		}
		dmsg('成功发送('.$i.')封', $forward);
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$DT_PRE}mail_log WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'clear':
		$time = $DT_TODAY - 90*86400;
		$db->query("DELETE FROM {$DT_PRE}mail_log WHERE addtime<$time");
		dmsg('清理成功', $forward);
	break;
	default:
		if($DT['mail_type'] == 'close') msg('邮件发送功能未开启', '?file=setting&tab=5');
		if(isset($send)) {
			if(isset($preview) && $preview) {
				$content = stripslashes($content);
				if($template) {
					if($sendtype == 2) {
						$emails = explode("\n", $emails);
						$email = trim($emails[0]);
					} else if($sendtype == 3) {
						$emails = explode("\n", file_get(DT_ROOT.'/file/'.$path.'/'.$mail));
						$email = trim($emails[0]);
					}
					$user = _userinfo($fields, $email);
					if($user && _safecheck($title)) eval("\$title = \"$title\";");
					$content = ob_template($template, 'mail');
				}
				echo '<br/><b>邮件标题：</b>'.$title.'<br/><br/>';
				echo '<b>邮件正文：</b><br/><br/>';
				echo $content;
				exit;
			}
			if($sendtype == 1) {
				$title or msg('请填写邮件标题');
				is_email($email) or msg('请填写邮件地址');
				($template || $content) or msg('请填写邮件内容');
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				clear_upload($content, $_userid, 'sendmail');
				$email = trim($email);
				$DT['mail_name'] = $name;
				$s = 0;
				if($template) {
					$user = _userinfo($fields, $email);
					if($user && _safecheck($title)) eval("\$title = \"$title\";");
					$content = ob_template($template, 'mail');					
				}
				$code = send_mail($email, $title, $content, $sender);
				if($code) $s++;
				dmsg($s ? '邮件发送成功' : '邮件发送失败', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
			} else if($sendtype == 2) {
				$title or msg('请填写邮件标题');
				$emails or msg('请填写邮件地址');
				($template || $content) or msg('请填写邮件内容');
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				clear_upload($content, $_userid, 'sendmail');
				$emails = explode("\n", $emails);
				$DT['mail_name'] = $name;
				$_content = $content;
				$s = $f = 0;
				foreach($emails as $email) {
					$email = trim($email);
					if(is_email($email)) {
					    $content = $_content;
						if($template) {
							$user = _userinfo($fields, $email);
							if($user && _safecheck($title)) eval("\$title = \"$title\";");
							$content = ob_template($template, 'mail');
						}
						$code = send_mail($email, $title, $content, $sender);
						if($code) {
							$s++;
						} else {
							$f++;
						}
					}
				}
				dmsg('发送成功('.$s.'),发送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
			} else if($sendtype == 3) {
				if(isset($id)) {
					$data = cache_read('temp-'.$file.'-'.$_username.'.php');
					$title = $data['title'];
					$content = $data['content'];
					$sender = $data['sender'];
					$name = $data['name'];
					$template = $data['template'];
					$list = $data['list'];
					$fields = $data['fields'];
				} else {
					$id = $s = $f = 0;
					$title or msg('请填写邮件标题');
					$list or msg('请选择邮件列表');
					($template || $content) or msg('请填写邮件内容');
					$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
					clear_upload($content, $_userid, 'sendmail');
					$data = array();
					$data['title'] = $title;
					$data['content'] = $content;
					$data['sender'] = $sender;
					$data['name'] = $name;
					$data['template'] = $template;
					$data['list'] = $list;
					$data['fields'] = $fields;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
				$_content = $content;
				$pertime = isset($pertime) ? intval($pertime) : 3;
				if($pertime < 0) $pertime = 3;
				$pernum = isset($pernum) ? intval($pernum) : 5;
				if($pernum < 1) $pernum = 5;
				$DT['mail_name'] = $name;
				$emails = file_get(DT_ROOT.'/file/'.$path.'/'.$list);
				$emails = explode("\n", $emails);
				for($i = 1; $i <= $pernum; $i++) {
					$email = trim($emails[$id++]);
					if(is_email($email)) {
						$content = $_content;
						if($template) {
							$user = _userinfo($fields, $email);
							if($user && _safecheck($title)) eval("\$title = \"$title\";");
							$content = ob_template($template, 'mail');
						}
						$code = send_mail($email, $title, $content, $sender);
						if($code) {
							$s++;
						} else {
							$f++;
						}
					}
				}
				$tt = count($emails);
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
					$sender = $data['sender'];
					$name = $data['name'];
					$template = $data['template'];
					$fields = $data['fields'];
				} else {
					$id = $s = $f = 0;
					$title or msg('请填写邮件标题');
					($template || $content) or msg('请填写邮件内容');
					$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
					clear_upload($content, $_userid, 'sendmail');
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
					$data['sender'] = $sender;
					$data['name'] = $name;
					$data['template'] = $template;
					$data['fields'] = $fields;
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
					$email = ($fields == 'mail' && isset($r['mail']) && $r['mail']) ? $r['mail'] : $r['email'];
					if(is_email($email)) {
						$content = $_content;
						if($template) {
							$user = userinfo($r['username']);
							if($user && _safecheck($title)) eval("\$title = \"$title\";");
							$content = ob_template($template, 'mail');
						}
						$code = send_mail($email, $title, $content, $sender);
						//$code = 1;echo $email.'<br/>';
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
			isset($email) or $email = '';
			$emails = '';
			if(isset($userid)) {
				if($userid) {
					$userids = is_array($userid) ? implode(',', $userid) : $userid;					
					$result = $db->query("SELECT email FROM {$DT_PRE}member WHERE userid IN ($userids)");
					while($r = $db->fetch_array($result)) {
						$emails .= $r['email']."\n";
					}
				}
			}
			if($email) {
				if(strpos($email, ',') === false) {
					is_email($email) or $email = '';
				} else {
					$tmp = explode(',', $email);
					foreach($tmp as $eml) {
						if(is_email($eml)) $emails .= $eml."\n";
					}
					$email = '';
				}
			}
			if($emails && strpos($emails, "\n") !== false) $sendtype = 2;
			include tpl('sendmail', $module);
		}
	break;
}
?>