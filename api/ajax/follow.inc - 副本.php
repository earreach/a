<?php
defined('IN_DESTOON') or exit('Access Denied');
$_userid or exit('请登录');
(isset($username) && check_name($username)) or $username = '';
$userid = isset($userid) ? intval($userid) : 0;
if($username) {
	$_username != $username or exit('不能关注自己');
	//$F = userinfo($username);
	$F = $db->get_one("SELECT userid,passport FROM {$DT_PRE}member WHERE username='$username'", 'CACHE');
	$F or exit('会员不存在');
	$fuserid = $F['userid'];
} else if($userid) {
	$_userid != $userid or exit('不能关注自己');
	$F = $db->get_one("SELECT username,passport FROM {$DT_PRE}member WHERE userid=$userid", 'CACHE');
	$F or exit('会员不存在');
	$username = $F['username'];
	$fuserid = $userid;
}
include DT_ROOT.'/include/module.func.php';
if(blacked($username)) exit('对方拒绝');
$T = $db->get_one("SELECT itemid FROM {$DT_PRE}follow WHERE userid=$_userid AND fuserid=$fuserid", 'CACHE');
if($T) {//取关
	$dc->remove("SELECT itemid FROM {$DT_PRE}follow WHERE userid=$_userid AND fuserid=$fuserid");
	$db->query("DELETE FROM {$DT_PRE}follow WHERE itemid=$T[itemid]");
	$db->query("UPDATE {$DT_PRE}follow SET status=0 WHERE userid=$fuserid AND fuserid=$_userid");
	$db->query("UPDATE {$DT_PRE}member SET fans=fans-1 WHERE userid=$fuserid AND fans>0");
	$db->query("UPDATE {$DT_PRE}member SET follows=follows-1 WHERE userid=$_userid AND follows>0");
	exit('ko');
} else {//关注
	$status = 0;
	$W = $db->get_one("SELECT itemid FROM {$DT_PRE}follow WHERE userid=$fuserid AND fuserid=$_userid");//对方是否关注自己
	if($W) {//互关
		$status = 1;
		$db->query("UPDATE {$DT_PRE}follow SET status=1 WHERE itemid=$W[itemid]");
	}
	$db->query("INSERT INTO {$DT_PRE}follow (username,passport,userid,fusername,fpassport,fuserid,addtime,status) VALUES ('$_username','$_passport','$_userid','$username','$F[passport]','$fuserid','$DT_TIME','$status')");
	$db->query("UPDATE {$DT_PRE}member SET fans=fans+1 WHERE userid=$fuserid");
	$db->query("UPDATE {$DT_PRE}member SET follows=follows+1 WHERE userid=$_userid");
	exit('ok');
}
?>