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
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="买家" title="买家 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="seller" value="<?php echo $seller;?>" size="10" placeholder="卖家" title="卖家 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>领取时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>更新时间</option>
<option value="fromtime"<?php if($datetype == 'fromtime') echo ' selected';?>>开始时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>结束时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mtype">
<option value="price"<?php if($mtype == 'price') echo ' selected';?>>优惠额度</option>
<option value="cost"<?php if($mtype == 'cost') echo ' selected';?>>最低消费</option>
</select>&nbsp;
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="8"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="8"/>&nbsp;
<input type="text" name="pid" value="<?php echo $pid;?>" size="8" title="优惠ID" placeholder="优惠ID"/>&nbsp;
<input type="text" name="oid" value="<?php echo $oid;?>" size="8" title="订单ID" placeholder="订单ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>优惠名称</th>
<th>买家</th>
<th>卖家</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">优惠额度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">最低消费 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1" width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">开始时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">结束时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">领取时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="80">订单ID</th>
<th width="80">状态</th>
<th width="130">备注</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('kw','<?php echo $v['title'];?>');"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="<?php if($seller){ ?>_user(this.innerHTML);<?php } else {?>Dq('seller',this.innerHTML);<?php } ?>"><?php echo $v['seller'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['price'];?>',0);Dq('maxamount','<?php echo $v['price'];?>',0);Dq('mtype','price');"><?php echo $DT['money_sign'];?><?php echo $v['price'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['cost'];?>',0);Dq('maxamount','<?php echo $v['cost'];?>',0);Dq('mtype','cost');"><?php echo $DT['money_sign'];?><?php echo $v['cost'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('datetype','fromtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['fromtime'], 5);?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['totime'], 5);?></a></td>
<td title="修改时间:<?php echo timetodate($v['edittime'], 5);?>"><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 5);?></a></td>
<td ondblclick="Dq('oid','<?php echo $v['oid'];?>');"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=order&itemid=<?php echo $v['oid'];?>', '订单详情');"><?php echo $v['oid'] ? $v['oid'] : '';?></a></td>
<td>
<?php if($v['oid']) { ?>
<span class="f_green">已使用</span>
<?php } else if($v['fromtime'] > $DT_TIME) { ?>
<span class="f_gray">未开始</span>
<?php } else if($v['totime'] < $DT_TIME) { ?>
<span class="f_red">已过期</span>
<?php } else { ?>
<span class="f_blue">待使用</span>
<?php } ?>
</td>
<td title="<?php echo $v['note'];?>"><input type="text" size="15" value="<?php echo $v['note'];?>"/></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中优惠券吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=del'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除过期30天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear_coupon');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>