<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$table = $DT_PRE.'follow';
$type = 'follow';
switch($action) {
	case 'note':
		if(isset($post) && is_array($post)) {
			foreach($post as $k=>$v) {
				$itemid = intval($k);
				$note = strip_tags(trim($v['note']));
				$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
				if($r['username'] == $_username) {
					$db->query("UPDATE {$table} SET fnote='$note' WHERE itemid=$itemid");
				} else if($r['fusername'] == $_username) {
					$db->query("UPDATE {$table} SET note='$note' WHERE itemid=$itemid");
				}
			}
		}		
		dmsg($L['op_update_success'], $forward);
	break;
	case 'edit':
		$itemid or message();
		$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		if($r['username'] == $_username) {
			$type = 'follow';
		} else if($r['fusername'] == $_username) {
			$type = 'fans';
		} else {
			message();
		}
		$TYPE = get_type($type.'-'.$_userid);
		if($submit) {
			isset($TYPE[$typeid]) or $typeid = 0;
			$note = strip_tags(trim($note));
			if($type == 'follow') {
				$db->query("UPDATE {$table} SET ftypeid=$typeid,fnote='$note' WHERE itemid=$itemid");
			} else {
				$db->query("UPDATE {$table} SET typeid=$typeid,note='$note' WHERE itemid=$itemid");
			}
			dmsg($L['op_edit_success'], $forward);
		} else {
			if($type == 'follow') {
				$username = $r['fusername'];
				$passport = $r['fpassport'];
				$typeid = $r['ftypeid'];
				$note = $r['fnote'];
				$head_title = $L['follow_edit_follow'];
			} else {
				$username = $r['username'];
				$passport = $r['passport'];
				$typeid = $r['typeid'];
				$note = $r['note'];
				$head_title = $L['follow_edit_fans'];
			}
			$user = userinfo($username);
			$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid);
		}
	break;
	case 'remove':
		$itemid or message($L['follow_msg_choose']);	
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($item['fusername'] != $_username) message();
			$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
			if($job == 'black') black_add($item['username'], $L['follow_black_fans']);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'delete':
		$itemid or message($L['follow_msg_choose']);
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
			if($item['username'] != $_username) message();
			$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
			if($job == 'black') black_add($item['fusername'], $L['follow_black_follow']);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'fans':
		$type = 'fans';
		$TYPE = get_type('fans-'.$_userid);
		$sfields = $L['follow_sfields'];
		$dfields = array('username', 'username', 'passport', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		$status = isset($status) ? intval($status) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);
		$condition = "fuserid=$_userid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($status) $condition .= " AND status=1";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = $users = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['type'] = $r['typeid'] && isset($TYPE[$r['typeid']]) ? set_style($TYPE[$r['typeid']]['typename'], $TYPE[$r['typeid']]['style']) : $L['default_type'];
			$lists[] = $r;
		}
		$lists = list_user($lists, 'userid,fans,follows,sign,validate');
		if($items != $_fans && substr_count('AND', $condition) == 0) {
			$db->query("UPDATE {$DT_PRE}member SET fans=$items WHERE userid=$_userid");
			userclean($_username);
		}
		$head_title = $L['follow_title_fans'];
	break;
	default:
		$TYPE = get_type('follow-'.$_userid);
		$sfields = $L['follow_sfields'];
		$dfields = array('fusername', 'fusername', 'fpassport', 'fnote');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		$status = isset($status) ? intval($status) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);
		$condition = "userid=$_userid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($typeid > -1) $condition .= " AND ftypeid=$typeid";
		if($status) $condition .= " AND status=1";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = $users = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['type'] = $r['ftypeid'] && isset($TYPE[$r['ftypeid']]) ? set_style($TYPE[$r['ftypeid']]['typename'], $TYPE[$r['ftypeid']]['style']) : $L['default_type'];
			$lists[] = $r;
		}
		$lists = list_user($lists, 'userid,fans,follows,sign,validate', 'fuserid');
		if($items != $_follows && substr_count('AND', $condition) == 0) {
			$db->query("UPDATE {$DT_PRE}member SET follows=$items WHERE userid=$_userid");
			userclean($_username);
		}
		$head_title = $L['follow_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('follow', $module);
?>