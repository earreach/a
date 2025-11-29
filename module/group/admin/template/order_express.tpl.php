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
<?php echo $status_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<input type="text" name="send_no" value="<?php echo $send_no;?>" size="15" placeholder="快递单号"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="15" placeholder="订单号"/>&nbsp;
<input type="text" name="seller" value="<?php echo $seller;?>" size="15" placeholder="卖家"/>&nbsp;
<input type="text" name="buyer" value="<?php echo $buyer;?>" size="15" placeholder="买家"/>&nbsp;
<input type="text" name="mobile" value="<?php echo $mobile;?>" size="15" placeholder="买家手机"/>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>下单时间</option>
<option value="updatetime"<?php if($datetype == 'updatetime') echo ' selected';?>>更新时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-' ,1);?> 至 <?php echo dcalendar('todate', $todate, '-' ,1);?>&nbsp;
</td>
</tr>
</table>
</form>
<table cellspacing="0" class="tb ls">
<tr>
<th>单号</th>
<th>快递公司</th>
<th>快递单号</th>
<th>快递状态</th>
<th>卖家</th>
<th>买家</th>
<th data-hide-1200="1">收件地址</th>
<th>收件人</th>
<th>收件手机</th>
<th width="150">下单时间</th>
<th width="150">更新时间</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td title="订单详情"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&id=<?php echo $v['gid'];?>&itemid=<?php echo $v['itemid'];?>', '订单详情');"><?php echo $v['itemid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',2,0);Dq('kw','='+this.innerHTML);"><?php echo $v['send_type'];?></a></td>
<td title="快递追踪"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&id=<?php echo $v['gid'];?>&itemid=<?php echo $v['itemid'];?>', '快递追踪');"><?php echo $v['send_no'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status',<?php echo $v['send_status'];?>);"><?php echo $dsend_status[$v['send_status']];?></a></td>
<td ondblclick="Dq('seller','<?php echo $v['seller'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['seller'];?>');"><?php echo $v['seller'];?></a></td>
<td ondblclick="Dq('buyer','<?php echo $v['buyer'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['buyer'];?>');"><?php echo $v['buyer'];?></a></td>
<td align="left" data-hide-1200="1"><a href="javascript:;" onclick="Dq('fields',7,0);Dq('kw','='+this.innerHTML);"><?php echo $v['buyer_address'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',6,0);Dq('kw','='+this.innerHTML);"><?php echo $v['buyer_name'];?></a></td>
<td><a href="javascript:;" onclick="Dq('mobile',this.innerHTML);"><?php echo $v['buyer_mobile'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','updatetime',0);Dq('date',this.innerHTML);"><?php echo $v['updatetime'];?></a></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>