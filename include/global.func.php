<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function daddslashes($string) {
	return is_array($string) ? array_map('daddslashes', $string) : addslashes($string);
}

function dstripslashes($string) {
	return is_array($string) ? array_map('dstripslashes', $string) : stripslashes($string);
}

function dtrim($string) {
	return str_replace(array(chr(10), chr(13), urldecode('%C2%A0'), "\t", ' '), array('', '', '', '', ''), $string);
}

function dwrite($string) {
	return str_replace(array(chr(10), chr(13), "'"), array('', '', "\'"), $string);
}

function dheader($url) {
	exit(header('location:'.$url));
}

function dmsg($dmsg = '', $dforward = '') {
	if(!$dmsg && !$dforward) {
		$dmsg = get_cookie('dmsg');
		if($dmsg) {
			echo '<script type="text/javascript">showmsg(\''.$dmsg.'\');</script>';
			set_cookie('dmsg', '');
		}
	} else {
		set_cookie('dmsg', $dmsg);
		if(strpos($dforward, '?hash=') !== false) {
			$dforward = preg_replace("/\?hash=([0-9]{2})/", "?hash=".mt_rand(10, 99), $dforward);
		} else if(strpos($dforward, '&hash=') !== false) {
			$dforward = preg_replace("/\&hash=([0-9]{2})/", "&hash=".mt_rand(10, 99), $dforward);
		} else {
			$dforward .= (strpos($dforward, '?') === false ? '?' : '&').'hash='.mt_rand(10, 99);
		}
		dheader($dforward);
	}
}

function dalert($dmessage = errmsg, $dforward = '', $extend = '') {
	global $DT;
	exit(include template('alert', 'message'));
}

function dsubstr($string, $length, $suffix = '', $start = 0) {
	if($start) {
		$tmp = dsubstr($string, $start);
		$string = substr($string, strlen($tmp));
	}
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array('&quot;', '&lt;', '&gt;'), array('"', '<', '>'), $string);
	$length = $length - strlen($suffix);
	$str = '';
	if(DT_CHARSET == 'UTF-8') {
		$n = $tn = $noc = 0;
		while($n < $strlen)	{
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) break;
		}
		if($noc > $length) $n -= $tn;
		$str = substr($string, 0, $n);
	} else {
		for($i = 0; $i < $length; $i++) {
			$str .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	$str = str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $str);
	return $str == $string ? $str : $str.$suffix;
}

function match_kw($key, $keyword) {
	$op = substr($keyword, 0, 1);
	$kw = substr($keyword, 1);
	if($op == '=') {
		return " AND ".$key."='".$kw."'";
	} else if($op == '!') {
		return " AND ".$key."<>'".$kw."'";
	}
	$op = substr($keyword, 0, 4);
	$kw = substr($keyword, 4);
	if($op == 'EQU:') {
		return " AND ".$key."='".$kw."'";
	} else if($op == 'NEQ:') {
		return " AND ".$key."<>'".$kw."'";
	} else if($op == 'GTR:') {
		if(is_numeric($kw)) return " AND ".$key.">'".$kw."'";
	} else if($op == 'LSS:') {
		if(is_numeric($kw)) return " AND ".$key."<'".$kw."'";
	} else if($op == 'GEQ:') {
		if(is_numeric($kw)) return " AND ".$key.">='".$kw."'";
	} else if($op == 'LEQ:') {
		if(is_numeric($kw)) return " AND ".$key."<='".$kw."'";
	}
	return " AND ".$key." LIKE '%".$keyword."%'";
}

function cutstr($str, $mark1 = '', $mark2 = '') {
	if(!$str) return '';
	if($mark1) {
		$p1 = strpos($str, $mark1);
		if($p1 !== false) $str = substr($str, $p1 + strlen($mark1));
	}
	if(!$mark2) return $str;
	$p2 = strpos($str, $mark2);
	if($p2 === false) return $str;
	return substr($str, 0, $p2);
}

function encrypt($txt, $key = '', $ttl = 0) {
	strlen($key) > 5 or $key = DT_KEY;
	$str = $txt.substr($key, 0, 3);
	$i = 0;/*FIX +E= -E-E-P-*/
	while($i++ < 100) {
		$res0 = mycrypt($str, $key, 0, $ttl);
		$res1 = str_replace(array('=', '+', '/', '0x', '0X'), array('-E-', '-P-', '-S-', '-Z-', '-X-'), $res0);
		$res2 = str_replace(array('-E-', '-P-', '-S-', '-Z-', '-X-'), array('=', '+', '/', '0x', '0X'), $res1);
		if($res2 == $res0) return $res1;
	}
	return '';
}

function decrypt($txt, $key = '') {
	strlen($key) > 5 or $key = DT_KEY;
	$str = mycrypt(str_replace(array('-E-', '-P-', '-S-', '-Z-', '-X-'), array('=', '+', '/', '0x', '0X'), $txt), $key, 1);
	return substr($str, -3) == substr($key, 0, 3) ? substr($str, 0, -3) : '';
}

function mycrypt($string, $key, $decode = 1, $ttl = 0) {
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($decode ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $decode ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $ttl ? $ttl + DT_TIME : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($decode) {
		if((substr($result, 0, 1) == '0' || intval(substr($result, 0, 10)) - DT_TIME > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) return substr($result, 26);
		return '';
	}
	return $keyc.base64_encode($result);
}

function dround($var, $precision = 2, $sprinft = false) {
	$var = round(floatval($var), $precision);
	if($sprinft) $var = sprintf('%.'.$precision.'f', $var);
	return $var;
}

function dalloc($i, $n = 5000) {
	return ceil($i/$n);
}

function strip_nr($string, $js = false) {
	$string =  str_replace(array(chr(13), chr(10), "\n", "\r", "\t", '  '),array('', '', '', '', '', ''), $string);
	if($js) $string = str_replace("'", "\'", $string);
	return $string;
}

function template($template = 'index', $dir = '') {
	global $CFG, $DT_PC;
	check_name($template) or exit('BAD TPL NAME');
	if($dir) check_name($dir) or exit('BAD TPL DIR');
	$tpl = $DT_PC ? $CFG['template'] : $CFG['template_mobile'];
	$to = DT_CACHE.'/tpl/'.$tpl.'/'.($dir ? $dir.'/' : '').$template.'.php';
	$isfileto = is_file($to);
	if($CFG['template_refresh'] || !$isfileto) {
		if($dir) $dir = $dir.'/';
        $from = DT_ROOT.'/template/'.$tpl.'/'.$dir.$template.'.htm';
		if(!is_file($from)) $from = DT_ROOT.'/template/'.($DT_PC ? 'default' : 'mobile').'/'.$dir.$template.'.htm';
        if(!$isfileto || filemtime($from) > filemtime($to) || (filesize($to) == 0 && filesize($from) > 0)) {
			require_once DT_ROOT.'/include/template.func.php';
			template_compile($from, $to);
		}
	}
	return $to;
}

function ob_template($template, $dir = '') {
	extract($GLOBALS, EXTR_SKIP);
	ob_start();
	include template($template, $dir);
	$contents = ob_get_contents();
	ob_clean();
	return $contents;
}

function message($dmessage = errmsg, $dforward = 'goback', $dtime = 1) {
	if(!$dmessage && $dforward && $dforward != 'goback') dheader($dforward);
	global $DT, $DT_PC;
	if($DT_PC) {
		//
	} else {
		extract($GLOBALS, EXTR_SKIP);
		$foot = '';
	}
	exit(include template('message', 'message'));
}

function login() {
	global $_userid, $MODULE, $DT_URL, $DT_PC, $DT;
	$_userid or dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).$DT['file_login'].'?forward='.rawurlencode($DT_URL));
}

function random($length, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz') {
	if($chars == '0-9') {
		$chars = '0123456789';
	} else if($chars == 'a-z') {
		$chars = 'abcdefghijklmnopqrstuvwxyz';
	} else if($chars == 'A-Z') {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	} else if($chars == 'a-Z') {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	}
	$str = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++)	{
		$str .= $chars[mt_rand(0, $max)];
	}
	return $str;
}

function set_cookie($var, $value = '', $time = 0) {
	global $CFG;
	$time = $time > 0 ? ($time < DT_TIME ? DT_TIME + $time : $time) : (empty($value) ? DT_TIME - 3600 : 0);
	$port = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	$var = $CFG['cookie_pre'].$var;
	return setcookie($var, $value, $time, $CFG['cookie_path'], $CFG['cookie_domain'], $port);
}

function get_cookie($var) {
	global $CFG;
	$var = $CFG['cookie_pre'].$var;
	return isset($_COOKIE[$var]) ? $_COOKIE[$var] : '';
}

