<?php 
defined('IN_DESTOON') or exit('Access Denied');
$MOD['oauth'] or dheader('./');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
if(!in_array($action, array('bind', 'auto'))) login();
switch($action) {
	case 'bind':
		$avatar = '';
		if(!$_userid) {
			$auth = decrypt(get_cookie('bind'), DT_KEY.'BIND');
			if(strpos($auth, '|') !== false) {
				$t = explode('|', $auth);
				$itemid = intval($t[0]);
				$U = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE itemid=$itemid");
				if($U && $U['site'] == $t[1]) {
					if(check_name($U['username'])) {
						require DT_ROOT.'/module/member/member.class.php';
						$do = new member;
						$user = $do->login($U['username'], '', 0, 'oauth-'.$U['site']);
						if($user) {
							$forward_url = get_cookie('forward_url');
							if($forward_url) $forward = $forward_url;
							if(strpos($forward, 'api/oauth') !== false) $forward = '';
							$forward or $forward = $DT_PC ? 'index.php' : DT_MOB.'my.php';
							include DT_ROOT.'/api/passport.inc.php';
							dheader($forward);
						} else {
							message($do->errmsg);
						}
					} else {
						$OAUTH = cache_read('oauth.php');
						$avatar = $U['avatar'] ? $U['avatar'] : DT_PATH.'api/oauth/avatar.png';
						$icon = DT_PATH.'api/oauth/'.$U['site'].'/icon.png';
						$nickname = $U['nickname'] ? $U['nickname'] : 'USER';
						$site = $OAUTH[$U['site']]['name'];
						$connect = DT_PATH.'api/oauth/'.$U['site'].'/connect.php';
					}
				}
			}
		}
		$avatar or dheader($DT_PC ? 'index.php' : DT_MOB.'my.php');
		$head_title = $L['oauth_bind'];
	break;
	case 'auto':
		$avatar = '';
		if(!$_userid) {
			$auth = decrypt(get_cookie('bind'), DT_KEY.'BIND');
			if(strpos($auth, '|') !== false) {
				$t = explode('|', $auth);
				$itemid = intval($t[0]);
				$U = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE itemid=$itemid");
				if($U && $U['site'] == $t[1] && !check_name($U['username'])) {
					require DT_ROOT.'/include/client.func.php';
					require DT_ROOT.'/module/member/member.class.php';
					$do = new member;
					$post = array();
					$post['groupid'] = $post['regid'] = 5;
					$post['username'] = 'uid-'.$do->get_uid();
					$post['passport'] = $U['nickname'];
					$post['truename'] = $U['truename'];
					$post['password'] = $post['cpassword'] = $do->get_pwd();
					$post['email'] = $post['username'].'@'.$U['site'].'.sns';
					$post['areaid'] = area2id(ip2area(DT_IP));
					if(!$do->is_passport($post['passport'], 1)) $post['passport'] = $post['username'];
					$mobile = decrypt(get_cookie('wxmini_phone'), DT_KEY.'WXID');
					if(is_mobile($mobile)) {
						$post['mobile'] = $mobile;
						$post['vmobile'] = 1;
					}
					$MOD['checkuser'] = $MOD['truename_register'] = $MOD['mobile_register'] = $MOD['email_register'] = $MOD['qq_register'] = $MOD['wx_register'] = 0;
					if($do->pass($post, 1)) {
						if($do->add($post)) {
							$db->query("UPDATE {$DT_PRE}oauth SET username='$post[username]' WHERE itemid=$itemid");
							$user = $do->login($post['username'], '', 0, 'oauth-'.$U['site']);
							if($user) {
								$forward_url = get_cookie('forward_url');
								if($forward_url) $forward = $forward_url;
								if(strpos($forward, 'api/oauth') !== false) $forward = '';
								$forward or $forward = $DT_PC ? 'index.php' : DT_MOB.'my.php';
								include DT_ROOT.'/api/passport.inc.php';
								dheader($forward);
							} else {
								message($do->errmsg);
							}
						} else {
							message($do->errmsg);
						}
					} else {
						message($do->errmsg);
					}
				}
			}
		}
		dheader($DT_PC ? 'index.php' : DT_MOB.'my.php');
	break;
	case 'delete':
		$itemid or message();
		$U = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE itemid=$itemid");
		if(!$U || $U['username'] != $_username) message();
		$db->query("DELETE FROM {$DT_PRE}oauth WHERE itemid=$itemid");
		dmsg($L['oauth_quit'], '?action=index');
	break;
	case 'login':
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}oauth_login WHERE username='$_username'");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth_login WHERE username='$_username' ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['logintime'] = timetodate($r['logintime'], 5);
			$r['area'] = ip2area($r['loginip'], 2);
			$lists[] = $r;
		}
		$OAUTH = cache_read('oauth.php');
		$head_title = $L['oauth_login_title'];	
	break;
	default:
		$lists = $tags = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}oauth WHERE username='$_username' ORDER BY logintime DESC");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['logindate'] = timetodate($r['logintime'], 5);
			$r['nickname'] or $r['nickname'] = '-';
			$tags[$r['site']][] = $r;
		}
		foreach($tags as $kk=>$vv) {
			foreach($vv as $k=>$v) {
				if($k) {
					$db->query("UPDATE {$DT_PRE}oauth SET username='' WHERE itemid=$v[itemid]");
				} else {
					$lists[$kk] = $v;
				}
			}
		}
		$OAUTH = cache_read('oauth.php');
		if($OAUTH['wechat']['enable'] && $OAUTH['wechat']['id'] == 'gzh' && isset($lists['weixin']) && !isset($lists['wechat'])) $lists['wechat'] = $lists['weixin'];
		$head_title = $L['oauth_title'];	
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('oauth', $module);
?>