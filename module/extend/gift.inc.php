<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$ext = 'gift';
$MOD[$ext.'_enable'] or message($L['closed'], $DT_PC ? DT_PATH : DT_MOB, 6);
$url = $EXT[$ext.'_url'];
$mob = $EXT[$ext.'_mob'];
$TYPE = get_type($ext, 1);
$_TP = sort_type($TYPE);
require DT_ROOT.'/module/'.$module.'/'.$ext.'.class.php';
$do = new $ext();
$typeid = isset($typeid) ? intval($typeid) : 0;
switch($action) {
	case 'my':
		login();
		$condition = "username='$_username'";
		$lists = $do->get_my_order($condition);
		$head_title = $L['gift_my_order'].$DT['seo_delimiter'].$L['gift_title'];
	break;
	case 'result':
		$code = isset($code) ? intval($code) : 0;
		$uri = ($DT_PC ? $url : $mob).($itemid ? 'index'.DT_EXT.'?itemid='.$itemid : '');
		$head_title = $L['gift_my_order'];
	break;
	case 'order':
		login();
		$itemid or dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=10');
		$do->itemid = $itemid;
		$item = $do->get_one();
		$item or dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=10');
		extract($item);
		$left = $amount - $orders > 0 ? $amount - $orders : 0;
		$process = $left ? get_process($fromtime, $totime) : 4;
		$_url = $DT_PC ? $linkurl : str_replace($url, $mob, $linkurl);
		if($process == 1) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=11');
		if($process == 3) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=13');
		if($process == 4) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=14');
		if($_credit < $credit) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=15');
		if(!check_group($_groupid, $groupid)) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=16');
		if($maxorder) {
			$num = $db->count($DT_PRE.'gift_order', "itemid=$itemid AND username='$_username'");
			if($num >= $maxorder) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=17');
		}
		if($EXT['gift_time']) {
			$t = $db->get_one("SELECT * FROM {$DT_PRE}gift_order WHERE username='$_username'");
			if($t && $DT_TIME - $t['addtime'] < $EXT['gift_time']) dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid.'&code=18');
		}
		credit_add($_username, -$credit);
		credit_record($_username, -$credit, 'system', $L['gift_credit_reason'], 'ID:'.$itemid);
		$db->query("INSERT INTO {$DT_PRE}gift_order (itemid,credit,username,ip,addtime,edittime,status) VALUES ('$itemid','$credit','$_username','$DT_IP','$DT_TIME','$DT_TIME','".$L['gift_status']."')");
		$db->query("UPDATE {$DT_PRE}gift SET orders=orders+1 WHERE itemid=$itemid");
		dheader('index'.DT_EXT.'?action=result&&itemid='.$itemid);
	break;
	default:
		if($itemid) {
			$do->itemid = $itemid;
			$item = $do->get_one();
			$item or dheader($url);
			require DT_ROOT.'/include/content.class.php';
			extract($item);
			$left = $amount - $orders > 0 ? $amount - $orders : 0;
			$process = $left ? get_process($fromtime, $totime) : 4;
			$adddate = timetodate($addtime, 3);
			$fromdate = $fromtime ? timetodate($fromtime, 3) : $L['timeless'];
			$todate = $totime ? timetodate($totime, 3) : $L['timeless'];
			$middle = str_replace('.thumb.', '.middle.', $thumb);
			$large = str_replace('.thumb.'.file_ext($thumb), '', $thumb);
			$gname = '';
			if($groupid) {
				$GROUP = cache_read('group.php');
				foreach(explode(',', $groupid) as $gid) {
					if(isset($GROUP[$gid])) $gname .= $GROUP[$gid]['groupname'].' ';
				}
			}
			$content = DC::format($content, $DT_PC);
			if(!$DT_BOT) $db->query("UPDATE LOW_PRIORITY {$DT_PRE}{$ext} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
			$head_title = $title.$DT['seo_delimiter'].$L['gift_title'];
		} else {
			$pagesize = 10;
			$offset = ($page-1)*$pagesize;
			$head_title = $L['gift_title'];
			if($catid) $typeid = $catid;
			$condition = "1";
			if($typeid) {
				isset($TYPE[$typeid]) or dheader($url);
				$condition .= " AND typeid IN (".type_child($typeid, $TYPE).")";
				$head_title = $TYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
			}
			if($keyword) $condition .= match_kw('title', $keyword);
			if($cityid) $condition .= ($AREA[$cityid]['child']) ? " AND areaid IN (".$AREA[$cityid]['arrchildid'].")" : " AND areaid=$cityid";
			$lists = $do->get_list($condition, 'addtime DESC');
		}
	break;
}
$template = $ext;
if($DT_PC) {
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace($url, $mob, $DT_URL);
} else {
	if($action == 'my') {
		$pages = mobile_pages($items, $page, $pagesize);
	} elseif($action == 'result') {
	} else {
		if($itemid) {
			$js_item = 1;
		} else {
			$pages = mobile_pages($items, $page, $pagesize);
		}
	}
	$head_name = $L['gift_title'];
	if($sns_app) $seo_title = $site_name;
	$foot = '';	
}
include template($template, $module);
?>