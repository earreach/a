<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;&nbsp;<textarea name="sql" id="sql" style="width:98%;height:150px;overflow:visible;" class="f_fd"><?php echo $sql;?></textarea></td>
</tr>
<tr>
<td>
&nbsp;&nbsp;<input type="submit" name="submit" value="执 行" class="btn-r"/> <span id="dsql" class="f_red"></span></td>
</tr>
</table>
</form>
<?php if($lists) {?>
<div style="width:100000px;padding:16px 0;">
<table cellspacing="1" cellpadding="16" class="ls" style="background:#E7E7EB;">
<tr bgcolor="#F5F5F5">
<?php foreach($lists[0] as $k=>$v) {?>
<th><?php echo $k;?></th>
<?php } ?>
</tr>
<?php if($fds) {?>
<tr align="center" bgcolor="#F9F9F9">
<?php foreach($lists[0] as $k=>$v) {?>
<td><?php echo $fds[$k];?></td>
<?php } ?>
</tr>
<?php } ?>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" bgcolor="#FFFFFF">
<?php foreach($v as $kk=>$vv) {?>
<td><?php echo dsubstr(htmlspecialchars($vv), 256, '...');?></td>
<?php } ?>
</tr>
<?php } ?>
</table>
</div>
<?php } ?>
<script type="text/javascript">
function check() {
	var v = $.trim(Dd('sql').value);
	if(v.length < 5) {
		Dmsg('SQL语句不能为空', 'sql');
		return false;
	}
	if(v.substring(0, 6).toLowerCase() == 'select') return true;
	return confirm('确定要执行此语句吗？此操作将不可恢复');
}
</script>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>