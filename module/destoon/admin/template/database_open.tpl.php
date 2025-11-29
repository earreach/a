<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb ls">
<tr>
<th>文件名称</th>
<th width="150">大小</th>
<th width="200">修改时间</th>
<th width="100">分卷</th>
<th width="40">导入</th>
<th width="40">下载</th>
</tr>
<?php
for($i = 1; $i <= $tid; $i++) {
	$v = $sqls[$i];
?>
<tr align="center">
<td align="left">&nbsp;<img src="file/ext/sql.gif" width="16" height="16" alt="" align="absmiddle"/>  <a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=view&dir=<?php echo $dir;?>&filename=<?php echo $v['filename'];?>', '<?php echo $dir;?>/<?php echo $v['filename'];?>');" class="f_fd"><?php echo $v['filename'];?></a></td>
<td><?php echo $v['filesize'];?></td>
<td title="备份时间:<?php echo $v['btime'];?>"><?php echo $v['mtime'];?></td>
<td><?php echo $v['number'];?></td>
<td><a href="?file=<?php echo $file;?>&action=import&filepre=<?php echo $v['pre'];?>&tid=<?php echo $tid;?>&import=1" onclick="return confirm('确定要导入此系列文件吗？现有数据将被覆盖，此操作将不可恢复');"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="导入" alt=""/></a></td>
<td><a href="?file=<?php echo $file;?>&action=download&dir=<?php echo $dir;?>&filename=<?php echo $v['filename'];?>"><img src="<?php echo DT_STATIC;?>admin/save.png" width="16" height="16" title="下载" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php include tpl('footer');?>