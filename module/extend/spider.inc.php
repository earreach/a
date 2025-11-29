<?php
defined('DT_ADMIN') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/spider.class.php';
$do = new spider();
$menus = array (
    array('添加采集', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('采集列表', '?moduleid='.$moduleid.'&file='.$file),
    array('网址管理', '?moduleid='.$moduleid.'&file='.$file.'&action=url'),
    array('数据管理', '?moduleid='.$moduleid.'&file='.$file.'&action=data'),
);
if($_catids || $_areaids) require DT_ROOT.'/module/destoon/admin/check.inc.php';
$table = $DT_PRE.'spider';
$dstatus = array('<span class="f_gray">等待采集</span>', '<span class="f_red">采集失败</span>', '<span class="f_blue">等待发布</span>', '<span class="f_green">发布成功</span>', '<span class="f_red">发布失败');
switch($action) {
	case 'add':
		if($submit) {
			if($do->pass($post)) {
				$do->add($post);
				dmsg('添加成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$tables = $do->get_tables();
			foreach($do->fields as $v) {
				isset($$v) or $$v = '';
			}
			$addtime = timetodate($DT_TIME);
			$typeid = 0;
			$menuid = 0;
			include tpl('spider_edit', $module);
		}
	break;
	case 'edit':
		$itemid or msg();
		$do->itemid = $itemid;
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$tables = $do->get_tables();
			extract($do->get_one());
			$addtime = timetodate($addtime);
			$menuid = 1;
			include tpl('spider_edit', $module);
		}
	break;
	case 'delete':
		$itemid or msg('请选择数据');
		$do->delete($itemid, $job);
		dmsg('删除成功', $forward);
	break;
	case 'status':
		$itemid or msg('请选择数据');
		$status = intval($status);
		$do->status($itemid, $status);
		dmsg('设置成功', $forward);
	break;
	case 'clear':
		$time = $DT_TODAY - 30*86400;
		if($job == 'data') {
			$db->query("DELETE FROM {$table}_data WHERE addtime<$time");
		} else {
			$db->query("DELETE FROM {$table}_url WHERE addtime<$time");
		}
		dmsg('清理成功', $forward);
	break;
	case 'view':
		$itemid or msg();
		$r = $db->get_one("SELECT * FROM {$table}_data WHERE itemid=$itemid");
		if(!$r) $r = $db->get_one("SELECT * FROM {$table}_url WHERE itemid=$itemid");
		$r or msg('数据不存在');
		$s = $do->get($r['sid']);
		$setting = $s['setting'];
		$config = $s['config'];
		$linkurl = $r['linkurl'];
		if(!isset($r['html']) || !$r['html']) $r['html'] = $do->dcurl($linkurl, $config);
		if($config['func']) include DT_ROOT.'/api/spider/'.$config['func'].'.inc.php';
		$post = $do->get_data($r['html'], $r['sid']);
		if($config['inc']) include DT_ROOT.'/api/spider/'.$config['inc'].'.inc.php';
		$html = dhtmlspecialchars($r['html']);
		if($s['mid']) {
			$_module = $module;
			$_moduleid = $moduleid;
			$moduleid = $s['mid'];
			$module = $MODULE[$moduleid]['module'];
			$MOD = cache_read('module-'.$moduleid.'.php');
			include DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
			$po = new $module($moduleid);
			$msg = $po->pass($post) ? 'ok' : $po->errmsg;
			$module = $_module;
			$moduleid = $_moduleid;
		}
		include tpl('spider_view', $module);
	break;
	case 'start':
		if($itemid) {
			$ids = is_array($itemid) ? implode(',', $itemid) : $itemid;
		} else {
			$ids = 0;
		}
		$all = isset($all) ? intval($all) : 0;		
		if($job == 'data') {
			dheader('?moduleid='.$moduleid.'&file='.$file.'&action=post&all='.$all.'&ids='.$ids);
		} else if($job == 'urls') {
			dheader('?moduleid='.$moduleid.'&file='.$file.'&action=show&all='.$all.'&ids='.$ids);
		} else if($job == 'post') {
			dheader('?moduleid='.$moduleid.'&file='.$file.'&action=post&all='.$all.'&sid='.$ids);
		} else if($job == 'show') {
			dheader('?moduleid='.$moduleid.'&file='.$file.'&action=show&all='.$all.'&sid='.$ids);
		} else {
			dheader('?moduleid='.$moduleid.'&file='.$file.'&action=list&all='.$all.'&sid='.$ids);
		}
	break;
	case 'list':
		$all = isset($all) ? intval($all) : 0;
		$oks = isset($oks) ? intval($oks) : 0;
		$pid = isset($pid) ? intval($pid) : 0;
		isset($sid) or $sid = '';
		$condition = "setting<>''";
		if($sid) $condition .= " AND itemid IN (".$sid.")";
		if(!isset($fid)) {
			$r = $db->get_one("SELECT min(itemid) AS fid FROM {$table} WHERE {$condition}");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		if(!isset($tid)) {
			$r = $db->get_one("SELECT max(itemid) AS tid FROM {$table} WHERE {$condition}");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		$name = '';
		if($fid <= $tid) {
			$r = $db->get_one("SELECT * FROM {$table} WHERE {$condition} AND itemid>=$fid ORDER BY itemid ASC");
			if($r) {
				$itemid = $r['itemid'];
				$name = $r['title'];
				$list_url = '';
				if($r['setting']) {
					$setting = unserialize($r['setting']);
					$config = $setting[0];
					if($config['page_from'] && $config['page_to'] && $config['page_max'] > 1) {
						$pid or $pid = $config['page_max'];
						$list_url = $pid > 1 ? $config['page_from'].$pid.$config['page_to'] : $r['linkurl'];
					} else {
						$list_url = $r['linkurl'];
					}
				}
				if($list_url) {//同步 cron/spider
					$list_html = $do->dcurl($list_url, $config);
					$lists = $do->get_url($list_html, $config);
					if($lists) $oks = $oks + $do->save_list($lists, $itemid);
					$pid--;
					if($pid > 0) msg(($name ? $name.' ' : '').'第'.($pid + 1).'页 网址抓取成功<br/>当前已抓取 '.$oks.' 条网址'.progress(0, $config['page_max'] - $pid, $config['page_max']), "?moduleid=$moduleid&file=$file&action=$action&fid=$itemid&tid=$tid&pid=$pid&oks=$oks&all=$all&sid=$sid", intval($config['time']));
				}
			}
			$itemid += 1;
			msg(($name ? $name.' ' : '').' 网址抓取成功', "?moduleid=$moduleid&file=$file&action=$action&fid=$itemid&tid=$tid&oks=$oks&all=$all&sid=$sid");
		}
		if($all > 1) msg('网址抓取成功，开始采集内容', "?moduleid=$moduleid&file=$file&action=show&all=$all&sid=$sid");
		dmsg('网址抓取成功', "?moduleid=$moduleid&file=$file&action=url");
	break;
	case 'show':
		$all = isset($all) ? intval($all) : 0;
		$oks = isset($oks) ? intval($oks) : 0;
		$kos = isset($kos) ? intval($kos) : 0;
		isset($ids) or $ids = '';
		isset($sid) or $sid = '';
		$condition = "status=0";
		if($ids) $condition .= " AND itemid IN (".$ids.")";
		if($sid) $condition .= " AND sid IN (".$sid.")";
		if(!isset($tid)) $tid = $db->count($table.'_url', $condition);
		$r = $db->get_one("SELECT * FROM {$table}_url WHERE {$condition} ORDER BY itemid ASC");
		if($r) {
			if(!isset($time)) {
				$s = $do->get($r['sid']);
				$time = intval($s['config']['time']);
			}
			$res = $do->save_show($r);
			if($res) { $oks++; } else { $kos++; }
			msg(dsubstr($r['title'], 40, '...').'<br/>内容采集'.($res ? '成功' : '失败').'，当前成功 '.$oks.' 条，失败 '.$kos.' 条'.progress(0, $oks + $kos, $tid), "?moduleid=$moduleid&file=$file&action=$action&time=$time&tid=$tid&oks=$oks&kos=$kos&all=$all&sid=$sid&ids=$ids", intval($time));
		}
		if($all > 2) msg('内容采集成功，开始发布数据', "?moduleid=$moduleid&file=$file&action=post&all=$all&sid=$sid");
		dmsg('内容采集成功', "?moduleid=$moduleid&file=$file&action=data");
	break;
	case 'post':
		$all = isset($all) ? intval($all) : 0;
		$oks = isset($oks) ? intval($oks) : 0;
		$kos = isset($kos) ? intval($kos) : 0;
		isset($ids) or $ids = '';
		isset($sid) or $sid = '';
		$condition = "status=2";
		if($ids) $condition .= " AND itemid IN (".$ids.")";
		if($sid) $condition .= " AND sid IN (".$sid.")";
		if(!isset($tid)) $tid = $db->count($table.'_data', $condition);
		$r = $db->get_one("SELECT * FROM {$table}_data WHERE {$condition} ORDER BY itemid ASC");
		if($r) {//同步 cron/poster
			$itemid = $r['itemid'];
			$s = $do->get($r['sid']);
			$setting = $s['setting'];
			$config = $s['config'];
			if($config['func']) include_once DT_ROOT.'/api/spider/'.$config['func'].'.func.php';
			$post = $do->get_data($r['html'], $r['sid']);
			if($config['inc']) include DT_ROOT.'/api/spider/'.$config['inc'].'.inc.php';
			$post = daddslashes($post);
			$id = 0;
			$note = '';
			$status = 3;
			if($s['mid']) {
				$_table = $table;
				$_moduleid = $moduleid;

				$moduleid = $s['mid'];
				$module = $MODULE[$moduleid]['module'];
				$MOD = cache_read('module-'.$moduleid.'.php');
				if($config['save']) $MOD['save_remotepic'] = $post['save_remotepic'] = $post['thumb_no'] = 1;
				if($config['clear']) $MOD['clear_link'] = $post['clear_link'] = 1;
				require DT_ROOT.'/module/'.$module.'/global.func.php';
				if($moduleid > 4) {
					$table = $DT_PRE.$module.'_'.$moduleid;
					$table_data = $DT_PRE.$module.'_data_'.$moduleid;
					if($module == 'sell') $table_search = $DT_PRE.$module.'_search_'.$moduleid;
				}
				include DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
				$po = new $module($moduleid);
				if(in_array('thumbs', $po->fields)) {
					$thumbs = array();
					if(isset($post['thumb']) && is_url($post['thumb'])) $thumbs[] = $post['thumb'];
					if(isset($post['thumb1']) && is_url($post['thumb1'])) $thumbs[] = $post['thumb1'];
					if(isset($post['thumb2']) && is_url($post['thumb2'])) $thumbs[] = $post['thumb2'];
					if(isset($post['thumbs']) && $post['thumbs']) {
						if(is_array($post['thumbs'])) {
							foreach($post['thumbs'] as $v) {
								if(is_url($v) && !in_array($v, $thumbs)) $thumbs[] = $v;
							}
						} else {
							foreach(explode('|', $post['thumbs']) as $v) {
								if(is_url($v) && !in_array($v, $thumbs)) $thumbs[] = $v;
							}
						}
					}
					$post['thumbs'] = $thumbs;
				}
				if($po->pass($post)) $id = $po->add($post);
				if($id) {
					if($moduleid == 2) {
						$FD = cache_read('fields-member.php');
						if($FD) {
							$fields = array();
							foreach($FD as $k=>$v) {
								$kk = $v['name'];
								if(isset($post[$kk])) $fields[$kk] = $post[$kk];
							}
							if($fields) $db->query("UPDATE {$DT_PRE}member SET ".arr2sql($fields, 1)." WHERE userid=$id");
						}
						$FD = cache_read('fields-company.php');
						if($FD) {
							$fields = array();
							foreach($FD as $k=>$v) {
								$kk = $v['name'];
								if(isset($post[$kk])) $fields[$kk] = $post[$kk];
							}
							if($fields) $db->query("UPDATE {$DT_PRE}company SET ".arr2sql($fields, 1)." WHERE userid=$id");
						}
					} else {
						$FD = cache_read('fields-'.substr($table, strlen($DT_PRE)).'.php');
						if($FD) {
							$fields = array();
							foreach($FD as $k=>$v) {
								$kk = $v['name'];
								if(isset($post[$kk])) $fields[$kk] = $post[$kk];
							}
							if($fields) $db->query("UPDATE {$table} SET ".arr2sql($fields, 1)." WHERE itemid=$id");
						}
					}
					$oks++;
				} else {
					$kos++;
					$note = addslashes($po->errmsg);
					$status = 4;
				}				

				$moduleid = $_moduleid;
				$table = $_table;
			} else {
				if($post) {
					$db->query("INSERT INTO {$s[tb]} ".arr2sql($post, 0));
					$id = $db->insert_id();
					$oks++;
				} else {
					$kos++;
				}
			}
			$db->query("UPDATE {$table}_data SET status=$status,posttime=$DT_TIME,tid=$id,note='$note' WHERE itemid=$itemid");
			$db->query("UPDATE {$table}_url SET status=$status,posttime=$DT_TIME WHERE itemid=$itemid");
			msg(dsubstr($r['title'], 40, '...').'<br/>内容发布'.($status == 3 ? '成功' : '失败').'，当前成功 '.$oks.' 条，失败 '.$kos.' 条'.progress(0, $oks + $kos, $tid), "?moduleid=$moduleid&file=$file&action=$action&tid=$tid&oks=$oks&kos=$kos&all=$all&sid=$sid&ids=$ids", 0);
		}
		dmsg('内容发布成功', "?moduleid=$moduleid&file=$file&action=data");
	break;
	case 'setting':
		$itemid or msg();
		$do->itemid = $itemid;
		$s = $do->get_one();
		$s or msg('采集信息不存在');
		$menus = array (
			array('导入规则', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid.'&job=import'),
			array('规则设置', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid),
			array('导出规则', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid.'&job=export\', \'导出规则\');'),
			array('测试采集', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid.'&job=preview\', \'测试采集\');'),
		);
		if($job == 'import') {
			include tpl('spider_import', $module);
		} else if($job == 'export') {
			($s['setting']) or msg('规则设置不完整，请先完成配置', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid);
			$rule = $do->export($s['setting']);
			include tpl('spider_export', $module);
		} else if($job == 'preview') {
			$setting = unserialize($s['setting']);
			$config = isset($setting[0]) ? $setting[0] : array();
			$list_url = (isset($list_url) && is_url($list_url)) ? $list_url : $s['linkurl'];
			$show_url = (isset($show_url) && is_url($show_url)) ? $show_url : '';
			$show_html = $content = '';
			$list_html = $do->dcurl($list_url, $config);
			$lists = $do->get_url($list_html, $config);
			if($lists && !$show_url) $show_url = $lists[0]['linkurl'];
			$msg = '';
			$post = array();
			if(is_url($show_url)) {
				$show_html = $do->dcurl($show_url, $config);
				if($config['func']) include DT_ROOT.'/api/spider/'.$config['func'].'.inc.php';
				$post = $do->get_data($show_html, $itemid);
				if($config['inc']) include DT_ROOT.'/api/spider/'.$config['inc'].'.inc.php';
				if($s['mid']) {
					$_module = $module;
					$_moduleid = $moduleid;
					$moduleid = $s['mid'];
					$module = $MODULE[$moduleid]['module'];
					$MOD = cache_read('module-'.$moduleid.'.php');
					include DT_ROOT.'/module/'.$module.'/'.$module.'.class.php';
					$po = new $module($moduleid);
					$msg = $po->pass($post) ? 'ok' : $po->errmsg;
					$module = $_module;
					$moduleid = $_moduleid;
				}
			}
			$list_html = dhtmlspecialchars($list_html);
			$show_html = dhtmlspecialchars($show_html);
			include tpl('spider_preview', $module);
		} else {
			if($submit) {
				$setting = (isset($_REQUEST['setting']) && $_REQUEST['setting']) ? $_REQUEST['setting'] : strip_sql($setting, 0);
				$do->save($setting);
				dmsg('保存成功', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&itemid='.$itemid);
			} else {
				if(isset($rule)) {
					$tmp = $do->import((isset($_REQUEST['rule']) && $_REQUEST['rule']) ? $_REQUEST['rule'] : strip_sql($rule, 0));
					if($tmp) $s['setting'] = $tmp;
				}
				$setting = array();
				if($s['setting']) $setting = unserialize($s['setting']);
				if(isset($setting[0])) extract($setting[0]);
				foreach(array('encode', 'page_from', 'page_to', 'page_max', 'list_from', 'list_to', 'show_basehref', 'show_include', 'show_exclude', 'text_from', 'text_to', 'html_include', 'html_exclude', 'status', 'func', 'inc', 'agent', 'ip', 'cookie', 'header', 'time') as $v) {
					isset($$v) or $$v = '';
				}
				isset($clear) or $clear = 1;
				isset($save) or $save = 1;
				$lists = array();
				if($s['mid']) {
					$lists = $do->get_fields(get_table($s['mid']));
					if($s['mid'] == 2) {
						$lists = $lists + $do->get_fields(DT_PRE.'member_misc') + $do->get_fields(DT_PRE.'company') + $do->get_fields(DT_PRE.'company_data');
						foreach(array('userid', 'admin', 'role', 'aid', 'fans', 'follow', 'award', 'comments', 'keyword', 'linkurl') as $v) {
							if(isset($lists[$v])) unset($lists[$v]);
						}
					} else {
						foreach(array('itemid', 'userid', 'areaid', 'linkurl', 'keyword', 'pptword', 'award', 'orders', 'comments', 'editdate', 'adddate', 'filepath', 'template') as $v) {
							if(isset($lists[$v])) unset($lists[$v]);
						}
						if($s['mid'] > 4) $lists['content'] = '内容';
					}
				} else {
					$lists = $do->get_fields($s['tb']);
				}
				include tpl('spider_setting', $module);
			}
		}
	break;
	case 'data':
		$sfields = array('按条件', '标题', '网址', '目标', '分类', '源码', '数据', '编辑', '备注');
		$dfields = array('title','title','linkurl','name','catname','html','data','editor', '备注');
		$sorder  = array('结果排序方式', '采集时间降序', '采集时间升序', '发布时间降序', '发布时间升序', '采集状态降序', '采集状态升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'posttime DESC', 'posttime ASC', 'status DESC', 'status ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('posttime', 'addtime')) or $datetype = 'addtime';
		$itemid or $itemid = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : -1;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$module_select = module_select('mid', '模块', $mid, '', '1,3,4');
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($status > -1) $condition .= " AND status=$status";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($mid) $condition .= " AND mid='$mid'";
		if($itemid) $condition .= " AND sid=$itemid";
		$lists = $do->get_list_data($condition, $dorder[$order]);
		if($itemid && $condition == "1 AND sid=$itemid" && $page == 1) $db->query("UPDATE {$table} SET datas=$items WHERE itemid=$itemid");
		include tpl('spider_data', $module);
	break;
	case 'url':
		$sfields = array('按条件', '标题', '网址', '编辑');
		$dfields = array('title','title','linkurl','editor');
		$sorder  = array('结果排序方式', '抓取时间降序', '抓取时间升序', '采集时间降序', '采集时间升序', '发布时间降序', '发布时间升序', '采集状态降序', '采集状态升序');
		$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC', 'posttime DESC', 'posttime ASC', 'status DESC', 'status ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		isset($datetype) && in_array($datetype, array('posttime', 'edittime', 'addtime')) or $datetype = 'addtime';
		$itemid or $itemid = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;
		$status = isset($status) && isset($dstatus[$status]) ? intval($status) : -1;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$status_select = dselect($dstatus, 'status', '状态', $status, '', 1, '', 1);
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($status > -1) $condition .= " AND status=$status";
		if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
		if($totime) $condition .= " AND `$datetype`<=$totime";
		if($itemid) $condition .= " AND sid=$itemid";
		$lists = $do->get_list_url($condition, $dorder[$order]);
		if($itemid && $condition == "1 AND sid=$itemid" && $page == 1) $db->query("UPDATE {$table} SET urls=$items WHERE itemid=$itemid");
		include tpl('spider_url', $module);
	break;
	default:
		$sfields = array('按条件', '标题', '网址', '目标', '分类', '编辑', '备注');
		$dfields = array('title','title','linkurl','name','catname','editor','content');
		$sorder  = array('结果排序方式', '采集时间降序', '采集时间升序', '网址总数降序', '网址总数升序', '数据总数降序', '数据总数升序', '添加时间降序', '添加时间升序', '修改时间降序', '修改时间升序');
		$dorder  = array('addtime DESC', 'lasttime DESC', 'lasttime ASC', 'urls DESC', 'urls ASC', 'datas DESC', 'datas ASC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		isset($order) && isset($dorder[$order]) or $order = 0;
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);
		$module_select = module_select('mid', '模块', $mid, '', '1,3,4');
		$condition = '1';
		if($_self) $condition .= " AND editor='$_username'";//SELF
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($mid) $condition .= " AND mid='$mid'";
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('spider', $module);
	break;
}
?>