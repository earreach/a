<?php 
defined('IN_DESTOON') or exit('Access Denied');
$seo_title = isset($HOME['seo_title']) && $HOME['seo_title'] ? $HOME['seo_title'] : '';
$head_title = '';
if($DT_PC) {
	$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $DT_PRE.'company_data');
	$r = $db->get_one("SELECT content FROM {$content_table} WHERE userid=$userid", 'CACHE');
	$COM['content'] = $r['content'];
	$intro_length = isset($HOME['intro_length']) && $HOME['intro_length'] ? intval($HOME['intro_length']) : 1000;
	$COM['intro'] = nl2br(dsubstr(trim(strip_tags($r['content'])), $intro_length, '...'));
} else {
	$background = (isset($HOME['background']) && $HOME['background']) ? $HOME['background'] : '';
	$M = array();
	$my_sell = $my_mall = 0;
	foreach($MENU as $k=>$v) {
		$i = $D_MENU[$k];
		if(is_numeric($i)) {
			$v['icon'] = $MODULE[$i]['icon'] ? $MODULE[$i]['icon'] : DM_SKIN.'mod-'.$MODULE[$i]['module'].'.png';
		} else {
			if(in_array($k, array('introduce', 'contact', 'news'))) continue;
			$v['icon'] = DM_SKIN.'my-'.$k.'.png';
		}
		$M[] = $v;
		if(is_numeric($i)) {
			if($MODULE[$i]['module'] == 'sell' && !$my_sell) $my_sell = $i;
			if($MODULE[$i]['module'] == 'mall' && !$my_mall) $my_mall = $i;
		}
	}
	if($bannerf) require DT_ROOT.'/include/content.class.php';
	$head_name = $home_name;
	$foot = 'home';
}
include template('index', $template);
if($DT['baidu_push'] && $COM['hits'] == 10) baidu_push($COM['linkurl'], $DT['baidu_push']);
?>