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
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $level_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" size="10" name="itemid" value="<?php echo $itemid;?>" placeholder="信息ID" title="信息ID"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo ajax_category_select('catid', '行业/职位', $catid, $moduleid);?>&nbsp;
<?php echo ajax_area_select('areaid', '工作地点', $areaid);?>&nbsp;
<select name="gender">
<?php
foreach($GENDER as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $gender ? ' selected' : '';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<select name="type">
<?php
foreach($TYPE as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $type ? ' selected' : '';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<select name="marriage">
<?php
foreach($MARRIAGE as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $marriage ? ' selected' : '';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<select name="education">
<?php
foreach($EDUCATION as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $education ? ' selected' : '';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<select name="experience">
<option value="0">工作经验</option>
<?php for($i = 1; $i < 21; $i++) { ?>
<option value="<?php echo $i;?>" <?php echo $i == $experience ? ' selected' : '';?>><?php echo $i;?>年以上</option>
<?php
}
?>
</select>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>更新时间</option>
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>发布时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>到期时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
薪资：<input name="minsalary" type="text" id="minsalary" size="5" value="<?php echo $minsalary;?>"/> 至 <input name="maxsalary" type="text" id="maxsalary" size="5" value="<?php echo $maxsalary;?>"/> <?php echo $DT['money_unit'];?>/月&nbsp;
<label><input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/>图片&nbsp;</label>
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60"><a href="javascript:;" onclick="Dq('thumb',<?php echo $thumb ? 0 : 1;?>);">图片</a></th>
<th>标题</th>
<th>行业</th>
<th>职位</th>
<th>部门</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 29 ? 30 : 29;?>');">人数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 30 ? 'asc' : ($order == 29 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 25 ? 26 : 25;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 26 ? 'asc' : ($order == 25 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员</th>
<?php if($timetype == 'add') {?>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">添加时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php } else { ?>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">更新时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php } ?>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">浏览 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($order == 9 || $order == 10) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">点赞 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 11 || $order == 12) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">反对 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 13 || $order == 14) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">收藏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 15 || $order == 16) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">打赏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 17 || $order == 18) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">赏金 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 19 || $order == 20) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');">分享 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 21 || $order == 22) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 21 ? 22 : 21;?>');">举报 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 22 ? 'asc' : ($order == 21 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 23 ? 24 : 23;?>');">评论 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 24 ? 'asc' : ($order == 23 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'] ? $v['thumb'] : DT_STATIC.'image/nopic60.png';?>" width="60" class="thumb"/></a></td>
<td align="left">&nbsp;
<?php if($v['status'] == 3) {?>
<a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['title'];?></a>
<?php } else { ?>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><?php echo $v['title'];?></a>
<?php } ?>
</td>
<td><a href="javascript:;" onclick="Dq('catid','<?php echo $v['parentid'];?>');"><?php echo $CATEGORY[$v['parentid']]['catname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('catid','<?php echo $v['catid'];?>');"><?php echo $CATEGORY[$v['catid']]['catname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('kw','<?php echo $v['department'];?>');"><?php echo $v['department'];?></a></td>
<td><?php echo $v['total'];?></td>
<td><?php if($v['vip']) {?><a href="javascript:;" onclick="Dq('minvip','<?php echo $v['vip'];?>',0);Dq('maxvip','<?php echo $v['vip'];?>');"><img src="<?php echo DT_SKIN;?>vip_<?php echo $v['vip'];?>.gif" title="<?php echo VIP;?>:<?php echo $v['vip'];?>" align="absmiddle"/></a><?php } ?></td>
<td title="编辑:<?php echo $v['editor'];?>">
<?php if($v['username']) { ?>
	<a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a>
<?php } else { ?>
	<a href="javascript:;" onclick="_ip(this.innerHTML);" title="游客"><?php echo $v['ip'];?></a>
<?php } ?>
</td>
<?php if($timetype == 'add') {?>
<td title="更新时间<?php echo timetodate($v['edittime'], 5);?>" class="c_p" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 5);?></td>
<?php } else { ?>
<td title="添加时间<?php echo timetodate($v['addtime'], 5);?>" class="c_p" onclick="Dq('datetype','edittime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['edittime'], 5);?></td>
<?php } ?>
<td><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&catid=<?php echo $v['catid'];?>&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 浏览记录');"><?php echo $v['hits'];?></a></td>
<?php if($order == 9 || $order == 10) { ?><td><a href="javascript:;" onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '点赞记录');"><?php echo $v['likes'];?></a></td><?php } ?>
<?php if($order == 11 || $order == 12) { ?><td><a href="javascript:;" onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '反对记录');"><?php echo $v['hates'];?></a></td><?php } ?>
<?php if($order == 13 || $order == 14) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=favorite&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 收藏记录');"><?php echo $v['favorites'];?></a></td><?php } ?>
<?php if($order == 15 || $order == 16) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 打赏记录');"><?php echo $v['awards'];?></a></td><?php } ?>
<?php if($order == 17 || $order == 18) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 打赏记录');"><?php echo $v['award'];?></a></td><?php } ?>
<?php if($order == 19 || $order == 20) { ?><td><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&itemid=<?php echo $v['itemid'];?>&kw=share.php', '[<?php echo $v['alt'];?>] 分享记录');"><?php echo $v['shares'];?></a></td><?php } ?>
<?php if($order == 21 || $order == 22) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '举报记录');"><?php echo $v['reports'];?></a></td><?php } ?>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=3&file=comment&mid=<?php echo $moduleid;?>&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 评论列表');"><?php echo $v['comments'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php include tpl('notice_chip');?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>

<?php if($action == 'check') { ?>

<input type="submit" value="通过审核" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check';"/>&nbsp;
<input type="submit" value="拒 绝" class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';"/>&nbsp;
<input type="submit" value="移动分类" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'expire') { ?>

<span class="f_red f_r">
批量延长过期时间 <input type="text" size="3" name="days" id="days" value="30"/> 
天 <input type="submit" value="确 定" class="btn" onclick="if(Dd('days').value==''){alert('请填写天数');return false;}if(confirm('确定要延长'+Dd('days').value+'天吗？')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=expire&refresh=1&extend=1'}else{return false;}"/>
</span>

<input type="submit" value="刷新过期" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=expire&refresh=1';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'reject') { ?>

<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'recycle') { ?>

<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="还 原" class="btn" onclick="if(confirm('确定要还原选中<?php echo $MOD['name'];?>吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value="清 空" class="btn-r" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>&nbsp;

<?php } else { ?>

<input type="submit" value="刷新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=refresh';" title="刷新时间为最新"/>&nbsp;
<input type="submit" value="更新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;
<?php if($MOD['show_html']) { ?><input type="submit" value=" 生成网页 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=tohtml';"/>&nbsp; <?php } ?>
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="移动分类" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>&nbsp;

<?php } ?>

</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>