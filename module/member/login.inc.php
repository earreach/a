<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$forward_url = get_cookie('forward_url');
foreach(array($DT['file_login'], $DT['file_register'], '/send'.DT_EXT, '/logout'.DT_EXT, 'forward=') as $str) {
	if($forward && strpos($forward, $str) !== false) $forward = '';
	if($forward_url && strpos($forward_url, $str) !== false) $forward_url = '';
}
if(!is_url($forward)) $forward = $forward_url;
if(!is_url($forward)) $forward = $DT_PC ? $MOD['linkurl'] : DT_MOB.'my.php';
if($forward_url != $forward) set_cookie('forward_url', $forward);
$_forward = urlencode($forward);
if($_userid && !$MOD['passport'] && !in_array($action, array('oauth', 'scan'))) dheader($forward);
$MOD['enable_login'] or message($L['login_msg_close'], $forward, 3);
require DT_ROOT.'/module/'.$module.'/member.class.php';
$do = new member;
$OAUTH = cache_read('oauth.php');
$could_sms = ($MOD['login_sms'] && $DT['sms']) ? 1 : 0;
$could_scan = ($MOD['login_scan'] && ($EXT['mobile_ios'] || $EXT['mobile_adr']) && $DT_PC) ? 1 : 0;
$could_child = $MOD['child'] ? 1 : 0;
$could_weixin = ($MOD['login_weixin'] && $OAUTH['wechat']['enable'] && $DT_PC) ? 1 : 0;
if(!$action) {
	if($MOD['default_login'] == 1 && $MOD['login_sms']) $action = 'sms';
	if($DT_PC) {
		if($MOD['default_login'] == 2 && $MOD['login_weixin']) $action = 'weixin';
		if($MOD['default_login'] == 3 && $MOD['login_scan']) $action = 'scan';
	}
}
switch($action) {
	case 'child':
		$could_child or dheader('?action=login');
		if($submit) {
			captcha($captcha, $MOD['captcha_login']);
			$username = trim($username);
			$password = trim($password);
			if(!check_name($username)) message($L['login_msg_username']);
			if(strlen($password) < 5) message($L['login_msg_password']);
			$r = $db->get_one("SELECT itemid,parent,password,passsalt,status FROM {$DT_PRE}member_child WHERE username='$username'");
			if($r) {
				if($r['status'] != 3) message($L['member_login_member_ban']);
				if($r['password'] != dpassword($password, $r['passsalt'])) message($L['member_login_password_bad']);
				$cookietime = $MOD['login_time'] >= 86400 ? $MOD['login_time'] : 0;
				$user = $do->login($r['parent'], '', $cookietime, 'child', $r['itemid']);
				if($user) dheader($forward);
				message($do->errmsg);
			} else {
				message($L['login_msg_not_member']);
			}
		}		
		$head_title = $L['login_title_child'];
	break;
	case 'sms':
		$could_sms or dheader('?action=login');
		if($submit) {
			$session = new dsession();
			$_SESSION['mobile_oppo'] = $_SESSION['mobile_oppo'] + 1;
			if($_SESSION['mobile_oppo'] > 3 || $DT_TIME - $_SESSION['mobile_time'] > $MOD['auth_days']*60) $_SESSION['mobile_code'] = '';
			(is_mobile($mobile) && preg_match("/^[0-9]{6}$/", $code) && isset($_SESSION['mobile_code']) && $_SESSION['mobile_code'] == md5($mobile.'|'.$code)) or message($L['login_msg_bad_code']);
			$_SESSION['mobile_code'] = '';
			$cookietime = $MOD['login_time'] >= 86400 ? $MOD['login_time'] : 0;
			$password = $code;
			$user = $db->get_one("SELECT username,groupid,passsalt FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1 ORDER BY userid");
			if($user) {
				if(in_array($user['groupid'], array(2, 3, 4))) message($L['login_msg_bad_mobile']);
				$username = $user['username'];
				$user = $do->login($username, $password, $cookietime, 'sms');
				if($user) {
					dheader($forward);
				} else {
					if($DT['login_log'] == 2) $do->login_log($username, $password, $user['passsalt'], 0, $do->errmsg);
					message($do->errmsg);
				}
			}
			if($_SESSION['mobile_auto']) {
				require DT_ROOT.'/include/client.func.php';
				$post = array();
				$post['groupid'] = $post['regid'] = 5;
				$post['username'] = 'uid-'.$do->get_uid();
				$post['passport'] = $post['username'];
				$post['password'] = $post['cpassword'] = $do->get_pwd();
				$post['email'] = $post['username'].'@mob.sns';
				$post['areaid'] = area2id(ip2area(DT_IP));
				$post['mobile'] = $mobile;
				if($do->pass($post, 1)) {
					if($do->add($post)) {
						$username = $post['username'];
						$db->query("UPDATE {$DT_PRE}member SET vmobile=0 WHERE mobile='$mobile'");
						$db->query("UPDATE {$DT_PRE}member SET vmobile=1 WHERE username='$username'");
						$db->query("INSERT INTO {$DT_PRE}validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$mobile','$mobile','mobile','$username','$DT_IP','$DT_TIME','3','login','$DT_TIME')");
						$user = $do->login($username, $password, $cookietime, 'sms');
						if($user) {
							$_SESSION['mobile_auto'] = 0;
							dheader($forward);
						}
					}
				}
			}
			message($do->errmsg, '?action=login&forward='.$_forward);
		} else {
			$verfiy = 0;
			if(isset($auth)) {
				$auth = decrypt($auth, DT_KEY.'VSMS');
				if(is_mobile($auth)) {
					$verfiy = 1;
					$mobile = $auth;
				}
			}
			$head_title = $L['login_title_sms'];
		}
	break;
	case 'send':
		$could_sms or exit('close');
		is_mobile($mobile) or exit('format');
		$msg = captcha($captcha, 1, true);
		if($msg) exit('captcha');
		$auto = 0;
		$user = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE mobile='$mobile' AND vmobile=1 ORDER BY userid");
		if($user) {
			if(in_array($user['groupid'], array(2,3,4))) exit('exist');
		} else {
			if($MOD['login_sms'] > 1) {
				$auto = 1;
			} else {
				exit('exist');
			}
		}
		$session = new dsession();
		isset($_SESSION['mobile_send']) or $_SESSION['mobile_send'] = 0;
		isset($_SESSION['mobile_time']) or $_SESSION['mobile_time'] = 0;
		if($_SESSION['mobile_send'] > 4) exit('max');
		if($_SESSION['mobile_time'] && (($DT_TIME - $_SESSION['mobile_time']) < 60)) exit('fast');
		if(max_sms($mobile)) exit('max');
		$mobilecode = random(6, '0-9');
		$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode);
		$_SESSION['mobile_time'] = $DT_TIME;
		$_SESSION['mobile_oppo'] = 0;
		$_SESSION['mobile_send'] = $_SESSION['mobile_send'] + 1;
		$_SESSION['mobile_auto'] = $auto;
		$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days'])).$DT['sms_sign'];
		send_sms($mobile, $content);
		#log_write($content, 'sms', 1);
		exit('ok');
	break;
	case 'oauth':
		if($_userid) exit('ok');
		if(get_cookie('bind')) exit('bd');
		exit('ko');
	break;
	case 'weixin':
		$could_weixin or dheader('?action=login');
		require DT_ROOT.'/api/oauth/wechat/init.inc.php';
		$head_title = $L['login_title_weixin'];
	break;
	case 'scan':
		if($DT_PC) {
			//
		} else {
			if(isset($auth)) {
				$auth = decrypt($auth, DT_KEY.'SCANPC');
				list($username, $ip, $userid, $job, $forward) = explode('|', $auth);
				if(strpos($forward, $MOD['mobile']) === false) $forward = DT_MOB.'my.php';
				if(substr($job, 0, 5) == 'album') $forward = DT_MOB.'api/album'.DT_EXT.'?moduleid='.intval(substr($job, 5));
				if(substr($job, 0, 5) == 'group') {
					list($name, $mid, $gid) = explode('-', $job);
					if($MODULE[$mid]['module'] == 'club' && $gid) $forward = $MODULE[$mid]['mobile'].'chat'.DT_EXT.'?gid='.$gid;
				}
				if($ip == $DT_IP && $username != $_username) $user = $do->login($username, '', 0, 'scan-pc');
				dheader($forward);
			}
		}
		$could_scan or dheader('?action=login');
		$session = new dsession();
		$sid = md5(DT_IP.DT_KEY.session_id());
		$expire = $DT_TIME - 300;
		$db->query("DELETE FROM {$DT_PRE}app_scan WHERE lasttime<$expire");
		if($job == 'ajax') {
			$t = $db->get_one("SELECT * FROM {$DT_PRE}app_scan WHERE sid='$sid'");
			if($t) {
				if($t['username'] == 'sc') {
					exit('scan');
				} else if($t['username']) {
					$db->query("DELETE FROM {$DT_PRE}app_scan WHERE sid='$sid'");
					$user = $do->login($t['username'], '', 0, 'app-scan');
					exit($user ? 'ok' : 'ko');
				}
				exit('wait');
			} else {
				exit('out');
			}
		} else {
			if(strpos($forward, 'api/') !== false) $forward = '';
			$forward or $forward = $MODULE[2]['linkurl'];
			$db->query("REPLACE INTO {$DT_PRE}app_scan (sid,username,lasttime) VALUES ('$sid','','$DT_TIME')");
			#$auth = encrypt('SCAN:'.$sid, DT_KEY.'QRCODE');
			$auth = $MODULE[2]['mobile'].'account'.DT_EXT.'?action=scan&sid='.$sid;
			$auth = urlencode($auth);
		}
		$head_title = $L['login_title_scan'];
	break;
	default:
		$LOCK = cache_read($DT_IP.'.php', 'ban');
		if($LOCK && $DT_TIME - $LOCK['time'] < 3600 && $LOCK['times'] >= 2) $MOD['captcha_login'] = 1;
		isset($auth) or $auth = '';
		if($_userid) $auth = '';
		if($auth) {
			$auth = decrypt($auth, DT_KEY.'LOGIN');
			$_auth = explode('|', $auth);
			if($_auth[0] == 'LOGIN' && check_name($_auth[1]) && strlen($_auth[2]) >= $MOD['minpassword'] && $DT_TIME >= intval($_auth[3]) && $DT_TIME - intval($_auth[3]) < 30) {
				$submit = 1;
				$username = $_auth[1];
				$password = $_auth[2];
				$MOD['captcha_login'] = $captcha = 0;
			}
		}
		$action = 'login';
		if($submit) {
			captcha($captcha, $MOD['captcha_login']);
			$username = trim($username);
			$password = trim($password);
			if(strlen($username) < 3) message($L['login_msg_username']);
			if(strlen($password) < 5) message($L['login_msg_password']);
			$api_msg = $api_url = '';
			$cookietime = $MOD['login_time'] >= 86400 ? $MOD['login_time'] : 0;
			if(is_email($username)) {
				$condition = "email='$username' AND vemail=1";
			} else if(is_mobile($username)) {
				$condition = "mobile='$username' AND vmobile=1";
			} else if(check_name($username)) {
				$condition = "username='$username'";
			} else {
				if(is_clean($username)) {
					if(strlen($username) < $MOD['minusername'] || strlen($username) > $MOD['maxusername']) message($L['login_msg_username']);
					$condition = "passport='$username'";
				} else {
					message($L['login_msg_not_member']);
				}
			}
			$r = $db->get_one("SELECT username,passport,password,passsalt,loginip,mobile,vmobile FROM {$DT_PRE}member WHERE {$condition} ORDER BY userid");
			if($r) {
				if($MOD['verfiy_login'] && $could_sms && is_mobile($r['mobile']) && $r['vmobile'] && $r['loginip'] != DT_IP) {
					if(ip2area($r['loginip'], 2) != ip2area(DT_IP, 2)) {
						if($r['password'] != dpassword($password, $r['passsalt'])) message($L['member_login_password_bad']);
						dheader('?action=sms&auth='.encrypt($r['mobile'], DT_KEY.'VSMS'));
					}
				}
				$username = $r['username'];
				$passport = $r['passport'];
				if($MOD['passport'] == 'uc') include DT_ROOT.'/api/'.$MOD['passport'].'.inc.php';
			} else {
				$passport = $username;
				if($MOD['passport'] == 'uc') include DT_ROOT.'/api/'.$MOD['passport'].'.inc.php';
				message($L['login_msg_not_member']);
			}			
			$user = $do->login($username, $password, $cookietime);
			if($user) {
				if($DT['login_log'] == 2) $do->login_log($username, $password, $user['passsalt'], 0);
				include DT_ROOT.'/api/passport.inc.php';
				set_cookie('forward_url', '');
				message($api_msg, $forward);
			} else {
				if($DT['login_log'] == 2) $do->login_log($username, $password, $user['passsalt'], 0, $do->errmsg);
				message($do->errmsg, '?action=login&forward='.$_forward);
			}
		}
		$register = isset($register) && $username ? 1 : 0;
		$head_title = $register ? $L['login_title_reg'] : $L['login_title'];
	break;
}
isset($username) or $username = $_username;
isset($password) or $password = '';
$username or $username = get_cookie('username');
check_name($username) or $username = '';
$oa = 0;
foreach($OAUTH as $v) {
	if($v['enable']) {
		$oa = 1;
		break;
	}
}
if($DT_PC) {
	//
} else {
	if($oa) {
		if(in_array($DT_MBS, array('weixin', 'wxmini'))) {
			$OAUTH = array_merge(array('wechat' => $OAUTH['wechat']), $OAUTH);
		} else if(in_array($DT_MBS, array('qq', 'tim'))) {
			$OAUTH = array_merge(array('qq' => $OAUTH['qq']), $OAUTH);
		} else if(in_array($DT_MBS, array('weibo'))) {
			$OAUTH = array_merge(array('sina' => $OAUTH['sina']), $OAUTH);
		}
	}
	$js_pull = 0;
	$head_name = $head_title;
	$foot = 'my';
}
include template('login', $module);
?>