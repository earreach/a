<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>
&nbsp;<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $level_select;?>&nbsp;
<select name="ontop">
<option value="0">置顶</option>
<option value="1"<?php if($ontop == 1) echo ' selected';?>>本圈</option>
<option value="2"<?php if($ontop == 2) echo ' selected';?>>全局</option>
</select>&nbsp;
<select name="style">
<option value="0">高亮</option>
<?php
foreach($COLOR as $k=>$v) {
?>
<option value="<?php echo $k;?>" style="color:#<?php echo $k;?>;"<?php if($style == '#'.$k) echo ' selected';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&gid=<?php echo $gid;?>' : ''));"/>
</td>
</tr>
<tr>
<td>
&nbsp;<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>发布时间</option>
<option value="replytime"<?php if($datetype == 'replytime') echo ' selected';?>>回复时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>修改时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo category_select('catid', '不限分类', $catid, $moduleid);?>&nbsp;
<?php echo $DT['city'] ? ajax_area_select('areaid', '不限地区', $areaid).'&nbsp;' : '';?>
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="gid" value="<?php echo $gid;?>" size="6" title="商圈ID" placeholder="商圈ID"/>&nbsp;
<input type="text" size="6" name="itemid" value="<?php echo $itemid;?>" title="帖子ID" placeholder="帖子ID"/>&nbsp;
<label><input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/>图片</label>&nbsp;
<label><input type="checkbox" name="guest" value="1"<?php echo $guest ? ' checked' : '';?>/>游客</label>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<input type="hidden" name="gid" value="<?php echo $gid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>商圈</th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60"><a href="javascript:;" onclick="Dq('thumb',<?php echo $thumb ? 0 : 1;?>);">图片</a></th>
<th>标题</th>
<th>会员</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">浏览 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">点赞 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($order == 11 || $order == 12) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">反对 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 13 || $order == 14) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">收藏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 15 || $order == 16) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">打赏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 17 || $order == 18) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">赏金 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 19 || $order == 20) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');">分享 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 21 || $order == 22) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 21 ? 22 : 21;?>');">举报 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 22 ? 'asc' : ($order == 21 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 25 ? 26 : 25;?>');">回复 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 26 ? 'asc' : ($order == 25 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="<?php echo $v['groupurl'];?>" target="_blank"><?php echo $v['groupname'];?></a></td>
<td>
<?php if($v['ontop']) { ?>
<img src="<?php echo DT_SKIN;?>club_ontop_<?php echo $v['ontop'];?>.gif" alt="" title="<?php if($v['ontop']==1) { ?>本圈<?php } else { ?>全局<?php } ?>
置顶"/>
<?php } else if($v['level']) { ?>
<img src="<?php echo DT_SKIN;?>club_level_<?php echo $v['level'];?>.gif" alt="" title="精华<?php echo $v['level'];?>"/>
<?php } else if($v['video']) { ?>
<img src="<?php echo DT_SKIN;?>club_video.gif" alt="" title="有视频"/>
<?php } else { ?>
&nbsp;
<?php } ?>
</td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'] ? $v['thumb'] : DT_STATIC.'image/nopic60.png';?>" width="60" class="thumb"/></a></td>
<td>
<div class="lt">
<?php if($v['status'] == 3) {?>
<a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['title'];?></a>
<?php } else { ?>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>" class="t"><?php echo $v['title'];?></a>
<?php } ?>
<div>
回复:<span class="c_p" onclick="Dq('datetype','replytime');Dq('date',this.innerHTML);"><?php echo timetodate($v['replytime'], 6);?></span><br/>
添加:<span class="c_p" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 6);?></span>
</div>
</div>
</td>
<td title="编辑:<?php echo $v['editor'];?>">
<?php if($v['username']) { ?>
	<a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a>
<?php } else { ?>
	<a href="javascript:;" onclick="_ip(this.innerHTML);" title="游客"><?php echo $v['ip'];?></a>
<?php } ?>
</td>
<td><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&catid=<?php echo $v['catid'];?>&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 浏览记录');"><?php echo $v['hits'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '点赞记录');"><?php echo $v['likes'];?></a></td>
<?php if($order == 11 || $order == 12) { ?><td><a href="javascript:;" onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '反对记录');"><?php echo $v['hates'];?></a></td><?php } ?>
<?php if($order == 13 || $order == 14) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=favorite&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 收藏记录');"><?php echo $v['favorites'];?></a></td><?php } ?>
<?php if($order == 15 || $order == 16) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 打赏记录');"><?php echo $v['awards'];?></a></td><?php } ?>
<?php if($order == 17 || $order == 18) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 打赏记录');"><?php echo $v['award'];?></a></td><?php } ?>
<?php if($order == 19 || $order == 20) { ?><td><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&itemid=<?php echo $v['itemid'];?>&kw=share.php', '[<?php echo $v['alt'];?>] 分享记录');"><?php echo $v['shares'];?></a></td><?php } ?>
<?php if($order == 21 || $order == 22) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '举报记录');"><?php echo $v['reports'];?></a></td><?php } ?>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=reply&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 回复管理');"><?php echo $v['reply'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>&gid=<?php echo $gid;?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php include tpl('notice_chip');?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>

<?php if($action == 'check') { ?>

<input type="submit" value="通过审核" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check';"/>&nbsp;
<input type="submit" value="拒 绝" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>

<?php } else if($action == 'reject') { ?>

<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>

<?php } else if($action == 'recycle') { ?>

<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="还 原" class="btn" onclick="if(confirm('确定要还原选中<?php echo $MOD['name'];?>吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value="清 空" class="btn-r" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>

<?php } else { ?>

<input type="submit" value="更新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;
<?php if($MOD['show_html']) { ?><input type="submit" value=" 生成网页 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=tohtml';"/>&nbsp; <?php } ?>
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="移动帖子" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<?php echo level_select('level', '加精</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>&nbsp;
<select name="ontop" onchange="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=ontop';this.form.submit();"><option value="0">置顶</option><option value="0">取消</option><option value="1">本圈</option><option value="2">全局</option></select>&nbsp;
<select name="style" onchange="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=style';this.form.submit();"><option value="0">高亮</option><option value="0">取消</option>
<?php
foreach($COLOR as $k=>$v) {
?>
<option value="<?php echo $k;?>" style="color:#<?php echo $k;?>;"><?php echo $v;?></option>
<?php
}
?>
</select>

<?php } ?>

</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">
$(function(){
	Menuon(<?php echo $menuid;?>);
	$('.thumb').on('error', function(e) {
		 $(this).attr('src', '<?php echo DT_STATIC;?>image/nopic60.png');
	});
});
</script>
<?php include tpl('footer');?>