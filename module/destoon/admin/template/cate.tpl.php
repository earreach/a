<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="120">模块</th>
<th width="100">数量</th>
<th width="100">添加</th>
<th width="100">管理</th>
<th></th>
</tr>
<?php
foreach($MODULE as $k=>$v) {
	if($v['islink'] || $v['moduleid'] < 4) continue;
	$v['num'] = $db->count($DT_PRE.'category', "moduleid=".$v['moduleid']);
?>
<tr align="center">
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['name'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?mid=<?php echo $v['moduleid'];?>&file=category', '管理分类 - <?php echo $v['name'];?>');"><?php echo $v['num'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?mid=<?php echo $v['moduleid'];?>&file=category&action=add', '管理分类 - <?php echo $v['name'];?>');"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="添加分类" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?mid=<?php echo $v['moduleid'];?>&file=category', '管理分类 - <?php echo $v['name'];?>');" class="t"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="管理分类" alt=""/></a></td>
<td></td>
</tr>
<?php } ?>
</table>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>