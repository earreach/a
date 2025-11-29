<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/module/member/global.func.php';
require DT_ROOT.'/include/post.func.php';
$gid = isset($gid) ? intval($gid) : 0;
($MOD['chat'] && $gid) or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
$GRP = get_group($gid);
($GRP && $GRP['status'] == 3 && $GRP['chat']) or dheader($DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
$is_fans = is_fans($GRP);
$is_fans or dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).$DT['file_my'].'?mid='.$moduleid.'&job=join&action=add&gid='.$gid);
$chatid = md5($gid.'@'.$moduleid);
$chat_poll = intval($MOD['chat_poll']);
$chat_ban = is_array($is_fans) ? $is_fans['ban'] : 0;
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

switch($action) {
	case 'send':
		if($chat_ban) exit('ban');
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
		$db->query("UPDATE {$table_group} SET chattime={$DT_TIME} WHERE itemid=$gid");
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
		$chatlast = $_chatlast = isset($chatlast) ? intval($chatlast) : 0;
		$first = isset($first) ? intval($first) : 0;
		$i = $j = 0;
		$chat_lastuser = '';
		$chat_repeat = 0;
		$json = '';
		$time1 = 0;
		if($chatlast < 1 || $GRP['chattime'] > $chatlast) {
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
				$nick = $r['nickname'] ? $r['nickname'] : ($r['passport'] ? $r['passport'] : $r['username']);
				$head = useravatar($name);
				$home = userurl($name, 'file=space');
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
								$word = str_replace($u, '<video src="'.$u.'" width="200" height="150" controls="controls"></video>', $word);
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
				$json .= ($i ? ',' : '').'{id:"'.$id.'",time:"'.$time.'",date:"'.$date.'",name:"'.$name.'",nick:"'.$nick.'",head:"'.$head.'",home:"'.$home.'",word:"'.$word.'",self:"'.$self.'"}';
				$i = 1;
			}
		}
		if($_chatlast == 0) $j = 0;
		$json = '{chat_msg:['.$json.'],chat_new:"'.$j.'",chat_last:"'.$chatlast.'"}';
		exit($json);
	break;
	case 'down':
		if($data && is_md5($chatid)) {
			$data = stripslashes(dsafe($data));
			$css = file_get(DT_ROOT.'/static/member/chat.css');
			$css = str_replace('#chat {width:auto;height:366px;overflow:auto;', '#chat {width:700px;margin:auto;', $css);
			$css = str_replace('margin:100px 0 0 0;', 'margin:0;', $css);
			$css = str_replace("url('", "url('".DT_STATIC."member/", $css);
			$data = str_replace('<i></i>', '', $data);
			$data = '<!DOCTYPE html><html><head><meta charset="'.DT_CHARSET.'"/><title>'.$GRP['title'].'</title><style type="text/css">'.$css.'</style><base href="'.$MODULE[2]['linkurl'].'"/></head><body><div id="chat">'.$data.'</div></body></html>';
			file_down('', 'chat-'.$gid.'-'.timetodate($DT_TIME, 'YmdHi').'.html', $data);
		}
		exit;
	break;
	default:
		include DT_ROOT.'/file/config/face.inc.php';
		$lists = array();
		$condition = "gid=$gid AND status=3";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_fans} WHERE {$condition}");
		$items = $r['num'];
		$pages = pages($items, $page, $pagesize);
		$result = $db->query("SELECT * FROM {$table_fans} WHERE {$condition} ORDER BY addtime ASC LIMIT 30");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 'Y/m/d H:i');
			$lists[] = $r;
		}
		$admin = is_admin($GRP);

		include DT_ROOT.'/include/seo.inc.php';
		$seo_title = $L['chat_title'].$seo_delimiter.$GRP['title'].$MOD['seo_name'].$seo_delimiter.$seo_page.$seo_modulename.$seo_delimiter.$seo_sitename;
	break;
}
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	$js_pull = 0;
	$foot = '';
	if($sns_app) $seo_title = $L['chat_title'];
}
$template = $GRP['chat_template'] ? $GRP['chat_template'] : ($MOD['template_chat'] ? $MOD['template_chat'] : 'chat');
include template($template, $module);
?>