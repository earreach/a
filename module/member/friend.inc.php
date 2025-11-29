<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['friend_limit'] > -1 or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/include/post.func.php';
$TYPE = get_type('friend-'.$_userid);
require DT_ROOT.'/module/'.$module.'/friend.class.php';
$do = new friend();
isset($username) && check_name($username) or $username = '';
switch($action) {
	case 'add':
		if(isset($auth)) {
			$str = decrypt($auth, DT_KEY.'FRIEND');
			if(strpos($str, '|') !== false) {
				list($job, $username, $fusername, $time) = explode('|', $str);
				if(DT_TIME - $time > 86400*3 || DT_TIME < $time || $username != $_username || !check_name($fusername)) message($L['friend_pass_auth'], '?action=index');
				$user = userinfo($fusername);
				($user && !in_array($user['groupid'], array(2, 4))) or message($L['friend_pass_member'], '?action=index');
				if($job == 'add') {//直接添加
					$post = array();
					$post['userid'] = $_userid;
					$post['username'] = $_username;
					$post['fuserid'] = $user['userid'];
					$post['fusername'] = $user['username'];
					$post['fpassport'] = $user['passport'];
					if($do->pass($post)) {
						$fid = $do->add($post);
						if($fid) {
							$t0 = lang($L['friend_msg_t0'], array($_passport));
							$content = $L['friend_msg_member'].' <a href="'.userurl($_username, 'file=space').'" class="b" target="_blank">'.$_passport.'</a> '.$L['friend_msg_at'].' '.timetodate(DT_TIME, 5).' '.$L['friend_msg_c0'].'<br/><br/>';
							send_message($user['username'], $t0, $content);
							dmsg($L['friend_msg_success'], '?action=show&itemid='.$fid);
						}
					}
					message($do->errmsg, '?action=index');
				} else if($job == 'agree') {//互相添加
					$do->itemid = 0;
					$post = array();
					$post['userid'] = $user['userid'];
					$post['username'] = $user['username'];
					$post['fuserid'] = $_userid;
					$post['fusername'] = $_username;
					$post['fpassport'] = $_passport;
					if($do->pass($post)) $do->add($post);
					$do->itemid = 0;
					$post = array();
					$post['userid'] = $_userid;
					$post['username'] = $_username;
					$post['fuserid'] = $user['userid'];
					$post['fusername'] = $user['username'];
					$post['fpassport'] = $user['passport'];
					if($do->pass($post)) {
						$fid = $do->add($post);
						if($fid) {
							$t1 = lang($L['friend_msg_t1'], array($_passport));
							$content = $L['friend_msg_member'].' <a href="'.userurl($_username, 'file=space').'" class="b" target="_blank">'.$_passport.'</a> '.$L['friend_msg_at'].' '.timetodate(DT_TIME, 5).' '.$L['friend_msg_c1'].'<br/><br/>';
							send_message($user['username'], $t1, $content);
							dmsg($L['friend_msg_success'], '?action=show&itemid='.$fid);
						}
					}
					message($do->errmsg, '?action=index');
				} else if($job == 'refuse') {//拒绝请求
					isset($reason) or dheader('?action=refuse&username='.$fusername.'&auth='.$auth);
					$t2 = lang($L['friend_msg_t2'], array($_passport));
					$content = $L['friend_msg_member'].' <a href="'.userurl($_username, 'file=space').'" class="b" target="_blank">'.$_passport.'</a> '.$L['friend_msg_at'].' '.timetodate(DT_TIME, 5).' '.$L['friend_msg_c2'].'<br/><br/>'.($reason ? $L['friend_msg_r1'].$reason.'<br/><br/>' : '');
					send_message($user['username'], $t2, $content);
					dmsg($L['friend_msg_reject'], ($forward && strpos($forward, 'friend') === false) ? $forward : '?action=index');
				} else {
					message($L['friend_pass_auth'], '?action=index');
				}
			}
		} else {
			$username or dheader('?action=find');
			if($username == $_username) message($L['friend_pass_self'], '?action=find');
			if($MG['friend_limit']) {
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}friend WHERE userid=$_userid");
				if($r['num'] >= $MG['friend_limit']) message(lang($L['limit_add'], array($MG['friend_limit'], $r['num'])), '?action=index');
			}
			$user = userinfo($username);
			($user && !in_array($user['groupid'], array(2, 4))) or dheader('?action=find');
			if($user['verify'] == 2 || blacked($username)) message($L['friend_pass_reject'], '?action=find', 6);
			$fid = $do->friended($username);
			if($fid) message($L['friend_pass_again'], '?action=show&itemid='.$fid);
			if($user['verify'] == 1) {//允许任何人
				$post = array();
				$post['userid'] = $_userid;
				$post['username'] = $_username;
				$post['fuserid'] = $user['userid'];
				$post['fusername'] = $user['username'];
				$post['fpassport'] = $user['passport'];
				if($do->pass($post)) {
					$fid = $do->add($post);
					if($fid) {
						$t3 = lang($L['friend_msg_t3'], array($_passport));
						$content = $L['friend_msg_member'].' <a href="'.userurl($_username, 'file=space').'" class="b" target="_blank">'.$_passport.'</a> '.$L['friend_msg_at'].' '.timetodate(DT_TIME, 5).' '.$L['friend_msg_c3'].'<br/><br/>'.$L['friend_msg_do'].' <a href="'.$MOD['linkurl'].'friend'.DT_EXT.'?action=add&auth='.encrypt('add|'.$username.'|'.$_username.'|'.DT_TIME , DT_KEY.'FRIEND').'" target="_blank" class="b">'.$L['friend_msg_d1'].'</a> '.$L['friend_msg_or'].' <a href="'.$MOD['linkurl'].'friend'.DT_EXT.'?action=black&username='.$username.'" target="_blank" class="b">'.$L['friend_msg_d3'].'</a><br/><br/>';
						send_message($user['username'], $t3, $content);
						dmsg($L['friend_msg_success'], '?action=show&itemid='.$fid);
					}
				}
				message($do->errmsg, '?action=index');
			}
			//需要我审核
			isset($reason) or dheader('?action=apply&username='.$username);
			$reason = strip_tags(trim($reason));
			$t4 = lang($L['friend_msg_t4'], array($_passport));
			$content = $L['friend_msg_member'].' <a href="'.userurl($_username, 'file=space').'" class="b" target="_blank">'.$_passport.'</a> '.$L['friend_msg_at'].' '.timetodate(DT_TIME, 5).' '.$L['friend_msg_c4'].'<br/><br/>'.$L['friend_msg_r2'].($reason ? $reason : $L['friend_msg_r0']).'<br/><br/>'.$L['friend_msg_can'].' <a href="'.$MOD['linkurl'].'friend'.DT_EXT.'?action=add&auth='.encrypt('agree|'.$username.'|'.$_username.'|'.DT_TIME , DT_KEY.'FRIEND').'" target="_blank" class="b">'.$L['friend_msg_d3'].'</a> '.$L['friend_msg_or'].' <a href="'.$MOD['linkurl'].'friend'.DT_EXT.'?action=add&auth='.encrypt('refuse|'.$username.'|'.$_username.'|'.DT_TIME , DT_KEY.'FRIEND').'" target="_blank" class="b">'.$L['friend_msg_d4'].'</a> '.$L['friend_msg_or'].' '.$L['friend_msg_d0'].'<br/><br/>';
			send_message($user['username'], $t4, $content);
			message($L['friend_msg_check'], ($forward && strpos($forward, 'friend') === false) ? $forward : '?action=index', 5);
		}
	break;
	case 'find':
		$lists = array();
		if($keyword) {
			$condition = "groupid>4";
			if(is_mobile($kw)) {
				$condition .= " AND mobile='$kw' AND vmobile=1 AND fmobile=1";
			} else if(is_email($kw)) {
				$condition .= " AND email='$kw' AND vemail=1 AND femail=1";
			} else if(check_name($kw)) {
				$condition .= " AND username='$kw'";
			} else {
				$condition .= match_kw('passport', $keyword);
			}
			$result = $db->query("SELECT username,passport,validate,sign,fans,follows FROM {$DT_PRE}member WHERE {$condition} ORDER BY validate DESC,userid ASC LIMIT 10");
			while($r = $db->fetch_array($result)) {
				if($r['username'] == $_username) continue;
				$lists[] = $r;
			}
		}
		$head_title = $L['friend_title_find'];
	break;
	case 'apply':
		$username or dheader('?action=find');
		$user = userinfo($username);
		($user && !in_array($user['groupid'], array(2, 4))) or message($L['friend_pass_member'], '?action=index');
		$head_title = $L['friend_title_apply'];
	break;
	case 'refuse':
		isset($auth) or dheader('?action=index');
		$username or dheader('?action=index');
		$user = userinfo($username);
		($user && !in_array($user['groupid'], array(2, 4))) or message($L['friend_pass_member'], '?action=index');
		$head_title = $L['friend_title_refuse'];
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['userid'] != $_userid) message();
		if($submit) {
			if($do->pass($post)) {
				$post['username'] = $_username;
				$do->edit($post);
				dmsg($L['op_edit_success'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			extract($r);
			$user = $fusername ? userinfo($fusername) : array();
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type'], $typeid);
			$head_title = $L['friend_title_edit'];
		}
	break;
	case 'show':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['userid'] != $_userid) message();
		extract($r);
		$url = $homepage ? gourl($homepage) : '';
		$user = $fusername ? userinfo($fusername) : array();
		$head_title = $L['friend_title_show'];
	break;
	case 'delete':
		$itemid or message($L['friend_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if(!$item || $item['userid'] != $_userid) message();
			$do->delete($itemid);
			if($job == 'black') black_add($item['fusername'], $L['friend_title_black']);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'note':
		if(isset($post) && is_array($post)) {
			foreach($post as $k=>$v) {
				$itemid = intval($k);
				$note = strip_tags(trim($v['note']));
				$r = $db->get_one("SELECT * FROM {$DT_PRE}friend WHERE itemid=$itemid");
				if($r['username'] == $_username) $db->query("UPDATE {$DT_PRE}friend SET note='$note' WHERE itemid=$itemid");
			}
		}		
		dmsg($L['op_update_success'], $forward);
	break;
	case 'reason':
		if(isset($post) && is_array($post)) {
			foreach($post as $k=>$v) {
				$itemid = intval($k);
				$note = strip_tags(trim($v['note']));
				$r = $db->get_one("SELECT * FROM {$DT_PRE}member_blacklist WHERE itemid=$itemid");
				if($r['username'] == $_username) $db->query("UPDATE {$DT_PRE}member_blacklist SET note='$note' WHERE itemid=$itemid");
			}
		}		
		dmsg($L['op_update_success'], $forward);
	break;
	case 'black':
		(isset($username) && check_name($username)) or $username = '';
		$username or message($L['friend_pass_member'] );
		if($username == $_username) message($L['friend_pass_black_self']);
		isset($note) or $note = '';
		if(black_add($username, $note)) dmsg($L['op_success'], $forward);
		message($L['friend_msg_black_fail']);
	break;
	case 'remove':
		$itemid or message($L['friend_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$r = $db->get_one("SELECT * FROM {$DT_PRE}member_blacklist WHERE itemid=$itemid");
			if($r['username'] == $_username) $db->query("DELETE FROM {$DT_PRE}member_blacklist WHERE itemid=$itemid");
		}		
		dmsg($L['op_del_success'], $forward);
	break;
	case 'list':
		$sfields = $L['friend_sfields'];
		$dfields = array('fpassport', 'truename', 'alias', 'company', 'career', 'telephone', 'mobile', 'homepage', 'email', 'qq', 'wx', 'ali', 'skype', 'fusername', 'fpassport', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		$lists = $do->get_list_black($condition, 'itemid DESC');
		$lists = list_user($lists, 'userid,fans,follows,sign,validate', 'buserid');
		$head_title = $L['friend_title_list'];
	break;
	default:
		$sfields = $L['friend_sfields'];
		$dfields = array('company', 'truename', 'alias', 'company', 'career', 'telephone', 'mobile', 'homepage', 'email', 'qq', 'wx', 'ali', 'skype', 'username', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);
		$condition = "userid=$_userid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		$lists = $do->get_list($condition, 'listorder DESC,itemid DESC');
		$lists = list_user($lists, 'userid,fans,follows,sign,validate', 'fuserid');
		if($MG['friend_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}friend WHERE userid=$_userid");
			$limit_used = $r['num'];
			$limit_free = $MG['friend_limit'] > $limit_used ? $MG['friend_limit'] - $limit_used : 0;
		}
		$head_title = $L['friend_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if($action == 'add' || $action == 'edit' || $action == 'show' || $action == 'find' || $action == 'apply' || $action == 'refuse') {
		//
	} else {
		$pages = mobile_pages($items, $page, $pagesize);
	}	
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('friend', $module);
?>