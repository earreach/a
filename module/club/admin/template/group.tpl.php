<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$itemid) show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $level_select;?>&nbsp;
<select name="chat">
<option value="-1"<?php if($chat == -1) echo ' selected';?>>群聊</option>
<option value="1"<?php if($chat == 1) echo ' selected';?>>开启</option>
<option value="0"<?php if($chat == 0) echo ' selected';?>>关闭</option>
</select>&nbsp;
<?php echo category_select('catid', '不限分类', $catid, $moduleid);?>&nbsp;
<?php echo ajax_area_select('areaid', '不限地区', $areaid);?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>分类</th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');"> <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="90">图标</th>
<th>名称</th>
<th>创建者</th>
<th width="130">
<?php if($order == 10 || $order == 9) { ?>
<a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">群聊时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a>
<?php } elseif($order == 8 || $order == 7) { ?>
<a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">回复时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a>
<?php } elseif($order == 6 || $order == 5) { ?>
<a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">发帖时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a>
<?php } elseif($order == 4 || $order == 3) { ?>
<a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">修改时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a>
<?php } else { ?>
<a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">创建时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a>
<?php } ?>
</th>
<?php if($itemid) { ?>
<th>ID</th>
<?php } else { ?>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">帖子 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">回复 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">粉丝 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">群聊 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">发帖</th>
<th width="40">管理</th>
<th width="40">修改</th>
<?php } ?>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td<?php if($itemid) { ?> onclick="window.parent.Dd('tocatid').value='<?php echo $v['itemid'];?>';window.parent.cDialog();"<?php } ?>><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="<?php echo $v['caturl'];?>" target="_blank"><?php echo $v['catname'];?></a></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'];?>" style="width:80px;padding:5px;"/></a></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['passport'];?></a></td>
<td>
<?php
if($order == 10 || $order == 9) {
	echo timetodate($v['chattime'], 5);
} else if($order == 8 || $order == 7) {
	echo timetodate($v['replytime'], 5);
} else if($order == 6 || $order == 5) {
	echo timetodate($v['posttime'], 5);
} else if($order == 4 || $order == 3) {	
	echo $v['editdate'];
} else {
	echo $v['adddate'];
}
?>
</td>
<?php if($itemid) { ?>
<td onclick="window.parent.Dd('tocatid').value='<?php echo $v['itemid'];?>';window.parent.cDialog();" class="c_p" title="点击选择"><?php echo $v['itemid'];?></td>
<?php } else { ?>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&gid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 帖子管理');"><?php echo $v['post'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=reply&gid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 回复管理');"><?php echo $v['reply'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=fans&gid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 粉丝管理');"><?php echo $v['fans'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=chat&action=view&chatid=<?php echo $v['chatid'];?>', '聊天记录');" title="查看聊天记录"><?php echo $v['chat'] ? '<span class="f_green">开启</span>' : '<span class="f_red">关闭</span>';?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&action=add&gid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 帖子管理');"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="发帖" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=manage&gid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 帖子管理记录');"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="管理记录" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
<?php } ?>
</tr>
<?php }?>
</table>
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
<input type="submit" value="更新商圈" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;
<?php if($MOD['list_html']) { ?><input type="submit" value=" 生成商圈 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=tohtml';"/>&nbsp; <?php } ?>
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>

<?php } ?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>