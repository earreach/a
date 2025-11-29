<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/fans.class.php';
$do = new fans();
if($action) {
	($itemid && is_array($itemid)) or message($L['my_choose_fans']);
	foreach($itemid as $fid) {
		$F = $db->get_one("SELECT * FROM {$table_fans} WHERE itemid=$fid");
		$GRP = get_group($F['gid']);
		($GRP && $GRP['status'] == 3) or message($L['my_not_group']);
		($_admin || $GRP['username'] == $_username) or message($L['my_not_admin']);
	}
}
switch($action) {
	case 'delete':
		$do->recycle($itemid);
		dmsg($L['success_delete'], $forward);
	break;
	case 'cancel':
		$do->check($itemid, 2);
		dmsg($L['success_cancel'], $forward);
	break;
	case 'ban':
		$do->ban($itemid, 1);
		dmsg($L['success_ban'], $forward);
	break;
	case 'unban':
		$do->ban($itemid, 0);
		dmsg($L['success_cancel'], $forward);
	break;
	case 'check':
		$do->check($itemid);
		dmsg($L['success_checked'], $forward);
	break;
	case 'reject':
		$do->reject($itemid);
		dmsg($L['success_reject'], $forward);
	break;
	default:
		$gid = isset($gid) ? intval($gid) : 0;
		$ids = '';
		$result = $db->query("SELECT itemid FROM {$table_group} WHERE username='$_username'");
		while($r = $db->fetch_array($result)) {
			$ids .= ','.$r['itemid'];
		}
		if(!$_admin && strpos($ids.',', ','.$gid.',') === false) $gid = 0;
		$gid or $gid = '';
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3)) or $status = 3;
		$condition = $gid ? "gid=$gid" : ($ids ? "gid IN (".substr($ids, 1).")" : "gid=0");
		$nums = array();
		for($i = 1; $i < 4; $i++) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_fans} WHERE {$condition} AND status=$i");
			$nums[$i] = $r['num'];
		}		
		$sfields = $L['my_fans_fields'];
		$dfields = array('username', 'reason');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition .= " AND status=$status";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		$lists = $do->get_list($condition);
	break;
}
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MODULE[2]['linkurl'], $MODULE[2]['mobile'], $DT_URL);
} else {
	$foot = '';
	if($action == 'add' || $action == 'edit') {
		//
	} else {
		$pages = mobile_pages($items, $page, $pagesize);
		$foot = '';
	}
}
$head_title = $L['my_fans_title'];
include template($MOD['template_my_fans'] ? $MOD['template_my_fans'] : 'my_club_fans', 'member');
?>