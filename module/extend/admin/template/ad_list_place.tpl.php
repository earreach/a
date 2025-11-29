<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menusad);
?>
<div class="tt">请选择广告位</div>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
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
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="60">ID</th>
<th width="100">广告类型</th>
<th width="15"></th>
<th>广告位名称</th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">宽度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">高度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">价格 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">添加</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" name="编辑:<?php echo $v['editor'];?>&#10;更新时间:<?php echo $v['editdate'];?>">
<td><?php echo $v['pid'];?></td>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $v['typename'];?></a></td>
<td><?php if($v['thumb']) {?> <a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo DT_STATIC;?>admin/img.png" width="16" height="16" title="广告位示意图,点击查看" alt=""/></a><?php } ?></td>
<td align="left" title="添加时间:<?php echo $v['adddate'];?>&#10;编辑:<?php echo $v['editor'];?>&#10;上次修改:<?php echo $v['editdate'];?>"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=add&pid=<?php echo $v['pid'];?>"><?php echo $v['name'];?></td>
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
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=add&pid=<?php echo $v['pid'];?>"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="向此广告位添加广告" alt=""/></a></td>
</tr>
<?php }?>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>