<?php
require '../../../common.inc.php';
isset($MODULE[20]) or exit;
$_userid or exit;
isset($auth) or exit;
$d = decrypt($auth, DT_KEY.'SYNC');
strpos($d, '-') !== false or exit;
$t = explode('-', $d);
$mid = intval($t[0]);
isset($MODULE[$mid]) or exit;
$MODULE[$mid]['module'] != 'moment' or exit;
$itemid = intval($t[1]);
$itemid > 0 or exit;
if($mid == 2) {
	$item = $db->get_one("SELECT * FROM {$DT_PRE}news WHERE itemid=$itemid");
} else if($mid > 4) {
	$item = $db->get_one("SELECT * FROM ".get_table($mid)." WHERE itemid=$itemid");
} else {
	exit;
}
$item or exit;
$DT_TIME - $item['addtime'] < 30 or exit;
$item['status'] == 3 or exit;
$moduleid = 20;
$module = 'moment';
$MOD = cache_read('module-'.$moduleid.'.php');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
include load($module.'.lang');
include load('my.lang');
require DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
$do = new $module($moduleid);
$post = array();
$post['mid'] = intval($mid);
$post['tid'] = intval($itemid);
$post['linkto'] = strpos($item['linkurl'], '://') === false ? $MODULE[$mid]['linkurl'].$item['linkurl'] : $item['linkurl'];
$post['video'] = isset($item['video']) ? $item['video'] : '';
if(isset($item['thumbs'])) {
	$post['thumbs'] = explode('|', $item['thumb'].'|'.$item['thumbs']);
} else if(isset($item['thumb'])) {
	$post['thumbs'] = array($item['thumb']);
}
if($post['video'] && count($post['thumbs']) == 1) $post['thumbs'] = array();
$post['content'] = addslashes($item['title']);
$post['title'] = addslashes($item['title']);
$post['status'] = $item['status'];
$post['username'] = $item['username'];
$post['open'] = 1;
#log_write($post, 'mm', 1);
if($do->pass($post)) $do->add($post);
?>