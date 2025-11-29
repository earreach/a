<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="卖家" title="卖家 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>添加时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>更新时间</option>
<option value="fromtime"<?php if($datetype == 'fromtime') echo ' selected';?>>开始时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>结束时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mtype">
<option value="price"<?php if($mtype == 'price') echo ' selected';?>>优惠额度</option>
<option value="cost"<?php if($mtype == 'cost') echo ' selected';?>>最低消费</option>
<option value="amount"<?php if($mtype == 'amount') echo ' selected';?>>数量限制</option>
<option value="number"<?php if($mtype == 'number') echo ' selected';?>>领券人数</option>
</select>&nbsp;
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="8"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="8"/>&nbsp;
<select name="open">
<option value="-1"<?php if($open == -1) echo ' selected';?>>领取</option>
<option value="1"<?php if($open == 1) echo ' selected';?>>开启</option>
<option value="0"<?php if($open == 0) echo ' selected';?>>关闭</option>
</select>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>优惠名称</th>
<th>卖家</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">优惠额度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">最低消费 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">数量限制 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">领券人数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">开始时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">结束时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>状态</th>
<th data-hide-1200="1" data-hide-1400="1" width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">添加时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="130">备注</th>
<th width="40">赠送</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('kw','<?php echo $v['title'];?>');"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['price'];?>',0);Dq('maxamount','<?php echo $v['price'];?>',0);Dq('mtype','price');"><?php echo $DT['money_sign'];?><?php echo $v['price'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['cost'];?>',0);Dq('maxamount','<?php echo $v['cost'];?>',0);Dq('mtype','cost');"><?php echo $DT['money_sign'];?><?php echo $v['cost'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['amount'];?>',0);Dq('maxamount','<?php echo $v['amount'];?>',0);Dq('mtype','amount');"><?php echo $v['amount'];?></a></td>
<td ondblclick="Dq('minamount','<?php echo $v['number'];?>',0);Dq('maxamount','<?php echo $v['number'];?>',0);Dq('mtype','number');"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=coupon&pid=<?php echo $v['itemid'];?>', '领券记录');"><?php echo $v['number'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','fromtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['fromtime'], 5);?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['totime'], 5);?></a></td>
<td><?php echo $L['process'][$v['process']];?></td>
<td data-hide-1200="1" data-hide-1400="1" title="修改时间:<?php echo timetodate($v['edittime'], 5);?>"><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 5);?></a></td>
<td data-hide-1200="1" title="<?php echo $v['note'];?>"><input type="text" size="15" value="<?php echo $v['note'];?>"/></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=send&itemid=<?php echo $v['itemid'];?>', '赠送优惠券');"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="赠送" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中优惠吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除过期30天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>