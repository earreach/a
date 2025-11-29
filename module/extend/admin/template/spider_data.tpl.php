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
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $module_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&itemid=<?php echo $itemid;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>采集时间</option>
<option value="posttime"<?php if($datetype == 'posttime') echo ' selected';?>>发布时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo $status_select;?>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="10" placeholder="规则ID" title="规则ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>标题</th>
<th width="130">目标</th>
<th width="130">分类</th>
<th data-hide-1200="1" data-hide-1400="1" width="130">信息ID</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">采集时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">发布时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="100"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">状态<img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1" width="150">备注</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td align="left">&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['title'];?>] 数据预览');"><?php echo $v['title'];?></td>
<td><a href="javascript:;" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);"><?php echo $v['name'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['catname'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><?php echo $v['url'] ? '<a href="'.$v['url'].'" target="_blank">'.$v['tid'].'</a>' : '';?></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','posttime',0);Dq('date',this.innerHTML);"><?php echo $v['postdate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $dstatus[$v['status']];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><input type="text" size="15" value="<?php echo $v['note'];?>"/></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="发布数据" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=start&job=data';"/>&nbsp;
<input type="submit" value="删除数据" class="btn-r" onclick="if(confirm('确定要删除选中数据吗？此操作将不可撤销\n删除数据可能会导致相同内容重复发布 ')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&job=data'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了规避重复采集，系统仅删除30天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear&job=data');}"/>&nbsp;
<select onchange="if(this.value>-1){if(confirm('确定要改变选中数据状态吗？一般无需设置\n改变状态主要为了选中数据不再处理或重新处理')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=status&status='+this.value;this.form.submit();}}">
<option value="-1">设置状态为</option>
<option value="0">等待采集</option>
<option value="1">采集失败</option>
<option value="2">等待发布</option>
<option value="3">发布成功</option>
<option value="4">发布失败</option>
</select>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>