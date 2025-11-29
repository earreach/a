<?php
$moduleid = 3;
require '../../common.inc.php';
require DT_ROOT.'/include/mobile.inc.php';
require DT_ROOT.'/include/module.func.php';
$url = isset($url) ? fix_link($url) : DT_MOB;
if(isset($username)) {
	if(check_name($username)) {
		$r = $db->get_one("SELECT linkurl FROM {$DT_PRE}company WHERE username='$username'");
		$url = $r ? $r['linkurl'] : userurl($username);
	}
} else if(isset($aid)) {
	$aid = intval($aid);
	if($aid) {
		$r = $db->get_one("SELECT * FROM {$DT_PRE}ad WHERE aid=$aid");
		if($r && $r['fromtime'] < DT_TIME && $r['totime'] > DT_TIME) {
			if($r['key_moduleid'] && $r['typeid'] > 5) {
				$url = 'redirect'.DT_EXT.'?mid='.$r['key_moduleid'].'&itemid='.$r['key_id'];
			} else if($r['url']) {
				$url = $r['url'];
			}
			if($r['stats']) {
				require DT_ROOT.'/include/client.func.php';
				$ua = addslashes(dhtmlspecialchars(strip_sql(strip_tags(DT_UA))));
				$os = get_os();
				$bs = get_bs();
				$pid = $r['pid'];
				$db->query("INSERT INTO {$DT_PRE}ad_stats (pid,aid,os,bs,ua,username,ip,pc,addtime) VALUES ('$pid','$aid','$os','$bs','$ua','$_username','$DT_IP','$DT_PC','$DT_TIME')");
				$db->query("UPDATE LOW_PRIORITY {$DT_PRE}ad SET hits=hits+1 WHERE aid=$aid", 'UNBUFFERED');
			}
		}
	}
} else if($mid) {
	if($mid == 1) {
	} else if($mid == 2) {
		if($itemid) {
			if(in_array($tb, array('news', 'honor', 'page'))) {
				$r = $db->get_one("SELECT * FROM {$DT_PRE}{$tb} WHERE itemid=$itemid");			
				if($r && $r['linkurl']) $url = $r['linkurl'];
			} else {
				$r = $db->get_one("SELECT username,domain FROM {$DT_PRE}company WHERE userid=$itemid");
				if($r) $url = userurl($r['username'], 'file=space', $r['domain']);
			}
		}
	} else if($mid == 3) {
		isset($tb) or $tb = '';
		if($itemid) {
			if(in_array($tb, array('announce', 'webpage', 'link', 'gift', 'vote', 'poll', 'form'))) {
				$r = $db->get_one("SELECT * FROM {$DT_PRE}{$tb} WHERE itemid=$itemid");			
				if($r && $r['linkurl']) {
					if($tb == 'webpage') {
						$url = strpos($r['linkurl'], '://') === false ? DT_MOB.$r['linkurl'] : $r['linkurl'];
					} else {
						$k = $tb.'_mob';
						$url = strpos($r['linkurl'], '://') === false ? $EXT[$k].$r['linkurl'] : $r['linkurl'];
					}
				}
			} else {
				$r = $db->get_one("SELECT * FROM {$DT_PRE}comment WHERE itemid=$itemid");
				if($r && $r['status'] == 3) {
					$page = $r['fid'] ? ceil($r['fid']/$EXT['comment_pagesize']) : 1;
					$url = $EXT['comment_mob'].rewrite('index.php?mid='.$r['item_mid'].'&itemid='.$r['item_id'].($page > 1 ? '&page='.$page : '')).'#H'.$itemid;
				}
			}
		}
	} else if($mid == 4) {
		$r = $db->get_one("SELECT linkurl FROM {$DT_PRE}company WHERE userid=$itemid");
		if($r) {
			$url = $r['linkurl'];
			if($sum) $url = $MODULE[2]['mobile'].'pay.php?mid='.$mid.'&itemid='.$itemid;
		}
	} else {
		if(isset($MODULE[$mid]) && !$MODULE[$mid]['islink']) {
			$table = get_table($mid);
			$r = $db->get_one("SELECT linkurl FROM {$table} WHERE itemid=$itemid");
			if($r) {
				$url = strpos($r['linkurl'], '://') === false ? $MODULE[$mid]['mobile'].$r['linkurl'] : $r['linkurl'];
				if($sum) $url = $MODULE[2]['mobile'].'pay.php?mid='.$mid.'&itemid='.$itemid;
			}
		}
	}
} else {
	(check_referer() && is_url($url)) or $url = DT_MOB;
	if(is_uri($url) || $_groupid == 1 || strpos(cutstr($url, '://', '/'), base64_decode('ZGVzdG9vbi5jb20=')) !== false) {
		//
	} else {
		$head_title = $L['redirect_title'];
		$head_name = $head_title;
		$js_pull = 0;
		include template('redirect', $module);
		exit;
	}
}
dheader($url);
?>