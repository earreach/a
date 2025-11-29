<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="fid" value="<?php echo $fid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="40">排序</th>
<th>ID</th>
<th>选项名称</th>
<th>添加方式</th>
<th>必填</th>
<th>输入限制</th>
<th>默认(备选)值</th>
<th width="40">报表</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['qid'];?>"/></td>
<td><input type="text" size="2" name="post[<?php echo $v['qid'];?>][listorder]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['qid'];?></td>
<td><input type="text" style="width:300px;" name="post[<?php echo $v['qid'];?>][name]" value="<?php echo $v['name'];?>"/></td>
<td><?php echo $TYPES[$v['type']];?></td>
<td><?php echo $v['required'] ? '<span class="f_red">是</span>' : '否';?></td>
<td><?php echo $v['required'];?></td>
<td><input type="text" style="width:300px;" name="post[<?php echo $v['qid'];?>][value]" value="<?php echo $v['value'];?>"/></td>
<?php if(in_array($v['type'], array('select', 'checkbox', 'radio'))) { ?>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=answer&fid=<?php echo $fid;?>&job=stats&qid=<?php echo $v['qid'];?>', '[<?php echo $v['name'];?>] 统计报表');"><img src="<?php echo DT_STATIC;?>admin/poll.png" width="16" height="16" title="统计报表" alt=""/></a></td>
<?php } else { ?>
<td></td>
<?php } ?>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=question&job=edit&fid=<?php echo $fid;?>&qid=<?php echo $v['qid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php } ?>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="9">
<input type="submit" value="更 新" class="btn-g" onclick="this.form.action='?job=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">
Menuon(1);
$(function(){
	if($('body').width()<900) $('body').width(900);
});
</script>
<?php include tpl('footer');?>