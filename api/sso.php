<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
$moduleid = 2;
require '../common.inc.php';
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$MOD['passport'] == 'sso' or exit('Access Denied');
(isset($auth) && $auth) or exit('Access Denied');
$sso_key = trim($MOD['passport_key']);
$sso_str = decrypt($auth, $sso_key);
strpos($sso_str, '|') !== false or exit('Access Denied');
$sso_arr = explode('|', $sso_str);
#log_write($sso_arr, 'sso', 1);
if($DT_TIME - intval($sso_arr[1]) > 300) exit('Access Expiried');
require DT_ROOT.'/include/post.func.php';
$action = $sso_arr[0];
if($action == 'login' || $action == 'oauth') {
	$username = check_name($sso_arr[2]) ? $sso_arr[2] : '';
	$passport = $sso_arr[3];
	$mobile = is_mobile($sso_arr[4]) ? $sso_arr[4] : '';
	$vmobile = $sso_arr[5] ? 1 : 0;
	$email = is_email($sso_arr[6]) ? $sso_arr[6] : '';
	$vemail = $sso_arr[7] ? 1 : 0;
	$groupid = intval($sso_arr[8]);
	$truename = $sso_arr[9];
	$company = $sso_arr[10];
	$password = is_md5($sso_arr[11]) ? $sso_arr[11] : '';
	$passsalt = preg_match("/^[0-9a-zA-Z]{8,10}$/", $sso_arr[12]) ? $sso_arr[12] : '';
	$payword = is_md5($sso_arr[13]) ? $sso_arr[13] : '';
	$paysalt = preg_match("/^[0-9a-zA-Z]{8,10}$/", $sso_arr[14]) ? $sso_arr[14] : '';
	if(($_mobile && $_mobile == $mobile) || ($_email && $_email == $email)) exit('1');
	$user = array();
	if(is_mobile($mobile) && $vmobile) {
		$user = $db->get_one("SELECT userid,password,passsalt,payword,paysalt,username,passport,groupid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1");
	} elseif(is_email($email) && $vemail) {
		$user = $db->get_one("SELECT userid,password,passsalt,payword,paysalt,username,passport,groupid FROM {$DT_PRE}member WHERE email='$email' AND vemail=1");
	} else {
		exit('-1');
	}
	$sql = "loginip='$DT_IP',logintime=$DT_TIME,logintimes=logintimes+1";
	if($user) {
		if($user['groupid'] == 2 || $user['groupid'] == 4) exit('-1');
		if($payword && $paysalt && $payword != $user['payword']) $sql .= ",payword='$payword',paysalt='$paysalt'";
		if($password && $passsalt && $password != $user['password']) {
			$sql .= ",password='$password',passsalt='$passsalt'";
			$user['password'] = $password;
		}
	} else {
		require DT_ROOT.'/include/client.func.php';
		require DT_ROOT.'/module/'.$module.'/member.class.php';
		$do = new member;			
		$post = $user = array();
		$post['groupid'] = $post['regid'] = $groupid > 5 ? 6 : 5;
		$post['username'] = $post['truename'] = $username;
		$post['passport'] = $passport;
		$post['password'] = $post['cpassword'] = $do->get_pwd();
		$post['mobile'] = $mobile;
		$post['email'] = $email;
		$post['truename'] = $truename;
		$post['company'] = $company;
		$post['areaid'] = area2id(ip2area(DT_IP));
		if($do->pass($post, 1)) {
			$userid = $do->add($post);
			if($userid) {
				$user['userid'] = $userid;
				$user['password'] = $post['password'];
				$sql .= ",vmobile=$vmobile,vemail=$vemail";
				if($payword && $paysalt) $sql .= ",payword='$payword',paysalt='$paysalt'";
				if($password && $passsalt) {
					$sql .= ",password='$password',passsalt='$passsalt'";
					$user['password'] = $password;
				}
			}
		}
		$user or exit('-1');
	}
	$cookietime = $DT_TIME + ($cookietime ? $cookietime : 86400*7);
	$destoon_auth = encrypt($user['userid'].'|'.$user['password'], DT_KEY.'USER');
	ob_clean();
	header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	set_cookie('auth', $destoon_auth, $cookietime);
	$db->query("UPDATE {$DT_PRE}member SET {$sql} WHERE userid=$user[userid]");
	exit('1');
} else if($action == 'logout') {
	if($_userid) {
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		set_cookie('auth', '');
	}
	exit('1');
}
?>