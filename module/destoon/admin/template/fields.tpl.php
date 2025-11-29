<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?action=update">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="tb" value="<?php echo $tb;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th>排序</th>
<th>字段</th>
<th>字段名称</th>
<th>表单类型</th>
<th>字段属性</th>
<th>显示</th>
<th>前台</th>
<th>搜索</th>
<th width="40">调用</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['itemid'];?>"/></td>
<td><input name="post[<?php echo $v['itemid'];?>][listorder]" type="text" size="2" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['name'];?></td>
<td><input name="post[<?php echo $v['itemid'];?>][title]" type="text" size="10" value="<?php echo $v['title'];?>"/></td>
<td><?php echo $v['html'];?><input name="post[<?php echo $v['itemid'];?>][html]" type="hidden" value="<?php echo $v['html'];?>"/></td>
<td><?php echo $v['type'];?><?php echo $v['length'] ? '('.$v['length'].')' : '';?></td>
<td><select name="post[<?php echo $v['itemid'];?>][display]"><option value="1"<?php echo $v['display'] ? ' selected' : '';?>>是</option><option value="0"<?php echo $v['display'] ? '' : ' selected';?>>否</option></select></td>
<td><select name="post[<?php echo $v['itemid'];?>][front]"><option value="1"<?php echo $v['front'] ? ' selected' : '';?>>是</option><option value="0"<?php echo $v['front'] ? '' : ' selected';?>>否</option></select></td>
<td><select name="post[<?php echo $v['itemid'];?>][search]"><option value="1"<?php echo $v['search'] ? ' selected' : '';?>>是</option><option value="0"<?php echo $v['search'] ? '' : ' selected';?>>否</option></select></td>
<td><a href="javascript:Dcall('<?php echo $v['itemid'];?>', '<?php echo $v['name'];?>');"><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="查看" alt=""/></a></td>
<td>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&tb=<?php echo $tb;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="12">
<input type="submit" value="更 新" class="btn-g" onclick="this.form.action='?action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?action=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="取 消" class="btn" onclick="window.parent.cDialog();"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">
function Dcall(id, name) {
	var tips = '';
	tips += '表单名称：post_fields['+name+']<br/>';
	tips += '表单调用：{fields_show('+id+')}<br/>';
	tips += '标签调用：{$t['+name+']}<br/>';
	tips += '内容调用：{$'+name+'}<br/>';
	Dalert(tips);
}
Menuon(1);
</script>
<?php include tpl('footer');?>