<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
define('MANAGE_ADMIN', true);
$AREA or $AREA = cache_read('area.php');
require DT_ROOT.'/module/destoon/admin/admin.class.php';
$do = new admin;
$menus = array (
    array('添加管理员', '?moduleid='.$moduleid.'&file='.$file.'&action=add'),
    array('管理员列表', '?moduleid='.$moduleid.'&file='.$file),
    array('在线管理员', '?moduleid='.$moduleid.'&file='.$file.'&action=online'),
    array('后台日志', '?moduleid='.$moduleid.'&file='.$file.'&action=log'),
    array('临时授权', '?moduleid='.$moduleid.'&file='.$file.'&action=temp'),
);
$this_forward = '?file='.$file;
switch($action) {
	case 'add':
		if($submit) {
			$admin = $admin == 1 ? 1 : 2;
			if($do->set_admin($username, $admin, $role, $aid)) {
				$userid = $do->userid;
				$r = $do->get_one($userid, 0);
				if($r['admin'] == 2) {
					foreach($MODULE as $m) {
						if(isset($roles[$m['moduleid']])) {
							$right = array();
							$right['title'] = $m['name'].'管理';
							$right['url'] = '?moduleid='.$m['moduleid'];
							$do->add($userid, $right, $admin);
						}
					}
					if(isset($roles['database'])) {
						$right = array();
						$right['title'] = '数据库管理';
						$right['url'] = '?file=database';
						$do->add($userid, $right, $admin);
					}
					if(isset($roles['template'])) {
						$right = array();
						$right['title'] = '模板管理';
						$right['url'] = '?file=template';
						$do->add($userid, $right, $admin);
						$right = array();
						$right['title'] = '风格管理';
						$right['url'] = '?file=skin';
						$do->add($userid, $right, $admin);
						$right = array();
						$right['title'] = '标签向导';
						$right['url'] = '?file=tag';
						$do->add($userid, $right, $admin);
					}
					$do->cache_right($userid);
					$do->cache_panel($userid);
				}
				msg('管理员添加成功，下一步请分配权限和管理面板', '?file='.$file.'&id='.$userid.'&tm='.($DT_TIME+5));
			}
			msg($do->errmsg);
		} else {
			isset($username) or $username = '';
			include tpl('admin_edit');
		}
	break;
	case 'edit':
		if($submit) {
			$admin = $admin == 1 ? 1 : 2;
			if($do->set_admin($username, $admin, $role, $aid)) {
				$userid = $do->userid;
				$r = $do->get_one($userid, 0);
				$userid = $r['userid'];
				if($r['admin'] == 2) {
					$do->cache_right($userid);
					$do->cache_panel($userid);
				}
				dmsg('修改成功', '?file='.$file);
			}
			msg($do->errmsg);
		} else {
			if(!$userid) msg();
			$user = $do->get_one($userid, 0);
			include tpl('admin_edit');
		}
	break;
	case 'temp':
		$link = '';
		$link_user = $_username;
		$link_minute = 60;
		$link_ip = '';
		if($submit) {
			if($_founder) {
				$username = trim($username);
				if($username == $_username) {
					//
				} else {
					check_name($username) or msg('用户格式错误');
					$user = userinfo($username);
					$user or msg('用户'.$username.'不存在');
					($user['groupid'] == 1 && $user['admin'] > 0)  or msg('用户'.$username.'非管理员');
				}
				$link_user = $username;
			} else {
				$link_user = $username =  $_username;
			}
			$minute = intval($minute);
			if($minute < 10) $minute = 10;
			if($minute > 600) $minute = 30;
			$link_minute = $minute;
			$ip = trim($ip);
			$link_ip = $ip;
			$expiry = $minute*60;
			$auth = $username.'|'.(DT_TIME + $expiry).'|'.$ip;
			$link = DT_PATH.basename(get_env('self')).'?file=login&action=temp&auth='.encrypt($auth, DT_KEY.'TMPA', $expiry);
			if(strpos(get_env('self'), '/admin'.DT_EXT) !== false) $link = '后台地址admin.php未修改，暂不支持临时授权';
		}
		include tpl('admin_temp');
	break;
	case 'delete':
		if($do->delete_admin($username)) dmsg('撤销成功', $this_forward);
		msg($do->errmsg);
	break;
	case 'right':
		if(!$userid) msg();
		$do->userid = $userid;
		$user = $do->get_one($userid, 0);
		($user && $user['groupid'] == 1) or msg('管理员不存在');
		if($job == 'delete') {
			$itemid or msg('请选择项目');
			foreach($itemid as $id) {
				$do->delete($id, $right[$id]);
			}
			dmsg('删除成功', $forward);
		} else {
			if(isset($update)) {
				$right[0]['action'] = isset($right[0]['action']) ? implode('|', $right[0]['action']) : '';
				$right[0]['catid'] = isset($right[0]['catid']) ? implode('|', $right[0]['catid']) : '';
				if($do->update($userid, $right, $user['admin'])) dmsg('更新成功', '?file='.$file.'&action='.$action.'&job='.$job.'&userid='.$userid);
				msg($do->errmsg);
			}
			$menus = array (
				array('面板设置', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&userid='.$userid.'&job=panel'),
				array('权限设置', '?moduleid='.$moduleid.'&file='.$file.'&action='.$action.'&userid='.$userid.'&job=right'),
			);
			$username = $user['username'];
			$R = $do->get_right($userid);
			$P = $do->get_panel($userid);
			include tpl('admin_'.($job == 'right' ? 'right' : 'panel'));
		}
	break;
	case 'ajax':
		@include DT_ROOT.'/module/'.$MODULE[$mid]['module'].'/admin/config.inc.php';
		if(isset($fi)) {
			if(isset($RT) && isset($RT['action'][$fi])) {
				$action_select = '<select name="right[0][action][]" size="2" multiple  style="height:200px;width:150px;"><option value="">选择动作[按Ctrl键多选]</option>';
				foreach($RT['action'][$fi] as $k=>$v) {
					$action_select .= '<option value="'.$k.'">'.$v.'['.$k.']</option>';
				}
				$action_select .= '</select>';
				echo $action_select;
			} else {
				echo '0';
			}
		} else {
			if(isset($RT)) {
				$file_select = '<select name="right[0][file]" size="2" style="height:200px;width:150px;" onchange="get_action(this.value, '.$mid.');"><option value="">选择文件[单选]</option>';
				foreach($RT['file'] as $k=>$v) {
					$file_select .= '<option value="'.$k.'">'.$v.'['.$k.']</option>';
				}
				$file_select .= '</select>';
				echo $file_select.'|';
				if($CT) {
					$CATEGORY = cache_read('category-'.$mid.'.php');
					echo '<select name="right[0][catid][]" size="2" multiple style="height:200px;width:300px;">';
					echo '<option>选择分类多选[按Ctrl键多选]</option>';
					foreach($CATEGORY as $c) {
						if($c['parentid'] == 0) echo '<option value="'.$c['catid'].'">'.$c['catname'].'</option>';
					}
					echo '</select>';
				} else {
					echo '0';
				}
			} else {
				echo '0|0';
			}
		}
	break;
	case 'online':
		$lastime = $DT_TIME - $DT['online'];
		$db->query("DELETE FROM {$DT_PRE}admin_online WHERE lasttime<$lastime");
		$sid = session_id();
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}admin_online ORDER BY lasttime DESC");
		while($r = $db->fetch_array($result)) {
			$r['lasttime'] = timetodate($r['lasttime'], 'H:i:s');
			$lists[] = $r;
		}
		include tpl('admin_online');
	break;
	case 'clear':
		$time = $DT_TODAY - 30*86400;
		$db->query("DELETE FROM {$DT_PRE}admin_log WHERE logtime<$time");
		dmsg('清理成功', '?file='.$file.'&action=log');
	break;
	case 'log':
		$F = array(
			'index' => '列表',
			'setting' => '设置',
			'category' => '栏目管理',
			'type' => '分类管理',
			'keylink' => '关联链接',
			'split' => '数据拆分',
			'html' => '更新数据',
			'panel' => '定义面板',
			'module' => '模块管理',
			'area' => '地区管理',
			'admin' => '管理设置',
			'database' => '数据维护',
			'data' => '数据处理',
			'file' => '文件管理',
			'md5' => '文件校验',
			'scan' => '文件查找',
			'template' => '模板管理',
			'tag' => '标签向导',
			'skin' => '风格管理',
			'stats' => '流量统计',
			'upload' => '上传记录',
			'404' => '404日志',
			'patch' => '文件备份',
			'keyword' => '搜索记录',
			'question' => '问题验证',
			'banword' => '词语过滤',
			'repeat' => '重名检测',
			'banip' => '禁止访问',
			'fetch' => '单页采编',
			'word' => '编辑助手',
			'bdpush' => '百度推送',
			'doctor' => '系统体检',
			'login' => '后台登录',
			'ip' => 'IP查询',
			'property' => '分类属性',

			'contact' => '联系会员',
			'group' => '会员组',
			'validate' => '资料审核',
			'grade' => '会员升级',
			'weixin' => '微信管理',
			'oauth' => '一键登录',

			'child' => '子账号',
			'agent' => '代理分销',
			'stock' => '库存管理',
			'honor' => '荣誉资质',
			'news' => '公司新闻',
			'page' => '公司单页',
			'link' => '友情链接',
			'style' => '公司模板',

			'record' => '资金管理',
			'credit' => '积分管理',
			'sms' => '短信管理',
			'charge' => '支付记录',
			'cash' => '提现记录',
			'pay' => '信息支付',
			'award' => '信息打赏',
			'promo' => '优惠促销',
			'deposit' => '保证金',
			'card' => '充值卡',

			'fans' => '粉丝关注',
			'chat' => '在线交谈',
			'message' => '站内信件',
			'sendmail' => '电子邮件',
			'sendsms' => '手机短信',
			'sendpush' => '消息推送',
			'ask' => '客服中心',
			'friend' => '会员好友',
			'favorite' => '站内收藏',
			'history' => '浏览历史',
			'address' => '收货地址',
			'alert' => '贸易提醒',
			'validate' => '资料认证',
			'mail' => '邮件订阅',
			'loginlog' => '登录日志',

			'spread' => '排名推广',
			'ad' => '广告管理',
			'announce' => '公告管理',
			'webpage' => '单页管理',
			'comment' => '评论管理',
			'guestbook' => '留言管理',
			'vote' => '投票管理',
			'gift' => '积分换礼',
			'poll' => '票选管理',
			'form' => '表单管理',
			'spider' => '数据采集',

			'expert' => '知道专家',
			'answer' => '知道答案',
			'product' => '行情产品',
			'price' => '报价',
			'order' => '订单管理',
		);
		$A = array(
			'index' => '列表',
			'add' => '添加',
			'edit' => '修改',
			'delete' => '<span class="f_red">删除</span>',
			'clear' => '<span class="f_red">清理</span>',
			'check' => '待审核',
			'reject' => '未通过',
			'expire' => '已过期',
			'recycle' => '回收站',
			'level' => '级别',
			'order' => '排序',
			'html' => '更新',
			'update' => '更新',
			'send' => '发送',
			'move' => '移动',
		);
		$sfields = array('按条件', '网址', '管理员', 'IP', '端口');
		$dfields = array('qstring', 'qstring', 'username', 'ip', 'port');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$ip = isset($ip) ? $ip : '';
		(isset($username) && check_name($username)) or $username = '';
		(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
		$fromtime = $fromdate ? datetotime($fromdate) : 0;
		(isset($todate) && is_time($todate)) or $todate = '';
		$totime = $todate ? datetotime($todate) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);

		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($fromtime) $condition .= " AND logtime>=$fromtime";
		if($totime) $condition .= " AND logtime<=$totime";
		if($ip) $condition .= " AND ip='$ip'";
		if($username) $condition .= " AND username='$username'";
		if($page > 1 && $sum) {
			$items = $sum;
		} else {	
			$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}admin_log WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);		
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}admin_log WHERE {$condition} ORDER BY itemid DESC LIMIT {$offset},{$pagesize}");
		while($r = $db->fetch_array($result)) {
			parse_str($r['qstring'], $t);
			$m = isset($t['moduleid']) ? $t['moduleid'] : 1;
			$r['mid'] = $m;
			$r['module_name'] = $MODULE[$m]['name'];
			$r['file'] = $f = isset($t['file']) ? $t['file'] : 'index';
			$r['file_name'] = isset($F[$f]) ? $F[$f] : '';
			$r['action'] = $a = isset($t['action']) ? $t['action'] : '';
			$r['action_name'] = isset($A[$a]) ? $A[$a] : '';
			if(!$r['file_name'] || !$r['action_name']) {
				include DT_ROOT.'/module/'.$MODULE[$m]['module'].'/admin/config.inc.php';
				if(!$r['file_name'] && isset($RT['file'][$f])) $r['file_name'] = $RT['file'][$f];
				if(!$r['action_name'] && isset($RT['action'][$f][$a])) $r['action_name'] = $RT['action'][$f][$a];
			}
			$i = isset($t['itemid']) ? $t['itemid'] : (isset($t['userid']) ? $t['userid'] : '');
			if(!$i) {
				$qstr = $r['qstring'];
				$qstr = str_replace('moduleid=', '', $qstr);
				$qstr = str_replace('mid=', '', $qstr);
				if(strpos($qstr, 'id=') !== false) $i = cutstr($qstr, 'id=', '&');
			}
			if(!is_numeric($i)) $i = '';
			$r['itemid'] = $i ? $i : '';
			$r['logdate'] = timetodate($r['logtime'], 6);
			$lists[] = $r;
		}
		include tpl('admin_log');
	break;
	default:
		$sfields = array('按条件', '用户名', '姓名', '角色');
		$dfields = array('username', 'username', 'truename', 'role');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sorder  = array('结果排序方式', '登录时间降序', '登录时间升序', '登录次数降序', '登录次数升序', '会员ID降序', '会员ID升序');
		$dorder  = array('admin ASC,userid ASC', 'logintime DESC', 'logintime ASC', 'logintimes DESC', 'logintimes ASC', 'userid DESC', 'userid ASC');
		isset($order) && isset($dorder[$order]) or $order = 0;
		$type = isset($type) ? intval($type) : 0;
		$areaid = isset($areaid) ? intval($areaid) : 0;

		$fields_select = dselect($sfields, 'fields', '', $fields);
		$order_select  = dselect($sorder, 'order', '', $order);

		$condition = 'groupid=1 AND admin>0';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($type) $condition .= " AND admin=$type";
		if($areaid) $condition .= ($AREA[$areaid]['child']) ? " AND aid IN (".$AREA[$areaid]['arrchildid'].")" : " AND aid=$areaid";
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('admin');
	break;
}
?>