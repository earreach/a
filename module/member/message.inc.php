<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/module/'.$module.'/message.class.php';
$action or $action = 'inbox';
$menuid = $action;
$NAME = $L['message_type'];
$COLORS = array('FF0000','0000FF','000000','008080','008000','800000','808000','808080');
$head_title = $L['message_title'];
if($_userid) {
	$MG['inbox_limit'] > -1 or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
	$do = new message;
	$lists = array();
} else {
	$action = 'guest';
}
$lists = array();
if(in_array($action, array('inbox', 'outbox', 'draft', 'recycle', 'outbox'))) {
	$sfields = $L['message_sfields'];
	$dfields = array('title', 'title', 'content', 'fromuser', 'touser');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	(isset($style) && in_array($style, $COLORS)) or $style = '';
	$typeid = isset($typeid) ? intval($typeid) : -1;
	$tid = isset($tid) ? intval($tid) : 0;
	(isset($username) && check_name($username)) or $username = '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$condition = '';
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	if($typeid > -1) $condition .= " AND typeid=$typeid";
	if($style) $condition .= " AND style='$style'";
	if($username) $condition .= " AND fromuser='$username'";
	if($mid > 4) $condition .= " AND mid=$mid";
	if($tid > 0) $condition .= " AND tid=$tid";
}
switch($action) {
	case 'guest':
	break;
	case 'send':
		$MG['message_limit'] > -1 or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
		$limit_used = $limit_free = 0;
		if($MG['message_limit']) {
			$today = $DT_TODAY - 86400;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}message WHERE fromuser='$_username' AND addtime>$today AND status=3");
			$limit_used = $r['num'];
			$limit_used < $MG['message_limit'] or dalert(lang($L['message_limit'], array($MG['message_limit'], $limit_used)), 'goback');
			$limit_free = $MG['message_limit'] > $limit_used ? $MG['message_limit'] - $limit_used : 0;
		}
		$need_captcha = $MOD['captcha_sendmessage'] == 2 ? $MG['captcha'] : $MOD['captcha_sendmessage'];
		if($submit) {
			captcha($captcha, $need_captcha);
			$post['typeid'] = 0;
			if($do->send($post)) {
				if(isset($post['save'])) dmsg($L['message_msg_save_draft'], '?action=draft');
				if(isset($post['copy'])) dmsg($L['message_msg_send'], '?action=outbox');
				dmsg($L['message_msg_send'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			(isset($touser) && check_name($touser)) or $touser = '';
			if($touser && blacked($touser)) message($L['message_msg_black'], '?action=inbox');
			$user = $touser ? userinfo($touser) : array();
			$title = isset($title) ? stripslashes($title) : '';
			$content = isset($content) ? stripslashes($content) : '';	
		}
	break;
	case 'edit':
		$itemid or message($L['message_msg_choose']);
		$do->itemid = $itemid;
		if($submit) {
			if($do->edit($post)) {
				dmsg(isset($post['send']) ? $L['message_msg_send'] : $L['message_msg_edit_draft'], '?action=draft');
			} else {
				message($do->errmsg);
			}
		} else {
			$r = $do->get_one();
			if(!$r || $r['status'] != 1 || $r['fromuser'] != $_username) message($L['message_msg_deny']);
			$touser = $r['touser'];
			$title = $r['title'];
			$content = $r['content'];
			$menuid = 'draft';
		}
	break;
	case 'clear':
		$status or message();
		$do->clear($status);
		dmsg($L['message_msg_clear'], $forward);
	break;
	case 'delete':
		$itemid or message($L['message_msg_choose']);
		$recycle = (isset($recycle) && $recycle) ? 1 : 0;
		$do->itemid = $itemid;
		$black = $job == 'black' ? $L['message_black'] : '';
		$do->delete($recycle, $black);
		dmsg($L['op_del_success'], $forward);
	break;
	case 'mark':
		$itemid or message($L['message_msg_choose']);
		$do->itemid = $itemid;
		$do->mark();
		dmsg($L['message_msg_mark'], $forward);
	break;
	case 'markall':
		$do->markall();
		dmsg($L['message_msg_mark'], $forward);
	break;
	case 'restore':
		$itemid or message($L['message_msg_choose']);
		$do->itemid = $itemid;
		$do->restore();
		dmsg($L['message_msg_restore'], $forward);
	break;
	case 'color':
		$itemid or message();
		$do->itemid = $itemid;
		$do->color($style);
		dmsg($L['op_set_success'], $forward);
	break;
	case 'show':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r) message($L['message_msg_deny']);
		$fback = isset($feedback) ? 1 : 0;
		extract($r);
		if($status == 4 || $status == 3) {
			if($touser != $_username) message($L['message_msg_deny']);
			if(!$isread) {
				$do->read();
				--$_message;
				if($fback && $feedback) $do->feedback($r);
			}
		} else if($status == 2 || $status == 1) {
			if($fromuser != $_username) message($L['message_msg_deny']);
		}
		require DT_ROOT.'/include/content.class.php';
		$content = DC::format($content, $DT_PC);
		$adddate = timetodate($addtime, 5);
		if($status == 1) {
			$menuid = 'draft';
		} else if($status == 2) {
			$menuid = 'outbox';
		} else if($status == 4) {
			$menuid = 'recycle';
		} else {
			$menuid = 'inbox';
		}
		if(strpos($forward, 'message') === false) $forward = '?action='.$menuid;
	break;
	case 'export':
		if($submit) {
			$do->export($post) or message($do->errmsg);
		} else {
			$fromdate = timetodate(datetotime('-1 month'), 3).' 00:00:00';
			$todate = timetodate($DT_TIME, 3).' 23:59:59';
		}
	break;
	case 'empty':
		if($submit) {
			$post['username'] = $_username;
			if($do->_clear($post)) {
				dmsg($L['message_msg_empty'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			$fromdate = '';
			$todate = timetodate(datetotime('-6 month'), 3).' 23:59:59';
		}
	break;
	case 'outbox':
		$status = 2;
		$name = $L['message_title_outbox'];
		$condition = "fromuser='$_username' AND status=$status ".$condition;
		$lists = $do->get_list($condition);
	break;
	case 'draft':
		$status = 1;
		$name = $L['message_title_draft'];
		$condition = "fromuser='$_username' AND status=$status ".$condition;
		$lists = $do->get_list($condition);
	break;
	case 'recycle':
		$status = 4;
		$name = $L['message_title_recycle'];
		$condition = "touser='$_username' AND status=$status ".$condition;
		$lists = $do->get_list($condition);
	break;
	case 'last':
		if($_message) {
			$item = $db->get_one("SELECT itemid,feedback FROM {$DT_PRE}message WHERE touser='$_username' AND status=3 AND isread=0 ORDER BY itemid DESC");
			if($item) dheader('?action=show&itemid='.$item['itemid'].($item['feedback'] ? '&feedback=1' : ''));
		} 
		dheader('?action=index');
	break;
	default:
		if($MG['inbox_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}message WHERE touser='$_username' AND status=3");
			$limit_used = $r['num'];
			$limit_free = $MG['inbox_limit'] > $limit_used ? $MG['inbox_limit'] - $limit_used : 0;
			if($limit_used >= $MG['inbox_limit']) dalert($L['message_msg_inbox_limit'], '?action=empty');
		}
		$status = 3;
		$name = $L['message_title_inbox'];
		if($_message) $do->fix_message();
		$condition = "touser='$_username' AND status=$status ".$condition;
		$lists = $do->get_list($condition);
		$systems = $typeid > -1 ? array() : $do->get_sys();
		$color_select = '';
		foreach($COLORS as $v) {
			$color_select .= '<option value="'.$v.'" style="background:#'.$v.';">&nbsp;</option>';
		}
	break;
}
if($DT_PC) {
	//
} else {
	$foot = 'message';
	if($action == 'guest' || $action == 'close') {
		//
	} else if($action == 'send' || $action == 'edit') {
		//
	} else if($action == 'show') {
		//
	} else {
		if(isset($items)) {
			$pages = mobile_pages($items, $page, $pagesize);
			if($items) $js_load = '?action='.$action.'&kw='.$kw.'&job=ajax';
		}
	}
	if($sns_app) $seo_title = '';
}
include template('message', $module);
?>