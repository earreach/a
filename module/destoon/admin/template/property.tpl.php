<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?action=update">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="40">排序</th>
<th>ID</th>
<th>名称</th>
<th>必填</th>
<th>默认(备选)值</th>
<th>搜索</th>
<th>添加方式</th>
<th width="40">操作</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['oid'];?>"/></td>
<td><input type="text" size="2" name="post[<?php echo $v['oid'];?>][listorder]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['oid'];?></td>
<td><input type="text" name="post[<?php echo $v['oid'];?>][name]" style="width:80px;" value="<?php echo $v['name'];?>"/></td>
<td><select name="post[<?php echo $v['oid'];?>][required]"><option value="1"<?php echo $v['required'] ? ' selected' : '';?>>是</option><option value="0"<?php echo $v['required'] ? '' : ' selected';?>>否</option></select></td>
<td><input type="text" name="post[<?php echo $v['oid'];?>][value]" style="width:300px;" value="<?php echo $v['value'];?>"/></td>
<td><?php echo $v['search'] ? '<span class="f_green">是</span>' : '<span class="f_red">否</span>';?></td>
<td><?php echo $TYPE[$v['type']];?></td>
<td>
<a href="?file=<?php echo $file;?>&action=edit&catid=<?php echo $v['catid'];?>&oid=<?php echo $v['oid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a>
</td>
</tr>
<?php } ?>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="8">
<input type="submit" value="更 新" class="btn-g" onclick="this.form.action='?action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?action=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="取 消" class="btn" onclick="window.parent.cDialog();"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>