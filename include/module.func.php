<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function keyword($search, $username, $kw, $items, $moduleid) {
	global $DT;
	if($search) {
		if(strlen($kw) < $DT['min_kw'] || strlen($kw) > $DT['max_kw'] || strpos($kw, ' ') !== false || strpos($kw, '%') !== false) return;
		$kw = addslashes($kw);
		if($items > 1) {
			$r = DB::get_one("SELECT * FROM ".DT_PRE."keyword WHERE moduleid=$moduleid AND word='$kw' ORDER BY itemid ASC");
			if($r) {
				$items = $items > $r['items'] ? $items : $r['items'];
				$month_search = timetodate($r['updatetime'], 'Y-m') == timetodate(DT_TIME, 'Y-m') ? 'month_search+1' : '1';
				$week_search = timetodate($r['updatetime'], 'W') == timetodate(DT_TIME, 'W') ? 'week_search+1' : '1';
				$today_search = timetodate($r['updatetime'], 'Y-m-d') == timetodate(DT_TIME, 'Y-m-d') ? 'today_search+1' : '1';
				DB::query("UPDATE ".DT_PRE."keyword SET items='$items',updatetime='".DT_TIME."',total_search=total_search+1,month_search=$month_search,week_search=$week_search,today_search=$today_search WHERE itemid=$r[itemid]");
				DB::query("DELETE FROM ".DT_PRE."keyword WHERE moduleid=$moduleid AND word='$kw' AND itemid>$r[itemid]");
			} else {
				$letter = trim(gb2py($kw));
				$status = $search == 2 ? 2 : 3;
				if(strlen($letter) < 2) $status = 2;
				DB::query("INSERT INTO ".DT_PRE."keyword (moduleid,word,keyword,letter,items,updatetime,total_search,month_search,week_search,today_search,status) VALUES ('$moduleid','$kw','$kw','$letter','$items','".DT_TIME."','1','1','1','1','$status')");
			}
		}
		if(check_name($username)) DB::query("INSERT INTO ".DT_PRE."keyword_record (moduleid,keyword,username,items,addtime) VALUES ('$moduleid','$kw','$username','$items','".DT_TIME."')");
	}
}

function bansearch() {
	global $kw, $moduleid;
	if($kw) {
		$arr = cache_read('bansearch-'.$moduleid.'.php');
		if(!$arr) return false;
		if(in_array($kw, $arr)) return true;
		foreach($arr as $k=>$v) {
			if(strpos($v, '*') !== false) {
				if(substr($v, 0, 1) == '*') {
					if(stripos($kw, substr($v, 1)) !== false) return true;
				} else {
					$i = $j = 0;
					foreach(explode('*', $v) as $w) {
						$w = trim($w);
						if(!$w) continue;
						$i++;
						if(stripos($kw, $w) !== false) $j++;
					}
					if($j > 0 && $j == $i) return true;
				}
			}
		}
	}
	return false;
}

function history_log($item, $moduleid, $modules) {
	global $_username;
	if($item && $_username && $item['username'] != $_username && in_array($moduleid, explode(',', $modules))) {
		$itemid = $moduleid == 4 ? $item['userid'] : $item['itemid'];
		if($itemid) {
			$title = addslashes($moduleid == 4 ? $item['company'] : $item['title']);
			$thumb = is_url($item['thumb']) ? $item['thumb'] : '';
			$author = $item['username'];
			DB::query("REPLACE INTO ".DT_PRE."history (mid,tid,username,author,title,thumb,lasttime) VALUES ('$moduleid','$itemid','$_username','$author','$title','$thumb','".DT_TIME."')");
		}
	}
}

function money_add($username, $amount) {
	if($username && $amount) {
		if($amount < 0) {
			$r = DB::get_one("SELECT money FROM ".DT_PRE."member WHERE username='$username'");
			if($r['money'] < abs($amount)) {
				set_cookie('auth', '');
				dhttp(403, 0);
				dalert('HTTP 403 Forbidden - Bad Data', DT_PATH);
			}
		}
		DB::query("UPDATE ".DT_PRE."member SET money=money+{$amount} WHERE username='$username'");
		userclean($username);
	}
}

function money_record($username, $amount, $bank, $editor, $reason, $note = '') {
	if($username && $amount) {
		$r = DB::get_one("SELECT money FROM ".DT_PRE."member WHERE username='$username'");
		$balance = $r['money'];
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		DB::query("INSERT INTO ".DT_PRE."finance_record (username,bank,amount,balance,addtime,reason,note,editor) VALUES ('$username','$bank','$amount','$balance','".DT_TIME."','$reason','$note','$editor')");
	}
}

