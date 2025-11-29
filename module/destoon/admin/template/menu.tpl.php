<?php
defined('DT_ADMIN') or exit('Access Denied');
?>
<div class="menu" onselectstart="return false" id="destoon_menu">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td valign="bottom">
<table cellpadding="0" cellspacing="0">
<tr>
<?php echo $menu;?>
</tr>
</table>
</td>
<td>
<div>
<a href="?action=home&job=<?php echo $mid ? $mid : $moduleid;?>-<?php echo $file?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/tool-home.png" width="16" height="16" title="前台" alt=""/></a>
<img src="<?php echo DT_STATIC;?>admin/tool-favor.png" width="16" height="16" title="加入面板" onclick="Dwidget('?file=panel&itemid=1&url='+encodeURIComponent(window.location.href)+'&title='+encodeURIComponent($('.tab_on').text()), '加入面板');" alt=""/>
<?php if($_admin != 2) { ?>
<img src="<?php echo DT_STATIC;?>admin/tool-search.png" width="16" height="16" title="搜索" onclick="Dwidget('?file=search', '后台搜索');" alt=""/>
<img src="<?php echo DT_STATIC;?>admin/tool-help.png" width="16" height="16" title="帮助" onclick="Dwidget('?file=cloud&action=doc&mfa=<?php echo $module;?>-<?php echo $file?>-<?php echo $action?>', '帮助文档');" alt=""/>
<?php } ?>
<img src="<?php echo DT_STATIC;?>admin/tool-reload.png" width="16" height="16" title="刷新" onclick="window.location.reload();" alt=""/>
<script type="text/javascript">
if(parent.location == window.location) {
	document.write('<img src="<?php echo DT_STATIC;?>admin/tool-close.png" width="16" height="16" title="关闭" onclick="window.close();" alt=""/>');
} else {
	document.write('<img src="<?php echo DT_STATIC;?>admin/tool-full.png" width="16" height="16" title="全屏" onclick="window.open(window.location.href);" alt=""/>');
}
</script>
</div>
</td>
</tr>
</table>
</div>
<div class="menu-fix">&nbsp;</div>