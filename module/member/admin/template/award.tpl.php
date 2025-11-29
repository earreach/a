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
<?php echo $module_select;?>&nbsp;
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
金额 
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="8"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="8"/>&nbsp;
<input type="text" name="tid" value="<?php echo $tid;?>" size="10" title="信息ID" placeholder="信息ID"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="10" title="流水号" placeholder="流水号"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">流水号 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">金额(<?php echo $DT['money_unit'];?>) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>模块</th>
<th>标题</th>
<th>会员名</th>
<th>IP</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">支付时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['itemid'];?>');"><?php echo $v['itemid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['fee'];?>',0);Dq('maxamount','<?php echo $v['fee'];?>');"><span class="f_blue"><?php echo $DT['money_sign'];?><?php echo $v['fee'];?></span></a></td>
<td><a href="javascript:;" onclick="Dq('mid','<?php echo $v['mid'];?>');"><?php echo $MODULE[$v['mid']]['name'];?></a></td>
<td ondblclick="Dq('mid','<?php echo $v['mid'];?>',0);Dq('tid','<?php echo $v['tid'];?>');" align="left"> &nbsp; <a href="<?php echo gourl('?mid='.$v['mid'].'&itemid='.$v['tid'].'&page=2');?>" target="_blank"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['paytime'];?></a></td>
</tr>
<?php }?>
<tr align="center">
<td></td>
<td><strong>小计</strong></td>
<td class="f_blue"><?php echo $DT['money_sign'];?><?php echo $fee;?></td>
<td colspan="5">&nbsp;</td>
</tr>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<br/>
<?php include tpl('footer');?>