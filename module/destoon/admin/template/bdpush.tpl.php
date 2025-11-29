<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="status">
<option value="0"<?php if($status == 0) echo ' selected';?>>状态</option>
<option value="1"<?php if($status == 1) echo ' selected';?>>成功</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>失败</option>
</select>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
</form>
</div>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="150">推送时间</th>
<th>网址</th>
<th width="150">结果</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td align="left">&nbsp;&nbsp;<a href="<?php echo $v['url'];?>" target="_blank"><?php echo $v['url'];?></a></td>
<td><?php echo $v['error'] ? '<span class="f_red">'.$v['error'].'</span>' : '<span class="f_green">成功</span>';?></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="批量重推" class="btn" onclick="if(confirm('确定要删除选中记录吗？仅重推未成功的记录')){this.form.action='?file=<?php echo $file;?>&action=push'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除30天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>