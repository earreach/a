<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$url = admin_cloud($action, $DT, $DT_URL);
if(isset($mfa)) $url .= '&mfa='.$mfa;
if($action == 'smssign') $url .= '&sign='.urlencode($DT['sms_sign'] ? $DT['sms_sign'] : $DT['sitename']).'&home='.urlencode(DT_PATH);
dheader($url);
?>