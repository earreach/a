<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>用户</th>
<th>主机</th>
<th>数据库</th>
<th>命令</th>
<th>时间</th>
<th>状态</th>
<th width="220">SQL查询</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['Id'];?>"<?php echo $v['Command'] == 'Sleep' ? ' checked' : '';?>/></td>
<td><?php echo $v['User'];?></td>
<td><?php echo $v['Host'];?></td>
<td><?php echo $v['db'];?></td>
<td><?php echo $v['Command'];?></td>
<td><?php echo $v['Time'];?></td>
<td><?php echo $v['State'];?></td>
<td><textarea style="width:200px;height:15px;" title="<?php echo $v['Info'];?>" onmouseover="this.select();"><?php echo $v['Info'];?></textarea></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 结束进程 " class="btn-r" onclick="if(confirm('确定要结束选中进程吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&action=kill'}else{return false;}"/>
</div>
</form>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>