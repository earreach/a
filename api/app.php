<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
$_COOKIE = array();
require '../common.inc.php';
$url = $EXT['mobile_url'];
if($DT_MOB == 'ios') {
	if(preg_match("/^([0-9]{1,})@([a-z0-9]{16,})$/i", $EXT['mobile_ios'])) {
		$t = explode('@', $EXT['mobile_ios']);
		dheader('https://app.destoon.com/get.php?o=ios&u='.$t[0].'&k='.encrypt($url, $t[1]));
	} else if(strpos($EXT['mobile_ios'], '.apple.com') !== false) {
		if($DT_MBS == 'weixin') {			
			$html = '';
			$html .= '<!doctype html><html><head><meta charset="UTF-8">';
			$html .= '<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>';
			$html .= '<title>Tips</title></head>';
			$html .= '<body style="background:#F6F6F6;text-align:center;">';
			$html .= '<img src="'.DT_STATIC.'image/weixin-ios.png?v='.DT_REFRESH.'" style="width:96%;"/>';
			$html .= '</body></html>';
			exit($html);
		}
		dheader($EXT['mobile_ios']);
	}
} else if($DT_MOB == 'android') {
	if(preg_match("/^([0-9]{1,})@([a-z0-9]{16,})$/i", $EXT['mobile_adr'])) {
		$t = explode('@', $EXT['mobile_adr']);
		dheader('https://app.destoon.com/get.php?o=adr&u='.$t[0].'&k='.encrypt($url, $t[1]));
	} else if(strpos($EXT['mobile_adr'], '.apk') !== false) {
		if($DT_MBS == 'weixin') {			
			$html = '';
			$html .= '<!doctype html><html><head><meta charset="UTF-8">';
			$html .= '<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>';
			$html .= '<title>Tips</title></head>';
			$html .= '<body style="background:#F6F6F6;text-align:center;">';
			$html .= '<img src="'.DT_STATIC.'image/weixin-adr.png?v='.DT_REFRESH.'" style="width:96%;"/>';
			$html .= '</body></html>';
			exit($html);
		}
		dheader($EXT['mobile_adr']);
	}
}
dheader($url);
?>