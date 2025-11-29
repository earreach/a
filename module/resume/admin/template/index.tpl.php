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
<?php echo $level_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" size="10" name="itemid" value="<?php echo $itemid;?>" placeholder="简历ID" title="简历ID"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo ajax_category_select('catid', '行业/职位', $catid, $moduleid);?>&nbsp;
<?php echo ajax_area_select('areaid', '居住地点', $areaid);?>&nbsp;
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
</select>&nbsp;
<select name="open">
<option value="0">公开状态</option>
<option value="1"<?php echo $open == 1 ? ' selected' : '';?>>关闭</option>
<option value="2"<?php echo $open == 2 ? ' selected' : '';?>>仅网站可见</option>
<option value="3"<?php echo $open == 3 ? ' selected' : '';?>>开放</option>
</select>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>更新时间</option>
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>发布时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
薪资：
<span title="期望薪资"><input name="minsalary" type="text" id="minsalary" size="5" value="<?php echo $minsalary;?>"/> 至 <input name="maxsalary" type="text" id="maxsalary" size="5" value="<?php echo $maxsalary;?>"/> <?php echo $DT['money_unit'];?>/月</span>
<label><input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/>照片&nbsp;</label>
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60"><a href="javascript:;" onclick="Dq('thumb',<?php echo $thumb ? 0 : 1;?>);">照片</a></th>
<th>姓名</th>
<th width="16"></th>
<th>期望职位</th>
<th>期望行业</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">学历 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">年龄 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>居住地</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">工作经验 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">浏览 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>

<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'] ? $v['thumb'] : DT_STATIC.'image/nopic60.png';?>" width="60" class="thumb"/></a></td>
<td>
<div class="lt">
<?php if($v['status'] == 3) {?>
<a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['truename'];?></a>
<?php } else { ?>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>" class="t"><?php echo $v['truename'];?></a>
<?php } ?>
<div>
更新:<span class="c_p" onclick="Dq('datetype','edittime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['edittime'], 6);?></span><br/>
添加:<span class="c_p" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 6);?></span>
</div>
</div>
</td>
<td><a href="javascript:;" onclick="Dq('gender','<?php echo $v['gender'];?>');"><?php echo $v['gender'] == 1 ? '男' : '女';?></a></td>
<td><a href="javascript:;" onclick="Dq('catid','<?php echo $v['catid'];?>');"><?php echo $CATEGORY[$v['catid']]['catname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('catid','<?php echo $v['parentid'];?>');"><?php echo $CATEGORY[$v['parentid']]['catname'];?></a></td>
<td title="<?php echo $v['school'];?>"><a href="javascript:;" onclick="Dq('education','<?php echo $v['education'];?>');"><?php echo $EDUCATION[$v['education']];?></a></td>
<td><?php echo $v['age'];?>岁</td>
<td><a href="javascript:;" onclick="Dq('areaid','<?php echo $v['areaid'];?>');"><?php echo $AREA[$v['areaid']]['areaname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('experience','<?php echo $v['experience'];?>');"><?php echo $v['experience'] ? $v['experience'].'年' : '无';?></a></td>
<td title="编辑:<?php echo $v['editor'];?>">
<?php if($v['username']) { ?>
	<a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a>
<?php } else { ?>
	<a href="javascript:;" onclick="_ip(this.innerHTML);" title="游客"><?php echo $v['ip'];?></a>
<?php } ?>
</td>
<td><?php echo $v['hits'];?></td>
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

<?php } else if($action == 'reject') { ?>


<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'recycle') { ?>

<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中招聘吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="还 原" class="btn" onclick="if(confirm('确定要还原选中<?php echo $MOD['name'];?>吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value="清 空" class="btn-r" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>

<?php } else { ?>

<input type="submit" value="刷新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=refresh';" title="刷新时间为最新"/>&nbsp;
<input type="submit" value="更新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中简历吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="移动分类" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>

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