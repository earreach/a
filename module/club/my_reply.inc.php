<?php 
defined('IN_DESTOON') or exit('Access Denied');
$reply_limit = intval($MOD['reply_limit_'.$_groupid]);
if($reply_limit < 0) {
	if($_userid) dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
	login();
}
require DT_ROOT.'/module/'.$module.'/reply.class.php';
$do = new reply($moduleid);
$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
$today = $DT_TODAY - 86400;
if(in_array($action, array('', 'add')) && $reply_limit) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_reply} WHERE $sql AND status>1 AND addtime>$today");
	$limit_used = $r['num'];
	$limit_free = $reply_limit > $limit_used ? $reply_limit - $limit_used : 0;
}
switch($action) {
	case 'add':
		check_group($_groupid, $MOD['group_reply']) or dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
		$tid = isset($tid) ? intval($tid) : 0;
		$tid or dalert($L['my_choose_post'], $DT_PC ? $MOD['linkurl'] : $MOD['mobile']);
		$T = $db->get_one("SELECT * FROM {$table} WHERE itemid=$tid");
		($T && $T['status'] == 3) or dalert($L['my_not_post'], 'goback');
		$gid = $T['gid'];
		$GRP = get_group($gid);
		($GRP && $GRP['status'] == 3) or dalert($L['my_not_group'], 'goback');
		if($reply_limit && $limit_used >= $reply_limit) dalert(lang($L['day_limit'], array($reply_limit, $limit_used)), $MODULE[2]['linkurl'].$DT['file_my'].'?mid='.$mid.'&job='.$job);
		$is_fans = is_fans($GRP);
		$ban = is_array($is_fans) ? $is_fans['ban'] : 0;
		if($ban) dalert($L['my_baned'], 'goback');
		if($GRP['reply_type'] && !$is_fans) {
			$action = 'reply';
			$head_title = lang('message->without_permission');
			exit(include template('nofans', $module));
		}
		$rid = isset($rid) ? intval($rid) : 0;
		$R = array();
		if($rid) {
			$R = $db->get_one("SELECT * FROM {$table_reply} WHERE itemid=$rid");
			($R && $R['status'] == 3 && $R['tid'] == $tid) or dalert($L['my_not_reply']);
			$str = $R['content'];
			if(strpos($str, '<hr class="club_break" />') !== false) {
				$str = substr($str, strpos($str, '<hr class="club_break" />'));
			} else if(strpos($str, '<hr class="club_break">') !== false) {
				$str = substr($str, strpos($str, '<hr class="club_break">'));
			} else if(strpos($str, '<hr class="club_break"/>') !== false) {
				$str = substr($str, strpos($str, '<hr class="club_break"/>'));
			}
			$str = get_intro($str, 500);
			$R['quote'] = '<div class="club_quote"><div><a href="'.$MOD['linkurl'].'goto'.DT_EXT.'?itemid='.$rid.'"><p>'.$R['passport'].$L['my_reply_at'].timetodate($R['addtime'], 5).'</p>'.$str.'</a></div></div><hr class="club_break" />';
		}
		$need_captcha = $MOD['captcha_reply'] == 2 ? $MG['captcha'] : $MOD['captcha_reply'];
		$need_question = $MOD['question_reply'] == 2 ? $MG['question'] : $MOD['question_reply'];
		if($submit) {
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) dalert($msg);
			$msg = question($answer, $need_question, true);
			if($msg) dalert($msg);
			if($do->pass($post)) {				
				$post['level'] = 0;
				$post['tid'] = $tid;
				$post['gid'] = $gid;
				$post['rid'] = $rid;
				if($R) $post['content'] = addslashes($R['quote']).$post['content'];
				$need_check =  $MOD['check_reply'] == 2 ? $MG['check'] : $MOD['check_reply'];
				$post['status'] = get_status(3, $need_check);
				$post['userid'] = $_userid;
				$post['username'] = $_username;
				$post['passport'] = addslashes($_passport);
				$_DT_PC = $DT_PC;
				$do->add($post);
				$DT_PC = $_DT_PC;
				$js = '';
				if($post['status'] == 3) {
					$forward = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'goto'.DT_EXT.'?itemid='.$do->itemid;
					$msg = '';
				} else {
					if($_userid) {
						set_cookie('dmsg', $msg);
						$forward = '?mid='.$mid.'&job='.$job.'&status='.$post['status'];
						$msg = '';
					} else {
						$forward = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$T['linkurl'];
						$msg = $L['success_check'];
					}
				}
				$js .= 'window.onload=function(){'.(strpos($forward, '://') === false ? 'parent' : 'top').'.window.location="'.$forward.'";}';
				dalert($msg, '', $js);
			} else {
				dalert($do->errmsg, '', ($need_captcha ? reload_captcha() : '').($need_question ? reload_question() : ''));
			}
		} else {
			foreach($do->fields as $v) {
				if($v == 'tid' || $v == 'rid') continue;
				$$v = '';
			}
			$content = '';
			$item = array();
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$item = $do->get_one();
		if(!$item || $item['username'] != $_username) message();
		$tid = $item['tid'];
		$T = $db->get_one("SELECT * FROM {$table} WHERE itemid=$tid");

		if($MG['edit_limit'] < 0) message($L['edit_refuse']);
		if($MG['edit_limit'] && $DT_TIME - $item['addtime'] > $MG['edit_limit']*86400) message(lang($L['edit_limit'], array($MG['edit_limit'])));

		if($submit) {
			if($do->pass($post)) {
				$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_add'];
				$post['status'] = get_status($item['status'], $need_check);				
				$post['level'] = $item['level'];
				$do->edit($post);
				if($post['status'] < 3 && $item['status'] > 2) history($moduleid, 'reply-'.$itemid, 'set', $item);
				set_cookie('dmsg', $post['status'] == 2 ? $L['success_edit_check'] : $L['success_edit']);
				dalert('', '', 'parent.window.location="'.($post['status'] == 2 ? '?mid='.$moduleid.'&job='.$job.'&status=2' : $forward).'"');
			} else {
				dalert($do->errmsg);
			}
		} else {
			extract($item);
		}
	break;
	case 'delete':
		$MG['delete'] or message();
		$itemid or message();
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $db->get_one("SELECT username FROM {$table_reply} WHERE itemid=$itemid");
			if(!$item || $item['username'] != $_username) message();
			$do->recycle($itemid);
		}
		dmsg($L['success_delete'], $forward);
	break;
	default:
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3)) or $status = 3;
		$condition = "username='$_username'";
		$condition .= " AND status=$status";
		if($keyword) $condition .= match_kw('content', $keyword);
		$timetype = strpos($MOD['order'], 'edit') === false ? 'add' : '';
		$lists = $do->get_list($condition, $MOD['order']);
	break;
}
if($_userid) {
	$nums = array();
	for($i = 1; $i < 4; $i++) {
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_reply} WHERE username='$_username' AND status=$i");
		$nums[$i] = $r['num'];
	}
}
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MODULE[2]['linkurl'], $MODULE[2]['mobile'], $DT_URL);
} else {
	$foot = '';
	if($action == 'add' || $action == 'edit') {
		//
	} else {
		foreach($lists as $k=>$v) {
			$lists[$k]['linkurl'] = str_replace($MOD['linkurl'], $MOD['mobile'], $v['linkurl']);
			$lists[$k]['date'] = timetodate($v['addtime'], 5);
		}
		$pages = mobile_pages($items, $page, $pagesize);
		$foot = '';
	}
}
$head_title = $L['my_reply_title'];
include template($MOD['template_my_reply'] ? $MOD['template_my_reply'] : 'my_club_reply', 'member');
?>