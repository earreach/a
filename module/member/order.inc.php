<?php
defined('IN_DESTOON') or exit('Access Denied');
//买家订单管理
login();
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
$table_order = $table;
$table_log = $DT_PRE.'order_log';
$table_service = $DT_PRE.'order_service';
$S = $B = array();
if($action == 'update') {
	$itemid or message();
	$O = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	$O or message($L['trade_msg_null']);
	if($O['buyer'] != $_username) message($L['trade_msg_deny']);
	if($O['pid'] > 0 && $O['pid'] != $itemid) dheader('?action='.$action.'&step='.$step.'&itemid='.$O['pid']);
	if($O['contract'] == 1 && $step != 'contract') message($L['order_update_contract'], '?action='.$action.'&step=contract&itemid='.$itemid);
	$O['total'] = $O['amount'] + $O['fee'];
	$O['total'] = number_format($O['total'], 2, '.', '');
	$O['money'] = $O['amount'] + $O['discount'];
	$O['money'] = number_format($O['money'], 2, '.', '');
	$O['adddate'] = timetodate($O['addtime'], 5);
	$O['updatedate'] = timetodate($O['updatetime'], 5);
	$O['linkurl'] = gourl('?mid='.$O['mid'].'&itemid='.$O['mallid']);
	$O['par'] = '';
	if(strpos($O['note'], '|') !== false) list($O['note'], $O['par']) = explode('|', $O['note']);
	$lists = array($O);
	if(($O['amount'] + $O['discount']) > $O['price']*$O['number']) {
		$result = $db->query("SELECT * FROM {$table} WHERE pid=$itemid ORDER BY itemid DESC");
		while($r = $db->fetch_array($result)) {
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
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
		case 'edit'://修改地址
			if($O['status'] > 1) message($L['trade_msg_deny']);
			if($submit) {
				$aid = isset($aid) ? intval($aid) : 0;
				$aid > 0 or message($L['order_edit_aid']);
				$addr = get_address($_username, $aid);
				$addr or message($L['order_edit_addr']);
				$buyer_name = addslashes($addr['truename']);
				$buyer_address = addslashes($addr['address']);
				$buyer_postcode = $addr['postcode'];
				$buyer_mobile = $addr['mobile'];
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET buyer_name='$buyer_name',buyer_address='$buyer_address',buyer_postcode='$buyer_postcode',buyer_mobile='$buyer_mobile' WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['order_edit_title']."','')");
				dmsg($L['order_edit_success'], $forward);
			}
			$address = get_address($_username);
			$head_title = $L['order_edit_title'];
		break;
		case 'express'://快递追踪
			($O['send_type'] && $O['send_no']) or dheader('?action=update&step=detail&itemid='.$itemid);
			$auth = encrypt('mall|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$head_title = $L['trade_exprss_title'];
		break;
		case 'pay'://买家付款
			if($O['status'] == 2) dmsg($L['trade_pay_order_success'], '?nav=2&itemid='.$itemid);
			if($O['status'] == 0) message($L['trade_msg_confirm'], '?action=update&step=detail&itemid='.$itemid);
			if($O['status'] == 7) message($L['trade_msg_cod'], '?action=update&step=detail&itemid='.$itemid);
			if($O['status'] != 1) message($L['trade_msg_deny']);
			if($O['bill']) dheader('?action=update&step=bill&itemid='.$itemid);
			$money = $O['total'];
			$money > 0 or message($L['trade_msg_deny']);
			$auto = 0;
			$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
			if($auth && substr($auth, 0, 6) == 'trade|') {				
				$_itemid = intval(substr($auth, 6));
				if($_itemid == $itemid) $auto = $submit = 1;
			}
			$could_pay = 1;
			foreach($lists as $k=>$v) {
				if(!stock_check($v)) {
					$could_pay = $submit = 0;
					break;
				}
			}
			$could_bill = 0;
			if($could_pay) {				
				$S = userinfo($O['seller']);
				if($S['bill'] && $S['vbank']) {
					$SG = cache_read('group-'.$S['groupid'].'.php');
					if($SG['bill']) $could_bill = 1;
				}
			}
			if($submit) {
				$money <= $_money or message($L['money_not_enough']);
				if($money <= $DT['quick_pay']) $auto = 1;
				if(!$auto) {
					is_payword($_username, $password) or message($L['error_payword']);
				}
				money_add($_username, -$money);
				money_record($_username, -$money, $L['in_site'], 'system', $L['trade_pay_order_title'], $L['trade_order_id'].$itemid);
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=2,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_pay]','')");
				$touser = $O['seller'];
				$title = lang($L['trade_message_t2'], array($itemid));
				$url = $memberurl.'trade'.DT_EXT.'?itemid='.$itemid;
				$content = lang($L['trade_message_c2'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['seller'], $title, $content);			
				//send sms
				if($DT['sms'] && $_sms && isset($sendsms)) {
					$S or $S = userinfo($O['seller']);
					if($S['mobile']) {
						$sms_num = sms_send($S['mobile'], lang('sms->ord_pay', array($itemid, $money)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_sms_pay'], 'ID:'.$itemid);
					}
				}
				//send sms
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
				dmsg($L['trade_pay_order_success'], '?nav=2&itemid='.$itemid);
			} else {
				$head_title = $L['trade_pay_order_title'];
			}
		break;
		case 'bill'://线下付款
			if($O['status'] == 2) dmsg($L['trade_pay_order_success'], '?nav=2&itemid='.$itemid);
			if($O['status'] == 0) message($L['trade_msg_confirm'], '?action=update&step=detail&itemid='.$itemid);
			if($O['status'] != 1 && $O['status'] != 7) message($L['trade_msg_deny']);
			if($O['status'] == 7 && !$O['send_time']) message($L['order_bill_wait']);
			$money = $O['total'];
			$money > 0 or message($L['trade_msg_deny']);
			$could_pay = 1;
			foreach($lists as $k=>$v) {
				if(!stock_check($v)) {
					$could_pay = $submit = 0;
					break;
				}
			}
			$could_pay or message($L['order_bill_stock']);
			$could_bill = 0;			
			$S = userinfo($O['seller']);
			if($S['bill'] && $S['vbank']) {
				$SG = cache_read('group-'.$S['groupid'].'.php');
				if($SG['bill']) $could_bill = 1;
			}
			$could_bill or message($L['order_bill_error']);
			if($submit) {
				is_url($bill) or message($L['order_bill_upload']);
				is_image($bill) or message($L['order_bill_format']);
				clear_upload($bill);
				$db->query("UPDATE {$table} SET updatetime=$DT_TIME,bill='$bill' WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['order_bill_send']."','')");
		
				$touser = $O['seller'];
				$title = lang($L['trade_message_t18'], array($itemid));
				$url = $memberurl.'trade.php?itemid='.$itemid;
				$content = lang($L['trade_message_c18'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['seller'], $title, $content);
				
				if($DT['sms'] && $_sms && isset($sendsms)) {
					$S or $S = userinfo($O['seller']);
					if(is_mobile($S['mobile'])) {
						$sms_num = sms_send($S['mobile'], lang($L['trade_message_s18'], array($itemid)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['order_bill_send'], 'ID:'.$itemid);
					}
				}

				dmsg($L['order_bill_success'], '?nav=1&itemid='.$itemid);
			} else {
				$head_title = $L['order_bill_title'];
			}
		break;
		case 'invoice'://申请发票
			if($O['status'] != 4) message($L['trade_msg_deny']);
			if($O['invoice'] < 1 && $DT_TIME - $O['updatetime'] > 90*86400) message($L['order_invoice_timeout']);
			$money = $O['total'];
			$S = userinfo($O['seller']);
			if(!$S['invoice']) message($L['order_invoice_empty']);
			$TYPES = explode(',', $S['invoice']);			
			$invoice_status = $O['invoice'];
			if($invoice_status) $submit = 0;
			if($submit) {
				$post = array_map("trim", $post);
				if(strlen($post['type']) < 4) message($L['order_invoice_type']);
				if(strlen($post['company']) < 2) message($L['order_invoice_company']);
				$post['taxid'] = strtoupper(strip_sql($post['taxid'], 0));
				if(!preg_match("/^[0-9A-Z]{12,21}$/", $post['taxid'])) message($L['order_invoice_taxid']);
				if(strpos($post['type'], $L['order_invoice_sign']) !== false) {					
					if(strlen($post['address']) < 10) message($L['order_invoice_address']);
					if(strlen($post['telephone']) < 7) message($L['order_invoice_telephone']);
					if(strlen($post['bank']) < 4) message($L['order_invoice_bank']);
					if(strlen($post['account']) < 10) message($L['order_invoice_account']);
				}
				$temp = dhtmlspecialchars($post);
				$post = array();
				$post['oid'] = $itemid;
				$post['amount'] = $money;
				$post['buyer_email'] = $_email;
				$post['addtime'] = DT_TIME;
				foreach(array('mid', 'title', 'buyer', 'seller', 'buyer_name', 'buyer_address', 'buyer_postcode', 'buyer_mobile') as $v) {
					$post[$v] = addslashes($O[$v]);
				}
				foreach(array('type', 'company', 'taxid', 'address', 'telephone', 'bank', 'account') as $v) {
					$post[$v] = $temp[$v];
				}
				$db->query("DELETE FROM {$DT_PRE}invoice WHERE oid=$itemid AND mid=$mid");
				$db->query("INSERT INTO {$DT_PRE}invoice ".arr2sql($post, 0));
				$db->query("UPDATE {$table} SET invoice=1 WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['order_invoice_apply']."','')");
						$touser = $O['seller'];
				$title = lang($L['trade_message_t19'], array($itemid));
				$url = $memberurl.'trade'.DT_EXT.'?itemid='.$itemid;
				$content = lang($L['trade_message_c19'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['seller'], $title, $content);

				if($DT['sms'] && $_sms && isset($sendsms)) {
					$S or $S = userinfo($O['seller']);
					if(is_mobile($S['mobile'])) {
						$sms_num = sms_send($S['mobile'], lang($L['trade_message_s19'], array($itemid)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['order_invoice_apply'], 'ID:'.$itemid);
					}
				}
			
				dmsg($L['order_invoice_success'], '?action=invoice');
			} else {
				if($invoice_status) {
					$I = $db->get_one("SELECT * FROM {$DT_PRE}invoice WHERE oid=$itemid AND mid=$mid");
					$auth = encrypt('invoice|'.$I['send_type'].'|'.$I['send_no'].'|'.$I['send_status'].'|'.$I['buyer_mobile'].'|'.$I['itemid'], DT_KEY.'EXPRESS');
				} else {
					$B = userinfo($O['buyer']);
					$type = '';
					$company = $B['company'];
					$taxid = $B['taxid'];
					$address = $B['address'];
					$telephone = $B['telephone'];
					$bank = $B['branch'];
					$account = $B['account'];
					$T = $db->get_one("SELECT * FROM {$DT_PRE}invoice WHERE buyer='$_username' ORDER BY itemid DESC");
					if($T) {//历史记录
						$type = $T['type'];
						if($T['company']) $company = $T['company'];
						if($T['taxid']) $taxid = $T['taxid'];
						if($T['address']) $address = $T['address'];
						if($T['telephone']) $telephone = $T['telephone'];
						if($T['bank']) $bank = $T['bank'];
						if($T['account']) $account = $T['account'];
					}
				}
				$head_title = $L['order_invoice_title'];
			}
		break;
		case 'contract'://合同详情
			$itemid or message();
			$C = $db->get_one("SELECT * FROM {$DT_PRE}contract WHERE oid=$itemid AND mid=$mid");
			$C or message($L['order_contract_item']);
			if($C['buyer'] != $_username) message();
			if($submit && $C['status'] == 1) {
				is_url($fileurl) or message($L['trade_contract_upload']);
				in_array(file_ext($fileurl), array('pdf', 'jpg', 'jpeg', 'png')) or message($L['trade_contract_format']);
				$db->query("UPDATE {$DT_PRE}contract SET buyer_contract='$fileurl',buyer_time='$DT_TIME',status=2 WHERE itemid=$C[itemid]");
				$db->query("UPDATE {$table} SET contract=2 WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['order_contract_sign']."','')");
				clear_upload($fileurl);
		
				$touser = $O['seller'];
				$title = lang($L['trade_message_t20'], array($itemid));
				$url = $memberurl.'trade.php?itemid='.$itemid;
				$content = lang($L['trade_message_c20'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['seller'], $title, $content);
				
				if($DT['sms'] && $_sms && isset($sendsms)) {
					$S or $S = userinfo($O['seller']);
					if(is_mobile($S['mobile'])) {
						$sms_num = sms_send($S['mobile'], lang($L['trade_message_s20'], array($itemid)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['order_contract_sign'], 'ID:'.$itemid);
					}
				}

				dmsg($L['order_contract_success'], '?action='.$action.'&step='.$step.'&itemid='.$itemid.'&reload='.$DT_TIME);
			}
			$cstatus = $L['contract_status'];
			$head_title = $L['order_contract_title'];
		break;
		case 'refund'://申请售后
			if($O['cod']) message($L['order_refund_cod']);
			if($O['bill']) message($L['order_refund_bill']);
			$pass = 0;
			foreach($lists as $k=>$v) {
				if(in_array($v['status'], array(2, 3, 4))) {$pass = 1; break;}
			}
			if(!$pass) message($L['trade_msg_deny']);
			$money = $O['total'];
			$gone = $DT_TIME - $O['updatetime'];
			$type = 0;
			if($O['status'] == 2) {//待发货
				unset($dservice[1], $dservice[2], $dservice[3]);
			} else if($O['status'] == 3) {//已发货
				$type = 1;
			} else if($O['status'] == 4) {//已完成
				if($gone > 7*86400) unset($dservice[0], $dservice[1]);//7天后不能退款
				if($gone > 30*86400) unset($dservice[2]);//30天后不能换货
				$type = 3;
			}

			if($submit) {
				(is_array($itemids) && count($itemids) > 0) or message($L['order_refund_goods']);
				$typeid = intval($post['typeid']);
				isset($dservice[$typeid]) or message($L['order_refund_type']);
				$buyer_title = dhtmlspecialchars(trim($post['reason'][$typeid]));
				$buyer_title or message($L['order_refund_reason']);
				$buyer_reason = dhtmlspecialchars(trim($post['content']));
				$buyer_thumbs = $uploads = '';
				foreach($post['thumbs'] as $v) {
					if(is_url($v)) $buyer_thumbs .= '|'.$v;
				}
				if($buyer_thumbs) {
					$buyer_thumbs = substr($buyer_thumbs, 1);
					$uploads .= $buyer_thumbs;
				}
				$buyer_video = is_url($post['video']) ? $post['video'] : '';
				if($buyer_video) $uploads .= $buyer_video;
				if($typeid < 2 && count($lists) > 1) {//对退款的子订单进行拆分
					$S = array();//按ID保存分离订单详情
					$all = isset($itemids[$itemid]) ? 1 : 0;
					foreach($lists as $k=>$v) {
						$oid = $v['itemid'];
						if($all) {//主订单退款，拆分所有子订单
							$S[$oid] = $v;
						} else {//拆分选中订单
							if(isset($itemids[$oid])) $S[$oid] = $v;
						}
					}
					if($S) {
						foreach($S as $k=>$v) {
							$new_id = $k;
							$new_amount = $v['price']*$v['number'];
							$new_discount = dround($O['discount']*$new_amount/$O['money']);//按金额比例计算新订单的优惠
							$new_fee = dround($O['fee']*$new_amount/$O['money']);//按金额比例计算新订单的附加费用
							$new_fee_name = addslashes($O['fee_name']);
							$tmp_amount = $new_amount - $new_discount;
							$db->query("UPDATE {$table} SET pid=0,amount=$tmp_amount,discount=$new_discount,fee=$new_fee,fee_name='$new_fee_name' WHERE itemid=$k");
							if($all) {
								//
							} else {
								$db->query("UPDATE {$table} SET amount=amount-$tmp_amount,discount=discount-$new_discount,fee=fee-$new_fee WHERE itemid=$itemid");
							}
							if($k == $itemid) {								
								$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$new_id','$DT_TIME','".$L['trade_split_from']."','')");
							} else {//复制和新增订单记录
								$result = $db->query("SELECT * FROM {$table_log} WHERE oid=$itemid ORDER BY itemid ASC");
								while($r = $db->fetch_array($result)) {
									$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$new_id','$r[addtime]','".addslashes($r['title'])."','".addslashes($r['note'])."')");
								}
								$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$new_id','$DT_TIME','".$L['trade_split_from']."','".$L['trade_split_oid'].$itemid."')");
								$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','".$L['trade_split_from']."','".$L['trade_split_nid'].$new_id."')");
							}
						}
					}
				}
				$title = $L['log_refund'][$typeid];
				foreach($lists as $kk=>$vv) {
					$oid = $vv['itemid'];
					if(!isset($itemids[$oid]) || !in_array($vv['status'], array(2, 3, 4))) continue;
					$post = array();
					$post['oid'] = $oid;
					$post['number'] = isset($numbers[$oid]) ? intval($numbers[$oid]) : $vv['number'];
					if($post['number'] < 1 || $post['number'] > $vv['number']) $post['number'] = $vv['number'];
					//计算实付金额
					$post['amount'] = dround($vv['price']*$vv['number']/$O['money']*$O['total']/$vv['number']*$post['number']);
					$post['addtime'] = $post['updatetime'] = DT_TIME;
					$post['pid'] = $typeid < 2 ? 0 : $vv['pid'];
					foreach(array('mid', 'mallid', 'title', 'thumb', 'skuid', 'price','buyer', 'seller', 'note') as $v) {
						$post[$v] = addslashes($vv[$v]);
					}
					$post['oss'] = $vv['status'];
					foreach(array('typeid', 'buyer_title','buyer_reason', 'buyer_thumbs', 'buyer_video') as $v) {
						$post[$v] = $$v;
					}
					$db->query("INSERT INTO {$table_service} ".arr2sql($post, 0));
					if($typeid < 2) {
						$db->query("UPDATE {$table} SET status=5,updatetime=$DT_TIME WHERE itemid=$oid");
						$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','$title','')");
					}
				}
				if($typeid > 1) $db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$title','')");
				clear_upload($uploads, $itemid);		
				$touser = $O['seller'];
				$title = lang($L['trade_message_t21'], array($oid, $dservice[$typeid]));
				$url = $memberurl.'trade.php?action=service&oid='.$oid;
				$content = lang($L['trade_message_c21'], array($myurl, $_username, $timenow, $url, $dservice[$typeid]));
				$content = ob_template('messager', 'mail');
				send_message($O['seller'], $title, $content);
				
				if($DT['sms'] && $_sms && isset($sendsms)) {
					$S or $S = userinfo($O['seller']);
					if(is_mobile($S['mobile'])) {
						$sms_num = sms_send($S['mobile'], lang($L['trade_message_s21'], array($itemid, $dservice[$typeid])));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['order_refund_apply'], 'ID:'.$itemid);
					}
				}

				message($L['trade_refund_success'], '?action=service', 3);
			} else {
				$thumbs = array();
				$video = '';
				$head_title = $L['trade_refund_title'];
			}
		break;
		case 'remind'://买家提醒卖家发货			
			if($O['status'] != 2) message($L['trade_msg_deny']);
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_remind]','')");
		break;
		case 'receive_goods'://确认收货
			$gone = $DT_TIME - $O['updatetime'];
			if($O['status'] != 3 || $gone > ($MOD['trade_day']*86400 + $O['add_time']*3600)) message($L['trade_msg_deny']);
			//交易成功
			$money = $O['total'];
			money_add($O['seller'], $money);
			money_record($O['seller'], $money, $L['in_site'], 'system', $L['trade_record_pay'], $L['trade_order_id'].$itemid);
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
				$db->query("UPDATE {$table} SET status=4,updatetime=$DT_TIME,inviter='$inviter' WHERE itemid=$v[itemid]");
			}
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_get]','')");
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_success]','')");

			$touser = $O['seller'];
			$title = lang($L['trade_message_t4'], array($itemid));
			$url = $memberurl.'trade'.DT_EXT.'?itemid='.$itemid;
			$content = lang($L['trade_message_c4'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($O['seller'], $title, $content);

			message($L['trade_success'], $forward, 3);
		break;
		case 'comment'://交易评价
			if($MODULE[$O['mid']]['module'] != 'mall') message($L['trade_msg_deny_comment']);
			if($submit) {
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_buyer_coment]','')");
				$uploads = '';
				foreach($lists as $k=>$v) {
					$mid = $v['mid'];
					$itemid = $v['itemid'];
					$mallid = $v['mallid'];
					$star = intval($stars[$itemid]);
					in_array($star, array(1, 2, 3, 4, 5)) or $star = 5;
					$star_express = intval($stars_express[$itemid]);
					in_array($star_express, array(1, 2, 3, 4, 5)) or $star_express = 5;
					$star_service = intval($stars_service[$itemid]);
					in_array($star_service, array(1, 2, 3, 4, 5)) or $star_service = 5;
					$thumb = '';
					foreach($thumbs[$itemid] as $v) {
						if(is_url($v)) $thumb .= '|'.$v;
					}
					if($thumb) {
						$thumb = substr($thumb, 1);
						$uploads .= $thumb;
					}
					$video = is_url($videos[$itemid]) ? $videos[$itemid] : '';
					if($video) $uploads .= $video;
					$hidden = isset($hiddens[$itemid]) ? 1 : 0;
					$content = dhtmlspecialchars(banword($BANWORD, $contents[$itemid], false));
					$db->query("UPDATE ".get_table($mid)." SET comments=comments+1 WHERE itemid=$mallid");
					$db->query("UPDATE {$table} SET seller_star=$star WHERE itemid=$itemid");
					$s = 's'.$star;
					$db->query("UPDATE {$DT_PRE}mall_comment_".$mid." SET seller_star=$star,seller_star_express=$star_express,seller_star_service=$star_service,seller_comment='$content',seller_thumbs='$thumb',seller_video='$video',seller_ctime=$DT_TIME,buyer_hidden=$hidden WHERE itemid=$itemid");
					$db->query("UPDATE {$DT_PRE}mall_stat_".$mid." SET scomment=scomment+1,`$s`=`$s`+1 WHERE mallid=$mallid");
				}
				clear_upload($uploads, $itemid);
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
					if($C[$oid]['seller_reply']) message($L['trade_msg_explain_again']);
					$db->query("UPDATE {$DT_PRE}mall_comment_".$O[$oid]['mid']." SET seller_reply='$content',seller_rtime=$DT_TIME WHERE itemid=$oid");
					dmsg($L['trade_msg_explain_success'], '?action='.$action.'&step='.$step.'&itemid='.$itemid);
				}
			}
			$head_title = $L['trade_comment_show_title'];
		break;
		case 'close'://关闭交易
			if($O['status'] == 0) {
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=8,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_buyer_close]','')");
				dmsg($L['trade_close_success'], $forward);
			} else if($O['status'] == 1) {
				foreach($lists as $k=>$v) {
					$db->query("UPDATE {$table} SET status=8,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
				}
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_buyer_close]','')");
				dmsg($L['trade_close_success'], $forward);
			} else if($O['status'] == 9) {
				foreach($lists as $k=>$v) {
					$db->query("DELETE FROM {$table} WHERE itemid=$v[itemid]");
					if($MODULE[$v['mid']]['module'] == 'mall') $db->query("DELETE FROM {$DT_PRE}mall_comment_{$v[mid]} WHERE itemid=$v[itemid]");
				}
				$db->query("DELETE FROM {$table_log} WHERE oid=$itemid");
				dmsg($L['trade_delete_success'], $forward);
			} else {
				message($L['trade_msg_deny']);
			}
		break;
		default://订单详情
			$logs = array();
			$result = $db->query("SELECT * FROM {$table_log} WHERE oid=$itemid ORDER BY itemid DESC");
			while($r = $db->fetch_array($result)) {
				$r['adddate'] = timetodate($r['addtime'], 5);
				$logs[] = $r;
			}
			$S = userinfo($O['seller']);
			$auth = encrypt('mall|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');
			$step = 'detail';
			$head_title = $L['trade_detail_title'];
		break;
	}
} else if($action == 'process') {//售后流程
	$itemid or message();
	$O = $db->get_one("SELECT * FROM {$table_service} WHERE itemid=$itemid");
	$O or message($L['process_item']);
	if($O['buyer'] != $_username) message($L['trade_msg_deny']);
	$O['adddate'] = timetodate($O['addtime'], 5);
	$O['updatedate'] = timetodate($O['updatetime'], 5);
	$O['linkurl'] = gourl('?mid='.$O['mid'].'&itemid='.$O['mallid']);
	$O['par'] = '';
	if(strpos($O['note'], '|') !== false) list($O['note'], $O['par']) = explode('|', $O['note']);
	switch($step) {
		case 'close'://撤销申请
			$O['status'] == 0 or message($L['process_close_status']);
			$db->query("UPDATE {$table_service} SET status=1,updatetime=$DT_TIME WHERE itemid=$itemid");
			$db->query("UPDATE {$table} SET status=$O[oss] WHERE itemid=$O[oid]");
			dmsg($L['process_close_success'], $forward);
		break;
		case 'send'://寄回商品
			if($O['status'] != 3) message($L['trade_msg_deny']);
			if($submit) {
				is_time($send_time) or message($L['process_send_time']);
				$status = 4;
				$oid = $O['pid'] ? $O['pid'] : $O['oid'];
				$buyer_name = $buyer_address = $buyer_postcode = $buyer_mobile = '';
				$aid = isset($aid) ? intval($aid) : 0;
				if($aid > 0) {
					$addr = get_address($_username, $aid);
					if($addr) {
						$buyer_name = addslashes($addr['truename']);
						$buyer_address = addslashes(($addr['areaid'] ? area_pos($addr['areaid'], '') : '').$addr['address']);
						$buyer_postcode = $addr['postcode'];
						$buyer_mobile = $addr['mobile'];
					} else {
						message($L['process_send_addr']);
					}
				} else {
					$addr = $db->get_one("SELECT buyer_name,buyer_address,buyer_postcode,buyer_mobile FROM {$table} WHERE itemid=$oid");
					if($addr) {						
						$buyer_name = addslashes($addr['buyer_name']);
						$buyer_address = addslashes($addr['buyer_address']);
						$buyer_postcode = addslashes($addr['buyer_postcode']);
						$buyer_mobile = addslashes($addr['buyer_mobile']);
					} else {
						message($L['process_send_aid']);
					}
				}
				$seller_send_time = datetotime($send_time);
				$seller_send_type = dhtmlspecialchars(trim($send_type));
				$seller_send_no = dhtmlspecialchars(trim($send_no));			
				$db->query("UPDATE {$table_service} SET status=$status,updatetime=$DT_TIME,buyer_name='$buyer_name',buyer_address='$buyer_address',buyer_postcode='$buyer_postcode',buyer_mobile='$buyer_mobile',seller_send_time='$seller_send_time',seller_send_type='$seller_send_type',seller_send_no='$seller_send_no' WHERE itemid=$itemid");
				$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$oid','$DT_TIME','".$L['process_buyer_send']."','')");
		
				$touser = $O['seller'];
				$title = lang($L['trade_message_t22'], array($oid));
				$url = $memberurl.'trade.php?action=service&oid='.$oid;
				$content = lang($L['trade_message_c22'], array($myurl, $_username, $timenow, $url));
				$content = ob_template('messager', 'mail');
				send_message($O['seller'], $title, $content);
				
				if($DT['sms'] && $_sms && isset($sendsms)) {
					$S or $S = userinfo($O['seller']);
					if(is_mobile($S['mobile'])) {
						$sms_num = sms_send($S['mobile'], lang($L['trade_message_s22'], array($itemid)));
						if($sms_num > 0) sms_add($_username, -$sms_num);
						if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['process_buyer_back'], 'ID:'.$itemid);
					}
				}

				dmsg($L['op_success'], '?action=process&step=service&itemid='.$itemid);
			}
			$send_types = explode('|', trim($MOD['send_types']));
			$send_time = timetodate($DT_TIME, 6);
			$address = get_address($_username);
			$O['buyer_thumbs'] = $O['buyer_thumbs'] ? explode('|', $O['buyer_thumbs']) : array();
			$O['seller_thumbs'] = $O['seller_thumbs'] ? explode('|', $O['seller_thumbs']) : array();
			$head_title = $L['process_send_title'];
		break;
		case 'finish'://完成售后
			if($O['status'] != 6) message($L['trade_msg_deny']);
			$db->query("UPDATE {$table_service} SET status=7,updatetime=$DT_TIME WHERE itemid=$itemid");
			dmsg($L['op_success'], '?action=process&step=service&itemid='.$itemid);
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
} else if($action == 'muti') {//批量付款
	$auto = 0;	
	$auth = isset($auth) ? decrypt($auth, DT_KEY.'CG') : '';
	if($auth && substr($auth, 0, 7) == 'trades|') {				
		$auto = $submit = 1;
		$itemid = explode(',', substr($auth, 7));
	}
	if($submit) {
		($itemid && is_array($itemid)) or message($L['trade_msg_muti_choose']);
		$itemids = implode(',', $itemid);
		$condition = "pid=0 AND buyer='$_username' AND status=1 AND itemid IN ($itemids)";
		$tags = array();
		$money = 0;
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT 50");
		while($r = $db->fetch_array($result)) {
			$money += ($r['amount'] + $r['fee']);
			$tags[] = $r;
		}
		$money <= $_money or message($L['money_not_enough']);
		if($money <= $DT['quick_pay']) $auto = 1;
		if(!$auto) {
			is_payword($_username, $password) or message($L['error_payword']);
		}
		foreach($tags as $O) {
			$itemid = $O['itemid'];
			$mallid = $O['mallid'];
			$money = $O['amount'] + $O['fee'];
			$lists = get_orders($itemid);
			foreach($lists as $k=>$v) {
				if(!stock_check($v)) message($v['title'].$L['stock_less']);
			}
			money_add($_username, -$money);
			money_record($_username, -$money, $L['in_site'], 'system', $L['trade_pay_order_title'], $L['trade_order_id'].$itemid);
			foreach($lists as $k=>$v) {
				$db->query("UPDATE {$table} SET status=2,updatetime=$DT_TIME WHERE itemid=$v[itemid]");
			}
			$db->query("INSERT INTO {$table_log} (oid,addtime,title,note) VALUES ('$itemid','$DT_TIME','$L[log_pay]','')");
			$touser = $O['seller'];
			$title = lang($L['trade_message_t2'], array($itemid));
			$url = $memberurl.'trade'.DT_EXT.'?itemid='.$itemid;
			$content = lang($L['trade_message_c2'], array($myurl, $_username, $timenow, $url));
			$content = ob_template('messager', 'mail');
			send_message($O['seller'], $title, $content);			
			//send sms
			if($DT['sms'] && $_sms && isset($sendsms)) {
				$S = userinfo($O['seller']);
				if(is_mobile($S['mobile'])) {
					$sms_num = sms_send($S['mobile'], lang('sms->ord_pay', array($itemid, $money)));
					if($sms_num > 0) sms_add($_username, -$sms_num);
					if($sms_num > 0) sms_record($_username, -$sms_num, $_username, $L['trade_sms_pay'], 'ID:'.$itemid);
				}
			}
			//send sms
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
		}
		dmsg($L['trade_pay_order_success'], '?nav=2');
	} else {
		$ids = isset($ids) ? explode(',', $ids) : array();
		if($ids) $ids = array_map('intval', $ids);
		$condition = "pid=0 AND buyer='$_username' AND status=1";
		if($ids) $condition .= " AND itemid IN (".implode(',', $ids).")";
		$lists = $pids = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT 50");
		while($r = $db->fetch_array($result)) {
			if(!stock_check($r)) continue;
			if($r['amount'] > $r['price']*$r['number']) $pids[] = $r['itemid'];
			$r['addtime'] = timetodate($r['addtime'], 5);
			$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
			$r['par'] = '';
			if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
			$r['dstatus'] = $dstatus[$r['status']];
			$r['money'] = $r['amount'] + $r['fee'];
			$r['money'] = number_format($r['money'], 2, '.', '');
			$lists[$r['itemid']] = $r;
		}
		if($pids) {
			$result = $db->query("SELECT * FROM {$table} WHERE pid IN (".implode(',', $pids).") ORDER BY itemid DESC");
			while($r = $db->fetch_array($result)) {
				if(!stock_check($r)) {
					unset($lists[$r['pid']]);
					continue;
				}
				$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
				$r['par'] = '';
				if(strpos($r['note'], '|') !== false) list($r['note'], $r['par']) = explode('|', $r['note']);
				$tags[$r['pid']][] = $r;
			}
		}
		if(!$lists) {
			if($ids) dmsg($L['trade_pay_order_success'], '?nav=2');
			message($L['trade_msg_muti_empty'], '?nav=1', 5);
		}
		$itemids = '';
		foreach($lists as $k=>$v) {
			$itemids .= ','.$k;
		}
		if($itemids) $itemids = substr($itemids, 1);
		$head_title = $L['trade_muti_title'];
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
	$condition = "pid=0 AND send_no<>'' AND buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($status !== '') $condition .= " AND send_status='$status'";
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);	
	$lists = $pids = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		if($r['amount'] > $r['price']*$r['number']) $pids[] = $r['itemid'];
		$r['addtime'] = timetodate($r['addtime'], 5);
		$r['updatetime'] = timetodate($r['updatetime'], 5);
		$r['linkurl'] = gourl('?mid='.$r['mid'].'&itemid='.$r['mallid']);
		$r['dstatus'] = $dsend_status[$r['send_status']];
		$lists[] = $r;
	}
	$head_title = $L['express_title'];
} else if($action == 'invoice') {//我的发票	
	$sfields = $L['invoice_order_sfields'];
	$dfields = array('company', 'company', 'type', 'taxid', 'amount', 'title', 'seller');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$itemid or $itemid = '';
	$status = isset($status) ? intval($status) : 0;
	(isset($seller) && check_name($seller)) or $seller = '';
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'updatetime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$condition = "buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($itemid) $condition .= " AND itemid=$itemid";
	if($seller) $condition .= " AND seller='$seller'";
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
	$sfields = $L['contract_order_sfields'];
	$dfields = array('title', 'title', 'amount', 'seller_company', 'seller');
	$dstatus = $L['contract_status'];
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	$nav = isset($nav) ? intval($nav) : -1;
	(isset($seller) && check_name($seller)) or $seller = '';
	isset($datetype) && in_array($datetype, array('addtime', 'seller_time', 'buyer_time')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$itemid or $itemid = '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($seller) $condition .= " AND seller='$seller'";
	if($status !== '') $condition .= " AND status=$status";
	if($itemid) $condition .= " AND itemid=$itemid";
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
	(isset($seller) && check_name($seller)) or $seller = '';
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
	$condition = "buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($seller) $condition .= " AND seller='$seller'";
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
	$sfields = $L['trade_order_sfields'];
	$dfields = array('title', 'title ', 'amount', 'fee', 'fee_name', 'seller', 'send_type', 'send_no', 'note');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$mallid = isset($mallid) ? intval($mallid) : 0;
	$mallid or $mallid = '';
	$itemid or $itemid = '';
	$cod = isset($cod) ? intval($cod) : 0;
	$nav = isset($nav) ? intval($nav) : -1;
	(isset($seller) && check_name($seller)) or $seller = '';
	isset($datetype) && in_array($datetype, array('addtime', 'updatetime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$status = isset($status) && isset($dstatus[$status]) ? intval($status) : '';
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$status_select = dselect($dstatus, 'status', $L['status'], $status, '', 1, '', 1);
	$condition = "buyer='$_username'";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($status !== '') $condition .= " AND status=$status";
	if($itemid) $condition .= " AND itemid='$itemid'";
	if($mallid) $condition .= " AND mallid=$mallid";
	if($seller) $condition .= " AND seller='$seller'";
	if($cod) $condition .= " AND cod=1";
	if(in_array($nav, array(0,1,2,3,4,5,6))) {
		$condition .= " AND status=$nav";
		$status = $nav;
	} else if($nav == 7) {
		$condition .= " AND status=4 AND seller_star=0";
		$status = $nav;
	}
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = $DT_PC ? pages($items, $page, $pagesize) : mobile_pages($items, $page, $pagesize);
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
	$head_title = $L['trade_order_title'];
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('order', $module);
?>