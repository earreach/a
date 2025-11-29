<?php
defined('DT_ADMIN') or exit('Access Denied');
isset($username) or $username = '';
$menus = array (
    array('发送短信', '?moduleid='.$moduleid.'&file='.$file.'&username='.$username),
    array('发送记录', '?moduleid='.$moduleid.'&file='.$file.'&username='.$username.'&action=record'),
    array('获取列表', '?moduleid='.$moduleid.'&file='.$file.'&action=make'),
    array('号码列表', '?moduleid='.$moduleid.'&file='.$file.'&action=list'),
);
function _userinfo($mobile) {
	return DB::get_one("SELECT * FROM ".DT_PRE."member m,".DT_PRE."company c WHERE m.userid=c.userid AND m.mobile='$mobile'");
}
$path = 'mobile';
$key = $path;
include DT_ROOT.'/module/'.$module.'/admin/sendlist.inc.php';
switch($action) {
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$DT_PRE}sms WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	case 'clear':
		$time = $DT_TODAY - 90*86400;
		$db->query("DELETE FROM {$DT_PRE}sms WHERE sendtime<$time");
		dmsg('清理成功', $forward);
	break;
	case 'record':
		$sfields = array('按条件', '短信内容', '发送结果', '手机号', 'IP', '操作人');
		$dfields = array('message', 'message', 'code', 'mobile', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;

		(isset($ip) && is_ip($ip)) or $ip = '';
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
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
		if($mobile) $condition .= " AND mobile='$mobile'";
		if($username) $condition .= " AND editor='$username'";
		if($ip) $condition .= " AND ip='$ip'";

		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}sms WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}sms WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['sendtime'] = timetodate($r['sendtime'], 6);
			$r['num'] = ceil($r['word']/$DT['sms_len']);
			if($r['status'] < 2) {
				$r['status'] = strpos($r['code'], $DT['sms_ok']) === false ? 2 : 3;
				$db->query("UPDATE {$DT_PRE}sms SET status=$r[status] WHERE itemid=$r[itemid]");
			}
			$lists[] = $r;
		}
		include tpl('sendsms_record', $module);
	break;
	default:
		if(!$DT['sms'] || !DT_CLOUD_UID || !DT_CLOUD_KEY) msg('短信发送功能未开启', '?file=setting&tab=8');
		if(isset($send)) {
			if(isset($preview) && $preview) {
				if($sendtype == 2) {
					$mobiles = explode("\n", $mobiles);
					$mobile = trim($mobiles[0]);
				} else if($sendtype == 3) {
					$mobiles = explode("\n", file_get(DT_ROOT.'/file/'.$path.'/'.$list));
					$mobile = trim($mobiles[0]);
				}
				$user = _userinfo($mobile);
				if($user && _safecheck($content)) eval("\$content = \"$content\";");
				exit($content.$sign);
			}
			if($sendtype == 1) {
				$content or msg('请填写短信内容');
				$mobile or msg('请填写接收号码');
				$mobile = trim($mobile);
				$DT['sms_sign'] = $sign;
				if(is_mobile($mobile)) {
					if(strpos($content, '{$') !== false) {
						$user = _userinfo($mobile);
						if($user && _safecheck($content)) eval("\$content = \"$content\";");
					}
					$content = strip_sms($content);
					$code = send_sms($mobile, $content);
					if(strpos($code, $DT['sms_ok']) === false) msg('短信发送失败，'.$code);
				}
				dmsg('短信发送成功', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
			} else if($sendtype == 2) {
				$content or msg('请填写短信内容');
				$mobiles or msg('请填写接收号码');
				$mobiles = explode("\n", $mobiles);
				$_content = $content;
				$DT['sms_sign'] = $sign;
				$s = $f = 0;
				foreach($mobiles as $mobile) {
					$mobile = trim($mobile);
					if(is_mobile($mobile)) {
						$content = $_content;
						if(strpos($content, '{$') !== false) {
							$user = _userinfo($mobile);
							if($user && _safecheck($content)) eval("\$content = \"$content\";");
						}
						$content = strip_sms($content);
						$code = send_sms($mobile, $content);
						if(strpos($code, $DT['sms_ok']) !== false) {
							$s++;
						} else {
							if($s == 0) msg('短信发送失败，'.$code);
							$f++;
						}
					}
				}
				dmsg('发送成功('.$s.'),发送失败('.$f.')', '?moduleid='.$moduleid.'&file='.$file.'&action=record');
			} else if($sendtype == 3) {
				if(isset($id)) {
					$data = cache_read('temp-'.$file.'-'.$_username.'.php');
					$content = $data['content'];
					$list = $data['list'];
					$sign = $data['sign'];
				} else {
					$id = $s = $f = 0;
					$content or msg('请填写短信内容');
					$list or msg('请选择号码列表');
					$data = array();
					$data['list'] = $list;
					$data['content'] = $content;
					$data['sign'] = $sign;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
				$_content = $content;
				$DT['sms_sign'] = $sign;
				$pertime = isset($pertime) ? intval($pertime) : 3;
				if($pertime < 0) $pertime = 3;
				$pernum = isset($pernum) ? intval($pernum) : 5;
				if($pernum < 1) $pernum = 5;
				$mobiles = file_get(DT_ROOT.'/file/'.$path.'/'.$list);
				$mobiles = explode("\n", $mobiles);
				for($i = 1; $i <= $pernum; $i++) {
					$mobile = trim($mobiles[$id++]);
					if(is_mobile($mobile)) {
						$content = $_content;
						if(strpos($content, '{$') !== false) {
							$user = _userinfo($mobile);
							if($user && _safecheck($content)) eval("\$content = \"$content\";");
						}
						$content = strip_sms($content);
						$code = send_sms($mobile, $content);
						if(strpos($code, $DT['sms_ok']) !== false) {
							$s++;
						} else {
							if($s == 0) msg('短信发送失败，'.$code);
							$f++;
						}
					}
				}
				$tt = count($mobiles);
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
					$content = $data['content'];
					$sign = $data['sign'];
				} else {
					$id = $s = $f = 0;
					$content or msg('请填写短信内容');
					$tb or $tb = $DT_PRE.'member';
					$tb = strip_sql($tb, 0);
					$sql or $sql = 'groupid>4';
					$sql = strip_sql($sql, 0);
					$tt = $db->count($tb, $sql);
					$data = array();
					$data['tt'] = $tt;
					$data['tb'] = $tb;
					$data['sql'] = $sql;
					$data['content'] = $content;
					$data['sign'] = $sign;
					cache_write('temp-'.$file.'-'.$_username.'.php', $data);
				}
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
					$mobile = $r['mobile'];
					if(is_mobile($mobile)) {
						$content = $_content;
						if(strpos($content, '{$') !== false) {
							$user = userinfo($r['username']);
							if($user && _safecheck($content)) eval("\$content = \"$content\";");
						}
						$content = strip_sms($content);
						$code = send_sms($mobile, $content);
						if(strpos($code, $DT['sms_ok']) !== false) {
							$s++;
						} else {
							if($s == 0) msg('短信发送失败，'.$code);
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
			isset($mobile) or $mobile = '';
			$mobiles = '';
			if(isset($userid)) {
				if($userid) {
					$userids = is_array($userid) ? implode(',', $userid) : $userid;					
					$result = $db->query("SELECT mobile FROM {$DT_PRE}member WHERE userid IN ($userids)");
					while($r = $db->fetch_array($result)) {
						if($r['mobile']) $mobiles .= $r['mobile']."\n";
					}
				}
			}
			if($mobile) {
				if(strpos($mobile, ',') === false) {
					is_mobile($mobile) or $mobile = '';
				} else {
					$tmp = explode(',', $mobile);
					foreach($tmp as $mob) {
						if(is_mobile($mob)) $mobiles .= $mob."\n";
					}
					$mobile = '';
				}
			}
			if($mobiles && strpos($mobiles, "\n") !== false) $sendtype = 2;
			include tpl('sendsms', $module);
		}
	break;
}
?>