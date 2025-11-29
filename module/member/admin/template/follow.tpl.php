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
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="fusername" value="<?php echo $fusername;?>" size="10" placeholder="关注会员" title="关注会员 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<label><input type="checkbox" name="status" value="1"<?php if($status) { ?> checked<?php } ?>/> 互关</label>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="60">头像</th>
<th>会员</th>
<th>昵称</th>
<th width="60">头像</th>
<th>关注</th>
<th>昵称</th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">关注时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60">互关</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><img src="<?php echo useravatar($v['username']);?>" width="48" height="48" class="avatar c_p" onclick="_user('<?php echo $v['username'];?>');"/></td>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:Dq('username','<?php echo $v['username'];?>');"><?php echo $v['passport'];?></a></td>
<td><img src="<?php echo useravatar($v['fusername']);?>" width="48" height="48" class="avatar c_p" onclick="_user('<?php echo $v['fusername'];?>');"/></td>
<td ondblclick="Dq('fusername','<?php echo $v['fusername'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['fusername'];?>');"><?php echo $v['fusername'];?></a></td>
<td><a href="javascript:Dq('fusername','<?php echo $v['fusername'];?>');"><?php echo $v['fpassport'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 5);?></a></td>
<td><?php if($v['status']) { ?><img src="<?php echo DT_STATIC;?>image/ico-followed.png" title="互相关注" class="c_p" onclick="Dq('status', 1);"/><?php } ?></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>