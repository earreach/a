<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
isset($MODULE[$mid]) or dheader($DT_PC ? DT_PATH : DT_MOB);
$itemid or dheader($DT_PC ? $MODULE[$mid]['linkurl'] : $MODULE[$mid]['mobile']);
in_array($mid, explode(',', $MOD['comment_module'])) or dheader($DT_PC ? $MODULE[$mid]['linkurl'] : $MODULE[$mid]['mobile']);
if(in_array($MODULE[$mid]['module'], array('mall', 'club'))) dheader($DT_PC ? DT_PATH : DT_MOB);
if(in_array($itemid, cache_read('bancomment-'.$mid.'.php'))) $action = 'close';
$level = 0;
if($mid == 4) {
	$item = $db->get_one("SELECT company,linkurl,username,groupid,comments FROM ".get_table($mid)." WHERE userid=$itemid");
	$item or dheader(DT_PATH);
	$item['groupid'] > 4 or dheader(DT_PATH);
	$item['title'] = $item['company'];
	$linkurl = $item['linkurl'];
} else if(in_array($MODULE[$mid]['module'], array('moment'))) {
	$item = $db->get_one("SELECT title,linkurl,username,status,comments,comment,open FROM ".get_table($mid)." WHERE itemid=$itemid");
	$item or dheader(DT_PATH);
	$item['status'] == 3 or dheader(DT_PATH);
	$item['open'] > 0 or dheader(DT_PATH);
	$linkurl = ($DT_PC ? $MODULE[$mid]['linkurl'] : $MODULE[$mid]['mobile']).$item['linkurl'];
	if($item['comment'] == 0) {
		$action = 'close';
	} else if($item['comment'] == 2) {
		$level = 1;
	}
} else {
	$item = $db->get_one("SELECT title,linkurl,username,status,comments FROM ".get_table($mid)." WHERE itemid=$itemid");
	$item or dheader(DT_PATH);
	$item['status'] > 2 or dheader(DT_PATH);
	$linkurl = ($DT_PC ? $MODULE[$mid]['linkurl'] : $MODULE[$mid]['mobile']).$item['linkurl'];
}
$ext = 'comment';
$table = $DT_PRE.$ext;
$url = $EXT[$ext.'_url'];
$mob = $EXT[$ext.'_mob'];
$template = $message = $forward = '';
$username = $item['username'];
$title = $item['title'];
$could_del = ($_groupid == 1 && $MOD['comment_admin_del']) ? true : false;
$iframe = 0;
switch($action) {
	case 'count':
		if($EXT['comment_api'] == 'changyan') {
			$comments = $dc->get('comments-'.$mid.'-'.$itemid);
			if(strlen($comments) > 0) {
				echo $comments;
				exit;
			}
			$rec = dcurl('http://changyan.sohu.com/api/2/topic/load?client_id='.$EXT['comment_api_id'].'&topic_source_id='.$mid.'-'.$itemid.'&topic_url='.urlencode($linkurl));
			if(strpos($rec, 'cmt_sum') !== false) {
				$arr = json_decode($rec, true);
				$comments = intval($arr['cmt_sum']);
			} else {
				$comments = 0;
			}
			$dc->set('comments-'.$mid.'-'.$itemid, $comments, $CFG['db_expires'] ? $CFG['db_expires'] : 1800);
		} else {
			$condition = "item_mid=$mid AND item_id=$itemid AND status=3";
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}", 'CACHE');
			$comments = $r['num'];
		}
		if($comments != $item['comments']) $db->query("UPDATE ".get_table($mid)." SET comments=$comments WHERE ".($mid == 4 ? 'userid' : 'itemid')."=$itemid", 'UNBUFFERED');
		echo $comments;
		exit;
	break;
	case 'delete':
		$could_del or dalert($L['comment_msg_del']);
		$cid = isset($cid) ? intval($cid) : 0;
		$cid or dalert($L['comment_msg_cid']);
		$db->query("UPDATE {$table} SET status=0,editor='$_username' WHERE itemid=$cid");
		$forward = rewrite('index'.DT_EXT.'?mid='.$mid.'&itemid='.$itemid.'&page='.$page.'&rand='.mt_rand(10, 99));
		dalert($L['comment_msg_del_success'], '', 'parent.window.location="'.$forward.'";');
	break;
	default:
		if($EXT['comment_api']) {
			//
		} else {
			if(check_group($_groupid, $MOD['comment_group'])) {
				$user_status = 3;
			} else {
				if($_userid) {
					$user_status = 1;
				} else {
					$user_status = 2;
				}
			}
			$need_captcha = $MOD['comment_captcha_add'] == 2 ? $MG['captcha'] : $MOD['comment_captcha_add'];
			if($MOD['comment_pagesize']) {
				$pagesize = $MOD['comment_pagesize'];
				$offset = ($page-1)*$pagesize;
			}
			if($submit) {
				if($user_status != 3) dalert($L['comment_msg_permission']);
				$sql = $_userid ? "username='$_username'" : "ip='$DT_IP'";
				if($MOD['comment_limit']) {
					$today = $DT_TODAY - 86400;
					$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE $sql AND addtime>$today");
					$r['num'] < $MOD['comment_limit'] or dalert(lang($L['comment_msg_limit'], array($MOD['comment_limit'], $r['num'])));
				}
				if($MOD['comment_time']) {
					$r = $db->get_one("SELECT addtime FROM {$table} WHERE $sql ORDER BY addtime DESC");
					if($r && $DT_TIME - $r['addtime'] < $MOD['comment_time']) dalert(lang($L['comment_msg_time'], array($MOD['comment_time'])));
				}
				if($need_captcha) {
					$msg = captcha($captcha, 1, true);
					if($msg) dalert($msg);
				}
				$content = dhtmlspecialchars(trim($content));
				$content = preg_replace("/&([a-z]{1,});/", '', $content);
				$len = word_count($content);
				if($len < $MOD['comment_min']) dalert(lang($L['comment_msg_min'], array($MOD['comment_min'])));
				if($len > $MOD['comment_max']) dalert(lang($L['comment_msg_max'], array($MOD['comment_max'])));
				$BANWORD = cache_read('banword.php');
				if($BANWORD) $content = banword($BANWORD, $content, false);
				if($DT['spam_appcode'] && cloud_spam($content, $DT['spam_appcode'], 2)) dalert($L['comment_msg_ban']);
				$star = intval($star);
				in_array($star, array(1, 2, 3, 4, 5)) or $star = 5;
				$status = get_status(3, $MOD['comment_check'] == 2 ? $MG['check'] : $MOD['comment_check']);
				$hidden = isset($hidden) ? 1 : 0;
				$title = addslashes($title);
				$content = nl2br($content);
				$quotation = '';
				$qid = isset($qid) ? intval($qid) : 0;
				if($qid) {
					$r = $db->get_one("SELECT ip,hidden,username,passport,content,quotation,addtime FROM {$table} WHERE itemid=$qid");
					if($r) {
						if($r['username']) {
							$r['name'] = $r['hidden'] ? $MOD['comment_am'] : $r['passport'];
						} else {
							$r['name'] = 'IP:'.hide_info($r['ip'], 'ip');
						}
						$r['addtime'] = timetodate($r['addtime'], 5);
						if($r['quotation']) $r['content'] = $r['quotation'];
						$floor = substr_count($r['content'],'quote_content') + 1;
						if($floor == 1) {
							$quotation = addslashes('<div class="quote"><div class="quote_title"><span class="quote_floor">'.$floor.'</span>'.$r['name'].' '.$L['comment_quote_at'].' <span class="quote_time">'.$r['addtime'].'</span> '.$L['comment_quote_or'].'</div><div class="quote_content">'.$r['content'].'</div><!----></div>').$content;
						} else {
							$quotation = str_replace('<!----></div>', '</div><div class="quote_title"><span class="quote_floor">'.$floor.'</span>'.$r['name'].' '.$L['comment_quote_at'].' <span class="quote_time">'.$r['addtime'].'</span> '.$L['comment_quote_or'].'</div><div class="quote_content">', $r['content']);
							$quotation = '<div class="quote">'.$quotation.'</div><!----></div>';
							$quotation = addslashes($quotation).$content;
						}
					}
					$db->query("UPDATE {$table} SET quotes=quotes+1 WHERE itemid=$qid");
				}
				$db->query("INSERT INTO {$table} (item_mid,item_id,item_title,item_username,content,quotation,qid,addtime,userid,username,passport,hidden,star,ip,status) VALUES ('$mid','$itemid','$title','$username','$content','$quotation','$qid','$DT_TIME','$_userid','$_username','".addslashes($_passport)."','$hidden','$star','$DT_IP','$status')");
				$cid = $db->insert_id();
				$r = $db->get_one("SELECT sid FROM {$table}_stat WHERE moduleid=$mid AND itemid=$itemid");
				$star = 'star'.$star;
				if($r) {
					$db->query("UPDATE {$table}_stat SET comment=comment+1,`{$star}`=`{$star}`+1 WHERE sid=$r[sid]");
				} else {
					$db->query("INSERT INTO {$table}_stat (moduleid,itemid,{$star},comment) VALUES ('$mid','$itemid','1','1')");
				}
				if($status == 3) {
					if($_username && $MOD['credit_add_comment']) {
						credit_add($_username, $MOD['credit_add_comment']);
						credit_record($_username, $MOD['credit_add_comment'], 'system', $L['comment_record_add'], 'ID:'.$cid);
					}
					$items = isset($items) ? intval($items) + 1 : 1;
					$page = ceil($items/$pagesize);
					$forward = rewrite('index'.DT_EXT.'?mid='.$mid.'&itemid='.$itemid.'&page='.$page.'&rand='.mt_rand(10, 99)).'#last';
					dalert($level ? $L['comment_check'] : '', '', 'parent.window.location="'.$forward.'";');
				} else {
					dalert($L['comment_check'], '', 'parent.window.location=parent.window.location;');
				}
			} else {
				$proxy = '';
				$order = $MOD['comment_order'] ? $MOD['comment_order'] : 'itemid ASC';
				$lists = $ids = array();
				$pages = '';
				$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE item_mid=$mid AND item_id=$itemid AND status=3".($level ? " AND level>0" : ''), 'CACHE');
				$items = $r['num'];
				if($items != $item['comments'] && !$level) $db->query("UPDATE ".get_table($mid)." SET comments=$items WHERE ".($mid == 4 ? 'userid' : 'itemid')."=$itemid", 'UNBUFFERED');
				$pages = pages($items, $page, $pagesize);
				$floor = $page == 1 ? 0 : ($page-1)*$pagesize;
				$top = intval($MOD['comment_top']);
				if($top > 0 && $level < 1) {
					$result = $db->query("SELECT * FROM {$table} WHERE item_mid=$mid AND item_id=$itemid AND status=3 AND level>0 ORDER BY level DESC,{$order} LIMIT $top", 'CACHE');
					while($r = $db->fetch_array($result)) {
						$ids[] = $r['itemid'];
						$r['floor'] = 0;
						$r['adddate'] = timetodate($r['addtime'], 5);
						$r['replydate'] = $r['replytime'] ? timetodate($r['replytime'], 5) : '';
						if($r['username']) {
							$r['name'] = $r['hidden'] ? $MOD['comment_am'] : $r['passport'];
							$r['uname'] = $r['hidden'] ? '' : $r['username'];
						} else {
							$r['name'] = $MOD['comment_am'];
							$r['uname'] = '';
						}
						if(strpos($r['content'], '://') !== false) $r['content'] = parse_link($r['content']);
						if(strpos($r['content'], ')') !== false) $r['content'] = parse_face($r['content']);
						if(strpos($r['quotation'], ')') !== false) $r['quotation'] = parse_face($r['quotation']);
						$lists[] = $r;
					}
				}
				$result = $db->query("SELECT * FROM {$table} WHERE item_mid=$mid AND item_id=$itemid AND status=3".($level ? " AND level>0" : '')." ORDER BY {$order} LIMIT {$offset},{$pagesize}", $page < 2 ? 'CACHE' : '');
				while($r = $db->fetch_array($result)) {
					$r['floor'] = ++$floor;
					$r['adddate'] = timetodate($r['addtime'], 5);
					$r['replydate'] = $r['replytime'] ? timetodate($r['replytime'], 5) : '';
					if($r['username']) {
						$r['name'] = $r['hidden'] ? $MOD['comment_am'] : $r['passport'];
						$r['uname'] = $r['hidden'] ? '' : $r['username'];
					} else {
						$r['name'] = $MOD['comment_am'];
						$r['uname'] = '';
					}
					if(strpos($r['content'], ')') !== false) $r['content'] = parse_face($r['content']);
					if(strpos($r['quotation'], ')') !== false) $r['quotation'] = parse_face($r['quotation']);
					if($r['fid'] != $r['floor']) $db->query("UPDATE {$table} SET fid=$r[floor] WHERE itemid=$r[itemid]");
					if(!in_array($r['itemid'], $ids)) $lists[] = $r;
				}
				$lists = list_user($lists, 'validate,gradeid');
				$stat = $r = $db->get_one("SELECT * FROM {$table}_stat WHERE moduleid=$mid AND itemid=$itemid", 'CACHE');
				if($stat && $stat['comment']) {
					$stat['pc1'] = dround($stat['star1']*100/$stat['comment'], 2, true).'%';
					$stat['pc2'] = dround($stat['star2']*100/$stat['comment'], 2, true).'%';
					$stat['pc3'] = dround($stat['star3']*100/$stat['comment'], 2, true).'%';
					$stat['pc4'] = dround($stat['star4']*100/$stat['comment'], 2, true).'%';
					$stat['pc5'] = dround($stat['star5']*100/$stat['comment'], 2, true).'%';
				} else {
					$stat['star1'] = $stat['star2'] = $stat['star3'] = $stat['star4'] = $stat['star5'] = 0;
					$stat['pc1'] = $stat['pc2'] = $stat['pc3'] = $stat['pc4'] = $stat['pc5'] = '0%';
				}				
				include DT_ROOT.'/file/config/face.inc.php';
			}
		}
		$moduleid = $mid;
		$head_title = $title.$L['comment_title'].$DT['seo_delimiter'].$MODULE[$mid]['name'];
	break;
}
$template = $ext;
if($DT_PC) {
	$CSS = array('comment');
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace($url, $mob, $DT_URL);
} else {
	$pages = mobile_pages($items, $page, $pagesize);
	$head_name = $L['comment_title'];
	if($sns_app) $seo_title = $site_name;
	$foot = '';
}
include template($template, $module);
?>