<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$print) show_menu($menus);
?>

<?php if($print) { ?>
<table cellspacing="0" class="tb ls" style="width:700px;">
<tr>
<th>卡号</th>
<th width="100">密码</th>
<th width="100">面额</th>
<th width="150">有效期至</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><?php echo $v['number'];?></td>
<td><?php echo $v['password'];?></td>
<td><?php echo $DT['money_sign'];?><?php echo $v['amount'];?></td>
<td><?php echo $v['todate'];?></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages" style="width:700px;" title="双击隐藏分页" ondblclick="$(this).hide();">'.$pages.'</div>' : '';?>
<?php exit; } ?>

<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="status">
<option value="0">状态</option>
<option value="1"<?php if($status == 1) echo ' selected';?>>未使用</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>已使用</option>
<option value="3"<?php if($status == 3) echo ' selected';?>>已过期</option>
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
<select name="datetype">
<option value="updatetime"<?php if($datetype == 'updatetime') echo ' selected';?>>充值时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>到期时间</option>
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>制卡时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
面额：
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="8"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="8"/>&nbsp;
<input type="text" name="number" value="<?php echo $number;?>" size="15" title="卡号" placeholder="卡号"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>卡号</th>
<th width="40">复制</th>
<th>密码</th>
<th width="40">复制</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">面额 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">有效期至 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>充值会员</th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">充值时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1">充值IP</th>
<th data-hide-1200="1" width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">制卡时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="80">状态</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('number','<?php echo $v['number'];?>');"><?php echo $v['number'];?></a></td>
<td><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#no-<?php echo $v['itemid'];?>" onclick="Dtoast('卡号 <?php echo $v['number'];?> 已复制');"/></td>
<td><?php echo $v['password'];?></td>
<td><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#pw-<?php echo $v['itemid'];?>" onclick="Dtoast('密码 <?php echo $v['password'];?> 已复制');"/></td>
<td><a href="javascript:;" onclick="Dq('minamount','<?php echo $v['amount'];?>',0);Dq('maxamount','<?php echo $v['amount'];?>');"><span class="f_blue"><?php echo $DT['money_sign'];?><?php echo $v['amount'];?></span></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo $v['todate'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','updatetime',0);Dq('date',this.innerHTML);"><?php echo $v['updatedate'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');" title="显示IP所在地"><?php echo $v['ip'];?></a></td>
<td data-hide-1200="1" title="制卡人:<?php echo $v['editor'];?>"><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td>
<?php if($v['updatetime']) { ?>
<a href="javascript:;" onclick="Dq('status',2);"><span class="f_green">已使用</span></a>
<?php } else if($v['totime'] < $DT_TIME) { ?>
<a href="javascript:;" onclick="Dq('status',3);"><span class="f_red">已过期</span></a>
<?php } else { ?>
<a href="javascript:;" onclick="Dq('status',1);"><span class="f_gray">未使用</span></a>
<?php }?>
</td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中充值卡吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="button" value=" 打印卡号 " class="btn" onclick="window.open('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&print=1');"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<div style="z-index:1000;position:absolute;top:-10000px;">
<?php if(is_array($lists)) { foreach($lists as $k => $v) { ?>
<textarea id="no-<?php echo $v['itemid'];?>"><?php echo $v['number'];?></textarea>
<textarea id="pw-<?php echo $v['itemid'];?>"><?php echo $v['password'];?></textarea>
<?php } } ?>
</div>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
Menuon(1);
</script>
<?php include tpl('footer');?>