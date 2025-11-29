<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
($MG['biz'] && $MG['stock_limit'] > -1) or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
if($MG['type'] && !$_edittime && $action == 'add') dheader($MODULE[2]['linkurl'].'edit'.DT_EXT.'?tab=2');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('my.lang');
$TYPE = get_type('stock-'.$_userid);
$menu_id = 2;
require DT_ROOT.'/module/'.$module.'/stock.class.php';
$do = new stock();
switch($action) {
	case 'add':
		if($_credit < 0 && $MOD['credit_less']) dheader('credit'.DT_EXT.'?action=less');
		if($MG['stock_limit']) {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stock WHERE username='$_username'");
			if($r['num'] >= $MG['stock_limit']) dalert(lang($L['limit_add'], array($MG['stock_limit'], $r['num'])), '?action=index');
		}
		if($submit) {
			$post['username'] = $_username;
			if($do->pass($post)) {
				$amount = $post['amount'];
				$post['amount'] = 0;
				$post['level'] = $post['addtime'] = 0;
				$do->add($post);
				stock_update($do->itemid, $post['skuid'], $_username, $amount, $_cname ? $_cname : $_username, $L['stock_title_add'], '');
				set_cookie('dmsg', $L['success_add']);
				$forward = '?action=index';
				dalert('', '', 'parent.window.location="'.$forward.'"');
			} else {
				dalert($do->errmsg);
			}
		} else {
			foreach($do->fields as $v) {
				$$v = '';
			}
			$content = '';
			$typeid = 0;
			if($itemid) {
				$do->itemid = $itemid;
				$r = $do->get_one();
				if($r) {
					if(!$r['username'] || $r['username'] == $_username) {
						$skuid = $r['skuid'];
						$title = $r['title'];
						$thumb = $r['thumb'];
						$price = $r['price'];
						$cost = $r['cost'];
						$unit = $r['unit'];
						$brand = $r['brand'];
						$n1 = $r['n1'];
						$n2 = $r['n2'];
						$n3 = $r['n3'];
						$v1 = $r['v1'];
						$v2 = $r['v2'];
						$v3 = $r['v3'];
						$content = $r['content'];
						if($r['username']) {
							$typeid = $r['typeid'];							
							$location = $r['location'];
						} else {
							$db->query("UPDATE {$DT_PRE}stock SET amount=amount+1 WHERE itemid=$itemid");
						}
					} else {
						message();
					}
				}
			}
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type']);
			$head_title = $L['stock_title_add'];
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$r = $do->get_one();
		if(!$r || $r['username'] != $_username) message();
		if($submit) {
			$post['username'] = $_username;
			$amount = $post['amount'];
			$post['amount'] = $r['amount'];
			if($do->pass($post)) {
				$post['level'] = $r['level'];
				$post['addtime'] = timetodate($r['addtime']);
				$do->edit($post);
				if($r['amount'] != $amount) stock_update($do->itemid, $post['skuid'], $_username, $amount - $r['amount'], $_cname ? $_cname : $_username, $L['stock_title_edit'], '');
				set_cookie('dmsg', $L['success_edit']);
				dalert('', '', 'parent.window.location="'.$forward.'"');
			} else {
				dalert($do->errmsg);
			}
		} else {
			extract($r);
			$addtime = timetodate($addtime);
			$type_select = type_select($TYPE, 0, 'post[typeid]', $L['default_type'], $typeid);
			$head_title = $L['stock_title_edit'];
		}
	break;
	case 'delete':
		$itemid or message($L['stock_msg_choose']);
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			if($item && $item['username'] == $_username) $do->delete($itemid);
		}
		dmsg($L['op_del_success'], $forward);
	break;
	case 'update':
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		$type = isset($type) && $type ? 1 : 0;
		$title = '';
		if($itemid) {
			$do->itemid = $itemid;
			$r = $do->get_one();
			if(!$r || $r['username'] != $_username) message();
			$skuid = $r['skuid'];
			$title = $r['title'];
		} else if($skuid) {
			$r = $do->get_one("skuid='$skuid' AND username='$_username'");
			if(!$r) message($L['stock_msg_barcode'] );
			$title = $r['title'];
		}
		if($submit) {
			$amount = intval($amount);
			if($amount < 1) message($L['stock_msg_amount']);
			if(!is_skuid($skuid)) message($L['stock_msg_skuid']);
			$forward = $itemid ? '?action=record' : '?action='.$action.'&type='.$type.'&amount='.$amount.'&reason='.urlencode($reason).'&note='.urlencode($note);
			$reason = dhtmlspecialchars(trim($reason));
			$note = dhtmlspecialchars(trim($note));
			$itemid = $r['itemid'];
			$username = $r['username'];
			$editor = $_cname ? $_cname : $_username;
			if($type) $amount = -$amount;
			stock_update($itemid, $skuid, $username, $amount, $editor, $reason, $note);
			dmsg($L['op_success'], $forward);
		} else {
			$reason = isset($reason) ? strip_tags($reason) : '';
			$note = isset($note) ? strip_tags($note) : '';
			$amount = isset($amount) ? intval($amount) : 1;
			$head_title = $type ? $L['stock_title_out'] : $L['stock_title_in'];
		}
	break;
	case 'record':
		$sfields = $L['stock_record_sfields'];
		$dfields = array('title', 'title', 'skuid', 'reason', 'note', 'editor');
		$sorder  = $L['stock_record_sorder'];
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'balance DESC', 'balance ASC', 'addtime DESC', 'addtime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($type) or $type = 0;
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($order) && isset($dorder[$order]) or $order = 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);

		$condition = "username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($type) $condition .= $type == 1 ? " AND amount>0" : " AND amount<0";
		if($itemid) $condition .= " AND stockid=$itemid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($editor) $condition .= " AND editor='$editor'";

		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stock_record WHERE {$condition}");
		$items = $r['num'];
		$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}stock_record WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		$income = $expense = 0;
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$r['amount'] > 0 ? $income += $r['amount'] : $expense += $r['amount'];
			$lists[] = $r;
		}
		$head_title = $L['stock_title_record'];	
	break;
	case 'open':
		$sfields = $L['stock_open_sfields'];
		$dfields = array('title', 'title', 'skuid', 'location', 'brand', 'unit', 'n1', 'n2', 'n3', 'v1', 'v2', 'v3', 'note', 'editor');
		$sorder  = $L['stock_open_sorder'];
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'price DESC', 'price ASC', 'cost DESC', 'cost ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);

		$condition = "username=''";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($editor) $condition .= " AND editor='$editor'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";

		$lists = $do->get_list($condition, $dorder[$order]);
		foreach($lists as $k=>$v) {
			$lists[$k]['type'] = $lists[$k]['typeid'] && isset($TYPE[$lists[$k]['typeid']]) ? set_style($TYPE[$lists[$k]['typeid']]['typename'], $TYPE[$lists[$k]['typeid']]['style']) : $L['default_type'];
		}
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stock WHERE username='$_username'");
		$limit_used = $r['num'];
		$limit_free = $MG['stock_limit'] && $MG['stock_limit'] > $limit_used ? $MG['stock_limit'] - $limit_used : 0;
		$head_title = $L['stock_title_open'];
	break;
	default:
		$sfields = $L['stock_sfields'];
		$dfields = array('title', 'title', 'skuid', 'location', 'brand', 'unit', 'n1', 'n2', 'n3', 'v1', 'v2', 'v3', 'note', 'editor');
		$sorder  = $L['stock_sorder'];
		$dorder  = array('itemid DESC', 'amount DESC', 'amount ASC', 'price DESC', 'price ASC', 'cost DESC', 'cost ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
		(isset($editor) && check_name($editor)) or $editor = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($order) && isset($dorder[$order]) or $order = 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);

		$condition = "username='$_username'";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($editor) $condition .= " AND editor='$editor'";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";

		$lists = $do->get_list($condition, $dorder[$order]);
		foreach($lists as $k=>$v) {
			$lists[$k]['type'] = $lists[$k]['typeid'] && isset($TYPE[$lists[$k]['typeid']]) ? set_style($TYPE[$lists[$k]['typeid']]['typename'], $TYPE[$lists[$k]['typeid']]['style']) : $L['default_type'];
		}
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}stock WHERE username='$_username'");
		$limit_used = $r['num'];
		$limit_free = $MG['stock_limit'] && $MG['stock_limit'] > $limit_used ? $MG['stock_limit'] - $limit_used : 0;
		$head_title = $L['stock_title'];
	break;
}
if($DT_PC) {	
	$menu_id = 2;
} else {
	$wx_jssdk = array();
	if(in_array($action, array('add', 'edit', 'update')) && in_array($DT_MBS, array('weixin', 'wxmini'))) {
		$WX = cache_read('weixin.php');
		if($WX['appid'] && $WX['appsecret']) {
			require DT_ROOT.'/api/weixin/jssdk.php';
			$jssdk = new JSSDK($WX['appid'], $WX['appsecret']);
			$wx_jssdk = $jssdk->GetSignPackage();
		}	
	}
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	if($pages) $pages = mobile_pages($items, $page, $pagesize);
	$head_name = $head_title;
}
include template('stock', $module);
?>