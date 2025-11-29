<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
$moduleid = 1;
?>
<?php if($user['admin'] == 1) { ?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl" height="64"></td>
<td class="f_gray"> 超级管理员无需设置权限</span>
</td>
</tr>
<tr>
<td class="tl"></td>
<td>
<input type="button" value="返 回" class="btn-g" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&userid=<?php echo $userid;?>');"/>&nbsp;&nbsp;&nbsp;&nbsp;</span>
</td>
</tr>
</table>
<?php } else { ?>
<form method="post" action="?" id="admin-right">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="100">模块ID</th>
<th width="200">文件(file)</th>
<th width="200">动作(action)</th>
<th width="400">分类ID(catid)</th>
<th width="160">信息列表<?php tips('如果选择自己，则对应列表只显示该管理员发布的信息');?></th>
<th></th>
</tr>
<?php foreach($R as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td align="left"><input name="right[<?php echo $v['itemid'];?>][moduleid]" type="text" size="2" value="<?php echo $v['moduleid'];?>"/> <?php echo $v['module'];?></td>
<td align="left"><input name="right[<?php echo $v['itemid'];?>][file]" type="text" size="10" value="<?php echo $v['file'];?>"/> <?php echo $v['name'] ? $v['name'] : '(模块权限)';?></td>
<td><input name="right[<?php echo $v['itemid'];?>][action]" type="text" size="25" value="<?php echo $v['action'];?>"/></td>
<td><input name="right[<?php echo $v['itemid'];?>][catid]" type="text" size="45" value="<?php echo $v['catid'];?>"/></td>
<td><label><input name="right[<?php echo $v['itemid'];?>][self]" type="radio" value="0"<?php echo $v['self'] == 0 ? ' checked' : '';?>/> 全部</label> &nbsp; <label><input name="right[<?php echo $v['itemid'];?>][self]" type="radio" value="1"<?php echo $v['self'] == 1 ? ' checked' : '';?>/> 自己</label></td>
<td></td>
</tr>
<?php }?>

<tr align="center">
<td class="f_green">新增</td>
<td align="left"><input name="right[-1][moduleid]" type="text" size="10"/></td>
<td align="left"><input name="right[-1][file]" type="text" size="10"/></td>
<td><input name="right[-1][action]" type="text" size="25"/></td>
<td><input name="right[-1][catid]" type="text" size="45"/></td>
<td><label><input name="right[-1][self]" type="radio" value="0" checked/> 全部</label> &nbsp; <label><input name="right[-1][self]" type="radio" value="1"/> 自己</label></td>
<td></td>
</tr>

<tr align="center">
<td class="f_green">选择</td>
<td id="moduleids" align="left">
<select name="right[0][moduleid]" size="2" style="height:200px;width:100px;" onchange="get_file(this.value);">
<option value="0">选择模块[单选]</option>
<?php foreach($MODULE as $k=>$v) { if(!$v['islink']) {?>
<option value="<?php echo $k;?>"><?php echo $v['name'];?>[<?php echo $k;?>]</option>
<?php }} ?>
</select>
</td>
<td id="files" align="left">
<select name="right[0][file]" size="2" style="height:200px;width:150px;" onchange="get_action(this.value);">
<option value="">选择文件[单选]</option>
</select>
</td>
<td id="actions">
<select name="right[0][action][]" size="2" multiple style="height:200px;width:150px;">
<option>选择动作[按Ctrl键多选]</option>
</select>
</td>
<td id="catids">
<select name="right[0][catid][]" size="2" multiple style="height:200px;width:300px;">
<option>选择分类多选[按Ctrl键多选]</option>
</select>
</td>
<td></td>
<td></td>
</tr>
<tr>
<td height="30" align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="6">
<input type="submit" value="更 新" class="btn-g" onclick="this.form.action='?job=<?php echo $job;?>&update=1';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<span class="f_gray">提示：动作和分类可按住Ctrl键多选</span></td>
</tr>
</table>
</form>
<script type="text/javascript">
var html_file = Dd('files').innerHTML;
var html_action = Dd('actions').innerHTML;
var html_catid = Dd('catids').innerHTML;
function get_file(mid) {
	if(mid) {
		$.get('?file=<?php echo $file;?>&action=ajax&mid='+mid, function(data) {
			if(data) {
				var s = data.split('|');
				Dd('files').innerHTML = s[0] != 0 ? s[0] : html_file;
				Dd('actions').innerHTML = html_action;
				Dd('catids').innerHTML = s[1] != 0 ? s[1] : html_catid;
			}
		});
	}
}
function get_action(fi, mid) {
	if(mid) {
		$.get('?file=<?php echo $file;?>&action=ajax&mid='+mid+'&fi='+fi, function(data) {
			Dd('actions').innerHTML = data != 0 ? data : html_action;
		});
	}
}
</script>
<?php } ?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>