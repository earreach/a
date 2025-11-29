<?php 
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
$itemid or dheader($MOD['linkurl']);
$MG['message_limit'] > -1 or message(lang('message->without_permission'));
require DT_ROOT.'/module/'.$module.'/common.inc.php';
include load('misc.lang');
$item = $db->get_one("SELECT title,linkurl,username,company,vip,status FROM {$table} WHERE itemid=$itemid");
$item or message($L['not_exists']);
$item['status'] == 3 or message($L['not_exists']);
$item['username'] or message($L['com_not_member']);
$_username != $item['username'] or message($L['send_self']);

$limit_used = $limit_free = 0;
if($MG['message_limit']) {
	$today = $DT_TODAY - 86400;
	$sql = $_userid ? "fromuser='$_username'" : "ip='$DT_IP'";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}message WHERE $sql AND addtime>$today AND status=3");
	$limit_used = $r['num'];
	$limit_used < $MG['message_limit'] or message(lang($L['message_limit'], array($MG['message_limit'], $limit_used)));
	$limit_free = $MG['message_limit'] > $limit_used ? $MG['message_limit'] - $limit_used : 0;
}

$linkurl = $MOD['linkurl'].$item['linkurl'];
$need_captcha = $MOD['captcha_message'] == 2 ? $MG['captcha'] : $MOD['captcha_message'];
$need_question = $MOD['question_message'] == 2 ? $MG['question'] : $MOD['question_message'];
require DT_ROOT.'/include/post.func.php';
if($submit) {
	captcha($captcha, $need_captcha);
	question($answer, $need_question);
	$title = dhtmlspecialchars(trim($title));
	if(!$title) message($L['msg_type_title']);
	$content = dhtmlspecialchars(trim($content));
	if(!$content) message($L['msg_type_content']);
	$truename = dhtmlspecialchars(trim($truename));
	if(!$truename) message($L['msg_type_truename']);
	$telephone = dhtmlspecialchars(trim($telephone));
	if(!is_tel($telephone) && !is_mobile($telephone)) message($L['msg_type_telephone']);
	$email = dhtmlspecialchars(trim($email));
	$company = $_userid ? addslashes($company) : dhtmlspecialchars(trim($company));
	if($DT['im_qq']) $qq = dhtmlspecialchars(trim($qq));
	if($DT['im_wx']) $wx = dhtmlspecialchars(trim($wx));
	if($DT['im_ali'])$ali = dhtmlspecialchars(trim($ali));
	if($DT['im_skype']) $skype = dhtmlspecialchars(trim($skype));
	$content = nl2br($content);
	if($company) $content .= '<br/>'.$L['content_company'].'<span data-company="1">'.$company.'</span>';
	if($truename) $content .= '<br/>'.$L['content_truename'].'<span data-truename="1">'.$truename.'</span>';
	if(is_tel($telephone) || is_mobile($telephone)) $content .= '<br/>'.$L['content_telephone'].'<span data-telephone="1">'.$telephone.'</span>';
	if(is_email($email)) $content .= '<br/>'.$L['content_email'].'<span data-email="1">'.$email.'</span>';
	if($DT['im_qq'] && is_qq($qq)) $content .= '<br/>'.$L['content_qq'].' '.im_qq($qq).' <span data-qq="1">'.$qq.'</span>';
	if($DT['im_wx'] && is_wx($wx)) $content .= '<br/>'.$L['content_wx'].' '.im_wx($wx, $_username).' <span data-wx="1">'.$wx.'</span>';
	if($DT['im_ali'] && $ali) $content .= '<br/>'.$L['content_ali'].' '.im_ali($ali).' <span data-ali="1">'.$ali.'</span>';
	if($DT['im_skype'] && $skype) $content .= '<br/>'.$L['content_skype'].' '.im_skype($skype).' <span data-skype="1">'.$skype.'</span>';
	if(is_date($date)) $content .= '<br/>'.lang($L['content_date'], array($date));
	$message = $L['content_brand'].'<a href="'.$linkurl.'"><strong>'.$item['title'].'</strong></a><br/>'.$content;
	//send sms
	if($DT['sms'] && $_sms && $item['username'] && isset($sendsms)) {
		$S = userinfo($item['username']);
		if(is_mobile($S['mobile'])) {
			$sms_num = sms_send($S['mobile'], lang('sms->sms_message', array($item['title'], $itemid, $truename, $telephone)));
			if($sms_num > 0) sms_add($_username, -$sms_num);
			if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $MOD['name'].$L['sms_message'], 'ID:'.$itemid);
		}
	}
	//send sms
	$forward = $DT_PC ? $linkurl : str_replace($MOD['linkurl'], $MOD['mobile'], $linkurl);
	if(send_message($item['username'], $title, $message, 3, $_username, $moduleid, $itemid)) {
		$db->query("UPDATE {$table} SET messages=messages+1 WHERE itemid=$itemid");
		message($L['msg_message_success'], $forward);
	} else {
		message($_userid ? $L['msg_member_failed'] : $L['msg_guest_failed']);
	}
}
if($_userid) {
	$user = userinfo($_username);
	$company = $user['company'];
	$truename = $user['truename'];
	$telephone = $user['telephone'] ? $user['telephone'] : $user['mobile'];
	$email = $user['mail'] ? $user['mail'] : $user['email'];
	$qq = $user['qq'];
	$wx = $user['wx'];
	$ali = $user['ali'];
	$skype = $user['skype'];
}
$iask = explode('|', trim($MOD['message_ask']));
isset($content) or $content = '';
$date = timetodate($DT_TIME + 7*86400, 3);
$title = lang($L['brand_message_title'], array($item['title']));
$head_title = $L['brand_head_title'].$DT['seo_delimiter'].$item['title'].$DT['seo_delimiter'].$MOD['name'];
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $DT_URL);
} else {
	$forward = $MOD['mobile'].$item['linkurl'];
	$head_name = $L['brand_head_title'];
	if($sns_app) $seo_title = $MOD['name'];
	$js_pull = 0;
	$foot = '';
}
include template($MOD['template_message'] ? $MOD['template_message'] : 'message', $module);
?>