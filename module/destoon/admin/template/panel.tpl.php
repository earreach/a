<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?action=update" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="80">排序</th>
<th width="160">名称</th>
<th width="460">网址</th>
<th></th>
</tr>
<?php foreach($dmenus as $k=>$v) {?>
<tr align="center">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['itemid'];?>"/></td>
<td><input name="right[<?php echo $v['itemid'];?>][listorder]" type="text" size="3" value="<?php echo $v['listorder'];?>"/></td>
<td><input name="right[<?php echo $v['itemid'];?>][title]" type="text" size="12" value="<?php echo $v['title'];?>"/> <?php echo dstyle('right['.$v['itemid'].'][style]', $v['style']);?></td>
<td align="left"><input name="right[<?php echo $v['itemid'];?>][url]" type="text" size="60" value="<?php echo $v['url'];?>"/> &nbsp; <a href="<?php echo $v['url'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="打开链接" alt=""/></a></td>
<td></td>
</tr>
<?php }?>
<tr align="center"<?php if($url) echo ' class="on"';?>>
<td class="f_green">新增</td>
<td><input name="right[0][listorder]" type="text" size="3" value=""/></td>
<td><input name="right[0][title]" type="text" size="12" value="<?php echo $title;?>" id="title"/> <?php echo dstyle('right[0][style]');?></td>
<td align="left"><input name="right[0][url]" type="text" size="60" value="<?php echo $url;?>" id="url"/> &nbsp; <a href="javascript:;" onclick="if($('#url').val()){window.open($('#url').val());}else{alert('请输入网址');}"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="打开链接" alt=""/></a>
</td>
<td></td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td height="30" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保 存" class="btn-g" onclick="this.form.action='?action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($url) {?>
<input type="button" value=" 取 消 " class="btn" onclick="window.parent.cDialog();"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php } else { ?>
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?action=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
</td>
</tr>
</table>
</form>
<br/>
<script type="text/javascript">
Menuon(0);
<?php if(isset($update)) { ?>
window.top.frames[0].n();
<?php } ?>
<?php if($title) { ?>
Dd('dform').submit();
<?php } ?>
</script>
<?php include tpl('footer');?>