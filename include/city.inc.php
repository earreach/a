<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$AREA = cache_read('area.php');
$city_arr = array();
$city_set = get_cookie('city');
$http_host = get_env('host');
if($city_set) {
	list($cityid, $city_domain) = explode('|', $city_set);
	$cityid = intval($cityid);
	if(strpos(DT_PATH, $http_host) === false && strpos($city_domain, $http_host) === false) {
		$city_arr = $db->get_one("SELECT * FROM {$DT_PRE}city WHERE domain='http://".$http_host."/' OR domain='https://".$http_host."/'", 'CACHE');
		if($city_arr) {
			set_cookie('city', $city_arr['areaid'].'|'.$city_arr['domain'], DT_TIME + 86400*30);
			$cityid = $city_arr['areaid'];
		}
	}
	if($city_domain && substr($http_host, 0 ,4) == 'www.' && strpos($DT_URL, '/api/') === false && strpos($DT_URL, '/api/city') === false) {
		$cityid = 0;
		$city_domain = '';
	}
	if($city_domain && $DT_URL == DT_PATH) dheader($city_domain);
} else {
	$cityid = 0;
	if(strpos(DT_PATH, $http_host) === false) {
		$city_arr = $db->get_one("SELECT * FROM {$DT_PRE}city WHERE domain='http://".$http_host."/' OR domain='https://".$http_host."/'", 'CACHE');
		if($city_arr) {
			set_cookie('city', $city_arr['areaid'].'|'.$city_arr['domain'], $DT_TIME + 30*86400);
			$cityid = $city_arr['areaid'];
		}
	}
	if($DT['city_ip'] && !defined('DT_ADMIN') && !$DT_BOT && !$cityid) {
		$iparea = ip2area($DT_IP);
		$result = $db->query("SELECT * FROM {$DT_PRE}city ORDER BY areaid DESC", 'CACHE');
		while($r = $db->fetch_array($result)) {
			if(preg_match("/".$r['name'].($r['iparea'] ? '|'.$r['iparea'] : '')."/i", $iparea)) {
				set_cookie('city', $r['areaid'].'|'.$r['domain'], $DT_TIME + 30*86400);
				$cityid = $r['areaid'];
				if($r['domain'] && strpos($DT_URL, '/api/') === false) {
					$city_url = str_replace(DT_PATH, $r['domain'], $DT_URL);
					if($city_url != $DT_URL) dheader($city_url);
				}
				$city_arr = $r;
				break;
			}
		}
	}
}
if($cityid) {
	$city_arr or $city_arr = $db->get_one("SELECT * FROM {$DT_PRE}city WHERE areaid=$cityid", 'CACHE');
	if(!defined('DT_ADMIN')) {
		if($city_arr['seo_title']) {		
			$DT['seo_title'] = $city_sitename = $city_arr['seo_title'];
		} else {
			$citysite = lang($L['citysite'], array($city_arr['name']));
			$DT['seo_title'] = $citysite.$DT['seo_delimiter'].$DT['seo_title'];
			$city_sitename = $citysite.$DT['seo_delimiter'].$DT['sitename'];
		}
		if($city_arr['seo_keywords']) $DT['seo_keywords'] = $city_arr['seo_keywords'];
		if($city_arr['seo_description']) $DT['seo_description'] = $city_arr['seo_description'];
	}
	$city_name = $city_arr['name'];
	$city_domain = $city_arr['domain'];
	$city_template = $city_arr['template'];
}
if($city_domain) {
	foreach($MODULE as $k=>$v) {
		if($v['islink']) continue;
		$MODULE[$k]['linkurl'] = $k == 1 ? $city_domain : $city_domain.$v['moduledir'].'/';
		$MODULE[$k]['mobile'] = $k == 1 ? $city_domain.'mobile/' : $city_domain.'mobile/'.$v['moduledir'].'/';
	}
	$MOD['linkurl'] = $MODULE[$moduleid]['linkurl'];
	foreach($EXT as $k=>$v) {
		if(strpos($k, '_url') !== false) {
			$EXT[$k] = $city_domain.str_replace('_url', '', $k).'/';
		}
	}
}
?>