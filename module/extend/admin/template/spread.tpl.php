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
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>添加时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>修改时间</option>
<option value="fromtime"<?php if($datetype == 'fromtime') echo ' selected';?>>开始时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>到期时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mid">
<option value="0">模块</option>
<?php 
foreach($MODULE as $v) {
	if(($v['moduleid'] > 0 && $v['moduleid'] < 4) || $v['islink']) continue;
	echo '<option value="'.$v['moduleid'].'"'.($mid == $v['moduleid'] ? ' selected' : '').'>'.$v['name'].'</option>';
} 
?>
</select>&nbsp;
<input type="text" size="10" name="itemid" value="<?php echo $itemid;?>" placeholder="信息ID" title="信息ID"/>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>模块</th>
<th>关键词</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">出价 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>单位</th>
<th>会员</th>
<th data-hide-1200="1">公司</th>
<th>信息ID</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">开始时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">到期时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 6 ? 5 : 6;?>');">剩余(天) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>状态</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">添加时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('mid','<?php echo $v['mid'];?>');"><?php echo $MODULE[$v['mid']]['name'];?></a></td>
<td>&nbsp;<a href="<?php echo $MODULE[$v['mid']]['linkurl'];?>search<?php echo DT_EXT;?>?kw=<?php echo urlencode($v['word']);?>" target="_blank"><?php echo $v['word'];?></td>
<td><?php echo $v['price'];?></td>
<td><?php echo $v['currency'] == 'money' ? $DT['money_unit'] : $DT['credit_unit'];?></td>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('username','<?php echo $v['username'];?>');"><?php echo $v['company'];?></a></td>
<td><?php if($itemid) {?><a href="<?php echo gourl('?mid='.$v['mid'].'&itemid='.$v['tid']);?>" target="_blank"><?php } else {?><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['tid'];?>');"><?php } ?><?php echo $v['tid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','fromtime',0);Dq('date',this.innerHTML);"><?php echo $v['fromdate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo $v['todate'];?></a></td>
<td<?php if($v['days']<5) echo ' class="f_red"';?> data-hide-1200="1"><?php echo $v['days'];?></td>
<td><?php echo $v['process'];?></td>
<td title="编辑:<?php echo $v['editor'];?>&#10;更新时间:<?php echo $v['editdate'];?>"><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<?php if($action == 'check') { ?>
<input type="submit" value="通过审核" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=3';"/>&nbsp;
<?php } else { ?>
<input type="submit" value="取消审核 " class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=2';"/>&nbsp;
<?php } ?>
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中排名吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
&nbsp;&nbsp;
<span class="f_gray">提示：系统会定期自动生成排名，如果需要立即看到效果，请点生成排名</span>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>