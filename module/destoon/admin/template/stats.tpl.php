<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo dcalendar('fromdate', $fromdate);?> 至 <?php echo dcalendar('todate', $todate);?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
</form>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th width="100"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 20 ? 19 : 20;?>');">日期 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1400="1" width="80">星期</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">总IP <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">电脑IP <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">手机IP <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">总UV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">电脑UV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">手机UV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">总PV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">电脑PV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">手机PV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');">爬虫PV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 21 ? 22 : 21;?>');">电脑PV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 22 ? 'asc' : ($order == 21 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 23 ? 24 : 23;?>');">手机PV <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 24 ? 'asc' : ($order == 23 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59', '[<?php echo $v['date'];?>] PV记录');"><?php echo $v['date'];?></a></td>
<td data-hide-1400="1" ><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59', '[<?php echo $v['date'];?>] UV记录');"><?php echo $v['week'];?></a></td>
<td bgcolor="#F9F9F9"><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59', '[<?php echo $v['date'];?>] 浏览记录');"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&pc=1', '[<?php echo $v['date'];?>] 电脑浏览记录');"><?php echo $v['ip_pc'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&pc=0', '[<?php echo $v['date'];?>] 手机浏览记录');"><?php echo $v['ip_mb'];?></a></td>
<td bgcolor="#F9F9F9"><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59', '[<?php echo $v['date'];?>] 浏览记录');"><?php echo $v['uv'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&pc=1', '[<?php echo $v['date'];?>] 电脑浏览记录');"><?php echo $v['uv_pc'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=uv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&pc=0', '[<?php echo $v['date'];?>] 手机浏览记录');"><?php echo $v['uv_mb'];?></a></td>
<td bgcolor="#F9F9F9"><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59', '[<?php echo $v['date'];?>] 浏览记录');"><?php echo $v['pv'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&pc=1', '[<?php echo $v['date'];?>] 电脑浏览记录');"><?php echo $v['pv_pc'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&pc=0', '[<?php echo $v['date'];?>] 手机浏览记录');"><?php echo $v['pv_mb'];?></a></td>
<td data-hide-1200="1" bgcolor="#F9F9F9"><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&robot=all', '[<?php echo $v['date'];?>] 爬虫记录');"><?php echo $v['rb'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&robot=all&pc=1', '[<?php echo $v['date'];?>] 电脑爬虫记录');"><?php echo $v['rb_pc'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=pv&fromdate=<?php echo $v['date'];?> 00:00:00&todate=<?php echo $v['date'];?> 23:23:59&robot=all&pc=0', '[<?php echo $v['date'];?>] 手机爬虫记录');"><?php echo $v['rb_mb'];?></a></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>