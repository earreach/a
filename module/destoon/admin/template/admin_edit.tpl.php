<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php if($action == 'add') { ?>
<style type="text/css">
#ro div {width:25%;float:left;height:30px;}
</style>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td>
<input type="text" size="20" name="username" id="username" value="<?php echo $username;?>"/>
&nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/>
&nbsp; <img src="<?php echo DT_STATIC;?>image/ico-new.png" width="16" height="16" title="添加会员" class="c_p" onclick="Dwidget('?moduleid=2&action=add', '添加会员');"/>
<span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 管理类别</td>
<td>
<div class="b10">&nbsp;</div>
<label><input type="radio" name="admin" value="1" id="admin_1" onclick="Dh('ro');" checked/> 超级管理员</label> <span class="f_gray">拥有除创始人特权外的所有权限</span>
<div class="b10">&nbsp;</div>
<label><input type="radio" name="admin" value="2" id="admin_2" onclick="Ds('ro');"/> 普通管理员</label> <span class="f_gray">拥有系统分配的权限</span>
<div class="b10">&nbsp;</div>
</td>
</tr>
<tbody id="ro" style="display:none;">
<tr>
<td class="tl"><span class="f_hid">*</span> 选择权限</td>
<td>
<?php 
foreach($MODULE as $m) {
	if($m['moduleid'] == 1 || $m['moduleid'] == 3 || $m['islink']) continue;
?>
<div><label><input type="checkbox" name="roles[<?php echo $m['moduleid'];?>]" value="1" id="ro_<?php echo $m['moduleid'];?>"/> <?php echo $m['name'];?>模块管理员</label></div>
<?php } ?>
<div><label><input type="checkbox" name="roles[template]" value="1" id="ro_template"/> 模板风格管理员</label></div>
<div><label><input type="checkbox" name="roles[database]" value="1" id="ro_database"/> 数据库管理员</label></div>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分站权限</td>
<td><?php echo ajax_area_select('aid', '请选择分站');?></td>
</tr>
</tbody>
<tr>
<td class="tl"><span class="f_hid">*</span> 角色名称</td>
<td><input type="text" size="20" name="role" id="role"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">可以为角色名称，例如编辑、美工、某分站编辑等，也可以为该管理员的备注</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="下一步" class="btn-g"></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'username';
	l = Dd(f).value;
	if(l == '') {
		Dmsg('请填写会员名', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php } else { ?>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<input type="hidden" name="username" value="<?php echo $user['username'];?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 会员名</td>
<td><a href="javascript:;" onclick="_user('<?php echo $user['username'];?>');" class="t"><?php echo $user['username'];?></a> <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 管理员类别</td>
<td>
<div class="b10">&nbsp;</div>
<label><input type="radio" name="admin" value="1" id="admin_1"<?php echo $user['admin'] == 1 ? ' checked' : '';?>/> 超级管理员</label> <span class="f_gray">拥有除创始人特权外的所有权限</span>
<div class="b10">&nbsp;</div>
<label><input type="radio" name="admin" value="2" id="admin_2"<?php echo $user['admin'] == 2 ? ' checked' : '';?>/> 普通管理员</label> <span class="f_gray">拥有系统分配的权限</span>
<div class="b10">&nbsp;</div>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分站权限</td>
<td><?php echo ajax_area_select('aid', '请选择', $user['aid']);?> <span class="f_gray">分站权限仅对<span class="f_red">普通管理员</span>生效</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 角色名称</td>
<td><input type="text" size="20" name="role" id="role" value="<?php echo $user['role'];?>"/> <span class="f_gray">可以为角色名称，例如编辑、美工、某分站编辑等，也可以为该管理员的备注</span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"></div>
</form>
<script type="text/javascript">Menuon(1);</script>
<?php } ?>
<?php include tpl('footer');?>