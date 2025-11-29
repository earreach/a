<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
require '../common.inc.php';
check_referer() or exit;
if($DT_BOT) dhttp(403);
$session = new dsession();
require DT_ROOT.'/include/captcha.class.php';
$do = new captcha;
$do->font = DT_ROOT.'/file/font/'.$DT['water_font'];
if($DT['captcha_cn']) $do->cn = is_file($do->font);
if($action == 'question') {
	$id = isset($id) ? trim($id) : 'questionstr';
	$do->question($id);
} else {
	if(strlen($DT['captcha_chars']) > 4) $do->chars = trim($DT['captcha_chars']);
	$do->image();
}
?>