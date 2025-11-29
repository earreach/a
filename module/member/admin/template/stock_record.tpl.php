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
<select name="type">
<option value="0">类型</option>
<option value="1" <?php if($type==1) { ?>selected<?php } ?>>入库</option>
<option value="2" <?php if($type==2) { ?>selected<?php } ?>>出库</option>
</select>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="skuid" value="<?php echo $skuid;?>" size="20" placeholder="条形编码" title="条形编码"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="10" placeholder="商品ID" title="商品ID"/>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="160"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">发生时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('type',1,0);Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">入库 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('type',2,0);Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">出库 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">库存 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>商品名称</th>
<th>条形编码</th>
<th width="130">会员</th>
<th width="180">事由</th>
<th width="180" data-hide-1200="1">备注</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td class="f_blue"><?php if($v['amount'] > 0) { ?><?php echo $v['amount'];?><?php } else { ?>&nbsp;<?php } ?></td>
<td class="f_red"><?php if($v['amount'] < 0) { ?><?php echo $v['amount'];?><?php } else { ?>&nbsp;<?php } ?></td>
<td><?php if($v['balance']) { ?><?php echo $v['balance'];?><?php } else { ?>&nbsp;<?php } ?></td>
<td><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['stockid'];?>');"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="Dq('skuid','<?php echo $v['skuid'];?>');"><?php echo $v['skuid'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td title="<?php echo $v['reason'];?>"><input type="text" size="20" value="<?php echo $v['reason'];?>"/></td>
<td data-hide-1200="1" title="<?php echo $v['note'];?>"><input type="text" size="20" value="<?php echo $v['note'];?>"/></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除一年之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<br/>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>