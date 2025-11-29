<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<select name="status">
<option value="0"<?php if($status == 0) echo ' selected';?>>状态</option>
<option value="1"<?php if($status == 1) echo ' selected';?>>过期</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>有效</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="300">缓存ID</th>
<th width="150">体积</th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">到期时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="150">剩余</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="cid[]" value="<?php echo $v['cacheid'];?>"/></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=show&cid=<?php echo $v['cacheid'];?>', '查看缓存数据 ID:<?php echo $v['cacheid'];?>', 800, 600);"><?php echo $v['cacheid'];?></a></td>
<td><?php echo $v['size'];?></td>
<td><a href="javascript:;" onclick="Dq('date', this.innerHTML);"><?php echo $v['todate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status', <?php echo $v['expire'] ? 1 : 2;?>);"><?php echo $v['left'];?></a></td>
<td></td>
</tr>
<?php }?>
</table>
<div class="ts"><br/>
&nbsp; &nbsp; &nbsp; &nbsp;当前为文件缓存，不推荐使用，建议配置基于内存的缓存方式，例如Memcache、Redis<br/>
&nbsp; &nbsp; &nbsp; &nbsp;一般情况下，无需手动点击删除或清空，系统会自动处理<br/>
</div>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;&nbsp;
<input type="submit" value=" 清空过期 " class="btn-r" onclick="if(confirm('确定要清空过期记录吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&action=clear'}else{return false;}"/>&nbsp;&nbsp;
<input type="submit" value=" 清空全部 " class="btn-r" onclick="if(confirm('确定要清空全部记录吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&action=clear&job=all'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>