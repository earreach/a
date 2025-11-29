<?php 
defined('IN_DESTOON') or exit('Access Denied');
$DT['im_web'] or dheader('./');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$chatid = (isset($chatid) && is_md5($chatid)) ? $chatid : '';
$table = $DT_PRE.'chat';
$chat_poll = intval($MOD['chat_poll']);
$chat_group = 0;
foreach($MODULE as $m) {
	if($m['module'] == 'club') {
		$chat_group = $m['moduleid'];
		break;
	}
}
$head_title = $L['chat_title'];
if($_userid) {
	//
} else {
	$action = 'guest';
}
switch($action) {
	case 'add':
		$head_title = $L['chat_add'];
	break;
	case 'guest':
		if($DT_PC) login();
	break;
	case 'group':
		$gpid = isset($gid) ? intval($gid) : 0;
		$lists = array();
		foreach($MODULE as $m) {
			if($m['module'] == 'club') {
				$mid = $m['moduleid'];
				$result = $db->query("SELECT * FROM {$DT_PRE}club_fans_{$mid} WHERE status=3 AND username='$_username' ORDER BY addtime DESC LIMIT 100");
				while($r = $db->fetch_array($result)) {
					$gid = $r['gid'];
					$GRP = $db->get_one("SELECT * FROM {$DT_PRE}club_group_{$mid} WHERE itemid=$gid", 'CACHE');
					if($GRP && $GRP['status'] == 3 && $GRP['chat']) {
						$arr = array();
						if($kw && strpos($GRP['title'], $kw) === false) continue;
						$arr['mid'] = $mid;
						$arr['gid'] = $gid;
						$arr['linkurl'] = ($DT_PC ? $m['linkurl'] : $m['mobile']).rewrite('chat'.DT_EXT.'?gid='.$gid);
						$arr['title'] = $GRP['title'];
						$arr['thumb'] = $GRP['thumb'];
						$arr['fans'] = $GRP['fans'];
						$arr['chattime'] = $GRP['chattime'];
						$arr['last'] = timetoread($GRP['chattime'], 'm/d H:i');
						$lists[] = $arr;
					}
				}
			}
		}
		if($mid && $MODULE[$mid]['module'] != 'club') $mid = 0;
		$gid = $gpid;
		$head_title = $L['chat_group'];
	break;
	case 'friend':
		$TP = get_type('friend-'.$_userid);
		$lists = array();
		$condition = "userid=$_userid AND fusername<>''";
		if($keyword) {
			if(is_mobile($keyword)) {
				$condition .= " AND mobile='$keyword'";
			} else if(check_name($keyword)) {				
				$condition .= " AND fusername='$keyword'";
			} else {				
				$condition .= ' AND ('.substr(match_kw('fpassport', $keyword), 5).' OR '.substr(match_kw('truename', $keyword), 5).' OR '.substr(match_kw('alias', $keyword), 5).')';
			}
		}
		$result = $db->query("SELECT * FROM {$DT_PRE}friend WHERE {$condition} ORDER BY listorder DESC,itemid DESC LIMIT 500");
		while($r = $db->fetch_array($result)) {
			$typeid = $r['typeid'];
			if(!isset($TP[$typeid])) $typeid = 0;
			$arr = array();
			$arr['linkurl'] = 'chat'.DT_EXT.'?touser='.$r['fusername'];
			$arr['name'] = $r['fpassport'] ? $r['fpassport'] : $r['fusername'];
			if($r['alias']) $arr['name'] = $r['alias'];
			$arr['username'] = $r['fusername'];
			$arr['note'] = $r['note'] ? $r['note'] : $r['company'];
			$lists[$typeid][] = $arr;
		}
		$TYPE = array();
		if(isset($lists[0])) $TYPE[0] = array('typeid'=>0, 'typename'=>$L['chat_friend'], 'num'=>count($lists[0]));
		foreach($TP as $k=>$v) {
			if(isset($lists[$k])) {
				$v['num'] = count($lists[$k]);
				$TYPE[$k] = $v;
			}
		}
		$head_title = $L['chat_friend'];
	break;
	case 'view':
		$admin = 0;
		if($mid) {
			$gid = isset($gid) ? intval($gid) : 0;
			($MODULE[$mid]['module'] == 'club' && $gid) or dheader('?action=index');
			$table_fans = $DT_PRE.'club_fans_'.$mid;
			$table_group = $DT_PRE.'club_group_'.$mid;
			$chat = $db->get_one("SELECT * FROM {$table_fans} WHERE username='$_username' AND gid=$gid AND status=3");
			$chat or dheader('?action=index');
			$chatid = md5($gid.'@'.$mid);
			$table = get_chat_tb($chatid);
			require DT_ROOT.'/module/club/global.func.php';
			$GRP = get_group($gid);
			$admin = is_admin($GRP);
			if($admin) {
				if($itemid) {
					$chat = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
					if($chat && $chat['chatid'] == $chatid) {
						if($job == 'ban') {
							$db->query("UPDATE {$table_fans} SET ban=1 WHERE gid=$gid AND username='$chat[username]'");
							dmsg($L['op_success'], $forward);
						} else if($job == 'del') {
							$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
							dmsg($L['op_del_success'], $forward);
						}
					}
				}
			}
		} else {
			$chatid or dheader('?action=index');
			$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
			($chat && ($chat['fromuser'] == $_username || $chat['touser'] == $_username)) or dheader('?action=index');
			$table = get_chat_tb($chatid);
		}
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$condition = "chatid='$chatid'";
		if($keyword) $condition .= match_kw('content', $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($username) $condition .= " AND username='$username'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$word = $r['content'];
			if($MOD['chat_url'] || $MOD['chat_img']) {
				if(preg_match_all("/([http|https]+)\:\/\/([a-z0-9\/\-\_\.\,\?\&\#\=\%\+\;]{4,})/i", $word, $m)) {
					foreach($m[0] as $u) {
						if($MOD['chat_img'] && preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
							$word = str_replace($u, '<img src="'.$u.'" onload="if(this.width>320)this.width=320;" onclick="window.open(this.src);"/>', $word);
						} else if($MOD['chat_img'] && preg_match("/^(mp4)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
							$word = str_replace($u, '<video src="'.$u.'" width="200" height="150" controls="controls"></video>', $word);
						} else if($MOD['chat_url']) {
							$word = str_replace($u, '<a href="'.$u.'" target="_blank">'.$u.'</a>', $word);
						}
					}
				}
			}			
			if(strpos($word, ')') !== false) $word = parse_face($word);			
			if(strpos($word, '[emoji]') !== false) $word = emoji_decode($word);
			$r['word'] = $word;
			$r['date'] = timetoread($r['addtime'], 6);
			$lists[] = $r;
		}
	break;
	case 'list':
		$data = '';
		$new = 0;
		$result = $db->query("SELECT * FROM {$table} WHERE fromuser='$_username' OR touser='$_username' ORDER BY lasttime DESC LIMIT 100");
		while($r = $db->fetch_array($result)) {
			if($r['fromuser'] == $_username) {
				$r['username'] = $r['touser'];
				$r['user'] = $r['tpassport'] ? $r['tpassport'] : $r['touser'];
				$r['name'] = $r['talias'] ? $r['talias'] : $r['user'];
				$r['new'] = $r['fnew'];
			} else {
				$r['username'] = $r['fromuser'];
				$r['user'] = $r['fpassport'] ? $r['fpassport'] : $r['fromuser'];
				$r['name'] = $r['falias'] ? $r['falias'] : $r['user'];
				$r['new'] = $r['tnew'];
			}
			$new += $r['new'];
			if($r['new'] > 99) $r['new'] = 99;
			$r['last'] = timetoread($r['lasttime'], $r['lasttime'] > $DT_TODAY - 86400 ? 'H:i:s' : 'y/m/d');
			$r['online'] = online($r['username'], 1);
			if($DT_PC) {
				$data .= '<table cellpadding="0" cellspacing="0" onclick="Schat(\'chat'.DT_EXT.'?chatid='.$r['chatid'].'\');" id="chat-'.$r['chatid'].'" uid="'.$r['user'].'"><tr><td width="60">';
				$data .= '<img src="'.useravatar($r['username']).'" class="'.($r['online'] ? 'chat_onl' : 'chat_off').'"/>';
				$data .= '</td><td><ul>';
				$data .= '<li><span>'.$r['last'].'</span><b>'.$r['name'].'</b></li>';
				$data .= '<li>'.($r['new'] ? '<em>'.$r['new'].'</em>' : '').($r['online'] ? $L['chat_online'] : $L['chat_offline']).' '.$r['lastmsg'].'</li>';
				$data .= '</ul></td></tr></table>';
			} else {
				$data .= '<div class="list-img list-chat">';
				$data .= '<a href="chat'.DT_EXT.'?chatid='.$r['chatid'].'"><img src="'.useravatar($r['username']).'" class="'.($r['online'] ? 'chat_onl' : 'chat_off').'"/></a><ul>';
				$data .= '<li><span class="f_r">'.$r['last'].'</span><a href="chat'.DT_EXT.'?chatid='.$r['chatid'].'"><strong>'.$r['name'].'</strong></a></li>';
				$data .= '<li>'.($r['new'] ? '<em>'.$r['new'].'</em>' : '').'<span>'.$r['lastmsg'].'</span></li>';
				$data .= '</ul></div>';
			}
		}
		if($new != $_chat) {
			$db->query("UPDATE {$DT_PRE}member SET chat=$new WHERE userid=$_userid");
			$_chat = $new;
		}
		if(!$data) $data = '<div style="padding:100px 0;text-align:center;">'.$L['chat_empty'].'</div>';
		exit($data);
	break;
	default:
		$chatid = (isset($chatid) && is_md5($chatid)) ? $chatid : '';
		$touser = (isset($touser) && check_name($touser)) ? $touser : '';
		if(!$DT_PC) {
			if($touser) dheader('chat'.DT_EXT.'?touser='.$touser);
			if($chatid) dheader('chat'.DT_EXT.'?chatid='.$chatid);
		}
	break;
}
if($DT_PC) {
	//
} else {
	$foot = 'message';
	if($action == 'view') $pages = mobile_pages($items, $page, $pagesize);
	$head_name = $head_title;
	if($sns_app) $seo_title = '';
}
include template('im', $module);
?>