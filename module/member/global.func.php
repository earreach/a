<?php
defined('IN_DESTOON') or exit('Access Denied');
function update_company_setting($userid, $setting, $home) {
	$S = get_company_setting($userid);
	foreach($setting as $k=>$v) {
		if(!check_name($k)) continue;
		if(is_array($v)) continue;
		if(isset($S[$k])) {
			DB::query("UPDATE ".DT_PRE."company_setting SET item_value='$v' WHERE userid=$userid AND item_key='$k'");
		} else {
			DB::query("INSERT INTO ".DT_PRE."company_setting (userid,item_key,item_value) VALUES ('$userid','$k','$v')");
		}
		if($k == 'map' && is_lnglat($v)) {
			list($lng, $lat) = explode(',', $v);
			DB::query("UPDATE ".DT_PRE."company SET lng=$lng,lat=$lat WHERE userid=$userid");
		}
	}
	if($home) {		
		DB::query("DELETE FROM ".DT_PRE."company_home WHERE userid=$userid");
		foreach($home as $k1=>$v1) {
			if(!in_array($k1, array('menu', 'side', 'main'))) continue;
			foreach($v1 as $k2=>$v2) {
				if(!check_name($k2)) continue;
				$name = trim($v2['name']);
				$pagesize = intval($v2['pagesize']);
				if($pagesize > 100) $pagesize = 100;
				if($pagesize < 1) $pagesize = 1;
				$listorder = intval($v2['listorder']);
				$status = $v2['status'] ? 1 : 0;
				DB::query("INSERT INTO ".DT_PRE."company_home (userid,type,file,name,pagesize,listorder,status) VALUES('$userid','$k1','$k2','$name','$pagesize','$listorder','$status')");
			}
		}
	}
	return true;
}

function stock_update($itemid, $skuid, $username, $amount, $editor, $reason, $note = '') {
	$condition = $itemid ? "itemid=$itemid" : "skuid='$skuid' AND username='$username'";
	$r = DB::get_one("SELECT itemid,title,skuid,amount,username FROM ".DT_PRE."stock WHERE {$condition}");
	if($r && $r['username'] && $r['username'] == $username && $amount) {
		$itemid = $r['itemid'];
		$skuid = $r['skuid'];
		$title = addslashes($r['title']);
		$balance = $r['amount'] + $amount;
		$reason = addslashes(stripslashes(strip_tags($reason)));
		$note = addslashes(stripslashes(strip_tags($note)));
		DB::query("UPDATE ".DT_PRE."stock SET amount=amount+{$amount},edittime=".DT_TIME." WHERE itemid=$itemid");
		DB::query("INSERT INTO ".DT_PRE."stock_record (stockid,skuid,title,username,amount,balance,addtime,reason,note,editor) VALUES ('$itemid','$skuid','$title','$username','$amount','$balance','".DT_TIME."','$reason','$note','$editor')");
	}
}

function stock_check($v = array()) {
	if(is_skuid($v['skuid'])) {
		$r = DB::get_one("SELECT amount FROM ".DT_PRE."stock WHERE skuid='$v[skuid]' AND username='$v[seller]'");
	} else {
		$r = DB::get_one("SELECT amount FROM ".get_table($v['mid'])." WHERE itemid='$v[mallid]'");
	}
	return $r['amount'] < $v['number'] ? 0 : 1;
}

function max_sms($mobile) {
	global $DT, $L, $DT_TODAY, $_username;
	$max = intval($DT['sms_max']);
	if($max) {
		$condition = $_username ? "editor='$_username'" : "mobile='$mobile'";
		$condition .= " AND message LIKE '%".$L['sms_code']."%' AND sendtime>$DT_TODAY-86400";
		$items = DB::count(DT_PRE.'sms', $condition);
		if($items >= $max) return true;
	}
	return false;
}

