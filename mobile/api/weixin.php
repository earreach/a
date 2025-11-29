<?php
require '../../common.inc.php';
in_array($DT_MBS, array('weixin', 'wxmini')) or message('Not in weixin');
require DT_ROOT.'/api/weixin/init.inc.php';
include DT_ROOT.'/module/member/oauth.class.php';
$site = 'weixin';
$oa = new oauth($site);
if($action == 'login') {
	$url = get_cookie('forward_url');
	$forward = $url ? $url : DT_MOB.'my'.DT_EXT;
	$weixin_openid = $oa->get_wxid('weixin_openid');
	if(is_openid($weixin_openid)) {
		$O = $oa->userinfo($weixin_openid);
		$U = weixin_user($weixin_openid);
		if($U) {
			$post = array();
			if($U['nickname'] && $O['nickname'] != $U['nickname']) $post['nickname'] = $U['nickname'];
			if($U['username'] && $O['username'] != $U['username']) $post['username'] = $U['username'];
			if($U['unionid'] && $O['unionid'] != $U['unionid']) $post['unionid'] = $U['unionid'];
			if($U['headimgurl'] && $O['avatar'] != $U['headimgurl']) $post['avatar'] = $U['headimgurl'];
			if($post) $oa->update($post);

			include load('member.lang');
			$MOD = cache_read('module-2.php');
			include DT_ROOT.'/include/post.func.php';
			include DT_ROOT.'/include/module.func.php';
			include DT_ROOT.'/module/member/member.class.php';
			$do = new member;
			$wxmini_openid = $oa->get_wxid('wxmini_openid');
			is_openid($wxmini_openid) or $wxmini_openid = '';
			$wxmini_phone = $oa->get_wxid('wxmini_phone');
			is_mobile($wxmini_phone) or $wxmini_phone = '';
			$username = $U['username'];
			if(!$username && $wxmini_phone) {
				$t = $db->get_one("SELECT username FROM {$DT_PRE}member WHERE mobile='$wxmini_phone' AND vmobile=1 ORDER BY userid");
				if($t) $username = $t['username'];
			}
			if($username) {
				$user = $do->login($username, '', 0, 'oauth-'.$site);
				if($user) {
					$oa->weixin($user);
					include DT_ROOT.'/api/passport.inc.php';
					dheader($forward);
				}
			} else {
				set_cookie('bind', encrypt($oa->itemid.'|'.$site.'|'.$U['nickname'], DT_KEY.'BIND'));
				if(strpos($url, 'action=bind') !== false) dheader($MODULE[2]['mobile'].'oauth'.DT_EXT.'?action=bind');
				require DT_ROOT.'/include/client.func.php';
				$post = array();
				$post['groupid'] = $post['regid'] = 5;
				$post['username'] = 'uid-'.$do->get_uid();
				$post['passport'] = $U['nickname'];
				$post['password'] = $post['cpassword'] = $do->get_pwd();
				$post['email'] = $post['username'].'@weixin.sns';
				$post['areaid'] = area2id(ip2area(DT_IP));
				if($wxmini_phone) $post['mobile'] = $wxmini_phone;
				if(!$do->is_passport($post['passport'], 1)) $post['passport'] = $post['username'];
				$MOD['checkuser'] = $MOD['truename_register'] = $MOD['mobile_register'] = $MOD['email_register'] = $MOD['qq_register'] = $MOD['wx_register'] = 0;
				if($do->pass($post, 1)) {
					if($do->add($post)) {
						$user = $do->login($post['username'], '', 0, 'oauth-'.$site);
						if($user) {
							$oa->weixin($user);
							include DT_ROOT.'/api/passport.inc.php';
							dheader($forward);
						}
					}
				}
				dheader($MODULE[2]['mobile'].'oauth'.DT_EXT.'?action=bind');
			}
		}
	}
	dheader($forward);
} else if($action == 'member') {
	isset($auth) or $auth = '';
	if($auth) {
		$weixin_openid = decrypt($auth, DT_KEY.'WXID');
		if(is_openid($weixin_openid)) {
			set_cookie('weixin_openid', $auth);
			set_cookie('forward_url', DT_MOB.'my'.DT_EXT.'?action=bind');
			dheader('?action=login&reload='.$DT_TIME);
		}
	}
} else if($action == 'callback') {
	if($code) {
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.WX_APPID.'&secret='.WX_APPSECRET.'&code='.$code.'&grant_type=authorization_code';
		$rec = dcurl($url);
		$arr = json_decode($rec, true);
		if($arr['openid'] && is_openid($arr['openid'])) {
			$wxmini_openid = $oa->get_wxid('wxmini_openid');
			is_openid($wxmini_openid) or $wxmini_openid = '';
			$wxmini_phone = $oa->get_wxid('wxmini_phone');
			is_mobile($wxmini_phone) or $wxmini_phone = '';
			$weixin_openid = $arr['openid'];
			$unionid = is_openid($arr['unionid']) ? $arr['unionid'] : '';
			$O = $oa->userinfo($weixin_openid);
			$U = weixin_user($weixin_openid);
			$itemid = $U['itemid'];
			//if($U['nickname']) $arr['access_token'] = '';
			if($_userid) {
				if($U['username'] != $_username) {
					$oa->weixin(userinfo($_username));
					$U['username'] = $_username;
				}
			}
			$oa->login($U);
			if($arr['access_token']) {
				$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$weixin_openid.'&lang=zh_CN';
				$rec = dcurl($url);
				$info = json_decode($rec, true);
				$info['unionid'] = $unionid;
				$sql = '';
				if(isset($info['nickname'])) {
					foreach(array('nickname', 'sex', 'city', 'province', 'country', 'language', 'headimgurl', 'unionid', 'remark') as $v) {
						if(isset($info[$v])) $sql .= ",".$v."='".addslashes($info[$v])."'";
					}
				}
				if($sql) $db->query("UPDATE {$DT_PRE}weixin_user SET ".substr($sql, 1)." WHERE itemid=$itemid");
			}
			set_cookie('weixin_openid', encrypt($weixin_openid, DT_KEY.'WXID'));
			dheader('?action=login&reload='.$DT_TIME);
		}
	}
} else {
	isset($url) or $url = DT_MOB.'my'.DT_EXT;
	if($url == 'bind') $url = $MODULE[2]['mobile'].'oauth'.DT_EXT.'?action=bind';
	if($moduleid > 3) $url = $MODULE[$moduleid]['mobile'];
	if($_userid && strpos($url, 'openid'.DT_EXT) === false) dheader($url);
	set_cookie('forward_url', $url);
	#$scope = $action == 'connect' ? 'snsapi_userinfo' : 'snsapi_base';
	$scope = 'snsapi_userinfo';
	if(get_cookie('weixin_openid') && $scope == 'snsapi_base') dheader('?action=login&reload='.$DT_TIME);
	dheader('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.WX_APPID.'&redirect_uri='.urlencode(($EXT['mobile_domain'] ? $EXT['mobile_domain'] : DT_PATH.'mobile/').'api/weixin'.DT_EXT.'?action=callback').'&response_type=code&scope='.$scope.'&state=1#wechat_redirect');
}
dheader(DT_MOB.'index'.DT_EXT.'?reload='.$DT_TIME);
?>