<?php
defined('IN_DESTOON') or exit('Access Denied');
if($CFG['cache'] == 'file') $dc->expire();
/*自动发布待发布*/
foreach($MODULE as $m) {
	if($m['module'] == 'article' || $m['module'] == 'moment') {
		if(!function_exists('url2video')) require_once DT_ROOT.'/include/module.func.php';
		$module = $m['module'];
		$moduleid = $m['moduleid'];
		$table = $DT_PRE.$module.'_'.$moduleid;
		$table_data = $DT_PRE.$module.'_data_'.$moduleid;
		$MOD = cache_read('module-'.$moduleid.'.php');
		if($MOD['show_html']) {
			$result = $db->query("SELECT itemid FROM {$table} WHERE status=4 AND addtime<$DT_TIME");
			while($r = $db->fetch_array($result)) {
				$itemid = $r['itemid'];
				$db->query("UPDATE {$table} SET status=3 WHERE itemid=$itemid");
				tohtml('show', $module);
			}
		} else {
			$db->query("UPDATE {$table} SET status=3 WHERE status=4 AND addtime<$DT_TIME");
		}
	}
}
/*清理过期会员*/
$result = DB::query("SELECT username,userid,groupid FROM {$DT_PRE}company WHERE totime>0 AND totime<$DT_TIME AND vip>0 AND groupid>6 ORDER BY userid ASC LIMIT 100");
while($r = DB::fetch_array($result)) {
	$userid = $r['userid'];
	$username = $r['username'];
	$user = userinfo($username);
	$gid = $user['regid'] == 5 ? 5 : 6;
	DB::query("UPDATE ".DT_PRE."member SET groupid=$gid WHERE userid=$userid");
	DB::query("UPDATE ".DT_PRE."company SET groupid=$gid,vip=0,styletime=0,styleid=0,fromtime=0,totime=0 WHERE userid=$userid");
	userclean($username);
}
?>