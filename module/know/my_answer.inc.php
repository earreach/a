<?php 
defined('IN_DESTOON') or exit('Access Denied');
$answer_limit = intval($MOD['answer_limit_'.$_groupid]);
if($answer_limit < 0) {
	if($_userid) dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
	login();
}
include load('misc.lang');
require DT_ROOT.'/module/'.$module.'/answer.class.php';
$do = new answer($moduleid);
$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
$today = $DT_TODAY - 86400;
if(in_array($action, array('', 'add')) && $answer_limit) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_answer} WHERE $sql AND status>1 AND addtime>$today");
	$limit_used = $r['num'];
	$limit_free = $answer_limit > $limit_used ? $answer_limit - $limit_used : 0;
}
switch($action) {
	case 'add':
		$itemid or dheader('?mid='.$mid.'&job='.$job.'&action=question');
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		$item['status'] > 2 or dheader('?mid='.$mid.'&job='.$job.'&action=question');
		$linkurl = ($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).$item['linkurl'];
		$could_answer = check_group($_groupid, $MOD['group_answer']);
		if($item['process'] != 1 || ($_username && $_username == $item['username'])) $could_answer = false;
		$need_captcha = $MOD['captcha_answer'] == 2 ? $MG['captcha'] : $MOD['captcha_answer'];
		$need_question = $MOD['question_answer'] == 2 ? $MG['question'] : $MOD['question_answer'];
		if($could_answer && !$MOD['answer_repeat']) {
			if($_username) {
				$r = $db->get_one("SELECT itemid FROM {$table_answer} WHERE username='$_username' AND qid=$itemid");
			} else {
				$r = $db->get_one("SELECT itemid FROM {$table_answer} WHERE ip='$DT_IP' AND qid=$itemid AND addtime>$DT_TIME-86400");
			}
			if($r) $could_answer = false;
		}
		$could_answer or dheader($linkurl);
		if($submit) {
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) dalert($msg);
			$msg = question($answer, $need_question, true);
			if($msg) dalert($msg);
			$content = stripslashes(trim($content));
			if(!$content) dalert($L['type_answer']);
			$content = save_local($content);
			if($MOD['clear_alink']) $content = clear_link($content);
			if($MOD['save_remotepic']) $content = save_remote($content);
			$content = dsafe($content);
			$content = addslashes($content);
			is_url($url) or $url = '';
			$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_answer'];
			$status = get_status(3, $need_check);
			$hidden = isset($hidden) ? 1 : 0;
			$expert = 0;
			if($_username) {
				$t = $db->get_one("SELECT itemid FROM {$table_expert} WHERE username='$_username'");
				if($t) {
					$expert = 1;
					$db->query("UPDATE {$table_expert} SET answer=answer+1 WHERE username='$_username'");
				}
			}
			$db->query("INSERT INTO {$table_answer} (qid,url,content,username,passport,expert,addtime,ip,status,hidden) VALUES ('$itemid','$url','$content','$_username','$_passport','$expert','$DT_TIME','$DT_IP','$status','$hidden')");
			$aid = $db->insert_id();
			clear_upload($content, $aid, $table_answer);
			if($MOD['credit_answer'] && $_username && $status == 3) {
				$could_credit = true;
				if($MOD['credit_maxanswer'] > 0) {					
					$r = $db->get_one("SELECT SUM(amount) AS total FROM {$DT_PRE}finance_credit WHERE username='$_username' AND addtime>$DT_TIME-86400  AND reason='".$L['answer_question']."'");
					if($r['total'] >= $MOD['credit_maxanswer']) $could_credit = false;
				}
				if($could_credit) {
					credit_add($_username, $MOD['credit_answer']);
					credit_record($_username, $MOD['credit_answer'], 'system', $L['answer_question'], 'ID:'.$itemid);
				}
			}
			if($MOD['answer_message'] && $item['username']) {
				send_message($item['username'], lang($L['answer_msg_title'], array(dsubstr($item['title'], 20, '...'))), lang($L['answer_msg_content'], array($item['title'], stripslashes($content), $linkurl)));
			}
			dalert($status == 3 ? $L['answer_success'] : $L['answer_check'], '', 'top.window.location="'.$linkurl.'";');
		} else {
			$content = $url = $hidden = '';
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();

		$qid = $r['qid'];
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$qid");

		if($MG['edit_limit'] < 0) message($L['edit_refuse']);
		if($MG['edit_limit'] && $DT_TIME - $item['addtime'] > $MG['edit_limit']*86400) message(lang($L['edit_limit'], array($MG['edit_limit'])));

		$need_question = $need_captcha = '';

		if($submit) {
			$content = stripslashes(trim($content));
			if(!$content) dalert($L['type_answer']);
			$content = save_local($content);
			if($MOD['clear_alink']) $content = clear_link($content);
			if($MOD['save_remotepic']) $content = save_remote($content);
			$content = dsafe($content);
			$content = addslashes($content);
			clear_upload($content, $itemid, $table_answer);
			is_url($url) or $url = '';
			$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_answer'];
			$status = get_status($r['status'], $need_check);
			$hidden = isset($hidden) ? 1 : 0;
			$db->query("UPDATE {$table_answer} SET content='$content',url='$url',hidden='$hidden',status='$status',edittime='$DT_TIME' WHERE itemid=$itemid");
			if($post['status'] < 3 && $item['status'] > 2) history($moduleid, 'answer-'.$itemid, 'set', $item);
			set_cookie('dmsg', $post['status'] == 2 ? $L['success_edit_check'] : $L['success_edit']);
			dalert('', '', 'parent.window.location="'.($post['status'] == 2 ? '?mid='.$moduleid.'&job='.$job.'&status=2' : $forward).'"');
		} else {
			extract($r);
		}
	break;
	case 'question':
		require DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
		$do = new $module($moduleid);
		$condition = "status=3 AND process=1";
		if($keyword) $condition .= match_kw('title', $keyword);
		if($catid) $condition .= ($CAT['child']) ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		$lists = $do->get_list($condition, 'addtime desc');
	break;
	default:
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3)) or $status = 3;
		$condition = "username='$_username'";
		$condition .= " AND status=$status";
		if($keyword) $condition .= match_kw('content', $keyword);
		$lists = $do->get_list($condition, $MOD['order']);
	break;
}
if($_userid) {
	$nums = array();
	for($i = 1; $i < 4; $i++) {
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_answer} WHERE username='$_username' AND status=$i");
		$nums[$i] = $r['num'];
	}
}
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MODULE[2]['linkurl'], $MODULE[2]['mobile'], $DT_URL);
} else {
	$foot = '';
}
$head_title = $L['answer_title'];
include template($MOD['template_my_answer'] ? $MOD['template_my_answer'] : 'my_know_answer', 'member');
?>