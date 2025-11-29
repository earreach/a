<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function mobile_pages($total, $page = 1, $perpage = 20, $demo = '') {
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
	if($DT['max_list'] > 0 && $total > $DT['max_list'] && strpos($demo_url, '/list') !== false && !defined('TOHTML')) $total = $DT['max_list'];
	if($DT['max_search'] > 0 && $total > $DT['max_search'] && strpos($demo_url, '/search') !== false && !defined('TOHTML')) $total = $DT['max_search'];
	$pages = '';
	$_page = $page <= 1 ? $total : ($page - 1);
	$url = str_replace('{destoon_page}', $_page, $demo_url);
	$pages .= '<a href="'.$url.'" rel="external" id="page-prev">&laquo; '.$L['prev_page'].'</a> ';
	if(strpos($demo_url, 'javascript') === false) {
		$pages .= '<a href="javascript:GoPage('.$total.', '.$items.', \''.$demo_url.'\');" id="page-goto"><b>'.$page.'</b>/'.$total.'</a> ';
	} else {		
		$pages .= '<a href="'.$url.'" id="page-goto"><b>'.$page.'</b>/'.$total.'</a> ';
	}
	$_page = $page >= $total ? 1 : $page + 1;
	$url = str_replace('{destoon_page}', $_page, $demo_url);
	$pages .= '<a href="'.$url.'" rel="external" id="page-next">'.$L['next_page'].' &raquo;</a> ';
	return $pages;
}

function m301($moduleid, $catid = 0, $itemid = 0, $page = 1) {
	global $MODULE;
	$url = '';
	if($itemid) {
		if($moduleid > 4) {
			$item = DB::get_one("SELECT * FROM ".get_table($moduleid)." WHERE itemid=$itemid");
			if($item && $item['status'] > 2) {
				$url = $MODULE[$moduleid]['mobile'].itemurl($item, $page > 1 ? $page : '');
			}
		}
	} else if($catid) {
		$CAT = get_cat($catid);
		$url = $MODULE[$moduleid]['mobile'].listurl($CAT, $page > 1 ? $page : '');
	} else {
		$url = $MODULE[$moduleid]['mobile'];
	}
	if($moduleid == 4) {
		global $username, $DT_URL;
		if(check_name($username)) $url = userurl($username, cutstr($DT_URL, $username.'&', '.html'));
	}
	if($url) d301($url);
}

function input_trim($wd) {
	return trim(urldecode(str_replace('%E2%80%86', '', urlencode($wd))));
}

function is_pc() {
	if(DT_DEBUG || is_robot()) return false;
	$UA = strtoupper(DT_UA);
	if(strpos($UA, 'WINDOWS NT') !== false) {
		if(strpos($UA, 'MICROMESSENGER/') !== false) return false;//WX
		return true;
	}
	return false;
}
?>