function get_table($moduleid, $data = 0) {
	global $MODULE;
	$module = $MODULE[$moduleid]['module'];
	if($module == 'destoon' || $moduleid == 3) return '';
	$M = array('company', 'member');
	if($data) {
		return in_array($module, $M) ? DT_PRE.$module.'_data' : DT_PRE.$module.'_data_'.$moduleid;
	} else {
		return in_array($module, $M) ? DT_PRE.$module : DT_PRE.$module.'_'.$moduleid;
	}
}

function get_process($fromtime, $totime) {
	if($totime > 0 && DT_TIME > $totime) return 3;
	if($fromtime > 0 && DT_TIME < $fromtime) return 1;
	return 2;
}

function blacked($username, $name = '') {
	global $_username;
	$name or $name = $_username;
	if(!check_name($name) || !check_name($username) || $name == $username) return false;
	$T = DB::get_one("SELECT itemid FROM ".DT_PRE."member_blacklist WHERE username='$username' AND busername='$name'");
	return $T ? true : false;
}

function send_message($touser, $title, $content, $typeid = 4, $fromuser = '', $mid = 0, $tid = 0) {
	global $DT, $MODULE, $_username, $_passport;
	if($touser == $fromuser) return false;
	if(preg_match("/(embed|object)/i", $content)) return false;
	if(check_name($touser) && $title && $content) {
		$tpassport = get_user($touser, 'username', 'passport');
		if(!$tpassport) return false;
		if(check_name($fromuser)) {
			if(blacked($touser, $fromuser)) return false;
			$fpassport = $fromuser == $_username ? $_passport : get_user($fromuser, 'username', 'passport');
			if(!$fpassport) return false;
		} else {
			$fromuser = $fpassport = '';
		}
		$title = stripslashes($title);
		$title = addslashes($title);
		$content = stripslashes($content);
		$content = addslashes($content);
		$title = dhtmlspecialchars(trim($title));
		if(strlen($title) < 3) return false;
		$content = dsafe($content);
		if(strlen($content) < 3) return false;		
		$content = str_replace(array('o<i></i>nerror', 'o<i></i>nload'), array('onerror', 'onload'), $content);
		$typeid = intval($typeid);
		$mid = intval($mid);
		$tid = intval($tid);
		DB::query("INSERT INTO ".DT_PRE."message (title,typeid,touser,tpassport,fromuser,fpassport,content,addtime,ip,status,mid,tid) VALUES ('$title','$typeid','$touser','$tpassport','$fromuser','$fpassport','$content','".DT_TIME."','".DT_IP."','3','$mid','$tid')");
		$itemid = DB::insert_id();
		DB::query("UPDATE ".DT_PRE."member SET message=message+1 WHERE username='$touser'");
		if($fromuser) {
			DB::query("INSERT INTO ".DT_PRE."message (title,typeid,content,fromuser,fpassport,touser,tpassport,addtime,ip,status,mid,tid) VALUES ('$title','$typeid','$content','$fromuser','$fpassport','$touser','$tpassport','".DT_TIME."','".DT_IP."','2','$mid','$tid')");
		}
		if($DT['push_appkey'] && $DT['push_secret'] && $DT['push_ok']) send_push($touser, $title, '', $MODULE[2]['mobile'].'message'.DT_EXT.'?action=show&itemid='.$itemid);
		return $itemid;		
	}
	return false;
}

function send_mail($mail_to, $mail_subject, $mail_body, $mail_from = '', $mail_sign = true) {
	global $DT;	
	if(substr($mail_to, -4) == '.sns') return false;
	require_once DT_ROOT.'/include/mail.func.php';
	$result = dmail(trim($mail_to), $mail_subject, $mail_body, $mail_from, $mail_sign);
	$success = $result == 'SUCCESS' ? 1 : 0;
	if($DT['mail_log']) {
		$status = $success ? 3 : 2;
		$note = $success ? '' : addslashes($result);
		$mail_subject = stripslashes($mail_subject);
		$mail_body = stripslashes($mail_body);
		$mail_subject = addslashes($mail_subject);
		$mail_body = addslashes($mail_body);
		DB::query("INSERT INTO ".DT_PRE."mail_log (email,title,content,addtime,ip,status,note) VALUES ('$mail_to','$mail_subject','$mail_body','".DT_TIME."','".DT_IP."','$status','$note')");
	}
	return $success;
}

function send_push($username, $content, $title = '', $linkurl = '', $uuid = '') {
	global $DT, $_username;
	if(!$DT['push_appkey'] || !$DT['push_secret'] || !$DT['push_ok'] || strlen($content) < 10) return false;
	if($uuid) {
		//
	} elseif(check_name($username)) {
		$user = DB::get_one("SELECT uuid FROM ".DT_PRE."app_bind WHERE username='$username' ORDER BY lasttime DESC");
		$uuid = $user ? $user['uuid'] : '';
	}
	$title = dsubstr(strip_tags(trim($title)), 20);
	$content = dsubstr(strip_tags(trim($content)), 50);
	if($linkurl) $linkurl = moburl($linkurl);
	$code = '';
	$status = 2;
	if($uuid) {
		$code = cloud_push($DT['push_appkey'], $DT['push_secret'], $uuid, $content, $title, $linkurl);
		if(strpos($code, $DT['push_ok']) !== false) $status = 3;
	} else {
		$code = 'UNBind';
	}
	DB::query("INSERT INTO ".DT_PRE."app_push (uuid,username,title,content,linkurl,editor,sendtime,ip,status,code) VALUES ('$uuid','$username','$title','$content','$linkurl','$_username','".DT_TIME."','".DT_IP."','$status','$code')");
	return $code;
}

function strip_sms($message) {
	global $DT;
	$message = strip_tags($message);
	$message = trim($message);
	$message = preg_replace("/&([a-z]{1,});/", '', $message);
	return $message;
}

function send_sms($mobile, $message, $word = 0, $cron = 0) {
	global $DT, $_username;
	if(!$DT['sms'] || !$DT['sms_sign'] || !DT_CLOUD_UID || !DT_CLOUD_KEY || !is_mobile($mobile)) return 0;
	$message = preg_replace("/&([a-z]{1,});/", '', trim(strip_tags($message)));
	if(strlen($message) < 5) return 0;
	if(strpos($message, substr($DT['sms_sign'], 0, 3)) === false) $message = $DT['sms_sign'].$message;
	if($DT['sms_api'] && $DT['sms_appid'] && $DT['sms_template'] && $DT['sms_code'] && strpos($message, $DT['sms_code']) !== false) {
		$code = dcurl(DT_PATH.'api/sms/'.$DT['sms_api'].'.php', 'auth='.encrypt($mobile.'|'.$message, DT_KEY.'SMS', 600));
	} else {
		$data = 'sms_uid='.DT_CLOUD_UID.'&sms_key='.md5(DT_CLOUD_KEY.'|'.DT_TIME.'|'.$mobile.'|'.md5($message)).'&sms_charset='.DT_CHARSET.'&sms_mobile='.$mobile.'&sms_time='.DT_TIME.'&sms_cron='.$cron.'&sms_message='.rawurlencode($message).'&sms_url='.rawurlencode(DT_PATH);
		$code = dcurl((DT_CLOUD_SSL ? 'https' : 'http').'://sms.destoon.com/send.php', $data);
		if($code && strpos($code, 'destoon_sms_code=') !== false) {
			$code = cutstr($code, 'destoon_sms_code=', '');
		} else {
			$code = 'Can Not Connect SMS Server';
		}
	}
	$word or $word = word_count($message);
	$status = strpos($code, $DT['sms_ok']) !== false ? 3 : 2;
	$code = addslashes($code);
	DB::query("INSERT INTO ".DT_PRE."sms (mobile,message,word,editor,sendtime,ip,status,code) VALUES ('$mobile','$message','$word','$_username','".DT_TIME."','".DT_IP."','$status','$code')");
	return $code;
}

function send_weixin($touser, $word) {
	if(check_name($touser) && strlen($word) > 1) {
		$user = DB::get_one("SELECT openid,push,visittime FROM ".DT_PRE."weixin_user WHERE username='$touser'");
		if($user && $user['openid'] && $user['push'] && DT_TIME - $user['visittime'] < 172800) {
			$openid = $user['openid'];
			$type = 'text';
			require_once DT_ROOT.'/api/weixin/init.inc.php';
			if(!is_object($wx)) {
				$wx = new weixin;
				$wx->access_token = $wx->get_token();
			}
			$arr = $wx->send($openid, $type, $word);
			if($arr['errcode'] != 0) return false;
			$post = array();
			$post['content'] = $word;
			$post['type'] = 'push';
			$post['openid'] = $openid;
			$post['editor'] = 'system';
			$post['addtime'] = DT_TIME;
			$post['misc'] = '';
			$post = daddslashes($post);
			DB::query("INSERT INTO ".DT_PRE."weixin_chat ".arr2sql($post, 0));
			return true;
		}
	}
	return false;
}

