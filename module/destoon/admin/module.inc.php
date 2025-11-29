<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('添加模块', '?file='.$file.'&action=add'),
    array('模块管理', '?file='.$file),
    array('系统模型', '?file='.$file.'&action=sys'),
    array('更新缓存', '?file='.$file.'&action=cache'),
);
require DT_ROOT.'/include/sql.func.php';
$this_forward = '?update=1&file='.$file;
$mid = isset($mid) ? intval($mid) : 0;
function get_modules() {
	$moduledirs = glob(DT_ROOT.'/module/*');
	$sysmodules = array();
	foreach($moduledirs as $k=>$v) {
		if(is_file($v.'/admin/config.inc.php')) {
			include $v.'/admin/config.inc.php';
			$sysmodules[$MCFG['module']] = $MCFG;
		}
	}
	return $sysmodules;
}
switch($action) {
	case 'add':
		if($submit) {
			if(!$post['name']) msg('请填写模块名称');
			if($post['islink']) {
				if(!$post['linkurl']) msg('请填写链接地址');
			} else {
				$dir = $post['moduledir'];
				$module = $post['module'];
				if(!$module) msg('请选择所属模型');
				$module_cfg = DT_ROOT.'/module/'.$module.'/admin/config.inc.php';
				if(!is_file($module_cfg)) msg('此模型无法安装，请检查');
				include $module_cfg;
				if($MCFG['uninstall'] == false) msg('此模型无法安装，请检查');
				if($MCFG['copy'] == false) {
					$r = $db->get_one("SELECT moduleid FROM {$DT_PRE}module WHERE module='$module' AND islink=0");
					if($r) msg('此模型已经安装过，请检查');
				}
				if(!$dir) msg('请填写安装目录');
				if(!preg_match("/^[0-9a-z_-]+$/i", $dir)) msg('目录名不合法,请更换一个再试');
				$r = $db->get_one("SELECT moduleid FROM {$DT_PRE}module WHERE moduledir='$dir' AND islink=0");
				if($r) msg('此目录名已经被其他模块使用,请更换一个再试');
				if(is_dir(DT_ROOT.'/'.$dir)) msg('此目录名在根目录已存在,请更换一个再试');
				if(is_dir(DT_ROOT.'/company/'.$dir)) msg('此目录名在company目录已存在,请更换一个再试');
				if(is_dir(DT_ROOT.'/mobile/'.$dir)) msg('此目录名在mobile目录已存在,请更换一个再试');
				$sysdirs = array('ad', 'admin', 'announce', 'api', 'archiver', 'comment', 'feed', 'file', 'gift', 'guestbook', 'include', 'install', 'lang', 'link', 'module', 'poll', 'sitemap', 'static', 'spread', 'template', 'upgrade', 'vote', 'mobile', 'form');
				if(in_array($dir, $sysdirs)) msg('安装目录与系统目录冲突，请更换安装目录');
				if(!dir_create(DT_ROOT.'/'.$dir.'/')) msg('无法创建'.$dir.'目录，请检查PHP是否有创建权限或手动创建');
				if(!is_write(DT_ROOT.'/'.$dir.'/')) msg('目录'.$dir.'无法写入，请设置此目录可写权限');
			}
			is_url($post['logo']) or $post['logo'] = '';
			is_url($post['icon']) or $post['icon'] = '';
			if($post['domain']) $post['domain'] = fix_domain($post['domain']);
			if($post['mobile']) $post['mobile'] = fix_domain($post['mobile']);
			$post['linkurl'] = $post['islink'] ? $post['linkurl'] : ($post['domain'] ? $post['domain'] : linkurl($post['moduledir']."/"));
			if($post['islink']) $post['module'] = 'destoon';
			$post['installtime'] = $DT_TIME;
			if($MCFG['moduleid']) {
				$db->query("DELETE FROM {$DT_PRE}module WHERE moduleid=".$MCFG['moduleid']);
				$post['moduleid'] = $MCFG['moduleid'];
			}
			$sql1 = $sql2 = $s = "";
			foreach($post as $key=>$value) {
				$sql1 .= $s.$key;
				$sql2 .= $s."'".$value."'";
				$s = ",";
			}
			$db->query("INSERT INTO {$DT_PRE}module ($sql1) VALUES ($sql2)");
			$moduleid = $db->insert_id();
			$db->query("UPDATE {$DT_PRE}module SET listorder=$moduleid WHERE moduleid=$moduleid");
			if($post['islink']) {
			} else {
				$module = $post['module'];
				$dir = $post['moduledir'];
				$modulename = $post['name'];
				@include DT_ROOT.'/module/'.$module.'/admin/install.inc.php';
			}
			clear_upload($post['logo'].$post['icon']);
			cache_module();
			dmsg('模块添加成功', $this_forward);
		} else {
			$imodules = array();
			$result = $db->query("SELECT module FROM {$DT_PRE}module");
			while($r = $db->fetch_array($result)) {
				$imodules[$r['module']] = $r['module'];
			}
			$modules = get_modules();
			$module_select = '<select name="post[module]"  id="module"><option value="0">请选择</option>';
			foreach($modules as $k=>$v) {
				if($v['copy'] == false) {
					if(in_array($v['module'], $imodules)) continue;
				}
				$module_select .= '<option value="'.$v['module'].'">'.$v['name'].'</option>';
			}
			$module_select .= '</select>';
			include tpl('module_edit');
		}
	break;
	case 'edit':
		if(!$mid) msg('模块ID不能为空');
		if($mid == 1 || $mid == 3) msg('系统模型，不可修改');
		$r = $db->get_one("SELECT * FROM {$DT_PRE}module WHERE moduleid='$mid'");
		if(!$r) msg('模块不存在');
		extract($r);
		if($submit) {
			if(!$post['name']) msg('请填写模块名称');
			if($islink) {
				if(!$post['linkurl']) msg('请填写链接地址');
			} else {
				if($mid == 4) $post['moduledir'] = 'company';
				if(!$post['moduledir']) msg('请填写安装目录');
				if(!preg_match("/^[0-9a-z_-]+$/i", $post['moduledir'])) msg('目录名不合法,请更换一个再试');
				if(!$islink && $moduledir != $post['moduledir']) {
					if(is_dir(DT_ROOT.'/'.$post['moduledir'])) msg('此目录名在根目录已存在,请更换一个再试');
					if(is_dir(DT_ROOT.'/company/'.$post['moduledir'])) msg('此目录名在company目录已存在,请更换一个再试');
					if(is_dir(DT_ROOT.'/mobile/'.$post['moduledir'])) msg('此目录名在mobile目录已存在,请更换一个再试');
				}
				$sysdirs = array('ad', 'admin', 'announce', 'api', 'archiver', 'comment', 'feed', 'file', 'gift', 'guestbook', 'include', 'install', 'lang', 'link', 'module', 'poll', 'sitemap', 'skin', 'spread', 'template', 'upgrade', 'vote', 'mobile', 'form');
				if(in_array($post['moduledir'], $sysdirs)) msg('安装目录与系统目录冲突，请更换安装目录');
				$r = $db->get_one("SELECT moduleid FROM {$DT_PRE}module WHERE moduledir='$post[moduledir]' AND moduleid!=$mid");
				if($r) msg('此目录名已经被其他模块使用,请更换一个再试');
				if($post['domain']) $post['domain'] = fix_domain($post['domain']);
				if($post['mobile']) $post['mobile'] = fix_domain($post['mobile']);
				$post['linkurl'] = $post['domain'] ? $post['domain'] : linkurl($post['moduledir']."/");
				$post['disabled'] = $post['disabled'] && $mid > 4 ? 1 : 0;
			}
			is_url($post['logo']) or $post['logo'] = '';
			is_url($post['icon']) or $post['icon'] = '';		
			$sql = $s = "";
			foreach($post as $key=>$value) {
				$sql .= $s.$key."='".$value."'";
				$s = ",";
			}
			$db->query("UPDATE {$DT_PRE}module SET $sql WHERE moduleid=$mid");
			if(!$islink && $moduledir != $post['moduledir']) {
				rename(DT_ROOT.'/'.$moduledir, DT_ROOT.'/'.$post['moduledir']) or msg('无法重命名目录'.$moduledir.'为'.$post['moduledir'].',请手动修改');
				rename(DT_ROOT.'/mobile/'.$moduledir, DT_ROOT.'/mobile/'.$post['moduledir']);
			}
			clear_upload($post['logo'].$post['icon']);
			cache_module();
			dmsg('模块修改成功', $this_forward);
		} else {
			@include DT_ROOT.'/module/'.$module.'/admin/config.inc.php';
			$modulename = isset($MCFG['name']) ? $MCFG['name'] : '';
			include tpl('module_edit');
		}
	break;
	case 'delete':
		if(!$mid) msg('模块ID不能为空');	
		if($mid < 5) msg('系统模型不可删除');
		#if($mid < 23) dheader('?file='.$file.'&action=disable&value=1&mid='.$mid);
		$r = $db->get_one("SELECT * FROM {$DT_PRE}module WHERE moduleid='$mid'");
		if(!$r) msg('此模块不存在');
		if(!$r['islink']) {
			$moduleid = $r['moduleid'];
			$module = $r['module'];
			$dir = $r['moduledir'];
			$module_cfg = DT_ROOT.'/module/'.$module.'/admin/config.inc.php';
			if(!is_file($module_cfg)) msg('此模型不可卸载，请检查');
			include $module_cfg;
			if($MCFG['uninstall'] == false) msg('此模型不可卸载，请检查');
			@include DT_ROOT.'/module/'.$module.'/admin/uninstall.inc.php';			
			$result = $db->query("SHOW TABLES FROM `".$CFG['db_name']."`");
			while($r = $db->fetch_row($result)) {
				$tb = $r[0];
				$pt = str_replace($DT_PRE.$moduleid.'_', '', $tb);
				if(is_numeric($pt)) $db->query("DROP TABLE IF EXISTS `".$tb."`");
			}
			$db->query("DELETE FROM `".$DT_PRE."category` WHERE moduleid=$moduleid");
			$db->query("DELETE FROM `".$DT_PRE."keylink` WHERE item=$moduleid");
			$db->query("DELETE FROM `".$DT_PRE."setting` WHERE item=$moduleid");
			$tb = str_replace($DT_PRE, '', get_table($moduleid));
			$db->query("DELETE FROM `".$DT_PRE."fields` WHERE tb='$tb'");
			dir_delete(DT_ROOT.'/'.$dir);
			dir_delete(DT_ROOT.'/mobile/'.$dir);
			if(is_dir(DT_ROOT.'/company/'.$dir)) dir_delete(DT_ROOT.'/company/'.$dir);
		}
		$db->query("DELETE FROM {$DT_PRE}module WHERE moduleid='$mid'");
		cache_module();
		dmsg('模块删除成功', $this_forward);
		break;
	case 'remkdir':
		if(!$mid) msg('模块ID不能为空');
		$r = $db->get_one("SELECT * FROM {$DT_PRE}module WHERE moduleid='$mid'");
		$remkdir = DT_ROOT.'/module/'.$r['module'].'/admin/remkdir.inc.php';
		if(is_file($remkdir)) {
			$moduleid = $r['moduleid'];
			$module = $r['module'];
			$dir = $r['moduledir'];
			if(!dir_create(DT_ROOT.'/'.$dir)) msg('无法创建'.$dir.'目录，请检查PHP是否有创建权限或手动创建');
			if(!file_put(DT_ROOT.'/'.$dir.'/ajax.php', "DESTOON TEST")) msg('目录'.$dir.'无法写入，如果是Linux/Unix服务器，请设置此目录可写权限');
			file_del(DT_ROOT.'/'.$dir.'/config.inc.php');
			file_copy(DT_ROOT.'/api/ajax.php', DT_ROOT.'/'.$dir.'/ajax.php');
			file_copy(DT_ROOT.'/api/ajax.php', DT_ROOT.'/mobile/'.$dir.'/ajax.php');
			include $remkdir;			
			cache_module();
			dmsg('目录重建成功', '?file='.$file);
		} else {
			msg('此模型无需重建目录', '?file='.$file);
		}
	break;
	case 'disable':
		if(!$mid) msg('模块ID不能为空');
		if($mid < 5) msg('系统模型不可禁用');
		$value = $value ? 1 : 0;
		$db->query("UPDATE {$DT_PRE}module SET disabled='$value' WHERE moduleid=$mid");
		cache_module();
		dmsg('模块状态已经修改', $this_forward);
	break;
	case 'order':
		foreach($listorder as $k=>$v) {
			$k = intval($k);
			$v = intval($v);
			$db->query("UPDATE {$DT_PRE}module SET listorder='$v' WHERE moduleid=$k");
		}
		cache_module();
		dmsg('更新成功', $this_forward);
	break;
	case 'cache':
		cache_module();
		dmsg('更新成功', $forward);
	break;
	case 'sys':
		$sysmodules = get_modules();
		include tpl('module_sys');
	break;
	case 'del':
		$sysmodules = get_modules();
		(check_name($mod) && isset($sysmodules[$mod])) or msg();
		$M = $sysmodules[$mod];
		$M['uninstall'] or msg($M['name'].'模型不可卸载');
		if(in_array($mod, array('destoon', 'member', 'company', 'extend'))) msg($M['name'].'模型不可卸载');
		$mods = $dirs = $files = array();
		foreach($MODULE as $m) {
			if($m['module'] == $mod) {
				$mods[] = $m;
				if(is_dir(DT_ROOT.'/'.$m['moduledir'])) $dirs[] = $m['moduledir'];
				if(is_dir(DT_ROOT.'/mobile/'.$m['moduledir'])) $dirs[] = 'mobile/'.$m['moduledir'];
				if(is_file(DT_ROOT.'/module/company/'.$mod.'.inc.php') && is_dir(DT_ROOT.'/company/'.$m['moduledir'])) $dirs[] = 'company/'.$m['moduledir'];
			}
		}
		if(is_dir(DT_ROOT.'/module/'.$mod)) $dirs[] = 'module/'.$mod;
		if(is_dir(DT_ROOT.'/template/'.$CFG['template'].'/'.$mod)) $dirs[] = 'template/'.$CFG['template'].'/'.$mod;
		if(is_dir(DT_ROOT.'/template/'.$CFG['template_mobile'].'/'.$mod)) $dirs[] = 'template/'.$CFG['template_mobile'].'/'.$mod;

		if(is_file(DT_ROOT.'/module/company/'.$mod.'.inc.php')) $files[] = 'module/company/'.$mod.'.inc.php';
		if(is_file(DT_ROOT.'/static/skin/'.$CFG['skin'].'/'.$mod.'.css')) $files[] = 'static/skin/'.$CFG['skin'].'/'.$mod.'.css';

		foreach(file_list(DT_ROOT.'/file/setting') as $v) {
			if(strpos(basename($v), $mod) === false) continue;
			$files[] = str_replace(DT_ROOT.'/', '', $v);
		}

		foreach(file_list(DT_ROOT.'/template') as $v) {
			if(file_ext($v) != 'htm') continue;
			if(strpos($v, '/'.$mod.'/') !== false) continue;
			if(strpos(basename($v), $mod) === false) continue;
			$files[] = str_replace(DT_ROOT.'/', '', $v);
		}

		include tpl('module_del');
	break;
	default:
		$sfields = array('按条件', '模块名称', '模块目录', '绑定域名', '手机域名');
		$dfields = array('name', 'name', 'moduledir', 'domain', 'mobile');
		isset($fields) && isset($dfields[$fields]) or $fields = 0;
		$sysmodules = get_modules();		
		isset($mod) && isset($sysmodules[$mod]) or $mod = '';
		$fields_select = dselect($sfields, 'fields', '', $fields);
		$fmid = 0;
		$condition = '1';
		if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
		if($mod) $condition .= " AND module='$mod'";
		$modules = $_modules = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}module WHERE $condition ORDER BY ismenu DESC,listorder ASC,moduleid ASC");
		while($r = $db->fetch_array($result)) {
			if($r['moduleid'] == 1 || $r['moduleid'] == 3) continue;
			if(!$fmid) $fmid = $r['moduleid'];
			$r['installdate'] = timetodate($r['installtime'], 3);
			$r['modulecn'] = isset($sysmodules[$r['module']]) ? $sysmodules[$r['module']]['name'] : '<span class="f_red">外链</span>';
			$r['moduleen'] = isset($sysmodules[$r['module']]) ? $sysmodules[$r['module']]['module'] : '';
			if($r['disabled']) {
				$_modules[] = $r;
			} else {
				$modules[] = $r;
			}
		}
		include tpl('module');
	break;
}
?>