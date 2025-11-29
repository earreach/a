<?php
defined('DT_ADMIN') or exit('Access Denied');
$do = new grade();
$menus = array (
    array('升级记录', '?moduleid='.$moduleid.'&file='.$file),
    array('审核申请', '?moduleid='.$moduleid.'&file='.$file.'&action=check'),
    array('拒绝记录', '?moduleid='.$moduleid.'&file='.$file.'&action=reject'),
);
$dstatus = $L['account_upgrade_status'];
if(in_array($action, array('', 'check', 'reject'))) {
	$sfields = array('按条件', '公司名', '会员名', '操作人', '操作原因', '备注');
	$dfields = array('company', 'company', 'username', 'editor', 'reason', 'note');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$sorder  = array('结果排序方式', '申请时间降序', '申请时间升序', '受理时间降序', '受理时间升序', '支付费用降序', '支付费用升序', '申请状态降序', '申请状态升序', '原会员组ID降序', '原会员组ID升序', '新会员组ID降序', '新会员组ID升序');
	$dorder  = array('addtime DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'amount DESC', 'amount ASC', 'status DESC', 'status ASC', 'gid DESC', 'gid ASC', 'groupid DESC', 'groupid ASC');
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
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
	$status = isset($status) ? intval($status) : 0;
	if($action == 'check') $status = 2;
	if($action == 'reject') $status = 1;

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order_select  = dselect($sorder, 'order', '', $order);
	$status_select  = dselect($dstatus, 'status', '', $status);
	$fgroup_select = group_select('gid', '原会员组', $gid);
	$tgroup_select = group_select('groupid', '新会员组', $groupid);

	$condition = '1';
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($minamount) $condition .= " AND amount>=$minamount";
	if($maxamount) $condition .= " AND amount<=$maxamount";
	if($gid) $condition .= " AND gid=$gid";
	if($groupid) $condition .= " AND groupid=$groupid";
	if($editor) $condition .= " AND editor='$editor'";
	if($username) $condition .= " AND username='$username'";
	if($status) $condition .= " AND status=$status";
}
$menuon = array('4', '2', '1', '0');
switch($action) {
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->edit($post)) {
				dmsg('操作成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$user = $username ? userinfo($username) : array();
			$addtime = timetodate($addtime);
			$edittime = timetodate($edittime);
			$fromtime = timetodate($DT_TIME, 3);
			$days = 364;
			$totime = timetodate($DT_TIME + 86400*$days, 3);
			$UG = cache_read('group-'.$groupid.'.php');
			$fee = $UG['fee'];
			$pay = $fee - $amount;
			$menuid = $menuon[$status];
			include tpl('grade_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择记录');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	case 'reject':
		$menuid = 2;
		$lists = $do->get_list($status.$condition, $dorder[$order]);
		include tpl('grade', $module);
	break;
	case 'check':
		$menuid = 1;
		$lists = $do->get_list($status.$condition, $dorder[$order]);
		include tpl('grade', $module);
	break;
	default:
		$menuid = 0;
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('grade', $module);
	break;
}

class grade {
	var $itemid;
	var $table;
	var $errmsg = errmsg;

    function __construct() {
		$this->table = DT_PRE.'member_upgrade';
    }

    function grade() {
		$this->__construct();
    }

	function get_one($condition = '') {
        return DB::get_one("SELECT * FROM {$this->table} WHERE itemid='$this->itemid' $condition");
	}

	function get_list($condition = 'status=3', $order = 'addtime DESC') {
		global $MOD, $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = $r['edittime'] ? timetodate($r['edittime'], 5) : 'N/A';
			$r['linkurl'] = userurl($r['username']);
			$lists[] = $r;
		}
		return $lists;
	}

	function edit($post) {
		global $DT, $GROUP, $L, $_username;
		$item = $this->get_one();
		$user = $item['username'] ? userinfo($item['username']) : array();		
		$msg = isset($post['msg']) ? 1 : 0;
		$eml = isset($post['eml']) ? 1 : 0;
		$sms = isset($post['sms']) ? 1 : 0;
		$wec = isset($post['wec']) ? 1 : 0;
		$message = ($msg || $eml || $sms || $wec) ? 1 : 0;
		$post['status'] = intval($post['status']);
		$post['reason'] = strip_tags($post['reason']);
		$post['note'] = strip_tags($post['note']);
		$gsql = $msql = $csql = '';
		$gsql = "edittime=".DT_TIME.",editor='$_username',status=$post[status],message='$message',reason='$post[reason]',note='$post[note]'";
		if($post['status'] == 1) {
			//reject
			if($user) {
				if($item['amount'] > 0) {
					money_add($user['username'], $item['amount']);
					money_record($user['username'], $item['amount'], $L['in_site'], 'system', $L['grade_title'], $L['grade_return']);
				}
				$subject = '您的'.$GROUP[$item['groupid']]['groupname'].'升级审核未通过';
				$body = '尊敬的会员：<br/>您的'.$GROUP[$item['groupid']]['groupname'].'升级审核未通过！<br/>';
				if($post['reason']) $body .= '操作原因：<br/>'.$post['reason'].'<br/>';
				$body .= '如果您对此操作有异议，请及时与网站联系。';
				if($msg) send_message($user['username'], $subject, $body);
				if($wec) send_weixin($user['username'], $subject);
				if($eml) send_mail($user['email'], $subject, $body);
				if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
			}
		} else if($post['status'] == 2) {
			//
		} else if($post['status'] == 3) {
			if($user) {
				if($GROUP[$item['groupid']]['type']) {
					$t = DB::get_one("SELECT userid FROM ".DT_PRE."company WHERE company='$post[company]' AND userid<>$user[userid]");
					if($t) msg('公司名称已存在');
				}
				$msql = $csql = "groupid=$item[groupid],company='$post[company]'";
				$gsql .= ",company='$post[company]'";
				$vip = $GROUP[$item['groupid']]['vip'];
				$enterprise = $GROUP[$item['groupid']]['type'] ? 1 : 0;
				$csql .= ",vip=$vip,vipt=$vip";
				$validate = $post['validated'] ? ($GROUP[$item['groupid']]['type'] ? 2 : 1) : 0;
				$msql .= ",validate=$validate,enterprise=$enterprise";
				if(isset($post['fromtime'])) {
					$validtime = datetotime($post['validtime']);
					$fromtime = datetotime($post['fromtime']);
					$totime = datetotime($post['totime'].' 23:59:59');
					$csql .= ",fromtime=$fromtime,totime=$totime,validtime=$validtime,validator='$post[validator]',validated=$post[validated]";
					DB::query("INSERT INTO ".DT_PRE."company_vip (username,company,amount,gid,groupid,fromtime,totime,addtime,reason,note,editor) VALUES ('$user[username]','$post[company]','$item[amount]','$item[gid]','$item[groupid]','$fromtime','$totime','".DT_TIME."', '升级','$post[note]','$_username')");
				}
				$subject = '您的'.$GROUP[$item['groupid']]['groupname'].'升级审核已通过';
				$body = '尊敬的会员：<br/>您的'.$GROUP[$item['groupid']]['groupname'].'升级审核已通过！<br/>';
				if($post['reason']) $body .= '操作原因：<br/>'.$post['reason'].'<br/>';
				$body .= '感谢您的支持！';
				if($msg) send_message($user['username'], $subject, $body);
				if($wec) send_weixin($user['username'], $subject);
				if($eml) send_mail($user['email'], $subject, $body);
				if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
			}
		}
		DB::query("UPDATE {$this->table} SET $gsql WHERE itemid=$this->itemid");
		if($msql) DB::query("UPDATE ".DT_PRE."member SET $msql WHERE userid=$item[userid]");
		if($csql) DB::query("UPDATE ".DT_PRE."company SET $csql WHERE userid=$item[userid]");
		if($msql || $csql) userclean($user['username']);
		return true;
	}

	function delete($itemid, $all = true) {
		if(is_array($itemid)) {
			foreach($itemid as $v) { $this->delete($v); }
		} else {
			DB::query("DELETE FROM {$this->table} WHERE itemid=$itemid");
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>