<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="make" value="1"/>
<input type="hidden" name="first" value="1"/>
<table cellspacing="0" class="tb">
<?php include tpl('sendlist_chip', $module);?>
<tr>
<td class="tl"><span class="f_red">*</span> 每轮数量</td>
<td><input type="text" size="10" name="num" id="num" value="1000" class="f_fd"/> <span id="dnum" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 列表备注</td>
<td class="f_gray"><input type="text" size="60" id="note" name="note" value="全部会员"/></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="获 取" class="btn-g"/></div>
</form>
<script type="text/javascript">
function mk(v) {
	var pre = '<?php echo $DT_PRE;?>';
	var arr = v.split('|');
	if(arr[0]) Dd('tb').value = pre+arr[0].replace(/,/, ','+pre);
	if(arr[1]) Dd('sql').value = arr[1];
	if(arr[0]) Dd('note').value = $('#op').find('option:selected').text();
}
function check() {
	var l;
	var f;
	f = 'tb';
	l = Dd(f).value.length;
	if(l < 5) {
		Dmsg('请填写数据表', f);
		return false;
	}
	f = 'sql';
	l = Dd(f).value.length;
	if(l < 5) {
		Dmsg('请填写查询条件', f);
		return false;
	}
	f = 'num';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写每轮数量', f);
		return false;
	}
	f = 'note';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写列表备注', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>