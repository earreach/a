<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="20" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词"/>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="status">
<option value="0">状态</option>
<option value="3"<?php echo $status == 3 ? ' selected' : '';?>>已注销</option>
<option value="2"<?php echo $status == 2 ? ' selected' : '';?>>未审核</option>
</select>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>会员</th>
<th>注销原因</th>
<th>IP</th>
<th width="130">提交时间</th>
<th>操作人</th>
<th>状态</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><textarea style="width:400px;height:16px;" title="<?php echo $v['title'];?>"><?php echo $v['title'];?></textarea></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');" title="显示IP所在地"><?php echo $v['ip'];?></a></td>
<td><?php echo $v['adddate'];?></td>
<td title="<?php echo timetodate($v['edittime']);?>"><?php echo $v['editor'];?></td>
<td><?php echo $v['status'] == 3 ? '<span class="f_green">已注销</span>' : '<span class="f_red">未审核</span>';?></td>
</tr>
<?php }?>
</table>
<?php if(!$edit) {?>
<?php include tpl('notice_chip');?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="拒绝申请" class="btn-g" onclick="if(_reject()){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=close_reject';}else{return false;}"/>&nbsp;
<input type="submit" value="通过申请" class="btn-r" onclick="if(_check()){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=close_check';}else{return false;}"/>&nbsp;
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&job=<?php echo $action;?>'}else{return false;}"/>&nbsp;
</div>
</form>
<?php } ?>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">
Menuon(<?php echo $menuid;?>);
function is_reason() {
	return Dd('reason').value.length > 2 && Dd('reason').value != '操作原因';
}
function _check() {
	return confirm('通过申请将会彻底删除会员所有数据和文件，此操作将不可恢复，确定要删除吗？');
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
$(function(){
	if(window.screen.width<1366) {
		$('.tab a').css('padding', '0 12px');
	}
});
</script>
<?php include tpl('footer');?>