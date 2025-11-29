<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 规则内容</td>
<td><textarea id="rule" name="rule" style="width:98%;height:500px;"></textarea></td>
</tr>
</table>
<div class="sbt"><input type="submit" value="导 入" class="btn-g"/> <span id="drule" class="f_red"></span></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'rule';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请填写规则内容', f);
		return false;
	}
	return true;
}
Menuon(0);
</script>
<?php include tpl('footer');?>