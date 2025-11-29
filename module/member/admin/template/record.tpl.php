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
<select name="type">
<option value="0">类型</option>
<option value="1"<?php if($type == 1) echo ' selected';?>>收入</option>
<option value="2"<?php if($type == 2) echo ' selected';?>>支出</option>
</select>&nbsp;
<select name="bank">
<option value="">支付方式</option>
<?php
foreach($BANKS as $k=>$v) {
	echo '<option value="'.$v.'" '.($bank == $v ? 'selected' : '').'>'.$v.'</option>';
}
?>
</select>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mtype">
<option value="amount"<?php if($mtype == 'amount') echo ' selected';?>>收支</option>
<option value="balance"<?php if($mtype == 'balance') echo ' selected';?>>余额</option>
</select>&nbsp;
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="10"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="10"/>&nbsp;
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
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">流水号 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('type','1',0);Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">收入 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('type','2',0);Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">支出 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">余额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员名</th>
<th>支付平台</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">发生时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>操作人</th>
<th width="130">事由</th>
<th width="130" data-hide-1200="1">备注</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['itemid'];?>');"><?php echo $v['itemid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['amount'];?>',0);Dq('maxamount','<?php echo $v['amount'];?>',0);Dq('mtype','amount');"><span class="f_blue"><?php if($v['amount'] > 0) echo '+'.$v['amount'];?></span></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['amount'];?>',0);Dq('maxamount','<?php echo $v['amount'];?>',0);Dq('mtype','amount');"><span class="f_red"><?php if($v['amount'] < 0) echo $v['amount'];?></span></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['balance'];?>',0);Dq('maxamount','<?php echo $v['balance'];?>',0);Dq('mtype','balance');"><?php echo $DT['money_sign'];?><?php echo $v['balance'] ? $v['balance'] : '';?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('bank','<?php echo $v['bank'];?>');"><?php echo $v['bank'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="Dq('editor','<?php echo $v['editor'];?>');"><?php echo $v['editor'];?></a></td>
<td title="<?php echo $v['reason'];?>"><input type="text" size="15" value="<?php echo $v['reason'];?>"/></td>
<td data-hide-1200="1" title="<?php echo $v['note'];?>"><input type="text" size="15" value="<?php echo $v['note'];?>"/></td>
</tr>
<?php }?>
<tr align="center">
<td></td>
<td><strong>小计</strong></td>
<td class="f_blue">+<?php echo $income;?></td>
<td class="f_red"><?php echo $expense;?></td>
<td colspan="7">&nbsp;</td>
</tr>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除90天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<br/>
<?php include tpl('footer');?>