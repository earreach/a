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
<td class="tl"><span class="f_red">*</span> 排名模块</td>
<td>
<select name="post[mid]" id="mid">
<?php 
foreach($MODULE as $v) {
	if(($v['moduleid'] > 0 && $v['moduleid'] < 4) || $v['islink']) continue;
	echo '<option value="'.$v['moduleid'].'"'.($mid == $v['moduleid'] ? ' selected' : '').'>'.$v['name'].'</option>';
} 
?>
</select>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 关键词</td>
<td><input type="text" size="40" name="post[word]" id="word" value="<?php echo $word;?>"/> <span id="dword" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 出价</td>
<td><input type="text" size="20" name="post[price]" id="price" value="<?php echo $price;?>"/> <span id="dprice" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 单位</td>
<td>
<label><input type="radio" name="post[currency]" value="money" <?php if($currency == 'money') echo 'checked';?>/> <?php echo $DT['money_name'];?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[currency]" value="credit" <?php if($currency == 'credit') echo 'checked';?>/> <?php echo $DT['credit_name'];?></label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 信息ID</td>
<td><input type="text" size="10" name="post[tid]" id="key_id" value="<?php echo $tid;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-sort.png" width="11" height="11" title="选择信息ID" class="c_p" onclick="select_item(Dd('mid').value+'&itemid='+Dd('key_id').value);"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-link.png" width="11" height="11" title="打开信息" class="c_p" onclick="if(Dd('key_id').value){window.open('<?php echo gourl('?mid=');?>'+Dd('mid').value+'&itemid='+Dd('key_id').value);}else{Dmsg('请填写信息ID','key_id');}"/> <span id="dkey_id" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 投放时段</td>
<td><?php echo dcalendar('post[fromtime]', $fromtime, '-', 1);?> 至 <?php echo dcalendar('post[totime]', $totime, '-', 1);?> <span id="dtime" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员名称</td>
<td><input type="text" size="20" name="post[username]" id="username" value="<?php echo $username;?>"/>&nbsp;&nbsp;&nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 排名状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 通过</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 待审</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注事项</td>
<td><input type="text" size="60" name="post[note]" value="<?php echo $note;?>"/></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'word';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请输入关键词', f);
		return false;
	}
	f = 'price';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写出价', f);
		return false;
	}
	f = 'key_id';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写信息ID', f);
		return false;
	}	
	if(Dd('postfromtime').value.length != 19 || Dd('posttotime').value.length != 19) {
		Dmsg('请选择投放时段', 'time');
		return false;
	}
	f = 'username';
	l = Dd(f).value.length;
	if(l < 3) {
		Dmsg('请填写会员名称', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>