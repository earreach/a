<?php
defined('IN_DESTOON') or exit('Access Denied');
$_userid or exit('请登录');
$mid = intval($mid);
if($mid > 3 && $itemid > 0) {
	$table = get_table($mid);
	if($table) {
		$fd = $mid == 4 ? 'userid' : 'itemid';
		$t = $db->get_one("SELECT itemid FROM {$DT_PRE}favorite WHERE userid=$_userid AND mid=$mid AND tid=$itemid LIMIT 0,1", 'CACHE');
		if($t) {
			$dc->remove("SELECT itemid FROM {$DT_PRE}favorite WHERE userid=$_userid AND mid=$mid AND tid=$itemid LIMIT 0,1");
			$db->query("DELETE FROM {$DT_PRE}favorite WHERE itemid=$t[itemid]");
			$db->query("UPDATE LOW_PRIORITY {$table} SET favorites=favorites-1 WHERE `{$fd}`=$itemid AND favorites>0", 'UNBUFFERED');
			exit('ko');
		}
		$t = $db->get_one("SELECT * FROM {$table} WHERE `{$fd}`=$itemid");
		if($t) {
			$post = array();
			if($mid == 4) {
				if($t['groupid'] > 5) {
					$post['title'] = $t['company'];
					$post['url'] = $t['linkurl'];
				}
			} else {
				if($t['status'] > 2) {
					$post['title'] = $t['title'];
					$post['url'] = strpos($t['linkurl'], '://') === false ? $MODULE[$mid]['linkurl'].$t['linkurl'] : $t['linkurl'];
				}
			}
			if($post) {
				require DT_ROOT.'/module/member/favorite.class.php';
				$do = new favorite();
				if(isset($t['thumb']) && is_url($t['thumb'])) $post['thumb'] = $t['thumb'];
				$post['userid'] = $_userid;
				$post['username'] = $_username;
				$post['addtime'] = $DT_TIME;
				$post['mid'] = $mid;
				$post['tid'] = $itemid;
				$post = daddslashes($post);
				$do->add($post);
				exit('ok');
			}
		}
	}
}
exit('收藏失败');
?>