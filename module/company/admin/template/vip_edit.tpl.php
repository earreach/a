<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><a href="javascript:;" onclick="_user('<?php echo $username;?>');" class="t"><?php echo $username;?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员组</td>
<td>
<?php foreach($GROUP as $g) {
	if($g['vip'] > 0) echo '<label><input type="radio" name="post[groupid]" value="'.$g['groupid'].'" '.($groupid == $g['groupid'] ? 'checked' : '').'/> '.$g['groupname'].'</label>&nbsp;';
}
?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 开通时间</td>
<td><?php echo dcalendar('post[fromtime]', $fromtime);?> &nbsp; <span class="f_gray">不建议修改</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 到期时间</td>
<td><?php echo dcalendar('post[totime]', $totime);?> &nbsp; <span class="f_gray">不建议修改</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 第三方认证</td>
<td>
<label><input type="radio" name="post[validated]" value="1" <?php if($validated) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[validated]" value="0" <?php if(!$validated) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 认证名称或机构</td>
<td><input type="text" name="post[validator]" size="30" value="<?php echo $validator;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 认证日期</td>
<td><?php echo dcalendar('post[validtime]', $validtime);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo VIP;?>指数修正值</td>
<td><input type="text" name="post[vipr]" size="2" value="<?php echo $vipr;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><input type="text" name="note" size="60" value=""/></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=vip');"/></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	return true;
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>