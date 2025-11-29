<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
$do = new $module();
$menus = array (
    array('添加会员', '?moduleid='.$moduleid.'&action=add'),
    array('会员列表', '?moduleid='.$moduleid),
    array('审核会员', '?moduleid='.$moduleid.'&action=check'),
    array('会员副表', '?moduleid='.$moduleid.'&action=misc'),
    array('联系会员', '?moduleid='.$moduleid.'&file=contact'),
);
isset($userid) or $userid = 0;
if(in_array($action, array('add', 'edit', 'show'))) {
	$MFD = cache_read('fields-member.php');
	$CFD = cache_read('fields-company.php');
	isset($post_fields) or $post_fields = array();
	if($MFD || $CFD) require DT_ROOT.'/include/fields.func.php';
}
if($_catids || $_areaids) {
	if(isset($userid)) $itemid = $userid;
	if(isset($post['areaid'])) $post['areaid'] = $post['areaid'];
	require DT_ROOT.'/module/destoon/admin/check.inc.php';
}
if(!$DT['im_qq']) $MOD['qq_register'] = 0;
if(!$DT['im_wx']) $MOD['wx_register'] = 0;
if(in_array($action, array('', 'check'))) {
	$sfields = array('按条件', '公司名', '商铺名', '会员名', '昵称', '个性签名', '姓名', '手机号码', '部门', '职位', 'Email', 'QQ', '微信', '阿里旺旺', 'Skype', '注册IP', '登录IP', '客服专员', '邀请人');
	$dfields = array('username', 'company', 'shop', 'username', 'passport', 'sign', 'truename', 'mobile', 'department', 'career', 'email', 'qq', 'wx', 'ali', 'skype', 'regip', 'loginip', 'support', 'inviter');
	$sorder  = array('结果排序方式', '注册时间降序', '注册时间升序', '修改时间降序', '修改时间升序', '登录时间降序', '登录时间升序', '登录次数降序', '登录次数升序', '账户'.$DT['money_name'].'降序', '账户'.$DT['money_name'].'升序', '会员'.$DT['credit_name'].'降序', '会员'.$DT['credit_name'].'升序', '短信余额降序', '短信余额升序', '站内信件降序', '站内信件升序', '站内交谈降序', '站内交谈升序', '粉丝数量降序', '粉丝数量升序', '关注数量降序', '关注数量升序', '动态数量降序', '动态数量升序', '会员组降序', '会员组升序', '积分组降序', '积分组升序', '会员ID降序', '会员ID升序');
	$dorder  = array('userid DESC', 'regtime DESC', 'regtime ASC', 'edittime DESC', 'edittime ASC', 'logintime DESC', 'logintime ASC', 'logintimes DESC', 'logintimes ASC', 'money DESC', 'money ASC', 'credit DESC', 'credit ASC', 'sms DESC', 'sms ASC', 'message DESC', 'message ASC', 'chat DESC', 'chat ASC', 'fans DESC', 'fans ASC', 'follows DESC', 'follows ASC', 'moments DESC', 'moments ASC', 'groupid DESC', 'groupid ASC', 'gradeid DESC', 'gradeid ASC', 'userid DESC', 'userid ASC');
	$sgender = array('未知', '先生' , '女士');
	$savatar = array('头像', '已上传' , '未上传');
	$sprofile = array('资料', '已完善' , '未完善');
	$semail = array('邮件', '已认证' , '未认证');
	$smobile = array('手机', '已认证' , '未认证');
	$struename = array('实名', '已认证' , '未认证');
	$sbank = array('银行', '已认证' , '未认证');
	$scompany = array('公司', '已认证' , '未认证');
	$sshop = array('商铺', '已认证' , '未认证');
	$senterprise = array('个人', '机构');
	$svalidate = array('未认证', '个人认证', '机构认证');
	$snn = array('truename' => '姓名', 'mobile' => '手机', 'email' => '邮件', 'qq' => 'QQ', 'wx' => '微信', 'sign' => '签名', 'shop' => '商铺', 'inviter' => '邀请人', 'support' => '客服');

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	(isset($datetype) && in_array($datetype, array('regtime', 'logintime', 'edittime'))) or $datetype = 'regtime';
	(isset($mixt) && in_array($mixt, array('money', 'cardit', 'sms', 'deposit','fans','follows','moments','logtimes'))) or $mixt = 'money';
	$minv = isset($minv) ? intval($minv) : '';
	$maxv = isset($maxv) ? intval($maxv) : '';
	$minv or $minv = '';
	$maxv or $maxv = '';
	$gradeid = isset($gradeid) ? intval($gradeid) : 0;
	$groupid = isset($groupid) ? intval($groupid) : 0;
	$gender = isset($gender) ? intval($gender) : -1;
	$avatar = isset($avatar) ? intval($avatar) : 0;
	$vprofile = isset($vprofile) ? intval($vprofile) : 0;
	$vemail = isset($vemail) ? intval($vemail) : 0;
	$vmobile = isset($vmobile) ? intval($vmobile) : 0;
	$vtruename = isset($vtruename) ? intval($vtruename) : 0;
	$vbank = isset($vbank) ? intval($vbank) : 0;
	$vcompany = isset($vcompany) ? intval($vcompany) : 0;
	$vshop = isset($vshop) ? intval($vshop) : 0;
	$validate = isset($validate) ? intval($validate) : -1;
	$enterprise = isset($enterprise) ? intval($enterprise) : -1;
	$uid = isset($uid) ? intval($uid) : '';
	$uid or $uid = '';
	$passport = isset($passport) ? trim($passport) : '';
	(isset($username) && check_name($username)) or $username = '';
	(isset($inviter) && check_name($inviter)) or $inviter = '';
	(isset($support) && check_name($support)) or $support = '';
	(isset($mobile) && is_mobile($mobile)) or $mobile = '';
	isset($nn) && isset($snn[$nn]) or $nn = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order_select  = dselect($sorder, 'order', '', $order);
	$gender_select = dselect($sgender, 'gender', '性别', $gender, '', 1, '-1');
	$avatar_select = dselect($savatar, 'avatar', '', $avatar);
	$group_select = group_select('groupid', '会员组', $groupid);
	$grade_select = grade_select('gradeid', '积分组', $gradeid);
	$vprofile_select = dselect($sprofile, 'vprofile', '', $vprofile);
	$vemail_select = dselect($semail, 'vemail', '', $vemail);
	$vmobile_select = dselect($smobile, 'vmobile', '', $vmobile);
	$vtruename_select = dselect($struename, 'vtruename', '', $vtruename);
	$vbank_select = dselect($sbank, 'vbank', '', $vbank);
	$vcompany_select = dselect($scompany, 'vcompany', '', $vcompany);
	$vshop_select = dselect($sshop, 'vshop', '', $vshop);
	$snn_select = dselect($snn, 'nn', '非空项目', $nn);
	$validate_select = dselect($svalidate, 'validate', '认证', $validate, '', 1, '-1');
	$enterprise_select = dselect($senterprise, 'enterprise', '类型', $enterprise, '', 1, '-1');
	if($action == 'check') {
		$condition = 'groupid=4';
	} else if($action == 'invite') {
		$condition = "inviter<>''";
	} else {		
		$condition = 'groupid!=4';
	}
	if($_areaids) $condition .= " AND areaid IN (".$_areaids.")";//CITY
	if($_self) $condition .= " AND support='$_username'";//SELF
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($minv) $condition .= " AND `$mixt`>=$minv";
	if($maxv) $condition .= " AND `$mixt`<=$maxv";
	if($gender > -1) $condition .= " AND gender=$gender";
	if($avatar) $condition .= $avatar == 1 ? " AND avatar=1" : " AND avatar=0";
	if($gradeid) $condition .= " AND gradeid=$gradeid";
	if($groupid) $condition .= " AND groupid=$groupid";
	if($uid) $condition .= " AND userid=$uid";
	if($username) $condition .= " AND username='$username'";
	if($inviter) $condition .= " AND inviter='$inviter'";
	if($support) $condition .= " AND support='$support'";
	if($passport) $condition .= " AND passport='$passport'";
	if($mobile) $condition .= " AND mobile='$mobile'";
	if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
	if($vprofile) $condition .= $vprofile == 1 ? " AND edittime>0" : " AND edittime=0";
	if($vemail) $condition .= $vemail == 1 ? " AND vemail>0" : " AND vemail=0";
	if($vmobile) $condition .= $vmobile == 1 ? " AND vmobile>0" : " AND vmobile=0";
	if($vtruename) $condition .= $vtruename == 1 ? " AND vtruename>0" : " AND vtruename=0";
	if($vbank) $condition .= $vbank == 1 ? " AND vbank>0" : " AND vbank=0";
	if($vcompany) $condition .= $vcompany == 1 ? " AND vcompany>0" : " AND vcompany=0";
	if($vshop) $condition .= $vshop == 1 ? " AND vshop>0" : " AND vshop=0";
	if($validate > -1) $condition .= " AND validate=$validate";
	if($enterprise > -1) $condition .= " AND enterprise=$enterprise";
	if($nn) $condition .= " AND `$nn`<>''";
}
if(in_array($action, array('add', 'edit', 'misc'))) {
	$COM_TYPE = explode('|', $MOD['com_type']);
	$COM_SIZE = explode('|', $MOD['com_size']);
	$COM_MODE = explode('|', $MOD['com_mode']);
	$MONEY_UNIT = explode('|', $MOD['money_unit']);
	$BANKS = explode('|', trim($MOD['cash_banks']));
	$INVOICE_TYPES = explode('|', $MOD['invoice_types']);
	$ID_TYPES = explode('|', $MOD['id_types']);
}
switch($action) {
	case 'add':
		if($submit) {
			$MOD['checkuser'] = 0;
			$post['groupid'] = $post['regid'];
			if($GROUP[$post['groupid']]['type'] == 0) $post['company'] = $post['truename'];
			$post['passport'] = $post['passport'] ? $post['passport'] : $post['username'];
			$post['edittime'] = $post['edittime'] ? $DT_TIME : 0;
			if($MFD && $post['pass']) fields_check($post_fields, $MFD);
			if($CFD && $post['pass']) fields_check($post_fields, $CFD);
			if(!$post['pass'] || $do->pass($post)) {
				$do->add($post);
				if($MFD) fields_update($post_fields, $do->table_member, $do->userid, 'userid', $MFD);
				if($CFD) fields_update($post_fields, $do->table_company, $do->userid, 'userid', $CFD);
				if($MOD['welcome_sms'] && $DT['sms'] && is_mobile($post['mobile'])) {
					$message = lang('sms->wel_reg', array($post['truename'], $DT['sitename'], $post['username'], $post['password']));
					$message = strip_sms($message);
					send_sms($post['mobile'], $message);
				}
				if($MOD['welcome_message'] || $MOD['welcome_email']) {
					$username = $post['username'];
					$email = $post['email'];
					$title = $L['register_msg_welcome'];
					$content = ob_template('welcome', 'mail');
					if($MOD['welcome_message']) send_message($username, $title, $content);
					if($MOD['welcome_email'] && $DT['mail_type'] != 'close') send_mail($email, $title, $content);
				}
				dmsg('添加成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			include tpl('member_add', $module);
		}
	break;
	case 'edit':
		$userid or msg();
		$do->userid = $userid;
		$user = $do->get_one();
		if(!$_founder && $userid != $_userid && $user['groupid'] == 1) msg('您无权修改其他管理员资料');
		if($submit) {
			if($userid == $_userid && $post['password'] && $post['cpassword']) msg('系统检查到您要修改密码，正在进入密码修改界面...', '?action=password', 3);
			$post['username'] = $user['username'];
			$post['passport'] = $user['passport'];
			$post['edittime'] = $post['edittime'] ? $DT_TIME : 0;
			$post['validtime'] = $post['validtime'] ? datetotime($post['validtime']) : 0;
			if(is_founder($userid)) $post['groupid'] = 1;
			if($MFD && $post['pass']) fields_check($post_fields, $MFD);
			if($CFD && $post['pass']) fields_check($post_fields, $CFD);
			$status = 0;
			if($user['groupid'] != $post['groupid']) {
				$groupid = $post['groupid'];
				if($groupid == 1) {
					$status = 1;
					$post['groupid'] = $user['groupid'];
					$forward = '?file=admin&action=add&username='.$username;
				} else if($GROUP[$groupid]['vip']) {
					$status = 2;
					$post['groupid'] = $user['groupid'];
					$forward = $user['vip'] ? '?moduleid=4&action=edit&userid='.$userid : '?moduleid=4&action=add&username='.$username;
				}
			}			
			$post['enterprise'] = $GROUP[$post['groupid']]['type'] ? 1 : 0;
			if(!$post['pass'] || $do->pass($post)) {
				$do->edit($post);
				if($MFD) fields_update($post_fields, $do->table_member, $do->userid, 'userid', $MFD);
				if($CFD) fields_update($post_fields, $do->table_company, $do->userid, 'userid', $CFD);
				$post['userid'] = $user['userid'];
				$post['validate'] = $user['validate'];
				update_validate($post);
				if($status == 1) msg('会员资料修改成功，如果需要添加管理员，请进入管理员管理...', $forward, 5);
				if($status == 2) msg('会员资料修改成功，如果需要添加'.VIP.'会员，请进入'.VIP.'管理...', $forward, 5);
				dmsg('会员资料修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($user);
			$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $DT_PRE.'company_data');
			$t = $db->get_one("SELECT * FROM {$content_table} WHERE userid=$userid");
			if($t) {
				$content = $t['content'];
			} else {
				$content = '';
				$db->query("REPLACE INTO {$content_table} (userid,content) VALUES ('$userid','')");
			}
			$cates = $catid ? explode(',', substr($catid, 1, -1)) : array();
			$validtime = $validtime ? timetodate($validtime, 3) : '';
			$is_company = $GROUP[$groupid]['type'] || ($groupid == 4 && $GROUP[$regid]['type']);
			include tpl('member_edit', $module);
		}
	break;
	case 'show':
		if(isset($mobile)) {
			$r = $db->get_one("SELECT username FROM {$table} WHERE mobile='$mobile'");
			if($r) $username = $r['username'];
		}
		if(isset($email)) {
			$r = $db->get_one("SELECT username FROM {$table} WHERE email='$email'");
			if($r) $username = $r['username'];
		}
		$username = isset($username) ? $username : '';
		($userid || $username) or msg('会员不存在');
		if($userid) $do->userid = $userid;
		$user = $do->get_one($username);
		$user or msg('会员不存在');
		if(!$_founder && $userid != $_userid && $user['groupid'] == 1) msg('您无权查看其他管理员资料');
		extract($user);
		$svalidate = array('未认证', '个人认证', '机构认证');
		$MG = cache_read('group-'.$groupid.'.php');
		include tpl('member_show', $module);
	break;
	case 'update':
		$userid or dheader('?moduleid='.$moduleid.'&file=html&action=show&update=1');
		$userids = is_array($userid) ? $userid : array($userid);
		foreach($userids as $uid) {
			$do->userid = $uid;
			$user = $do->get_one();
			$do->update($user['username'], $user);
		}
		dmsg('更新成功', $forward);
	break;
	case 'delete':
		$userid or msg('请选择会员');
		if(!$_founder) {
			$userids = is_array($userid) ? $userid : array($userid);
			foreach($userids as $uid) {
				$do->userid = $uid;
				$user = $do->get_one();
				if($user['groupid'] == 1) dalert('您无权删除管理员', '?file=logout');
			}
		}
		$db->halt = 0;
		if($do->delete($userid)) {
			dmsg('删除成功', $forward);
		} else {
			msg($do->errmsg);
		}
	break;
	case 'clean':
		if(check_name($username)) userclean($username);
		$user = userinfo($username);
		if($user) {
			$userid = $user['userid'];
			$dc->remove("SELECT * FROM ".DT_PRE."company_setting WHERE userid=$userid");
			$dc->remove("SELECT * FROM ".DT_PRE."company_home WHERE userid=$userid AND status>0 ORDER BY listorder ASC");
		}
		dmsg('更新成功', $forward);
	break;
	case 'avatar':
		$userid or msg('请选择会员');
		$userids = is_array($userid) ? $userid : array($userid);
		foreach($userids as $uid) {
			$do->userid = $uid;
			$user = $do->get_one();
			$username = $user['username'];
			$userid = $user['userid'];
			$img = array();
			$img[1] = useravatar($userid, 'large', 0, 2);
			$img[2] = useravatar($userid, '', 0, 2);
			$img[3] = useravatar($userid, 'small', 0, 2);
			$img[4] = useravatar($username, 'large', 1, 2);
			$img[5] = useravatar($username, '', 1, 2);
			$img[6] = useravatar($username, 'small', 1, 2);
			foreach($img as $i) {
				file_del($i);
			}
			if($DT['ftp_remote'] && $DT['remote_url']) {
				require DT_ROOT.'/include/ftp.class.php';
				$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
				if($ftp->connected) {
					foreach($img as $i) {
						$t = explode("/file/", $i);
						$ftp->dftp_delete($t[1]);
					}
				}
			}
			$db->query("UPDATE {$DT_PRE}member SET avatar=0 WHERE userid=$userid");
		}
		dmsg('删除成功', $forward);
	break;
	case 'move':
		$userid or msg('请选择会员');
		$gid = isset($groupids) ? $groupids : $groupid;
		if($gid == 1) msg('操作失败！&nbsp;如果需要添加管理员<br/><a href="?file=admin&action=add">请点这里进入管理员管理...</a>');
		if($GROUP[$gid]['vip']) msg('操作失败！&nbsp;如果需要添加'.VIP.'会员<br/><a href="?moduleid=4&action=add">请点这里进入'.VIP.'管理...</a>');
		$do->move($userid, $gid);
		dmsg('移动成功', $forward);
	break;
	case 'edit_username':
		$userid = intval($userid);
		$userid or dalert('未指定会员', 'goback');
		$do->userid = $userid;
		$user = $do->get_one();
		$username = $user['username'];
		if($submit) {
			$cusername = $username;
			check_name($nusername) or dalert('新会员名格式错误', 'goback');
			if($nusername == $cusername) dalert('新会员名与旧会员名不能相同', 'goback');
			if(!$_founder && $cusername != $_username) {
				if($user['groupid'] == 1) dalert('您无权修改其他管理员用户名', 'goback');
			}
			if($do->rename($cusername, $nusername)) {
				$linkurl = userurl($nusername, $user['domain']);
				$db->query("UPDATE {$DT_PRE}company SET linkurl='$linkurl' WHERE userid=$userid");
				userclean($cusername);
				userclean($nusername);
				dmsg('修改成功', '?moduleid='.$moduleid.'&action='.$action.'&userid='.$userid.'&success=1');
			} else {
				dalert($do->errmsg, 'goback');
			}
		} else {
			include tpl('member_edit_username', $module);
		}
	break;
	case 'edit_passport':
		$userid = intval($userid);
		$userid or dalert('未指定会员', 'goback');
		$do->userid = $userid;
		$user = $do->get_one();
		$username = $user['username'];
		$passport = $user['passport'];
		if($submit) {
			$cpassport = $passport;
			$npassport or dalert('会员昵称不能为空', 'goback');
			if($npassport == $cpassport) dalert('新昵称与旧昵称不能相同', 'goback');
			if(!$_founder && $username != $_username) {
				if($user['groupid'] == 1) dalert('您无权修改其他管理员昵称', 'goback');
			}
			if($do->rename_passport($cpassport, $npassport, $username)) {
				dmsg('修改成功', '?moduleid='.$moduleid.'&action='.$action.'&userid='.$userid.'&success=1');
			} else {
				dalert($do->errmsg, 'goback');
			}
		} else {
			include tpl('member_edit_passport', $module);
		}
	break;
	case 'login':
		if($userid) {
			if($_userid == $userid) {
				set_cookie('admin_user', '');
				msg('', $MODULE[2]['linkurl']);
			}
			$do->userid = $userid;
			$user = $do->get_one();
			if(!$_founder) {
				if($user['groupid'] == 1) msg('您无权登入其他管理员会员中心');
				if($_admin > 1 && $user['support'] && $user['support'] != $_username) msg('您无权登入该会员的会员中心');
			}
			$do->update($user['username'], $user);
			$auth = encrypt($userid.'|'.$_username, DT_KEY.'ADMIN');
			set_cookie('admin_user', $auth);
			msg('授权成功，正在转入会员中心...', $MODULE[2]['linkurl'].'?reload='.$DT_TIME);
		} else {
			msg();
		}
	break;
	case 'note_add':
		$userid or msg('请选择会员');
		$note = str_replace(array('|', '-'), array('/', '_'), strip_tags(trim($note)));
		strlen($note) > 3 or msg('请填写备注内容');
		$do->userid = $userid;
		$member = $do->get_one();
		$member or msg('会员不存在');
		if($member['note']) {
			$note = timetodate($DT_TIME, 5)."|".$_username."|".$note."\n--------------------\n".addslashes($member['note']);
		} else {
			$note = timetodate($DT_TIME, 5)."|".$_username."|".$note;
		}
		$db->query("UPDATE {$table}_misc SET note='$note' WHERE userid=$userid");
		dmsg('追加成功', '?moduleid='.$moduleid.'&action=show&userid='.$userid);
	break;
	case 'note_edit':
		$_admin == 1 or msg();
		$userid or msg('请选择会员');
		$do->userid = $userid;
		$member = $do->get_one();
		$member or msg('会员不存在');
		$note = strip_tags($note);
		$db->query("UPDATE {$table}_misc SET note='$note' WHERE userid=$userid");
		dmsg('修改成功', '?moduleid='.$moduleid.'&action=show&userid='.$userid);
	break;
	case 'misc':
		$sfields = array('按条件', '会员名', '证件', '号码', '银行', '支行', '账号', '封面', '自动回复', '注册理由', '备注');
		$dfields = array('username', 'username', 'idtype', 'idno', 'bank', 'branch', 'account', 'cover', 'reply', 'reason', 'note');
		$scover = array('空间封面', '已上传' , '未上传');

		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($idtype) && in_array($idtype, $ID_TYPES)) or $idtype = '';
		(isset($bank) && in_array($bank, $BANKS)) or $bank = '';
		$uid = isset($uid) ? intval($uid) : '';
		$uid or $uid = '';
		(isset($username) && check_name($username)) or $username = '';
		$cover = isset($cover) ? intval($cover) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$idtype_select = dselect($ID_TYPES, 'idtype', '证件类型', $idtype, '', 0);
		$bank_select = dselect($BANKS, 'bank', '银行类型', $bank, '', 0);
		$cover_select = dselect($scover, 'cover', '', $cover);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($uid) $condition .= " AND userid=$uid";
		if($username) $condition .= " AND username='$username'";
		if($idtype) $condition .= " AND idtype='$idtype'";
		if($bank) $condition .= " AND bank='$bank'";
		if($cover) $condition .= $cover == 1 ? " AND cover<>''" : " AND cover=''";

		$lists = $do->get_list_misc($condition);
		include tpl('member_misc', $module);
	break;
	case 'check':
		if($userid) {
			if(is_array($userid)) {
				$userids = $userid;
			} else {
				$userids[0] = $userid;
			}
			foreach($userids as $userid) {
				$do->userid = $userid;
				$post = $do->get_one();
				$groupid = $post['regid'];
				$db->query("UPDATE {$DT_PRE}member SET groupid=$groupid WHERE userid=$userid");
				$db->query("UPDATE {$DT_PRE}company SET groupid=$groupid WHERE userid=$userid");
				if($MOD['welcome_message'] || $MOD['welcome_email']) {
					unset($post['password']);
					$username = $post['username'];
					$email = $post['email'];
					$title = $L['register_msg_welcome'];
					$content = ob_template('welcome', 'mail');
					if($MOD['welcome_message']) send_message($username, $title, $content);
					if($MOD['welcome_email'] && $DT['mail_type'] != 'close') send_mail($email, $title, $content);
				}
			}
			dmsg('审核成功', $forward);
		} else {
			$lists = $do->get_list($condition, $dorder[$order]);
			$lists = $do->join($lists);
			include tpl('member_check', $module);
		}
	break;
	default:
		$lists = $do->get_list($condition, $dorder[$order]);
		$lists = $do->join($lists);
		include tpl('member', $module);
	break;
}
?>