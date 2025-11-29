<?php
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/include/module.func.php';
(isset($from) && check_name($from)) or $from = '';
if($job == 'item') {
	$mid > 3 or dheader('DT_PATH');	
	$from or $from = 'item';
	isset($username) or $username = '';
	$itemid or $itemid = '';
	$condition = $mid == 4 ? 'groupid IN ('.get_gids().')' : 'status=3';
	if($keyword) $condition .= match_kw('keyword', $keyword);
	if($from == 'relate' && $MODULE[$mid]['module'] == 'mall') {
		check_name($username) or exit;
		$condition .= " AND username='$username'";
	} else {
		if($_groupid == 1) {
			if($from == 'member') $condition .= " AND username='$_username'";
		} else {
			$condition .= " AND username='$_username'";
		}
	}
	if($itemid) $condition .= $mid == 4 ? " AND userid=$itemid" : " AND itemid=$itemid";
	$order = $mid == 4 ? 'userid DESC' : 'addtime DESC';
	$table = get_table($mid);
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		if($mid == 4) {
			$r['itemid'] = $r['userid'];
			$r['alt'] = $r['title'] = $r['company'];
			$r['adddate'] = $r['editdate'] = timetodate(0, 5);
			$r['level'] = 0;
			$r['style'] = '';
		} else {
			$r['adddate'] = timetodate($r['addtime'], 5);
			$r['editdate'] = timetodate($r['edittime'], 5);
			$r['alt'] = $r['title'];
			$r['title'] = set_style($r['title'], $r['style']);
			if(strpos($r['linkurl'], '://') === false) $r['linkurl'] = $MODULE[$mid]['linkurl'].$r['linkurl'];
		}
		$lists[] = $r;
	}
	$head_title = '选择信息';
} else if($job == 'stock') {
	(isset($skuid) && is_skuid($skuid)) or $skuid = '';
	isset($key) or $key = '';
	if($key == 'content' && $itemid) {
		$t = $db->get_one("SELECT username FROM {$DT_PRE}stock WHERE itemid=$itemid");
		if(!$t || $t['username'] != $_username) exit('');
		$t = $db->get_one("SELECT content FROM {$DT_PRE}stock_data WHERE itemid=$itemid");
		exit($t ? $t['content'] : '');
	}
	$condition = "username='$_username'";
	if($keyword) $condition .= match_kw('keyword', $keyword);
	if($skuid) $condition .= " AND skuid='$skuid'";
	$order = 'addtime DESC';
	$table = $DT_PRE.'stock';
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
	$items = $r['num'];
	$pages = pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['adddate'] = timetodate($r['addtime'], 6);
		$r['editdate'] = timetodate($r['edittime'], 6);
		$r['profit'] = dround($r['price'] - $r['cost'], 2, 1);
		$r['alt'] = $r['title'];
		$r['title'] = set_style($r['title'], $r['style']);
		$lists[] = $r;
	}
	$head_title = '选择商品';
} else if($job == 'topic') {
	$MOD['module'] == 'moment' or exit;
	(isset($fid) && check_name($fid)) or $fid = 'hash';
	$sfields = array($L['search_by'], '标题', '简介');;
	$dfields = array('title', 'title', 'content');
	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	$condition = "status=3";
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order = 'addtime DESC';
	$table = $DT_PRE.'moment_topic_'.$moduleid;
	$items = $db->count($table, $condition);
	$pages = pages($items, $page, $pagesize);
	$lists = array();
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		$r['alt'] = $r['title'];
		$r['title'] = set_style($r['title'], $r['style']);
		$r['linkurl'] = $MOD['linkurl'].rewrite('topic'.DT_EXT.'?itemid='.$r['itemid']);
		$lists[] = $r;
	}
	$head_title = '选择话题';
} else if($job == 'friend') {
	(isset($fid) && check_name($fid)) or $fid = 'touser';
	(isset($key) && check_name($key)) or $key = 'username';
	$tabid = isset($tabid) ? intval($tabid) : 0;
	$table = $DT_PRE.'follow';
	$order = 'itemid DESC';
	$name = '好友';
	$typeid = isset($typeid) ? ($typeid === '' ? -1 : intval($typeid)) : -1;
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	if($tabid == 2) {
		$name = '粉丝';
		$TYPE = get_type('fans-'.$_userid);
		$sfields = $L['follow_sfields'];
		$dfields = array('username', 'username', 'passport', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$condition = "fuserid=$_userid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($typeid > -1) $condition .= " AND typeid=$typeid";
	} else if($tabid == 1) {
		$name = '关注';
		$TYPE = get_type('follow-'.$_userid);
		$sfields = $L['follow_sfields'];
		$dfields = array('fusername', 'fusername', 'fpassport', 'fnote');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$condition = "userid=$_userid";
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($typeid > -1) $condition .= " AND ftypeid=$typeid";
	} else {
		$TYPE = get_type('friend-'.$_userid);
		$condition = "userid=$_userid";
		$condition .= $from == 'sms' ? " AND mobile<>''" : " AND fusername<>''";
		$sfields = $L['friend_sfields'];
		$dfields = array('fpassport', 'truename', 'alias', 'company', 'career', 'telephone', 'mobile', 'homepage', 'email', 'qq', 'wx', 'ali', 'skype', 'fusername', 'fpassport', 'note');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($typeid > -1) $condition .= " AND typeid=$typeid";
		$table = $DT_PRE.'friend';
		$order = 'listorder DESC,itemid DESC';
	}
	$fields_select = dselect($sfields, 'fields', '', $fields);
	$type_select = type_select($TYPE, 0, 'typeid', $L['default_type'], $typeid, '', $L['all_type']);	
	$items = $db->count($table, $condition);
	$pages = pages($items, $page, $pagesize);
	$lists = array();
	#exit("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
	$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
	while($r = $db->fetch_array($result)) {
		if($tabid == 2) {
			$r['fusername'] = $r['username'];
			$r['fpassport'] = $r['passport'];
		} else if($tabid == 1) {
			$r['typeid'] = $r['ftypeid'];
			$r['note'] = $r['fnote'];
		}
		$r['type'] = $r['typeid'] && isset($TYPE[$r['typeid']]) ? set_style($TYPE[$r['typeid']]['typename'], $TYPE[$r['typeid']]['style']) : $L['default_type'];
		$lists[] = $r;
	}
	$head_title = '选择'.$name;
} else {
	$tab = isset($tab) ? intval($tab) : 0;
	$fid = isset($fid) ? trim($fid) : '';
	$photo = 0;
	$lists = $tags = array();
	$pagesize = 49;
	$offset = ($page-1)*$pagesize;
	if($DT['uploadlog']) {
		$sorder  = array('排序方式', '上传时间降序', '上传时间升序', '文件大小降序', '文件大小升序', '图片宽度降序', '图片宽度升序', '图片高度降序', '图片高度升序');
		$dorder  = array('pid DESC', 'addtime DESC', 'addtime ASC', 'filesize DESC', 'filesize ASC', 'width DESC', 'width ASC', 'height DESC', 'height ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$module_select = module_select('mid', '模块', $mid, '');
		$order_select = dselect($sorder, 'order', '', $order);
		$table = $DT_PRE.'upload_'.($_userid%10);
		$condition = "username='$_username' AND width>90";
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($mid) $condition .= " AND moduleid='$mid'";	
		//$condition .= $from == 'album' ? " AND (upfrom='album' OR upfrom='photo')" : " AND upfrom<>'thumb'";
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
		$items = $r['num'];
		$pages = pages($items, $page, $pagesize);
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['introduce'] = timetodate($r['addtime'], 6).'&#10;'.$r['width'].'px * '.$r['height'].'px&#10;';
			$r['ext'] = file_ext($r['fileurl']);
			$r['thumb'] = $r['fileurl'];
			$r['middle'] = str_replace('.thumb.'.$r['ext'], '.middle.'.$r['ext'], $r['thumb']);
			$r['large'] = str_replace('.thumb.'.$r['ext'], '', $r['thumb']);
			$lists[] = $r;
		}
		if(isset($ajax)) exit(json_encode($lists));
	} else {
		foreach($MODULE as $M) {
			if($M['module'] == 'photo') {
				$mid = $M['moduleid'];
				break;
			}
		}
		$mid or message('系统未开启图库功能');
		$photo = 1;
		if($itemid) {
			$item = $db->get_one("SELECT username,title FROM {$DT_PRE}photo_{$mid} WHERE itemid=$itemid");
			if(!$item || ($item['username'] != $_username && $_groupid > 1)) message('相册不存在');
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}photo_item_{$mid} WHERE item=$itemid");
			$items = $r['num'];
			$pages = pages($items, $page, $pagesize);
			$result = $db->query("SELECT * FROM {$DT_PRE}photo_item_{$mid} WHERE item=$itemid ORDER BY listorder ASC,itemid ASC LIMIT {$offset},{$pagesize}");
			while($r = $db->fetch_array($result)) {
				$r['ext'] = file_ext($r['thumb']);
				$r['middle'] = str_replace('.thumb.'.$r['ext'], '.middle.'.$r['ext'], $r['thumb']);
				$r['large'] = str_replace('.thumb.'.$r['ext'], '', $r['thumb']);
				$lists[] = $r;
			}
		} else {
			$condition = "status=3 AND items>0 AND username='$_username'";
			if($keyword) $condition .= match_kw('keyword', $keyword);
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}photo_{$mid} WHERE {$condition}");
			$items = $r['num'];
			$pages = pages($items, $page, $pagesize);
			$result = $db->query("SELECT * FROM {$DT_PRE}photo_{$mid} WHERE {$condition} ORDER BY addtime LIMIT {$offset},{$pagesize}");
			while($r = $db->fetch_array($result)) {
				$tags[] = $r;
			}
		}
	}
	$head_title = '选择图片';
}
if($DT_PC) {
	//
} else {
	$foot = '';
}
include template('choose', 'member');
?>