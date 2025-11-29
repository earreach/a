<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$forward or $forward = '?action=dashboard';
$logout = $MODULE[2]['linkurl'].'logout'.DT_EXT.'?forward='.urlencode(DT_PATH);
if($_destoon_admin && $_userid && $_destoon_admin == $_userid && $action != 'temp') {dheader($forward);}
if($DT['admin_area']) {
	$AA = explode("|", trim($DT['admin_area']));
	$A = ip2area(DT_IP);
	$pass = false;
	foreach($AA as $v) {
		if(strpos($A, $v) !== false) { $pass = true; break; }
	}
	if(!$pass) {dalert('未被允许的地区', $logout);}
}
$LOCK = cache_read(DT_IP.'.php', 'ban');
if($LOCK && DT_TIME - $LOCK['time'] < 3600 && $LOCK['times'] >= 1) $DT['captcha_admin'] = 1;
if($DT['close']) $DT['captcha_admin'] = 0;
$MOD = admin_login();
$_forward = $forward ? urlencode($forward) : '';
$could_sms = ($MOD['login_sms'] && $DT['sms']) ? 1 : 0;
$could_name = $could_sms && $DT['sms_admin'] ? 0 : 1;
if($CFG['authadmin'] == 'cookie') $session = new dsession();
switch($action) {
	case 'sms':
		$could_sms or dheader('?file='.$file.'&forward='.$_forward);
		if($submit) {
			$_SESSION['mobile_oppo'] = $_SESSION['mobile_oppo'] + 1;
			if($_SESSION['mobile_oppo'] > 3) $_SESSION['mobile_code'] = '';
			(is_mobile($mobile) && preg_match("/^[0-9]{6}$/", $code) && isset($_SESSION['mobile_code']) && $_SESSION['mobile_code'] == md5($mobile.'|'.$code.'|ADM')) or msg('短信验证失败');
			$_SESSION['mobile_code'] = '';
			$password = $code;
			$user = $db->get_one("SELECT username,groupid,passsalt FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1 ORDER BY userid");
			($user && $user['groupid'] == 1) or msg('管理账号不存在');
			include load('member.lang');
			require DT_ROOT.'/include/module.func.php';
			require DT_ROOT.'/module/member/member.class.php';
			$do = new member;
			$username = $user['username'];
			$user = $do->login($username, $password, 0, 'sms');
			if($user) {
				if($user['groupid'] != 1 || $user['admin'] < 1) {dalert('您无权限访问后台', $logout);}
				if(!is_founder($user['userid'])) {
					if(($DT['admin_week'] && !check_period(','.$DT['admin_week'])) || ($DT['admin_hour'] && !check_period($DT['admin_hour']))) {dalert('未被允许的管理时间', $logout);}
				}
				if($CFG['authadmin'] == 'cookie') {
					set_cookie($secretkey, $user['userid']);
				} else {
					$_SESSION[$secretkey] = $user['userid'];
				}
				require DT_ROOT.'/module/destoon/admin/admin.class.php';
				$admin = new admin;
				$admin->cache_right($user['userid']);
				$admin->cache_panel($user['userid']);
				$admin->cache_menus($user['userid']);
				if($DT['login_log']) {$do->login_log($username, $password, $user['passsalt'], 1);}
				dheader($forward);
			} else {
				if($DT['login_log']) {$do->login_log($username, $password, $user['passsalt'], 1, $do->errmsg);}
				msg($do->errmsg, '?file='.$file.'&action=sms&forward='.$_forward);
			}
		} else {
			$verfiy = 0;
			if(isset($auth)) {
				$auth = decrypt($auth, DT_KEY.'VSMS');
				if(is_mobile($auth)) {
					$verfiy = 1;
					$mobile = $auth;
				}
			}
		}
	break;
	case 'send':
		include load('member.lang');
		require DT_ROOT.'/module/member/global.func.php';
		$could_sms or exit('close');
		is_mobile($mobile) or exit('format');
		$user = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1 ORDER BY userid");
		($user && $user['groupid'] == 1) or exit('exist');
		isset($_SESSION['mobile_send']) or $_SESSION['mobile_send'] = 0;
		isset($_SESSION['mobile_time']) or $_SESSION['mobile_time'] = 0;
		if($_SESSION['mobile_send'] > 9) {exit('max');}
		if($_SESSION['mobile_time'] && ((DT_TIME - $_SESSION['mobile_time']) < 60)) {exit('fast');}
		if(max_sms($mobile)) {exit('max');}
		$mobilecode = random(6, '0-9');
		$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode.'|ADM');
		$_SESSION['mobile_time'] = DT_TIME;
		$_SESSION['mobile_oppo'] = 0;
		$_SESSION['mobile_send'] = $_SESSION['mobile_send'] + 1;
		$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days']*10)).$DT['sms_sign'];
		send_sms($mobile, $content);
		exit('ok');
	break;
	case 'temp':
		if(strpos(get_env('self'), '/admin'.DT_EXT) !== false) msg('后台地址未更改', $logout);
		$auth = isset($auth) ? decrypt($auth, DT_KEY.'TMPA') : '';
		strpos($auth, '|') !== false or msg('授权链接已失效', $logout);
		$arr = explode('|', $auth);
		$username = $arr[0];
		check_name($username) or msg('会员错误', $logout);
		if($arr[2]) {
			if(is_ip($arr[2])) {
				if(DT_IP != $arr[2]) msg('IP地址错误', $logout);
			} else {
				if(strpos(ip2area(DT_IP), $arr[2]) === false) msg('IP归属地错误', $logout);
			}
		}
		$totime = intval($arr[1]);
		$totime > DT_TIME or msg('授权已过期', $logout);
		$expiry = $totime - DT_TIME;
		$expiry <= 36000 or msg('授权时间过长', $logout);
		$r = $db->get_one("SELECT username,passport,groupid,admin,password,passsalt,loginip,mobile,vmobile FROM {$DT_PRE}member WHERE username='$username'");
		if($r && $r['groupid'] == 1 && $r['admin'] > 0) {
			include load('member.lang');
			require DT_ROOT.'/include/module.func.php';
			require DT_ROOT.'/module/member/member.class.php';
			$do = new member;
			$user = $do->login($username, '', $expiry, 'tmp');
			if($user) {
				if($CFG['authadmin'] == 'cookie') {
					set_cookie($secretkey, $user['userid']);
				} else {
					$_SESSION[$secretkey] = $user['userid'];
				}
				set_cookie('username', '');
				msg('授权登录成功', '?action=dashboard');
			} else {
				msg($do->errmsg, $logout);
			}
		} else {
			msg('管理账号不存在', $logout);
		}
	break;
	default:
		if(!$could_name) {
			$action = 'sms';
			$submit = $verfiy = 0;
		}
		if($submit) {
			$msg = captcha($captcha, $DT['captcha_admin'], true);
			if($msg) {msg('验证码填写错误');}
			if(strlen($username) < 3) {msg('请输入正确的用户名');}
			if(strlen($password) < 6 || strlen($password) > 32) {msg('请输入正确的密码');}
			if(is_email($username)) {
				$condition = "email='$username' AND vemail=1";
			} else if(is_mobile($username)) {
				$condition = "mobile='$username' AND vmobile=1";
			} else if(check_name($username)) {
				$condition = "username='$username'";
			} else {
				msg('账号格式错误');
			}
			$r = $db->get_one("SELECT username,passport,groupid,admin,password,passsalt,loginip,mobile,vmobile FROM {$DT_PRE}member WHERE {$condition} ORDER BY userid");
			if($r && $r['groupid'] == 1 && $r['admin'] > 0) {
				if($MOD['verfiy_login'] && $could_sms && is_mobile($r['mobile']) && $r['vmobile'] && $r['loginip'] != DT_IP) {
					if(ip2area($r['loginip']) != ip2area(DT_IP)) {
						if($r['password'] != dpassword($password, $r['passsalt'])) {message($L['member_login_password_bad']);}
						dheader('?file='.$file.'&action=sms&auth='.encrypt($r['mobile'], DT_KEY.'VSMS').'&forward='.$_forward);
					}
				}
				$username = $r['username'];
			} else {
				msg('管理账号不存在');
			}
			include load('member.lang');
			require DT_ROOT.'/include/module.func.php';
			require DT_ROOT.'/module/member/member.class.php';
			$do = new member;
			$user = $do->login($username, $password);
			if($user) {
				if($user['groupid'] != 1 || $user['admin'] < 1) dalert('您无权限访问后台', $logout);
				if(!is_founder($user['userid'])) {
					if(($DT['admin_week'] && !check_period(','.$DT['admin_week'])) || ($DT['admin_hour'] && !check_period($DT['admin_hour']))) dalert('未被允许的管理时间', $logout);
				}
				if($CFG['authadmin'] == 'cookie') {
					set_cookie($secretkey, $user['userid']);
				} else {
					$_SESSION[$secretkey] = $user['userid'];
				}
				require DT_ROOT.'/module/destoon/admin/admin.class.php';
				$admin = new admin;
				$admin->cache_right($user['userid']);
				$admin->cache_panel($user['userid']);
				$admin->cache_menus($user['userid']);
				if($DT['login_log']) {$do->login_log($username, $password, $user['passsalt'], 1);}
				dheader($forward);
			} else {
				if($DT['login_log']) {$do->login_log($username, $password, $user['passsalt'], 1, $do->errmsg);}
				msg($do->errmsg, '?file='.$file.'&forward='.$_forward);
			}
		} else {
			if(strpos($DT_URL, DT_PATH) === false) {dheader(DT_PATH.basename(get_env('self')));}
			$username = isset($username) ? $username : $_username;
		}
	break;
}
include tpl('login');
?>