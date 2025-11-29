<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$linkurl = '';
if($itemid) {
	if($mid == 3) {
		if($job) {
			if(isset($EXT[$job.'_enable']) && $EXT[$job.'_enable']) {
				$table = $DT_PRE.$job;
				$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
				if(isset($item['title']) && isset($item['linkurl'])) {
					$TBN = include DT_ROOT.'/file/setting/comment.php';
					if(isset($TBN[$job])) $MODULE[$mid]['name'] = $TBN[$job];
					$MODULE[$mid]['linkurl'] = $DT_PC ? $EXT[$job.'_url'] : $EXT[$job.'_mob'];
					$title = $item['title'];
					$linkurl = $DT_PC ? $item['linkurl'] : str_replace($EXT[$job.'_url'], $EXT[$job.'_mob'], $item['linkurl']);
					$thumb = isset($item['thumb']) ?  $item['thumb'] : '';
					$pic = $thumb ? str_replace('.thumb.'.file_ext($thumb), '', $thumb) : '';
					$introduce = '';
					$auth = urlencode(str_replace('amp;', '', $linkurl));
				}
			}
		}
	} else if($mid == 4) {
		$table = $DT_PRE.'company';
		$item = $db->get_one("SELECT * FROM {$table} WHERE userid=$itemid");
		($item && $item['groupid'] > 4) or message($L['msg_not_exist']);
		$title = $item['company'];
		$linkurl = $item['linkurl'];
		$thumb = isset($item['thumb']) ?  $item['thumb'] : '';
		$pic = $thumb ? str_replace('.thumb.'.file_ext($thumb), '', $thumb) : '';
		$introduce = isset($item['introduce']) ? $item['introduce'] : '';
		$auth = urlencode(str_replace('amp;', '', $linkurl));
		$db->query("UPDATE {$table} SET shares=shares+1 WHERE userid=$itemid");
	} else if($mid > 4) {
		$table = get_table($mid);
		$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
		($item && $item['status'] > 2) or message($L['msg_not_exist']);
		$title = $item['title'];
		$linkurl = $item['linkurl'];
		if(strpos($linkurl, '://') === false) $linkurl = ($DT_PC ? $MODULE[$mid]['linkurl'] : $MODULE[$mid]['mobile']).$linkurl;
		$thumb = isset($item['thumb']) ?  $item['thumb'] : '';
		$pic = $thumb ? str_replace('.thumb.'.file_ext($thumb), '', $thumb) : '';
		$introduce = isset($item['introduce']) ? $item['introduce'] : '';
		$auth = urlencode(str_replace('amp;', '', $linkurl));
		$db->query("UPDATE {$table} SET shares=shares+1 WHERE itemid=$itemid");
	} else {
		message($L['share_not_support']);
	}
}
$linkurl or message($L['share_not_support']);
$_title = urlencode($title);
$_linkurl = urlencode($linkurl);
$sms = 'sms:?body='.$linkurl;
if(preg_match("/(iPhone|iPod|iPad)/i", DT_UA)) $sms = 'sms: &body='.$title.$linkurl;
$template = 'share';
$head_title = $L['share_title'];
if($DT_PC) {	
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace(DT_PATH, DT_MOB, $DT_URL);
	$moduleid = $mid;
} else {
	$back_link = $linkurl;
	$head_name = $head_title;
	if($sns_app) $seo_title = $site_name;
}
include template($template, $module);
?>