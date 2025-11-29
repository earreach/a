<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="224">组名称</th>
<th width="160">最大积分</th>
<th width="100">组ID</th>
<th width="100">图标</th>
<!--<th width="40">设置</th>-->
<th></th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['gradeid'];?>"/></td>
<td><input name="post[<?php echo $v['gradeid'];?>][name]" type="text" value="<?php echo $v['name'];?>" style="width:160px;color:<?php echo $v['style'];?>"/> <?php echo $v['style_select'];?></td>
<td><input name="post[<?php echo $v['gradeid'];?>][credit]" type="text" size="20" value="<?php echo $v['credit'];?>"/></td>
<td><?php echo $v['gradeid'];?></td>
<td><img src="<?php echo DT_STATIC;?>image/grade-<?php echo $v['gradeid'];?>.png" width="24" onerror="this.src='<?php echo DT_STATIC;?>image/spacer.png';"/></td>
<!--<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=grade&gradeid=<?php echo $v['gradeid'];?>', '<?php echo $v['name'];?>设置');"><img src="<?php echo DT_STATIC;?>admin/set.png" width="16" height="16" title="设置" alt=""/></a></td>-->
<td>&nbsp;</td>
</tr>
<?php } ?>
<tr align="center">
<td class="f_green">新增</td>
<td><input name="post[0][name]" type="text" value="" style="width:160px;"/> <?php echo $new_style;?></td>
<td><input name="post[0][credit]" type="text" size="20" value=""/></td>
<td></td>
<td></td>
<!--<td></td>-->
<td></td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td height="30" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="submit" value="保 存" class="btn-g" onclick="this.form.action='?job=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
<tr>
<td> </td>
<td colspan="6" class="ts">
&nbsp;&nbsp;- 积分设置必须依次递增，否则会导致会员级别排名混乱<br/>
&nbsp;&nbsp;- 积分组调整过之后，请在会员<a href="?moduleid=<?php echo $moduleid;?>&file=html" class="t">更新数据</a>里更新会员<br/>
</td>
</tr>
</table>
</form>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>