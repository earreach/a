<?php
defined('DT_ADMIN') or exit('Access Denied');
$all = (isset($all) && $all) ? 1 : 0;
$menus = array (
    array('更新数据', '?moduleid='.$moduleid.'&file='.$file),
    array('模块首页', $MOD['linkurl'], ' target="_blank"'),
);
$all = (isset($all) && $all) ? 1 : 0;
$one = (isset($one) && $one) ? 1 : 0;
$this_forward = '?moduleid='.$moduleid.'&file='.$file;
switch($action) {
	case 'all':
		msg('', '?moduleid='.$moduleid.'&file='.$file.'&action=show&update=1&all=1&one='.$one);
	break;
	case 'show':
		$update = 1;
		$GROUP = cache_read('group.php');
		$GRADE = cache_read('grade.php');
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(userid) AS fid FROM {$table}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		isset($sid) or $sid = $fid;
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(userid) AS tid FROM {$table}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		if($update) {
			require DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
			$do = new $module();
		}
		isset($num) or $num = 50;
		if($fid <= $tid) {
			$result = $db->query("SELECT userid,username FROM {$table} WHERE userid>=$fid ORDER BY userid LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$userid = $r['userid'];
					$username = $r['username'];
					$do->update($username);
				}
				$userid += 1;
			} else {
				$userid = $fid + $num;
			}
		} else {
			$all ? msg('', '?moduleid='.$moduleid.'&file='.$file.'&action=passport&all=1&one='.$one) : dmsg('更新成功', $this_forward);
		}
		msg('ID从'.$fid.'至'.($userid-1).$MOD['name'].'更新成功'.progress($sid, $fid, $tid), "?moduleid=$moduleid&file=$file&action=$action&sid=$sid&fid=$userid&tid=$tid&num=$num&update=$update&all=$all&one=$one", 0);
	break;
	case 'passport':
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(userid) AS fid FROM {$table}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		isset($sid) or $sid = $fid;
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(userid) AS tid FROM {$table}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		isset($num) or $num = 50;
		if($fid <= $tid) {
			$result = $db->query("SELECT userid,username,passport,company,shop FROM {$table} WHERE userid>=$fid ORDER BY userid LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$userid = $r['userid'];
					$username = $r['username'];
					$passport = addslashes($r['passport']);
					$shop = addslashes($r['shop'] ? $r['shop'] : $r['company']);

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}order WHERE seller='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}order SET shop='$shop' WHERE seller='$username'");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}order WHERE buyer='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}order SET buyer_passport='$passport' WHERE buyer='$username'");

					for($i = 0; $i < 10; $i++) {
						$t = $db->get_one("SELECT username FROM {$DT_PRE}chat_data_{$i} WHERE username='$username'");
						if($t) $db->query("UPDATE {$DT_PRE}chat_data_{$i} SET passport='$passport' WHERE username='$username'");
					}

					$t = $db->get_one("SELECT chatid FROM {$DT_PRE}chat WHERE fromuser='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}chat SET fpassport='$passport' WHERE fromuser='$username'");

					$t = $db->get_one("SELECT chatid FROM {$DT_PRE}chat WHERE touser='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}chat SET tpassport='$passport' WHERE touser='$username'");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}message WHERE fromuser='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}message SET fpassport='$passport' WHERE fromuser='$username'");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}message WHERE touser='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}message SET tpassport='$passport' WHERE touser='$username'");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}favorite WHERE userid=$userid");
					if($t) $db->query("UPDATE {$DT_PRE}favorite SET username='$username' WHERE userid=$userid");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}friend WHERE userid=$userid");
					if($t) $db->query("UPDATE {$DT_PRE}friend SET username='$username' WHERE userid=$userid");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}friend WHERE fusername='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}friend SET fpassport='$passport',fuserid=$userid WHERE fusername='$username'");

					$t = $db->get_one("SELECT itemid FROM {$DT_PRE}comment WHERE username='$username'");
					if($t) $db->query("UPDATE {$DT_PRE}comment SET passport='$passport',userid=$userid WHERE username='$username'");
					foreach($MODULE as $m) {
						$mid = $m['moduleid'];
						if($m['module'] == 'know') {
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}know_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}know_{$mid} SET passport='$passport',userid=$userid WHERE username='$username'");

							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}know_answer_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}know_answer_{$mid} SET passport='$passport',userid=$userid WHERE username='$username'");

							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}know_expert_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}know_expert_{$mid} SET passport='$passport' WHERE username='$username'");

							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}know_vote_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}know_vote_{$mid} SET passport='$passport' WHERE username='$username'");
						}
						if($m['module'] == 'club') {
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}club_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}club_{$mid} SET passport='$passport',userid=$userid WHERE username='$username'");
							
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}club_fans_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}club_fans_{$mid} SET passport='$passport' WHERE username='$username'");
							
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}club_group_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}club_group_{$mid} SET passport='$passport' WHERE username='$username'");
							
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}club_reply_{$mid} WHERE username='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}club_reply_{$mid} SET passport='$passport',userid=$userid WHERE username='$username'");
							
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}club_{$mid} WHERE replyuser='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}club_{$mid} SET replyer='$passport' WHERE replyuser='$username'");
						}
						if($m['module'] == 'group') {
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}group_order_{$mid} WHERE seller='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}group_order_{$mid} SET shop='$shop' WHERE seller='$username'");
						
							$t = $db->get_one("SELECT itemid FROM {$DT_PRE}group_order_{$mid} WHERE buyer='$username'");
							if($t) $db->query("UPDATE {$DT_PRE}group_order_{$mid} SET buyer_passport='$passport' WHERE buyer='$username'");
						}
					}
				}
				$userid += 1;
			} else {
				$userid = $fid + $num;
			}
		} else {
			dmsg('更新成功', $this_forward);
		}
		msg('ID从'.$fid.'至'.($userid-1).'昵称更新成功'.progress($sid, $fid, $tid), "?moduleid=$moduleid&file=$file&action=$action&sid=$sid&fid=$userid&tid=$tid&num=$num&all=$all&one=$one", 0);
	break;
	default:
		$r = $db->get_one("SELECT min(userid) AS fid,max(userid) AS tid FROM {$table}");
		$fid = $r['fid'] ? $r['fid'] : 0;
		$tid = $r['tid'] ? $r['tid'] : 0;
		include tpl('html', $module);
	break;
}
?>