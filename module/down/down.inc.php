<?php 
// 防止直接访问站内文件
defined('IN_DESTOON') or exit('Access Denied');






// 如果是搜索引擎爬虫，直接返回403禁止访问
if($DT_BOT) dhttp(403);
// 引入公共模块，初始化模块
require DT_ROOT.'/module/'.$module.'/common.inc.php';
// 检查http referer，防止倒链，否则跳转到模块首页。
check_referer() or dheader($MOD['linkurl']);
// 检查itemid是否存在，否则跳转到模块首页。
$itemid or dheader($MOD['linkurl']);
// 获取本页内容
$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
// 查询本业状态是否正常,不正常直接跳转到模型首页
// var_dump(dheader($MOD['linkurl']));
// die();
($item && $item['status'] > 2) or dheader($MOD['linkurl']);
// 引入内容处理类
require DT_ROOT.'/include/content.class.php';
// 把获取到的内容数组，分裂成变量
extract($item);
// 获取本页内容的分类信息
$CAT = get_cat($catid);
// 构建完整的页面URL
$linkurl = $MOD['linkurl'].$linkurl;
// 用户组权限检查
if(!check_group($_groupid, $MOD['group_show']) || !check_group($_groupid, $MOD['group_contact']) || !check_group($_groupid, $CAT['group_show'])) {
	dheader($linkurl );
}
// 付费检查
$fee = DC::fee($item['fee'], $MOD['fee_view']);
// 计算实际费用。如果管理员设置免费，则为0
if($MG['fee_mode'] && $MOD['fee_mode']) $fee = 0;
// 如果是自己发布的，下载免费
if($item['username'] == $_username) $fee = 0;
// 如果需要费用，检查是否已付费。否则不往下走了。停留在本业链接
if($fee) {
	if($_userid) {
		check_pay($moduleid, $itemid) or dheader($linkurl);
	} else {
		dheader($linkurl);
	}
}

// 26-33文件路径处理
// 更新下载次数
$db->query("UPDATE {$table} SET download=download+1 WHERE itemid=$itemid");
// 处理文件路径,移除系统根路径
$fileurl = trim($fileurl);
$localfile = str_replace(DT_PATH, '', $fileurl);
// 判断是本地文件还是远程文件
if(strpos($localfile, '://') !== false) {
	$local = false;
} else {
	$localfile = DT_ROOT.'/'.$localfile;	
	if($DT['pcharset']) $localfile = convert($localfile, DT_CHARSET, $DT['pcharset']);
	if(is_file($localfile)) {
		$local = true;
		$fileurl = linkurl($fileurl);
	} else {
		dheader($fileurl);
		//dalert($L['not_file'], $linkurl);
	}
}
// 镜像处理,如果指定了镜像参数
if(isset($mirror)) {	
	// 引入镜像配置文件
	include DT_ROOT.'/file/config/mirror.inc.php';
	// 检查镜像是否存在
	if(isset($MIRROR[$mirror])) {
		// 根据文件类型,重定向到镜像地址
		if($local) {
			dheader(str_replace(DT_ROOT.'/', $MIRROR[$mirror]['url'], $localfile));
		} else {
			if($DT['ftp_remote'] && $DT['remote_url']) $fileurl = str_replace($DT['remote_url'], $MIRROR[$mirror]['url'], $fileurl);
			dheader($fileurl);
		}
	} else {
		// 镜像不存在的提示错误
		dalert($L['not_mirror'], $linkurl);
	}
	// 直接下载处理
} else {
	// 处理本地文件下载
	if($local) {
		// 检查文件大小是否是允许下载范围
		if($MOD['upload'] && filesize($localfile) < $MOD['readsize']*1024*1024) {
			// 检查文件扩展名
			$ext = file_ext($localfile);
			if(!in_array($ext, explode('|', $MOD['upload'])) || in_array($ext, array('php', 'sql')) || strpos($localfile, './') !== false) dheader($fileurl);//Safe
			// 文件名处理,兼容不同浏览器
			$title = file_vname($title);
			// 如果文件名存在就往下走,不存在就停止访问
			$title or dheader($fileurl);
			if(strpos(DT_UA, 'Firefox') !== false) $title = str_replace(' ', '_', $title);
			if(strpos(DT_UA, 'MSIE') !== false || strpos(DT_UA, 'rv:1') !== false) $title = convert($title, DT_CHARSET, 'GBK');
			$title or dheader($fileurl);
			// 调用file_down函数强制下载
			file_down($localfile, $title.'.'.$ext);
		} else {
			// 大文件直接跳转本页
			dheader($fileurl);
		}
		// 远程文件直接跳转本页
	} else {
		dheader($fileurl);
	}
}
?>