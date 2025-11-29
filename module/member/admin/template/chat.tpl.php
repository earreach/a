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
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="freadtime"<?php if($datetype == 'freadtime') echo ' selected';?>>发起人收到时间</option>
<option value="fgettime"<?php if($datetype == 'fgettime') echo ' selected';?>>发起人阅读时间</option>
<option value="treadtime"<?php if($datetype == 'treadtime') echo ' selected';?>>接收人收到时间</option>
<option value="tgettime"<?php if($datetype == 'tgettime') echo ' selected';?>>接收人阅读时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="fromuser" value="<?php echo $fromuser;?>" size="10" placeholder="发起人" title="发起人 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="touser" value="<?php echo $touser;?>" size="10" placeholder="接收人" title="接收人 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="60">头像</th>
<th>发起人</th>
<th>昵称</th>
<th data-hide-1200="1">未读消息</th>
<th width="150">最后会话</th>
<th width="60">头像</th>
<th>接收人</th>
<th>昵称</th>
<th data-hide-1200="1">未读消息</th>
<th width="150">最后会话</th>
<th width="40">来源</th>
<th width="40">查看</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="chatid[]" value="<?php echo $v['chatid'];?>"/></td>
<td><img src="<?php echo useravatar($v['fromuser']);?>" width="48" height="48" class="avatar c_p" onclick="_user('<?php echo $v['fromuser'];?>');"/></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['fromuser'];?>');"><?php echo $v['fromuser'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fromuser', '<?php echo $v['fromuser'];?>');"><?php echo $v['fpassport'];?></a></td>
<td data-hide-1200="1"><?php echo $v['fnew'];?></td>
<td title="接收时间:<?php echo timetodate($v['fgettime'], 6);?>&#10;阅读时间:<?php echo timetodate($v['freadtime'], 6);?>" class="c_p f_gray" onclick="Dq('datetype','freadtime',0);Dq('date', '<?php echo timetodate($v['freadtime'], 3);?>');"><?php echo timetoread($v['freadtime'], 6);?></td>
<td><img src="<?php echo useravatar($v['touser']);?>" width="48" height="48" class="avatar c_p" onclick="_user('<?php echo $v['touser'];?>');"/></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['touser'];?>')"><?php echo $v['touser'];?></a></td>
<td><a href="javascript:;" onclick="Dq('touser', '<?php echo $v['touser'];?>');"><?php echo $v['tpassport'];?></a></td>
<td data-hide-1200="1"><?php echo $v['tnew'];?></td>
<td title="接收时间:<?php echo timetodate($v['tgettime'], 6);?>&#10;阅读时间:<?php echo timetodate($v['treadtime'], 6);?>" class="c_p f_gray" onclick="Dq('datetype','treadtime',0);Dq('date', '<?php echo timetodate($v['treadtime'], 3);?>');"><?php echo timetoread($v['treadtime'], 6);?></td>
<td>
<?php if($v['forward']) { ?>
<a href="<?php echo $v['forward'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开来源网址" alt=""/></a>
<?php } else { ?>
&nbsp;
<?php } ?>
</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&chatid=<?php echo $v['chatid'];?>', '聊天记录');"><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="点击查看" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除交谈" class="btn-r" onclick="if(confirm('确定要删除选中交谈吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=del'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除一年之前的聊天记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>