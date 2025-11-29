<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="156">模型</th>
<th width="156">目录</th>
<th width="80">复制</th>
<th width="100">作者</th>
<th width="180">官方网站</th>
<th width="80">卸载</th>
<th></th>
</tr>
<?php foreach($sysmodules as $k=>$v) {?>
<tr align="center">
<td align="left">&nbsp;<img src="file/ext/folder.gif" align="absmiddle"/>&nbsp; <?php echo $v['name'];?></td>
<td title="位于module/<?php echo $v['module'];?>/"><?php echo $v['module'];?></td>
<td><?php echo $v['copy'] ? '<img src="'.DT_STATIC.'image/yes.png" title="可复制"/>' : ''; ?></td>
<td><?php echo $v['author'];?></td>
<td><a href="<?php echo 'https://'.$v['homepage'];?>" target="_blank"><?php echo $v['homepage'];?></a></td>
<td><?php if($v['uninstall']) { ?><a href="?file=<?php echo $file;?>&action=del&mod=<?php echo $v['module'];?>" class="t">查看说明</a><?php } ?></td>
<td></td>
</tr>
<?php
}
?>
</table>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>