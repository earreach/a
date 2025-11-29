<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $type_select;?>&nbsp;
<?php echo $level_select;?>&nbsp;
<?php echo $DT['city'] ? ajax_area_select('areaid', '地区(分站)', $areaid).'&nbsp;' : '';?>
<span data-hide-1200="1"><?php echo $order_select;?>&nbsp;</span>
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th data-hide-1200="1">ID</th>
<th data-hide-1200="1">分类</th>
<th width="16"></th>
<th>标题</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">状态 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">选项 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">票数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">浏览 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">添加时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="200">调用代码</th>
<th width="40">选项</th>
<th width="40">投票</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" title="编辑:<?php echo $v['editor'];?>&#10;更新时间:<?php echo $v['editdate'];?>">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td data-hide-1200="1"><?php echo $v['itemid'];?></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $v['typename'];?></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['title'];?></td>
<td><img src="<?php echo DT_STATIC;?>image/process_<?php echo get_process($v['fromtime'], $v['totime']);?>.gif"/></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=item&pollid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 投票选项');"><?php echo $v['items'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=record&pollid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 投票记录');"><?php echo $v['polls'];?></a></td>
<td><?php echo $v['hits'];?></td>
<td><?php echo $v['adddate'];?></td>
<td data-hide-1200="1"><input type="text" size="20" value="<script type=&quot;text/javascript&quot; src=&quot;<?php echo $EXT['poll_url'];?>index<?php echo DT_EXT;?>?action=js&itemid=<?php echo $v['itemid'];?>&quot;></script>" id="poll-<?php echo $v['itemid'];?>"/><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#poll-<?php echo $v['itemid'];?>" onclick="Dtoast('代码已复制');"/></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=item&pollid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 投票选项');"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="管理选项" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=record&pollid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 投票记录');"><img src="<?php echo DT_STATIC;?>admin/poll.png" width="16" height="16" title="投票记录" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中票选吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
Menuon(1);
</script>
<?php include tpl('footer');?>