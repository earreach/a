<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($action == 'send') {
	require DT_ROOT.'/include/post.func.php';
	include load('misc.lang');
	$content = dhtmlspecialchars(trim($content));
	$len = word_count($content);
	if($len < 10 || $len > 5000) dalert($L['msg_type_gbook']);
	is_mobile($mobile) or dalert($L['msg_type_mobile']);
	if($_userid) {
		$limit = 3;
	} else {		
		$msg = captcha($captcha, 1, true);
		if($msg) dalert($msg);
		$limit = 1;
	}
	$today = $DT_TODAY - 86400;
	$sql = $_userid ? "fromuser='$_username'" : "ip='$DT_IP'";	
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}message WHERE $sql AND addtime>$today AND typeid=3 AND status=3");
	$r['num'] < $limit or dalert(lang($L['message_limit'], array($limit, $r['num'])));
	$title = $L['gbook_pre'].dsubstr($content, 60, '...');
	$content = nl2br($content);
	$content .= '<br/>'.$L['content_mobile'].$mobile;
	$type = 3;
	if(send_message($username, $title, $content, $type, $_username)) {
		dalert($L['msg_home_success'], '', 'parent.window.location=parent.window.location;');
	} else {
		dalert($_userid ? $L['msg_home_member_failed'] : $L['msg_home_guest_failed']);
	}
	dalert($username);
}
$map_auth = encrypt($COM['address'].'|'.$COM['company'].'|'.$map, DT_KEY.'MAP');
if($DT_PC) {
	//
} else {
	$foot = 'contact';
}
include template('contact', $template);
?>