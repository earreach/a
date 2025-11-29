<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menusad);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $type_select;?>
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>&pid=<?php echo $pid ? $pid : 0;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>结束时间</option>
<option value="fromtime"<?php if($datetype == 'fromtime') echo ' selected';?>>开始时间</option>
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>添加时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>修改时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo ajax_area_select('areaid', '地区(分站)', $areaid);?>&nbsp;
<input type="text" name="pid" value="<?php echo $pid;?>" size="8" class="t_c" title="广告位ID" placeholder="广告位ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="40">排序</th>
<th data-hide-1200="1" data-hide-1400="1">ID</th>
<?php if($pid == 0) { ?>
<th>广告类型</th>
<?php } ?>
<th width="90">广告图片</th>
<th>广告名称</th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">费用 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1">单位</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">点击 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">开始时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">结束时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 8 ? 7 : 8;?>');">剩余(天) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>状态</th>
<th data-hide-1200="1">审核</th>
<th data-hide-1200="1">会员</th>
<th width="40">预览</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="aids[]" value="<?php echo $v['aid'];?>"/></td>
<td><input type="text" size="2" name="listorder[<?php echo $v['aid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td data-hide-1200="1" data-hide-1400="1"><?php echo $v['aid'];?></td>
<?php if($pid == 0) { ?>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $TYPE[$v['typeid']];?></a></td>
<?php } ?>
<td>
<?php if($v['typeid'] == 4) { ?>
<a href="javascript:;" onclick="Dwidget('?file=upload&action=play&id=1&url=<?php echo $v['video_src'];?>', '视频播放', 480, 360, 'no');"><img src="static/image/video.gif" width="80" alt=""/></a>
<?php } else { ?>
<a href="javascript:;" onclick="_preview('<?php echo $v['image_src'];?>');"><img src="<?php echo $v['image_src'];?>" width="80" onerror="this.src='static/image/nopic80.png';" alt=""/></a>
<?php } ?>
</td>
<td align="left" title="编辑:<?php echo $v['editor'];?>&#10;添加时间:<<?php echo $v['adddate'];?>&#10;更新时间:<?php echo $v['editdate'];?>">&nbsp;<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&aid=<?php echo $v['aid'];?>&pid=<?php echo $v['pid'];?>"><?php echo $v['title'];?></a></td>
<td data-hide-1200="1" class="f_red"><?php echo $v['amount'];?></td>
<td data-hide-1200="1"><?php echo $v['currency'] == 'money' ? $DT['money_unit'] : $DT['credit_unit'];?></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=stats&pid=<?php echo $v['pid'];?>&aid=<?php echo $v['aid'];?>', '[<?php echo $v['title'];?>] 点击记录');"><?php echo $v['hits'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('datetype','fromtime',0);Dq('date',this.innerHTML);"><?php echo $v['fromdate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo $v['todate'];?></a></td>
<td data-hide-1200="1"<?php if($v['days']<5) echo ' class="f_red"';?>><?php echo $v['days'];?></td>
<td><?php echo $v['process'];?></td>
<td data-hide-1200="1"><?php echo $v['status']==3 ? '已通过' : '<span class="f_red">待审核</span>';?></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&aid=<?php echo $v['aid'];?>" target="_blank"/><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="预览此广告" alt=""></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&aid=<?php echo $v['aid'];?>&pid=<?php echo $v['pid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更新排序" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=order_ad&pid=<?php echo $pid;?>';"/>&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中广告吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&pid=<?php echo $pid;?>'}else{return false;}"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<br/>
<script type="text/javascript">Menuon(<?php echo $job == 'check' ? 2 : 1;?>);</script>
<?php include tpl('footer');?>