<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="nav">
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>"<?php echo $typeid == 0 ? ' class="b"' : '';?>>全部类型</a>
<?php
foreach($TYPE as $k=>$v) {
	if($k > 0) {
?>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&typeid=<?php echo $k;?>"<?php echo $typeid == $k  ? ' class="b"' : '';?>><?php echo $v;?></a>
<?php
	}
}
?>
</div>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo $type_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="text" size="5" name="width" value="<?php echo $width;?>" placeholder="宽度" title="宽度"/>&nbsp;
<input type="text" size="5" name="height" value="<?php echo $height;?>" placeholder="高度" title="高度"/>&nbsp;
<span data-hide-1200="1">
<select name="open">
<option value="-1"<?php if($open == -1) echo ' selected';?>>前台</option>
<option value="1"<?php if($open == 1) echo ' selected';?>>显示</option>
<option value="0"<?php if($open == 0) echo ' selected';?>>隐藏</option>
</select>&nbsp;
</span>
<span data-hide-1200="1">
<select name="sign">
<option value="-1"<?php if($sign == -1) echo ' selected';?>>广告标识</option>
<option value="1"<?php if($sign == 1) echo ' selected';?>>显示</option>
<option value="0"<?php if($sign == 0) echo ' selected';?>>隐藏</option>
</select>&nbsp;
</span>
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="40">排序</th>
<th width="60">ID</th>
<th width="100">广告类型</th>
<th width="15"></th>
<th>广告位名称</th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">宽度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">高度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">价格 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">广告 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="200">HTML调用代码</th>
<th data-hide-1200="1" width="200">JS调用代码</th>
<th width="40">添加</th>
<th width="40">广告</th>
<th width="40">预览</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" name="编辑:<?php echo $v['editor'];?>&#10;更新时间:<?php echo $v['editdate'];?>">
<td><input type="checkbox" name="pids[]" value="<?php echo $v['pid'];?>"/></td>
<td><input type="text" size="2" name="listorder[<?php echo $v['pid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['pid'];?></td>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $v['typename'];?></a></td>
<td><?php if($v['thumb']) {?> <a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo DT_STATIC;?>admin/img.png" width="16" height="16" title="广告位示意图,点击查看" alt=""/></a><?php } ?></td>
<td align="left" title="添加时间:<?php echo $v['adddate'];?>&#10;编辑:<?php echo $v['editor'];?>&#10;上次修改:<?php echo $v['editdate'];?>"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=list&pid=<?php echo $v['pid'];?>', '[<?php echo $v['alt'];?>] 广告管理');"><?php echo $v['name'];?></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('width','<?php echo $v['width'];?>');"><?php echo $v['width'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('height','<?php echo $v['height'];?>');"><?php echo $v['height'];?></a></td>
<td data-hide-1200="1">
<?php
if($v['prices']) {
	if(count($v['prices']) == 1) {
		foreach($v['prices'] as $kk=>$vv) {
			echo $vv.$unit.'/'.$PTYPE[$kk];
		}
	} else {
		echo '<select>';
		foreach($v['prices'] as $kk=>$vv) {
			echo '<option>'.$vv.$unit.'/'.$PTYPE[$kk].'</option>';
		}
		echo '</select>';
	}
} else {
	echo '面议';
}
?>
</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=list&pid=<?php echo $v['pid'];?>', '[<?php echo $v['alt'];?>] 广告管理');"><?php echo $v['ads'];?></a></td>
<td data-hide-1200="1"><input type="text" size="20" <?php if($v['typeid'] == 6 || $v['typeid'] == 7) { ?>value="{ad($moduleid,$catid,$kw,<?php echo $v['typeid'];?>)}"<?php } else { ?>value="{ad(<?php echo $v['pid'];?>)}"<?php } ?> id="htm-<?php echo $v['pid'];?>"/><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#htm-<?php echo $v['pid'];?>" onclick="Dtoast('代码已复制');"/></td>
<td data-hide-1200="1"><input type="text" size="20" <?php if($v['typeid'] > 1 && $v['typeid'] < 5) { ?>value="<script type=&quot;text/javascript&quot; src=&quot;{DT_PATH}file/script/A<?php echo $v['pid'];?>.js&quot;></script>"<?php } else { ?>value="不支持" disabled<?php } ?> id="js-<?php echo $v['pid'];?>"/><?php if($v['typeid'] > 1 && $v['typeid'] < 5) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#js-<?php echo $v['pid'];?>" onclick="Dtoast('代码已复制');"/><?php } else { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" onclick="Dtoast('不支持复制');"/><?php } ?></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=add&pid=<?php echo $v['pid'];?>', '[<?php echo $v['alt'];?>] 广告管理');"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="向此广告位添加广告" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=list&pid=<?php echo $v['pid'];?>', '[<?php echo $v['alt'];?>] 广告管理');"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="此广告位广告列表" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&pid=<?php echo $v['pid'];?>" target="_blank"/><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="预览此广告位" alt=""></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit_place&pid=<?php echo $v['pid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改此广告位" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更新排序" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=order_place';"/>&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中广告位吗？\n\n广告位下的所有广告也将被删除\n\n此操作不可撤销\n\n强烈建议不要删除系统自带的广告位')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete_place'}else{return false;}"/>&nbsp;&nbsp;&nbsp;
提示：系统会定期自动更新广告，如果需要立即看到效果，请点更新广告
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php if(isset($id) && isset($tm) && $id && $tm > $DT_TIME) { ?>
<script type="text/javascript">Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=add&pid=<?php echo $id;?>', '请添加广告');</script>
<?php } ?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
Menuon(1);
</script>
<?php include tpl('footer');?>