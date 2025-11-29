<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function dcloud($url) {
	$arr = explode('->', $url);
	$url = (DT_CLOUD_SSL ? 'https' : 'http').'://cloud.destoon.com/'.$arr[0].'/';
	$par = $arr[1].'&version='.DT_VERSION.'&release='.DT_RELEASE.'&charset='.DT_CHARSET.'&domain='.(DT_DOMAIN ? DT_DOMAIN : DT_PATH).'&uid='.DT_CLOUD_UID.'&auth='.encrypt($arr[1], DT_CLOUD_KEY);
	return dcurl($url, $par);
}

function mobile2area($mobile) {
	global $dc, $DT;
	if(!is_mobile($mobile)) return 'Unknown';
	$area = $dc->get($mobile);
	if(!$area) {
		if($DT['mobile_appcode']) {
			$area = cloud_mobile($mobile, $DT['mobile_appcode']);
		} else {
			$area = dcloud('mobile->mobile='.$mobile);
		}
		$dc->set($mobile, $area, 86400*30);
	}
	return $area ? trim($area) : 'Unknown';
}

function cloud_push($appkey, $secret, $uuid, $content, $title = '', $linkurl = '') {
	#https://docs.jiguang.cn/jpush/server/push/rest_api_v3_push
	$notification = $ret = $headers = array();
	$headers[] = "Content-type:application/json;charset='utf-8'";
	$headers[] = "Accept:application/json";
	$headers[] = "Authorization:Basic ".base64_encode($appkey.':'.$secret);
	$extras['linkurl'] = $linkurl;
	//ANDROID
	$notification['android']['alert'] = $content;
	$notification['android']['title'] = $title;
	$notification['android']['extras'] = array('linkurl' => $linkurl);
	//IOS
	$notification['ios']['alert'] = array('title' => $title, 'body' => $content);
	$notification['ios']['sound'] = 'sound.caf';
	$notification['ios']['extras'] = array('linkurl' => $linkurl);
	$notification['ios']['badge'] = 0;

	$ret['notification'] = $notification;
	$ret['options'] = array('apns_production' => false);
	$ret['platform'] ='all';
	$ret['audience'] = array('registration_id' => array($uuid));

	return dcurl('https://api.jpush.cn/v3/push', json_encode($ret), $headers);
}

function cloud_ip($ip, $key) {
	#https://market.aliyun.com/apimarket/detail/cmapi00066996
	if(strlen($key) < 32) return 'Missed api key';
	if(!is_ip($ip)) return 'Unknown';
    $headers = array();
    $headers[] = "Authorization:APPCODE ".$key;
    $headers[] = "Content-Type:application/x-www-form-urlencoded;charset=UTF-8";
	$rec = dcurl('https://kzipglobal.market.alicloudapi.com/api/ip/query', 'ip='.$ip, $headers);
	$area = '';
	if(strpos($rec, 'data') !== false) {
		$arr = json_decode($rec, true);
		if($arr['data']['nation'] && $arr['data']['nation'] != '中国') $area .= $arr['data']['nation'].' ';
		if($arr['data']['province']) $area .= $arr['data']['province'].' ';
		if($arr['data']['city']) $area .= $arr['data']['city'].' ';
		if($arr['data']['isp']) $area .= $arr['data']['isp'].' ';
	}
	return $area ? trim($area) : 'Unknown';
}

function cloud_mobile($mobile, $key) {
	#https://market.aliyun.com/apimarket/detail/cmapi00047726
	if(strlen($key) < 32) return 'Missed api key';
	if(!is_mobile($mobile)) return 'Unknown';
    $headers = array();
    $headers[] = "Authorization:APPCODE ".$key;
    $headers[] = "Content-Type:application/x-www-form-urlencoded;charset=UTF-8";
	$rec = dcurl('https://jumcvit.market.alicloudapi.com/mobile/area', 'mobile_number='.$mobile, $headers);
	$area = '';
	if(strpos($rec, 'data') !== false) {
		$arr = json_decode($rec, true);
		$area = $arr['data']['area'].'-'.$arr['data']['originalIsp'];
	}
	return $area ? trim($area) : 'Unknown';
}

function cloud_lnglat($address, $key, $type = 0) {
	#https://market.aliyun.com/apimarket/detail/cmapi00054668
	global $dc;
	if(strlen($address) < 4) return '';
	if(strlen($key) < 32) return '';
	$map = $dc->get($address.$type);
	if(is_lnglat($map)) return $map;
    $headers = array();
    $headers[] = "Authorization:APPCODE ".$key;
    $headers[] = "Content-Type:application/x-www-form-urlencoded;charset=UTF-8";
	$rec = dcurl('https://jmregeocd.market.alicloudapi.com/geocode/geo_query', 'address='.urlencode($address), $headers);
	if(strpos($rec, 'location') !== false) {
		$arr = json_decode($rec, true);
		$map = $arr['data']['geocodes'][0]['location'];
		if(is_lnglat($map)) {
			if($type) $map = cutstr($map, ',', '').','.cutstr($map, '', ',');//lnglat to latlng for qq|google
			$dc->set($address.$type, $map, 86400*30);
			return $map;
		}
	}
	return '';
}

function cloud_spam($arr, $key, $extend = 1) {
	#https://market.aliyun.com/apimarket/detail/cmapi00063146
	if(strlen($key) < 32) return false;
	$str = '';
	if(is_array($arr)) {
		foreach($arr as $v) {
			if(!is_array($v) && !is_numeric($v)) $str .= strip_tags(trim($v))."\n";
		}
	} else {
		$str = strip_tags($arr);
	}
    $headers = array();
    $headers[] = "Authorization:APPCODE ".$key;
    $headers[] = "Content-Type:application/x-www-form-urlencoded; charset=UTF-8";
	$rec = dcurl('https://jmwbsh.market.alicloudapi.com/wbsh/text/review', 'text='.urlencode($str), $headers);
	if(strpos($rec, 'data') !== false) {
		$arr = json_decode($rec, true);
		if($arr['data']['result'] > 1) {
			foreach($arr['data']['resultItems'][0]['hits'][0]['wordHitPositions'] as $k=>$v) {
				if($v) {
					if(strlen($v['subLabelDesc']) > 3) {
						if($extend == 2) return $v['subLabelDesc'].':'.$v['keyword'];
						dalert($v['subLabelDesc'].':'.$v['keyword']);
					}
				}
			}
		}
	}
	return false;
}

function cloud_split($str, $key) {
	#https://market.aliyun.com/apimarket/detail/cmapi018397
	if(strlen($str) < 12) return '';
	if(strlen($key) < 32) return '';
    $headers = array();
    $headers[] = "Authorization:APPCODE ".$key;
    $headers[] = "Content-Type:application/x-www-form-urlencoded;charset=UTF-8";
	$rec = dcurl('https://showapifc.market.alicloudapi.com/sepWord', 'text='.urlencode($str), $headers);
	if(strpos($rec, 'list') !== false) {
		$arr = json_decode($rec, true);
		if($arr['showapi_res_body']['list']) return implode(' ', $arr['showapi_res_body']['list']);
	}
	return '';
}
?>