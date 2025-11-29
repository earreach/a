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
<select name="bank">
<option value="">支付平台</option>
<?php
foreach($PAY as $k=>$v) {
	if($v['enable'] && $v['name']) echo '<option value="'.$k.'" '.($bank == $k ? 'selected' : '').'>'.$v['name'].'</option>';
}
?>
</select>&nbsp;
<?php echo $status_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="sendtime"<?php if($datetype == 'sendtime') echo ' selected';?>>下单时间</option>
<option value="receivetime"<?php if($datetype == 'receivetime') echo ' selected';?>>支付时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mtype">
<option value="amount"<?php if($mtype == 'amount') echo ' selected';?>>支付金额</option>
<option value="fee"<?php if($mtype == 'fee') echo ' selected';?>>手续费</option>
<option value="money"<?php if($mtype == 'money') echo ' selected';?>>实收金额</option>
</select>&nbsp;
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="8"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="8"/>&nbsp;
<input type="text" name="editor" value="<?php echo $editor;?>" size="10" title="操作人 双击显示资料" placeholder="操作人" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="10" title="流水号" placeholder="流水号"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">流水号 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">支付金额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">手续费 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">实收金额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员名</th>
<th>支付平台</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">下单时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">支付时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>操作人</th>
<th>状态</th>
<th width="130" data-hide-1200="1">事由</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['itemid'];?>');"><?php echo $v['itemid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['amount'];?>',0);Dq('maxamount','<?php echo $v['amount'];?>',0);Dq('mtype','amount');"><?php echo $DT['money_sign'];?><?php echo $v['amount'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['fee'];?>',0);Dq('maxamount','<?php echo $v['fee'];?>',0);Dq('mtype','fee');"><?php echo $DT['money_sign'];?><?php echo $v['fee'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['money'];?>',0);Dq('maxamount','<?php echo $v['money'];?>',0);Dq('mtype','money');"><span class="f_blue"><?php echo $DT['money_sign'];?><?php echo $v['money'];?></span></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('bank','<?php echo $v['bank'];?>');"><?php echo $PAY[$v['bank']]['name'];?></a><?php if($v['bill']) { ?> <a href="javascript:;" onclick="_preview('<?php echo $v['bill'];?>');" class="t">凭证</a><?php } ?></td>
<td><a href="javascript:;" onclick="Dq('datetype','sendtime',0);Dq('date',this.innerHTML);"><?php echo $v['sendtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','receivetime',0);Dq('date',this.innerHTML);"><?php echo $v['receivetime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('editor','<?php echo $v['editor'];?>');"><?php echo $v['editor'];?></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $v['dstatus'];?></a></td>
<td data-hide-1200="1" title="<?php echo $v['reason'];?>"><input type="text" size="15" value="<?php echo $v['reason'];?>"/></td>
</tr>
<?php }?>
<tr align="center">
<td></td>
<td><strong>小计</strong></td>
<td><?php echo $DT['money_sign'];?><?php echo $amount;?></td>
<td><?php echo $DT['money_sign'];?><?php echo $fee;?></td>
<td class="f_blue"><?php echo $DT['money_sign'];?><?php echo $money;?></td>
<td colspan="7"></td>
</tr>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 人工审核 " class="btn-g" onclick="if(confirm('确定要通过选中记录状态吗？此操作将不可撤销\n\n如果金额未到帐或金额不符，请勿进行此操作')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check'}else{return false;}"/>&nbsp;
<input type="submit" value=" 作 废 " class="btn-r" onclick="if(confirm('确定要作废选中(限未知)记录状态吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=recycle'}else{return false;}"/>&nbsp;
<input type="submit" value=" 删除记录 " class="btn-r" onclick="if(confirm('警告：确定要删除选中(限未知)记录吗？此操作将不可撤销\n\n如果无特殊原因，建议不要删除记录，以便查询对帐')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<br/>
<?php include tpl('footer');?>