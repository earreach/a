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
<select name="currency">
<option value="">付费方式</option>
<option value="money"<?php echo $currency == 'money' ? ' selected' : '';?>><?php echo $DT['money_name'];?></option>
<option value="credit"<?php echo $currency == 'credit' ? ' selected' : '';?>><?php echo $DT['credit_name'];?></option>
</select>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>购买时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>到期时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mtype">
<option value="fee"<?php echo $mtype == 'fee' ? ' selected' : '';?>>模板价格</option>
<option value="number"<?php echo $mtype == 'number' ? ' selected' : '';?>>购买时长</option>
<option value="amount"<?php echo $mtype == 'amount' ? ' selected' : '';?>>订单总额</option>
</select>&nbsp;
<input type="text" size="10" name="minfee" value="<?php echo $minfee;?>"/> 至
<input type="text" size="10" name="maxfee" value="<?php echo $maxfee;?>"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="10" placeholder="模板ID" title="模板ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="100">预览图</th>
<th>模板名称</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">价格 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">时长(月) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">订单总额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员名</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">购买时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">到期时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">剩余(天) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td title="点击预览"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $v['styleid'];?>" target="_blank"><img src="<?php echo $v['thumb'];?>" width="80"/></a></td>
<td><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['styleid'];?>');"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minfee','<?php echo $v['fee'];?>',0);Dq('maxfee','<?php echo $v['fee'];?>',0);Dq('mtype','fee');"><?php echo $v['fee'] ? ($v['currency'] == 'money' ? '<span class="f_red">'.$v['fee'].$DT['money_unit'].'/月</span>' : '<span class="f_blue">'.$v['fee'].$DT['credit_unit'].'/月</span>') : '<span class="f_green">免费</span>';?></a></td>
<td><a href="javascript:;" onclick="Dq('minfee','<?php echo $v['number'];?>',0);Dq('maxfee','<?php echo $v['number'];?>',0);Dq('mtype','number');"><?php echo $v['number'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minfee','<?php echo $v['amount'];?>',0);Dq('maxfee','<?php echo $v['amount'];?>',0);Dq('mtype','amount');"><?php echo $v['currency'] == 'money' ? '<span class="f_red">'.$v['amount'].$DT['money_unit'].'</span>' : '<span class="f_blue">'.$v['amount'].$DT['credit_unit'].'</span>';?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo $v['todate'];?></a></td>
<td><?php echo $v['days'] ? $v['days'] : '<span class="f_red">过期</span>';?></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中模板吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=del'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>