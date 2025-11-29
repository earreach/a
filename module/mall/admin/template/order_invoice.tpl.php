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
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词"/>&nbsp;
<select name="status">
<option value="0"<?php if($status == 0) { ?> selected<?php } ?>>开票状态</option>
<option value="1"<?php if($status == 1) { ?> selected<?php } ?>>未开票</option>
<option value="2"<?php if($status == 2) { ?> selected<?php } ?>>已开票</option>
<option value="3"<?php if($status == 3) { ?> selected<?php } ?>>已上传</option>
<option value="4"<?php if($status == 4) { ?> selected<?php } ?>>已快递</option>
</select>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="updatetime"<?php if($datetype == 'updatetime') { ?> selected<?php } ?>>开票时间</option>
<option value="addtime"<?php if($datetype == 'addtime') { ?> selected<?php } ?>>申请时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="seller" value="<?php echo $seller;?>" size="15" placeholder="卖家"/>&nbsp;
<input type="text" name="buyer" value="<?php echo $buyer;?>" size="15" placeholder="买家"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="15" placeholder="订单号"/>&nbsp;
</td>
</tr>
</table>
</form>
<table cellspacing="0" class="tb ls">
<tr>
<th>单号</th>
<th>类型</th>
<th>抬头</th>
<th>纳税人识别号</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">金额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>卖家</th>
<th>买家</th>
<th data-hide-1200="1" data-hide-1400="1" width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">申请时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">开票时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="100">状态</th>
<th width="40">详情</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td title="订单详情"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $v['oid'];?>', '订单详情');"><?php echo $v['oid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',2,0);Dq('kw','='+this.innerHTML);"><?php echo $v['type'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',1,0);Dq('kw','='+this.innerHTML);"><?php echo $v['company'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);"><?php echo $v['taxid'];?></a></td>
<td><?php echo $DT['money_sign'];?><?php echo $v['amount'];?></td>
<td ondblclick="Dq('seller','<?php echo $v['seller'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['seller'];?>');"><?php echo $v['seller'];?></a></td>
<td ondblclick="Dq('buyer','<?php echo $v['buyer'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['buyer'];?>');"><?php echo $v['buyer'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','updatetime',0);Dq('date',this.innerHTML);"><?php echo $v['updatetime'];?></td>
<td>
<?php if($v['url']) { ?>
<a href="javascript:;" onclick="Dq('status',3);"><span class="f_green">已上传</span></a>
<?php } else if($v['send_type']) { ?>
<a href="javascript:;" onclick="Dq('status',4);"><span class="f_green">已快递</span></a>
<?php } else  { ?>
<a href="javascript:;" onclick="Dq('status',1);"><span class="f_gray">未开票</span></a>
<?php } ?>
</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=invoice_show&&itemid=<?php echo $v['itemid'];?>', '发票详情');" class="t"><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="查看" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>