<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="page" value="<?php echo $page;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th>模块ID</th>
<th>信息ID</th>
<th>模块</th>
<th>禁止时间</th>
<th>操作人</th>
<th>原文</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['bid'];?>"/></td>
<td><input name="post[<?php echo $v['bid'];?>][moduleid]" type="text" size="10" value="<?php echo $v['moduleid'];?>"/></td>
<td><input name="post[<?php echo $v['bid'];?>][itemid]" type="text" size="10" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&mid=<?php echo $v['moduleid'];?>"><?php echo isset($MODULE[$v['moduleid']]) ? $MODULE[$v['moduleid']]['name'] : '其他';?></a></td>
<td><?php echo $v['edittime'];?></td>
<td><?php echo $v['editor'];?></td>
<td><a href="<?php echo $v['url'];?>" target="_blank" class="t">打开</a></td>
</td>
</tr>
<?php } ?>
<tr align="center">
<td class="f_green">新增</td>
<td><?php echo module_select('post[0][moduleid]', '模块');?></td>
<td><input name="post[0][itemid]" type="text" size="10" value=""/></td>
<td colspan="4"> </td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td height="30" colspan="7">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保 存" class="btn-g" onclick="this.form.action='?job=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>