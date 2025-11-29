<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$DT['im_web'] or dheader('./');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$chatid = (isset($chatid) && is_md5($chatid)) ? $chatid : '';
$table = $DT_PRE.'chat';
$chat_poll = intval($MOD['chat_poll']);
function emoji_encode($str){
    $string = '';
    $length = mb_strlen($str, DT_CHARSET);
    for($i = 0; $i < $length; $i++) {
        $tmp = mb_substr($str, $i, 1, DT_CHARSET);    
        if(strlen($tmp) >= 4) {
            $string .= '[emoji]'.rawurlencode($tmp).'[/emoji]';
        } else {
            $string .= $tmp;
        }
    }
    return $string;
}

function get_alias($userid, $username, $type = 'alias') {
	$t = DB::get_one("SELECT alias FROM ".DT_PRE."friend WHERE userid=$userid AND fusername='$username'", 'CACHE');
	return $t ? ($type ? $t[$type] : $t) : '';
}

switch($action) {
	case 'send':		
		$chatid or exit('ko');
		trim($word) or exit('ko');
		if($MOD['chat_maxlen'] && strlen($word) > $MOD['chat_maxlen']*3) exit('max');
		$BANWORD = cache_read('banword.php');
		if($BANWORD) {
			$tmp = banword($BANWORD, $word, 2);
			if(is_array($tmp)) exit('bad');
			$word = $tmp;
		}
		if($DT['spam_appcode'] && cloud_spam($word, $DT['spam_appcode'], 2)) exit('bad');
		$word = stripslashes(trim($word));
		$word = strip_tags($word);
		$word = dsafe($word);
		$word = nl2br($word);
		$word = strip_nr($word);
		if(!$DT_PC) $word = emoji_encode($word);
		$word = str_replace('|', ' ', $word);
		if($MOD['chat_file'] && $MG['upload']) clear_upload($word, $_userid, $table);
		$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
		if($chat) {
			$lastmsg = strip_tags($word);
			if(!$DT_PC) $lastmsg = preg_replace('/\[emoji\](.+?)\[\/emoji\]/', "(:emoji:)", $lastmsg);
			$lastmsg = dsubstr($lastmsg, 50);
			$lastmsg = addslashes($lastmsg);
			if($chat['touser'] == $_username) {
				$sql = "fgettime=$DT_TIME,lasttime=$DT_TIME,lastmsg='$lastmsg'";
				if($DT_TIME - $chat['freadtime'] > $chat_poll) {
					$db->query("UPDATE {$DT_PRE}member SET chat=chat+1 WHERE username='$chat[fromuser]'");
					$sql .= ",fnew=fnew+1";
				}
				$tchild = $_cid ? $_child['nickname'] : '';
				$sql .= ",tchild='$tchild'";
				$db->query("UPDATE {$table} SET {$sql} WHERE chatid='$chatid'");
			} else if($chat['fromuser'] == $_username) {
				$sql = "tgettime=$DT_TIME,lasttime=$DT_TIME,lastmsg='$lastmsg'";
				if($DT_TIME - $chat['treadtime'] > $chat_poll) {
					$db->query("UPDATE {$DT_PRE}member SET chat=chat+1 WHERE username='$chat[touser]'");
					$sql .= ",tnew=tnew+1";
				}
				$fchild = $_cid ? $_child['nickname'] : '';
				$sql .= ",fchild='$fchild'";
				$db->query("UPDATE {$table} SET {$sql} WHERE chatid='$chatid'");
			} else {
				exit('ko');
			}
		} else {
			exit('ko');
		}
		$font_s = $font_s ? intval($font_s) : 0;
		$font_c = $font_c ? intval($font_c) : 0;
		$font_b = $font_b ? 1 : 0;
		$font_i = $font_i ? 1 : 0;
		$font_u = $font_u ? 1 : 0;
		$css = '';
		if($font_s) $css .= ' s'.$font_s;
		if($font_c) $css .= ' c'.$font_c;
		if($font_b) $css .= ' fb';
		if($font_i) $css .= ' fi';
		if($font_u) $css .= ' fu';
		if($css) $word = '<span class="'.trim($css).'">'.$word.'</span>';
		if($word) {
			$content = addslashes($word);
			$nickname = $_cid ? addslashes($_child['nickname']) : '';
			$db->query("INSERT INTO ".get_chat_tb($chatid)." (chatid,username,passport,nickname,addtime,content) VALUES ('$chatid','$_username','$_passport','$nickname','$DT_TIME','$content')");
			exit('ok');
		}
		exit('ko');
	break;
	case 'load':
		$chatid or exit;
		$tb = get_chat_tb($chatid);
		$chat_nick = '';
		$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
		if($chat) {
			if($chat['touser'] == $_username) {
				$chat_nick = $chat['fchild'];
				$db->query("UPDATE {$table} SET treadtime=$DT_TIME,tnew=0 WHERE chatid='$chatid'");
			} else if($chat['fromuser'] == $_username) {
				$chat_nick = $chat['tchild'];
				$db->query("UPDATE {$table} SET freadtime=$DT_TIME,fnew=0 WHERE chatid='$chatid'");
				if($DT_TIME - $chat['lasttime'] > 86400*7) {
					$r = $db->get_one("SELECT reply FROM {$DT_PRE}member_misc WHERE username='$chat[touser]'");
					if($r['reply']) {
						$content = addslashes(nl2br($r['reply']));
						$time = $DT_TIME + 10;
						$db->query("INSERT INTO {$tb} (chatid,username,passport,addtime,content) VALUES ('$chatid','$chat[touser]','$chat[tpassport]','$time','$content')");
						$db->query("UPDATE {$table} SET lasttime=$time WHERE chatid='$chatid'");
					}
				}
			} else {
				exit();
			}
		} else {
			exit();
		}
		$chatlast = $_chatlast = isset($chatlast) ? intval($chatlast) : 0;
		$first = isset($first) ? intval($first) : 0;
		$i = $j = 0;
		$chat_lastuser = '';
		$chat_repeat = 0;
		$json = '';
		$time1 = 0;
		if($chatlast < 1 || $chat['lasttime'] > $chatlast) {
			if($chatlast < 1) {				
				$result = $db->query("SELECT addtime FROM {$tb} WHERE chatid='$chatid' ORDER BY addtime DESC LIMIT $pagesize");
				while($r = $db->fetch_array($result)) {
					$chatlast = $r['addtime'];
				}
				if($chatlast > 1) $chatlast--;
			}
			$result = $db->query("SELECT itemid,addtime,username,passport,nickname,content FROM {$tb} WHERE chatid='$chatid' AND addtime>$chatlast ORDER BY addtime ASC LIMIT $pagesize");
			while($r = $db->fetch_array($result)) {
				$id = $r['itemid'];
				$time = $r['addtime'];
				$name = $r['username'];
				$word = $r['content'];
				if($_username == $name) { $chat_repeat++; } else { $chat_repeat = 0; }
				$chat_lastuser = $name;
				$chatlast = $time;
				$time2 = $time;
				if($time2 - $time1 < 300 || DT_TIME - $time < 300) {
					$date = '';
				} else {
					$date = '<span title=\"'.timetodate($time2, 5).'\">'.timetoread($time2, 5).'</span>';
					$time1 = $time2;
				}
				if($MOD['chat_url'] || $MOD['chat_img']) {
					if(preg_match_all("/([http|https]+)\:\/\/([a-z0-9\/\-\_\.\,\?\&\#\=\%\+\;]{4,})/i", $word, $m)) {
						foreach($m[0] as $u) {
							if($MOD['chat_img'] && preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
								$word = str_replace($u, '<img src="'.$u.'" onload="if(this.width>320)this.width=320;" onclick="'.($DT_PC ? 'window.open(this.src)' : 'chat_view(this.src)').';"/>', $word);
							} else if($MOD['chat_img'] && preg_match("/^(mp4)$/i", file_ext($u)) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
								$word = str_replace($u, '<video src="'.$u.'" width="320" height="180" controls="controls"></video>', $word);
							} else if($MOD['chat_url']) {
								$word = str_replace($u, '<a href="'.$u.'" target="_blank">'.$u.'</a>', $word);
							}
						}
					}
				}				
				if(strpos($word, ')') !== false) $word = parse_face($word);
				if(strpos($word, '[emoji]') !== false) $word = emoji_decode($word);
				$word = str_replace(array('"', "\r", "\n"), array('\"', "\\r", "\\n"), $word);
				$self = $_username == $name ? 1 : 0;
				if($self) {
					//$name = 'Me';
				} else {
					$j++;
				}
				$json .= ($i ? ',' : '').'{id:"'.$id.'",time:"'.$time.'",date:"'.$date.'",name:"'.$name.'",word:"'.$word.'",self:"'.$self.'"}';
				$i = 1;
			}
		}
		if($_chatlast == 0) $j = 0;
		$json = '{chat_msg:['.$json.'],chat_new:"'.$j.'",chat_last:"'.$chatlast.'",chat_nick:"'.$chat_nick.'"}';
		exit($json);
	break;
	case 'down':
		if($data && check_name($username) && is_md5($chatid)) {
			$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
			if($chat['fromuser'] == $_username) {
				$chat['touser'] == $username or exit;
			} else {
				$chat['fromuser'] == $username or exit;
			}
			$data = stripslashes(dsafe($data));
			$css = file_get(DT_ROOT.'/static/member/chat.css');
			$css = str_replace('#chat {width:auto;height:366px;overflow:auto;', '#chat {width:700px;margin:auto;', $css);
			$css = str_replace('margin:100px 0 0 0;', 'margin:0;', $css);
			$css = str_replace("url('", "url('".DT_STATIC."member/", $css);
			$data = str_replace('<i></i>', '', $data);
			$data = '<!DOCTYPE html><html><head><meta charset="'.DT_CHARSET.'"/><title>'.lang($L['chat_record'], array($username)).'</title><style type="text/css">'.$css.'</style><base href="'.$MOD['linkurl'].'"/></head><body><div id="chat">'.$data.'</div></body></html>';
			file_down('', 'chat-'.$username.'-'.timetodate($DT_TIME, 'YmdHi').'.html', $data);
		}
		exit;
	break;
	default:
		$item = array();
		if(isset($touser)) {
			if($touser == $_username) dalert($L['chat_msg_self'], $DT_PC ? 'close' : 'im.php');
			$MG['chat'] or dalert($L['chat_msg_no_rights'], $DT_PC ? 'close' : 'grade.php');
			$find = 0;
			if(is_mobile($touser)) {
				$r = DB::get_one("SELECT fusername FROM {$DT_PRE}friend WHERE mobile='$touser' AND userid=$_userid");
				if($r && check_name($r['fusername'])) $touser = $r['fusername'];
				$find = 1;
			} else if(check_name($touser)) {
				//
			} else if(is_passport($touser)) {
				$r = DB::get_one("SELECT fusername FROM {$DT_PRE}friend WHERE (alias='$touser' OR truename='$touser' OR fpassport='$touser') AND userid=$_userid");
				if($r && check_name($r['fusername'])) $touser = $r['fusername'];
				$find = 1;
			}
			$member = userinfo($touser);
			$member or dalert(($find ? $L['chat_msg_friend'] : $L['chat_msg_user']), $DT_PC ? 'close' : 'im.php');
			$member['alias'] = '';
			$chatid = get_chat_id($_username, $touser);
			$chat_id = $chatid;
			$online = online($member['userid']);
			$head_title = lang($L['chat_with'], array($member['company']));
			$forward = is_url($forward) ? addslashes(dhtmlspecialchars($forward)) : '';
			if(strpos($forward, $MOD['linkurl']) !== false) $forward = '';
			$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
			if($chat) {
				$sql = '';
				if($chat['talias']) {
					$member['alias'] = $chat['talias'];
				} else {
					$chat['talias'] = $member['alias'] = get_alias($_userid, $member['username']);
					if($member['alias']) $sql .= ",talias='".addslashes($member['alias'])."'";
				}
				if($forward != addslashes($chat['forward'])) $sql .= ",forward='$forward'";
				if($sql) $db->query("UPDATE {$table} SET ".substr($sql, 1)." WHERE chatid='$chatid'");
			} else {
				$falias = get_alias($member['userid'], $_username);
				$talias = get_alias($_userid, $member['username']);
				$member['alias'] = $talias;
				$db->query("INSERT INTO {$table} (chatid,fromuser,fpassport,falias,touser,tpassport,talias,tgettime,forward) VALUES ('$chat_id','$_username','$_passport','".addslashes($falias)."','$touser','$member[passport]','".addslashes($member['alias'])."','0','$forward')");
			}
			$type = 1;
			if($mid > 4 && $itemid) {
				$r = DB::get_one("SELECT * FROM ".get_table($mid)." WHERE itemid=$itemid");
				if($r && $r['status'] > 2 && $touser==$r['username']) {
					if(strpos($r['linkurl'], '://') == false) $r['linkurl'] = $MODULE[$mid]['linkurl'].$r['linkurl'];
					$r['price'] = isset($r['price']) ? $r['price'] : 0;
					$item = $r;
				}
			}
		} else if(isset($chatid) && is_md5($chatid)) {
			$chat = $db->get_one("SELECT * FROM {$table} WHERE chatid='$chatid'");
			if($chat && ($chat['touser'] == $_username || $chat['fromuser'] == $_username)) {
				if($chat['touser'] == $_username) {
					$member = userinfo($chat['fromuser']);
					if($chat['falias']) {
						$member['alias'] = $chat['falias'];
					} else {
						$chat['falias'] = $member['alias'] = get_alias($member['userid'], $_username);
						if($member['alias']) $db->query("UPDATE {$table} SET falias='".addslashes($member['alias'])."' WHERE chatid='$chatid'");
					}
				} else if($chat['fromuser'] == $_username) {
					$member = userinfo($chat['touser']);
					if($chat['talias']) {
						$member['alias'] = $chat['talias'];
					} else {
						$chat['talias'] = $member['alias'] = get_alias($_userid, $member['username']);
						if($member['alias']) $db->query("UPDATE {$table} SET talias='".addslashes($member['alias'])."' WHERE chatid='$chatid'");
					}
				}
				$online = online($member['userid']);
				$chat_id = $chatid;
				$head_title = lang($L['chat_with'], array($member['company']));
			} else {
				dheader('im.php');
			}
			$type = 2;
		} else {
			dheader('im.php');
		}
		$member['blacked'] = blacked($member['username'], $_username) ? 1 : 0;
		$member['friended'] = $member['alias'] ? 1 : 0;
		$username = $member['username'];
		include DT_ROOT.'/file/config/face.inc.php';
		/*
		$t = get_alias($_userid, $member['username'], '');
		if($t) {
			$member['alias'] = $t['alias'];
			$member['friended'] = 1;
		} else {
			$member['alias'] = '';
			$member['friended'] = 0;
		}
		*/
	break;
}
if($DT_PC) {
	//
} else {
	$js_pull = 0;
	$head_name = $head_title;	
	if($sns_app) $seo_title = $L['chat_title'];
}
include template('chat', $module);
?>