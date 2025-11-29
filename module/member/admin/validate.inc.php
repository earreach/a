<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('资料审核', '?moduleid='.$moduleid.'&file='.$file.'&action=member'),
    array('商铺审核', '?moduleid='.$moduleid.'&file='.$file.'&action=home'),
    array('公司认证', '?moduleid='.$moduleid.'&file='.$file.'&action=company'),
    array('银行认证', '?moduleid='.$moduleid.'&file='.$file.'&action=bank'),
    array('商铺认证', '?moduleid='.$moduleid.'&file='.$file.'&action=shop'),
    array('实名认证', '?moduleid='.$moduleid.'&file='.$file.'&action=truename'),
    array('手机认证', '?moduleid='.$moduleid.'&file='.$file.'&action=mobile'),
    array('邮件认证', '?moduleid='.$moduleid.'&file='.$file.'&action=email'),
    array('昵称修改', '?moduleid='.$moduleid.'&file='.$file.'&action=passport'),
    array('帐号修改', '?moduleid='.$moduleid.'&file='.$file.'&action=username'),
    array('账号注销', '?moduleid='.$moduleid.'&file='.$file.'&action=close'),
);
$table = $DT_PRE.'validate';
$V = array('member'=>'资料审核', 'home'=>'商铺审核', 'company'=>'公司认证', 'bank'=>'银行认证', 'shop'=>'商铺认证', 'truename'=>'实名认证', 'mobile'=>'手机认证', 'email'=>'邮件认证', 'passport'=>'昵称修改', 'username'=>'帐号修改', 'close'=>'账号注销');
$S = array('member'=>'0', 'home'=>'1', 'company'=>'2', 'bank'=>'3', 'shop'=>'4', 'truename'=>'5', 'mobile'=>'6', 'email'=>'7', 'passport'=>'8', 'username'=>'9', 'close'=>'10');
$reason = isset($reason) ? trim($reason) : '';
if($reason == '操作原因') $reason = '';
$msg = isset($msg) ? 1 : 0;
$eml = isset($eml) ? 1 : 0;
$sms = isset($sms) ? 1 : 0;
$wec = isset($wec) ? 1 : 0;
if(!$DT['sms']) $sms = 0;
if(!$EXT['weixin']) $wec = 0;