function credit_add($username, $amount) {
	if($username && $amount) {
		DB::query("UPDATE ".DT_PRE."member SET credit=credit+{$amount} WHERE username='$username'");
		userclean($username);
	}
}

function credit_record($username, $amount, $editor, $reason, $note = '') {
	global $DT;
	if($DT['log_credit'] && $username && $amount) {
		$r = DB::get_one("SELECT credit FROM ".DT_PRE."member WHERE username='$username'");
		$balance = $r['credit'];
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		DB::query("INSERT INTO ".DT_PRE."finance_credit (username,amount,balance,addtime,reason,note,editor) VALUES ('$username','$amount','$balance','".DT_TIME."','$reason','$note','$editor')");
	}
}

function sms_add($username, $amount) {
	if($username && $amount) {
		DB::query("UPDATE ".DT_PRE."member SET sms=sms+{$amount} WHERE username='$username'");
		userclean($username);
	}
}

function sms_record($username, $amount, $editor, $reason, $note = '') {
	if($username && $amount) {
		$r = DB::get_one("SELECT sms FROM ".DT_PRE."member WHERE username='$username'");
		$balance = $r['sms'];
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		DB::query("INSERT INTO ".DT_PRE."finance_sms (username,amount,balance,addtime,reason,note,editor) VALUES ('$username','$amount','$balance','".DT_TIME."','$reason','$note','$editor')");
	}
}

function sms_send($mobile, $message) {
	global $DT, $_sms;
	$message = strip_sms($message);
	$word = word_count($message);
	$sms_num = ceil($word/$DT['sms_len']);
	if($sms_num <= $_sms) {
		$sms_code = trim(send_sms($mobile, $message, $word));
		if(strpos($sms_code, $DT['sms_ok']) !== false) {
			$num = intval(cutstr($sms_code, '/',  '/'));
			if($num < 1) $num = 1;
			$_sms = $_sms - $num;
			return $num;
		}
	}
	return 0;
}

function sectotime($sec) {
	if($sec < 10) return '00:0'.$sec;
	if($sec < 60) return '00:'.$sec;
	if($sec < 3600) return sprintf('%02s',intval($sec/60)).substr(sectotime($sec%60), -3);
	return intval($sec/3600).':'.sectotime($sec%3600);
}

function sectoread($seconds) {
	//include load('include.lang');
	$date = '';
	if($seconds > 0) {
		$t = intval($seconds/86400);
		if($t) {
			$date .= $t.'天';
			$seconds = $seconds%86400;
		}
		$t = intval($seconds/3600);
		if($t) {
			$date .= $t.'小时';
			$seconds = $seconds%3600;
		}
		$t = intval($seconds/60);
		if($t) {
			$date .= $t.'分';
			$seconds = $seconds%60;
		}
		if($seconds) {
			$date .= $seconds.'秒';
		}
	}
	return $date;
}

function timetoread($time, $type = '5') {
	if(!is_numeric($time)) return $time;
	if($time < 1) return 'N/A';
	if($time > DT_TIME) return timetodate($time, $type);
	$sec = DT_TIME - $time;
	if($sec < 60) return '刚刚';
	if($sec < 3600) return intval($sec/60).'分钟前';
	if($sec < 21600) return intval($sec/3600).'小时前';
	if(timetodate($time, 'Ymd') == timetodate(DT_TIME, 'Ymd')) return '今天 '.timetodate($time, 'H:i');
	if(timetodate($time, 'Ymd') == timetodate(DT_TIME - 86400, 'Ymd')) return '昨天 '.timetodate($time, 'H:i');
	if(timetodate($time, 'Y') == timetodate(DT_TIME, 'Y')) return timetodate($time, 'm-d H:i');
	return timetodate($time, $type);
}

function get_intro($content, $length = 0, $suffix = '', $start = 0) {
	if($length) {
		$intro = trim(strip_tags($content));
		$intro = preg_replace("/&([a-z]{1,});/", '', $intro);
		$intro = str_replace(array("\r", "\n", "\t", '  '), array('', '', '', ''), $intro);
		return dsubstr($intro, $length, $suffix, $start);
	} else {
		return '';
	}
}

