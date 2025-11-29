<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="150">锁定时间</th>
<th width="150">IP</th>
<th width="200">来自</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="ip[]" value="<?php echo $v['ip'];?>"/></td>
<td><?php echo $v['addtime'];?></td>
<td><?php echo $v['ip'];?></td>
<td><?php echo ip2area($v['ip']);?></td>
<td></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除选定" class="btn-r" onclick="if(confirm('确定要删除选中IP吗？')){this.form.action='?file=<?php echo $file;?>&action=unban'}else{return false;}"/>
</div>
</form>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>