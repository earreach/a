<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function area_parse($area, $type = 'country') {
	$P = array('北京' ,'上海' ,'天津' ,'重庆' ,'河北' ,'山西' ,'内蒙古' ,'辽宁' ,'吉林' ,'黑龙江' ,'江苏' ,'浙江' ,'安徽' ,'福建' ,'江西' ,'山东' ,'河南' ,'湖北' ,'湖南' ,'广东' ,'广西' ,'海南' ,'四川' ,'贵州' ,'云南' ,'西藏' ,'陕西' ,'甘肃' ,'青海' ,'宁夏' ,'新疆' ,'台湾' ,'香港' ,'澳门');
	if($type == 'country') {
		//常见国家 - 可以根据网站目标用户地区增加
		$C = array('中国','美国','加拿大','英国','澳大利亚','俄罗斯','日本','韩国','新加坡','越南','法国','德国','意大利','印度','新西兰','南非');
		foreach($P as $v) {
			if(strpos($area, $v) !== false) return '中国';
		}
		foreach($C as $v) {
			if(strpos($area, $v) !== false) return $v;
		}
		return cutstr($area, '', ' ');
	} elseif($type == 'province') {
		foreach($P as $v) {
			if(strpos($area, $v) !== false) return $v;
		}
	} elseif($type == 'city') {
		$T = array('北京' ,'上海' ,'天津' ,'重庆' ,'香港' ,'澳门');
		foreach($T as $v) {
			if(strpos($area, $v) !== false) return $v;
		}
		if(strpos($area, '市') !== false) {
			if(strpos($area, '省') !== false) return trim(cutstr($area, '省', '市'));
			if(strpos($area, '自治区') !== false) return trim(cutstr($area, '自治区', '市'));
			foreach($P as $v) {
				if(strpos($area, $v) !== false) return trim(cutstr($area, $v, '市'));
			}			
		}
		$area = trim(cutstr($area, '', area_parse($area, 'network')));
		if(strpos($area, '省') !== false) return trim(cutstr($area, '省'));
		if(strpos($area, '自治区') !== false) return trim(cutstr($area, '自治区'));
		foreach($P as $v) {
			if(strpos($area, $v) !== false) return str_replace($v, '', $area);
		}
		return trim(str_replace(area_parse($area), '', $area));
	} elseif($type == 'network') {
		$N = array('电信' ,'移动' ,'联通' ,'铁通');
		foreach($N as $v) {
			if(strpos($area, $v) !== false) return $v;
		}
	}
	return '';
}

function area2id($area) {
	$kw = area_parse($area, 'city');
	if($kw) {
		$r = DB::get_one("SELECT areaid FROM ".DT_PRE."area WHERE areaname LIKE '%$kw%'");
		if($r) return $r['areaid'];
	}
	$kw = area_parse($area, 'province');
	if($kw) {
		$r = DB::get_one("SELECT areaid FROM ".DT_PRE."area WHERE areaname LIKE '%$kw%'");
		if($r) return $r['areaid'];
	}
	return 0;
}

function get_robot($ua = '') {
	global $L;
	$UA = $ua ? $ua : DT_UA;
	foreach($L['robot'] as $k=>$v) {
		if(stripos($UA, $k) !== false) return $k;
	}
	return 'other';
}

function get_os($ua = '') {
	$UA = $ua ? $ua : DT_UA;
	if(stripos($UA, 'Windows') !== false) {
		if(strpos($UA, 'NT 10.0') !== false) return 'Windows 10';
		if(strpos($UA, 'NT 6.1') !== false) return 'Windows 7';
		if(strpos($UA, 'NT 6.2') !== false) return 'Windows 8';
		if(strpos($UA, 'NT 6.3') !== false) return 'Windows 8.1';
		if(strpos($UA, 'NT 5.1') !== false) return 'Windows XP';
		if(strpos($UA, 'NT 6.0') !== false) return 'Windows Vista';
		if(strpos($UA, 'NT 5.0') !== false) return 'Windows 2000';
		if(strpos($UA, 'NT 5.2') !== false) return 'Windows 2003';
		if(strpos($UA, 'Me') !== false) return 'Windows Me';
		if(strpos($UA, '98') !== false) return 'Windows 98';
		if(strpos($UA, '95') !== false) return 'Windows 95';
		if(stripos($UA, 'Phone OS') !== false) return 'Windows Phone';
		return 'Windows';
	} else if(stripos($UA, 'Android') !== false) {
		return 'Android';
	} else if(stripos($UA, 'iPhone') !== false) {
		return 'iPhone';
	} else if(stripos($UA, 'iPad') !== false) {
		return 'iPad';
	} else if(stripos($UA, 'iPod') !== false) {
		return 'iPod';
	} else if(stripos($UA, 'Mac OS') !== false) {
		return 'Mac';
	} else if(stripos($UA, 'Linux') !== false) {
		return 'Linux';
	} else if(stripos($UA, 'Unix') !== false) {
		return 'Unix';
	} else if(stripos($UA, 'BSD') !== false) {
		return 'BSD';
	}
	return is_robot($UA) ? 'Robot' : '';
}