function word_count($string) {
	if(function_exists('mb_strlen')) return mb_strlen($string, DT_CHARSET);
	$string = convert($string, DT_CHARSET, 'gbk');
	$length = strlen($string);
	$count = 0;
	for($i = 0; $i < $length; $i++) {
		$t = ord($string[$i]);
		if($t > 127) $i++;
		$count++;
	}
	return $count;
}

function cache_read($file, $dir = '', $mode = '') {
	$file = $dir ? DT_CACHE.'/'.$dir.'/'.$file : DT_CACHE.'/'.$file;
	if(!is_file($file)) return $mode ? '' : array();
//    var_dump($file); die();
	return $mode ? file_get($file) : include $file;
}

function cache_write($file, $string, $dir = '') {
	if(is_array($string)) $string = "<?php defined('IN_DESTOON') or exit('Access Denied'); return ".strip_nr(var_export($string, true))."; ?>";
	$file = $dir ? DT_CACHE.'/'.$dir.'/'.$file : DT_CACHE.'/'.$file;
	$strlen = file_put($file, $string);
	return $strlen;
}

function cache_delete($file, $dir = '') {
	$file = $dir ? DT_CACHE.'/'.$dir.'/'.$file : DT_CACHE.'/'.$file;
	return file_del($file);
}

function cache_clear($str, $type = '', $dir = '') {
	$dir = $dir ? DT_CACHE.'/'.$dir.'/' : DT_CACHE.'/';
	$files = glob($dir.'*');
	if(is_array($files)) {
		if($type == 'dir') {
			foreach($files as $file) {
				if(is_dir($file)) {dir_delete($file);} else {if(file_ext($file) == $str) file_del($file);}
			}
		} else {
			foreach($files as $file) {
				if(!is_dir($file) && strpos(basename($file), $str) !== false) file_del($file);
			}
		}
	}
}

function content_table($moduleid, $itemid, $split, $table_data = '') {
	if($split) {
		return split_table($moduleid, $itemid);
	} else {
		$table_data or $table_data = get_table($moduleid, 1);
		return $table_data;
	}
}

function split_table($moduleid, $itemid) {
	$part = split_id($itemid);
	return DT_PRE.$moduleid.'_'.$part;
}

function split_id($id) {
	return $id > 0 ? ceil($id/100000) : 1;
}

function arr2sql($post, $type = 0, $fields = array()) {
	$sql = $sqlk = $sqlv = '';
	foreach($post as $k=>$v) {
		if($fields && !in_array($k, $fields)) continue;
		if($type) {
			$sql .= ",`".$k."`='".$v."'";
		} else {
			$sqlk .= ',`'.$k.'`'; $sqlv .= ",'".$v."'";
		}
	}
	return $type ? substr($sql, 1) : "(".substr($sqlk, 1).") VALUES (".substr($sqlv, 1).")";
}

function ip2area($ip, $type = 1) {
	global $dc, $DT;
	$area = '';
	if(is_ip($ip)) {
		if(strpos($ip, '.') !== false) {
			$tmp = explode('.', $ip);
			if($tmp[0] == 10 || $tmp[0] == 127 || ($tmp[0] == 192 && $tmp[1] == 168) || ($tmp[0] == 172 && ($tmp[1] >= 16 && $tmp[1] <= 31))) {
				$area = lang('include->ip_lan');
			} elseif($tmp[0] > 255 || $tmp[1] > 255 || $tmp[2] > 255 || $tmp[3] > 255) {
			} else {
				$area = $dc->get($ip);
				if(!$area) {
					if(is_file(DT_ROOT.'/file/ipdata/wry.dat')) {
						require_once DT_ROOT.'/include/ip.class.php';
						$ipcv = new ip($ip);
						$area = $ipcv->area();
					} else if($DT['ip_appcode']) {
						$area = cloud_ip($ip, $DT['ip_appcode']);
					}
					$dc->set($ip, $area, 86400*7);
				}
			}
		} else {
			if($DT['ip_appcode']) {
				$area = $dc->get($ip);
				if(!$area) {
					$area = cloud_ip($ip, $DT['ip_appcode']);
					$dc->set($ip, $area, 86400*7);
				}
			}
		}
	}
	if($area && $type > 1) {
		if(!function_exists('area_parse')) require_once DT_ROOT.'/include/client.func.php';
		if($type == 2) {
			$p = area_parse($area, 'province');
			if($p) {
				$c = area_parse($area, 'city');
				return $p == $c ? $p : $p.$c;
			}
			return area_parse($area, 'country');
		} else {
			$p = area_parse($area, 'province');
			return $p ? $p : area_parse($area, 'country');
		}
	}
	return $area ? $area : lang('include->ip_mars');
}

function banip() {
	$IP = cache_read('banip.php');
	if($IP) {
		$ban = false;
		foreach($IP as $v) {
			if($v['totime'] && $v['totime'] < DT_TIME) continue;
			if($v['ip'] == DT_IP) { $ban = true; break; }
			if(stripos(DT_UA, $v['ip']) !== false) { $ban = true; break; }
			if(preg_match("/^".str_replace('*', '[0-9]{1,3}', $v['ip'])."$/", DT_IP)) { $ban = true; break; }
		}
		if($ban) message(lang('include->msg_ip_ban'));
	}
}

function banword($WORD, $string, $extend = 1, $goback = '') {
	$string = stripslashes($string);
	foreach($WORD as $v) {
		$v[0] = preg_quote($v[0]);
		$v[0] = str_replace('/', '\/', $v[0]);
		$v[0] = str_replace("\*", ".*", $v[0]);
		if($v[2] && $extend) {
			if(preg_match("/".$v[0]."/i", $string)) {
				if($extend === 2) return array('ban' => $v[2] == 2 ? $v[0] : '');
				dalert(lang('include->msg_word_ban').($v[2] == 2 ? ':'.$v[0] : ''), $goback);
			}
		} else {
			if($string == '') break;
			if(preg_match("/".$v[0]."/i", $string)) $string = preg_replace("/".$v[0]."/i", $v[1], $string);
		}
	}
	return addslashes($string);
}

