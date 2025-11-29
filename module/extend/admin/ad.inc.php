<?php
defined('DT_ADMIN') or exit('Access Denied');
$TYPE = $L['ad_type'];
$PTYPE = $L['ad_ptype'];
$AREA or $AREA = cache_read('area.php');
require DT_ROOT.'/module/'.$module.'/ad.class.php';
isset($pid) or $pid = 0;
isset($aid) or $aid = 0;
$menus = array (
    array('添加广告位', '?moduleid='.$moduleid.'&file='.$file.'&action=add_place'),
    array('广告位管理', '?moduleid='.$moduleid.'&file='.$file),	
    array('广告管理', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file='.$file.'&action=list\', \'广告管理\');'),
    array('广告审核', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file='.$file.'&action=list&job=check\', \'广告审核\');'),
    array('点击记录', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file='.$file.'&action=stats\', \'点击记录\');'),
    array('更新广告', '?moduleid='.$moduleid.'&file='.$file.'&action=html'),
    array('模块设置', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file=setting&action='.$file.'\', \'模块设置\');'),
);
$menusad = array (
    array('添加广告', '?moduleid='.$moduleid.'&file='.$file.'&pid='.$pid.'&action=add'),
    array('广告管理', '?moduleid='.$moduleid.'&file='.$file.'&pid='.$pid.'&action=list'),
    array('广告审核', '?moduleid='.$moduleid.'&file='.$file.'&pid='.$pid.'&action=list&job=check'),
    array('点击记录', '?moduleid='.$moduleid.'&file='.$file.'&pid='.$pid.'&action=stats'),
);
if($_catids || $_areaids) require DT_ROOT.'/module/destoon/admin/check.inc.php';
$do = new ad();
$do->pid = $pid;
$do->aid = $aid;
$currency = $MOD['ad_currency'];
$unit = $currency == 'money' ? $DT['money_unit'] : $DT['credit_unit'];
$this_forward = '?moduleid='.$moduleid.'&file='.$file.'&action=list&pid='.$pid.'&page='.$page;
$this_place_forward = '?moduleid='.$moduleid.'&file='.$file.'&page='.$page;
switch($action) {
	case 'add':
		if($submit) {
			$pid or msg('未指定广告位');
			if($do->is_ad($post)) {
				$do->add($post);
				$aid = $do->aid;
				if($post['typeid'] == 6) $MOD['linkurl'] = $MODULE[$post['key_moduleid']]['linkurl'];
				tohtml('ad', $module);
				dmsg('添加成功', $this_forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			if(!$pid) dheader('?moduleid='.$moduleid.'&file='.$file.'&action=list_place');
			$p = $do->get_one_place();
			$fromtime = timetodate($DT_TIME, 3).' 00:00:00';
			$title = $introduce = $code = $url = $totime = $text_name = $text_url = $text_title = $text_style = $image_src = $image_url = $image_alt = $video_src = $video_url = $key_word = $key_id = $note = '';
			$video_auto = $video_loop = $stats = 1;
			$key_catid = $hits = 0;
			$username = $_username;
			$status = 3;
			$menuid = 0;
			include tpl('ad_edit', $module);
		}
	break;
	case 'edit':
		$aid or msg();
		if($submit) {
			if($do->is_ad($post)) {
				$do->edit($post);
				if($pid != $post['pid']) {
					$db->query("UPDATE {$DT_PRE}ad_place SET ads=ads+1 WHERE pid=$post[pid]");
					$db->query("UPDATE {$DT_PRE}ad_place SET ads=ads-1 WHERE pid=$pid");
				}
				if($post['typeid'] == 6) $MOD['linkurl'] = $MODULE[$post['key_moduleid']]['linkurl'];
				tohtml('ad', $module);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			extract($do->get_one());
			$do->pid = $pid;
			$p = $do->get_one_place();
			$fromtime = timetodate($fromtime, 6);
			$totime = timetodate($totime, 6);
			$menuid = $status == 2 ? 2 : 1;
			include tpl('ad_edit', $module);
		}
	break;
	case 'delete':
		$aids or msg('请选择广告');
		$do->delete($aids);
		dmsg('删除成功', $forward);
	break;
	case 'order_ad':
		$do->order_ad($listorder);
		dmsg('排序成功', $forward);
	break;
	case 'list':
		$sfields = array('按条件', '广告名称', '广告介绍', '广告代码', '关键词', '文字链接名称', '文字链接地址', '文字链接提示', '图片地址', '图片链接地址', '图片链接提示', '视频地址', '视频链接地址', '会员名', '备注');
		$dfields = array('title', 'title', 'introduce', 'code', 'key_word', 'text_name', 'text_url', 'text_title', 'image_src', 'image_url', 'image_alt', 'video_src', 'video_url', 'username', 'note');
		$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '修改时间降序', '修改时间升序', '开始时间降序', '开始时间升序', '结束时间降序', '结束时间升序', '点击次数降序', '点击次数升序', '费用总额降序', '费用总额升序');
		$dorder  = array('pid DESC,listorder ASC,addtime ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'fromtime DESC', 'fromtime ASC', 'totime DESC', 'totime ASC', 'hits DESC', 'hits ASC', 'amount DESC', 'amount ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($typeid) or $typeid = 0;
		$areaid = isset($areaid) ? intval($areaid) : 0;
		if($job == 'check' && $order == 0) $order = 1;
		$pid or $pid = '';
		isset($datetype) && in_array($datetype, array('edittime', 'addtime', 'fromtime', 'totime')) or $datetype = 'totime';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = $job == 'check' ? "status=2" : "status=3";
		if($_areaids) $condition .= " AND areaid IN (".$_areaids.")";//CITY
		if($_self) $condition .= " AND username='$_username'";//SELF
		if($pid) $condition .= " AND pid=$pid";
		if($typeid) $condition .= " AND typeid=$typeid";
		$type_select  = $pid ? '' : dselect($TYPE, 'typeid', '广告类型', $typeid).'&nbsp; ';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		$lists = $do->get_list($condition, $dorder[$order]);
		if($pid) $do->stats_place($pid);
		include tpl('ad_list', $module);
	break;
	case 'stats':
		$sfields = array('按条件', '会员', 'IP', '客户端', '操作系统', '浏览器');
		$dfields = array('username', 'username', 'ip', 'ua', 'os', 'bs');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$ip = isset($ip) ? $ip : '';
		$pc = isset($pc) ? intval($pc) : -1;
		$robot = isset($robot) ? $robot : '';
		$username = isset($username) ? $username : '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$aid or $aid = '';
		$pid or $pid = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND addtime>=$fromtime";
		if($totime) $condition .= " AND addtime<=$totime";
		if($ip) $condition .= " AND ip='$ip'";
		if($aid) $condition .= " AND aid=$aid";
		if($pid) $condition .= " AND pid=$pid";
		if($pc > -1) $condition .= " AND pc=$pc";
		if($username) $condition .= " AND username='$username'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}ad_stats WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}ad_stats WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$r['addtime'] = timetodate($r['addtime'], 6);
			$lists[] = $r;
		}
		include tpl('ad_stats', $module);
	break;
	case 'add_place':
		if($submit) {
			if($do->is_place($post)) {
				$do->add_place($post);
				dmsg('广告位添加成功，请添加广告', '?moduleid='.$moduleid.'&file='.$file.'&id='.$do->itemid.'&tm='.($DT_TIME+5));
			} else {
				msg($do->errmsg);
			}
		} else {
			$name = $style = $thumb = $introduce = $price = $code = $width = $height = $template = '';
			$typeid = $open = 1;
			$sign = 0;
			$menuid = 0;
			include tpl('ad_edit_place', $module);
		}
	break;
	case 'edit_place':
		$pid or msg();
		if($submit) {
			if($do->is_place($post)) {
				$do->edit_place($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$r = $do->get_one_place();
			$mid = $r['moduleid'];
			unset($r['moduleid']);
			extract($r);
			$menuid = 1;
			include tpl('ad_edit_place', $module);
		}
	break;
	case 'view':
		$url = $EXT['ad_url'];
		$mob = $EXT['ad_mob'];
		$destoon_task = '';
		$filename = '';
		$ad_moduleid = 0;
		if($pid) {
			$p = $do->get_one_place();
			$head_title = '广告位 ['.$p['name'].'] 预览';
			$title = $p['name'];
			$typeid = $p['typeid'];
		} else if($aid) {
			$a = $do->get_one();
			$head_title = '广告 ['.$a['title'].'] 预览';
			$title = $a['title'];
			$pid = $a['pid'];
			$typeid = $a['typeid'];
			if($typeid > 5) {
				$ad_moduleid = $a['key_moduleid'];
				$ad_catid = $a['key_catid'];
				$ad_kw = $a['key_word'];
			}
		}
		include template('ad', $module);
	break;
	case 'runcode':
		$destoon_task = '';
		$codes = stripslashes($codes);
		include tpl('ad_runcode', $module);
	break;
	case 'delete_place':
		$pids or msg('请选择广告位');
		$do->delete_place($pids);
		dmsg('删除成功', $forward ? $forward : $this_place_forward);
	break;
	case 'order_place':
		$do->order_place($listorder);
		dmsg('排序成功', $forward ? $forward : $this_place_forward);
	break;
	case 'html':
		$all = (isset($all) && $all) ? 1 : 0;
		$one = (isset($one) && $one) ? 1 : 0;
		if(!isset($num)) {
			$num = 100;
			cache_clear_htm('ad', 1);
			$fileroot = DT_CACHE.'/htm/';
			$totime = $DT_TIME + 86400*365*10;
			$result = $db->query("SELECT * FROM {$DT_PRE}ad_place WHERE ads=0 AND code<>''");
			while($r = $db->fetch_array($result)) {
				$default = $r['typeid'] > 5 ? 'ad_'.$r['moduleid'].'_d'.$r['typeid'].'.htm' : 'ad_'.$r['pid'].'_d0.htm';
				$data = '<!--'.$totime.'-->'.$r['code'];
				file_put($fileroot.$default, $data);
				if($EXT['mobile_enable']) {
					$default = substr($default, 0, -4).'_m.htm';
					file_put($fileroot.$default, $data);
				}
				if($r['typeid'] > 1 && $r['typeid'] < 6) {
					$data = 'document.write(\''.dwrite($r['code']).'\');';
					file_put(DT_ROOT.'/file/script/A'.$r['pid'].'.js', $data);
				}
			}
		}
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(aid) AS fid FROM {$DT_PRE}ad");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		isset($sid) or $sid = $fid;
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(aid) AS tid FROM {$DT_PRE}ad");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		$_moduleid = $moduleid;
		if($fid <= $tid) {
			$_result = $db->query("SELECT * FROM {$DT_PRE}ad WHERE aid>=$fid ORDER BY aid LIMIT 0,$num");
			if($db->affected_rows($_result)) {
				while($a = $db->fetch_array($_result)) {
					$aid = $a['aid'];
					tohtml('ad', $module);
				}
				$aid += 1;
			} else {
				$aid = $fid + $num;
			}
		} else {
			if($all) dheader('?moduleid=3&file=announce&action=html&all=1&one='.$one);
			dmsg('生成成功', "?moduleid=$_moduleid&file=$file");
		}
		msg('ID从'.$fid.'至'.($aid-1).'[广告]生成成功'.progress($sid, $fid, $tid), "?moduleid=$_moduleid&file=$file&action=$action&sid=$sid&fid=$aid&tid=$tid&num=$num&all=$all&one=$one", 0);
	break;
	default:
		$sorder  = array('结果排序方式', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序', '广告价格降序', '广告价格升序', '广告数量降序', '广告数量升序', '广告宽度降序', '广告宽度升序', '广告高度降序', '广告高度升序');
		$dorder  = array('listorder DESC,pid DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'eidttime ASC', 'price DESC', 'price ASC', 'ads DESC', 'ads ASC', 'width DESC', 'width ASC', 'height DESC', 'height ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($typeid) or $typeid = 0;
		$width = isset($width) ? intval($width) : '';
		$width or $width = '';
		$height = isset($height) ? intval($height) : '';
		$height or $height = '';
		$open = isset($open) ? $open : -1;
		$sign = isset($sign) ? $sign : -1;
		$thumb = isset($thumb) ? intval($thumb) : 0;

		$type_select = dselect($TYPE, 'typeid', '', $typeid);
		$order_select = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($_self) $condition .= " AND editor='$_username'";//SELF
		if($keyword) $condition .= match_kw('name', $keyword);
		if($typeid) $condition .= " AND typeid=$typeid";
		if($width) $condition .= " AND width=$width";
		if($height) $condition .= " AND height=$height";
		if($thumb) $condition .= " AND thumb<>''";
		if($open > -1) $condition .= " AND open=$open";
		if($sign > -1) $condition .= " AND sign=$sign";
		$lists = $do->get_list_place($condition, $dorder[$order]);
		include tpl($action == 'list_place' ? 'ad_list_place' : 'ad', $module);
	break;
}
?>