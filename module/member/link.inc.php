<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
($MG['biz'] && $MG['homepage'] && $MG['link_limit'] > -1) or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
if($MG['type'] && !$_edittime && $action == 'add') dheader('edit'.DT_EXT.'?tab=2');
require DT_ROOT.'/include/post.func.php';
include load('my.lang');
require DT_ROOT.'/module/'.$module.'/link.class.php';
$do = new dlink();
switch($action) {
	case 'add':
		if($_credit < 0 && $MOD['credit_less']) dheader('credit'.DT_EXT.'?action=less');
		if($MG['hour_limit']) {
			$today = $DT_TIME - 3600;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}link WHERE username='$_username' AND addtime>$today");
			if($r && $r['num'] >= $MG['hour_limit']) dalert(lang($L['hour_limit'], array($MG['hour_limit'])), '?action=index');
		}
		if($MG['day_limit']) {
			$today = $DT_TODAY - 86400;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}link WHERE username='$_username' AND addtime>$today");
			if($r && $r['num'] >= $MG['day_limit']) dalert(lang($L['day_limit'], array($MG['day_limit'])), '?action=index');
		}
		if($MG['link_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}link WHERE username='$_username' AND status>0");
			if($r['num'] >= $MG['link_limit']) dalert(lang($L['limit_add'], array($MG['link_limit'], $r['num'])), '?action=index');
		}
		if($submit) {
			$post['username'] = $_username;
			if($do->pass($post)) {
				$need_check =  $MOD['link_check'] == 2 ? $MG['check'] : $MOD['link_check'];
				$post['status'] = get_status(3, $need_check);
				$do->add($post);
				dmsg($L['op_add_success'], '?status='.$post['status']);
			} else {
				message($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				$$v = '';
			}
			$head_title = $L['link_title_add'];
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();
		if($submit) {
			$post['username'] = $_username;
			if($do->pass($post)) {
				$need_check =  $MOD['link_check'] == 2 ? $MG['check'] : $MOD['link_check'];
				$post['status'] = get_status($r['status'], $need_check);
				$do->edit($post);
				if($post['status'] < 3 && $item['status'] > 2) history($moduleid, 'link-'.$itemid, 'set', $item);
				if($post['status'] == 2) dmsg($L['op_edit_check'], '?status='.$post['status']);
				dmsg($L['op_edit_success'], $forward);
			} else {
				message($do->errmsg);
			}
		} else {
			extract($r);
			$head_title = $L['link_title_edit'];
		}
	break;
	case 'delete':
		$itemid or message($L['link_msg_choose']);
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if($item && $item['username'] == $_username) $do->delete($itemid);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	default:
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(2, 3)) or $status = 3;
		$condition = "username='$_username'";
		$condition .= " AND status=$status";
		if($keyword) $condition .= match_kw('title', $keyword);
		$lists = $do->get_list($condition);
		$head_title = $L['link_title'];
	break;
}
$nums = array();
$limit_used = 0;
for($i = 2; $i < 4; $i++) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}link WHERE username='$_username' AND status=$i");
	$nums[$i] = $r['num'];
	$limit_used += $r['num'];
}
$limit_free = $MG['link_limit'] && $MG['link_limit'] > $limit_used ? $MG['link_limit'] - $limit_used : 0;
if($DT_PC) {	
	$menu_id = 2;
} else {
	if(isset($lists)) {
		$time = 'addtime';
		foreach($lists as $k=>$v) {
			$lists[$k]['linkurl'] = str_replace($MOD['linkurl'], $MOD['mobile'], $v['linkurl']);
			$lists[$k]['date'] = timetodate($v[$time], 5);
		}
	}
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	if($pages) $pages = mobile_pages($items, $page, $pagesize);
	$head_name = $head_title;
}
include template('link', $module);
?>