function get_env($type, $par = '') {
	switch($type) {
		case 'ip':
			if(DT_CDN) {
				if(isset($_SERVER['X-REAL-IP']) && is_ip($_SERVER['X-REAL-IP'])) return $_SERVER['X-REAL-IP'];
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					if(is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
					$ip = trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
					if(is_ip($ip)) return $ip;
				}
			}
			if(isset($_SERVER['REMOTE_ADDR']) && is_ip($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
			if(isset($_SERVER['HTTP_CLIENT_IP']) && is_ip($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				if(is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
				$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$ip = trim(end($ips));
				if(is_ip($ip)) return $ip;
			}
			return '0.0.0.0';
		break;
		case 'self':
			return isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
		break;
		case 'referer':
			return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		break;
		case 'domain':
			return $_SERVER['SERVER_NAME'];
		break;
		case 'scheme':
			if(isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && in_array($_SERVER['HTTP_X_CLIENT_SCHEME'], array('http', 'https'))) return $_SERVER['HTTP_X_CLIENT_SCHEME'].'://';
			if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && in_array($_SERVER['HTTP_X_FORWARDED_PROTO'], array('http', 'https'))) return $_SERVER['HTTP_X_FORWARDED_PROTO'].'://';
			if(isset($_SERVER['HTTPS']) && in_array($_SERVER['HTTPS'], array('on', 'off'))) return $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
			if(isset($_SERVER['REQUEST_SCHEME']) && in_array($_SERVER['REQUEST_SCHEME'], array('http', 'https'))) return $_SERVER['REQUEST_SCHEME'].'://';
			if($_SERVER['SERVER_PORT'] == '443') return 'https://';
			if($_SERVER['SERVER_PORT'] == '80') return 'http://';
			return substr(DT_PATH, 0, 5) == 'https' ? 'https://' : 'http://';
		break;
		case 'port':
			return isset($_SERVER['REMOTE_PORT']) ? intval($_SERVER['REMOTE_PORT']) : 0;
		break;
		case 'host':
			return preg_match("/^[a-z0-9_\-\.]{4,}$/i", $_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		break;
		case 'url':
			if(isset($_SERVER['HTTP_X_REWRITE_URL']) && $_SERVER['HTTP_X_REWRITE_URL']) {
				$uri = $_SERVER['HTTP_X_REWRITE_URL'];
			} else if(isset($_SERVER['HTTP_X_ORIGINAL_URL']) && $_SERVER['HTTP_X_ORIGINAL_URL']) {
				$uri = $_SERVER['HTTP_X_ORIGINAL_URL'];
			} else if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
				$uri = $_SERVER['REQUEST_URI'];
			} else {
				$uri = $_SERVER['PHP_SELF'];
				if(isset($_SERVER['argv'])) {
					if(isset($_SERVER['argv'][0])) $uri .= '?'.$_SERVER['argv'][0];
				} else {
					$uri .= '?'.$_SERVER['QUERY_STRING'];
				}
			}
			$uri = dhtmlspecialchars($uri);
			if(strpos($uri, '.php?') !== false && strpos($uri, '.html') !== false && strpos($uri, '=') === false) $uri = str_replace('.php?', '-htm-', $uri);
			return get_env('scheme').$_SERVER['HTTP_HOST'].(strpos($_SERVER['HTTP_HOST'], ':') === false ? (($_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443') ? '' : ':'.$_SERVER['SERVER_PORT']) : '').$uri;
		break;
		case 'mobile':
			$ua = strtolower(DT_UA);
			if(strpos($ua, 'iphone') !== false || strpos($ua, 'ipod') !== false) return 'ios';
			if(strpos($ua, 'android') !== false || strpos($ua, 'adr') !== false) return 'android';
			return '';
		break;
		case 'browser':
			$ck = get_cookie('mobile');
			if(in_array($ck, array('app', 'cms', 'web', 'screen', 'desktop'))) return $ck;
			$browser = '';
			$ua = strtolower(DT_UA);
			if(strpos($ua, 'micromessenger/') !== false) {
				$browser = 'weixin';
				if(strpos($ua, 'miniprogram') !== false) {
					$browser = 'wxmini';
				} elseif(strpos($ua, 'wxwork') !== false) {
					$browser = 'wxwork';
				} 
			} else if(strpos($ua, 'tim/') !== false) {
				$browser = 'tim';
			} else if(strpos($ua, 'qq/') !== false) {
				$browser = 'qq';
			} else if(strpos($ua, 'alipay') !== false) {
				$browser = 'alipay';
			} else if(strpos($ua, 'dingtalk') !== false) {
				$browser = 'dingtalk';
			} else if(strpos($ua, 'bytedance') !== false) {
				$browser = 'douyin';
			} else if(strpos($ua, 'weibo') !== false) {
				$browser = 'weibo';
			} else if(strpos($ua, 'destoon') !== false) {
				$browser = 'app';
			} else if(strpos($ua, 'lt-app') !== false) {
				$browser = 'app-lt';
			} else if(strpos($ua, 'lt-pc') !== false) {
				$browser = 'desktop';
			} else if(strpos($ua, 'windows nt') !== false) {
				if(strpos($ua, 'rv:11.0') !== false) return 'IE11';
				if(strpos($ua, 'msie') !== false) return 'IE';
			}
			return $browser;
		break;
	}
}

function convert($str, $from = 'utf-8', $to = 'gb2312') {
	if(!$str) return '';
	$from = str_replace(array('gbk', 'utf8'), array('gb2312', 'utf-8'), strtolower($from));
	$to = str_replace(array('gbk', 'utf8'), array('gb2312', 'utf-8'), strtolower($to));
	if($from == $to) return $str;
	$tmp = array();
	if(function_exists('mb_convert_encoding')) {
		if(is_array($str)) {
			foreach($str as $key => $val) {
				$tmp[$key] = mb_convert_encoding($val, $to, $from);
			}
			return $tmp;
		} else {
			return mb_convert_encoding($str, $to, $from);
		}
	} else if(function_exists('iconv')) {
		if(is_array($str)) {
			foreach($str as $key => $val) {
				$tmp[$key] = iconv($from, $to."//IGNORE", $val);
			}
			return $tmp;
		} else {
			return iconv($from, $to."//IGNORE", $str);
		}
	} else {
		require_once DT_ROOT.'/include/convert.func.php';
		return dconvert($str, $from, $to);
	}
}

function get_type($item, $cache = 0) {
	$types = array();
	if($cache) {
		$types = cache_read('type-'.$item.'.php');
	} else {
		$result = DB::query("SELECT * FROM ".DT_PRE."type WHERE item='$item' ORDER BY listorder ASC,typeid DESC ");
		while($r = DB::fetch_array($result)) {
			$types[$r['typeid']] = $r;
		}
	}
	return $types;
}

function get_cat($catid) {
	if(!is_numeric($catid)) return array();
	$catid = intval($catid);
	return $catid ? DB::get_one("SELECT * FROM ".DT_PRE."category WHERE catid=$catid") : array();
}

function cat_pos($CAT, $str = ' &raquo; ', $target = '', $deep = 0, $start = 0) {
	global $MODULE;
	if(!$CAT) return '';
	$arrparentids = $CAT['arrparentid'].','.$CAT['catid'];
	$arrparentid = explode(',', $arrparentids);
	$pos = '';
	$target = $target ? ' target="_blank"' : '';	
	$CATEGORY = array();
	$result = DB::query("SELECT catid,moduleid,catname,linkurl FROM ".DT_PRE."category WHERE catid IN ($arrparentids)", 'CACHE');
	while($r = DB::fetch_array($result)) {
		$CATEGORY[$r['catid']] = $r;
	}
	if($deep) $i = 1;
	$j = 0;
	foreach($arrparentid as $catid) {
		if(!$catid || !isset($CATEGORY[$catid])) continue;
		if($j++ < $start) continue;
		if($deep) {
			if($i > $deep) continue;
			$i++;
		}
		$pos .= '<a href="'.$MODULE[$CATEGORY[$catid]['moduleid']]['linkurl'].$CATEGORY[$catid]['linkurl'].'"'.$target.'>'.$CATEGORY[$catid]['catname'].'</a>'.$str;
	}
	$_len = strlen($str);
	if($str && substr($pos, -$_len, $_len) === $str) $pos = substr($pos, 0, strlen($pos) - $_len);
	return $pos;
}

function cat_url($catid) {
	global $MODULE, $DT_PC;
	$catid = intval($catid);
	$r = DB::get_one("SELECT moduleid,linkurl FROM ".DT_PRE."category WHERE catid=$catid");
	return $r ? ($DT_PC ? $MODULE[$r['moduleid']]['linkurl'] : $MODULE[$r['moduleid']]['mobile']).$r['linkurl'] : '';
}

function get_area($areaid) {
	if(!is_numeric($areaid)) return array();
	$areaid = intval($areaid);
	return $areaid ? DB::get_one("SELECT * FROM ".DT_PRE."area WHERE areaid=$areaid") : array();
}
function get_shuxing($shuxingid) {
    global $moduleid, $module;
    if(!is_numeric($shuxingid)) return array();
    $shuxingid = intval($shuxingid);
    return $shuxingid ? DB::get_one("SELECT * FROM ".DT_PRE.$module."_shuxing_".$moduleid." WHERE shuxingid=$shuxingid") : array();
}
function area_pos($areaid, $str = ' &raquo; ', $deep = 0, $start = 0) {
	$areaid = intval($areaid);
	if($areaid) {
		global $AREA;
	} else {
		global $L;
		return $L['allcity'];
	}
	$AREA or $AREA = cache_read('area.php');
	$arrparentid = $AREA[$areaid]['arrparentid'] ? explode(',', $AREA[$areaid]['arrparentid']) : array();
	$arrparentid[] = $areaid;
	$pos = '';
	if($deep) $i = 1;
	$j = 0;
	foreach($arrparentid as $areaid) {
		if(!$areaid || !isset($AREA[$areaid])) continue;
		if($j++ < $start) continue;
		if($deep) {
			if($i > $deep) continue;
			$i++;
		}
		$pos .= $AREA[$areaid]['areaname'].$str;
	}
	$_len = strlen($str);
	if($str && substr($pos, -$_len, $_len) === $str) $pos = substr($pos, 0, strlen($pos)-$_len);
	return $pos;
}

function get_maincat($catid, $moduleid, $level = -1) {
	$catid = intval($catid);
	$condition = $catid ? "parentid=$catid" : "moduleid=$moduleid AND parentid=0";
	if($level >= 0) $condition .= " AND level=$level";
    $cat = array();
//    var_dump($CAT); die();
//    $f['thumb'] ='' ;
//    $f['icon'] = '';
//    $sql="SELECT parentid FROM ".DT_PRE."category WHERE catid='$catid'";
//    获取本栏目的parentid
    if($catid){$rr = DB::get_one("SELECT parentid,thumb FROM ".DT_PRE."category WHERE catid='$catid'");}
//    获取父级栏目
//    if($catid){$f = DB::get_one("SELECT thumb FROM ".DT_PRE."category WHERE catid=".$rr['parentid']);}
//    var_dump($f);
	$result = DB::query("SELECT catid,catname,child,style,linkurl,item,thumb,icon FROM ".DT_PRE."category WHERE {$condition} ORDER BY listorder,catid ASC", 'CACHE');
	while($r = DB::fetch_array($result)) {
        $r['thumb']=$r['thumb']!=''?$r['thumb']:$rr['thumb'];
		$cat[] = $r;
	}

	return $cat;
}

function get_mainarea($areaid) {
	$areaid = intval($areaid);
	$are = array();
	$result = DB::query("SELECT areaid,areaname FROM ".DT_PRE."area WHERE parentid=$areaid ORDER BY listorder,areaid ASC", 'CACHE');
	while($r = DB::fetch_array($result)) {
		$are[] = $r;
	}
	return $are;
}

function get_user($value, $key = 'username', $from = 'userid') {
	$r = DB::get_one("SELECT `$from` FROM ".DT_PRE."member WHERE `$key`='$value'");
	return $r[$from];
}

function check_group($groupid, $groupids) {
	if(!$groupids || $groupid == 1) return true;
	if($groupid == 4) $groupid = 3;
	return in_array($groupid, explode(',', $groupids));
}

function tohtml($htmlfile, $module = 'destoon', $parameter = '') {
	defined('TOHTML') or define('TOHTML', true);
    extract($GLOBALS, EXTR_SKIP);
	if($parameter) {
		parse_str($parameter, $par);		
		extract($par, EXTR_OVERWRITE);
	}
    include DT_ROOT.'/module/'.$module.'/'.$htmlfile.'.htm.php';
}

function set_style($string, $style = '', $tag = 'span') {
	if(preg_match("/^#[0-9a-zA-Z]{6}$/", $style)) $style = 'color:'.$style;
	return $style ? '<'.$tag.' style="'.$style.'">'.$string.'</'.$tag.'>' : $string;
}

function crypt_action($action) {
	return md5(md5($action.DT_KEY.DT_IP));
}

function captcha($captcha, $enable = 1, $return = false) {
	global $DT, $session;
	if($enable) {
		if($DT['captcha_cn']) {
			if(strlen($captcha) < 4) {
				$msg = lang('include->captcha_missed');
				return $return ? $msg : message($msg);
			}
		} else {
			if(!preg_match("/^[0-9a-z]{4,}$/i", $captcha)) {
				$msg = lang('include->captcha_missed');
				return $return ? $msg : message($msg);
			}
		}
		if(!is_object($session)) $session = new dsession();
		if(!isset($_SESSION['captchastr'])) {
			$msg = lang('include->captcha_expired');
			return $return ? $msg : message($msg);
		}
		if(decrypt($_SESSION['captchastr'], DT_KEY.'CPC') != strtoupper($captcha)) {
			$msg = lang('include->captcha_error');
			return $return ? $msg : message($msg);
		}
		unset($_SESSION['captchastr']);
	}
	return '';
}

function wxcode($wxcode, $enable = 1, $return = false) {
	global $DT, $session;
	if($enable) {
		if(!preg_match("/^[0-9]{6}$/", $wxcode)) {
			$msg = lang('include->captcha_missed');
			return $return ? $msg : message($msg);
		}
		DB::query("DELETE FROM ".DT_PRE."weixin_code WHERE addtime<".DT_TIME."-600");
		$t = DB::get_one("SELECT * FROM ".DT_PRE."weixin_code WHERE code='$wxcode'");
		if(!$t) {
			$msg = lang('include->captcha_error');
			return $return ? $msg : message($msg);
		}
		DB::query("DELETE FROM ".DT_PRE."weixin_code WHERE code='$wxcode'");
		set_cookie('weixin_openid', encrypt($t['openid'], DT_KEY.'WXID'));
	}
	return '';
}

function question($answer, $enable = 1, $return = false) {
	global $session;
	if($enable) {
		if(!$answer) {
			$msg = lang('include->answer_missed');
			return $return ? $msg : message($msg);
		}
		$answer = stripslashes($answer);
		if(!is_object($session)) $session = new dsession();
		if(!isset($_SESSION['answerstr'])) {
			$msg = lang('include->question_expired');
			return $return ? $msg : message($msg);
		}
		$msg = lang('include->answer_error');
		$ansstr = decrypt($_SESSION['answerstr'], DT_KEY.'ANS');
		if(strpos($ansstr, '|') !== false) {
			$ansarr = explode('|', $ansstr);
			if(!in_array($answer, $ansarr)) return $return ? $msg : message($msg);
		} else {
			if($ansstr != $answer) return $return ? $msg : message($msg);
		}
		unset($_SESSION['answerstr']);
	}
	return '';
}

function pages($total, $page = 1, $perpage = 20, $demo = '', $step = 3) {
	global $DT_URL, $DT, $L;
	if($total <= $perpage) return '';
	$items = $total;
	$total = ceil($total/$perpage);
	if($page < 1 || $page > $total) $page = 1;
	if($demo) {
		$demo_url = str_replace('%7Bdestoon_page%7D', '{destoon_page}', $demo);
		$home_url = str_replace('{destoon_page}', '1', $demo_url);
	} else {
		if(defined('DT_REWRITE') && $DT['rewrite'] && $_SERVER["SCRIPT_NAME"] && strpos($DT_URL, '?') === false) {
			$demo_url = $_SERVER["SCRIPT_NAME"];
			$demo_url = str_replace('//', '/', $demo_url);//Fix Nginx
			$mark = false;
			if(substr($demo_url, -4) == '.php') {
				if(strpos($_SERVER['QUERY_STRING'], '.html') === false) {
					$qstr = '';
					if($_SERVER['QUERY_STRING']) {					
						if(substr($_SERVER['QUERY_STRING'], -5) == '.html') {
							$qstr = '-'.substr($_SERVER['QUERY_STRING'], 0, -5);
						} else {
							parse_str($_SERVER['QUERY_STRING'], $qs);
							foreach($qs as $k=>$v) {
								$qstr .= '-'.$k.'-'.rawurlencode($v);
							}
						}
					}
					$demo_url = substr($demo_url, 0, -4).'-htm-page-{destoon_page}'.$qstr.'.html';
				} else {
					$demo_url = substr($demo_url, 0, -4).'-htm-'.$_SERVER['QUERY_STRING'];
					$mark = true;
				}
			} else {
				$mark = true;
			}
			if($mark) {
				if(strpos($demo_url, '%') === false) $demo_url =  rawurlencode($demo_url);
				$demo_url = str_replace(array('%2F', '%3A'), array('/', ':'), $demo_url);
				if(strpos($demo_url, '-page-') !== false) {
					$demo_url = preg_replace("/page-([0-9]+)/", 'page-{destoon_page}', $demo_url);
				} else {
					$demo_url = str_replace('.html', '-page-{destoon_page}.html', $demo_url);
				}
			}
			$home_url = str_replace('-page-{destoon_page}', '-page-1', $demo_url);
		} else {
			$DT_URL = str_replace('&amp;', '&', $DT_URL);
			$demo_url = $home_url = preg_replace("/(.*)([&?]page=[0-9]*)(.*)/i", "\\1\\3", $DT_URL);
			$s = strpos($demo_url, '?') === false ? '?' : '&';
			$demo_url = $demo_url.$s.'page={des'.'toon_page}';
			if(defined('DT_ADMIN') && strpos($demo_url, 'sum=') === false) $demo_url = str_replace('page=', 'sum='.$items.'&page=', $demo_url);
		}
	}
	if($DT['max_search'] > 0 && $total > $DT['max_search'] && strpos($demo_url, '/search') !== false && !defined('TOHTML')) $total = $DT['max_search'];
	$pages = '';
	include DT_ROOT.'/api/pages.'.((!$DT['pages_mode'] && $page < 100) ? 'default' : 'sample').'.php';
	return $pages;
}

function listpages($CAT, $total, $page = 1, $perpage = 20, $step = 2) {
	global $DT, $MOD, $L;
	if($total <= $perpage) return '';
	$items = $total;
	$total = ceil($total/$perpage);
	if($page < 1 || $page > $total) $page = 1;
	$home_url = $MOD['linkurl'].$CAT['linkurl'];
	$demo_url = $MOD['linkurl'].listurl($CAT, '{destoon_page}');
	if($DT['max_list'] > 0 && $total > $DT['max_list'] && strpos($demo_url, '/list') !== false && !defined('TOHTML')) $total = $DT['max_list'];
	$pages = '';
	include DT_ROOT.'/api/pages.'.((!$DT['pages_mode'] && $page < 100) ? 'default' : 'sample').'.php';
	return $pages;
}

function linkurl($linkurl) {
	if($linkurl == '/') return DT_PATH;
	return strpos($linkurl, '://') === false ? DT_PATH.$linkurl : $linkurl;
}

function imgurl($url = '', $width = '') {
	if($url) {
		return strpos($url, '://') === false ? DT_PATH.'file/upload/'.$url : $url;
	} else {
		return DT_STATIC.'image/nopic'.$width.'.png';
	}
}

function gourl($url = '') {
	global $DT_PC;
	if($url && substr($url, 0, 1) != '?') $url = '?url='.urlencode($url);
	return ($DT_PC ? DT_PATH : DT_MOB).'api/redirect'.DT_EXT.$url;
}

function userurl($username, $qstring = '', $domain = '') {
	global $CFG, $DT, $MODULE;
	$URL = '';
	$subdomain = 0;
	if($CFG['com_domain']) $subdomain = substr($CFG['com_domain'], 0, 1) == '.' ? 1 : 2;
	if(!$DT['homepage'] && $qstring == 'file=space') $qstring = '';
	if($username) {
		if($subdomain || $domain) {
			$scheme = $DT['com_https'] ? 'https://' : 'http://';
			$URL = $domain ? $scheme.$domain.'/' : ($subdomain == 1 ? $scheme.($DT['com_www'] ? 'www.' : '').$username.$CFG['com_domain'].'/' : $scheme.$CFG['com_domain'].'/'.$username.'/');
			if($qstring) {
				parse_str($qstring, $q);
				if(isset($q['file'])) {
					$URL .= $CFG['com_dir'] ? $q['file'].'/' : 'company/'.$q['file'].'/';
					unset($q['file']);
				}
				if($q) {
					if($DT['rewrite']) {
						foreach($q as $k=>$v) {
							$v = rawurlencode($v);
							$URL .= $k.'-'.$v.'-';
						}
						$URL = substr($URL, 0, -1).'.html';
					} else {
						$URL .= 'index'.DT_EXT.'?';
						$i = 0;
						foreach($q as $k=>$v) {
							$v = rawurlencode($v);
							$URL .= ($i++ == 0 ? '' : '&').$k.'='.$v;
						}
					}
				}
			}
		} else if($DT['rewrite']) {
			$URL = DT_PATH.($DT['com_mark'] ? $DT['com_mark'] : 'com').'/'.$username.'/';
			if($qstring) {
				parse_str($qstring, $q);
				if(isset($q['file'])) {
					$URL .= $CFG['com_dir'] ? $q['file'].'/' : 'company/'.$q['file'].'/';
					unset($q['file']);
				}
				if($q) {
					foreach($q as $k=>$v) {
						$v = rawurlencode($v);
						$URL .= $k.'-'.$v.'-';
					}
					$URL = substr($URL, 0, -1).'.html';
				}
			}
		} else {
			$URL = DT_PATH.'index'.DT_EXT.'?homepage='.$username;
			if($qstring) $URL = $URL.'&'.$qstring;
		}
	} else {
		$URL = $MODULE[4]['linkurl'].'guest'.DT_EXT;
	}
	return $URL;
}

function useravatar($var, $size = '', $isusername = 1, $real = 0) {
	in_array($size, array('large', 'small')) or $size = 'middle';
	if($real) {
		$ext = 'x48.jpg';
		if($size == 'large') $ext = '.jpg';
		if($size == 'small') $ext = 'x20.jpg';
		$file = DT_ROOT.'/api/avatar/default'.$ext;
		$md5 = md5($var);
		if($isusername) {
			$img = DT_ROOT.'/file/avatar/'.substr($md5, 0, 2).'/'.substr($md5, 2, 2).'/_'.$var.$ext;
			if(is_file($img) && check_name($var)) $file = $img;
		} else {
			$img = DT_ROOT.'/file/avatar/'.substr($md5, 0, 2).'/'.substr($md5, 2, 2).'/'.$var.$ext;
			if(is_file($img)) $file = $img;
		}
		if($real == 1) {
			$url = str_replace(DT_ROOT.'/', DT_PATH, $file);
			if(strpos($url, '/default') === false) {
				$remote = file_get(DT_ROOT.'/file/avatar/remote.html');
				if(strlen($remote) > 10) $url = str_replace(DT_ROOT.'/file/', $remote, $file);
			}
			return $url;
		}
		return strpos($file, '/api/') === false ? $file : '';
	} else {
		$name = $isusername ? 'username' : 'userid';
		return DT_PATH.'api/avatar/show'.DT_EXT.'?'.$name.'='.$var.'&size='.$size;
	}
}

function userinfo($username, $cache = 1) {
	global $dc, $CFG;
	if(!check_name($username)) return array();
	$user = array();
	if($cache && $CFG['db_expires']) {
		$user = $dc->get('user-'.$username);
		if($user) return $user;
	}
	$r1 = DB::get_one("SELECT * FROM ".DT_PRE."member WHERE username='$username'");
	if($r1) {
		$userid = $r1['userid'];
		$GROUP = cache_read('group.php');
		$r1['groupname'] = $GROUP[$r1['groupid']]['groupname'];
		$r2 = DB::get_one("SELECT * FROM ".DT_PRE."member_misc WHERE userid=$userid");
		$r3 = DB::get_one("SELECT * FROM ".DT_PRE."company WHERE userid=$userid");
		$user = array_merge($r1, $r2, $r3);
	}
	if($cache && $CFG['db_expires'] && $user) $dc->set('user-'.$username, $user, $CFG['db_expires']*100);
	return $user;
}

function userclean($username) {
	global $dc, $CFG;
	$user = array();
	if($CFG['db_expires']) $dc->rm('user-'.$username);
}

function listurl($CAT, $page = 0) {
	global $DT, $MOD, $L;
	include DT_ROOT.'/api/url.inc.php';
	$catid = $CAT['catid'];
	$file_ext = $DT['file_ext'];
	$index = $DT['index'];
	$catdir = $CAT['catdir'];
	$catname = file_vname($CAT['catname']);
	$prefix = $MOD['htm_list_prefix'];
	$urlid = $MOD['list_html'] ? $MOD['htm_list_urlid'] : $MOD['php_list_urlid'];
	$ext = $MOD['list_html'] ? 'htm' : 'php';
	isset($urls[$ext]['list'][$urlid]) or $urlid = 0;
	$url = $urls[$ext]['list'][$urlid];
	$url = $page ? $url['page'] : $url['index'];
    eval("\$listurl = \"$url\";");
	if(substr($listurl, 0, 1) == '/') $listurl = substr($listurl, 1);
	return $listurl;
}

function itemurl($item, $page = 0) {
	global $DT, $MOD, $L;
	if(isset($item['islink']) && $item['islink']) return $item['linkurl'];
	if($MOD['show_html'] && $item['filepath']) {
		if($page === 0) return $item['filepath'];
		$ext = file_ext($item['filepath']);
		return str_replace('.'.$ext, '_'.$page.'.'.$ext, $item['filepath']);
	}
	include DT_ROOT.'/api/url.inc.php';
	$file_ext = $DT['file_ext'];
	$index = $DT['index'];
	$itemid = $item['itemid'];
	$title = file_vname($item['title']);
	$addtime = $item['addtime'];
	$catid = $item['catid'];
	$year = date('Y', $addtime);
	$month = date('m', $addtime);
	$day = date('d', $addtime);
	$prefix = $MOD['htm_item_prefix'];
	$urlid = $MOD['show_html'] ? $MOD['htm_item_urlid'] : $MOD['php_item_urlid'];
	$ext = $MOD['show_html'] ? 'htm' : 'php';
	$alloc = dalloc($itemid);
	$url = $urls[$ext]['item'][$urlid];
	$url = $page ? $url['page'] : $url['index'];
	if(strpos($url, 'cat') !== false && $catid) {
		if(isset($item['gid'])) {
			$catid = $item['gid'];
			$cate = get_group($catid);
			$catdir = $cate['filepath'];
			$catname = $cate['title'];
		} else {
			$cate = get_cat($catid);
			$catdir = $cate['catdir'];
			$catname = $cate['catname'];
		}
	}
	if(!isset($catdir)) $catdir = 'none';
    eval("\$itemurl = \"$url\";");
	if(substr($itemurl, 0, 1) == '/') $itemurl = substr($itemurl, 1);
	return $itemurl;
}

function moburl($url, $mid = 0) {
	global $MODULE;
	if(strpos($url, DT_MOB) !== false) return $url;
	if(strpos($url, DT_PATH.'com/') !== false) return $url;
	if(strpos($url, DT_PATH.'index'.DT_EXT.'?homepage=') !== false) return $url;
	if(strpos($url, DT_PATH) !== false) return str_replace(DT_PATH, DT_MOB, $url);
	if($mid) return str_replace($MODULE[$mid]['linkurl'], $MODULE[$mid]['mobile'], $url);
	foreach($MODULE as $m) {
		if(strpos($url, $m['linkurl']) !== false) return str_replace($m['linkurl'], $m['mobile'], $url);
	}
	return $url;
}

function rewrite($url, $decode = 0) {
	if($decode) {
		if(strpos($url, '-htm-') === false) return $url;
		if(substr($url, -5) == '.html') $url = substr($url, 0, -5);
		$t1 = explode('-htm-', $url);
		$t2 = explode('-', $t1[1]);
		$rc = count($t2);
		$par = '';
		for($i = 0; $i < $rc; $i++) {
			$par .= '&'.$t2[$i].'='.$t2[++$i];
		}
		$url = $t1[0].DT_EXT.'?'.substr($par, 1);
	} else {
		if(!RE_WRITE) return $url;
		if(RE_WRITE == 1 && strpos($url, 'search'.DT_EXT) !== false) return $url;
		if(strpos($url, DT_EXT.'?') === false || strpos($url, '=') === false) return $url;
		$url = str_replace(array('+', '-'), array('%20', '%20'), $url);
		$url = str_replace(array(DT_EXT.'?', '&', '='), array('-htm-', '-', '-'), $url).'.html';
	}
	return $url;
}

function timetodate($time = 0, $type = 6) {
	if(!$time) $time = DT_TIME;
	$types = array('Y-m-d', 'Y', 'm-d', 'Y-m-d', 'm-d H:i', 'Y-m-d H:i', 'Y-m-d H:i:s');
	if(isset($types[$type])) $type = $types[$type];
	if($time > 2147212800) {		
		if(class_exists('DateTime')) {
			$D = new DateTime('@'.($time - 3600 * intval(str_replace('Etc/GMT', '', $GLOBALS['CFG']['timezone']))));
			return $D->format($type);
		}
	}
	return date($type, $time);
}

function datetotime($date) {
	$time = strtotime($date);
	if($time === false) {
		if(class_exists('DateTime')) {
			$D = new DateTime($date);
			$time = $D->format('U');
		}
	}
	return $time;
}

function log_write($message, $name = 'log', $force = 0) {
	if(!DT_DEBUG && !$force) return;
	global $DT_URL, $_username;
	$user = $_username ? $_username : 'guest';
	check_name($name) or $name = 'log';
	$msg = (is_array($message) ? var_export($message, true) : $message);
	$file = DT_ROOT.'/file/log/'.timetodate(0, 'Ym/d').'/'.$name.'-'.timetodate(0, 'H').'.php';
	$data = timetodate()."\t".DT_IP."\t".$user."\t".$DT_URL."\n".$msg."\n\n";
	if(is_file($file)) {
		file_put($file, "<?php exit;?>\n".$data.substr(file_get($file), 14));
	} else {
		file_put($file, "<?php exit;?>\n".$data);
	}
}

function load($file) {
	global $DT_PC;
	$ext = file_ext($file);
	if($ext == 'css') {
		echo '<link rel="stylesheet" type="text/css" href="'.(strpos($file, '/') === false ? ($DT_PC ? DT_SKIN : DM_SKIN) : DT_STATIC).$file.'?v='.(DT_DEBUG ? DT_TIME : DT_REFRESH).'"/>';
	} else if($ext == 'js') {
		echo '<script type="text/javascript" src="'.($DT_PC ? DT_STATIC : DT_MOB).'script/'.$file.'?v='.(DT_DEBUG ? DT_TIME : DT_REFRESH).'"></script>';
	} else if($ext == 'htm') {
		$file = str_replace('ad_m', 'ad_t6_m', $file);
		if(!$DT_PC) {
			if(strpos($file, 'ad_') !== false) $file = substr($file, 0, -4).'_m.htm';
			if(strpos($file, '_k') !== false && substr($file, 0, 1) == 'm') $file = substr($file, 0, -4).'_m.htm';
		}
		if(is_file(DT_CACHE.'/htm/'.$file)) {
			$content = file_get(DT_CACHE.'/htm/'.$file);
			if(substr($content, 0, 4) == '<!--') $content = substr($content, 17);
			echo $content;
		} else {
			echo '';
		}
	} else if($ext == 'lang') {
		$file = str_replace('.lang', '.inc.php', $file);
		return DT_ROOT.'/lang/'.DT_LANG.'/'.$file;
	} else if($ext == 'inc' || $ext == 'func' || $ext == 'class' || $ext == 'name') {
		return strpos($file, '/') === false ? DT_ROOT.'/include/'.$file.'.php' : DT_ROOT.'/'.$file.'.php';
	}
}

function ad($id, $cid = 0, $kw = '', $tid = 0) {
	global $cityid;
	if($tid) {
		if($kw) {
			$file = 'ad_t'.$tid.'_m'.$id.'_k'.urlencode($kw);
		} else if($cid) {
			$file = 'ad_t'.$tid.'_m'.$id.'_c'.$cid;
		} else {
			$file = 'ad_t'.$tid.'_m'.$id;
		}
		$a3 = 'ad_'.$id.'_d'.$tid.'.htm';
	} else {
		$file = 'ad_'.$id;
		$a3 = 'ad_'.$id.'_d0.htm';
	}
	$a1 = $file.'_'.$cityid.'.htm';
	if(is_file(DT_CACHE.'/htm/'.$a1)) return load($a1);
	$a2 = $file.'_0.htm';
	if(is_file(DT_CACHE.'/htm/'.$a2)) return load($a2);
	if(is_file(DT_CACHE.'/htm/'.$a3)) return load($a3);
}

function lang($str, $arr = array()) {
	if(strpos($str, '->') !== false) {
		global $DT;
		$t = explode('->', $str);
		include load($t[0].'.lang');
		$str = $L[$t[1]];
	}
	if($arr) {
		foreach($arr as $k=>$v) {
			$str = str_replace('{V'.$k.'}', $v, $str);
		}
	}
	return $str;
}

function check_name($username) {
	if(strpos($username, '__') !== false || strpos($username, '--') !== false) return false; 
	return preg_match("/^[a-z0-9]{1}[a-z0-9_\-]{0,}[a-z0-9]{1}$/", $username);
}

function check_post() {
	if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') return false;
	return check_referer();
}

function check_referer() {
	global $DT_REF, $CFG, $DT;
	if($DT['check_referer']) {
		if(!$DT_REF) return false;
		$R = parse_url($DT_REF);
		if($CFG['cookie_domain'] && strpos($R['host'], $CFG['cookie_domain']) !== false) return true;
		if($CFG['com_domain'] && strpos($R['host'], $CFG['com_domain']) !== false) return true;
		if($DT['safe_domain']) {
			$tmp = explode('|', $DT['safe_domain']);
			foreach($tmp as $v) {
				if(strpos($R['host'], $v) !== false) return true;
			}
		}		
		$U = parse_url(DT_PATH);
		if(strpos($R['host'], str_replace('www.', '.', $U['host'])) !== false) return true;
		return false;
	}
	return true;
}

function is_clean($str) {
	foreach(array("$","\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#") as $v) {
		if(strpos($str, $v) !== false) return false;
	}
	return true;
}

function is_passport($passport) {
	if(word_count($passport) < 2 || word_count($passport) > 30) return false;
	return is_clean($passport);
}

function is_robot($ua = '') {
	return preg_match("/(spider|bot|crawl|slurp|lycos|robozilla)/i", $ua ? $ua : DT_UA);
}

function is_url($url) {
	return preg_match("/^(http|https)\:\/\/([a-z0-9_\-\.\/]{4,})(.*)$/i", $url);
}

function is_uri($url) {
	return (is_url($url) && strpos(cutstr($url, '://', '/'), DT_DOMAIN ? DT_DOMAIN : cutstr(DT_PATH, '://', '/')) !== false) ? true : false;
}

function is_ip($ip) {
	if(preg_match("/^([0-9]{1,3}\.){3}[0-9]{1,3}$/", $ip)) return 4;
	if(preg_match("/^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/", $ip)) return 6;
	return 0;
}

function is_mobile($mobile) {
	return preg_match("/^1[3|4|5|6|7|8|9]{1}[0-9]{9}$/", $mobile);
}

function is_md5($password) {
	return preg_match("/^[a-f0-9]{32}$/", $password);
}

function is_openid($id, $min = 10, $max = 32) {
	return preg_match("/^[0-9a-zA-Z\-_]{".$min.",".$max."}$/", $id);
}

function is_uuid($id) {
	return preg_match("/^[a-f0-9]{16,32}$/", $id);
}

function is_touch() {
	$ck = get_cookie('mobile');
	if($ck == 'pc') return 0;
	if($ck == 'screen') return 1;
	if($ck == 'touch' && is_mob()) return 1;
	return is_mob();
}

function is_mob($par = '') {
	return preg_match("/(iPhone|iPad|iPod|Android|Phone|mobile)/i", ($par ? $par : DT_UA)) ? 1 : 0;
}

function is_lnglat($map) {
	if(substr_count($map, ',') != 1) return false;
	list($lng, $lat) = explode(',', $map);
	if(!is_numeric($lng) || abs($lng) > 180) return false;
	if(!is_numeric($lat) || abs($lat) > 180) return false;
	return true;
}

function is_founder($userid) {
	global $CFG;
	$userid = intval($userid);
	if($userid < 1) return false;
	if(strpos($CFG['founderid'], ',') === false) {
		return $userid == $CFG['founderid'] ? true : false;
	} else {
		return strpos(','.$CFG['founderid'].',', ','.$userid.',') === false ? false : true;
	}
}

function debug() {
	global $db, $debug_starttime;
	$mtime = explode(' ', microtime());
	$s = number_format(($mtime[1] + $mtime[0] - $debug_starttime), 3);
	echo 'Processed in '.$s.' second(s), '.$db->querynum.' queries';
    if(function_exists('memory_get_usage')) echo ', Memory '.round(memory_get_usage()/1024/1024, 2).' M';
}

function dhttp($status, $exit = 1) {
	switch($status) {
		case '301': @header("HTTP/1.1 301 Moved Permanently"); break;
		case '403': @header("HTTP/1.1 403 Forbidden"); break;
		case '404': @header("HTTP/1.1 404 Not Found"); break;
		case '503': @header("HTTP/1.1 503 Service Unavailable"); break;
	}
	if($exit) exit;
}

function dcurl($url, $par = '', $header = array()) {
	if(function_exists('curl_init')) {
		$cur = curl_init($url);
		if($par) {
			curl_setopt($cur, CURLOPT_POST, 1);
			curl_setopt($cur, CURLOPT_POSTFIELDS, $par);
		}
		if($header) curl_setopt($cur, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($cur, CURLOPT_REFERER, DT_PATH);
		curl_setopt($cur, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($cur, CURLOPT_HEADER, 0);
		curl_setopt($cur, CURLOPT_TIMEOUT, 30);
		if(substr($url, 0, 8) == 'https://') {
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, 0);
		}
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, 1);
		$rec = curl_exec($cur);
		curl_close($cur);
		if($rec) return $rec;
		if(substr($url, 0, 8) == 'https://') return dcurl('http://'.substr($url, 8), $par);
	}
	return file_get($par ? $url.'?'.$par : $url);
}

function d301($url) {
	dhttp(301, 0);
	dheader($url);
}

// 提取字符串第一个非空（包含0）元素,返回，否则返回空
function getFirst($str,$fuhao){
    $arr =array_merge(array_filter(array_unique( explode("$fuhao",$str)))) ;
    //     foreach ($arr as $k=>$v){
    //     if($v!=''){unset($arr[$k]);}
    // }
    // die(var_dump($arr));
    // foreach ($arr as $k=>$v){   不对
    //     if($v!='') die(var_dump(111)); return $arr[$k];
    // }
    for($i=0;$i<count($arr);$i++){

        if($arr[$i]!="") {return $arr[$i];}
    }
    return $arr[0];
}
// 提取字符串非空元素，组成新字符串并且返回
function getAllStr($str,$fuhao){
    $arr =array_unique(explode("$fuhao",$str));
    // die(var_dump($arr));
    $NewArr=[];
    foreach ($arr as $k=>$v){
        if($v!=''){$NewArr[]=$v;}
    }
    // die(var_dump($NewArr));
    return $NewStr = $NewArr==[]?'':implode(',',$NewArr);

}
// dxsbb.com 用的函数
function getLastNumOrFirst($str,$fuhao){
    $arr =array_unique(explode("$fuhao",$str));
    // die(var_dump($arr));
    foreach ($arr as $k=>$v){
        if($v==''){unset($arr[$k]);}
    }
    $arr=array_merge($arr);
//   die(var_dump(($arr)));

    // die(var_dump( $arr[0]));
    return $arr[count($arr)-1] == 0 ? $arr[0]:$arr[count($arr)-1];

}
// diyifanwen.com 用的函数
function getLastNum($str,$fuhao){
    $arr =array_unique(explode("$fuhao",$str));
    // die(var_dump($arr));
    foreach ($arr as $k=>$v){
        if($v==''){unset($arr[$k]);}
    }
    $arr=array_merge($arr);
//   die(var_dump(($arr)));

    // die(var_dump( $arr[0]));
    return $arr[count($arr)-1];

}
function getLastStrOrFirst($str,$fuhao){
    $arr =array_unique(explode("$fuhao",$str));
    // die(var_dump($arr));
    foreach ($arr as $k=>$v){
        if($v==''){unset($arr[$k]);}
    }
    $arr=array_merge($arr);
//   die(var_dump(($arr)));

    // die(var_dump( $arr[0]));
    return $arr[count($arr)-1] == '' ? $arr[0]:$arr[count($arr)-1];

}

function get_maincat_1($catid, $moduleid, $level = -1) {
    $catid = intval($catid);
    $condition = $catid ? "parentid=$catid" : "moduleid=$moduleid AND parentid=0";
    if($level >= 0) $condition .= " AND level=$level";
    $cat = array();
    $arr1 = array();
    $result = DB::query("SELECT catid,catname,arrchildid,child,style,linkurl,item,thumb,icon FROM ".DT_PRE."category WHERE {$condition} ORDER BY listorder,catid ASC", 'CACHE');
//    var_dump($result);die();
    while($r = DB::fetch_array($result)) {
        if($r['arrchildid']!=''){

            $arr=explode(',',$r['arrchildid']);
//            echo "<pre>";
//            var_dump($arr);
            $arr1=[];
            foreach ($arr as $i=>$v){
                $result1 = DB::query("SELECT catid,catname,child,style,linkurl,item,thumb,icon,parentid FROM ".DT_PRE."category WHERE catid = $v ORDER BY listorder,catid ASC", 'CACHE');
                while($r1 = DB::fetch_array($result1)){
                    if ($r1['parentid']==$r['catid']){
                        $arr1[]=$r1;
                    }
                }
            }
//            die();
        }
        $r['arrchildid']='';
        $r['child1']=$arr1;
        $cat[] = $r;
    }
    return $cat;
}


//获取子分类并且以字符串展示
function get_cattoarr1($catid, $moduleid, $level = -1) {
    $catid = intval($catid);
    $condition = $catid ? "parentid=$catid" : "moduleid=$moduleid AND parentid=0";
    if($level >= 0) $condition .= " AND level=$level";

    $arr =[];
    $result = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE {$condition} ORDER BY listorder,catid ASC", 'CACHE');
//    var_dump($result);die();
    while($r = DB::fetch_array($result)) {
        $arr[] =$r["catname"];
//        var_dump($str);die();

//            echo "<pre>";
//            var_dump($arr);

                $result1 = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE parentid =".$r['catid']." ORDER BY listorder,catid ASC", 'CACHE');
                while($r1 = DB::fetch_array($result1)){
                    $str.='['.'"'.$r1['catname'].'"'.","."[";
                    $result2 = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE parentid =".$r1['catid']." ORDER BY listorder,catid ASC", 'CACHE');
                    while ($r2 = DB::fetch_array($result2)){
                        $str.= '"' .$r2['catname'].'",';
//                        var_dump($str);die();
                    }
                    $str = substr($str,0,-1);
                    $str.= "]],";
//                    var_dump($str);die();

                }
        $str = substr($str,0,-1);
        $str.= "],";
//        var_dump($str);die();


    }
//    $str.= "]";
    $str = substr($str,0,-1);
//    $str .="'";
    $array1 = preg_split("/]]],/",$str);
//    echo "<pre>";

//    var_dump($array1 );die();
    return $array1;
}



//获取子分类并且以字符串展示
function get_cattoarr($catid, $moduleid, $level = -1) {
    $catid = intval($catid);
    $condition = $catid ? "parentid=$catid" : "moduleid=$moduleid AND parentid=0";
    if($level >= 0) $condition .= " AND level=$level";

    $arr =[];
    $result = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE {$condition} ORDER BY listorder,catid ASC", 'CACHE');
//    var_dump($result);die();
    $row = $result->fetch_all(MYSQLI_ASSOC);
    for ($i=0;$i<count($row);$i++){

        $arr[$i][0]=$row[$i]['catname'];
//        var_dump($arr);die();
        $result1 = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE parentid =".$row[$i]['catid']." ORDER BY listorder,catid ASC", 'CACHE');
        $row1 = $result1->fetch_all(MYSQLI_ASSOC);
        for($j=0;$j<count($row1);$j++){
            $arr[$i][1][$j][0]=$row1[$j]['catname'];
//            echo "<pre>";
//                    var_dump($arr);die();
            $result2 = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE parentid =".$row1[$j]['catid']." ORDER BY listorder,catid ASC", 'CACHE');
            $row2 = $result2->fetch_all(MYSQLI_ASSOC);
            for ($k=0;$k<count($row2);$k++){
//                [$arr[$i][1][$j][1][$k]=$row2[$k]['catid'],$arr[$i][1][$j][1][$k]=$row2[$k]['catname']];
//var_dump([$arr[$i][1][$j][1][$k]=$row2[$k]['catid'],$arr[$i][1][$j][1][$k]=$row2[$k]['catname']]);die();
                $arr[$i][1][$j][1][$k][]=$row2[$k]['catid'];
                $arr[$i][1][$j][1][$k][]=$row2[$k]['catname'];

            }
//            echo $k;
//            echo "<pre>";
//            var_dump($arr); die();
        }
    }
//    echo "<pre>";
//    var_dump($arr);die();



//        var_dump($str);die();

//            echo "<pre>";
//            var_dump($arr);







//    echo "<pre>";

//    var_dump($array1 );die();
    return $arr;
}
?>