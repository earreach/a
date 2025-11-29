<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="status" value="<?php echo $status;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="mid">
<option value="0">模块</option>
<?php 
foreach($MODULE as $v) {
	if(($v['moduleid'] > 0 && $v['moduleid'] < 4) || $v['islink']) continue;
	echo '<option value="'.$v['moduleid'].'"'.($mid == $v['moduleid'] ? ' selected' : '').'>'.$v['name'].'</option>';
} 
?>
</select>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&status=<?php echo $status;?>');"/>
</form>
</div>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>模块</th>
<th>关键词</th>
<th>相关词</th>
<th>拼音</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">结果 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">总搜索 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">本月 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">本周 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">今日 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>状态</th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center" title="更新时间：<?php echo timetodate($v['updatetime'], 6);?>">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('mid','<?php echo $v['moduleid'];?>');"><?php echo $MODULE[$v['moduleid']]['name'];?></a></td>
<td><input name="post[<?php echo $v['itemid'];?>][word]" type="text" size="15" value="<?php echo $v['word'];?>"/></td>
<td><input name="post[<?php echo $v['itemid'];?>][keyword]" type="text" size="15" value="<?php echo $v['keyword'];?>"/></td>
<td><input name="post[<?php echo $v['itemid'];?>][letter]" type="text" size="15" value="<?php echo $v['letter'];?>"/></td>
<td><a href="<?php echo $MODULE[$v['moduleid']]['linkurl'];?>search.php?kw=<?php echo urlencode($v['word']);?>" target="_blank"><?php echo $v['items'];?></a></td>
<td><input name="post[<?php echo $v['itemid'];?>][total_search]" type="text" size="5" value="<?php echo $v['total_search'];?>"/></td>
<td data-hide-1200="1"><input name="post[<?php echo $v['itemid'];?>][month_search]" type="text" size="5" value="<?php echo $v['month_search'];?>"/></td>
<td data-hide-1200="1"><input name="post[<?php echo $v['itemid'];?>][week_search]" type="text" size="4" value="<?php echo $v['week_search'];?>"/></td>
<td data-hide-1200="1"><input name="post[<?php echo $v['itemid'];?>][today_search]" type="text" size="3" value="<?php echo $v['today_search'];?>"/></td>
<td>
<select name="post[<?php echo $v['itemid'];?>][status]">
<option value="3"<?php echo $status==3 ? ' selected' : '';?>>启用</option>
<option value="2"<?php echo $status==2 ? ' selected' : '';?>>待审</option>
<option value="1"<?php echo $status==1 ? ' selected' : '';?>>禁止</option>
</select>
</td>
</tr>
<?php } ?>

<tr>
<th> </th>
<th>模块</th>
<th>关键词</th>
<th>相关词</th>
<th>拼音</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">结果 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">总搜索 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">本月 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">本周 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">今日 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>状态</th>
</tr>

<tr align="center">
<td class="f_green">新增</td>
<td>
<select name="post[0][moduleid]">
<?php 
foreach($MODULE as $v) {
	if(($v['moduleid'] > 0 && $v['moduleid'] < 4) || $v['islink']) continue;
	echo '<option value="'.$v['moduleid'].'">'.$v['name'].'</option>';
} 
?>
</select>
</td>
<td><input name="post[0][word]" type="text" size="15" value="" onblur="get_letter(this.value);" onkeyup="Dd('keyword').value=this.value;"/></td>
<td><input name="post[0][keyword]" type="text" size="15" id="keyword"/></td>
<td><input name="post[0][letter]" id="letter" type="text" size="15" value=""/></td>
<td><input name="post[0][items]" type="text" size="3" value="0"/></td>
<td><input name="post[0][total_search]" type="text" size="5" value="1"/></td>
<td data-hide-1200="1"><input name="post[0][month_search]" type="text" size="5" value="1"/></td>
<td data-hide-1200="1"><input name="post[0][week_search]" type="text" size="4" value="1"/></td>
<td data-hide-1200="1"><input name="post[0][today_search]" type="text" size="3" value="1"/></td>
<td>
<select name="post[0][status]">
<option value="3"<?php echo $status==3 ? ' selected' : '';?>>启用</option>
<option value="2"<?php echo $status==2 ? ' selected' : '';?>>待审</option>
<option value="1"<?php echo $status==1 ? ' selected' : '';?>>禁止</option>
</select>
</td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td height="30" colspan="10">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保 存" class="btn-g" onclick="this.form.action='?action=update&status=<?php echo $status;?>';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删除选中" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?action=delete&status=<?php echo $status;?>';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php if($status != 3) { ?>
<input type="submit" value="启用选中" class="btn" onclick="if($(':checkbox:checked').length){this.form.action='?action=status&status=3';}else{confirm('请选择要启用的项目');return false;}" />&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
<?php if($status != 2) { ?>
<input type="submit" value="待审选中" class="btn" onclick="if($(':checkbox:checked').length){this.form.action='?action=status&status=2';}else{confirm('请选择要待审的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
<?php if($status != 1) { ?>
<input type="submit" value="禁止选中" class="btn" onclick="if($(':checkbox:checked').length){this.form.action='?action=status&status=1';}else{confirm('请选择要禁止的项目');return false;}" />&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
</td>
</tr>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<div class="tt">相关说明</div>
<table cellspacing="0" class="tb">
<tr>
<td class="lh20 f_gray">
- 设置相关词可以使提示搜索或相关搜索更智能 例如关键词‘DT’可设置‘DT,DESTOON’则搜索DT和DESTOON均会提示DT相关搜索<br/>
- 多个相关词请用英文逗号分隔，为了系统检索效率，建议控制在200字内<br/>
- 如果设置了关键词状态为禁止，相关搜索结果将不予展示，如果设置‘*ABC’则关键词包含ABC即被禁止，如果设置‘ABC*DEF’则关键词包含ABC且包含DEF即被禁止
</td>
</tr>
</table>
<script type="text/javascript">
function get_letter(word) {
	$.get('?file=<?php echo $file;?>&action=letter&word='+word, function(data) {
		if(Dd('letter').value == '') Dd('letter').value = data;
	});
}
Menuon(<?php echo $menuid;?>);
</script>
<?php include tpl('footer');?>