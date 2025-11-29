<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('添加规则', '?file='.$file.'&action=add'),
    array('采编规则', '?file='.$file),
    array('测试规则', '?file='.$file.'&action=view'),
);
switch($action) {
	case 'add':
		if($submit) {
			if(strlen($sitename) < 3) msg('请填写网站名称');
			$domain = strtolower(trim($domain));
			if(!preg_match("/^[a-z0-9_\-\.]{3,}$/", $domain)) msg('请输入采编域名');
			$setting = (isset($_REQUEST['setting']) && $_REQUEST['setting']) ? $_REQUEST['setting'] : strip_sql($setting, 0);
			$arr = array();
			foreach($setting as $v) {
				if(preg_match("/^[a-z]{1}[a-z0-9_\-]{1,}$/", $v['fk'])) $arr[] = $v;
			}
			$setting = serialize(dstripslashes($arr));
			$sitename = trim(dhtmlspecialchars($sitename));
			$domain = trim(dhtmlspecialchars($domain));
			$mark = trim(dhtmlspecialchars($mark));
			$db->query("INSERT INTO {$DT_PRE}fetch (sitename,domain,mark,encode,setting,editor,edittime) VALUES ('$sitename','$domain','$mark','$encode','$setting','$_username','$DT_TIME')");
			dmsg('添加成功', $forward);
		} else {
			$domain = $sitename = $mark = '';
			$encode = strtolower(DT_CHARSET);
			$max = 5;
			$setting = array();
			$setting[] = array('fk' => 'title', 'nm' => '标题', 'fm' => '<title>', 'to' => '-');
			$setting[] = array('fk' => 'content', 'nm' => '内容', 'fm' => '<div class="content">', 'to' => '</div>');
			include tpl('fetch_edit');
		}
	break;
	case 'edit':
		$itemid or msg('请选择规则');
		if($submit) {
			if(strlen($sitename) < 3) msg('请填写网站名称');
			$domain = strtolower(trim($domain));
			if(!preg_match("/^[a-z0-9_\-\.]{3,}$/", $domain)) msg('请输入采编域名');
			$setting = (isset($_REQUEST['setting']) && $_REQUEST['setting']) ? $_REQUEST['setting'] : strip_sql($setting, 0);
			$arr = array();
			foreach($setting as $v) {
				if(preg_match("/^[a-z]{1}[a-z0-9_\-]{1,}$/", $v['fk'])) $arr[] = $v;
			}
			$setting = serialize(dstripslashes($arr));
			$sitename = trim(dhtmlspecialchars($sitename));
			$domain = trim(dhtmlspecialchars($domain));
			$mark = trim(dhtmlspecialchars($mark));
			$db->query("UPDATE {$DT_PRE}fetch SET sitename='$sitename',domain='$domain',mark='$mark',encode='$encode',setting='$setting',editor='$_username',edittime='$DT_TIME' WHERE itemid=$itemid");
			dmsg('修改成功', '?file='.$file.'&action='.$action.'&itemid='.$itemid);
		} else {
			extract($db->get_one("SELECT * FROM {$DT_PRE}fetch WHERE itemid=$itemid"));
			$setting = $setting ? unserialize($setting) : array();
			if(!$setting) {
				$setting = array();
				$setting[] = array('fk' => 'title', 'nm' => '标题', 'fm' => '<title>', 'to' => '-');
				$setting[] = array('fk' => 'content', 'nm' => '内容', 'fm' => '<div class="content">', 'to' => '</div>');
			}
			$max = count($setting) + 3;
			include tpl('fetch_edit');
		}
	break;
	case 'view':
		(isset($url) && is_url($url)) or $url = '';
		$post = $list = array();
		$html = $msg = '';
		$itemid = 0;
		if($url) {
			$arr = fetch_url($url, 1);
			$post = $arr[0];
			$list = $arr[1];
			$html = dhtmlspecialchars($arr[2]);
			$s = $arr[3];
			if($s) $itemid = $s['itemid'];
			if(!$list) {
				$msg = '未匹配到采编规则';
			} else if(!$html) {
				$msg = '未获取到网页源码';
			} else if(!$post) {
				$msg = '未解析到匹配数据';
			}
		} else {
			$msg = '请输入测试网址';
		}
		include tpl('fetch_view');
	break;
	case 'delete':
		$itemid or msg('请选择规则');
		$ids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		$db->query("DELETE FROM {$DT_PRE}fetch WHERE itemid IN ($ids)");
		dmsg('删除成功', $forward);
	break;
	default:
		$sfields = array('按条件', '网站', '域名', '标识', '编码', '设置', '编辑');
		$dfields = array('sitename', 'sitename', 'domain', 'mark', 'encode', 'setting', 'editor');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}fetch WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);	
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}fetch WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['edittime'] = timetodate($r['edittime'], 5);
			$lists[] = $r;
		}
		include tpl('fetch');
	break;
}
?>