function get_module_setting($moduleid, $key = '') {
	$M = cache_read('module-'.$moduleid.'.php');
	return $key ? $M[$key] : $M;
}

function get_company_setting($userid, $key = '', $cache = '') {
	if($key) {
		$r = DB::get_one("SELECT * FROM ".DT_PRE."company_setting WHERE userid=$userid AND item_key='$key'", $cache);
		return $r ? $r['item_value'] : '';
	} else {
		$setting = array();
		$query = DB::query("SELECT * FROM ".DT_PRE."company_setting WHERE userid=$userid", $cache);
		while($r = DB::fetch_array($query)) {
			$setting[$r['item_key']] = $r['item_value'];
		}
		return $setting;
	}
}

function get_company_home($userid, $type = '', $cache = '') {
	$condition = "userid=$userid";
	if(in_array($type, array('menu', 'side', 'main'))) $condition .= " AND type='$type'";
	if($cache) $condition .= " AND status>0";
	$home = array();
	$query = DB::query("SELECT * FROM ".DT_PRE."company_home WHERE {$condition} ORDER BY listorder ASC", $cache);
	while($r = DB::fetch_array($query)) {
		$home[$r['type']][$r['file']]['name'] = $r['name'];
		$home[$r['type']][$r['file']]['pagesize'] = $r['pagesize'];
		$home[$r['type']][$r['file']]['listorder'] = $r['listorder'];
		$home[$r['type']][$r['file']]['status'] = $r['status'];
	}
	return $home;
}

function anti_spam($string) {
	global $DT;
	if($DT['anti_spam'] && preg_match("/^[a-z0-9_@\-\s\/\.\,\(\)\+]+$/i", $string)) {
		return '<img src="'.DT_PATH.'api/image'.DT_EXT.'?auth='.encrypt($string, DT_KEY.'SPAM').'" align="absmddle"/>';
	} else {
		return $string;
	}
}

function hide_info($str, $type = 'username') {
	if($type == 'ip') {		
		if(is_ip($str)) {			
			$tmp = explode('.', $str);
			return $tmp[0].'.'.$tmp[1].'.*.*';
		}
	} elseif($type == 'username') {
		if(check_name($str)) {
			$len = strlen($str);
			$tmp = '';
			for($i = 0; $i < $len; $i++) {
				$tmp .= ($i == 0 || $i == $len - 1) ? $str[$i] : '*';
			}
			return $tmp;
		}
	} else if($type == 'mobile') {
		if(is_mobile($str)) return substr($str, 0, 3).'****'.substr($str, -4);
	}
	return $str;
}

function check_pay($moduleid, $itemid) {
	global $_username, $MOD;
	$condition = "mid=$moduleid AND tid=$itemid AND username='$_username'";
	if($MOD['fee_period']) $condition .= " AND paytime>".(DT_TIME - $MOD['fee_period']*60);
	return DB::get_one("SELECT itemid FROM ".DT_PRE."finance_pay WHERE {$condition}");
}

function check_sign($string, $sign) {
	return $sign == crypt_sign($string);
}

function crypt_sign($string) {
	return strtoupper(md5(md5(DT_IP.$string.DT_KEY.'SIGN')));
}

