<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>
&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $status_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">表名 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>注释</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">字段数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">记录数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="100">校验结果</th>
<th width="40">详情</th>
</tr>
<?php foreach($dtables as $k=>$v) {?>
<?php if($v['verify'] < 2) { ?>
<tr align="center">
<td align="left" height="30"> &nbsp; <a href="javascript:;" onclick="Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');" class="f_fd"><?php echo $v['name'];?></a></td>
<td><a href="javascript:Dcomment('<?php echo $v['name'];?>', '<?php echo urlencode($v['note']);?>');" title="点击修改注释"><?php echo $v['note'] ? $v['note'] : '--';?></a></td>
<td><a href="javascript:Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');"><?php echo $v['cols'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=execute&table=<?php echo $v['name'];?>', '预览表 - <?php echo $v['name'];?>');"><?php echo $v['rows'];?></a></td>
<td class="c_p" onclick="Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');">
<?php
if($v['verify'] == 1) {
	echo '<span class="f_red">异常</span>';
} else {
	echo '<span class="f_grey">未知</span>';
}
?>
</td>
<td><a href="javascript:Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="校验结果详情" alt=""/></a></td>
</tr>
<?php } ?>
<?php } ?>
<?php foreach($dtables as $k=>$v) {?>
<?php if($v['verify'] == 2) { ?>
<tr align="center">
<td align="left" height="30"> &nbsp; <a href="javascript:;" onclick="Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');" class="f_fd"><?php echo $v['name'];?></a></td>
<td><a href="javascript:Dcomment('<?php echo $v['name'];?>', '<?php echo urlencode($v['note']);?>');" title="点击修改注释"><?php echo $v['note'] ? $v['note'] : '--';?></a></td>
<td><a href="javascript:Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');"><?php echo $v['cols'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=execute&table=<?php echo $v['name'];?>', '预览表 - <?php echo $v['name'];?>');"><?php echo $v['rows'];?></a></td>
<td><span class="f_green"><img src="<?php echo DT_STATIC;?>image/yes.png" title="通过" align="absmiddle"/></span></td>
<td><a href="javascript:Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="校验结果详情" alt=""/></a></td>
</tr>
<?php } ?>
<?php } ?>

</table>
<script type="text/javascript">
function Dict(t, n) {
	Dwidget('?file=<?php echo $file;?>&action=dict&job=verify&table='+t+'&note='+n, '校验结果 - '+t+' - '+n);
}
function Dcomment(t, n) {
	Dwidget('?file=<?php echo $file;?>&action=comment&table='+t+'&note='+n, '修改注释');
}
Menuon(4);
</script>
<?php include tpl('footer');?>