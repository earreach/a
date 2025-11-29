<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="40" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="status">
<option value="0"<?php if($status == 0) echo ' selected';?>>状态</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>失败</option>
<option value="3"<?php if($status == 3) echo ' selected';?>>成功</option>
</select>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="发送人" title="发送人 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="mobile" value="<?php echo $mobile;?>" size="12" placeholder="手机" title="手机 双击显示会员资料" ondblclick="if(this.value){_user(this.value,'mobile');}"/>&nbsp;
<input type="text" name="ip" value="<?php echo $ip;?>" size="12" placeholder="IP" title="IP"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="120">手机</th>
<th>短信</th>
<th width="80">字数</th>
<th width="80">分条</th>
<th width="150">发送时间</th>
<th width="150">发送人</th>
<th width="100" data-hide-1200="1">IP</th>
<th width="40">状态</th>
<th width="180">发送结果</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('mobile','<?php echo $v['mobile'];?>');"><?php echo $v['mobile'];?></a></td>
<td align="left" class="lh20"><?php echo $v['message'];?></td>
<td><?php echo $v['word'];?></td>
<td><?php echo $v['num'];?></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['sendtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('username','<?php echo $v['editor'];?>');"><?php echo $v['editor'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $v['status'] == 3 ? '<span class="f_green">成功</span>' : '<span class="f_red">失败</span>';?></a></td>
<td><textarea style="width:150px;height:24px;"><?php echo $v['code'];?></textarea></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="批量删除" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除90天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<br/>
<?php include tpl('footer');?>