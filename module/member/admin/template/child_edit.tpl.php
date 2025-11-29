<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
if($DT['md5_pass']) {
	load('md5.js');
	echo '<script type="text/javascript">$(function(){cls_pwd();});</script>';
}
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 所属会员</td>
<td><input name="post[parent]" type="text" id="parent" size="20" value="<?php echo $parent;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('parent').value);"/> &nbsp; <span id="dparent" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><input name="post[username]" type="text" id="username" size="20" value="<?php echo $username;?>"/> <span class="f_gray"><?php echo $MOD['minusername'];?>-<?php echo $MOD['maxusername'];?>个字符，只能使用小写字母(a-z)、数字(0-9)、下划线(_)、中划线(-)，且以字母或数字开头和结尾</span> <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 密码</td>
<td><input name="post[password]" type="password" id="password" size="20" autocomplete="new-password"/> <span class="f_gray"><?php echo $MOD['minpassword'];?>-<?php echo $MOD['maxpassword'];?>个字符，区分大小写，推荐使用数字、字母和特殊符号组合<?php if($action=='edit') { ?>，如不修改请留空<?php } ?></span>  <span id="dpassword" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 昵称</td>
<td><input type="text" size="20" name="post[nickname]" id="nickname" value="<?php echo $nickname;?>"/> <span class="f_gray">在线交谈时显示</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 角色</td>
<td><input type="text" size="20" name="post[role]" id="role" value="<?php echo $role;?>"/> <span class="f_gray">例如：财务、编辑、客服</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 部门</td>
<td><input name="post[department]" type="text" id="department" size="30" value="<?php echo $department;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 姓名</td>
<td><input name="post[truename]" type="text" id="truename" size="10" value="<?php echo $truename;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 性别</td>
<td>
<label><input type="radio" name="post[gender]" value="1"<?php if($gender==1) { ?> checked="checked"<?php } ?>/> 先生</label>&nbsp;&nbsp;
<label><input type="radio" name="post[gender]" value="2"<?php if($gender==2) { ?> checked="checked"<?php } ?>/> 女士</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 手机</td>
<td><input name="post[mobile]" type="text" id="mobile" size="20" value="<?php echo $mobile;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 权限</td>
<td>
<style type="text/css">
.permission {width:500px;}
.permission li {float:left;width:120px;padding:6px 0;}
</style>
<ul class="permission">
<?php if(is_array($CHILD)) { foreach($CHILD as $k => $v) { ?>
<li><label><input type="checkbox" name="post[permission][]" value="<?php echo $k;?>" id="pm_<?php echo $k;?>"<?php if(in_array($k, $permission)) { ?> checked="checked"<?php } ?>/> <?php echo $v;?></label></li>
<?php } } ?>
<?php if(is_array($MENUMODS)) { foreach($MENUMODS as $k => $v) { ?>
<li><label><input type="checkbox" name="post[permission][]" value="<?php echo $v;?>" id="m_<?php echo $v;?>"<?php if(in_array($v, $permission)) { ?> checked="checked"<?php } ?>/> <?php echo $MODULE[$v]['name'];?>管理</label></li>
<?php } } ?>
</ul>
<br style="clear:both;"/><span id="dpermission" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?> id="status_3"/> 启用</label>&nbsp;&nbsp;
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?> id="status_2"/> 禁用</label>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'parent';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写所属会员', f);
		return false;
	}
	f = 'username';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写用户名', f);
		return false;
	}
	<?php if($action == 'add') { ?>
		f = 'password';
		l = Dd(f).value.length;
		if(l < 6) {
			Dmsg('请填写密码', f);
			return false;
		}
	<?php } ?>
		f = 'password';
		l = Dd(f).value.length;
		if(l > 5) {
			var a = Dpwd(Dd(f).value, '<?php echo $MOD['minpassword'];?>', '<?php echo $MOD['maxpassword'];?>', '<?php echo $MOD['mixpassword'];?>');
			var s = '';
			if(a[0] == 'mix') {
				if(a[1] == '09') s = '密码必须包含数字';
				if(a[1] == 'az') s = '密码必须包含小写字母';
				if(a[1] == 'AZ') s = '密码必须包含大写字母';
				if(a[1] == '..') s = '密码必须包含特殊符号';
			} else if(a[0] == 'min') {
				s = '密码最少'+a[1]+'字符';
			} else if(a[0] == 'max') {
				s = '密码最多'+a[1]+'字符';
			}
			if(s) {
				Dmsg(s, f);
				return false;
			}
			<?php if($DT['md5_pass']==3) { ?>
			Dd(f).value = hex_md5(Dd(f).value);
			<?php } ?>
		}
	l = $('.permission :checkbox:checked').length;
	if(l < 1) {
		Dmsg('请选择权限', 'permission');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>