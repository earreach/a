<?php 
defined('IN_DESTOON') or exit('Access Denied');
class oauth {
	var $itemid;
	var $site;
	var $fields;
	var $errmsg = errmsg;

    function __construct($site) {
		$this->site = $site;
		$this->fields = array('username','site','openid','unionid','uuid','nickname','mobile','gender','city','province','country','avatar','url');
    }

    function oauth($site) {
		$this->__construct($site);
    }

	function get_wxid($name) {
		$auth = get_cookie($name);
		if($auth) {
			$auth = decrypt($auth, DT_KEY.'WXID');
			return $auth;
		}
		return '';
	}

	function userinfo($openid) {
		global $_username;
		$r = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE openid='$openid' AND site='$this->site'");
		if($r) {
			$this->itemid = $r['itemid'];
		} else {
			DB::query("INSERT INTO ".DT_PRE."oauth (username,site,openid,addtime,logintime,loginip,logintimes) VALUES ('$_username','$this->site','$openid','".DT_TIME."','".DT_TIME."','".DT_IP."','1')");
			$this->itemid = DB::insert_id();
			$r = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE itemid=$this->itemid");
		}
		if($r && $this->site == 'weixin') {
			$W = DB::get_one("SELECT * FROM ".DT_PRE."weixin_user WHERE openid='$openid'");
			if($W) {
				$sync = array();
				if($W['nickname'] && $W['nickname'] != $r['nickname']) $sync['nickname'] = $r['nickname'] = $W['nickname'];
				if($W['headimgurl'] && $W['headimgurl'] != $r['avatar']) $sync['avatar'] = $r['avatar'] = $W['headimgurl'];
				if($sync) $this->update($sync);
			}
		}
		return $r ? $r : array();
	}

	function update($post = array()) {
		if($post) {
			$sql = '';
			foreach($post as $k=>$v) {
				if(in_array($k, $this->fields)) $sql .= ",$k='".addslashes($v)."'";
			}
			if($sql) DB::query("UPDATE ".DT_PRE."oauth SET ".substr($sql, 1)." WHERE itemid=$this->itemid");
		}
	}

	function login($U = array()) {
		if($U) {
			DB::query("UPDATE ".DT_PRE."oauth SET logintimes=logintimes+1,logintime=".DT_TIME.",loginip='".DT_IP."' WHERE itemid=$this->itemid");
			DB::query("INSERT INTO ".DT_PRE."oauth_login (username,site,loginip,loginport,logintime,agent) VALUES ('".$U['username']."','".$this->site."','".DT_IP."','".get_env('port')."','".DT_TIME."','".addslashes(dhtmlspecialchars(strip_sql(strip_tags(DT_UA))))."')");
			if($this->site == 'weixin') DB::query("UPDATE ".DT_PRE."weixin_user SET logintimes=logintimes+1,logintime=".DT_TIME.",loginip='".DT_IP."' WHERE openid='$U[openid]'");
		}
	}

	function weixin($user) {
		global $weixin_openid, $wxmini_openid, $wxmini_phone;
		if(!$user) return;
		$username = $user['username'];
		$userid = $user['userid'];
		$W = array();
		if(is_openid($weixin_openid)) {
			$W = DB::get_one("SELECT * FROM ".DT_PRE."weixin_user WHERE openid='$weixin_openid'");
			if($W && $W['username'] != $username) {
				DB::query("UPDATE ".DT_PRE."weixin_user SET username='' WHERE username='$username'");
				DB::query("UPDATE ".DT_PRE."weixin_user SET username='$username' WHERE itemid=$W[itemid]");
				$W['username'] = $username;
			}
			if($W) {
				$O = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE openid='$weixin_openid' AND site='weixin'");
				if(!$O) {
					DB::query("INSERT INTO ".DT_PRE."oauth (username,site,openid,addtime,logintime,loginip,logintimes) VALUES ('$username','weixin','$weixin_openid','".DT_TIME."','".DT_TIME."','".DT_IP."','1')");
					$itemid = DB::insert_id();
					$O = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE itemid=$itemid");
				}
				if($O) {//SYNC
					$sql = "logintimes=".$W['logintimes'].",logintime=".$W['logintime'].",loginip='".$W['loginip']."'";
					if($O['addtime'] != $W['addtime']) $sql .= ",addtime='".$W['addtime']."'";
					if($O['username'] != $W['username']) $sql .= ",username='".$W['username']."'";
					if($O['nickname'] != $W['nickname']) $sql .= ",nickname='".addslashes($W['nickname'])."'";
					if($O['avatar'] != $W['headimgurl']) $sql .= ",avatar='".addslashes($W['headimgurl'])."'";
					DB::query("UPDATE ".DT_PRE."oauth SET {$sql} WHERE itemid=$O[itemid]");
				}
			}
		}
		if(is_openid($wxmini_openid)) {
			$M = DB::get_one("SELECT itemid,username,nickname,avatar,mobile FROM ".DT_PRE."oauth WHERE openid='$wxmini_openid' AND site='wxmini'");
			if($M) {
				$sql = "";
				if($M['username'] != $username) {
					DB::query("UPDATE ".DT_PRE."oauth SET username='' WHERE username='$username' AND site='wxmini'");							
					$sql .= ",username='$username'";
				}
				if(!$W && $username) $W = DB::get_one("SELECT * FROM ".DT_PRE."weixin_user WHERE username='$username'");
				if($W && $W['nickname'] && $W['nickname'] != $M['nickname']) $sql .= ",nickname='".addslashes($W['nickname'])."'";
				if($W && $W['headimgurl'] && $W['headimgurl'] != $M['avatar']) $sql .= ",avatar='".addslashes($W['headimgurl'])."'";
				if((is_mobile($wxmini_phone) && $wxmini_phone != $M['mobile'])) $sql .= ",mobile='$wxmini_phone'";
				if($sql) DB::query("UPDATE ".DT_PRE."oauth SET ".substr($sql, 1)." WHERE itemid=$M[itemid]");
			}
		}
		if(is_mobile($wxmini_phone) && (!is_mobile($user['mobile']) || !$user['vmobile'])) {
			DB::query("UPDATE ".DT_PRE."member SET vmobile=0 WHERE mobile='$wxmini_phone'");
			DB::query("UPDATE ".DT_PRE."member SET vmobile=1,mobile='$wxmini_phone' WHERE userid=$userid");
			DB::query("INSERT INTO ".DT_PRE."validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$wxmini_phone','$user[mobile]','mobile','$username','".DT_IP."','".DT_TIME."','3','wxmini','".DT_TIME."')");
			userclean($username);
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>