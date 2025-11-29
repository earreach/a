<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('更新数据', '?file='.$file),
);
if($CFG['cache'] == 'memcache' || $CFG['session'] == 'memcache') $menus[] = array('Memcache', '?file=doctor&action=memcache', ' target="_blank"');
if($CFG['cache'] == 'file') $menus[] = array('SQL缓存', '?file='.$file.'&action=query');
$menus[] = array('网站首页', DT_PATH, ' target="_blank"');
switch($action) {
	case 'cache':
		cache_clear_htm('ad');
		cache_clear_htm('m');
		cache_clear('php', 'dir', 'tpl');
		cache_clear('cat');
		cache_category();
		cache_clear('area');
		cache_area();
		msg('缓存更新成功', '?file='.$file.'&action=module');
	break;
	case 'all':
		dmsg('全站更新成功', '?file='.$file);
	break;
	case 'index':
		tohtml('index');
		msg('网站首页生成成功', '?file='.$file.'&action=all');
	break;
	case 'back':
		$moduleids = 0;
		unset($MODULE[1]);
		unset($MODULE[2]);
		$KEYS = array_keys($MODULE);
		foreach($KEYS as $k => $v) {
			if($v == $mid) { $moduleids = $k; break; }
		}
		msg('['.$MODULE[$mid]['name'].'] 更新成功', '?file='.$file.'&action=module&moduleids='.($moduleids+1));
	break;
	case 'module':
		if(isset($moduleids)) {
			unset($MODULE[1]);
			unset($MODULE[2]);
			$KEYS = array_keys($MODULE);
			if(isset($KEYS[$moduleids])) {
				$bmoduleid = $moduleid = $KEYS[$moduleids];
				if(is_file(DT_ROOT.'/module/'.$MODULE[$moduleid]['module'].'/admin/html.inc.php')) {	
					msg('', '?moduleid='.$moduleid.'&file='.$file.'&action=all&one=1');
				} else {
					msg('['.$MODULE[$bmoduleid]['name'].'] 更新成功', '?file='.$file.'&action='.$action.'&moduleids='.($moduleids+1));
				}
			} else {
				msg('模块更新成功', '?file='.$file.'&action=index');
			}		
		} else {
			$moduleids = 0;
			msg('开始更新模块', '?file='.$file.'&action='.$action.'&moduleids='.$moduleids);
		}
	break;
	case 'start':
		msg('正在开始更新全站', '?file='.$file.'&action=cache');
	break;
	case 'cacheclear':
		if($CFG['cache'] == 'file') dheader('?file='.$file.'&action=clear&job=all');
		$dc->clear();
		msg('缓存更新成功', '?file='.$file);
	break;
	case 'homepage':
		$db->expires = $CFG['db_expires'] = 0;
		tohtml('index');
		$filename = $CFG['com_dir'] ? DT_ROOT.'/'.$DT['index'].'.'.$DT['file_ext'] : DT_CACHE.'/index.inc.html';
		msg('网站首页生成成功 '.(is_file($filename) ? dround(filesize($filename)/1024).'Kb ' : ''), '?file='.$file);
	break;
	case 'template':
		cache_clear('php', 'dir', 'tpl');
		msg('模板缓存更新成功', '?file='.$file);
	break;
	case 'caches':
		isset($step) or $step = 0;
		if($step == 1) {
			cache_clear('module');
			cache_module();
			msg('系统设置更新成功', '?file='.$file.'&action='.$action.'&step='.($step+1));
		} else if($step == 2) {
			cache_clear_htm('ad');
			cache_clear_htm('m');
			msg('广告缓存更新成功', '?file='.$file.'&action='.$action.'&step='.($step+1));
		} else if($step == 3) {
			cache_clear('php', 'dir', 'tpl');
			msg('模板缓存更新成功', '?file='.$file.'&action='.$action.'&step='.($step+1));
		} else if($step == 4) {
			cache_clear('cat');
			cache_category();
			msg('分类缓存更新成功', '?file='.$file.'&action='.$action.'&step='.($step+1));
		} else if($step == 5) {
			cache_clear('area');
			cache_area();
			msg('地区缓存更新成功', '?file='.$file.'&action='.$action.'&step='.($step+1));
		} else if($step == 6) {
			cache_clear('fields');
			cache_fields();
			cache_clear('option');
			msg('自定义字段更新成功', '?file='.$file.'&action='.$action.'&step='.($step+1));
		} else if($step == 7) {
			tohtml('index');
			msg('全部缓存更新成功', '?file='.$file);
		} else {
			cache_clear('group');
			cache_group();
			cache_clear('type');
			cache_type();
			cache_clear('keylink');
			cache_keylink();
			cache_pay();
			cache_weixin();
			cache_banip();
			cache_banword();
			cache_bancomment();
			msg('正在开始更新缓存', '?file='.$file.'&action='.$action.'&step='.($step+1));
		}
	break; 
	case 'show':
		$data = '';
		if(is_md5($cid)) $data = $dc->get($cid);
		include tpl('header');
		echo '<textarea style="width:98%;height:'.(DT_DEBUG ? 550 : 583).'px;border:none;outline:none;">';
		print_r($data);
		echo '</textarea>';
	include tpl('footer');
	break;
	case 'clear':
		$condition = $job == 'all' ? '1' : 'totime<'.DT_TIME;
		$total = isset($total) ? intval($total) : 0;
		$i = 0;
		$num = 1000;
		$result = $db->query("SELECT * FROM {$DT_PRE}cache WHERE {$condition} LIMIT 0,$num");
		while($r = $db->fetch_array($result)) {
			$cid = $r['cacheid'];
			if(is_md5($cid)) {
				file_del(DT_CACHE.'/php/'.substr($cid, 0, 2).'/'.$cid.'.php');
				$db->query("DELETE FROM {$DT_PRE}cache WHERE cacheid='$cid'");
				$total++;
				$i++;
			}
		}
		if($i > 1) msg('已清理'.$total.'条，系统将自动继续...', '?file='.$file.'&action='.$action.'&job='.$job.'&total='.$total, 0);
		dir_delete(DT_CACHE.'/php/');
		dmsg('清理成功', '?file='.$file.'&action=query');
	break;
	case 'delete':
		isset($cid) or $cid = '';
		$cids = is_array($cid) ? $cid : array($cid);
		foreach($cids as $cid) {
			if(is_md5($cid)) {
				file_del(DT_CACHE.'/php/'.substr($cid, 0, 2).'/'.$cid.'.php');
				$db->query("DELETE FROM {$DT_PRE}cache WHERE cacheid='$cid'");
			}
		}
		dmsg('删除成功', $forward);
	break;
	case 'query':
		include DT_ROOT.'/include/module.func.php';
		$table = $DT_PRE.'cache';
		$sorder  = array('排序方式', '时间降序', '时间升序');
		$dorder  = array('totime DESC', 'totime DESC', 'totime ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$status = isset($status) ? intval($status) : 0;

		isset($fromdate) or $fromdate = '';
		$fromtime = is_date($fromdate) ? str_replace('-', '', $fromdate) : '';
		isset($todate) or $todate = '';
		$totime = is_date($todate) ? str_replace('-', '', $todate) : '';

		$order_select = dselect($sorder, 'order', '', $order);

		$condition = '1';
		if($fromtime) $condition .= " AND totime>=$fromtime";
		if($totime) $condition .= " AND totime<=$totime";
		if($status == 1) $condition .= " AND totime<$DT_TIME";
		if($status == 2) $condition .= " AND totime>$DT_TIME";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		$lists = array();
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			$cid = $r['cacheid'];
			$php = DT_CACHE.'/php/'.substr($cid, 0, 2).'/'.$cid.'.php';
			$size = filesize($php);
			$r['size'] = $size > 0 ? dround($size/1024, 2).'K' : '0K';
			$r['todate'] = timetodate($r['totime'], 6);
			$left = $r['totime'] - DT_TIME;
			$r['left'] = $left > 0 ? sectoread($left) : '<span class="f_red">过期</span>';
			$r['expire'] = $left > 0 ? 0 : 1;
			$lists[] = $r;
		}
		include tpl('html_query');
	break;
	default:
		include tpl('html');
	break;
}
?>