function get_paylist() {
	global $DT_PC, $DT_MBS;
	$PAY = cache_read('pay.php');
	if($DT_PC) {
		$PAY['aliwap']['enable'] = 0;
	} else {
		if($PAY['aliwap']['enable']) {
			$PAY['alipay']['enable'] = 0;
			$tmp = $PAY['aliwap'];
			unset($PAY['aliwap']);
			$PAY = array_merge(array('aliwap'=>$tmp), $PAY);
		} else {
			if($PAY['alipay']['enable']) {
				$tmp = $PAY['alipay'];
				unset($PAY['alipay']);
				$PAY = array_merge(array('alipay'=>$tmp), $PAY);
			}
		}
	}
	if(in_array($DT_MBS, array('weixin', 'wxmini')) && $PAY['weixin']['enable']) {
		$tmp = $PAY['weixin'];
		unset($PAY['weixin']);
		$PAY = array_merge(array('weixin'=>$tmp), $PAY);
	}
	$bank = get_cookie('pay_bank');
	if($bank && $PAY[$bank]['enable']) {
		$tmp = $PAY[$bank];
		unset($PAY[$bank]);
		$PAY = array_merge(array($bank=>$tmp), $PAY);
	}
	$P = array();
	foreach($PAY as $k=>$v) {
		if($v['enable']) {
			$v['bank'] = $k;
			$P[] = $v;
		}
	}
	return $P;
}

function get_chat_id($f, $t) {
	return md5(strcmp($f, $t) > 0 ? $f.'|'.$t : $t.'|'.$f);
}

function get_chat_tb($chatid) {
	$k = 0;
	for($i = 0; $i < 32; $i++) {
		if(is_numeric($chatid[$i])) {$k = $chatid[$i]; break;}
	}
	return DT_PRE.'chat_data_'.$k;
}

function emoji_decode($str){
    return preg_replace_callback('/\[emoji\](.+?)\[\/emoji\]/s', "_emoji_decode", $str);
}

function _emoji_decode($matches) {
	return rawurldecode($matches[1]);
}

function get_orders($itemid) {
	$table = DT_PRE.'order';
	$lists = array();
	$r = DB::get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if(!$r) return $lists;
	$lists[$r['itemid']] = $r;
	$result = DB::query("SELECT * FROM {$table} WHERE pid=$itemid ORDER BY itemid DESC");
	while($r = DB::fetch_array($result)) {
		$lists[$r['itemid']] = $r;
	}
	return $lists;
}

function get_orders_id($itemid) {
	$ids = '';
	foreach(get_orders($itemid) as $k=>$v) {
		$ids .= ','.$k;
	}
	return $ids ? substr($ids, 1) : 0;
}

function get_address($username, $itemid = 0) {
	$lists = array();
	if(check_name($username)) {
		if($itemid) {
			$r = DB::get_one("SELECT * FROM ".DT_PRE."address WHERE itemid=$itemid");
			if($r && $r['username'] = $username) {
				$r['street'] = $r['address'];
				if($r['areaid']) $r['address'] = area_pos($r['areaid'], '').$r['address'];
				return $r;
			}
		} else {
			$result = DB::query("SELECT * FROM ".DT_PRE."address WHERE username='$username' ORDER BY listorder ASC,itemid ASC LIMIT 30");
			while($r = DB::fetch_array($result)) {
				$r['street'] = $r['address'];
				if($r['areaid']) $r['address'] = area_pos($r['areaid'], '').$r['address'];
				$lists[] = $r;
			}
		}
	}
	return $lists;
}

