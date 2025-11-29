<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<script type="text/javascript">var errimg = '<?php echo DT_STATIC;?>image/nopic60.png';</script>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="id" value="<?php echo $id;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词"/>&nbsp;
<?php echo $type_select;?>&nbsp;
<?php echo $status_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&id=<?php echo $id;?>');"/>
</td>
</tr>
<?php if(!$id) { ?>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>申请时间</option>
<option value="updatetime"<?php if($datetype == 'updatetime') echo ' selected';?>>更新时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-' ,1);?> 至 <?php echo dcalendar('todate', $todate, '-' ,1);?>&nbsp;
<select name="mtype">
<option value="amount"<?php if($mtype == 'amount') echo ' selected';?>>实付金额</option>
<option value="price"<?php if($mtype == 'price') echo ' selected';?>>商品单价</option>
<option value="number"<?php if($mtype == 'number') echo ' selected';?>>购买数量</option>
</select>&nbsp;
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="10"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="10"/>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="15" placeholder="订单号"/>&nbsp;
<input type="text" name="mallid" value="<?php echo $mallid;?>" size="15" placeholder="商品ID"/>&nbsp;
<input type="text" name="SKU" value="<?php echo $skuid;?>" size="15" placeholder="SKU"/>&nbsp;
<input type="text" name="seller" value="<?php echo $seller;?>" size="15" placeholder="卖家"/>&nbsp;
<input type="text" name="seller_mobile" value="<?php echo $seller_mobile;?>" size="15" placeholder="卖家手机"/>&nbsp;
<input type="text" name="buyer" value="<?php echo $buyer;?>" size="15" placeholder="买家"/>&nbsp;
<input type="text" name="buyer_mobile" value="<?php echo $buyer_mobile;?>" size="15" placeholder="买家手机"/>&nbsp;
</td>
</tr>
<?php } ?>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>单号</th>
<th width="70">缩略图</th>
<th>商品或服务</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">单价 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">数量 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">实付 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>卖家</th>
<th>买家</th>
<th width="75"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">申请时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="75"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">更新时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">类型 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">状态 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">操作</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><?php echo $v['itemid'];?></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank"><img src="<?php if($v['thumb']) { ?><?php echo $v['thumb'];?><?php } else { ?><?php echo DT_STATIC;?>image/nopic60.png<?php } ?>" width="60" height="60" onerror="this.src=errimg;"/></a></td>
<td>
<div class="lt">
<a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['title'];?></a>
</div>
</td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['price'];?>',0);Dq('maxamount','<?php echo $v['price'];?>',0);Dq('mtype','price');"><?php echo $DT['money_sign'];?><?php echo $v['price'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['number'];?>',0);Dq('maxamount','<?php echo $v['number'];?>',0);Dq('mtype','number');"><?php echo $v['number'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['amount'];?>',0);Dq('maxamount','<?php echo $v['money'];?>',0);Dq('mtype','money');"><span class="f_red"><?php echo $DT['money_sign'];?><?php echo $v['amount'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['seller'];?>');"><?php echo $v['seller'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['buyer'];?>');"><?php echo $v['buyer'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('datetype','updatetime',0);Dq('date',this.innerHTML);"><?php echo $v['updatetime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $v['typename'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $v['dstatus'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=service_show&id=<?php echo $id;?>&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="详情" alt=""/></a></td>
</tr>
<?php }?>
<tr align="center">
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td><strong>小计</strong></td>
<td class="f_red f_b"><?php echo $DT['money_sign'];?><?php echo $amount;?></td>
<td colspan="7">&nbsp;</td>
</tr>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="批量删除" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=service_delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(5);</script>
<?php include tpl('footer');?>