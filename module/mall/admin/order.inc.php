<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('订单管理', '?moduleid='.$moduleid.'&file='.$file),
    array('评价管理', '?moduleid='.$moduleid.'&file='.$file.'&action=comment'),
    array('快递管理', '?moduleid='.$moduleid.'&file='.$file.'&action=express'),
    array('发票管理', '?moduleid='.$moduleid.'&file='.$file.'&action=invoice'),
    array('合同管理', '?moduleid='.$moduleid.'&file='.$file.'&action=contract'),
    array('售后服务', '?moduleid='.$moduleid.'&file='.$file.'&action=service'),
    array('统计报表', '?moduleid='.$moduleid.'&file='.$file.'&action=stats'),
);
if($MODULE[$moduleid]['module'] != 'mall') unset($menus[1]);
include load('member.lang');
include load('order.lang');
$dstatus = $L['trade_status'];
$dsend_status = $L['send_status'];
$dservice_status = $L['service_status'];
$dservice = $L['service_type'];
$STARS = $L['star_type'];
$table = $DT_PRE.'order';
$table_log = $DT_PRE.'order_log';
$table_service = $DT_PRE.'order_service';
if($action == 'refund' || $action == 'show' || $action == 'comment_edit' || $action == 'note_add' || $action == 'note_edit') {
	$itemid or msg('未指定记录');
	$O = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	$O or msg('记录不存在');
	$O['mid'] == $moduleid or msg('记录不存在');
	$O['linkurl'] = gourl('?mid='.$O['mid'].'&itemid='.$O['mallid']);
	$O['total'] = $O['amount'] + $O['fee'];
	$O['total'] = number_format($O['total'], 2, '.', '');
	$O['money'] = $O['amount'] + $O['discount'];
	$O['money'] = number_format($O['money'], 2, '.', '');
	$O['adddate'] = timetodate($O['addtime'], 6);
	$O['updatedate'] = timetodate($O['updatetime'], 6);
	$O['par'] = '';
	if(strpos($O['note'], '|') !== false) list($O['note'], $O['par']) = explode('|', $O['note']);
	$lists = array($O);
	if(($O['amount'] + $O['discount']) > $O['price']*$O['number']) {
		$result = $db->query("SELECT * FROM {$table} WHERE pid=$itemid ORDER BY itemid DESC");
		while($r = $db->fetch_array($result)) {
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
			$lists[] = $r;
		}
	}
	$O['sku'] = $O['skuid'] ? get_sku($O['skuid'], $O['seller']) : array();
	$mid = $O['mid'];
	$mallid = $O['mallid'];
	$logs = array();
	$result = $db->query("SELECT * FROM {$table_log} WHERE oid=$itemid ORDER BY itemid DESC");
	while($r = $db->fetch_array($result)) {
		$r['adddate'] = timetodate($r['addtime'], 5);
		$logs[] = $r;
	}
}
switch($action) {
	case 'stats':
		$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
		$year or $year = date('Y', $DT_TIME);
		$month = isset($month) ? intval($month) : date('n', $DT_TIME);
		isset($seller) or $seller = '';
		$xd = $y0 = $y1 = $y2 = '';
		if($month) {
			$L = date('t', datetotime($year.'-'.$month.'-01'));
			for($i = 1; $i <= $L; $i++) {
				if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; $y2 .= ','; }
				$xd .= "'".$i."日'";
				$F = datetotime($year.'-'.$month.'-'.$i.' 00:00:00');
				$T = datetotime($year.'-'.$month.'-'.$i.' 23:59:59');
				$condition = "mid=$moduleid AND pid=0 AND addtime>=$F AND addtime<=$T";
				if($seller) $condition .= " AND seller='$seller'";
				$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$table} WHERE {$condition} AND status=4");
				$num1 = $t['num1'] ? dround($t['num1']) : 0;
				$num2 = $t['num2'] ? dround($t['num2']) : 0;
				$num = $num1 + $num2;
				$y0 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$table} WHERE {$condition} AND status=6");
				$num1 = $t['num1'] ? dround($t['num1']) : 0;
				$num2 = $t['num2'] ? dround($t['num2']) : 0;
				$num = $num1 + $num2;
				$y1 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$table} WHERE {$condition} AND status=7");
				$num1 = $t['num1'] ? dround($t['num1']) : 0;
				$num2 = $t['num2'] ? dround($t['num2']) : 0;
				$num = $num1 + $num2;
				$y2 .= $num;
			}
			$title = $year.'年'.$month.'月交易报表(单位:'.$DT['money_unit'].')';
			if($seller) $title = '['.$seller.'] '.$title;
		} else {
			for($i = 1; $i < 13; $i++) {
				if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; $y2 .= ','; }
				$xd .= "'".$i."月'";
				$F = datetotime($year.'-'.$i.'-01 00:00:00');
				$T = datetotime($year.'-'.$i.'-'.date('t', $F).' 23:59:59');
				$condition = "mid=$moduleid AND pid=0  AND addtime>=$F AND addtime<=$T";
				if($seller) $condition .= " AND seller='$seller'";
				$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$table} WHERE {$condition} AND status=4");
				$num1 = $t['num1'] ? dround($t['num1']) : 0;
				$num2 = $t['num2'] ? dround($t['num2']) : 0;
				$num = $num1 + $num2;
				$y0 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$table} WHERE {$condition} AND status=6");
				$num1 = $t['num1'] ? dround($t['num1']) : 0;
				$num2 = $t['num2'] ? dround($t['num2']) : 0;
				$num = $num1 + $num2;
				$y1 .= $num;
				$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$table} WHERE {$condition} AND status=7");
				$num1 = $t['num1'] ? dround($t['num1']) : 0;
				$num2 = $t['num2'] ? dround($t['num2']) : 0;
				$y1 .= $num;
			}
			$title = $year.'年交易报表(单位:'.$DT['money_unit'].')';
			if($seller) $title = '['.$seller.'] '.$title;
		}
		include tpl('order_stats', $module);
	break;
	case 'refund':
		//兼容8.0 9.0之后的退款订单归入售后服务
		//buyer_reason和seller_reason字段在9.0之后废弃
		($O['status'] == 5 && $O['buyer_reason']) or msg('此交易无需受理');
		isset($status) or msg('请指定受理结果');
		strlen($content) > 5 or msg('请填写操作理由');
		$content .= '[网站]';
		clear_upload($content, $itemid, $table);
		$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
		if($status == 6) {//已退款，买家胜 退款
			money_add($O['buyer'], $O['total']);
			money_record($O['buyer'], $O['total'], $L['in_site'], 'system', '订单退款', '单号:'.$itemid.'[网站]');
			$_msg = '受理成功，交易状态已经改变为 已退款给买家';
			//更新商品数据 增加库存
			if($MODULE[$O['mid']]['module'] == 'mall') {
				$db->query("UPDATE ".get_table($O['mid'])." SET orders=orders-1,sales=sales-$O[number],amount=amount+$O[number] WHERE itemid=$mallid");
			} else {
				$db->query("UPDATE ".get_table($O['mid'])." SET amount=amount+$O[number] WHERE itemid=$mallid");
			}
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','网站退款','')");
		} else if($status == 4) {//已退款，卖家胜 付款
			money_add($O['seller'], $O['total']);
			money_record($O['seller'], $O['total'], $L['in_site'], 'system', '交易成功', '单号:'.$itemid.'[网站]');
			//网站服务费
			$G = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE username='".$O['seller']."'");
			$SG = cache_read('group-'.$G['groupid'].'.php');
			if($SG['commission']) {
				$fee = dround($O['total']*$SG['commission']/100);
				if($fee > 0) {
					money_add($O['seller'], -$fee);
					money_record($O['seller'], -$fee, $L['in_site'], 'system', $L['trade_fee'], $L['trade_order_id'].$itemid);	
				}
			}
			//分销会员返款
			$inviter = $O['inviter'];
			if($inviter) {
				$a = $db->get_one("SELECT * FROM {$DT_PRE}agent WHERE username='$O[seller]' AND pusername='$inviter'");
				if($a && $a['status'] == 3 && $a['discount'] > 0 && $a['discount'] < 100) {
					$fee = dround($money*(100-$a['discount'])/100);
					if($fee > 0) {
						money_add($O['seller'], -$fee);
						money_record($O['seller'], -$fee, $L['in_site'], $inviter, $L['fee_agent'], $L['trade_order_id'].$itemid);

						money_add($inviter, $fee);
						money_record($inviter, $fee, $L['in_site'], $O['seller'], $L['fee_agent'], $L['trade_order_id'].$itemid);

						$db->get_one("UPDATE {$DT_PRE}agent SET orders=orders+1,amount=amount+{$money},amounty=amounty+{$money},amountm=amountm+{$money} WHERE itemid=$a[itemid]");
					}
				} else {
					$inviter = '';
				}
			}
			foreach($lists as $k=>$v) {
				$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME,inviter='$inviter' WHERE itemid=$v[itemid]");
			}

			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','网站拒绝','')");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','交易成功','')");
			$_msg = '受理成功，交易状态已经改变为 交易成功';
		} else {
			msg();
		}
		$db->query("UPDATE {$table} SET status=$status,editor='$_username',updatetime=$DT_TIME,refund_reason='$content' WHERE itemid=$itemid");
		$msg = isset($msg) ? 1 : 0;
		$eml = isset($eml) ? 1 : 0;
		$sms = isset($sms) ? 1 : 0;
		$wec = isset($wec) ? 1 : 0;
		if($msg == 0) $sms = $wec = 0;
		if($msg || $eml || $sms || $wec) {
			$reason = $content;
			$linkurl = $MODULE[2]['linkurl'].'order'.DT_EXT.'?action=update&step=show&itemid='.$itemid;

			$result = ($status == 6 ? '退款成功' : '退款失败');
			$subject = '您的[订单]'.dsubstr($O['title'], 30, '...').'(单号:'.$O['itemid'].')'.$result;
			$content = '尊敬的会员：<br/>您的[订单]'.$O['title'].'(单号:'.$O['itemid'].')'.$result.'！<br/>';
			if($reason) $content .= '操作原因：<br/>'.$reason.'<br/>';
			$content .= '请点击下面的链接查看订单详情：<br/>';
			$content .= '<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/>';
			$content .= '如果您对此操作有异议，请及时与网站联系。<br/>';
			$user = userinfo($O['buyer']);
			if($msg) send_message($user['username'], $subject, $content);
			if($eml) send_mail($user['email'], $subject, $content);
			if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
			if($wec) send_weixin($user['username'], $subject);

			$linkurl = $MODULE[2]['linkurl'].'trade'.DT_EXT.'?action=update&step=show&itemid='.$itemid;
			$result = ($status == 6 ? '已经退款给买家' : '未退款给买家，交易成功');
			$subject = '您的[订单]'.dsubstr($O['title'], 30, '...').'(单号:'.$O['itemid'].')'.$result;
			$content = '尊敬的会员：<br/>您的[订单]'.$O['title'].'(单号:'.$O['itemid'].')'.$result.'！<br/>';
			if($reason) $content .= '操作原因：<br/>'.$reason.'<br/>';
			$content .= '请点击下面的链接查看订单详情：<br/>';
			$content .= '<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/>';
			$content .= '如果您对此操作有异议，请及时与网站联系。<br/>';
			$user = userinfo($O['seller']);
			if($msg) send_message($user['username'], $subject, $content);
			if($eml) send_mail($user['email'], $subject, $content);
			if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
			if($wec) send_weixin($user['username'], $subject);
		}
		msg($_msg, $forward, 3);
	break;
	case 'note_add':
		$note = str_replace(array('|', '-'), array('/', '_'), strip_tags(trim($note)));
		strlen($note) > 3 or msg('请填写备注内容');
		if($O['admin_note']) {
			$note = timetodate($DT_TIME, 5)."|".$_username."|".$note."\n--------------------\n".addslashes($O['admin_note']);
		} else {
			$note = timetodate($DT_TIME, 5)."|".$_username."|".$note;
		}
		$db->query("UPDATE {$table} SET admin_note='$note' WHERE itemid=$itemid");
		dmsg('追加成功', '?moduleid='.$moduleid.'&file='.$file.'&id='.$id.'&action=show&itemid='.$itemid);
	break;
	case 'note_edit':
		$note = strip_tags($note);
		$db->query("UPDATE {$table} SET admin_note='$note' WHERE itemid=$itemid");
		dmsg('修改成功', '?moduleid='.$moduleid.'&file='.$file.'&id='.$id.'&action=show&itemid='.$itemid);
	break;
	case 'show':
		$mid = $O['mid'];
		$auth = encrypt('mall|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
		$comments = array();
		if($MODULE[$mid]['module'] == 'mall') {
			foreach($lists as $k=>$v) {
				$i = $v['itemid'];
				$j = $v['mid'];
				$c = $db->get_one("SELECT * FROM {$DT_PRE}mall_comment_{$j} WHERE itemid={$i}");
				$c['seller_thumbs'] = $c && $c['seller_thumbs'] ? explode('|', $c['seller_thumbs']) : array();
				$comments[$k] = $c;
			}
		}
		$id = isset($id) ? intval($id) : 0;
		include tpl('order_show', $module);
	break;
	case 'delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;		
		$result = $db->query("SELECT * FROM {$table} WHERE itemid IN ($itemids) OR pid IN ($itemids)");
		while($r = $db->fetch_array($result)) {
			$itemid = $r['itemid'];
			$db->query("DELETE FROM {$table} WHERE itemid=$itemid");
			$db->query("DELETE FROM {$table_log} WHERE oid=$itemid");
			if($MODULE[$r['mid']]['module'] == 'mall') $db->query("DELETE FROM {$DT_PRE}mall_comment_{$moduleid} WHERE itemid=$itemid");
		}
		dmsg('删除成功', $forward);
	break;
	case 'comment_edit':
		$mid = $O['mid'];
		$MODULE[$mid]['module'] == 'mall' or msg('此订单不支持评价');
		$cm = $db->get_one("SELECT * FROM {$DT_PRE}mall_comment_{$mid} WHERE itemid=$itemid");
		$cm or msg('评价不存在');
		if($submit) {
			$mallid = $O['mallid'];
			$post['seller_ctime'] = $post['seller_ctime'] ? datetotime($post['seller_ctime']) : 0;
			$post['seller_rtime'] = $post['seller_rtime'] ? datetotime($post['seller_rtime']) : 0;
			$post['buyer_ctime'] = $post['buyer_ctime'] ? datetotime($post['buyer_ctime']) : 0;
			$post['buyer_rtime'] = $post['buyer_rtime'] ? datetotime($post['buyer_rtime']) : 0;
			$post['seller_star'] = intval($post['seller_star']);
			$post['seller_star_express'] = intval($post['seller_star_express']);
			$post['seller_star_service'] = intval($post['seller_star_service']);
			$post['buyer_star'] = intval($post['buyer_star']);
			if($cm['seller_star'] != $post['seller_star']) {
				$s = $post['seller_star'];
				$s1 = 's'.$cm['seller_star'];
				$s2 = 's'.$post['seller_star'];
				$db->query("UPDATE {$table} SET seller_star=$s WHERE itemid=$itemid");
				$db->query("UPDATE {$DT_PRE}mall_stat_{$mid} SET `$s2`=`$s2`+1 WHERE mallid=$mallid");
				if($cm['seller_star']) $db->query("UPDATE {$DT_PRE}mall_stat_{$mid} SET `$s1`=`$s1`-1 WHERE mallid=$mallid");
			}
			if($cm['buyer_star'] != $post['buyer_star']) {
				$s = $post['buyer_star'];
				$s1 = 'b'.$cm['buyer_star'];
				$s2 = 'b'.$post['buyer_star'];
				$db->query("UPDATE {$table} SET buyer_star=$s WHERE itemid=$itemid");
				$db->query("UPDATE {$DT_PRE}mall_stat_{$mid} SET `$s2`=`$s2`+1 WHERE mallid=$mallid");
				if($cm['buyer_star']) $db->query("UPDATE {$DT_PRE}mall_stat_{$mid} SET `$s1`=`$s1`-1 WHERE mallid=$mallid");
			}
			$thumbs = array();
			foreach($post['thumbs'] as $v) {
				if(is_url($v)) $thumbs[] = $v;
			}
			unset($post['thumbs']);
			$post['seller_thumbs'] = $thumbs ? implode('|', $thumbs) : '';
			if($cm['seller_video'] != $post['seller_video']) delete_upload($cm['seller_video'], match_userid($cm['seller_video']));
			clear_upload($post['seller_thumbs'].$post['seller_video'], $itemid);
			$db->query("UPDATE {$DT_PRE}mall_comment_{$mid} SET ".arr2sql($post, 1)." WHERE itemid=$itemid");
			msg('修改成功', $forward);
		}
		$thumbs = $cm['seller_thumbs'] ? explode('|', $cm['seller_thumbs']) : array();
		include tpl('order_comment_edit', $module);
	break;
	case 'comment':
		$sfields = array('按条件', '买家评价', '卖家评价', '买家解释', '卖家解释', '图片地址', '视频地址');
		$dfields = array('seller_comment', 'seller_comment', 'buyer_reply', 'buyer_comment', 'seller_reply', 'seller_thumbs', 'seller_video');
		$sorder  = array('排序方式', '买家评价时间降序', '买家评价时间升序', '卖家评价时间降序', '卖家评价时间升序', '买家回复时间降序', '买家回复时间升序', '卖家回复时间降序', '卖家回复时间升序', '商品评分降序', '商品评分升序', '物流服务降序', '物流服务升序', '商家态度降序', '商家态度升序');
		$dorder  = array('seller_ctime DESC', 'seller_ctime DESC', 'seller_ctime ASC', 'buyer_ctime DESC', 'buyer_ctime ASC', 'seller_rtime DESC', 'seller_rtime ASC', 'buyer_rtime DESC', 'buyer_rtime ASC', 'seller_star DESC', 'seller_star ASC', 'seller_star_express DESC', 'seller_star_express ASC', 'seller_star_service DESC', 'seller_star_service ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('buyer_ctime', 'buyer_rtime', 'seller_ctime', 'seller_rtime')) or $datetype = 'buyer_ctime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$itemid or $itemid = '';
		$mallid = isset($mallid) && $mallid ? intval($mallid) : '';
		$thumb = isset($thumb) ? intval($thumb) : 0;
		$video = isset($video) ? intval($video) : 0;
		$seller_star = isset($seller_star) ? intval($seller_star) : 0;
		$seller_star_express = isset($seller_star_express) ? intval($seller_star_express) : 0;
		$seller_star_service = isset($seller_star_service) ? intval($seller_star_service) : 0;
		$buyer_star = isset($buyer_star) ? intval($buyer_star) : 0;
		isset($seller) or $seller = '';
		isset($buyer) or $buyer = '';
		isset($send_no) or $send_no = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		
		$condition = ($seller_star > 5 || $seller_star < 1) ? "seller_star>0" : "seller_star=$seller_star";
		if($seller_star_express) $condition .= $seller_star_express > 5 ? " AND seller_star_express>0" : " AND seller_star_express=$seller_star_express";
		if($seller_star_service) $condition .= $seller_star_service > 5 ? " AND seller_star_service>0" : " AND seller_star_service=$seller_star_service";
		if($buyer_star) $condition .= $buyer_star > 5 ? " AND buyer_star>0" : " AND buyer_star=$buyer_star";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND $datetype>=$fromtime";
		if($totime) $condition .= " AND $datetype<=$totime";
		if($seller) $condition .= " AND seller='$seller'";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($mallid) $condition .= " AND mallid=$mallid";
		if($thumb) $condition .= " AND seller_thumbs<>''";
		if($video) $condition .= " AND seller_video<>''";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_comment} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$table_comment} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['thumbs'] = $r['seller_thumbs'] ? explode('|', $r['seller_thumbs']) : array();
			$r['video'] = $r['seller_video'] ? $r['seller_video'] : '';
			$lists[] = $r;
		}
		include tpl('order_comment', $module);
	break;
	case 'express':
		$sfields = array('按条件', '商品名称', '快递类型', '快递单号', '卖家', '买家', '买家姓名', '买家地址', '买家邮编', '买家手机', '备注');
		$dfields = array('title', 'title', 'send_type', 'send_no', 'seller', 'buyer', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'updatetime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) && isset($dsend_status[$status]) ? intval($status) : '';
		$itemid or $itemid = '';
		$mallid = isset($mallid) && $mallid ? intval($mallid) : '';
		(isset($seller) && check_name($seller)) or $seller = '';
		(isset($buyer) && check_name($buyer)) or $buyer = '';
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
		isset($send_no) or $send_no = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($dsend_status, 'status', '状态', $status, '', 1, '', 1);
		$condition = "mid=$moduleid AND pid=0 AND send_no<>''";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND $datetype>=$fromtime";
		if($totime) $condition .= " AND $datetype<=$totime";
		if($status !== '') $condition .= " AND send_status='$status'";
		if($seller) $condition .= " AND seller='$seller'";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($mobile) $condition .= " AND buyer_mobile='$mobile'";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($mallid) $condition .= " AND mallid=$mallid";
		if($send_no) $condition .= " AND send_no='$send_no'";
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
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['updatetime'] = timetodate($r['updatetime'], 5);
			$lists[] = $r;
		}
		include tpl('order_express', $module);
	break;
	case 'invoice':
		$sfields = array('关键词', '发票抬头', '发票类型', '纳税人识别号', '商品名称', '卖家', '买家', '买家手机', '买家邮件', '备注');
		$dfields = array('company', 'company', 'type', 'taxid', 'title', 'seller', 'buyer', 'buyer_mobile', 'buyer_email', 'note');
		$sorder  = array('排序方式', '申请时间降序', '申请时间升序', '开票时间降序', '开票时间升序', '发票金额降序', '发票金额升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'updatetime DESC', 'updatetime ASC', 'amount DESC', 'amount ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'updatetime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$itemid or $itemid = '';
		$status = isset($status) ? intval($status) : 0;
		(isset($seller) && check_name($seller)) or $seller = '';
		(isset($buyer) && check_name($buyer)) or $buyer = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = "mid=$moduleid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($seller) $condition .= " AND seller='$seller'";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($itemid) $condition .= " AND (itemid=$itemid || oid=$itemid)";
		if($status == 1) $condition .= " AND updatetime=addtime";
		if($status == 2) $condition .= " AND updatetime>addtime";
		if($status == 3) $condition .= " AND url<>''";
		if($status == 4) $condition .= " AND send_type<>''";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}invoice WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}invoice WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['updatetime'] = $r['updatetime'] ? timetodate($r['updatetime'], 5) : 'N/A';
			$lists[] = $r;
		}
		include tpl('order_invoice', $module);
	break;
	case 'invoice_show':
		$oid = isset($oid) ? intval($oid) : 0;
		$I = array();
		if($itemid) {
			$I = $db->get_one("SELECT * FROM {$DT_PRE}invoice WHERE itemid=$itemid");
		} else if($oid) {
			$I = $db->get_one("SELECT * FROM {$DT_PRE}invoice WHERE oid=$oid");
		}
		$I or msg('发票不存在');
		$itemid = $I['itemid'];
		$auth = encrypt('invoice|'.$I['send_type'].'|'.$I['send_no'].'|'.$I['send_status'].'|'.$I['buyer_mobile'].'|'.$I['itemid'], DT_KEY.'EXPRESS');
		include tpl('order_invoice_show', $module);
	break;
	case 'contract':
		$sfields = array('关键词', '商品名称', '甲方名称', '乙方名称', '甲方合同', '乙方合同', '买家', '卖家');
		$dfields = array('title', 'title', 'buyer_company', 'seller_company', 'buyer_contract', 'seller_contract', 'buyer', 'seller');
		$sorder  = array('排序方式', '下单时间降序', '下单时间升序', '甲方签署降序', '甲方签署升序', '乙方签署降序', '乙方签署升序', '合同金额降序', '合同金额升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'buyer_time DESC', 'buyer_time ASC', 'seller_time DESC', 'seller_time ASC', 'amount DESC', 'amount ASC');
		$dstatus = $L['contract_status'];
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'buyer_time', 'seller_time')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		$itemid or $itemid = '';
		(isset($seller) && check_name($seller)) or $seller = '';
		(isset($buyer) && check_name($buyer)) or $buyer = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select = dselect($sorder, 'order', '', $order);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$condition = "mid=$moduleid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($seller) $condition .= " AND seller='$seller'";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($itemid) $condition .= " AND (itemid=$itemid || oid=$itemid)";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($status !== '') $condition .= " AND status=$status";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}contract WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}contract WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$lists[] = $r;
		}
		include tpl('order_contract', $module);
	break;
	case 'contract_show':
		$oid = isset($oid) ? intval($oid) : 0;
		$C = array();
		if($itemid) {
			$C = $db->get_one("SELECT * FROM {$DT_PRE}contract WHERE itemid=$itemid");
		} else if($oid) {
			$C = $db->get_one("SELECT * FROM {$DT_PRE}contract WHERE oid=$oid");
		}
		$C or msg('合同不存在');
		$itemid = $C['itemid'];
		$dstatus = $L['contract_status'];
		include tpl('order_contract_show', $module);
	break;
	case 'service':
		$table = $table_service;
		$dstatus = $dservice_status;
		$sfields = array('按条件', '商品名称', '买家', '买家申请理由', '买家补充说明', '买家姓名', '买家地址', '买家邮编', '买家手机', '买家快递类型', '买家快递单号', '卖家', '卖家操作理由', '卖家补充说明', '卖家姓名', '卖家地址', '卖家邮编', '卖家手机', '卖家快递类型', '卖家快递单号', '编辑');
		$dfields = array('title', 'title', 'buyer', 'buyer_title', 'buyer_reason', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile', 'buyer_send_type', 'buyer_send_no', 'seller', 'seller_title', 'seller_reason', 'seller_name', 'seller_address', 'seller_postcode', 'seller_mobile', 'seller_send_type', 'seller_send_no', 'editor');
		$sorder  = array('排序方式', '申请时间降序', '申请时间升序', '更新时间降序', '更新时间升序', '商品单价降序', '商品单价升序', '申请数量降序', '申请数量升序', '实付金额降序', '实付金额升序', '服务类型降序', '服务类型升序', '服务状态降序', '服务状态升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'updatetime DESC', 'updatetime ASC', 'price DESC', 'price ASC', 'number DESC', 'number ASC', 'amount DESC', 'amount ASC', 'typeid DESC', 'typeid ASC', 'status DESC', 'status ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		$typeid = isset($typeid) && isset($dservice[$typeid]) ? intval($typeid) : '';
		$itemid or $itemid = '';
		$mallid = isset($mallid) && $mallid ? intval($mallid) : '';
		$id = isset($id) ? intval($id) : 0;
		(isset($seller) && check_name($seller)) or $seller = '';
		(isset($buyer) && check_name($buyer)) or $buyer = '';
		(isset($seller_mobile) && is_mobile($seller_mobile)) or $seller_mobile = '';
		(isset($buyer_mobile) && is_mobile($buyer_mobile)) or $buyer_mobile = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($amounts) or $amounts = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
		isset($mtype) && in_array($mtype, array('amount', 'price', 'number')) or $mtype = 'amount';
		isset($minamount) or $minamount = '';
		isset($maxamount) or $maxamount = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$type_select = dselect($dservice, 'typeid', '类型', $typeid, '', 1, '', 1);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = "mid=$moduleid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND $datetype>=$fromtime";
		if($totime) $condition .= " AND $datetype<=$totime";
		if($status !== '') $condition .= " AND status=$status";
		if($typeid !== '') $condition .= " AND typeid=$typeid";
		if($seller) $condition .= " AND seller='$seller'";
		if($seller_mobile) $condition .= " AND seller_mobile='$seller_mobile'";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($buyer_mobile) $condition .= " AND buyer_mobile='$buyer_mobile'";
		if($itemid) $condition .= " AND (oid=$itemid OR pid=$itemid)";
		if($mallid) $condition .= " AND mallid=$mallid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($id) $condition .= " AND mallid=$id";
		if($minamount != '') $condition .= " AND $mtype>=$minamount";
		if($maxamount != '') $condition .= " AND $mtype<=$maxamount";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = $tags = $pids = array();
		$amount = 0;
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['updatetime'] = timetodate($r['updatetime'], 5);
			$r['typename'] = $dservice[$r['typeid']];
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
			$r['dstatus'] = $dstatus[$r['status']];
			$amount += $r['amount'];
			$lists[] = $r;
		}
		$amount = number_format($amount, 2, '.', '');
		include tpl('order_service', $module);
	break;
	case 'service_show':
		$itemid or msg();		
		$O = $db->get_one("SELECT * FROM {$table_service} WHERE itemid=$itemid");
		$O or msg('售后服务不存在');
		if($submit) {
			(($O['status'] == 0 || $O['status'] == 2) && $O['typeid'] < 2) or msg('此申请无需受理');
			$seller_title = dhtmlspecialchars(trim($reason));
			$seller_reason = dhtmlspecialchars(trim($content));
			if($O['oss'] == 4) {//已完成的订单需要从卖家账户扣除
				if($O['amount'] < 0.01) msg('实付金额错误');
				$S = userinfo($O['seller']);
				if($S['money'] < $O['amount']) msg('卖家账户余额不足');
				money_add($O['seller'], -$O['amount']);
				money_record($O['seller'], -$O['amount'], $L['in_site'], 'system', '订单退款', $L['trade_order_id'].$O['oid'].'（网站介入）');
			}
			money_add($O['buyer'], $O['amount']);
			money_record($O['buyer'], $O['amount'], $L['in_site'], 'system', '订单退款', $L['trade_order_id'].$O['oid'].'（网站介入）');
			$db->query("UPDATE {$table} SET status=6,updatetime=$DT_TIME WHERE itemid=$O[oid]");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$O[oid]','$DT_TIME','网站退款','')");
			$db->query("UPDATE {$table_service} SET status=7,updatetime=$DT_TIME,seller_title='$seller_title',seller_reason='$seller_reason',seller_note='',seller_thumbs='',seller_video='' WHERE itemid=$itemid");
			$msg = isset($msg) ? 1 : 0;
			$eml = isset($eml) ? 1 : 0;
			$sms = isset($sms) ? 1 : 0;
			$wec = isset($wec) ? 1 : 0;
			if($msg == 0) $sms = $wec = 0;
			if($msg || $eml || $sms || $wec) {
				$reason = $content;
				$linkurl = $MODULE[2]['linkurl'].'order'.DT_EXT.'?action=update&step=show&itemid='.$O['oid'];

				$result = '退款成功';
				$subject = '您的[订单]'.dsubstr($O['title'], 30, '...').'(单号:'.$O['oid'].')'.$result;
				$content = '尊敬的会员：<br/>您的[订单]'.$O['title'].'(单号:'.$O['oid'].')'.$result.'！<br/>';
				if($seller_title) $content .= '操作原因：<br/>'.$seller_title.'<br/>';
				$content .= '请点击下面的链接查看订单详情：<br/>';
				$content .= '<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/>';
				$content .= '如果您对此操作有异议，请及时与网站联系。<br/>';
				$user = userinfo($O['buyer']);
				if($msg) send_message($user['username'], $subject, $content);
				if($eml) send_mail($user['email'], $subject, $content);
				if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $subject);

				$linkurl = $MODULE[2]['linkurl'].'trade'.DT_EXT.'?action=update&step=show&itemid='.$O['oid'];
				$result = '已经退款给买家';
				$subject = '您的[订单]'.dsubstr($O['title'], 30, '...').'(单号:'.$O['oid'].')'.$result;
				$content = '尊敬的会员：<br/>您的[订单]'.$O['title'].'(单号:'.$O['oid'].')'.$result.'！<br/>';
				if($seller_title) $content .= '操作原因：<br/>'.$seller_title.'<br/>';
				$content .= '请点击下面的链接查看订单详情：<br/>';
				$content .= '<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/>';
				$content .= '如果您对此操作有异议，请及时与网站联系。<br/>';
				$user = userinfo($O['seller']);
				if($msg) send_message($user['username'], $subject, $content);
				if($eml) send_mail($user['email'], $subject, $content);
				if($sms) send_sms($user['mobile'], $subject.$DT['sms_sign']);
				if($wec) send_weixin($user['username'], $subject);
			}
			msg('退款成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid, 3);
		}
		$O['adddate'] = timetodate($O['addtime'], 5);
		$O['updatedate'] = timetodate($O['updatetime'], 5);
		$O['linkurl'] = gourl('?mid='.$O['mid'].'&itemid='.$O['mallid']);
		$O['par'] = '';
		if(strpos($O['note'], '|') !== false) list($O['note'], $O['par']) = explode('|', $O['note']);
		$buyer_auth = encrypt('buyer|'.$O['buyer_send_type'].'|'.$O['buyer_send_no'].'|'.$O['buyer_send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
		$seller_auth = encrypt('seller|'.$O['seller_send_type'].'|'.$O['seller_send_no'].'|'.$O['seller_send_status'].'|'.$O['seller_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
		$O['buyer_thumbs'] = $O['buyer_thumbs'] ? explode('|', $O['buyer_thumbs']) : array();
		$O['seller_thumbs'] = $O['seller_thumbs'] ? explode('|', $O['seller_thumbs']) : array();
		if($O['buyer_send_status'] == 3 && $O['status'] == 6) {//自动完成
			$O['status'] = 7;
			$db->query("UPDATE {$table_service} SET status=7,updatetime=$DT_TIME WHERE itemid=$itemid");
		}
		include tpl('order_service_show', $module);
	break;
	case 'service_delete':
		$itemid or msg('未选择记录');
		$itemids = is_array($itemid) ? implode(',', $itemid) : $itemid;		
		$db->query("DELETE FROM {$table_service} WHERE itemid IN ($itemids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '商品名称', '店铺', '卖家', '买家', '买家昵称', '订单金额', '附加金额', '附加名称', '买家姓名', '买家地址', '买家邮编', '买家手机', '快递类型', '快递单号', '买家备注', '卖家备注', '管理备注');
		$dfields = array('title', 'title', 'shop', 'seller', 'buyer', 'buyer_passport', 'amount', 'fee', 'fee_name', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile', 'send_type', 'send_no', 'note', 'seller_note', 'admin_note');
		$sorder  = array('排序方式', '下单时间降序', '下单时间升序', '更新时间降序', '更新时间升序', '商品单价降序', '商品单价升序', '购买数量降序', '购买数量升序', '订单金额降序', '订单金额升序', '附加金额降序', '附加金额升序', '订单状态降序', '订单状态升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'updatetime DESC', 'updatetime ASC', 'price DESC', 'price ASC', 'number DESC', 'number ASC', 'amount DESC', 'amount ASC', 'fee DESC', 'fee ASC', 'status DESC', 'status ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		$itemid or $itemid = '';
		$mallid = isset($mallid) && $mallid ? intval($mallid) : '';
		$id = isset($id) ? intval($id) : 0;
		$cod = isset($cod) ? intval($cod) : 0;
		$bill = isset($bill) ? intval($bill) : 0;
		$seller_star = isset($seller_star) ? intval($seller_star) : 0;
		$buyer_star = isset($buyer_star) ? intval($buyer_star) : 0;
		(isset($inviter) && check_name($inviter)) or $inviter = '';
		(isset($seller) && check_name($seller)) or $seller = '';
		(isset($buyer) && check_name($buyer)) or $buyer = '';
		(isset($mobile) && is_mobile($mobile)) or $mobile = '';
		(isset($skuid) && is_skuid($skuid)) or $skuid = '';
		isset($amounts) or $amounts = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
		isset($mtype) && in_array($mtype, array('money', 'amount', 'price', 'fee', 'number')) or $mtype = 'money';
		isset($minamount) or $minamount = '';
		isset($maxamount) or $maxamount = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$order_select = dselect($sorder, 'order', '', $order);
		$condition = "mid=$moduleid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND $datetype>=$fromtime";
		if($totime) $condition .= " AND $datetype<=$totime";
		if($status !== '') $condition .= " AND status=$status";
		if($seller) $condition .= " AND seller='$seller'";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($mobile) $condition .= " AND buyer_mobile='$mobile'";
		if($inviter) $condition .= " AND inviter='$inviter'";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($mallid) $condition .= " AND mallid=$mallid";
		if($skuid) $condition .= " AND skuid='$skuid'";
		if($id) $condition .= " AND mallid=$id";
		if($cod) $condition .= " AND cod>0";
		if($bill) $condition .= " AND bill<>''";
		if($seller_star) $condition .= $seller_star > 5 ? " AND seller_star>0" : " AND seller_star=$seller_star";
		if($buyer_star) $condition .= $buyer_star > 5 ? " AND buyer_star>0" : " AND buyer_star=$buyer_star";
		if($mtype == 'money') $mtype = "`amount`+`fee`";
		if($minamount != '') $condition .= " AND $mtype>=$minamount";
		if($maxamount != '') $condition .= " AND $mtype<=$maxamount";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = $tags = $pids = array();
		$amount = $fee = $money = 0;
		$result = $db->query("SELECT pid,itemid FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$pid = $r['pid'] ? $r['pid'] : $r['itemid'];
			$pids[$pid] = $pid;
		}
		if($pids) {
			$result = $db->query("SELECT * FROM {$table} WHERE itemid IN (".implode(',', $pids).") ORDER BY {$dorder[$order]}");
			while($r = $db->fetch_array($result)) {
				$r['addtime'] = str_replace(' ', '<br/>', timetodate($r['addtime'], 5));
				$r['updatetime'] = str_replace(' ', '<br/>', timetodate($r['updatetime'], 5));
				$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
				$r['dstatus'] = $dstatus[$r['status']];
				$r['money'] = $r['amount'] + $r['fee'];
				$r['money'] = number_format($r['money'], 2, '.', '');
				$amount += $r['amount'];
				$fee += $r['fee'];
				$lists[] = $r;
			}
			$result = $db->query("SELECT * FROM {$table} WHERE pid IN (".implode(',', $pids).") ORDER BY itemid DESC");
			while($r = $db->fetch_array($result)) {
				$r['par'] = '';
				if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
				$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
				$tags[$r['pid']][] = $r;
			}
		}
		$money = $amount + $fee;
		$money = number_format($money, 2, '.', '');
		include tpl('order', $module);
	break;
}
?>