<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
$do = new $module();
$menus = array (
    array('开通'.VIP, '?moduleid='.$moduleid.'&action=add'),
    array(VIP.'列表', '?moduleid='.$moduleid.'&action=vip'),
    array(VIP.'记录', '?moduleid='.$moduleid.'&action=record'),
    array($MOD['name'].'列表', '?moduleid='.$moduleid),
    array('移动地区', '?moduleid='.$moduleid.'&action=move'),
);

if($_catids || $_areaids) {
	if(isset($userid)) $itemid = $userid;
	if(isset($member['areaid'])) $post['areaid'] = $member['areaid'];
	require DT_ROOT.'/module/destoon/admin/check.inc.php';
}
$GROUP = cache_read('group.php');
switch($action) {
	case 'add':	
		if($submit) {
			$post['username'] = trim($post['username']);
			if(!$post['username']) msg('会员名不能为空');
			$amount = dround($amount);
			$pay = $pay ? 1 : 0;
			if($amount < 0) msg('应付金额不能小于0');
			$post['fromtime'] = timetodate($DT_TIME, 3);

			$groupid = $post['groupid'];
			$fromtime = datetotime($post['fromtime'].' 00:00:00');
			$totime = datetotime($post['totime'].' 23:59:59');
			$reason = '开通';
			$note = htmlspecialchars(trim($note));
			if($amount && $pay) $note = $note.'站内扣款';

			$usernames = explode("\n", trim($post['username']));
			foreach($usernames as $username) {
				$username = trim($username);
				if(!check_name($username)) continue;
				$user = $do->get_one($username);
				if(!$user || $user['vip']) continue;
				if($amount && $pay && $user['money'] < $amount) msg('会员'.$username.'余额不足');
				$post['username'] = $user['username'];
				$post['validated'] = $user['validated'];
				$post['validator'] = $user['validator'];
				$post['validtime'] = $user['validtime'] ? timetodate($user['validtime'], 3) : 0;
				$do->vip_edit($post, $user);
				if($amount && $pay) {
					money_add($username, -$amount);
					money_record($username, -$amount, '站内', $_username, VIP.'升级', $GROUP[$post['groupid']]['groupname']);
				} else {
					$amount = 0;
				}
				$gid = $user['groupid'];
				$company = addslashes($user['company']);
				$db->query("INSERT INTO {$DT_PRE}company_vip (username,company,amount,gid,groupid,fromtime,totime,addtime,reason,note,editor) VALUES ('$username','$company','$amount','$gid','$groupid','$fromtime','$totime','$DT_TIME', '$reason','$note','$_username')");
			}
			dmsg('开通成功', '?moduleid='.$moduleid.'&action=vip');
		} else {
			isset($username) or $username = '';
			if(isset($userid)) {
				if($userid) {
					$userids = is_array($userid) ? implode(',', $userid) : $userid;					
					$result = $db->query("SELECT username FROM {$DT_PRE}member WHERE userid IN ($userids)");
					while($r = $db->fetch_array($result)) {
						$username .= $r['username']."\n";
					}
				}
			}
			$fromdate = timetodate($DT_TIME, 3);
			$diff = 0;
			$todate = timetodate(datetotime('+12 month') + $diff, 3);
			include load('member.lang');
			include tpl('vip_add', $module);
		}
	break;
	case 'edit':
		$userid or msg();
		$do->userid = $userid;
		$user = $do->get_one();
		if($submit) {
			$post['username'] = $user['username'];
			if($do->vip_edit($post, $user)) {
				$fromtime = datetotime($post['fromtime'].' 00:00:00');
				$totime = datetotime($post['totime'].' 23:59:59');
				if($user['groupid'] != $post['groupid'] || $user['fromtime'] != $fromtime || $user['totime'] != $totime) {
					$note = htmlspecialchars(trim($note));
					$company = addslashes($user['company']);
					$db->query("INSERT INTO {$DT_PRE}company_vip (username,company,amount,gid,groupid,fromtime,totime,addtime,reason,note,editor) VALUES ('$user[username]','$company','0','$user[groupid]','$post[groupid]','$fromtime','$totime','$DT_TIME', '修改','$note','$_username')");
				}
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($user);
			$fromtime = $fromtime ? timetodate($fromtime, 3) : '';
			$totime = $totime ? timetodate($totime, 3) : '';
			$validtime = $validtime ? timetodate($validtime, 3) : '';
			include tpl('vip_edit', $module);
		}
	break;
	case 'renew':
		$userid or msg();
		$do->userid = $userid;
		$user = $do->get_one();
		$FM_TIME = $user['totime'] > $DT_TIME ? $user['totime'] : $DT_TIME;
		if($submit) {
			$amount = dround($amount);
			$pay = $pay ? 1 : 0;
			if($amount < 0) msg('应付金额不能小于0');		
			$username = $user['username'];
			$groupid = $post['groupid'];
			$fromtime = datetotime(timetodate($FM_TIME, 3).' 00:00:00');
			$totime = datetotime($post['totime'].' 23:59:59');
			$reason = '续费';
			$note = htmlspecialchars(trim($note));
			if($amount && $pay) $note = $note.'站内扣款';

			if($amount && $pay && $user['money'] < $amount) msg('会员'.$username.'余额不足');
			$post['username'] = $user['username'];
			$post['validated'] = $user['validated'];
			$post['validator'] = $user['validator'];
			$post['validtime'] = $user['validtime'] ? timetodate($user['validtime'], 3) : 0;
			$post['fromtime'] = timetodate($user['fromtime'], 3);	

			if($do->vip_edit($post, $user)) {
				if($amount && $pay) {
					money_add($username, -$amount);
					money_record($username, -$amount, '站内', $_username, VIP.'续费', $GROUP[$post['groupid']]['groupname']);
				}
				$gid = $user['groupid'];
				$company = addslashes($user['company']);
				$db->query("INSERT INTO {$DT_PRE}company_vip (username,company,amount,gid,groupid,fromtime,totime,addtime,reason,note,editor) VALUES ('$username','$company','$amount','$gid','$groupid','$fromtime','$totime','$DT_TIME', '$reason','$note','$_username')");
				dmsg('续费成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($user);
			$fromdate = timetodate($FM_TIME, 3);
			$diff = $user['totime'] > $DT_TIME ? $user['totime'] - $DT_TIME : 0;
			$todate = timetodate(datetotime('+12 month') + $diff, 3);
			include load('member.lang');
			include tpl('vip_renew', $module);
		}
	break;
	case 'delete':
		$userid or msg('请选择公司');
		$do->vip_delete($userid);
		dmsg('撤销成功', $forward);
	break;
	case 'update':
		is_array($userid) or dheader('?moduleid='.$moduleid.'&file=html&action=show&update=1');
		$userids = is_array($userid) ? $userid : array($userid);
		foreach($userids as $uid) {
			$do->update($uid);
		}
		dmsg('更新成功', $forward);
	break;
	case 'move':
		if($submit) {
			$fromids or msg('请填写来源ID');
			if($toareaid) {
				$db->query("UPDATE {$table} SET areaid=$toareaid WHERE `{$fromtype}` IN ($fromids)");
				$db->query("UPDATE {$DT_PRE}member SET areaid=$toareaid WHERE `{$fromtype}` IN ($fromids)");
			}
			dmsg('移动成功', $forward);
		} else {
			$userid = isset($userid) ? implode(',', $userid) : '';
			$menuid = 4;
			include tpl($action, $module);
		}
	break;
	case 'level':
		$userid or msg('请选择'.$MOD['name']);
		$level = intval($level);
		$do->level($userid, $level);
		dmsg('级别设置成功', $forward);
	break;
	case 'record':
		$sfields = array('按条件', '公司名', '会员名', '操作事由', '备注信息', '操作人');
		$dfields = array('company', 'company', 'username', 'reason', 'note', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('排序方式', '发生时间降序', '发生时间升序', '开始时间降序', '开始时间升序', '到期时间降序', '到期时间升序', '支付费用降序', '支付费用升序', '原会员组ID降序', '原会员组ID升序', '新会员组ID降序', '新会员组ID升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC', 'amount DESC', 'amount ASC', 'gid DESC', 'gid ASC', 'groupid DESC', 'groupid ASC');

		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'fromtime', 'totime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		(isset($username) && check_name($username)) or $username = '';
		(isset($editor) && check_name($editor)) or $editor = '';		
		$gid = isset($gid) ? intval($gid) : 0;
		$groupid = isset($groupid) ? intval($groupid) : 0;
		$minamount = isset($minamount) ? dround($minamount) : '';
		$minamount or $minamount = '';
		$maxamount = isset($maxamount) ? dround($maxamount) : '';
		$maxamount or $maxamount = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$fgroup_select = group_select('gid', '原会员组', $gid);
		$tgroup_select = group_select('groupid', '新会员组', $groupid);

		$condition = "1";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($minamount) $condition .= " AND amount>=$minamount";
		if($maxamount) $condition .= " AND amount<=$maxamount";
		if($gid) $condition .= " AND gid=$gid";
		if($groupid) $condition .= " AND groupid=$groupid";
		if($editor) $condition .= " AND editor='$editor'";
		if($username) $condition .= " AND username='$username'";

		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}company_vip WHERE {$condition}");
		$items = $r['num'];
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}company_vip WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['fromdate'] = timetodate($r['fromtime'], 3);
			$r['todate'] = timetodate($r['totime'], 3);
			$r['linkurl'] = userurl($r['username']);
			$lists[] = $r;
		}
		$menuid = 2;
		include tpl('vip_record', $module);
	break;
	case 'vip':
		$sfields = array('按条件', '公司名', '会员名');
		$dfields = array('keyword', 'company', 'username');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '开通时间降序', '开通时间升序', '到期时间降序', '到期时间升序', VIP.'指数降序', VIP.'指数升序', '理论值降序', '理论值升序', '修正值降序', '修正值升序', '会员组ID降序', '会员组ID升序', '会员ID降序', '会员ID升序');
		$dorder  = array('fromtime DESC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC', 'vip DESC', 'vip ASC', 'vipt DESC', 'vipt ASC', 'vipr DESC', 'vipr ASC', 'groupid DESC', 'groupid ASC', 'userid DESC', 'userid ASC');	
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($datetype) && in_array($datetype, array('fromtime', 'totime')) or $datetype = 'fromtime';
		$vipt = isset($vipt) ? intval($vipt) : 0;
		$vipt or $vipt = '';
		$vipr = isset($vipr) ? intval($vipr) : 0;
		$vipr or $vipr = '';
		$vip = isset($vip) ? intval($vip) : 0;
		$groupid = isset($groupid) ? intval($groupid) : 0;
	
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$group_select = group_select('groupid', '会员组', $groupid);
		
		$condition = $vip ? "vip=$vip" : "vip>0";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($groupid) $condition .= " AND groupid=$groupid";
		if($fromtime) $condition .= " AND $datetype>=$fromtime";
		if($totime) $condition .= " AND $datetype<=$totime";
		if($vipt != '') $condition .= " AND vipt=".intval($vipt);
		if($vipr != '') $condition .= " AND vipr=".intval($vipr);
		$companys = $do->get_list($condition, $dorder[$order]);
		include tpl('vip', $module);
	break;
	default:
		$sfields = array('按条件', '公司名', '会员名', '税号','公司类型', '认证机构', '开票类型', '公司规模', '销售', '采购', '主营行业', '经营模式', '电话', '传真',  'Email', '微信公众号', '地址', '经度', '纬度',  '邮编', '主页', '模板', '风格', '绑定域名', '备案号', '网安号', '简介');
		$dfields = array('keyword', 'company', 'username', 'taxid', 'type', 'validator', 'invoice', 'size', 'sell', 'buy', 'business', 'mode', 'telephone', 'fax', 'mail', 'gzh', 'address', 'lng', 'lat', 'postcode', 'homepage', 'template', 'skin', 'domain', 'icp', 'wan', 'introduce');
		$sorder  = array('结果排序方式', '服务开始降序', '服务开始升序', '服务结束降序', '服务结束升序', '推荐级别降序', '推荐级别升序', '浏览人气降序','浏览人气升序', '点赞次数降序', '点赞次数升序', '反对次数降序', '反对次数升序', '收藏次数降序', '收藏次数升序', '打赏次数降序', '打赏次数升序', '打赏金额降序', '打赏金额升序', '分享次数降序', '分享次数升序', '举报次数降序', '举报次数升序', '评论数量降序', '评论数量升序', VIP.'指数降序', VIP.'指数升序', '注册年份降序', '注册年份升序', '注册资本降序', '注册资本升序', '会员组ID降序', '会员组ID升序', '会员ID降序', '会员ID升序');
		$dorder  = array('userid DESC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC', 'level DESC', 'level ASC', 'hits DESC', 'hits ASC', 'likes DESC', 'likes ASC', 'hates DESC', 'hates ASC', 'favorites DESC', 'favorites ASC', 'awards DESC', 'awards ASC', 'award DESC', 'award ASC', 'shares DESC', 'shares ASC', 'reports DESC', 'reports ASC', 'comments DESC', 'comments ASC','vip DESC', 'vip ASC', 'regyear DESC', 'regyear ASC', 'capital DESC', 'capital ASC', 'groupid DESC', 'groupid ASC', 'userid DESC', 'userid ASC');		
		$snn = array('taxid' => '税号', 'invoice' => '开票', 'validator' => '认证名称', 'regcity' => '注册地', 'telephone' => '电话', 'fax' => '传真', 'mail' => '邮箱', 'gzh' => '公众号', 'address' => '注册地址', 'lng' => '坐标', 'homepage' => '官网', 'domain' => '绑定域名', 'icp' => 'ICP备案号', 'wan' => '网安备案');

		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('totime', 'fromtime', 'styletime', 'validtime')) or $datetype = 'totime';
		(isset($mixt) && in_array($mixt, array('regyear', 'capital', 'hits', 'comments'))) or $mixt = 'regyear';
		$minv = isset($minv) ? intval($minv) : '';
		$maxv = isset($maxv) ? intval($maxv) : '';
		$minv or $minv = '';
		$maxv or $maxv = '';
		$svalid = array('认证', '已通过' , '未通过');
		$MS = cache_read('module-2.php');
		$modes = explode('|', '经营模式|'.$MS['com_mode']);
		$types = explode('|', '公司类型|'.$MS['com_type']);
		$sizes = explode('|', '公司规模|'.$MS['com_size']);	
		$thumb = isset($thumb) ? intval($thumb) : 0;
		$domain = isset($domain) ? intval($domain) : 0;
		$areaid = isset($areaid) ? intval($areaid) : 0;
		isset($mode) && isset($modes[$mode]) or $mode = 0;
		isset($type) && isset($types[$type]) or $type = 0;
		isset($size) && isset($sizes[$size]) or $size = 0;
		$vip = isset($vip) ? ($vip === '' ? -1 : intval($vip)) : -1;
		$agent = isset($agent) ? intval($agent) : -1;
		$bill = isset($bill) ? intval($bill) : -1;
		$groupid = isset($groupid) ? intval($groupid) : 0;
		$valid = isset($valid) ? intval($valid) : 0;
		$level = isset($level) ? intval($level) : 0;
		$uid = isset($uid) ? intval($uid) : '';
		$uid or $uid = '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($nn) && isset($snn[$nn]) or $nn = '';

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$level_select = level_select('level', '级别', $level, 'all');
		$order_select  = dselect($sorder, 'order', '', $order);
		$valid_select = dselect($svalid, 'valid', '', $valid);
		$group_select = group_select('groupid', '会员组', $groupid);
		$mode_select = dselect($modes, 'mode', '', $mode);
		$type_select = dselect($types, 'type', '', $type);
		$size_select = dselect($sizes, 'size', '', $size);
		$snn_select = dselect($snn, 'nn', '非空项目', $nn);

		$condition = 'groupid IN ('.get_gids().')';
		if($action == 'domain') $condition .= " AND domain<>''";
		if($_catids) {//CATE
			$tmp = '';
			foreach(explode(',', $_catids) as $cid) {
				$cid = intval($cid);
				if($cid > 0) $tmp .= " OR catids LIKE '%,".$cid.",%'";
			}
			if($tmp) {
				if(substr_count($tmp, 'OR') > 1) {
					$condition .= " AND (".substr($tmp, 4).")";
				} else {
					$condition .= str_replace(' OR ', ' AND ', $tmp);
				}
			}
		}
		if($_areaids) $condition .= " AND areaid IN (".$_areaids.")";//CITY
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($groupid) $condition .= " AND groupid=$groupid";
		if($vip > -1) $condition .= " AND vip=$vip";
		if($agent > -1) $condition .= " AND agent=$agent";
		if($bill > -1) $condition .= " AND bill=$bill";
		if($level) $condition .= $level > 9 ? " AND level>0" : " AND level=$level";
		if($valid) $condition .= $valid == 1 ? " AND validated=1" : " AND validated=0";
		if($catid) $condition .= " AND catids LIKE '%,".$catid.",%'";
		if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		if($mode) $condition .= match_kw('mode', $modes[$mode]);
		if($type) $condition .= " AND type='$types[$type]'";
		if($size) $condition .= " AND size='$sizes[$size]'";
		if($thumb)  $condition .= " AND thumb<>''";
		if($domain)  $condition .= " AND domain<>''";
		if($uid) $condition .= " AND userid=$uid";
		if($username) $condition .= " AND username='$username'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($minv) $condition .= " AND `$mixt`>=$minv";
		if($maxv) $condition .= " AND `$mixt`<=$maxv";
		if($nn) $condition .= $nn == 'lng' ? " AND `$nn`>0" : " AND `$nn`<>''";
		$lists = $do->get_list($condition, $dorder[$order]);
		$menuid = 3;
		include tpl('index', $module);
	break;
}
?>