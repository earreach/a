<?php
defined('DT_ADMIN') or exit('Access Denied');
$menu = array(
	array('流量统计', '?file=stats'),
	array('信息统计', '?file=count'),
	array('数据维护', '?file=database'),
	array('文件维护', '?file=patch'),
	array('标签模板', '?file=template'),
	array('计划任务', '?file=cron'),
	array('上传记录', '?file=upload'),
	array('搜索记录', '?file=keyword'),
	array('表态记录', '?file=like'),
	array('问题验证', '?file=question'),
	array('词语过滤', '?file=banword'),
	array('禁止访问', '?file=banip'),
	array('单页采编', '?file=fetch'),
	array('编辑助手', '?file=word'),
	array('百度推送', '?file=bdpush'),
	array('系统体检', '?file=doctor'),
);
if(!$_founder) unset($menu[0],$menu[1],$menu[3]);
$menu_help = array(
	array('使用协议', '?file=cloud&action=license'),
	array('在线文档', '?file=cloud&action=doc'),
	array('技术支持', '?file=cloud&action=support'),
	array('应用商店', '?file=cloud&action=store'),
	array('信息反馈', '?file=cloud&action=feedback'),
	array('检查更新', '?file=cloud&action=update'),
	array('关于软件', '?file=cloud&action=about'),
);
$menu_system = array(
	array('网站设置', '?file=setting'),
	array('模块管理', '?file=module'),
	array('地区管理', '?file=area'),
	array('分类管理', '?file=cate'),
	array('城市分站', '?file=city'),
	array('生成首页', '?file=html&action=homepage'),
	array('更新数据', '?file=html'),
	array('管理设置', '?file=admin'),
);
?>