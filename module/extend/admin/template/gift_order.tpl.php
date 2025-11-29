<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$itemid) show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="datetype">
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>更新时间</option>
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>下单时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&itemid=<?php echo $itemid;?>');"/>
</form>
</div>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="130">下单时间</th>
<th>礼品</th>
<th><?php echo $DT['credit_name'];?></th>
<th>会员名</th>
<th width="200" data-hide-1200="1">订单状态</th>
<th width="260" data-hide-1200="1">快递</th>
<th width="130">更新时间</th>
<th width="40">操作</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" title="<?php echo $v['note'];?>">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['oid'];?>"/><input name="post[<?php echo $v['oid'];?>][itemid]" type="hidden" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>" target="_blank" title="<?php echo $v['title'];?>"><?php echo $v['title'];?></a></td>
<td><?php echo $v['credit'];?></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td data-hide-1200="1"><input name="post[<?php echo $v['oid'];?>][status]" type="text" size="10" value="<?php echo $v['status'];?>" id="status_<?php echo $v['oid'];?>"/>
<select onchange="if(this.value)Dd('status_<?php echo $v['oid'];?>').value=this.value;">
<option value="">备选状态</option>
<option value="处理中">处理中</option>
<option value="审核中">审核中</option>
<option value="已取消">已取消</option>
<option value="已发货">已发货</option>
<option value="已完成">已完成</option>
</select>
</td>
<td data-hide-1200="1"><?php echo dselect($send_types, 'post['.$v['oid'].'][express]', '快递类型', $v['express'], '', 0, '', 1);?> <input name="post[<?php echo $v['oid'];?>][expressid]" type="text" size="20" value="<?php echo $v['expressid'];?>" placeholder="快递单号：" title="快递单号："/>
</td>
<td><a href="javascript:;" onclick="Dq('datetype','edittime',0);Dq('date',this.innerHTML);"><?php echo $v['editdate'];?></a></td>
<td>
<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=order_show&itemid=<?php echo $v['oid'];?>', '订单受理');"><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="受理" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更 新" class="btn-g" onclick="this.form.action='?job=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>