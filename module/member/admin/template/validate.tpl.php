<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词"/>&nbsp;
<select name="action">
<option value="">认证类型</option>
<?php foreach($V as $k=>$v) { ?>
<option value="<?php echo $k;?>"<?php echo $k == $action ? ' selected' : '';?>><?php echo $v;?></option>
<?php } ?>
</select>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>提交时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>审核时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>证件1到期</option>
<option value="totime1"<?php if($datetype == 'totime1') echo ' selected';?>>证件2到期</option>
<option value="totime2"<?php if($datetype == 'totime2') echo ' selected';?>>证件3到期</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="status">
<option value="0">状态</option>
<option value="3"<?php echo $status == 3 ? ' selected' : '';?>>已认证</option>
<option value="2"<?php echo $status == 2 ? ' selected' : '';?>>未认证</option>
</select>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<?php
$nm1 = '';
$nm2 = '';
$nm3 = '';
$tt1 = '';
$tt2 = '';
$tt3 = '';
$tt = '有效期至';
if($action == 'truename') {
	$nm1 = $MOD['vtruename_v1'] ? $MOD['vtruename_v1_name'] : '';
	$nm2 = $MOD['vtruename_v2'] ? $MOD['vtruename_v2_name'] : '';
	$nm3 = $MOD['vtruename_v3'] ? $MOD['vtruename_v3_name'] : '';
	$tt1 = $MOD['vtruename_e1'] && $nm1 ? $tt : '';
	$tt2 = $MOD['vtruename_e2'] && $nm2 ? $tt : '';
	$tt3 = $MOD['vtruename_e3'] && $nm3 ? $tt : '';
} else if($action == 'bank') {
	$nm1 = $MOD['vbank_v1'] ? $MOD['vbank_v1_name'] : '';
	$nm2 = $MOD['vbank_v2'] ? $MOD['vbank_v2_name'] : '';
	$nm3 = $MOD['vbank_v3'] ? $MOD['vbank_v3_name'] : '';
	$tt1 = $MOD['vbank_e1'] && $nm1 ? $tt : '';
	$tt2 = $MOD['vbank_e2'] && $nm2 ? $tt : '';
	$tt3 = $MOD['vbank_e3'] && $nm3 ? $tt : '';
} else if($action == 'company') {
	$nm1 = $MOD['vcompany_v1'] ? $MOD['vcompany_v1_name'] : '';
	$nm2 = $MOD['vcompany_v2'] ? $MOD['vcompany_v2_name'] : '';
	$nm3 = $MOD['vcompany_v3'] ? $MOD['vcompany_v3_name'] : '';
	$tt1 = $MOD['vcompany_e1'] && $nm1 ? $tt : '';
	$tt2 = $MOD['vcompany_e2'] && $nm2 ? $tt : '';
	$tt3 = $MOD['vcompany_e3'] && $nm3 ? $tt : '';
} else if($action == 'shop') {
	$nm1 = $MOD['vshop_v1'] ? $MOD['vshop_v1_name'] : '';
	$nm2 = $MOD['vshop_v2'] ? $MOD['vshop_v2_name'] : '';
	$nm3 = $MOD['vshop_v3'] ? $MOD['vshop_v3_name'] : '';
	$tt1 = $MOD['vshop_e1'] && $nm1 ? $tt : '';
	$tt2 = $MOD['vshop_e2'] && $nm2 ? $tt : '';
	$tt3 = $MOD['vshop_e3'] && $nm3 ? $tt : '';
}
?>
<form method="post">
<table cellspacing="0" class="tb ls" id="vtb">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th><?php echo $sfields[1];?></th>
<th><?php echo $sfields[2];?></th>
<?php if($action == 'company') { ?>
<th>纳税识别号</th>
<?php } else if($action == 'bank') { ?>
<th>银行全称</th>
<th>银行帐号</th>
<?php } else if($action == 'truename') { ?>
<th>证件类型</th>
<th>证件号码</th>
<?php } ?>
<th>会员</th>
<?php if($nm1) { ?><th><?php echo $nm1;?></th><?php } ?>
<?php if($tt1) { ?><th><?php echo $tt1;?></th><?php } ?>
<?php if($nm2) { ?><th><?php echo $nm2;?></th><?php } ?>
<?php if($tt2) { ?><th><?php echo $tt2;?></th><?php } ?>
<?php if($nm3) { ?><th><?php echo $nm3;?></th><?php } ?>
<?php if($tt3) { ?><th><?php echo $tt3;?></th><?php } ?>
<th data-hide-1200="1" data-hide-1400="1">IP</th>
<th width="130">提交时间</th>
<th>操作人</th>
<th>状态</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<?php if($action == 'company') { ?>
<td><a href="<?php echo DT_PATH;?>api/company<?php echo DT_EXT;?>?wd=<?php echo $v['title'];?>" title="查询企业信息" target="_blank"><?php echo $v['title'];?></a></td>
<?php } elseif($action == 'mobile') { ?>
<td><a href="javascript:;" onclick="_mobile('<?php echo $v['title'];?>');" title="显示手机所在地"><?php echo $v['title'];?></a></td>
<?php } else { ?>
<td><a href="javascript:;" onclick="Dq('fields',1,0);Dq('kw','='+this.innerHTML);"><?php echo $v['title'];?></a></td>
<?php } ?>
<td><a href="javascript:;" onclick="Dq('fields',2,0);Dq('kw','=<?php echo $v['history'];?>');"><span class="f_gray"><?php echo $v['history'];?></span></a></td>
<?php if($action == 'company') { ?>
<td><a href="javascript:;" onclick="Dq('fields',6,0);Dq('kw','='+this.innerHTML);"><?php echo $v['title1'];?></a></td>
<?php } else if($action == 'bank') { ?>
<td><a href="javascript:;" onclick="Dq('fields',7,0);Dq('kw','='+this.innerHTML);"><?php echo $v['title2'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',6,0);Dq('kw','='+this.innerHTML);"><?php echo $v['title1'];?></a></td>
<?php } else if($action == 'truename') { ?>
<td><a href="javascript:;" onclick="Dq('fields',6,0);Dq('kw','='+this.innerHTML);"><?php echo $v['title1'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',7,0);Dq('kw','='+this.innerHTML);"><?php echo $v['title2'];?></a></td>
<?php } ?>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<?php if($nm1) { ?><td><?php if($v['thumb']) {?> <a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'];?>" width="128" alt=""/></a><?php } ?></td><?php } ?>
<?php if($tt1) { ?><td><?php echo $v['totime'] ? timetodate($v['totime'], 3) : '长期';?></td><?php } ?>
<?php if($nm2) { ?><td><?php if($v['thumb1']) {?> <a href="javascript:;" onclick="_preview('<?php echo $v['thumb1'];?>');"><img src="<?php echo $v['thumb1'];?>" width="128" alt=""/></a><?php } ?></td><?php } ?>
<?php if($tt2) { ?><td><?php echo $v['totime1'] ? timetodate($v['totime1'], 3) : '长期';?></td><?php } ?>
<?php if($nm3) { ?><td><?php if($v['thumb2']) {?> <a href="javascript:;" onclick="_preview('<?php echo $v['thumb2'];?>');"><img src="<?php echo $v['thumb2'];?>" width="128" alt=""/></a><?php } ?></td><?php } ?>
<?php if($tt3) { ?><td><?php echo $v['totime2'] ? timetodate($v['totime2'], 3) : '长期';?></td><?php } ?>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');" title="显示IP所在地"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td title="<?php echo timetodate($v['edittime']);?>"><a href="javascript:;" onclick="Dq('fields',5,0);Dq('kw','='+this.innerHTML);"><?php echo $v['editor'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $v['status'] == 3 ? '<span class="f_green">已认证</span>' : '<span class="f_red">未认证</span>';?></a></td>
</tr>
<?php }?>
</table>
<?php if($edit) {?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&job=<?php echo $action;?>'}else{return false;}"/>&nbsp;
</div>
<?php } else { ?>
<?php include tpl('notice_chip');?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="通过认证" class="btn-g" onclick="if(_check()){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check';}else{return false;}"/>&nbsp;
<input type="submit" value="拒绝认证" class="btn-r" onclick="if(_reject()){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';}else{return false;}"/>&nbsp;
<input type="submit" value="取消认证" class="btn-r" onclick="if(_cancel()){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=cancel';}else{return false;}"/>&nbsp;
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&job=<?php echo $action;?>'}else{return false;}"/>&nbsp;
</div>
<?php } ?>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">
Menuon(<?php echo $menuid;?>);
function is_reason() {
	return Dd('reason').value.length > 2 && Dd('reason').value != '操作原因';
}
function _check() {
	return true;
}
function _reject() {
	if((Dd('msg').checked || Dd('eml').checked) && !is_reason()) {
		alert('请填写操作原因或者取消通知');
		return false;
	}
	if(is_reason() && (!Dd('msg').checked && !Dd('eml').checked)) {
		alert('至少需要选择一种通知方式');
		return false;
	}
	return true;
}
function _cancel() {
	if((Dd('msg').checked || Dd('eml').checked) && !is_reason()) {
		alert('请填写操作原因或者取消通知');
		return false;
	}
	if(is_reason() && (!Dd('msg').checked && !Dd('eml').checked)) {
		alert('至少需要选择一种通知方式');
		return false;
	}
	return confirm('此操作不可撤销，确定要继续吗？');
}
$(function(){
	if(window.screen.width<1366) {
		$('.tab a').css('padding', '0 12px');
	}
	if(window.screen.width<1440) {
		$('#vtb img').css('width', '64px');
	}
});
</script>
<?php include tpl('footer');?>