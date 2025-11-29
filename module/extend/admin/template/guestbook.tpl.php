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
<select name="type">
<option value=""<?php if($type == 0) echo ' selected';?>>留言类型</option>
<?php foreach($TYPE as $k=>$v) { ?>
<option value="<?php echo $v;?>"<?php if($type == $v) echo ' selected';?>><?php echo $v;?></option>
<?php } ?>
</select>&nbsp;
<select name="status">
<option value="0"<?php if($status == 0) echo ' selected';?>>显示</option>
<option value="3"<?php if($status == 3) echo ' selected';?>>是</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>否</option>
</select>&nbsp;
<select name="reply">
<option value="0"<?php if($reply == 0) echo ' selected';?>>回复</option>
<option value="1"<?php if($reply == 1) echo ' selected';?>>是</option>
<option value="2"<?php if($reply == 2) echo ' selected';?>>否</option>
</select>&nbsp;
<select name="hidden">
<option value="0"<?php if($hidden == 0) echo ' selected';?>>匿名</option>
<option value="1"<?php if($hidden == 1) echo ' selected';?>>是</option>
<option value="2"<?php if($hidden == 2) echo ' selected';?>>否</option>
</select>&nbsp;
<?php echo $DT['city'] ? ajax_area_select('areaid', '地区(分站)', $areaid).'&nbsp;' : '';?>
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&tid=<?php echo $tid;?>&rid=<?php echo $rid;?>' : ''));"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>留言时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>回复时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo $module_select;?>&nbsp;
<input type="text" size="10" name="tid" value="<?php echo $tid;?>" placeholder="原文ID" title="原文ID"/>&nbsp;
<input type="text" size="10" name="rid" value="<?php echo $rid;?>" placeholder="回复/评论ID" title="回复/评论ID"/>&nbsp;
<label><input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/>图片</label>&nbsp;
<label><input type="checkbox" name="video" value="1"<?php echo $video ? ' checked' : '';?>/>视频</label>&nbsp;
<label><input type="checkbox" name="guest" value="1"<?php echo $guest ? ' checked' : '';?>/>游客</label>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="20"></th>
<th>留言标题</th>
<th>类型</th>
<th>会员</th>
<th data-hide-1200="1">IP</th>
<th data-hide-1200="1">归属地</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">留言时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">回复时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="40">显示</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td>
<?php if($v['video']) { ?>
<img src="<?php echo DT_SKIN;?>club_video.gif" alt="" title="包含视频" class="c_p" onclick="Dq('video', 1);"/>
<?php } elseif($v['thumbs']) { ?>
<img src="<?php echo DT_SKIN;?>club_thumb.gif" alt="" title="包含图片" class="c_p" onclick="Dq('thumb', 1);"/>
<?php } ?>
</td>
<td align="left"><div class="h"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><?php echo $v['title'];?></a></div></td>
<td><a href="javascript:;" onclick="Dq('type',this.innerHTML);"><?php echo $v['type'];?></a></td>
<td>
<?php if($v['username']) { ?>
<a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a>
<?php } else { ?>
<a href="javascript:;" onclick="Dq('guest', 1);">游客</a>
<?php } ?>
</td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('fields',13,0);Dq('kw','='+this.innerHTML);"><?php echo $v['ip'];?></a></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo ip2area($v['ip'], 2);?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.title);" title="<?php echo $v['adddate'];?>"><?php echo timetoread($v['addtime'], 5);?></a></td>
<td title="<?php echo $v['editor'];?>"><a href="javascript:;" onclick="Dq('datetype','edittime',0);Dq('date',this.title);" title="<?php echo $v['editdate'];?>"><?php echo timetoread($v['edittime'], 5);?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('status',<?php echo $v['status'] == 3 ? 3 : 2;?>);"><?php echo $v['status'] == 3 ? '<span class="f_red">是</span>' : '否';?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 删除留言 " class="btn-r" onclick="if(confirm('确定要删除选中留言吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value=" 设置显示 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=3';"/>&nbsp;
<input type="submit" value=" 设置隐藏 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=2';"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>