function cache_hits($moduleid, $itemid) {
	if(@$fp = fopen(DT_CACHE.'/hits-'.$moduleid.'.php', 'a')) {
		flock($fp, LOCK_EX);
		fwrite($fp, $itemid.' ');
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

function update_hits($moduleid, $table) {
	$hits = trim(file_get(DT_CACHE.'/hits-'.$moduleid.'.php'));
	file_put(DT_CACHE.'/hits-'.$moduleid.'.php', ' ');
	file_put(DT_CACHE.'/hits-'.$moduleid.'.dat', DT_TIME);
	if($hits) {
		$tmp = array_count_values(explode(' ', $hits));
		$arr = array();
		foreach($tmp as $k=>$v) {
			$arr[$v] .= $k ? ','.$k : '';
		}
		$id = $moduleid == 4 ? 'userid' : 'itemid';
		foreach($arr as $k=>$v) {
			DB::query("UPDATE LOW_PRIORITY {$table} SET `hits`=`hits`+".$k." WHERE `$id` IN (0".$v.")", 'UNBUFFERED');
		}
	}
}

function gender($gender, $type = 0) {
	global $L;
	if($type) return $gender == 1 ? $L['man'] : $L['woman'];
	return $gender == 1 ? $L['sir'] : $L['lady'];
}

function online($user, $type = 0) {
	$r = DB::get_one("SELECT online FROM ".DT_PRE."online WHERE `".($type ? 'username' : 'userid')."`='$user'");
	if($r) return $r['online'] ? 1 : -1;
	return 0;
}

function followed($userid, $uid = 0) {
	global $_userid;
	$uid > 0 or $uid = $_userid;
	if($uid < 1 || $userid < 1 || $uid == $userid) return false;
	$r = DB::get_one("SELECT itemid FROM ".DT_PRE."follow WHERE userid=$uid AND fuserid=$userid", 'CACHE');
	return $r ? true : false;
}

function friended($userid, $uid = 0) {
	global $_userid;
	$uid > 0 or $uid = $_userid;
	if($uid < 1 || $userid < 1 || $uid == $userid) return false;
	$r = DB::get_one("SELECT itemid FROM ".DT_PRE."friend WHERE userid=$uid AND fuserid=$userid");
	return $r ? true : false;
}

function agented($pusername, $username) {
	$r = DB::get_one("SELECT itemid FROM ".DT_PRE."agent WHERE pusername='$pusername' AND username='$username' AND status=3");
	return $r ? true : false;
}

function numtoread($num) {
	if($num < 10000) return $num;
	if($num < 100000000) return round($num/10000, 1).'万';
	return round($num/100000000, 1).'亿';
}

function fix_link($url) {
	$url = trim($url);
	if(strlen($url) < 10) return '';
	return strpos($url, '://') === false  ? 'http://'.$url : $url;
}

function vip_year($fromtime) {
	return $fromtime ? intval((DT_TIME - $fromtime)/86400/365) + 1 : 1;
}

function valid_name($validate) {
	if($validate == 2) return '机构认证';
	if($validate == 1) return '个人认证';
	return '未认证';
}

function get_gids($type = 1) {
	global $GROUP;
	$GROUP or $GROUP = cache_read('group.php');
	$ids = '';
	foreach($GROUP as $k=>$v) {
		if($v['type'] == $type) $ids .= ','.$k;
	}
	return $ids ? substr($ids, 1) : '0';
}

function get_albums($item) {
	$thumbs = array();
	if($item['thumb']) $thumbs[] = $item['thumb'];
	if(isset($item['thumb1']) && $item['thumb1'] && strpos($item['thumbs'], $item['thumb1']) === false) $thumbs[] = $item['thumb1'];
	if(isset($item['thumb2']) && $item['thumb2'] && strpos($item['thumbs'], $item['thumb2']) === false) $thumbs[] = $item['thumb2'];
	foreach(explode('|', $item['thumbs']) as $v) {
		if($v) $thumbs[] = $v;
	}
	$i = count($thumbs);
	while($i++ < 5) {
		$thumbs[] = DT_STATIC.'image/nopic.thumb.png';;
	}
	return $thumbs;
}

function get_thumbs($item = array()) {
	$thumbs = array();
	if($item) {
		if($item['thumb']) $thumbs[] = $item['thumb'];
		if(isset($item['thumb1']) && $item['thumb1'] && strpos($item['thumbs'], $item['thumb1']) === false) $thumbs[] = $item['thumb1'];
		if(isset($item['thumb2']) && $item['thumb2'] && strpos($item['thumbs'], $item['thumb2']) === false) $thumbs[] = $item['thumb2'];
		foreach(explode('|', $item['thumbs']) as $v) {
			if($v) $thumbs[] = $v;
		}
	}
	return $thumbs;
}

function xml_linkurl($linkurl, $modurl = '') {
	if(strpos($linkurl, '://') === false) $linkurl = $modurl.$linkurl;
	return str_replace('&', '&amp;', $linkurl);
}

function sort_type($TYPE) {
	$p = $c = array();
	foreach($TYPE as $v) {
		if($v['parentid']) {
			$c[$v['parentid']][] = $v;
		} else {
			$p[] = $v;
		}
	}
	return array($p, $c);
}

function list_user($tags, $fields = '*', $key = 'userid', $tb = 'member', $cache = 'CACHE') {
	if(!$tags || !isset($tags[0][$key])) return $tags;
	$arr1 = $arr2 = $arr3 = array();
	foreach($tags as $k=>$v) {
		if($fields == 'all') {
			$tags[$k]['member'] = userinfo($v['username']);
		} else {
			if(!$v[$key]) continue;
			$arr1[$k] = $v[$key];
			$arr2[$v[$key]] = $v[$key];
		}
	}
	if(!$arr2) return $tags;
	if(strpos($key, 'id') === false) {
		$key = 'username';
		$condition = "username IN ('".implode("','", $arr2)."')";
	} else {
		$key = 'userid';
		$condition = "userid IN (".implode(',', $arr2).")";
	}
	if($fields != '*' && strpos($fields, $key) === false) $fields = $key.','.$fields;
	$result = DB::query("SELECT {$fields} FROM ".DT_PRE.$tb." WHERE {$condition}", $cache);
	while($r = DB::fetch_array($result)) {
		$arr3[$r[$key]] = $r;
	}
	foreach($arr1 as $k=>$v) {
		$tags[$k]['member'] = isset($arr3[$v]) ? $arr3[$v] : array();
	}
	return $tags;
}

function update_user($member, $item, $fileds = array('groupid','vip','validated','company','truename','telephone','mobile','address','qq','wx','ali','skype','areaid')) {
	$update = '';
	if(isset($item['email']) && $item['email'] != $member['mail']) $update .= ",email='".addslashes($member['mail'])."'";
	foreach($fileds as $v) {
		if(isset($item[$v]) && $item[$v] != $member[$v]) $update .= ",$v='".addslashes($member[$v])."'";
	}
	return $update;
}

function highlight($str) {
	return '<span class="highlight">'.$str.'</span>';
}

function parse_face($str) {
	global $faces;
	if(strpos($str, ')') === false) return $str;
	if(strpos($str, ':') === false) return $str;
	if(preg_match_all("/\:([^\)]{3,15})\)/i", $str, $m)) {
		if(!$faces) include DT_ROOT.'/file/config/face.inc.php';
		foreach($m[0] as $u) {
			$k = substr($u, 1, 3);
			if(isset($faces[$k])) $str = str_replace($u, '<img src="'.DT_PATH.'file/face/'.$k.'.png" width="24" height="24" title="'.$faces[$k].'"/>', $str);
		}
	}
	return $str;
}

function parse_link($str) {
	global $DT_PC;
	if(preg_match_all("/([http|https]+)\:\/\/([a-z0-9\/\-\_\.\,\?\&\#\=\%\+\;]{4,})/i", $str, $m)) {
		foreach($m[0] as $u) {
			$ext = file_ext($u);
			if(preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", $ext) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
				$str = str_replace($u, '<img src="'.$u.'" onload="if(this.width>600)this.width=600;" onclick="'.($DT_PC ? 'window.open(this.src)' : '').';"/>', $str);
			} else if(preg_match("/^(mp4)$/i", $ext) && !preg_match("/([\?\&\=]{1,})/i", $u)) {
				$str = str_replace($u, '<video src="'.$u.'" width="480" height="270" controls="controls"></video>', $str);
			} else {
				$str = str_replace($u, ' <a href="'.$u.'" '.($DT_PC ? 'target="_blank"' : 'rel="external"').' class="b">'.$u.'</a> ', $str);
			}
		}
	}
	return $str;
}

function baidu_push($url, $key) {
	$url = trim($url);
	if($key && is_uri($url)) {
		$site = cutstr($url, '', '://').'://'.cutstr($url, '://', '/');
		$api = 'http://data.zz.baidu.com/urls?site='.$site.'&token='.$key;
		$ch = curl_init();
		$options =  array(CURLOPT_URL => $api, CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_POSTFIELDS => $url, CURLOPT_HTTPHEADER => array('Content-Type:text/plain'));
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		$result = 'success';
		$error = 'unkonwn';
		if(strpos($result, 'success') !== false) {
			$error = '';
		} elseif(strpos($result, 'error') !== false) {
			$error = cutstr($result, '"message":"', '"');
		}
		DB::query("INSERT INTO ".DT_PRE."baidu_push (url,error,addtime) VALUES ('$url','$error','".DT_TIME."')");
		return $error;
	}
	return false;
}

function url2video($u) {
	$d = cutstr($u, '://', '/');
	$h = cutstr($u, '', '://').'://';
	switch($d) {
		case 'v.youku.com':
			if(strpos($u, '/embed/') !== false) return $u;
			$p = cutstr($u, 'id_', '.html');
			if($p) return $h.'player.youku.com/embed/'.$p;
		break;
		case 'player.youku.com':
			if(strpos($u, '/embed/') !== false) return $u;
			$p = cutstr($u, 'sid/', '/');
			if($p) return $h.'player.youku.com/embed/'.$p;
			$p = cutstr($u, 'embed/', strpos($u, "'") !== false ? "'" : '"');
			if($p) return $h.'player.youku.com/embed/'.$p;
		break;
		case 'imgcache.qq.com':
		case 'static.v.qq.com':
		case 'v.qq.com':
			if(strpos($u, '/iframe/') !== false) return $u;
			if(strpos($u, 'page/') !== false && strpos($u, '.html') !== false) {
				$p = cutstr($u, 'page/', '.html');
				if($p) return $h.'v.qq.com/txp/iframe/player.html?vid='.$p;
			}
			if(strpos($u, 'vid=') !== false && strpos($u, '&') !== false) {
				$p = cutstr($u, 'vid=', '&');
				if($p) return $h.'v.qq.com/iframe/player.html?vid='.$p.'&tiny=0&auto=0';
			}
		break;
		case 'www.iqiyi.com':
			if(strpos($u, 'shareplay.html') !== false) return $u;
			$c = dcurl($u);
			if($c) {
				$p1 = cutstr($c, 'data-player-videoid="', '"');
				$p2 = cutstr($c, 'data-player-tvid="', '"');
				if($p1 && $p2) return $h.'m.iqiyi.com/shareplay.html?vid='.$p1.'&tvid='.$p2;
			}
		break;
		case 'open.iqiyi.com':
			if(strpos($u, 'shareplay.html') !== false) return $u;
			$p1 = cutstr($u, 'vid=', '&');
			$p2 = cutstr($u, 'tvId=', '&');
			if($p1 && $p2) return $h.'m.iqiyi.com/shareplay.html?vid='.$p1.'&tvid='.$p2;
		break;
		case 'player.video.qiyi.com':
			if(strpos($u, 'shareplay.html') !== false) return $u;
			$p1 = cutstr($u, 'player.video.qiyi.com/', '/');
			$p2 = cutstr($u, 'tvId=', '-');
			if($p1 && $p2) return $h.'m.iqiyi.com/shareplay.html?vid='.$p1.'&tvid='.$p2;
		break;
		case 'www.bilibili.com':
			if(strpos($u, 'player.html') !== false) return $u;
			$p = cutstr($u, '/video/', strpos($u, '?') === false ? '/' : '?');
			$p = cutstr($p, '', '/');
			if($p) return $h.'player.bilibili.com/player.html?bvid='.$p;
		break;
		case 'live.bilibili.com':
			if(strpos($u, 'player.html') !== false) return $u;
			$p = cutstr($u, 'bilibili.com/', '?');
			if($p) return $h.'www.bilibili.com/blackboard/live/live-activity-player.html?cid='.$p;
		break;
		case 'www.acfun.cn':
			if(strpos($u, '/player/') !== false) return $u;
			$p = cutstr($u, '/v/', strpos($u, '?') === false ? '/' : '?');
			if($p) return $h.'www.acfun.cn/player/'.$p;
		break;
		case 'www.youtube.com':
			if(strpos($u, '/embed/') !== false) return $u;
			$p = cutstr($u, 'v=', '&');
			if($p) return $h.'www.youtube.com/embed/'.$p;
		break;
		case 'www.huya.com':
			if(strpos($u, '/iframe/') !== false) return $u;
			$p = cutstr($u, 'www.huya.com/', '/');
			if($p) return $h.'liveshare.huya.com/iframe/'.$p;
		break;
		case 'www.douyu.com':
			if(strpos($u, '/share/') !== false) return $u;
			$p = cutstr($u, 'www.douyu.com/', '/');
			if($p) return $h.'staticlive.douyucdn.cn/common/share/play.swf?room_id='.$p;
		break;
	}
	return $u;
}

function player($url, $width = 0, $height = 0, $auto = 0, $extend = '') {
	if(is_uri($url)) {
		$ext = file_ext($url);
		if($ext == 'mp4') return '<video src="'.$url.'"'.($width ? ' width="'.$width.'"' : '').($height ? ' height="'.$height.'"' : '' ).($auto ? ' autoplay="autoplay"' : '').' controls="controls" style="object-fit:contain;"'.$extend.'></video>';
		if($ext == 'mp3') return '<audio src="'.$url.'"'.($auto ? ' autoplay="autoplay"' : '').' controls="controls"></audio>';
	}
	return '';
}
?>