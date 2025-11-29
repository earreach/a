<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$mod_limit = intval($MOD['limit_'.$_groupid]);
$mod_free_limit = intval($MOD['free_limit_'.$_groupid]);
if($mod_limit < 0)  dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
$MTYPE = get_type($module.'-'.$moduleid.'-'.$_userid);
require DT_ROOT.'/include/post.func.php';
include load($module.'.lang');
include load('my.lang');
require DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
$do = new $module($moduleid);
if(in_array($action, array('add', 'edit'))) {
	$FD = cache_read('fields-'.substr($table, strlen($DT_PRE)).'.php');
	if($FD) require DT_ROOT.'/include/fields.func.php';
	isset($post_fields) or $post_fields = array();
	$CP = $MOD['cat_property'];
	if($CP) require DT_ROOT.'/include/property.func.php';
	isset($post_ppt) or $post_ppt = array();
}
$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
$limit_used = $limit_free = $need_password = $need_captcha = $need_question = $fee_add = 0;
if(in_array($action, array('', 'add'))) {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $sql AND status>1");
	$limit_used = $r['num'];
	$limit_free = $mod_limit > $limit_used ? $mod_limit - $limit_used : 0;
}
if(check_group($_groupid, $MOD['group_refresh'])) $MOD['credit_refresh'] = 0;
switch($action) {
	case 'add':
		if($mod_limit && $limit_used >= $mod_limit) dalert(lang($L['info_limit'], array($mod_limit, $limit_used)), $_userid ? '?mid='.$mid : '?action=index');
		if($MG['hour_limit']) {
			$today = $DT_TIME - 3600;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $sql AND addtime>$today");
			if($r && $r['num'] >= $MG['hour_limit']) dalert(lang($L['hour_limit'], array($MG['hour_limit'])), $_userid ? '?mid='.$mid : '?action=index');
		}
		if($MG['day_limit']) {
			$today = $DT_TODAY - 86400;
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $sql AND addtime>$today");
			if($r && $r['num'] >= $MG['day_limit']) dalert(lang($L['day_limit'], array($MG['day_limit'])), $_userid ? '?mid='.$mid : '?action=index');
		}

		if($mod_free_limit >= 0) {
			$fee_add = ($MOD['fee_add'] && (!$MOD['fee_mode'] || !$MG['fee_mode']) && $limit_used >= $mod_free_limit && $_userid) ? dround($MOD['fee_add']) : 0;
		} else {
			$fee_add = 0;
		}
		$fee_currency = $MOD['fee_currency'];
		$fee_unit = $fee_currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
		$need_password = $fee_add && $fee_currency == 'money' && $fee_add > $DT['quick_pay'];
		$need_captcha = $MOD['captcha_add'] == 2 ? $MG['captcha'] : $MOD['captcha_add'];
		$need_question = $MOD['question_add'] == 2 ? $MG['question'] : $MOD['question_add'];
		$could_elite = check_group($_groupid, $MOD['group_elite']) && $MOD['credit_elite'] && $_userid;
		$could_color = check_group($_groupid, $MOD['group_color']) && $MOD['credit_color'] && $_userid;

		if($submit) {
			$credit = 0;
			if($fee_add && $fee_currency == 'credit') $credit += $fee_add;
			if($could_color && $color) $credit += $MOD['credit_color'];
			if($could_elite && isset($elite)) $credit += $MOD['credit_elite'];
			if($credit > 0 && $credit > $_credit) dalert($L['credit_lack']);
			if($fee_add && $fee_currency == 'money' && $fee_add > $_money) dalert($L['balance_lack']);
			if($need_password && !is_payword($_username, $password)) dalert($L['error_payword']);

			if(!$_userid) {
				if(strlen($post['company']) < 4) dalert($L['type_company']);
				if($AREA) {
					if(!isset($AREA[$post['areaid']])) dalert($L['type_area']);
				} else {
					if(!$post['areaid']) dalert($L['type_area']);
				}
				if(strlen($post['truename']) < 4) dalert($L['type_truename']);
				if(strlen($post['mobile']) < 7) dalert($L['type_mobile']);
			}

			if($MG['add_limit']) {
				$last = $db->get_one("SELECT addtime FROM {$table} WHERE $sql ORDER BY itemid DESC");
				if($last && $DT_TIME - $last['addtime'] < $MG['add_limit']) dalert(lang($L['add_limit'], array($MG['add_limit'])));
			}
			$msg = captcha($captcha, $need_captcha, true);
			if($msg) dalert($msg);
			$msg = question($answer, $need_question, true);
			if($msg) dalert($msg);

			$post['username'] = $_username;
			if($post['mode'] == 3 && !$DT_PC) $post = $do->muti_fix($post);
			if($do->pass($post)) {
				$CAT = get_cat($post['catid']);
				if(!$CAT || !check_group($_groupid, $CAT['group_add'])) dalert(lang($L['group_add'], array($CAT['catname'])));
				$post['addtime'] = $post['level'] = $post['fee'] = 0;
				$post['style'] = $post['template'] = $post['note'] = $post['filepath'] = '';
				$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_add'];
				$post['status'] = get_status(3, $need_check);
				$post['hits'] = 0;
				if($FD) fields_check($post_fields);
				if($CP) property_check($post_ppt);
				if($could_elite && isset($elite)) {
					$post['level'] = 1;
					credit_add($_username, -$MOD['credit_elite']);
					credit_record($_username, -$MOD['credit_elite'], 'system', lang($L['credit_record_elite'], array($MOD['name'])), $post['title']);
				}
				if($could_color && $color) {
					$post['style'] = $color;
					credit_add($_username, -$MOD['credit_color']);
					credit_record($_username, -$MOD['credit_color'], 'system', $L['title_color'], '['.$MOD['name'].']'.$post['title']);
				}
				$do->add($post);
				if($FD) fields_update($post_fields, $table, $do->itemid);
				if($CP) property_update($post_ppt, $moduleid, $post['catid'], $do->itemid);
				if($MOD['show_html'] && $post['status'] > 2) $do->tohtml($do->itemid);
				if($fee_add) {
					if($fee_currency == 'money') {
						money_add($_username, -$fee_add);
						money_record($_username, -$fee_add, $L['in_site'], 'system', lang($L['credit_record_add'], array($MOD['name'])), 'ID:'.$do->itemid);
					} else {
						credit_add($_username, -$fee_add);
						credit_record($_username, -$fee_add, 'system', lang($L['credit_record_add'], array($MOD['name'])), 'ID:'.$do->itemid);
					}
				}
				
				$msg = $post['status'] == 2 ? $L['success_check'] : $L['success_add'];
				$js = '';
				if(isset($post['sync_sina']) && $post['sync_sina']) $js .= sync_weibo('sina', $moduleid, $do->itemid);
				if(isset($post['sync_moment']) && $post['sync_moment']) $js .= sync_weibo('moment', $moduleid, $do->itemid);
				if($_userid) {
					set_cookie('dmsg', $msg);
					$forward = '?mid='.$mid.'&status='.$post['status'];
					$msg = '';
				} else {
					$forward = '?mid='.$mid.'&action=add';
				}
				$js .= 'window.onload=function(){parent.window.location="'.$forward.'";}';
				dalert($msg, '', $js);
			} else {
				dalert($do->errmsg, '', ($need_captcha ? reload_captcha() : '').($need_question ? reload_question() : ''));
			}
		} else {
			if($itemid) {
				$do->itemid = $itemid;
				if($job == 'copy') {
					$item = $do->get_one();
					if(!$item || !$item['username'] || $item['status'] != 3 || !$_username || !agented($_username, $item['username'])) message();
				} else {
					$MG['copy'] && $_userid or dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
					$item = $do->get_one();
					if(!$item || $item['username'] != $_username) message();
				}
				extract($item);
				$sfromtime = $sfromtime ? timetodate($sfromtime) : '';
				$stotime = $stotime ? timetodate($stotime) : '';
				$thumbs = get_thumbs($item);
				if($step) {
					extract(unserialize($step), EXTR_SKIP);
					$a2 > 0 or $a2 = '';
					$a3 > 0 or $a3 = '';
					$p2 > 0 or $p2 = '';
					$p3 > 0 or $p3 = '';
					$mode = 2;
				} else {
					$a1 = 1;
					$p1 = $item['price'];
					$a2 = $a3 = $p2 = $p3 = '';
					$mode = $prices ? 1 : 0;
				}
				$stocks = '';
				if($stock) {
					$stocks = json_encode(get_stocks($stock));
					$mode = 3;
				}
			} else {
				$_catid = $catid;
				foreach($do->fields as $v) {
					$$v = '';
				}
				$a1 = 1;
				$minamount = 1;
				$days = 1;
				$a2 = $a3 = $p1 = $p2 = $p3 = '';
				$boc = 1;
				$content = '';
				$catid = $_catid;
				$mycatid = 0;
				$stocks = '';
				$mode = 0;
			}
			$item = $thumbs = array();
			$mycatid_select = type_select($module.'-'.$moduleid.'-'.$_userid, 0, 'post[mycatid]', $L['type_default'], $mycatid);
		}
	break;
	case 'edit':
		$itemid or message();
		$do->itemid = $itemid;
		$item = $do->get_one();
		if(!$item || $item['username'] != $_username) message();

		if($MG['edit_limit'] < 0) message($L['edit_refuse']);
		if($MG['edit_limit'] && $DT_TIME - $item['addtime'] > $MG['edit_limit']*86400) message(lang($L['edit_limit'], array($MG['edit_limit'])));

		if($submit) {
			$post['username'] = $_username;
			if($post['mode'] == 3 && !$DT_PC) $post = $do->muti_fix($post);
			if($do->pass($post)) {
				$CAT = get_cat($post['catid']);
				if(!$CAT || !check_group($_groupid, $CAT['group_add'])) dalert(lang($L['group_add'], array($CAT['catname'])));
				$post['addtime'] = timetodate($item['addtime']);
				$post['level'] = $item['level'];
				$post['fee'] = $item['fee'];
				$post['style'] = addslashes($item['style']);
				$post['template'] = addslashes($item['template']);
				$post['filepath'] = addslashes($item['filepath']);
				$post['note'] = addslashes($item['note']);
				$need_check =  $MOD['check_add'] == 2 ? $MG['check'] : $MOD['check_add'];
				$post['status'] = get_status($item['status'], $need_check);
				$post['hits'] = $item['hits'];
				if($FD) fields_check($post_fields);
				if($CP) property_check($post_ppt);
				if($FD) fields_update($post_fields, $table, $do->itemid);
				if($CP) property_update($post_ppt, $moduleid, $post['catid'], $do->itemid);
				$do->edit($post);
				if($post['status'] < 3 && $item['status'] > 2) history($moduleid, $itemid, 'set', $item);
				set_cookie('dmsg', $post['status'] == 2 ? $L['success_edit_check'] : $L['success_edit']);
				dalert('', '', 'parent.window.location="'.($post['status'] == 2 ? '?mid='.$moduleid.'&status=2' : $forward).'"');
			} else {
				dalert($do->errmsg);
			}
		} else {
			extract($item);
			$sfromtime = $sfromtime ? timetodate($sfromtime) : '';
			$stotime = $stotime ? timetodate($stotime) : '';
			$thumbs = get_thumbs($item);
			if($step) {
				@extract(unserialize($step), EXTR_SKIP);
				$a2 > 0 or $a2 = '';
				$a3 > 0 or $a3 = '';
				$p2 > 0 or $p2 = '';
				$p3 > 0 or $p3 = '';
				$mode = 2;
			} else {
				$a1 = 1;
				$p1 = $item['price'];
				$a2 = $a3 = $p2 = $p3 = '';
				$mode = $prices ? 1 : 0;
			}
			$stocks = '';
			if($stock) {
				$stocks = json_encode(get_stocks($stock));
				$mode = 3;
			}
			$mycatid_select = type_select($module.'-'.$moduleid.'-'.$_userid, 0, 'post[mycatid]', $L['type_default'], $mycatid);
		}
	break;
	case 'delete':
		$MG['delete'] or message();
		$itemid or message();
		$itemids = is_array($itemid) ? $itemid : array($itemid);
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $db->get_one("SELECT username FROM {$table} WHERE itemid=$itemid");
			if(!$item || $item['username'] != $_username) message();
			$do->recycle($itemid);
		}
		dmsg($L['success_delete'], $forward);
	break;
	case 'refresh':
		$MG['refresh_limit'] > -1 or dheader(($DT_PC ? $MODULE[2]['linkurl'] : $MODULE[2]['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
		$itemid or message($L['select_goods']);
		if($MOD['credit_refresh'] && $_credit < $MOD['credit_refresh']) message($L['credit_lack']);
		$itemids = $itemid;
		$s = $f = 0;
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $db->get_one("SELECT username,edittime FROM {$table} WHERE itemid=$itemid");
			$could_refresh = $item && $item['username'] == $_username;
			if($could_refresh && $MG['refresh_limit'] && $DT_TIME - $item['edittime'] < $MG['refresh_limit']) $could_refresh = false;
			if($could_refresh && $MOD['credit_refresh'] && ($MOD['credit_refresh'] > $_credit || $_credit < 0)) $could_refresh = false;
			if($could_refresh) {
				$do->refresh($itemid);
				$s++;
				if($MOD['credit_refresh']) $_credit = $_credit - $MOD['credit_refresh'];
			} else {
				$f++;
			}			
		}
		if($MOD['credit_refresh'] && $s) {
			$credit = $s*$MOD['credit_refresh'];
			credit_add($_username, -$credit);
			credit_record($_username, -$credit, 'system', lang($L['credit_record_refresh'], array($MOD['name'])), lang($L['refresh_total'], array($s)));
		}
		$msg = lang($L['refresh_success'], array($s));
		if($f) $msg = $msg.' '.lang($L['refresh_fail'], array($f));
		dmsg($msg, $forward);
	break;
	case 'onsale':
		$itemid or message($L['select_goods']);
		$itemids = $itemid;
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $db->get_one("SELECT username FROM {$table} WHERE itemid=$itemid");
			if($item && $item['username'] == $_username) $do->onsale($itemid);		
		}
		dmsg($L['success_onsale'], $forward);
	break;
	case 'unsale':
		$itemid or message($L['select_goods']);
		$itemids = $itemid;
		foreach($itemids as $itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one(0);
			if($item && $item['username'] == $_username) $do->unsale($itemid);		
		}
		dmsg($L['success_unsale'], $forward);
	break;
	case 'relate_del':
		$itemid or message($L['select_goods']);
		$do->itemid = $itemid;
		$M = $do->get_one(0);
		($M && $M['status'] == 3 && $M['username'] == $_username) or message($L['select_goods']);
		isset($id) or $id = array();
		$ids = is_array($id) ? $id : array($id);
		$ids or msg($L['pass_relate_del']);
		foreach($ids as $id) {
			$do->itemid = intval($id);
			if($do->itemid < 1) continue;
			$A = $do->get_one(0);
			if(!$A || $A['username'] != $_username) continue;
			$do->relate_del($M, $A);
			$do->itemid = $itemid;
			$M = $do->get_one(0);
		}
		dmsg($L['success_remove'], '?mid='.$mid.'&itemid='.$itemid.'&action=relate');
	break;
	case 'relate_add':
		$relate_name = isset($relate_name) ? dhtmlspecialchars(trim($relate_name)) : '';
		$relate_name or message($L['mall_relate_name'] );
		$itemid or message($L['select_goods']);
		$do->itemid = $itemid;
		$M = $do->get_one(0);
		($M && $M['status'] == 3 && $M['username'] == $_username) or message($L['select_goods']);
		$id = isset($id) ? intval($id) : 0;
		$id or message($L['select_goods']);
		$do->itemid = $id;
		$A = $do->get_one(0);
		($A && $A['status'] == 3 && $A['username'] == $M['username']) or message($L['select_goods']);
		if($itemid != $id) $do->relate_add($M, $A, $relate_name);
		dmsg($L['success_add'], '?mid='.$mid.'&itemid='.$itemid.'&action=relate');
	break;
	case 'relate_update':
		$itemid or message($L['select_goods']);
		$do->itemid = $itemid;
		$M = $do->get_one(0);
		($M && $M['status'] == 3 && $M['username'] == $_username) or message($L['select_goods']);
		$relate_name = isset($relate_name) ? dhtmlspecialchars(trim($relate_name)) : '';
		$relate_name or message($L['mall_relate_name'] );
		$do->relate($M, $post, $relate_name);
		dmsg($L['success_update'], '?mid='.$mid.'&itemid='.$itemid.'&action=relate');
	break;
	case 'relate':
		$itemid or message($L['select_goods']);
		$do->itemid = $itemid;
		$M = $do->get_one(0);
		($M && $M['status'] == 3 && $M['username'] == $_username) or message($L['select_goods']);
		$lists = $do->relate_list($M);
	break;
	default:
		$pagesize = 8;
		$offset = ($page-1)*$pagesize;
		$sorder  = $L['mall_orderby'];
		$dorder  = array($MOD['order'], 'edittime DESC', 'edittime ASC', 'addtime DESC', 'addtime ASC', 'price DESC', 'price DESC', 'orders DESC', 'orders ASC', 'sales DESC', 'sales ASC', 'amount DESC', 'amount ASC', 'comments DESC', 'comments ASC', 'hits DESC', 'hits ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$status = isset($status) ? intval($status) : 3;
		in_array($status, array(1, 2, 3, 4)) or $status = 3;
		$mycatid = isset($mycatid) ? ($mycatid === '' ? -1 : intval($mycatid)) : -1;
		$minprice = isset($minprice) ? dround($minprice) : '';
		$minprice or $minprice = '';
		$maxprice = isset($maxprice) ? dround($maxprice) : '';
		$maxprice or $maxprice = '';
		$minorders = isset($minorders) ? intval($minorders) : '';
		$minorders or $minorders = '';
		$maxorders = isset($maxorders) ? intval($maxorders) : '';
		$maxorders or $maxorders = '';
		$minsales = isset($minsales) ? intval($minsales) : '';
		$minsales or $minsales = '';
		$maxsales = isset($maxsales) ? intval($maxsales) : '';
		$maxsales or $maxsales = '';
		$minamount = isset($minamount) ? intval($minamount) : '';
		$minamount or $minamount = '';
		$maxamount = isset($maxamount) ? intval($maxamount) : '';
		$maxamount or $maxamount = '';
		$mincomments = isset($mincomments) ? intval($mincomments) : '';
		$mincomments or $mincomments = '';
		$maxcomments = isset($maxcomments) ? intval($maxcomments) : '';
		$maxcomments or $maxcomments = '';
		$mode = isset($mode) ? intval($mode) : 0;
		$mycat_select = type_select($module.'-'.$moduleid.'-'.$_userid, 0, 'mycatid', $L['type_default'], $mycatid, '', $L['type_my']);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = "username='$_username' AND status=$status";
		if($keyword) $condition .= match_kw('keyword', $keyword);
		if($catid) $condition .= $CAT['child'] ? " AND catid IN (".$CAT['arrchildid'].")" : " AND catid=$catid";
		if($mycatid >= 0) $condition .= " AND mycatid IN (".type_child($mycatid, $MTYPE).")";
		if($minprice)  $condition .= " AND price>=$minprice";
		if($maxprice)  $condition .= " AND price<=$maxprice";
		if($minorders)  $condition .= " AND orders>=$minorders";
		if($maxorders)  $condition .= " AND orders<=$maxorders";
		if($minsales)  $condition .= " AND sales>=$minsales";
		if($maxsales)  $condition .= " AND sales<=$maxsales";
		if($minamount)  $condition .= " AND amount>=$minamount";
		if($maxamount)  $condition .= " AND amount<=$maxamount";
		if($mincomments)  $condition .= " AND comments>=$mincomments";
		if($maxcomments)  $condition .= " AND comments<=$maxcomments";
		if($mode == 1) $condition .= " AND prices<>''";
		if($mode == 2) $condition .= " AND step<>''";
		if($mode == 3) $condition .= " AND stock<>''";
		if($mode == 4) $condition .= " AND prices='' AND step='' AND stock=''";
		if($mode == 5) $condition .= " AND fprice>0";
		if($mode == 6) $condition .= " AND sprice>0";
		if($mode == 10) $condition .= " AND cod>0";
		if($mode == 11) $condition .= " AND relate_id<>''";
		if($mode == 12) $condition .= " AND elite>0";

		$timetype = strpos($MOD['order'], 'add') !== false ? 'add' : '';
		$lists = $do->get_list($condition, $dorder[$order]);
		foreach($lists as $k=>$v) {
			$lists[$k]['mycat'] = $v['mycatid'] && isset($MTYPE[$v['mycatid']]) ? set_style($MTYPE[$v['mycatid']]['typename'], $MTYPE[$v['mycatid']]['style']) : $L['type_default'];
		}
	break;
}
if($_userid) {
	$nums = array();
	for($i = 1; $i < 5; $i++) {
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE username='$_username' AND status=$i");
		$nums[$i] = $r['num'];
	}
}
$EXP = array();
if($_username && in_array($action, array('add', 'edit'))) {
	$result = $db->query("SELECT * FROM {$table_express} WHERE username='$_username' AND parentid=0 ORDER BY listorder ASC,itemid ASC LIMIT 100");
	while($r = $db->fetch_array($result)) {
		$EXP[] = $r;
	}
}
$show_limit = (($mod_limit || (!$MG['fee_mode'] && $MOD['fee_add'])) && isset($status) && $status == 3 && $page == 1 && !$kw) ? 1 : 0;
if($DT_PC) {
	if($EXT['mobile_enable']) $head_mobile = str_replace($MODULE[2]['linkurl'], $MODULE[2]['mobile'], $DT_URL);
} else {
	$foot = '';
	if($action == 'add' || $action == 'edit' || $action == 'relate') {
		//
	} else {
		$time = strpos($MOD['order'], 'add') !== false ? 'addtime' : 'edittime';
		foreach($lists as $k=>$v) {
			$lists[$k]['linkurl'] = str_replace($MOD['linkurl'], $MOD['mobile'], $v['linkurl']);
			$lists[$k]['date'] = timetodate($v[$time], 5);
		}
		$pages = mobile_pages($items, $page, $pagesize);
		if($kw) {
			$back_link = '?mid='.$mid.'&status='.$status;
		} else if($status == 3) {
			$back_link = $_cid ? 'child.php' : $DT['file_my'];
		}
	}
}
$head_title = lang($L['module_manage'], array($MOD['name']));
include template($MOD['template_my'] ? $MOD['template_my'] : 'my_'.$module, 'member');
?>