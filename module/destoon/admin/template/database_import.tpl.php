<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="delete"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>备份系列</th>
<th width="220">备注</th>
<th width="100">文件大小</th>
<th width="150">备份时间</th>
<th width="50">分卷</th>
<th width="40">导入</th>
<th width="40">下载</th>
</tr>
<?php foreach($dbaks as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"></td>
<td align="left">&nbsp;<img src="file/ext/folder.gif" alt="" align="absmiddle"/> <a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=open&dir=<?php echo $v['filename'];?>', '备份系列 - <?php echo $v['filename'];?>');" class="f_fd"><?php echo $v['filename'];?></a></td>
<td><input type="text" style="width:200px;" title="<?php echo $v['note'];?>" value="<?php echo $v['note'];?>" onblur="note('<?php echo $v['filename'];?>', this.value);"/></td>
<td><?php echo $v['filesize'];?></td>
<td title="修改时间:<?php echo $v['mtime'];?>"><?php echo $v['btime'];?></td>
<td><?php echo $v['number'];?></td>
<td><a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&filepre=<?php echo $v['pre'];?>&tid=<?php echo $v['number'];?>&import=1" onclick="return confirm('确定要导入此系列文件吗？现有数据将被覆盖，此操作将不可恢复');"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="导入" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=open&dir=<?php echo $v['filename'];?>', '备份系列 - <?php echo $v['filename'];?>');"><img src="<?php echo DT_STATIC;?>admin/save.png" width="16" height="16" title="下载" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php if($dsqls || $sqls) {?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"></th>
<th>SQL文件</th>
<th width="220">备注</th>
<th width="100">文件大小</th>
<th width="150">修改时间</th>
<th width="50">分卷</th>
<th width="40">导入</th>
<th width="40">下载</th>
</tr>
<?php if($dsqls) {?>
<?php foreach($dsqls as $k=>$v) {?>
<tr align="center"<?php if($v['class']) echo ' class="on"';?>>
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"></td>
<td align="left">&nbsp;<img src="file/ext/sql.gif" width="16" height="16" alt="" align="absmiddle"/> <a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=view&filename=<?php echo $v['filename'];?>', this.innerHTML);" class="f_fd"><?php echo $v['filename'];?></a></td>
<td><input type="text" style="width:200px;" title="<?php echo $v['note'];?>" value="<?php echo $v['note'];?>" readonly/></td>
<td><?php echo $v['filesize'];?></td>
<td title="修改时间:<?php echo $v['mtime'];?>"><?php echo $v['btime'];?></td>
<td><?php echo $v['number'];?></td>
<td><a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&filepre=<?php echo $v['pre'];?>&import=1" onclick="return confirm('确定要导入此系列文件吗？现有数据将被覆盖，此操作将不可恢复');"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="导入" alt=""/></td>
<td><a href="?file=<?php echo $file;?>&action=download&filename=<?php echo $v['filename'];?>"><img src="<?php echo DT_STATIC;?>admin/save.png" width="16" height="16" title="下载" alt=""/></a></td>
</tr>
<?php }?>
<?php }?>
<?php if($sqls) {?>
<?php foreach($sqls as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"></td>
<td align="left">&nbsp;<img src="file/ext/sql.gif" width="16" height="16" alt="" align="absmiddle"/> <a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=view&filename=<?php echo $v['filename'];?>', this.innerHTML);" class="f_fd"><?php echo $v['filename'];?></a></td>
<td><input type="text" style="width:200px;" title="<?php echo $v['note'];?>" value="<?php echo $v['note'];?>" readonly/></td>
<td><?php echo $v['filesize'];?></td>
<td><?php echo $v['mtime'];?></td>
<td> -- </td>
<td><a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&filename=<?php echo $v['filename'];?>&import=1" onclick="return confirm('确定要导入此文件吗？现有数据可能会被覆盖，此操作将不可恢复');"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="导入" alt=""/></a></td>
<td><a href="?file=<?php echo $file;?>&action=download&filename=<?php echo $v['filename'];?>"><img src="<?php echo DT_STATIC;?>admin/save.png" width="16" height="16" title="下载" alt=""/></a></td>
</tr>
<?php }?>
<?php }?>
</table>
<?php } ?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" name="submit" value="删除文件" class="btn-r" onclick="return confirm('确定要删除所选文件吗？此操作将不可恢复');"/></div>
</form>
<script type="text/javascript">
<?php if(count($dbaks) > 10) { ?>
Dalert('备份系列超 10 个，建议清理或转移过期备份')
<?php } ?>
function note(dir, note) {
	$.post('?', 'file=<?php echo $file;?>&action=note&dir='+dir+'&note='+note, function(data) {
		if(data == 'ok') showmsg('备注修改成功');
	});
}
Menuon(1);
</script>
<?php include tpl('footer');?>