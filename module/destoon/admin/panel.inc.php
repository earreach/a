<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/destoon/admin/admin.class.php';
$do = new admin;
$do->userid = $_userid;
$menus = array (
    array('我的面板', '?file='.$file),
);
if($DT['admin_hit']) $menus[] = array('点击记录', '?file='.$file.'&action=menu');
switch($action) {
	case 'log':
		if($DT['admin_hit'] && $title && $url) {
			$md5 = md5($url);
			if($dc->get('menu-url-'.$_userid.DT_IP) == $md5) exit;
			$do->menu_log($_userid, $title, $url);
			$dc->set('menu-url-'.$_userid.DT_IP, $md5);
		}
		exit;
	break;
	case 'menu':
		$DT['admin_hit'] or msg('此功能暂未开启');
		$sfields = array('按条件', '菜单', '网址');
		$dfields = array('title', 'title', 'url');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$datetype = 'addtime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = "userid=$_userid AND addtime>0";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		$lists = $do->menu_list($condition);
		if(DT_TIME - filemtime(DT_CACHE.'/admin-menus-'.$_userid.'.php') > 1800) $do->cache_menus($_userid);
		include tpl('panel_menu');
	break;
	case 'delete':		
		$itemid or msg('请选择项目');
		foreach($itemid as $id) {
			$do->delete($id, array());
		}
		dmsg('删除成功', '?file='.$file.'&update=1');
	break;
	case 'update':		
		if($do->update($_userid, $right, $_admin)) dmsg('保存成功', '?file='.$file.'&update=1');
		msg($do->errmsg);
	break;
	default:
		$dmenus = $do->get_panel($_userid);
		if(isset($title)) {
			//
		} else {
			$title = '';
		}
		if(isset($url)) {
			$pos = strpos($url, '?');
			if($pos !== false) $url = substr($url, $pos);
		} else {
			$url = '';
		}
		include tpl('panel');
	break;
}
?>