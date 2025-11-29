<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="order" value="<?php echo $order;?>"/>
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="job" onchange="if(this.value) Dq('job', this.value);">
<option value=""<?php if(!$job) echo ' selected';?>>年月</option>
<?php foreach($jobs as $k=>$v) { ?>
<option value="<?php echo $k;?>"<?php if($job == $k) echo ' selected';?>><?php echo $v;?></option>
<?php } ?>
</select>&nbsp;
<?php if($days) { ?>
<select name="day" onchange="if(this.value) Dq('day', this.value);">
<option value=""<?php if(!$day) echo ' selected';?>>日</option>
<?php foreach($days as $k=>$v) { ?>
<option value="<?php echo $k;?>"<?php if($day == $k) echo ' selected';?>><?php echo $v;?></option>
<?php } ?>
</select>&nbsp;
<?php } else { ?>
<?php echo dcalendar('date', $date);?>&nbsp;
<?php } ?>
<?php echo $order_select;?>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<?php if($files) { ?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="400"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">文件名 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="160"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">文件大小 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="160"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">修改时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th></th>
</tr>
<?php foreach($files as $k=>$v) {?>
<tr align="center">
<td align="left" class="f_fd">&nbsp;<img src="file/ext/<?php echo $v['ico'];?>.gif" alt="" align="absmiddle"/> <a href="javascript:Dwidget('?file=<?php echo $file;?>&action=view&auth=<?php echo $v['auth'];?>', '文件查看');"><?php echo $v['name'];?></a></td>
<td><?php echo $v['size'];?> K</td>
<td><?php echo $v['time'];?></td>
<td></td>
</tr>
<?php }?>
</table>
<?php } ?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>