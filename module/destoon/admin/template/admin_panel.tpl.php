<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="admin-panel">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="100">排序</th>
<th width="200">名称</th>
<th width="200">地址</th>
<th></th>
</tr>
<?php foreach($P as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><input name="right[<?php echo $v['itemid'];?>][listorder]" type="text" size="3" value="<?php echo $v['listorder'];?>"/></td>
<td><input name="right[<?php echo $v['itemid'];?>][title]" type="text" size="12" value="<?php echo $v['title'];?>"/> <?php echo dstyle('right['.$v['itemid'].'][style]', $v['style']);?></td>
<td><input name="right[<?php echo $v['itemid'];?>][url]" type="text" size="25" value="<?php echo $v['url'];?>"/></td>
<td></td>
</tr>
<?php }?>
<tr align="center">
<td class="f_green">新增</td>
<td><input name="right[0][listorder]" type="text" size="3" value=""/></td>
<td><input name="right[0][title]" type="text" size="12" value="" id="p_title"/> <?php echo dstyle('right[0][style]');?></td>
<td><input name="right[0][url]" type="text" size="25" value="" id="p_url"/></td>
<td></td>
</tr>
<tr>
<td height="30" align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="更 新" class="btn-g" onclick="this.form.action='?job=<?php echo $job;?>&update=1';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<select onchange="if(this.value){Dd('p_title').value=this.options[selectedIndex].innerHTML;Dd('p_url').value=this.value;}" style="width:120px;">
<option value="">常用操作</option>
<?php
foreach($MODULE as $m) {
	if($m['islink']) continue;
	$moduleid = $m['moduleid'];
	$name = $m['name'];
?>
<?php if($moduleid == 2) { ?>
<?php
	include DT_ROOT.'/module/member/admin/menu.inc.php';
	foreach($menu as $m) {
		if(strpos($m[1], 'setting') !== false) continue;
		echo '<option value="'.$m[1].'">'.$m[0].'</option>';
	}
	foreach($menu_finance as $m) {
		echo '<option value="'.$m[1].'">'.$m[0].'</option>';
	}
	foreach($menu_relate as $m) {
		echo '<option value="'.$m[1].'">'.$m[0].'</option>';
	}
?>
<?php } else { ?>
<option value="">----------------</option>
<?php
	include DT_ROOT.'/module/'.$m['module'].'/admin/menu.inc.php';
	foreach($menu as $m) {
		if(strpos($m[1], 'setting') !== false) continue;
		echo '<option value="'.$m[1].'">'.$m[0].'</option>';
	}
?>
<?php } ?>
<?php } ?>
</select>
<?php tips('添加常用操作可以自动分配对应权限');?>
</td>
</tr>
</table>
</form>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>