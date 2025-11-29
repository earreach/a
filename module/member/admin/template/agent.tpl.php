<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="status">
<option value="0"<?php if($status == 0) echo ' selected';?>>状态</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>待审核</option>
<option value="3"<?php if($status == 3) echo ' selected';?>>已通过</option>
</select>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="pusername" value="<?php echo $pusername;?>" size="10" placeholder="代理会员" title="关注会员 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>公司</th>
<th>会员</th>
<th>代理</th>
<th>会员</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">折扣 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">订单数量 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">分销订单 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">总销售额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">年销售额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">月销售额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">加入时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">状态 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td align="left"><a href="javascript:Dq('username','<?php echo $v['username'];?>');"><?php echo $v['company'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td align="left"><a href="javascript:Dq('pusername','<?php echo $v['pusername'];?>');"><?php echo $v['pcompany'];?></a></td>
<td ondblclick="Dq('pusername','<?php echo $v['pusername'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['pusername'];?>')"><?php echo $v['pusername'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','=<?php echo $v['discount'];?>');"><?php echo $v['discount'];?>%</a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&seller=<?php echo $v['username'];?>&buyer=<?php echo $v['pusername'];?>&fromdate=<?php echo $v['adddate'];?> 00:00:00', '查看订单');"><?php echo $v['orders'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&seller=<?php echo $v['username'];?>&inviter=<?php echo $v['pusername'];?>&fromdate=<?php echo $v['adddate'];?> 00:00:00', '查看订单');"><?php echo $v['trades'];?></a></td>
<td data-hide-1200="1"><?php echo $DT['money_sign'];?><?php echo $v['amount'];?></td>
<td data-hide-1200="1"><?php echo $DT['money_sign'];?><?php echo $v['amounty'];?></td>
<td data-hide-1200="1"><?php echo $DT['money_sign'];?><?php echo $v['amountm'];?></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 5);?></a></td>
<td><a href="javascript:;" onclick="Dq('status',<?php echo $v['status'];?>);"><?php echo $v['status'] == 3 ? '<span class="f_green">已通过</span>' : '<span class="f_blue">待审核</span>';?></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>