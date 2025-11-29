<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="item" value="<?php echo $item;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="120">排序</th>
<th>名称</th>
<th>上级分类</th>
</tr>
<?php if(is_array($lists['0'])) { foreach($lists['0'] as $k0 => $v0) { ?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v0['typeid'];?>"/></td>
<td><input name="post[<?php echo $v0['typeid'];?>][listorder]" type="text" size="5" value="<?php echo $v0['listorder'];?>" maxlength="3"/></td>
<td align="left"><input name="post[<?php echo $v0['typeid'];?>][typename]" type="text" size="20" value="<?php echo $v0['typename'];?>" style="width:140px;color:<?php echo $v0['style'];?>"/> <?php echo $v0['style_select'];?></td>
<td><?php echo $v0['parent_select'];?></td>
</tr>
<?php if(isset($lists['1'][$v0['typeid']])) { ?>
<?php if(is_array($lists['1'][$v0['typeid']])) { foreach($lists['1'][$v0['typeid']] as $k1 => $v1) { ?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v1['typeid'];?>"/></td>
<td><input name="post[<?php echo $v1['typeid'];?>][listorder]" type="text" size="5" value="<?php echo $v1['listorder'];?>" maxlength="3"/></td>
<td align="left"><img src="<?php echo DT_STATIC;?>admin/tree.png" align="absmiddle"/><input name="post[<?php echo $v1['typeid'];?>][typename]" type="text" size="20" value="<?php echo $v1['typename'];?>" style="width:120px;color:<?php echo $v1['style'];?>"/> <?php echo $v1['style_select'];?></td>
<td><?php echo $v1['parent_select'];?></td>
</tr>
<?php } } ?>
<?php } ?>
<?php } } ?>
<tr align="center">
<td class="f_green">新增</td>
<td><input name="post[0][listorder]" type="text" size="5" value="" maxlength="3"/></td>
<td align="left"><textarea name="post[0][typename]" style="width:142px;height:32px;float:left;" placeholder="允许批量添加，一行一个分类名称，按回车换行" title="允许批量添加，一行一个分类名称，按回车换行"></textarea> <?php echo $new_style;?> <?php echo tips('允许批量添加，一行一个分类名称，按回车换行');?></td>
<td><?php echo $parent_select;?></td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="3">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保 存" class="btn-g" onclick="this.form.action='?file=<?php echo $file;?>&action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?file=<?php echo $file;?>&action=delete&status=<?php echo $status;?>';}else{return false;}}else{confirm('请选择要删除的项目');return false;}" class="btn-r"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="取 消" class="btn" onclick="window.parent.cDialog();"/>
</td>
</tr>
</table>
</form>
<?php include tpl('footer');?>