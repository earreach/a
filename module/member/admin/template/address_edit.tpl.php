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
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><input type="text" size="20" name="post[username]" id="username" value="<?php echo $username;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 详细地址</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?><input name="post[address]" type="text" id="title" size="60" value="<?php echo $address;?>"/> <span id="dareaid" class="f_red"></span><span id="dtitle" class="f_red"></span></td>
</tr>
<?php if($DT['postcode']) { ?>
<tr>
<td class="tl"><span class="f_red">*</span> 邮政编码</td>
<td><input name="post[postcode]" type="text" id="postcode" size="10" value="<?php echo $postcode;?>" /> <span id="dpostcode" class="f_red"></span></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> 真实姓名</td>
<td><input name="post[truename]" type="text" id="truename" size="10" value="<?php echo $truename;?>"/> <span id="dtruename" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 手机号码</td>
<td><input name="post[mobile]" type="text" id="mobile" size="20" value="<?php echo $mobile;?>"/> <span id="dmobile" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 电话号码</td>
<td><input name="post[telephone]" type="text" id="telephone" size="20" value="<?php echo $telephone;?>"/> <span id="dtelephone" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 地址标签</td>
<td>
<input name="post[typename]" type="text" id="typename" size="20" value="<?php echo $typename;?>"/>&nbsp;
<select name="post[typeid]" onchange="if(this.value>0){$('#typename').val($(this).find('option:selected').text());}else{$('#typename').val('');}">
<?php if(is_array($L['address_type'])) { foreach($L['address_type'] as $k => $v) { ?>
<option value="<?php echo $k;?>"<?php if($typeid == $k) { ?> selected<?php } ?>><?php echo $v;?></option>
<?php } } ?>
</select>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 显示顺序</td>
<td class="f_gray"><input name="post[listorder]" type="text" id="listorder" size="4" value="<?php echo $listorder;?>"/> 数字越小越靠前<span id="dlistorder" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><input type="text" size="60" name="post[note]" id="note" value="<?php echo $note;?>"/> <span id="dnote" class="f_red"></span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'areaid_1';
	if(Dd(f).value == 0) {
		Dmsg('请选择所在地区', 'areaid', 1);
		return false;
	}
	f = 'username';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写会员名', f);
		return false;
	}
	f = 'title';
	l = Dd(f).value.length;
	if(l < 5) {
		Dmsg('请填写详细地址', f);
		return false;
	}
	<?php if($DT['postcode']) { ?>
	f = 'postcode';
	l = Dd(f).value.length;
	if(l < 6) {
		Dmsg('请填写邮政编码', f);
		return false;
	}
	<?php } ?>
	f = 'truename';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写真实姓名', f);
		return false;
	}
	f = 'mobile';
	l = Dd(f).value.length;
	if(l < 11) {
		Dmsg('请填写手机号码', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>