$action or $action = 'member';
switch($action) {
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		if($job == 'home') {
			$db->query("DELETE FROM {$DT_PRE}company_check WHERE itemid IN ($itemids)");
		} else if($job == 'member') {
			$db->query("DELETE FROM {$DT_PRE}member_check WHERE itemid IN ($itemids)");
		} else {
			$db->query("DELETE FROM {$table} WHERE itemid IN ($itemids)");
		}
		dmsg('删除成功', $forward);
	break;
	case 'clear':
		$time = $DT_TODAY - 90*86400;
		if($job == 'home') {
			$db->query("DELETE FROM {$DT_PRE}company_check WHERE addtime<$time");
		} else if($job == 'member') {
			$db->query("DELETE FROM {$DT_PRE}member_check WHERE addtime<$time");
		}
		dmsg('清理成功', $forward);
	break;
	case 'cancel':
		$itemid or msg('请选择记录');
		$i = 0;
		foreach($itemid as $id) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$id AND status=3");
			if($r) {
				$username = $r['username'];
				$user = userinfo($username);
				$userid = $user['userid'];
				$fd = $r['type'];
				$vfd = 'v'.$r['type'];
				if($r['thumb']) delete_upload($r['thumb'], $userid, $itemid);
				if($r['thumb1']) delete_upload($r['thumb1'], $userid, $itemid);
				if($r['thumb2']) delete_upload($r['thumb2'], $userid, $itemid);
				if($user) $db->query("UPDATE {$DT_PRE}member SET `{$vfd}`=0 WHERE userid=$userid");
				$db->query("DELETE FROM {$table} WHERE itemid=$id");
				$user[$vfd] = 0;
				update_validate($user);
				userclean($username);
				if($msg || $eml) {
					$content = $title = '您的'.$V[$fd].'已经被取消，请重新认证';
					if($reason) $content .= '<br/>取消原因:'.nl2br($reason);
					if($msg) send_message($username, $title, $content);
					if($eml) send_mail($user['email'], $title, $content);
				}
				$content = '您的'.$V[$fd].'已经被取消，请重新认证';
				if($reason) $content .= ',取消原因:'.$reason;
				if($sms) send_sms($user['mobile'], $content.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $content);
				$i++;
			}
		}
		dmsg('取消认证 '.$i.' 条', $forward);		
	break;
	case 'reject':
		$itemid or msg('请选择记录');
		$i = 0;
		foreach($itemid as $id) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$id AND status=2");
			if($r) {
				$username = $r['username'];
				$user = userinfo($username);
				$userid = $user['userid'];
				$fd = $r['type'];
				if($r['thumb']) delete_upload($r['thumb'], $userid, $itemid);
				if($r['thumb1']) delete_upload($r['thumb1'], $userid, $itemid);
				if($r['thumb2']) delete_upload($r['thumb2'], $userid, $itemid);
				$db->query("DELETE FROM {$table} WHERE itemid=$id");
				if($msg || $eml) {
					$content = $title = '您的'.$V[$fd].'没有通过审核，请重新认证';
					if($reason) $content .= '<br/>失败原因:'.nl2br($reason);
					if($msg) send_message($username, $title, $content);
					if($eml) send_mail($user['email'], $title, $content);
				}
				$content = '您的'.$V[$fd].'没有通过审核，请重新认证';
				if($reason) $content .= ',失败原因:'.$reason;
				if($sms) send_sms($user['mobile'], $content.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $content);
				$i++;
			}
		}
		dmsg('拒绝认证 '.$i.' 条', $forward);		
	break;
	case 'check':
		$itemid or msg('请选择记录');
		$i = 0;
		foreach($itemid as $id) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$id");
			if($r) {
				$value = $r['title'];
				$username = $r['username'];
				$user = userinfo($username);
				$userid = $user['userid'];
				$fd = $r['type'];
				$vfd = 'v'.$r['type'];
				if($user) {
					if($fd == 'company') {
						$taxid = $r['title1'];
						$db->query("UPDATE {$DT_PRE}company SET company='$value',taxid='$taxid' WHERE userid=$userid");
						$db->query("UPDATE {$DT_PRE}member SET company='$value' WHERE userid=$userid");
					}
					if($fd == 'bank') {
						$account = $r['title1'];
						$branch = $r['title2'];
						$db->query("UPDATE {$DT_PRE}member_misc SET bank='$value',branch='$branch',account='$account' WHERE userid=$userid");
						$db->query("UPDATE {$DT_PRE}member SET `{$vfd}`=1 WHERE userid=$userid");
					} elseif($fd == 'truename') {
						$idtype = $r['title1'];
						$idno = $r['title2'];
						$db->query("UPDATE {$DT_PRE}member_misc SET idtype='$idtype',idno='$idno' WHERE userid=$userid");
						$db->query("UPDATE {$DT_PRE}member SET `{$fd}`='$value',`{$vfd}`=1 WHERE userid=$userid");
					} else {
						$db->query("UPDATE {$DT_PRE}member SET `{$fd}`='$value',`{$vfd}`=1 WHERE userid=$userid");
					}
				}
				$db->query("UPDATE {$table} SET status=3,editor='$_username',edittime=$DT_TIME WHERE itemid=$id");
				$user[$vfd] = 1;
				update_validate($user);
				userclean($username);
				if($msg || $eml) {
					$content = $title = '您的'.$V[$fd].'已经通过审核';
					if($reason) $content .= '<br/>'.nl2br($reason);
					if($msg) send_message($username, $title, $content);
					if($eml) send_mail($user['email'], $title, $content);
				}
				$content = '您的'.$V[$fd].'已经通过审核';
				if($reason) $content .= ','.$reason;
				if($sms) send_sms($user['mobile'], $content.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $content);
				$i++;
			}
		}
		dmsg('通过认证 '.$i.' 条', $forward);		
	break;
	case 'close_reject':
		$itemid or msg('请选择记录');
		$i = 0;
		foreach($itemid as $id) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$id AND status=2 AND type='close'");
			if($r) {
				$username = $r['username'];
				$user = userinfo($username);
				$userid = $user['userid'];
				$fd = $r['type'];
				if($r['thumb']) delete_upload($r['thumb'], $userid, $itemid);
				if($r['thumb1']) delete_upload($r['thumb1'], $userid, $itemid);
				if($r['thumb2']) delete_upload($r['thumb2'], $userid, $itemid);
				$db->query("DELETE FROM {$table} WHERE itemid=$id");
				if($msg || $eml) {
					$content = $title = '您的'.$V[$fd].'没有通过审核，请重新申请';
					if($reason) $content .= '<br/>失败原因:'.nl2br($reason);
					if($msg) send_message($username, $title, $content);
					if($eml) send_mail($user['email'], $title, $content);
				}
				$content = '您的'.$V[$fd].'没有通过审核，请重新申请';
				if($reason) $content .= ',失败原因:'.$reason;
				if($sms) send_sms($user['mobile'], $content.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $content);
				$i++;
			}
		}
		dmsg('拒绝审核 '.$i.' 条', $forward);		
	break;
	case 'close_check':
		$itemid or msg('请选择记录');
		require DT_ROOT.'/module/'.$module.'/member.class.php';
		$do = new member;
		$i = 0;
		foreach($itemid as $id) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE itemid=$id AND status=2 AND type='close'");
			if($r) {
				$value = $r['title'];
				$username = $r['username'];
				$user = userinfo($username);
				if($user['groupid'] == 1) continue;
				$userid = $user['userid'];
				$fd = $r['type'];
				$db->query("UPDATE {$table} SET status=3,editor='$_username',edittime=$DT_TIME WHERE itemid=$id");
				if($msg || $eml) {
					$content = $title = '您的'.$V[$fd].'已经通过';
					if($reason) $content .= '<br/>'.nl2br($reason);
					#if($msg) send_message($username, $title, $content);
					if($eml) send_mail($user['email'], $title, $content);
				}
				$content = '您的'.$V[$fd].'已经通过';
				if($reason) $content .= ','.$reason;
				if($sms) send_sms($user['mobile'], $content.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $content);
				$do->delete($userid);
				$i++;
			}
		}
		dmsg('通过审核 '.$i.' 条', $forward);		
	break;
	case 'member':
		$sfields = array('按条件', '会员名', '公司名', '资料内容', 'IP', '操作人', '审核结果');
		$dfields = array('username', 'username', 'company', 'content', 'ip', 'editor', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) ? intval($status) : 0;
		(isset($username) && check_name($username)) or $username = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($status) $condition .= $status == 1 ? " AND edittime<1" : " AND edittime>0";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}member_check WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}member_check WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$lists[] = $r;
		}
		include tpl('validate_member', $module);
	break;
	case 'show':
		$itemid or msg();
		$c = $db->get_one("SELECT * FROM {$DT_PRE}member_check WHERE itemid=$itemid");
		$c or msg('记录不存在');
		$username = $c['username'];
		$U = userinfo($username);
		$U or msg('会员不存在');
		$addtime = timetodate($c['addtime'], 6);
		$edittime = $c['edittime'];
		$ip = $c['ip'];
		$userid = $U['userid'];
		$E = dstripslashes(unserialize($c['content']));
		$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $DT_PRE.'company_data');
		$t = $db->get_one("SELECT * FROM {$content_table} WHERE userid=$userid");
		$U['content'] = $t['content'];
		if(isset($E['regunit']) && !isset($E['capital'])) $E['capital'] = $U['capital'];
		$ECK = array(
			'sign' => '个性签名',
			'cover' => '空间封面',
			'thumb' => '形象图片',
			'areaid' => '所在地区',
			'type' => '公司类型',
			'business' => '经营范围',
			'regyear' => '成立年份',
			'capital' => '注册资本',
			'address' => '公司地址',
			'telephone' => '联系电话',
			'gzh' => '微信公众号',
			'gzhqr' => '公众号二维码',
			'content' => '公司介绍',
		);
		if($submit) {
			isset($pass) or $pass = array();
			$sql1 = $sql2 = $sql3 = $sql4 = '';
			$k = 'sign';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql1 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'cover';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				if($U[$k]) delete_upload($U[$k], $userid);
				$sql3 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'thumb';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				if($U[$k]) delete_upload($U[$k], $userid);
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'areaid';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql1 .= ",`{$k}`='".addslashes($E[$k])."'";
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'type';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'business';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'regyear';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'capital';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
				if(isset($E['regunit'])) $sql2 .= ",regunit='".addslashes($E['regunit'])."'";
			}
			$k = 'address';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'telephone';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'gzh';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'gzhqr';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				if($U[$k]) delete_upload($U[$k], $userid);
				$sql2 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$k = 'content';
			if(isset($pass[$k]) && $pass[$k] && isset($E[$k])) {
				delete_diff($E[$k], $U[$k], $userid);
				$sql4 .= ",`{$k}`='".addslashes($E[$k])."'";
			}
			$title = '会员资料修改审核结果';
			$content = '尊敬的会员：<br/>您的会员资料修改已经审核，现将结果通知如下：<br/>';
			foreach($E as $k=>$v) {
				if(!isset($ECK[$k]) || !isset($pass[$k])) continue;
				$content .= $ECK[$k].' ---------- '.($pass[$k] ? '<span style="color:green;">已通过</span>' : '<span style="color:red;">未通过</span>').'<br/>';
			}
			if($reason) $content .= '操作原因：'.nl2br($reason).'<br/>';
			if($msg) send_message($username, $title, $content);
			if($eml) send_mail($U['email'], $title, $content);
			if($sms) send_sms($U['mobile'], '您的会员资料修改审核结果已通过站内信发送，请注意查阅');
			if($wec) send_weixin($username, '您的会员资料修改审核结果已通过站内信发送，请注意查阅');
			if($sql1) $db->query("UPDATE {$DT_PRE}member SET ".substr($sql1, 1)." WHERE userid=$userid");
			if($sql2) $db->query("UPDATE {$DT_PRE}company SET ".substr($sql2, 1)." WHERE userid=$userid");
			if($sql3) $db->query("UPDATE {$DT_PRE}member_misc SET ".substr($sql3, 1)." WHERE userid=$userid");
			if($sql4) $db->query("UPDATE {$content_table} SET ".substr($sql4, 1)." WHERE userid=$userid");
			userclean($username);
			$res = array();
			foreach($pass as $k=>$v) {
				$res[$k]['p'] = $v;
				$res[$k]['v'] = $U[$k];
			}
			$note = addslashes(serialize($res));
			$db->query("UPDATE {$DT_PRE}member_check SET edittime=$DT_TIME,editor='$_username',note='$note' WHERE itemid=$itemid");
			dmsg('操作成功', '?moduleid='.$moduleid.'&file='.$file.'&action=member');
		} else {
			if($c['note']) {
				if(substr($c['note'], 0, 2) == 'a:') {
					$arr = unserialize($c['note']);
					if(is_array($arr)) {
						$note = '';
						foreach($arr as $k=>$v) {
							$note .= ($v['p'] ? '<span style="color:green;">已通过</span>' : '<span style="color:red;">未通过</span>').' ---------- '.$ECK[$k].'<br/>';
							$U[$k] = $v['v'];
						}
						$c['note'] = $note;
					}
				}
			}
			include tpl('validate_show', $module);
		}
	break;
	case 'home':
		$sfields = array('按条件', '会员名', '公司名', '设置内容', '菜单内容', 'IP', '操作人', '审核结果');
		$dfields = array('username', 'username', 'company', 'content', 'homepage', 'ip', 'editor', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) ? intval($status) : 0;
		(isset($username) && check_name($username)) or $username = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($username) $condition .= " AND username='$username'";
		if($status) $condition .= $status == 1 ? " AND edittime<1" : " AND edittime>0";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}company_check WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}company_check WHERE {$condition} ORDER BY addtime DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$lists[] = $r;
		}
		include tpl('validate_home', $module);
	break;
	case 'view':
		$itemid or msg();
		$c = $db->get_one("SELECT * FROM {$DT_PRE}company_check WHERE itemid=$itemid");
		$c or msg('记录不存在');
		$username = $c['username'];
		$U = userinfo($username);
		$U or msg('会员不存在');
		$addtime = timetodate($c['addtime'], 6);
		$edittime = $c['edittime'];
		$ip = $c['ip'];
		$userid = $U['userid'];
		$E = dstripslashes(unserialize($c['content']));
		$H = get_company_setting($userid);
		foreach($E as $k=>$v) {
			if(!isset($H[$k])) $H[$k] = '';
		}
		$S = array(
			'background' => '自定义背景图',
			'bgcolor' => '自定义背景色',
			'logo' => '自定义LOGO',
			'css' => '自定义CSS',
			'bannerw' => '横幅宽度',
			'bannerh' => '横幅高度',
			'bannert' => '横幅显示方式',
			'banner' => '横幅图片',
			'bannerf' => '横幅视频',
			'banner1' => '横幅图片1',
			'bannerlink1' => '横幅链接1',
			'banner2' => '横幅图片2',
			'bannerlink2' => '横幅链接2',
			'banner3' => '横幅图片3',
			'bannerlink3' => '横幅链接3',
			'banner4' => '横幅图片4',
			'bannerlink4' => '横幅链接4',
			'banner5' => '横幅图片5',
			'bannerlink5' => '横幅链接5',
			'video' => '形象视频',
			'announce' => '网站公告',
			'show_stats' => '访问次数',
			'side_width' => '侧栏宽度',
			'side_pos' => '侧栏位置',
			'intro_length' => '公司简介字符数',
			'seo_title' => '首页SEO标题',
			'seo_keywords' => '网站关键词',
			'seo_description' => '网站描述',
			'map' => '地图坐标',
			'stats_type' => '统计类型',
			'stats' => '统计帐号',
			'kf_type' => '客服类型',
			'kf' => '客服帐号',
		);
		$M = dstripslashes(unserialize($c['homepage']));
		$O = get_company_home($userid);
		foreach($M as $k=>$v) {
			if(!isset($O[$k])) $O[$k] = array();
		}
		$N = array(
			'menu' => '导航菜单',
			'side' => '侧栏设置',
			'main' => '首页设置',
		);
		if($submit && !$edittime) {
			isset($pass) or $pass = array();
			foreach($E as $k=>$v) {
				if(!isset($pass[$k])) continue;
				if(!$pass[$k]) continue;			
				$db->query("DELETE FROM {$DT_PRE}company_setting WHERE userid=$userid AND item_key='$k'");
				$db->query("INSERT INTO {$DT_PRE}company_setting (userid,item_key,item_value) VALUES ('$userid','$k','$v')");
				if(in_array($k, array('background', 'logo', 'video', 'banner', 'bannerf', 'banner1', 'banner2', 'banner3', 'banner4', 'banner5')) && is_url($H[$k])) delete_upload($H[$k], $userid);
				if($k == 'map' && strpos($v, ',') !== false) {
					list($lng, $lat) = explode(',', $v);
					if(is_numeric($lng) && is_numeric($lat)) $db->query("UPDATE {$DT_PRE}company SET lng=$lng,lat=$lat WHERE userid=$userid");
				}
			}
			foreach($N as $k1=>$v1) {
				if(!isset($pass[$k1])) continue;
				if(!$pass[$k1]) continue;
				DB::query("DELETE FROM ".DT_PRE."company_home WHERE userid=$userid AND type='$k1'");
				foreach($M[$k1] as $k2=>$v2) {
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
			$title = '商铺设置审核结果';
			$content = '尊敬的会员：<br/>您的商铺设置已经审核，现将结果通知如下：<br/>';
			foreach($pass as $k=>$v) {
				$s = isset($N[$k]) ? $N[$k] : $S[$k];
				if(!$s) continue;
				if($v) {
					$content .= $s.' ---------- <span style="color:green;">已通过</span><br/>';
				} else {
					$content .= $s.' ---------- <span style="color:red;">未通过</span><br/>';
				}
			}
			if($reason) $content .= '操作原因：'.nl2br($reason).'<br/>';

			if($msg) send_message($username, $title, $content);
			if($eml) send_mail($U['email'], $title, $content);
			if($sms) send_sms($U['mobile'], '您的商铺设置审核结果已通过站内信发送，请注意查阅');
			if($wec) send_weixin($username, '您的商铺设置审核结果已通过站内信发送，请注意查阅');
			$res = array();
			foreach($pass as $k=>$v) {
				$res[$k]['p'] = $v;
				$res[$k]['v'] = isset($O[$k]) ? $O[$k] : $H[$k];
			}
			$note = addslashes(serialize($res));
			$db->query("UPDATE {$DT_PRE}company_check SET edittime=$DT_TIME,editor='$_username',note='$note' WHERE itemid=$itemid");
			//清空相关缓存
			userclean($username);
			$dc->remove("SELECT * FROM ".DT_PRE."company_setting WHERE userid=$userid");
			$dc->remove("SELECT * FROM ".DT_PRE."company_home WHERE userid=$userid AND status>0 ORDER BY listorder ASC");
			dmsg('操作成功', '?moduleid='.$moduleid.'&file='.$file.'&action=home');
		} else {
			if($c['note']) {
				if(substr($c['note'], 0, 2) == 'a:') {
					$arr = unserialize($c['note']);
					if(is_array($arr)) {
						$note = '';
						foreach($arr as $k=>$v) {
							$note .= ($v['p'] ? '<span style="color:green;">已通过</span>' : '<span style="color:red;">未通过</span>').' ---------- '.(isset($O[$k]) ? $N[$k] : $S[$k]).'<br/>';
							if(isset($O[$k])) {
								$O[$k] = $v['v'];
							} else {
								$H[$k] = $v['v'];
							}
						}
						$c['note'] = $note;
					}
				}
			}
			include tpl('validate_view', $module);
		}
	break;
	default:
		$menuid = $S[$action];
		$edit = ($action == 'username' || $action == 'passport') ? 1 : 0;
		$sfields = array('按条件', ($edit ? '修改为' : '认证为'),  ($edit ? '修改前' : '认证前'), '会员名', 'IP', '操作人', '认证1', '认证2');
		$dfields = array('title', 'title', 'history', 'username', 'ip', 'editor', 'title1', 'title2');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'edittime', 'totime', 'totime1', 'totime2')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($type) or $type = '';
		(isset($username) && check_name($username)) or $username = '';
		$status = isset($status) ? intval($status) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($action) $condition .= " AND type='$action'";
		if($username) $condition .= " AND username='$username'";
		if($status) $condition .= " AND status=$status";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$lists[] = $r;
		}
		if($action == 'close') {
			$fields_select = str_replace('认证为', '注销原因', $fields_select);
			include tpl('validate_close', $module);
		} else {
			include tpl('validate', $module);
		}
	break;
}
?>