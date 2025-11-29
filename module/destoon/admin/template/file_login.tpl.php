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
<td class="tl"><span class="f_hid">*</span> 系统提示</td>
<td class="f_red">当前操作权限较高，需要验证操作密码 <?php tips('操作密码见网站根目录 config.inc.php 文件 $CFG[editfile] 项');?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 操作密码</td>
<td><input type="password" name="pwd" size="30" id="pwd" autocomplete="off"/> <span id="dpwd" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"> </td>
<td><input type="submit" name="submit" value="验证" class="btn-g"/></td>
</tr>
</form>
</table>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'pwd';
	l = Dd(f).value.length;
	if(l < 6) {
		Dmsg('请填写操作密码', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>