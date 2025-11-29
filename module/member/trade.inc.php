<?php
defined('IN_DESTOON') or exit('Access Denied');
//卖家订单管理
login();
($MG['biz'] && $MG['trade_order']) or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load('order.lang');
$dstatus = $L['trade_status'];
$dsend_status = $L['send_status'];
$dservice_status = $L['service_status'];
$dservice = $L['service_type'];
$step = isset($step) ? trim($step) : '';
$timenow = timetodate($DT_TIME, 5);
$memberurl = $DT_PC ? $MOD['linkurl'] : $MOD['mobile'];
$myurl = userurl($_username);
$STARS = $L['star_type'];
$table = $DT_PRE.'order';
$table_log = $DT_PRE.'order_log';
$table_service = $DT_PRE.'order_service';
$S = $B = array();
$menu_id = 2;
if($action == 'update') {
	$itemid or message();
	$O = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	$O or message($L['trade_msg_null']);
	if($O['seller'] != $_username) message($L['trade_msg_deny']);
	if($O['pid'] > 0 && $O['pid'] != $itemid) dheader('?action='.$action.'&step='.$step.'&itemid='.$O['pid']);
	require DT_ROOT.'/module/mall/global.func.php';
	$O['total'] = $O['amount'] + $O['fee'];
	$O['total'] = number_format($O['total'], 2, '.', '');
	$O['money'] = $O['amount'] + $O['discount'];
	$O['money'] = number_format($O['money'], 2, '.', '');
	$O['adddate'] = timetodate($O['addtime'], 5);
	$O['updatedate'] = timetodate($O['updatetime'], 5);
	$O['linkurl'] = gourl('?mid='.$O['mid'].'&itemid='.$O['mallid']);
	$O['par'] = '';
	if(strpos($O['note'], '|') !== false) list($O['note'], $O['par']) = explode('|', $O['note']);
	$O['sku'] = $O['skuid'] ? get_sku($O['skuid'], $O['seller']) : array();
	$lists = array($O);
	if(($O['amount'] + $O['discount']) > $O['price']*$O['number']) {
		$result = $db->query("SELECT * FROM {$table} WHERE pid=$itemid ORDER BY itemid DESC");
		while($r = $db->fetch_array($result)) {
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
			$r['sku'] = $r['skuid'] ? get_sku($r['skuid'], $r['seller']) : array();
			$amount = $r['price']*$r['number'];
			if($r['amount'] != $amount) {
				$r['amount'] = $amount;
				$db->query("UPDATE {$table} SET amount=$amount WHERE itemid=$r[itemid]");
			}
			$lists[] = $r;
		}
	}
	$mid = $O['mid'];
	$mallid = $O['mallid'];
	switch($step) {
		case 'edit'://修改价格||确认订单||修改为货到付款
			if($O['status'] > 1) message($L['trade_msg_deny']);
			if($submit) {
				$fee = dround($fee);
				if($fee < 0 && $fee < -$O['amount']) message(lang($L['trade_msg_less_fee'], array(-$O['amount'])));
				$fee_name = dhtmlspecialchars(trim($fee_name));
				$status = isset($confirm_order) ? 1 : 0;
				$cod = 0;
				if(isset($edit_cod)) {
					$cod = 1;
					$status = 7;
				}
				$db->query("UPDATE {$table} SET fee='$fee',fee_name='$fee_name',status=$status,cod=$cod,updatetime=$DT_TIME WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_edit]','')");
				foreach($lists as $k=>$v) {
					if($v['itemid'] != $itemid) $db->query("UPDATE {$table} SET status=$status,cod=$cod,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
				}
				if(isset($confirm_order)) {
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t1'], array($itemid));
					$url = $memberurl.'order'.DT_EXT.'?itemid='.$itemid;
					$content = lang($L['trade_message_c1'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);
					//send sms
					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'], lang('sms->ord_confirm', array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_sms_confirm'], 'ID:'.$itemid);
						}
					}
					//send sms
				}
				message($L['trade_price_edit_success'], $forward, 3);
			} else {
				$confirm = isset($confirm) ? 1 : 0;
				$head_title = $L['trade_price_title'];
			}
		break;
		case 'note'://更新备注
			$note = strip_tags(trim($note));
			$db->query("UPDATE {$table} SET seller_note='$note' WHERE itemid=$itemid");
			dmsg($L['trade_msg_svae_note'], '?action='.$action.'&step=detail&itemid='.$itemid);
		break;
		case 'print'://订单打印
			include template('trade_print', $module);
			exit;
		break;
		case 'express'://快递追踪
			($O['send_type'] && $O['send_no']) or dheader('?action=update&step=detail&itemid='.$itemid);
			$auth = encrypt('mall|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$head_title = $L['trade_exprss_title'];
		break;
		case 'refund_agree'://卖家同意买家退款
			//9.0废弃
			$S = $db->get_one("SELECT itemid FROM {$table_service} WHERE oid=$itemid ORDER BY itemid DESC");
			if($S) dheader('?action=process&step=service&itemid='.$S['itemid']);
			if($O['status'] != 5 || !$O['buyer_reason']) message($L['trade_msg_deny']);
			$money = $O['amount'] + $O['fee'];
			if($submit) {
				$content .= $L['trade_refund_by_seller'];
				clear_upload($content, $itemid, $table);
				$content = dsafe(addslashes(save_remote(save_local(stripslashes($content)))));
				is_payword($_username, $password) or message($L['error_payword']);
				money_add($O['buyer'], $money);
				money_record($O['buyer'], $money, $L['in_site'], 'system', $L['trade_refund'], $L['trade_order_id'].$itemid.$L['trade_refund_by_seller']);
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=6,editor='$_username',updatetime=$DT_TIME,refund_reason='$content' WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_agree]','')");
				//更新商品数据 增加库存
				foreach($lists as $k=>$v) {
					if($MODULE[$v['mid']]['module'] == 'mall') {
						$db->query("UPDATE ".get_table($v['mid'])." SET orders=orders-1,sales=sales-$v[number],amount=amount+$v[number] WHERE itemid=$v[mallid]");
					} else {
						$db->query("UPDATE ".get_table($v['mid'])." SET amount=amount+$v[number] WHERE itemid=$v[mallid]");
					}
					if(is_skuid($O['skuid'])) stock_update(0, $O['skuid'], $O['seller'], $v['number'], $O['buyer'], $L['stock_refund'], $L['stock_pay'].$itemid);
				}
				message($L['trade_refund_agree_success'], $forward, 3);
			} else {
				$head_title = $L['trade_refund_agree_title'];
			}
		break;
		case 'split'://拆分订单
			if(($O['status'] != 2 && $O['status'] != 7)) message($L['trade_msg_deny']);
			if($O['send_time'] || count($lists) == 1) message($L['trade_msg_deny']);
			if($submit) {
				(is_array($itemids) && count($itemids) > 0) or message($L['trade_split_order']);
				$C = array();//按ID保存子订单详情
				foreach($lists as $v) {
					if($v['pid'] == $itemid) $C[$v['itemid']] = $v;
				}

				$S = array();//按ID保存分离订单详情
				$new_id = 0;//新订单的ID
				$new_amount = 0.00;//总金额
				foreach($itemids as $id) {
					if(!isset($C[$id])) continue;
					$S[$id] = $C[$id];
					$new_amount += $C[$id]['amount'];
					if(!$new_id) $new_id = $id;
				}
				if($new_id) {
					$new_discount = dround($O['discount']*$new_amount/$O['money']);//按金额比例计算新订单的优惠
					$new_fee = dround($O['fee']*$new_amount/$O['money']);//按金额比例计算新订单的附加费用
					foreach($S as $k=>$v) {
						if($k == $new_id) {//新订单的主订单
							$new_fee_name = addslashes($O['fee_name']);
							$tmp_amount = $new_amount - $new_discount;
							$db->query("UPDATE {$table} SET pid=0,amount=$tmp_amount,discount=$new_discount,fee=$new_fee,fee_name='$new_fee_name' WHERE itemid=$k");							
							$db->query("UPDATE {$table} SET amount=amount-$tmp_amount,discount=discount-$new_discount,fee=fee-$new_fee WHERE itemid=$itemid");
							//复制和新增订单记录
							$result = $db->query("SELECT * FROM {$table_log} WHERE oid=$itemid ORDER BY itemid ASC");
							while($r = $db->fetch_array($result)) {
								$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$new_id','$r[addtime]','".addslashes($r['title'])."','".addslashes($r['note'])."')");
							}
							$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$new_id','$DT_TIME','".$L['trade_split_from']."','".$L['trade_split_oid'].$itemid."')");
							$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_split_to']."','".$L['trade_split_nid'].$new_id."')");
						} else {//新订单的子订单
							$db->query("UPDATE {$table} SET pid=$new_id WHERE itemid=$k");
						}
					}
				}
				message($L['trade_split_success'], $forward, 3);
			} else {
				$head_title = $L['trade_split_title'];
			}
		break;
		case 'bill'://审核凭证
			($O['status'] == 1 || $O['status'] == 7) or message($L['trade_msg_deny']);
			$O['bill'] or message($L['trade_bill_upload']);
			if($submit) {
				if($type) {
					$status = $O['status'] == 7 ? 4 : 2;
					$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_bill_confirm']."','')");
					foreach($lists as $k=>$v) {
						$db->query("UPDATE {$table} SET status=$status,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
					}
					//更新商品数据
					foreach($lists as $k=>$v) {
						$sql = "orders=orders+1";
						if(is_skuid($v['skuid'])) {
							stock_update(0, $v['skuid'], $v['seller'], -$v['number'], $v['buyer'], $L['stock_pay'], $L['stock_no'].$itemid);
						} else {
							$sql .= ",amount=amount-$v[number]"; 
						}
						if($MODULE[$v['mid']]['module'] == 'mall') $sql .= ",sales=sales+$v[number]"; 
						$db->query("UPDATE ".get_table($v['mid'])." SET {$sql} WHERE itemid=$v[mallid]");
					}				
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t7'], array($itemid));
					$url = $memberurl.'order.php?itemid='.$itemid;
					$content = lang($L['trade_message_c7'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);

					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'],  lang($L['trade_message_s7'], array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_bill_check'], 'ID:'.$itemid);
						}
					}
					
					if($status == 7) message($L['trade_success'], $forward, 3);
					message($L['trade_bill_pass'], $forward, 3);
				} else {
					$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_bill_reject']."','')");
					$db->query("UPDATE {$table} SET updatetime=$DT_TIME,bill='' WHERE itemid=$itemid");
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t8'], array($itemid));
					$url = $memberurl.'order.php?itemid='.$itemid;
					$content = lang($L['trade_message_c8'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);

					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'], lang($L['trade_message_s8'], array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_bill_check'], 'ID:'.$itemid);
						}
					}				
					message($L['trade_bill_fail'], $forward, 3);
				}
			} else {
				$head_title = $L['trade_bill_title'];
			}
		break;
		case 'send_goods'://卖家发货
			if(($O['status'] != 2 && $O['status'] != 7)) message($L['trade_msg_deny']);
			if($submit) {
				is_date($send_time) or message($L['msg_express_date_error']);
				$send_type = dhtmlspecialchars(trim($send_type));
				$send_no = dhtmlspecialchars(trim($send_no));
				$status = $O['status'] == 7 ? 7 : 3;
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=$status,updatetime=$DT_TIME,send_type='$send_type',send_no='$send_no',send_time='$send_time' WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_send]','')");
				$touser = $O['buyer'];
				$title = lang($L['trade_message_t3'], array($itemid));
				$url = $memberurl.'order'.DT_EXT.'?itemid='.$itemid;
				$content = lang($L['trade_message_c3'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['buyer'], $title, $content);
			
				//send sms
				if($DT['sms'] && $_sms && isset($sendsms)) {
					$B or $B = userinfo($O['buyer']);
					if(is_mobile($B['mobile'])) {
						$sms_num = sms_send($B['mobile'], lang('sms->ord_send', array($itemid, $send_type, $send_no, $send_time)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_sms_send'], 'ID:'.$itemid);
					}
				}
				//send sms
				
				//更新商品数据 限货到付款的商品
				if($O['cod']) {
					foreach($lists as $k=>$v) {
						if($MODULE[$v['mid']]['module'] == 'mall') {
							$db->query("UPDATE ".get_table($v['mid'])." SET orders=orders+1,sales=sales+$v[number],amount=amount-$v[number] WHERE itemid=$v[mallid]");
						} else {
							$db->query("UPDATE ".get_table($v['mid'])." SET amount=amount-$v[number] WHERE itemid=$v[mallid]");
						}
					}
				}
				message($L['trade_send_success'], $forward, 3);
			} else {
				$head_title = $L['trade_send_title'];
				$send_types = explode('|', trim($MOD['send_types']));
				$send_time = timetodate($DT_TIME, 3);
			}
		break;
		case 'cod_success'://货到付款，确认完成
			if($O['status'] != 7 || !$O['cod'] || !$O['send_time']) message($L['trade_msg_deny']);
			foreach($lists as $k=>$v) {
				$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
			}
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_get]','')");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_success]','')");
			//交易成功
			message($L['trade_success'], $forward, 3);
			
		break;
		case 'add_time'://增加确认收货时间
			if($O['status'] != 3) message($L['trade_msg_deny']);
			if($submit) {
				$add_time = intval($add_time);
				$add_time > 0 or message($L['trade_addtime_null']);
				$add_time = $O['add_time'] + $add_time;
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET add_time='$add_time' WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_addtime]','')");
				message($L['trade_addtime_success'], $forward);
			} else {
				$head_title = $L['trade_addtime_title'];
			}
		break;
		case 'get_pay'://买家确认超时 卖家申请直接付款
			$gone = $DT_TIME - $O['updatetime'];
			if($O['status'] != 3 || $gone < ($MOD['trade_day']*86400 + $O['add_time']*3600)) message($L['trade_msg_deny']);
			//交易成功
			$money = $O['amount'] + $O['fee'];
			money_add($O['seller'], $money);
			money_record($O['seller'], $money, $L['in_site'], 'system', $L['trade_record_pay'], lang($L['trade_buyer_timeout'], array($itemid)));
			//网站服务费
			$G = $db->get_one("SELECT groupid FROM {$DT_PRE}member WHERE username='".$O['seller']."'");
			$SG = cache_read('group-'.$G['groupid'].'.php');
			if($SG['commission']) {
				$fee = dround($money*$SG['commission']/100);
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
				$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
			}
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_getpay]','')");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_success]','')");
			message($L['trade_success'], $forward, 3);
		break;
		case 'comment'://交易评价
			if($MODULE[$O['mid']]['module'] != 'mall') message($L['trade_msg_deny_comment']);
			if($submit) {				
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_seller_coment]','')");
				foreach($lists as $k=>$v) {
					$mid = $v['mid'];
					$itemid = $v['itemid'];
					$mallid = $v['mallid'];
					$star = intval($stars[$itemid]);
					in_array($star, array(1, 2, 3, 4, 5)) or $star = 5;
					$content = dhtmlspecialchars(banword($BANWORD, $contents[$itemid], false));
					$db->query("UPDATE {$table} SET buyer_star=$star WHERE itemid=$itemid");
					$s = 'b'.$star;
					$db->query("UPDATE {$DT_PRE}mall_comment_".$mid." SET buyer_star=$star,buyer_comment='$content',buyer_ctime=$DT_TIME WHERE itemid=$itemid");
					$db->query("UPDATE {$DT_PRE}mall_stat_".$mid." SET bcomment=bcomment+1,`$s`=`$s`+1 WHERE mallid=$mallid");
				}
				message($L['trade_msg_comment_success'], $forward);
			}
			$head_title = $L['trade_comment_title'];
		break;
		case 'comment_detail'://评价详情
			if($MODULE[$O['mid']]['module'] != 'mall') message($L['trade_msg_deny_comment']);
			$comments = array();
			foreach($lists as $k=>$v) {
				$id = $v['itemid'];
				$c = $db->get_one("SELECT * FROM {$DT_PRE}mall_comment_$v[mid] WHERE itemid=$id");
				$c['seller_thumbs'] = $c['seller_thumbs'] ? explode('|', $c['seller_thumbs']) : array();
				$comments[$k] = $c;
			}
			if($submit) {
				$oid = intval($oid);
				if(isset($C[$oid])) {
					$content = dhtmlspecialchars(banword($BANWORD, $content, false));
					$content or message($L['trade_msg_empty_explain']);
					if($C[$oid]['buyer_reply']) message($L['trade_msg_explain_again']);
					$db->query("UPDATE {$DT_PRE}mall_comment_".$O[$oid]['mid']." SET buyer_reply='$content',buyer_rtime=$DT_TIME WHERE itemid=$oid");
					dmsg($L['trade_msg_explain_success'], '?action='.$action.'&step='.$step.'&itemid='.$itemid);
				}
			}
			$head_title = $L['trade_comment_show_title'];
		break;
		case 'close'://关闭交易
			if($O['status'] == 0) {
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=9,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_seller_close]','')");
				dmsg($L['trade_close_success'], $forward);
			} else if($O['status'] == 1) {
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=9,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_seller_close]','')");
				dmsg($L['trade_close_success'], $forward);
			} else if($O['status'] == 8) {
				foreach($lists as $k=>$v) {
					$db->query("DELETE FROM {$table} WHERE itemid=$v[itemid]");
				}
				$db->query("DELETE FROM {$table_log} WHERE itemid=$itemid");
				dmsg($L['trade_delete_success'], $forward);
			} else { 
				message($L['trade_msg_deny']);
			}
		break;
		case 'invoice'://开具发票
			$itemid or message();
			$I = $db->get_one("SELECT * FROM {$DT_PRE}invoice WHERE oid=$itemid AND mid=$mid");
			$I or message($L['trade_invoice_item']);
			if($I['seller'] != $_username) message();
			if($submit) {
				if($type) {
					is_url($fileurl) or message($L['trade_invoice_upload']);
					in_array(file_ext($fileurl), array('pdf', 'rar', 'zip')) or message($L['trade_invoice_format']);
					$send_type = $send_no = '';
					clear_upload($fileurl);
				} else {
					if(strlen($send_type) < 2) message($L['trade_invoice_express']);
					$send_type = dhtmlspecialchars(trim($send_type));
					$send_no = dhtmlspecialchars(trim($send_no));
					$fileurl = '';
				}
				$note = dhtmlspecialchars(trim($note));
				$db->query("UPDATE {$DT_PRE}invoice SET url='$fileurl',updatetime=$DT_TIME,send_type='$send_type',send_no='$send_no',note='$note' WHERE itemid=$I[itemid]");
				$db->query("UPDATE {$table} SET invoice=2 WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_invoice_print']."','')");
				$touser = $O['buyer'];
				$title = lang($L['trade_message_t9'], array($itemid));
				$url = $memberurl.'order.php?itemid='.$itemid;
				$content = lang($L['trade_message_c9'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['buyer'], $title, $content);

				if($DT['sms'] && $_sms && isset($sendsms)) {
					$B or $B = userinfo($O['buyer']);
					if(is_mobile($B['mobile'])) {
						$sms_num = sms_send($B['mobile'], lang($L['trade_message_s9'], array($itemid)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_invoice_sms'], 'ID:'.$itemid);
					}
				}

				dmsg($L['trade_invoice_success'], '?action='.$action.'&step=invoice&itemid='.$itemid.'&reload='.$DT_TIME);
			} else {
				$send_types = explode('|', trim($MOD['send_types']));
				$auth = encrypt('invoice|'.$I['send_type'].'|'.$I['send_no'].'|'.$I['send_status'].'|'.$I['buyer_mobile'].'|'.$I['itemid'], DT_KEY.'EXPRESS');
				$t = $db->get_one("SELECT send_type FROM {$DT_PRE}invoice WHERE seller='$_username' AND send_type<>'' ORDER BY itemid DESC");
				$send_type = $t ? $t['send_type'] : '';
			}
			$head_title = $L['trade_invoice_title'];
		break;
		case 'contract'://合同详情
			if(in_array($O['status'], array(5, 6, 8, 9))) message($L['trade_msg_deny']);
			$C = $O['contract'] ? $db->get_one("SELECT * FROM {$DT_PRE}contract WHERE oid=$itemid AND mid=$mid") : array();
			if($C && $C['seller'] != $_username) message();
			if($submit) {
				is_url($fileurl) or message($L['trade_contract_upload']);
				in_array(file_ext($fileurl), array('pdf', 'jpg', 'jpeg', 'png')) or message($L['trade_contract_format']);
				if($C) {//重签合同
					$db->query("UPDATE {$DT_PRE}contract SET seller_contract='$fileurl',seller_time='$DT_TIME',buyer_contract='',buyer_time=0,status=1 WHERE itemid=$C[itemid]");
					$db->query("UPDATE {$table} SET contract=1 WHERE itemid=$itemid");
					$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_contract_again']."','')");
					clear_upload($fileurl);
					delete_upload($C['seller_contract'], match_userid($C['seller_contract']));
					delete_upload($C['buyer_contract'], match_userid($C['buyer_contract']));
				
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t10'], array($itemid));
					$url = $memberurl.'order.php?itemid='.$itemid;
					$content = lang($L['trade_message_c10'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);

					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'], lang($L['trade_message_s10'], array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_contract_again'], 'ID:'.$itemid);
						}
					}

					dmsg($L['trade_contract_resign'], '?action='.$action.'&step='.$step.'&itemid='.$itemid.'&reload='.$DT_TIME);
				} else {//上传合同
					$db->query("DELETE FROM {$DT_PRE}contract WHERE oid=$itemid AND mid=$mid");
					$post = array();
					$post['oid'] = $itemid;
					$post['mid'] = $mid;
					$post['buyer'] = $O['buyer'];
					$post['seller'] = $O['seller'];
					$post['title'] = addslashes($O['title']);
					$post['amount'] = $O['money'];
					$post['seller_company'] = $_company;
					$post['seller_contract'] = $fileurl;
					$B = userinfo($O['buyer']);
					$post['buyer_company'] = $B['company'];
					$post['addtime'] = $O['addtime'];
					$post['seller_time'] = $DT_TIME;
					$post['status'] = 1;
					$db->query("INSERT INTO {$DT_PRE}contract ".arr2sql($post, 0));
					$db->query("UPDATE {$table} SET contract=1 WHERE itemid=$itemid");
					$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_contract_send']."','')");
					clear_upload($fileurl);				
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t11'], array($itemid));
					$url = $memberurl.'order.php?itemid='.$itemid;
					$content = lang($L['trade_message_c11'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);

					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'], lang($L['trade_message_s11'], array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_contract_sign'], 'ID:'.$itemid);
						}
					}

					dmsg($L['trade_contract_success'], '?action='.$action.'&step='.$step.'&itemid='.$itemid.'&reload='.$DT_TIME);
				}
			} else {
				$cstatus = $L['contract_status'];
				$head_title = $L['trade_contract_title'];
			}
		break;
		default://订单详情
			$logs = array();
			$result = $db->query("SELECT * FROM {$table_log} WHERE oid=$itemid ORDER BY itemid DESC");
			while($r = $db->fetch_array($result)) {
				$r['adddate'] = timetodate($r['addtime'], 5);
				$logs[] = $r;
			}
			$auth = encrypt('mall|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$S = userinfo($O['seller']);
			$step = 'detail';
			$head_title = $L['trade_detail_title'];
		break;
	}
} else if($action == 'process') {//售后流程
	$itemid or message();
	$O = $db->get_one("SELECT * FROM {$table_service} WHERE itemid=$itemid");
	$O or message($L['process_item']);
	if($O['seller'] != $_username) message($L['trade_msg_deny']);
	$O['adddate'] = timetodate($O['addtime'], 5);
	$O['updatedate'] = timetodate($O['updatetime'], 5);
	$O['linkurl'] = gourl('?mid='.$O['mid'].'&itemid='.$O['mallid']);
	$O['par'] = '';
	if(strpos($O['note'], '|') !== false) list($O['note'], $O['par']) = explode('|', $O['note']);
	switch($step) {
		case 'check'://受理申请
			if($O['status'] != 0) message($L['trade_msg_deny']);
			if($submit) {
				$agree = $post['typeid'] ? 1 : 0;
				$aid = isset($aid) ? intval($aid) : 0;
				if($agree) {
					if($O['typeid'] && !$aid) message($L['process_check_aid']);
					if(!$O['typeid'] && $O['oss'] == 4 && $_money < $O['amount']) message($L['process_check_money']);
					if($O['typeid'] < 2 && $O['amount'] < 0.01) message($L['process_check_amount']);
					$status = $O['typeid'] ? 3 : 7;//仅退款 已完成
					$post['reason'] = $post['content'] = $post['video'] = '';
					$post['thumbs'] = array();
				} else {
					if(!$post['reason']) message($L['process_check_reason']);
					$status = 2;
					$aid = 0;
					$post['note'] = '';
				}
				$seller_name = $seller_address = $seller_postcode = $seller_mobile = '';
				if($aid) {
					$addr = get_address($_username, $aid);
					if($addr) {
						$seller_name = addslashes($addr['truename']);
						$seller_address = addslashes(($addr['areaid'] ? area_pos($addr['areaid'], '') : '').$addr['address']);
						$seller_postcode = $addr['postcode'];
						$seller_mobile = $addr['mobile'];
					} else {
						message($L['process_check_addr']);
					}
				}
				$seller_title = dhtmlspecialchars(trim($post['reason']));
				$seller_reason = dhtmlspecialchars(trim($post['content']));
				$seller_note = dhtmlspecialchars(trim($post['note']));
				$seller_thumbs = $uploads = '';
				foreach($post['thumbs'] as $v) {
					if(is_url($v)) $seller_thumbs .= '|'.$v;
				}
				if($seller_thumbs) {
					$seller_thumbs = substr($seller_thumbs, 1);
					$uploads .= $seller_thumbs;
				}
				$seller_video = is_url($post['video']) ? $post['video'] : '';
				if($seller_video) $uploads .= $seller_video;				
				$db->query("UPDATE {$table_service} SET status=$status,updatetime=$DT_TIME,seller_name='$seller_name',seller_address='$seller_address',seller_postcode='$seller_postcode',seller_mobile='$seller_mobile',seller_title='$seller_title',seller_reason='$seller_reason',seller_note='$seller_note',seller_thumbs='$seller_thumbs',seller_video='$seller_video' WHERE itemid=$itemid");
				$oid = $O['pid'] ? $O['pid'] : $O['oid'];
				if($agree) {
					if($O['typeid']) {
						//站内信提醒售后已通过，请返回商品
						$touser = $O['buyer'];
						$title = lang($L['trade_message_t12'], array($oid));
						$url = $memberurl.'order.php?action=service&oid='.$oid;
						$content = lang($L['trade_message_c12'], array($myurl, $_username, $timenow, $url));
						$content = ob_template('messager', 'mail');
						send_message($O['buyer'], $title, $content);

						if($DT['sms'] && $_sms && isset($sendsms)) {
							$B or $B = userinfo($O['buyer']);
							if(is_mobile($B['mobile'])) {
								$sms_num = sms_send($B['mobile'], lang($L['trade_message_s12'], array($itemid)));
								if($sms_num > 0) sms_add($_username, -$sms_num);
								if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_check_agree'], 'ID:'.$itemid);
							}
						}

					} else {//仅退款
						if($O['oss'] == 4) {//已完成的订单需要从卖家账户扣除
							money_add($O['seller'], -$O['amount']);
							money_record($O['seller'], -$O['amount'], $L['in_site'], 'system', $L['process_check_refund'], $L['trade_order_id'].$oid.$L['trade_refund_by_seller']);
						}
						money_add($O['buyer'], $O['amount']);
						money_record($O['buyer'], $O['amount'], $L['in_site'], 'system', $L['process_check_refund'], $L['trade_order_id'].$oid.$L['trade_refund_by_seller']);
						$db->query("UPDATE {$table} SET status=6,updatetime=$DT_TIME WHERE itemid=$O[oid]");
						//站内信提醒退款成功
						$touser = $O['buyer'];
						$title = lang($L['trade_message_t13'], array($oid));
						$url = $memberurl.'order.php?action=service&oid='.$oid;
						$content = lang($L['trade_message_c13'], array($myurl, $_username, $timenow, $url));
						$content = ob_template('messager', 'mail');
						send_message($O['buyer'], $title, $content);

						if($DT['sms'] && $_sms && isset($sendsms)) {
							$B or $B = userinfo($O['buyer']);
							if(is_mobile($B['mobile'])) {
								$sms_num = sms_send($B['mobile'], lang($L['trade_message_s13'], array($itemid)));
								if($sms_num > 0) sms_add($_username, -$sms_num);
								if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_check_refund'], 'ID:'.$itemid);
							}
						}

					}
				} else {
					$db->query("UPDATE {$table} SET status=$O[oss],updatetime=$DT_TIME WHERE itemid=$O[oid]");//恢复订单状态
					//站内信提醒申请未通过
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t14'], array($oid));
					$url = $memberurl.'order.php?action=service&oid='.$oid;
					$content = lang($L['trade_message_c14'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);

					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'], lang($L['trade_message_s14'], array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_check_reject'], 'ID:'.$itemid);
						}
					}
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','".($agree ? $L['process_check_seller_agree'] : $L['process_check_seller_reject'])."','')");
				clear_upload($uploads, $itemid);
				dmsg($L['process_check_success'], '?action=process&step=service&itemid='.$itemid);
			}
			$thumbs = array();
			$video = '';
			$address = get_address($_username);
			$O['buyer_thumbs'] = $O['buyer_thumbs'] ? explode('|', $O['buyer_thumbs']) : array();
			$O['seller_thumbs'] = $O['seller_thumbs'] ? explode('|', $O['seller_thumbs']) : array();
			$head_title = $L['process_check_title'];
		break;
		case 'receive'://验收商品
			if($O['status'] != 4) message($L['trade_msg_deny']);
			if($submit) {
				$agree = $post['typeid'] ? 1 : 0;
				$status = $agree ? 3 : 2;
				if($agree) {
					if($O['typeid'] == 1 && $O['oss'] == 4 && $_money < $O['amount']) message($L['process_check_money']);
					$status = $O['typeid'] > 1 ? 5 : 7;//退货退款 已完成
					$post['reason'] = $post['content'] = $post['video'] = '';
					$post['thumbs'] = array();
				} else {
					if(!$post['reason']) message($L['process_check_reason']);
					$status = 2;
				}

				$seller_title = dhtmlspecialchars(trim($post['reason']));
				$seller_reason = dhtmlspecialchars(trim($post['content']));
				$seller_thumbs = $uploads = '';
				foreach($post['thumbs'] as $v) {
					if(is_url($v)) $seller_thumbs .= '|'.$v;
				}
				if($seller_thumbs) {
					$seller_thumbs = substr($seller_thumbs, 1);
					$uploads .= $seller_thumbs;
				}
				$seller_video = is_url($post['video']) ? $post['video'] : '';
				if($seller_video) $uploads .= $seller_video;				
				$db->query("UPDATE {$table_service} SET status=$status,updatetime=$DT_TIME,seller_title='$seller_title',seller_reason='$seller_reason',seller_thumbs='$seller_thumbs',seller_video='$seller_video' WHERE itemid=$itemid");
				$oid = $O['pid'] ? $O['pid'] : $O['oid'];
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','".($agree ? $L['process_receive_pass'] : $L['process_check_seller_reject'])."','')");
				if($agree) {
					if($O['typeid'] == 1) {//退款
						if($O['oss'] == 4) {//已完成的订单需要从卖家账户扣除
							money_add($O['seller'], -$O['amount']);
							money_record($O['seller'], -$O['amount'], $L['in_site'], 'system', $L['process_check_refund'], $L['trade_order_id'].$oid.$L['trade_refund_by_seller']);
						}
						money_add($O['buyer'], $O['amount']);
						money_record($O['buyer'], $O['amount'], $L['in_site'], 'system', $L['process_check_refund'], $L['trade_order_id'].$oid.$L['trade_refund_by_seller']);
						$db->query("UPDATE {$table} SET status=6,updatetime=$DT_TIME WHERE itemid=$O[oid]");
						$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','".$L['process_receive_refund']."','')");
						//站内信提醒退款成功
						$touser = $O['buyer'];
						$title = lang($L['trade_message_t15'], array($oid));
						$url = $memberurl.'order.php?action=service&oid='.$oid;
						$content = lang($L['trade_message_c15'], array($myurl, $_username, $timenow, $url));
						$content = ob_template('messager', 'mail');
						send_message($O['buyer'], $title, $content);

						if($DT['sms'] && $_sms && isset($sendsms)) {
							$B or $B = userinfo($O['buyer']);
							if(is_mobile($B['mobile'])) {
								$sms_num = sms_send($B['mobile'], lang($L['trade_message_s15'], array($itemid)));
								if($sms_num > 0) sms_add($_username, -$sms_num);
								if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_check_refund'], 'ID:'.$itemid);
							}
						}
					}
				} else {
					$db->query("UPDATE {$table} SET status=$O[oss],updatetime=$DT_TIME WHERE itemid=$O[oid]");//恢复订单状态
					//站内信提醒申请未通过
					$touser = $O['buyer'];
					$title = lang($L['trade_message_t16'], array($oid));
					$url = $memberurl.'order.php?action=service&oid='.$oid;
					$content = lang($L['trade_message_c16'], array($myurl, $_username, $timenow, $url));
					$content = ob_template('messager', 'mail');
					send_message($O['buyer'], $title, $content);

					if($DT['sms'] && $_sms && isset($sendsms)) {
						$B or $B = userinfo($O['buyer']);
						if(is_mobile($B['mobile'])) {
							$sms_num = sms_send($B['mobile'], lang($L['trade_message_s16'], array($itemid)));
							if($sms_num > 0) sms_add($_username, -$sms_num);
							if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_check_reject'], 'ID:'.$itemid);
						}
					}
				}
				clear_upload($uploads, $itemid);
				dmsg($L['process_receive_success'], '?action=process&step=service&itemid='.$itemid);
			}
			$thumbs = array();
			$video = '';
			$seller_auth = encrypt('seller|'.$O['seller_send_type'].'|'.$O['seller_send_no'].'|'.$O['seller_send_status'].'|'.$O['seller_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$O['buyer_thumbs'] = $O['buyer_thumbs'] ? explode('|', $O['buyer_thumbs']) : array();
			$O['seller_thumbs'] = $O['seller_thumbs'] ? explode('|', $O['seller_thumbs']) : array();
			$head_title = $L['process_receive_title'];
		break;
		case 'send'://寄回商品
			if($O['status'] != 5) message($L['trade_msg_deny']);
			if($submit) {
				is_time($send_time) or message($L['process_send_time']);
				$status = 6;
				$oid = $O['pid'] ? $O['pid'] : $O['oid'];				
				$buyer_send_time = datetotime($send_time);
				$buyer_send_type = dhtmlspecialchars(trim($send_type));
				$buyer_send_no = dhtmlspecialchars(trim($send_no));			
				$db->query("UPDATE {$table_service} SET status=$status,updatetime=$DT_TIME,buyer_send_time='$buyer_send_time',buyer_send_type='$buyer_send_type',buyer_send_no='$buyer_send_no' WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','".$L['process_seller_send']."','')");
				//通知买家卖家已寄回
				$touser = $O['buyer'];
				$title = lang($L['trade_message_t17'], array($oid));
				$url = $memberurl.'order.php?action=service&oid='.$oid;
				$content = lang($L['trade_message_c17'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['buyer'], $title, $content);

				if($DT['sms'] && $_sms && isset($sendsms)) {
					$B or $B = userinfo($O['buyer']);
					if(is_mobile($B['mobile'])) {
						$sms_num = sms_send($B['mobile'], lang($L['trade_message_s17'], array($itemid)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_send_back'], 'ID:'.$itemid);
					}
				}
				dmsg($L['op_success'], '?action=process&step=service&itemid='.$itemid);
			}
			$send_types = explode('|', trim($MOD['send_types']));
			$send_time = timetodate($DT_TIME, 6);
			$O['buyer_thumbs'] = $O['buyer_thumbs'] ? explode('|', $O['buyer_thumbs']) : array();
			$O['seller_thumbs'] = $O['seller_thumbs'] ? explode('|', $O['seller_thumbs']) : array();
			$head_title = $L['process_send_title'];
		break;
		default://售后详情
			$buyer_auth = encrypt('buyer|'.$O['buyer_send_type'].'|'.$O['buyer_send_no'].'|'.$O['buyer_send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$seller_auth = encrypt('seller|'.$O['seller_send_type'].'|'.$O['seller_send_no'].'|'.$O['seller_send_status'].'|'.$O['seller_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$O['buyer_thumbs'] = $O['buyer_thumbs'] ? explode('|', $O['buyer_thumbs']) : array();
			$O['seller_thumbs'] = $O['seller_thumbs'] ? explode('|', $O['seller_thumbs']) : array();
			if($O['buyer_send_status'] == 3 && $O['status'] == 6) {//自动完成
				$O['status'] = 7;
				$db->query("UPDATE {$table_service} SET status=7,updatetime=$DT_TIME WHERE itemid=$itemid");
			}
			$step = 'service';
			$head_title = $L['process_title'];
		break;
	}
} else if($action == 'muti') {//批量发货
	if($submit) {
		($itemid && is_array($itemid)) or message($L['trade_msg_muti_choose']);
		is_date($send_time) or message($L['msg_express_date_error']);
		$send_type = dhtmlspecialchars(trim($send_type));
		$itemids = implode(',', $itemid);
		$condition = "pid=0 AND seller='$_username' AND status=2 AND itemid IN ($itemids)";
		$tags = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT 50");
		while($r = $db->fetch_array($result)) {
			$tags[] = $r;
		}
		foreach($tags as $O) {
			$itemid = $O['itemid'];
			$send_no = dhtmlspecialchars(trim($send_nos[$itemid]));
			$status = $O['status'] == 7 ? 7 : 3;
			$lists = get_orders($itemid);
			foreach($lists as $k=>$v) {
				$db->query("UPDATE {$table} SET status=$status,updatetime=$DT_TIME,send_type='$send_type',send_no='$send_no',send_time='$send_time' WHERE itemid=$v[itemid]");
			}
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_send]','')");
			$touser = $O['buyer'];
			$title = lang($L['trade_message_t3'], array($itemid));
			$url = $memberurl.'order'.DT_EXT.'?itemid='.$itemid;
			$content = lang($L['trade_message_c3'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($O['buyer'], $title, $content);
			
			//send sms
			if($DT['sms'] && $_sms && isset($sendsms)) {
				$B or $B = userinfo($O['buyer']);
				if($B['mobile']) {
					$sms_num = sms_send($B['mobile'], lang('sms->ord_send', array($itemid, $send_type, $send_no, $send_time)));
					if($sms_num > 0) sms_add($_username, -$sms_num);
					if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_sms_send'], 'ID:'.$itemid);
				}
			}
			//send sms
			
			//更新商品数据 限货到付款的商品
			if($O['cod']) {
				foreach($lists as $k=>$v) {
					if($MODULE[$v['mid']]['module'] == 'mall') {
						$db->query("UPDATE ".get_table($v['mid'])." SET orders=orders+1,sales=sales+$v[number],amount=amount-$v[number] WHERE itemid=$v[mallid]");
					} else {
						$db->query("UPDATE ".get_table($v['mid'])." SET orders=orders+1,amount=amount-$v[number] WHERE itemid=$v[mallid]");
					}
					if(is_skuid($O['skuid'])) stock_update(0, $O['skuid'], $O['seller'], -$v['number'], $O['buyer'], $L['stock_pay'], $L['stock_no'].$itemid);
				}
			}
		}
		dmsg($L['trade_send_success'], '?action=muti');
	} else {
		require DT_ROOT.'/module/mall/global.func.php';
		$sfields = $L['trade_sfields'];
		$dfields = array('title', 'title ', 'amount', 'fee', 'fee_name', 'buyer', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile', 'buyer_phone', 'send_type', 'send_no', 'note');
		$mallid = isset($mallid) ? intval($mallid) : 0;
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$cod = isset($cod) ? intval($cod) : 0;
		$nav = isset($nav) ? intval($nav) : -1;
		(isset($buyer) && check_name($buyer)) or $buyer = '';
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);	
		$condition = "pid=0 AND seller='$_username' AND status=2";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($status !== '') $condition .= " AND status=$status";
		if($itemid) $condition .= " AND itemid=$itemid";
		if($mid > 4) $condition .= " AND mid=$mid";
		if($mallid) $condition .= " AND mallid=$mallid";
		if($buyer) $condition .= " AND buyer='$buyer'";
		if($cod) $condition .= " AND cod=1";
		if(in_array($nav, array(0,1,2,3,5,6))) {
			$condition .= " AND status=$nav";
			$status = $nav;
		} else if($nav == 4) {
			$condition .= " AND status=$nav AND buyer_star=0";
			$status = $nav;
		}	
		$lists = $pids = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT 100");
		while($r = $db->fetch_array($result)) {
			if($r['amount'] > $r['price']*$r['number']) $pids[] = $r['itemid'];
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
			$r['dstatus'] = $dstatus[$r['status']];
			$r['money'] = $r['amount'] + $r['fee'];
			$r['money'] = number_format($r['money'], 2, '.', '');			
			$r['sku'] = $r['skuid'] ? get_sku($r['skuid'], $r['seller']) : array();
			$lists[] = $r;
		}
		if($pids) {
			$result = $db->query("SELECT * FROM {$table} WHERE pid IN (".implode(',', $pids).") ORDER BY itemid DESC");
			while($r = $db->fetch_array($result)) {
				$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
				$r['par'] = '';
				if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
				$tags[$r['pid']][] = $r;
			}
		}
		$send_types = explode('|', trim($MOD['send_types']));
		$send_time = timetodate($DT_TIME, 3);
		$t = $db->get_one("SELECT send_type FROM {$table} WHERE seller='$_username' AND send_type<>'' ORDER BY itemid DESC");
		$send_type = $t ? $t['send_type'] : '';
		$head_title = $L['trade_muti_send_title'];
	}
} else if($action == 'express') {//我的快递
	$sfields = $L['express_sfields'];
	$dfields = array('title', 'title', 'send_type ', 'send_no', 'buyer_mobile', 'buyer_address');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$status = isset($status) && isset($dsend_status[$status]) ? intval($status) : '';
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dsend_status, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "pid=0 AND send_no<>'' AND seller='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($status !== '') $condition .= " AND send_status='$status'";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['addtime'] = timetodate($r['addtime'], 5);
		$r['updatetime'] = timetodate($r['updatetime'], 5);
		$r['dstatus'] = $dsend_status[$r['send_status']];
		$lists[] = $r;
	}
	$head_title = $L['express_title'];
} else if($action == 'invoice') {//我的发票
	$sfields = $L['invoice_trade_sfields'];
	$dfields = array('company', 'company', 'type', 'taxid', 'amount', 'title', 'buyer', 'buyer_mobile', 'buyer_email', 'note');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$itemid or $itemid = '';
	$status = isset($status) ? intval($status) : 0;
	(isset($buyer) && check_name($buyer)) or $buyer = '';
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'updatetime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$condition = "seller='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($itemid) $condition .= " AND itemid=$itemid";
	if($buyer) $condition .= " AND buyer='$buyer'";
	if($status == 1) $condition .= " AND updatetime<addtime";
	if($status == 2) $condition .= " AND updatetime>addtime";
	if($status == 3) $condition .= " AND url<>''";
	if($status == 4) $condition .= " AND send_type<>''";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}invoice WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);	
	$lists = $pids = array();
	$result = $db->query("SELECT * FROM {$DT_PRE}invoice WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['addtime'] = timetodate($r['addtime'], 5);
		$r['updatetime'] = $r['updatetime'] ? timetodate($r['updatetime'], 5) : 'N/A';
		$lists[] = $r;
	}
	$head_title = $L['invoice_title'];
} else if($action == 'contract') {//我的合同
	$table = $DT_PRE.'contract';
	$sfields = $L['contract_trade_sfields'];
	$dfields = array('title', 'title', 'amount', 'buyer_company', 'buyer');
	$dstatus = $L['contract_status'];
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	$nav = isset($nav) ? intval($nav) : -1;
	(isset($buyer) && check_name($buyer)) or $buyer = '';
	isset($datetype) && in_array($datetype, array('addtime', 'seller_time', 'buyer_time')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$itemid or $itemid = '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "seller='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($buyer) $condition .= " AND buyer='$buyer'";
	if($status !== '') $condition .= " AND status=$status";
	if($itemid) $condition .= " AND itemid=$itemid";
	if(in_array($nav, array(0,1,2,3))) {
		$condition .= " AND status=$nav";
		$status = $nav;
	}
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);	
	$lists = $pids = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['addtime'] = timetodate($r['addtime'], 5);
		$lists[] = $r;
	}
	$head_title = $L['contract_title'];
} else if($action == 'service') {//售后服务
	$sfields = $L['service_sfields'];
	$dfields = array('title', 'title', 'buyer_title', 'buyer_reason');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$status = isset($status) && isset($dservice_status[$status]) ? intval($status) : '';
	$typeid = isset($typeid) && isset($dservice[$typeid]) ? intval($typeid) : '';
	$nav = isset($nav) ? intval($nav) : -1;		
	(isset($buyer) && check_name($buyer)) or $buyer = '';
	isset($datetype) && in_array($datetype, array('addtime', 'edittime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$oid = isset($oid) ? intval($oid) : 0;
	if($oid < 1) $oid = '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dservice_status, 'status', $L['status'], $status, '', 1, '', 1);
	$type_select = dselect($dservice, 'typeid', $L['service_name'], $typeid, '', 1, '', 1);
	$condition = "seller='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($buyer) $condition .= " AND buyer='$buyer'";
	if($status !== '') $condition .= " AND status=$status";
	if($typeid !== '') $condition .= " AND typeid=$typeid";
	if($oid) $condition .= " AND (oid=$oid OR pid=$oid)";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table_service} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);	
	$lists = $pids = array();
	$result = $db->query("SELECT * FROM {$table_service} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['addtime'] = timetodate($r['addtime'], 5);
		$r['typename'] = $dservice[$r['typeid']];
		$r['par'] = '';
		if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
		$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
		$r['dstatus'] = $dservice_status[$r['status']];
		$lists[] = $r;
	}
	$head_title = $L['service_title'];
} else {
	$sfields = $L['trade_sfields'];
	$dfields = array('title', 'title ', 'amount', 'fee', 'fee_name', 'buyer', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile', 'buyer_phone', 'send_type', 'send_no', 'note', 'seller_note');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$mallid = isset($mallid) ? intval($mallid) : 0;
	$mallid or $mallid = '';
	$itemid or $itemid = '';
	$cod = isset($cod) ? intval($cod) : 0;
	$nav = isset($nav) ? intval($nav) : -1;
	(isset($inviter) && check_name($inviter)) or $inviter = '';
	(isset($buyer) && check_name($buyer)) or $buyer = '';
	(isset($skuid) && is_skuid($skuid)) or $skuid = '';
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	(isset($mobile) && is_mobile($mobile)) or $mobile = '';
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "seller='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($status !== '') $condition .= " AND status=$status";
	if($itemid) $condition .= " AND itemid=$itemid";
	if($mallid) $condition .= " AND mallid=$mallid";
	if($skuid) $condition .= " AND skuid='$skuid'";
	if($buyer) $condition .= " AND buyer='$buyer'";
	if($inviter) $condition .= " AND inviter='$inviter'";
	if($mobile) $condition .= " AND buyer_mobile='$mobile'";
	if($cod) $condition .= " AND cod=1";
	if(in_array($nav, array(0,1,2,3,4,5,6))) {
		$condition .= " AND status=$nav";
		$status = $nav;
	} else if($nav == 7) {
		$condition .= " AND status=4 AND buyer_star=0";
		$status = $nav;
	}
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
	$orders = $r['num'];
	$lists = $tags = $pids = array();
	$amount = $fee = $money = 0;
	$result = $db->query("SELECT pid,itemid FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$pid = $r['pid'] ? $r['pid'] : $r['itemid'];
		$pids[$pid] = $pid;
	}
	if($pids) {
		$result = $db->query("SELECT * FROM {$table} WHERE itemid IN (".implode(',', $pids).") ORDER BY itemid DESC");
		while($r = $db->fetch_array($result)) {
			$r['gone'] = $DT_TIME - $r['updatetime'];
			if($r['status'] == 3) {
				if($r['gone'] > ($MOD['trade_day']*86400 + $r['add_time']*3600)) {
					$r['lefttime'] = 0;
				} else {
					$r['lefttime'] = sectoread($MOD['trade_day']*86400 + $r['add_time']*3600 - $r['gone']);
				}
			}
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
			$r['addtime'] = timetodate($r['addtime'], $DT_PC ? 5 : 3);
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
	$head_title = $L['trade_title'];
}
if($DT_PC) {
	//
} else {
	$wx_jssdk = array();
	if(($action == 'muti' || $step == 'send_goods') && in_array($DT_MBS, array('weixin', 'wxmini'))) {
		$WX = cache_read('weixin.php');
		if($WX['appid'] && $WX['appsecret']) {
			require DT_ROOT.'/api/weixin/jssdk.php';
			$jssdk = new JSSDK($WX['appid'], $WX['appsecret']);
			$wx_jssdk = $jssdk->GetSignPackage();
		}	
	}
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : 'biz.php');
	$head_name = $head_title;
}
include template('trade', $module);
?>