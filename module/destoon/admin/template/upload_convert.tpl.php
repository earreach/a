<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>上传时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>转码时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo $status_select;?>&nbsp;
<input type="text" size="10" name="username" value="<?php echo $username;?>" placeholder="会员名" title="会员名"/>&nbsp;
</td>
</tr>
</form>
<form method="post" action="?">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="20"></th>
<th>源格式</th>
<th>新格式</th>
<th>原始大小</th>
<th>会员名</th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">上传时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">转码时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="100">状态</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="<?php echo $v['url'];?>"><img src="<?php echo DT_PATH.'file/ext/'.$v['ext'].'.gif';?>"/></a></td>
<td title="<?php echo $v['fileurl'];?>"><a href="<?php echo $v['fileurl'];?>" target="_blank"><?php echo $v['fileext'];?></a></td>
<td title="<?php echo $v['fileuri'];?>"><a href="<?php echo $v['fileuri'];?>" target="_blank"><?php echo $v['toext'];?></a></td>
<td><?php echo $v['size'];?></td>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype', 'addtime', 0);Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype', 'edittime', 0);Dq('date',this.innerHTML);"><?php echo $v['edittime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status',<?php echo $v['status'];?>);"><?php echo $dstatus[$v['status']];?></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="重新转码" class="btn" onclick="if(confirm('确定要重新转码选中记录吗？建议仅选择转码失败的文件')){this.form.action='?file=<?php echo $file;?>&action=reset'}else{return false;}"/> &nbsp; &nbsp; 
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？系统同时会删除对应文件，此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&action=delete_convert'}else{return false;}" title="删除记录和对应文件"/> &nbsp; &nbsp; 
<?php if(!$lists && $kw) {?>
&nbsp;&nbsp;&nbsp;&nbsp;<span class="f_red">未找到记录</span>
<?php }?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php if($CV) { ?>
<div class="tt">加入队列</div>
<form method="post" action="?" onsubmit="return Dcheck();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="queue"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 文件地址</td>
<td><textarea style="width:640px;height:64px;" name="files" id="files" class="f_fd"></textarea></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
一行一个以 <?php echo DT_PATH;?>file/upload/ 开头的文件地址<br/>
当前支持转码的文件后缀为<?php foreach($CV as $k=>$v) {echo ' '.$k;} ?>
</td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" name="submit" value="加入队列" class="btn-g"/></td>
</tr>
</table>
</form>
<?php } ?>
<script type="text/javascript">
function Dcheck() {
	var l;
	var f;
	f = 'files';
	l = Dd(f).value.length;
	if(Dd(f).value.indexOf('/file/upload/') == -1) {
		Dtoast('请填写文件地址');
		return false;
	}
	return true;
}
Menuon(1);
</script>
<?php include tpl('footer');?>