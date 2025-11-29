<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function dhtmlspecialchars($string) {
	if(is_array($string)) {
		return array_map('dhtmlspecialchars', $string);
	} else {
		$string = htmlspecialchars($string, ENT_QUOTES, DT_CHARSET == 'GBK' ? 'GB2312' : 'UTF-8');
		return str_replace('&amp;', '&', $string);
	}
}

function dsafe($string, $type = 1) {
	if(is_array($string)) {
		return $type ? array_map('dsafe', $string) : array_map('dsafe_sp', $string);
	} else {
		if($type) {
			$string = str_replace('<i></i>', '', $string);
			$string = preg_replace("/\<\!\-\-([\s\S]*?)\-\-\>/", "", $string);
			$string = preg_replace("/\/\*([\s\S]*?)\*\//", "", $string);
			$string = preg_replace("/&#([a-z0-9]{1,})/i", "<i></i>&#\\1", $string);
			$string = preg_replace_callback("/(about|frame|link|meta|textarea|eval|alert|confirm|prompt|cookie|document|newline|colon)/i", 'dsafe_wd', $string);
			$string = preg_replace_callback("/on([a-z]{2,})([\(|\=|\s]+)/i", 'dsafe_wd', $string);
			$string = preg_replace_callback("/s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t/i", 'dsafe_wd', $string);
			$string = preg_replace_callback("/d[\s]*a[\s]*t[\s]*a[\s]*\:/i", 'dsafe_wd', $string);
			$string = preg_replace_callback("/b[\s]*a[\s]*s[\s]*e/i", 'dsafe_wd', $string);
			$string = preg_replace_callback("/e[\\\]*x[\\\]*p[\\\]*r[\\\]*e[\\\]*s[\\\]*s[\\\]*i[\\\]*o[\\\]*n/i", 'dsafe_wd', $string);
			$string = preg_replace_callback("/i[\\\]*m[\\\]*p[\\\]*o[\\\]*r[\\\]*t/i", 'dsafe_wd', $string);
			$string = preg_replace(array("/<style/i","/\\\x/i"), array("<sty1e","\<i></i>x"), $string);
			$string = str_replace(array('isShowa<i></i>bout', 'co<i></i>ntrols'), array('isShowAbout', 'controls'), $string);
			return $string;
		} else {
			return str_replace(array('<i></i>', '<sty1e'), array('', '<style'), $string);
		}
	}
}

function dsafe_wd($m) {
	if(is_array($m) && isset($m[0])) {
		$wd = substr($m[0], 0, 1).'<i></i>'.substr($m[0], 1);
		return $wd;
	}
	return '';
}

function dsafe_sp($string) {
	return dsafe($string, 0);
}

function strip_sql($string, $type = 1) {
	if(is_array($string)) {
		return $type ? array_map('strip_sql', $string) : array_map('strip_sql_sp', $string);
	} else {
		if($type) {
			$string = preg_replace("/\/\*([\s\S]*?)\*\//", "", $string);
			$string = preg_replace("/0x([a-fA-d0-9]{2,})/", '0&#120;\\1', $string);
			$string = preg_replace("/0X([a-fA-d0-9]{2,})/", '0&#88;\\1', $string);
			$string = preg_replace_callback("/(select|update|replace|delete|drop)([\s\S]*?)(".DT_PRE."|from)/i", 'strip_wd', $string);
			$string = preg_replace_callback("/(load_file|substring|substr|reverse|trim|space|left|right|mid|lpad|concat|concat_ws|make_set|ascii|bin|oct|hex|ord|char|conv)([^a-z]?)\(/i", 'strip_wd', $string);
			$string = preg_replace_callback("/(union|where|having|outfile|dumpfile|".DT_PRE.")/i", 'strip_wd', $string);
			return $string;
		} else {
			return str_replace(array('&#95;','&#100;','&#101;','&#103;','&#105;','&#109;','&#110;','&#112;','&#114;','&#115;','&#116;','&#118;','&#120;','&#69;','&#82;','&#84;','&#88;'), array('_','d','e','g','i','m','n','p','r','s','t','v','x','E','R','T','X'), $string);
		}
	}
}

function strip_sql_sp($string) {
	return strip_sql($string, 0);
}

function strip_wd($m) {
	if(is_array($m) && isset($m[1])) {
		$wd = substr($m[1], 0, -1).'&#'.ord(substr($m[1], -1)).';';
		if(isset($m[3])) return $wd.$m[2].$m[3];
		if(isset($m[2])) return $wd.$m[2].'(';
		return $wd;
	}
	return '';
}

function strip_uri($uri) {
	if(strpos($uri, '%') !== false) {
		while($uri != urldecode($uri)) {
			$uri = urldecode($uri);
		}
	}
	if(strpos($uri, '<') !== false || strpos($uri, "'") !== false || strpos($uri, '"') !== false) {
		dhttp(403, 0);
		dalert('HTTP 403 Forbidden - Bad URL', DT_PATH);
	}
}

function strip_kw($kw, $max = 0) {
	$kw = dhtmlspecialchars(trim(urldecode($kw)));
	if($kw) {
		if(strpos($kw, '%') !== false) return '';
		$kw = str_replace(array("'", '"', '&', '|', '*', ';', ',', '{', '}', '(', ')'), array('', '', '', '', '', '', '', '', '', '', ''), $kw);
		$max = intval($max);
		if($max > 0 && strlen($kw) > $max) $kw = dsubstr($kw, $max);
	}
	return $kw;
}

function strip_key($array) {
	foreach($array as $k=>$v) {
		if(!preg_match("/^[a-z0-9_\-]{1,64}$/i", $k)) {
			dhttp(403, 0);
			dalert('HTTP 403 Forbidden - Bad DATA', DT_PATH);
		}
		if(is_array($v)) strip_key($v);
	}
}

function strip_str($string) {
	return str_replace(array('\\','"', "'"), array('', '', ''), $string);
}
?>