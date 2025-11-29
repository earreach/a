<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MG['ask'] or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/include/post.func.php';
$TYPE = get_type('ask', 1);
$TYPE or message($L['feature_close']);
$table = $DT_PRE.'ask';
$forward or $forward = '?action=index';
$dstatus = $L['ask_status'];
$dstars = $L['ask_star_type'];
$r = $db->get_one("SELECT support FROM {$DT_PRE}member WHERE userid=$_userid");
$support = $r['support'] ? $r['support'] : '';
switch($action) {
	case 'add':
		$a = array();
		if($itemid) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($r['username'] == $_username && $r['status'] > 1) $a = $r;
		}
		if($submit) {
			$typeid = intval($typeid);
			if(!$typeid || !isset($TYPE[$typeid])) message($L['pass_typeid']);
			if(strlen($title) < 6) message($L['pass_title']);
			if(strlen($content) < 6) message($L['pass_content']);
			$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
			$post = array();
			$post['typeid'] = $typeid;
			$post['title'] = dhtmlspecialchars($title);
			$post['content'] = $content;
			$post['qid'] = 0;
			$post['username'] = $_username;
			$post['addtime'] = $DT_TIME;
			$post['edittime'] = $DT_TIME;
			$db->query("INSERT INTO {$table} ".arr2sql($post, 0));
			$itemid = $db->insert_id();
			clear_upload($content, $itemid, 'ask');
			dmsg($L['ask_add_success'], '?action=index');
		} else {
			$typeid = isset($typeid) ? intval($typeid) : 0;
			$title = '';
			$content = '';
			if($a) {
				$typeid = $a['typeid'];
				$title = $a['title'];
				$content = $a['content'];
			}
			$type_select = type_select($TYPE, 1, 'typeid', $L['choose_type'], $typeid, 'id="typeid"');
			$head_title = $L['ask_title_add'];
		}
	break;
	case 'reply':
		$itemid or message();
		$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		$r or message();
		$r['username'] == $_username or message();
		if($r['star'] > 0) message($L['ask_msg_reply']);
		if($submit) {
			if(strlen($content) < 6) message($L['pass_content']);
			$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
			$post = array();
			$post['content'] = $content;
			$post['qid'] = $itemid;
			$post['username'] = $_username;
			$post['edittime'] = $DT_TIME;
			$post['addtime'] = $DT_TIME;
			$db->query("INSERT INTO {$table} ".arr2sql($post, 0));
			$db->query("UPDATE {$table} SET status=0,edittime=$DT_TIME WHERE itemid=$itemid");
			clear_upload($content, $itemid, 'ask');
			dmsg($L['ask_add_success'], '?action=show&itemid='.$itemid);
		} else {			
			dheader('?action=show&itemid='.$itemid);
		}
	break;
	case 'show':
		$itemid or message();
		$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		$r or message();
		$r['username'] == $_username or message();
		extract($r);
		if($reply) {
			$post = array();
			$post['qid'] = $itemid;
			$post['content'] = $content;
			$post['username'] = $post['editor'] = $editor;
			$post['addtime'] = $post['edittime'] = $edittime;
			$db->query("INSERT INTO {$table} ".arr2sql($post, 0));
			$db->query("UPDATE {$table} SET reply='' WHERE itemid=$itemid");
			$reply = '';
		}
		$adddate = timetodate($addtime, 5);
		$editdate = $edittime ? timetodate($edittime, 5) : 'N/A';
		$condition = "qid=$itemid";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY edittime ASC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$lists[] = $r;
		}		
		$stars = array_map("strip_tags", $dstars);
		$head_title = $L['ask_title_show'];
	break;
	case 'star':
		$itemid or message();
		$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		$r or message();
		$r['username'] == $_username or message();
		$r['star'] == 0 or message();
		$r['status'] > 1 or message();
		$star = isset($star) ? intval($star) : 4;
		in_array($star, array(1, 2, 3, 4, 5)) or $star = 4;
		$db->query("UPDATE {$table} SET star=$star WHERE itemid=$itemid");
		dmsg($L['ask_star_success'], '?action=show&itemid='.$itemid);
	break;
	case 'support':
		$support or message($L['support_error_1']);
		$user = userinfo($support);
		$user or message($L['support_error_2']);
		$head_title = $L['support_title'];
	break;
	default:
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : -1;
		$type_select = type_select($TYPE, 1, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);
		$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
		$condition = "username='$_username' AND qid=0";
		if($keyword) $condition .= match_kw('title', $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($status > -1) $condition .= " AND status=$status";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY edittime DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['dstatus'] = $dstatus[$r['status']];
			$r['dstar'] = $dstars[$r['star']];
			$r['type'] = $r['typeid'] && isset($TYPE[$r['typeid']]) ? set_style($TYPE[$r['typeid']]['typename'], $TYPE[$r['typeid']]['style']) : $L['default_type'];
			$lists[] = $r;
		}
		$head_title = $L['ask_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if(isset($lists)) {
		$time = 'addtime';
		foreach($lists as $k=>$v) {
			$lists[$k]['date'] = timetodate($v[$time], 5);
		}
	}
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('ask', $module);
?>