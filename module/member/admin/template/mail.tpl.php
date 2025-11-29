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
<?php echo $type_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>添加时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>修改时间</option>
<option value="sendtime"<?php if($datetype == 'sendtime') echo ' selected';?>>发送时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>分类</th>
<th>标题</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">添加时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">发送时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130">订阅人数</th>
<th width="40">发送</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $v['type'];?></a></td>
<td align="left"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','sendtime',0);Dq('date',this.innerHTML);"><?php echo $v['sendtime'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=list&typeid=<?php echo $v['typeid'];?>', '订阅列表');"><?php echo $v['num'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=send&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/start.png" width="16" height="16" title="发送邮件" alt=""/></a></td>
<td title="编辑:<?php echo $v['editor'];?>,上次修改:<?php echo $v['edittime'];?>"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php if(!$TYPE) { ?>
<script type="text/javascript">Dwidget('?file=type&item=<?php echo $file;?>', '启用邮件订阅，请先添加订阅分类');</script>
<?php } ?>
<script type="text/javascript">Menuon(1);</script>
<br/>
<?php include tpl('footer');?>