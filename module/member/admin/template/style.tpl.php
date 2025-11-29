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
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo $type_select;?>&nbsp;
<select name="currency">
<option value="">收费类型</option>
<option value="money"<?php echo $currency == 'money' ? ' selected' : '';?>>收费模板</option>
<option value="credit"<?php echo $currency == 'credit' ? ' selected' : '';?>><?php echo $DT['credit_name'];?>兑换</option>
<option value="free"<?php echo $currency == 'free' ? ' selected' : '';?>>免费模板</option>
</select>&nbsp;
<select name="groupid">
<option value="0">会员组</option>
<?php foreach($GROUP as $v) { if($v['groupid'] < 5) continue; ?>
<option value="<?php echo $v['groupid'];?>"<?php echo $v['groupid'] == $groupid ? ' selected' : '';?>><?php echo $v['groupname'];?></option>
<?php } ?>
</select>&nbsp;
<select name="mtype">
<option value="fee"<?php echo $mtype == 'fee' ? ' selected' : '';?>>模板价格</option>
<option value="money"<?php echo $mtype == 'money' ? ' selected' : '';?>><?php echo $DT['money_name'];?>收益</option>
<option value="credit"<?php echo $mtype == 'credit' ? ' selected' : '';?>><?php echo $DT['credit_name'];?>收益</option>
<option value="hits"<?php echo $mtype == 'hits' ? ' selected' : '';?>>使用人数</option>
<option value="orders"<?php echo $mtype == 'orders' ? ' selected' : '';?>>订单数量</option>
</select>&nbsp;
<input type="text" size="10" name="minfee" value="<?php echo $minfee;?>"/> 至
<input type="text" size="10" name="maxfee" value="<?php echo $maxfee;?>"/>&nbsp;

</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="50">排序</th>
<th width="200">预览图</th>
<th>模板名称</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">价格 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');"><?php echo $DT['money_name'];?>收益 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><?php echo $DT['credit_name'];?>收益 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">人气 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">订单 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><input name="listorder[<?php echo $v['itemid'];?>]" type="text" size="2" value="<?php echo $v['listorder'];?>"/></td>
<td title="点击预览"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $v['itemid'];?>" target="_blank"><img src="<?php echo $v['thumb'];?>"/></a></td>
<td>
<div class="lt">
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $v['itemid'];?>" target="_blank" class="t"><?php echo $v['title'];?></a>
<div>
分类：<span class="c_p" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo isset($TYPE[$v['typeid']]) ? $TYPE[$v['typeid']]['typename'] : '未分类';?></span><br/>
目录：<span class="c_p" onclick="Dq('fields',2,0);Dq('kw','='+this.innerHTML);" title="风格目录"><?php echo $v['skin'];?></span> & <span class="c_p" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);" title="模板目录"><?php echo $v['template'];?></span><br/>
权限：<?php echo $v['groups'];?><br/>
作者：<span class="c_p" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['author'];?></span><br/>
</div>
</div>
</td>
<td><?php echo $v['fee'] ? ($v['currency'] == 'money' ? '<span class="f_red">'.$v['fee'].$DT['money_unit'].'/月</span>' : '<span class="f_blue">'.$v['fee'].$DT['credit_unit'].'/月</span>') : '<span class="f_green">免费</span>';?></td>
<td><?php echo $v['money'].$DT['money_unit'];?></td>
<td><?php echo $v['credit'].$DT['credit_unit'];?></td>
<td><?php echo $v['hits'];?></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=order&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['title'];?>] 订单管理');"><?php echo $v['orders'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更新排序" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="批量删除" class="btn-r" onclick="if(confirm('确定要删除选中模板吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>