function check_validate() {
	global $DT, $DT_URL, $MOD, $MG, $_userid, $_username, $_groupid;
	if(!$_userid) return;
	$MD = $MOD['moduleid'] == 2 ? $MOD : cache_read('module-2.php');
	if(strpos($DT_URL, $MD['linkurl']) === false && strpos($DT_URL, $MD['mobile']) === false) return;
	$file = basename(get_env('self'), '.php');
	if(in_array($file, array('', 'index', 'validate', 'edit', 'send', 'account', 'avatar', 'deposit', 'weixin', 'oauth', 'logout', 'login', 'register', $DT['file_login'], $DT['file_register']))) return;
	if($_groupid > 4 && (($MD['vemail'] && $MG['vemail']) || ($MD['vmobile'] && $MG['vmobile']) || ($MD['vtruename'] && $MG['vtruename']) || ($MD['vcompany'] && $MG['vcompany']) || ($MD['vbank'] && $MG['vbank']) || ($MD['deposit'] && $MG['vdeposit']))) {
		$V = DB::get_one("SELECT vemail,vmobile,vtruename,vcompany,vbank,deposit FROM ".DT_PRE."member WHERE userid=$_userid");
		if($MD['vemail'] && $MG['vemail'] == 2 && !$V['vemail']) dheader('validate'.DT_EXT.'?action=email&itemid=1');
		if($MD['vmobile'] && $MG['vmobile'] == 2 && !$V['vmobile']) dheader('validate'.DT_EXT.'?action=mobile&itemid=1');
		if($MD['vtruename'] && $MG['vtruename'] == 2 && !$V['vtruename']) dheader('validate'.DT_EXT.'?action=truename&itemid=1');
		if($MD['vcompany'] && $MG['vcompany'] == 2 && !$V['vcompany']) dheader('validate'.DT_EXT.'?action=company&itemid=1');
		if($MD['vbank'] && $MG['vbank'] == 2 && !$V['vbank']) dheader('validate'.DT_EXT.'?action=bank&itemid=1');
		if($MD['deposit'] && $MG['vdeposit'] == 2 && $V['deposit'] < 100) dheader('deposit'.DT_EXT.'?action=add&itemid=1');
	}
	if($_groupid > 4 && $MG['vweixin'] == 2) {
		$W = DB::get_one("SELECT subscribe FROM ".DT_PRE."weixin_user WHERE username='$_username'");
		($W && $W['subscribe']) or dheader('weixin'.DT_EXT.'?itemid=1');
	}
}

function update_validate($user, $MG = array()) {
	global $MOD;
	if(!$user) return;
	$MG or $MG = cache_read('group-'.$user['groupid'].'.php');	
	$validated = 1;
	if($validated && $MOD['vemail'] && $MG['vemail'] == 2 && !$user['vemail']) $validated = 0;
	if($validated && $MOD['vmobile'] && $MG['vmobile'] == 2 && !$user['vmobile']) $validated = 0;
	if($validated && $MOD['vtruename'] && $MG['vtruename'] == 2 && !$user['vtruename']) $validated = 0;
	if($validated && $MOD['vbank'] && $MG['vbank'] == 2 && !$user['vbank']) $validated = 0;
	if($validated && $MOD['vcompany'] && $MG['vcompany'] == 2 && !$user['vcompany'] && $MG['type']) $validated = 0;
	if($validated && $MOD['vshop'] && $MG['vshop'] == 2 && !$user['vshop'] && $MG['homepage']) $validated = 0;
	if($validated && !$user['vemail'] && !$user['vmobile'] && !$user['vtruename'] && !$user['vbank'] && !$user['vcompany'] && !$user['vshop']) $validated = 0;
	$validate = $validated ? ($MG['type'] ? 2 : 1) : 0;
	if($user['validated'] != $validated || $user['validate'] != $validate) {
		DB::query("UPDATE ".DT_PRE."company SET validated=$validated WHERE userid=$user[userid]");
		DB::query("UPDATE ".DT_PRE."member SET validate=$validate WHERE userid=$user[userid]");
		userclean($user['username']);
	}
}

function black_add($username, $note = '', $user = array()) {
	global $_username;
	$B = DB::get_one("SELECT itemid FROM ".DT_PRE."member_blacklist WHERE username='$_username' AND busername='$username'");
	if($B) return true;
	$user or $user = userinfo($username);
	if(!$user) return false;
	$note = strip_tags(trim($note));
	DB::query("INSERT INTO ".DT_PRE."member_blacklist (username,buserid,busername,bpassport,addtime,note) VALUES ('$_username','".$user['userid']."','$username','".addslashes($user['passport'])."','".DT_TIME."','$note')");
	DB::query("DELETE FROM ".DT_PRE."friend WHERE username='$_username' AND fusername='$username'");
	DB::query("DELETE FROM ".DT_PRE."friend WHERE username='$username' AND fusername='$_username'");
	DB::query("DELETE FROM ".DT_PRE."follow WHERE username='$_username' AND fusername='$username'");
	DB::query("DELETE FROM ".DT_PRE."follow WHERE username='$username' AND fusername='$_username'");
	return true;
}
?>