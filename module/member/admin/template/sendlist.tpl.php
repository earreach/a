<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>文件</th>
<th>备注</th>
<th>大小(Kb)</th>
<th>记录数</th>
<th width="150">获取时间</th>
<th width="40">下载</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="filenames[]" value="<?php echo $v['filename'];?>"/></td>
<td align="left"> &nbsp; <a href="javascript:;" class="f_fd" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&filename=<?php echo $v['filename'];?>', '<?php echo $v['filename'];?> <?php echo $v['note'];?> <?php echo $v['count'];?>条', 600, 400);"><?php echo $v['filename'];?></a></td>
<td><input type="text" style="width:200px;" title="<?php echo $v['note'];?>" value="<?php echo $v['note'];?>" onblur="note('<?php echo $v['filename'];?>', this.value);"/></td>
<td><?php echo $v['filesize'];?></td>
<td><?php echo $v['count'];?></td>
<td><?php echo $v['mtime'];?></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=download&filename=<?php echo $v['filename'];?>"><img src="<?php echo DT_STATIC;?>admin/save.png" width="16" height="16" title="下载" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 删除文件 " class="btn-r" onclick="if(confirm('确定要删除选中文件吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=unlink'}else{return false;}"/>&nbsp;
</div>
</form>
<script type="text/javascript">
function note(name, note) {
	$.post('?', 'moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=note&filename='+name+'&note='+note, function(data) {
		if(data == 'ok') showmsg('备注修改成功');
	});
}
Menuon(3);
</script>
<?php include tpl('footer');?>