function get_bd($ua = '') {
	$UA = $ua ? $ua : DT_UA;
	if(stripos($UA, 'Android') !== false) {
		if(stripos($UA, 'HUAWEI') !== false) {
			return 'HUAWEI';
		} else if(stripos($UA, 'HONOR') !== false) {
			return 'HONOR';
		} else if(stripos($UA, 'Redmi') !== false) {
			return 'REDMI';
		} else if(stripos($UA, 'XiaoMi') !== false) {
			return 'XIAOMI';
		} else if(stripos($UA, 'VIVO') !== false) {
			return 'VIVO';
		} else if(stripos($UA, 'OPPO') !== false || stripos($UA, 'OPM') !== false) {  
			return 'OPPO';
		} else if(stripos($UA, 'Nexus') !== false) {
			return 'NEXUS';
		} else if(stripos($UA, 'Pixel') !== false) {
			return 'PIXEL';
		} else if(stripos($UA, 'Nokia') !== false) {
			return 'NOKIA';
		} else if(stripos($UA, 'SAMSUNG') !== false || stripos($UA, 'SM-') !== false) {
			return 'SAMSUNG';
		}
		$SN = cutstr($UA, ';', '; wv');
		$SN = trim(cutstr($SN, ';', ''));
		if($SN) {
			if(strpos($SN, 'MI ') !== false) return 'XIAOMI';
			if(substr($SN, 0, 2) == 'M2' || strpos($SN, 'RA') !== false) return 'REDMI';
			if(strpos($SN, 'TYH') !== false) return 'HUAWEI';
			if(preg_match("/V[0-9a-z]{4,6}A/i", $SN)) return 'VIVO';
			if(in_array(substr($SN, 0, 2), array('PC', 'PD', 'PE', 'PF', 'PG', 'PH', 'PJ'))) return 'OPPO';
		}
	} else if(stripos($UA, 'Windows Phone OS') !== false) {
		return 'Windows Phone';
	} else if(stripos($UA, 'iPhone') !== false) {
		return 'iPhone';
	} else if(stripos($UA, 'iPad') !== false) {
		return 'iPad';
	} else if(stripos($UA, 'iPod') !== false) {
		return 'iPod';
	} else if(stripos($UA, 'Mac OS') !== false) {
		return 'Mac';
	}
	return is_robot($UA) ? strtoupper(get_robot($UA)) : '';
}

function get_bs($ua = '') {
	$ck = get_cookie('mobile');
	if(in_array($ck, array('app', 'cms', 'web', 'screen'))) return 'APP';
	$UA = $ua ? $ua : DT_UA;
	if(stripos($UA, 'MSIE') !== false) {
		if(strpos($UA, 'MSIE 11.0') !== false) return 'IE11';
		if(strpos($UA, 'MSIE 10.0') !== false) return 'IE10';
		if(strpos($UA, 'MSIE 9.0') !== false) return 'IE9';
		if(strpos($UA, 'MSIE 8.0') !== false) return 'IE8';
		if(strpos($UA, 'MSIE 7.0') !== false) return 'IE7';
		if(strpos($UA, 'MSIE 6.0') !== false) return 'IE6';
		return 'IE';
	} else if(stripos($UA, 'rv:11.0') !== false) {
		return 'IE11';
	} else if(stripos($UA, 'IEMobile') !== false) {
		return 'IE Mobile';
	} else if(stripos($UA, 'MicroMessenger/') !== false) {
		if(stripos($UA, 'miniprogram') !== false) return 'WXMini';
		if(stripos($UA, 'wxwork') !== false) return 'WXWork';
		return 'WeiXin';
	} else if(stripos($UA, 'TIM/') !== false) {
		return 'TIM';
	} else if(stripos($UA, 'QQ/') !== false) {
		return 'QQ';
	} else if(stripos($UA, 'Alipay') !== false) {
		return 'Alipay';
	} else if(stripos($UA, 'Bytedance') !== false) {
		return 'DouYin';
	} else if(stripos($UA, 'DingTalk') !== false) {
		return 'DingTalk';
	} else if(stripos($UA, 'MiuiBrowser') !== false) {
		return 'MiuiBrowser';
	} else if(stripos($UA, 'baiduboxapp') !== false) {
		return 'Baidu';
	} else if(stripos($UA, 'Edg') !== false || stripos($UA, 'Edge') !== false) {
		return 'Edge';
	} else if(stripos($UA, 'Firefox') !== false) {
		return 'Firefox';
	} else if(stripos($UA, 'Opera') !== false || stripos($UA, 'OPR') !== false) {
		return 'Opera';
	} else if(stripos($UA, 'UCWEB') !== false || stripos($UA, 'UCBrowser') !== false) {
		return 'UC';
	} else if(stripos($UA, '360SE') !== false) {
		return '360';
	} else if(stripos($UA, 'Sogou') !== false) {
		return 'Sogou';
	} else if(stripos($UA, 'LBBROWSER') !== false || stripos($UA, 'LieBao') !== false) {
		return 'LieBao';
	} else if(stripos($UA, 'TaoBrowser') !== false) {
		return 'TaoBao';
	} else if(stripos($UA, 'Maxthon') !== false) {
		return 'Maxthon';
	} else if(stripos($UA, 'TheWorld') !== false) {
		return 'TheWorld';
	} else if(stripos($UA, 'Safari') !== false) {
		if(in_array(get_os($UA), array('Mac', 'iPhone', 'iPad', 'iPod'))) {
			return 'Safari';
		} else if(stripos($UA, 'Chrome') === false) {
			return 'Safari';
		}
	}
	return is_robot($UA) ? 'Spider' : 'Chrome';
}
?>