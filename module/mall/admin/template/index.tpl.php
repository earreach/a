<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>
&nbsp;<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<span data-hide-1200="1"><?php echo $level_select;?>&nbsp;</span>
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>
&nbsp;<select name="datetype">
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>更新时间</option>
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>发布时间</option>
<option value="sfromtime"<?php if($datetype == 'sfromtime') echo ' selected';?>>秒杀开始</option>
<option value="stotime"<?php if($datetype == 'stotime') echo ' selected';?>>秒杀结束</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo category_select('catid', '所属分类', $catid, $moduleid);?>&nbsp;
<?php echo ajax_area_select('areaid', '所在地区', $areaid);?>&nbsp;
</td>
</tr>
<tr>
<td>
&nbsp;<select name="mode">
<option value="0"<?php if($mode == 0) echo ' selected';?>>模式</option>
<option value="4"<?php if($mode == 4) echo ' selected';?>>单售价</option>
<option value="1"<?php if($mode == 1) echo ' selected';?>>多售价</option>
<option value="2"<?php if($mode == 2) echo ' selected';?>>阶梯价</option>
<option value="3"<?php if($mode == 3) echo ' selected';?>>属性价</option>
<option value="5"<?php if($mode == 5) echo ' selected';?>>粉丝价</option>
<option value="6"<?php if($mode == 6) echo ' selected';?>>秒杀价</option>
<option value="10"<?php if($mode == 10) echo ' selected';?>>到付</option>
<option value="11"<?php if($mode == 11) echo ' selected';?>>关联</option>
<option value="12"<?php if($mode == 12) echo ' selected';?>>橱窗</option>
</select>&nbsp;
<select name="mixt">
<option value="price"<?php if($mixt == 'price') echo ' selected';?>>单价</option>
<option value="fprice"<?php if($mixt == 'fprice') echo ' selected';?>>粉丝价</option>
<option value="sprice"<?php if($mixt == 'sprice') echo ' selected';?>>秒杀价</option>
<option value="orders"<?php if($mixt == 'orders') echo ' selected';?>>订单</option>
<option value="sales"<?php if($mixt == 'sales') echo ' selected';?>>销量</option>
<option value="amount"<?php if($mixt == 'amount') echo ' selected';?>>库存</option>
<option value="comments"<?php if($mixt == 'comments') echo ' selected';?>>评价</option>
<option value="vip"<?php if($mixt == 'vip') echo ' selected';?>><?php echo VIP;?></option>
</select>&nbsp;
<input type="text" size="10" name="minv" value="<?php echo $minv;?>"/>~<input type="text" size="10" name="maxv" value="<?php echo $maxv;?>"/>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" size="10" name="itemid" value="<?php echo $itemid;?>" placeholder="商品ID" title="商品ID"/>
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>分类</th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60">图片</th>
<th>商品</th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 25 ? 26 : 25;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 26 ? 'asc' : ($order == 25 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 27 ? 28 : 27;?>');">价格 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 28 ? 'asc' : ($order == 27 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 29 ? 30 : 29;?>');">订单 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 30 ? 'asc' : ($order == 29 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 31 ? 32 : 31;?>');">销量 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 32 ? 'asc' : ($order == 31 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 33 ? 34 : 33;?>');">库存 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 34 ? 'asc' : ($order == 33 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">浏览 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($order == 9 || $order == 10) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">点赞 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 11 || $order == 12) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">反对 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 13 || $order == 14) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">收藏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 15 || $order == 16) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">打赏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 17 || $order == 18) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">赏金 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 19 || $order == 20) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');">分享 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 21 || $order == 22) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 21 ? 22 : 21;?>');">举报 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 22 ? 'asc' : ($order == 21 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 23 ? 24 : 23;?>');">评论 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 24 ? 'asc' : ($order == 23 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">关联</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="<?php echo $v['caturl'];?>" target="_blank"><?php echo $v['catname'];?></a></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'] ? $v['thumb'] : DT_STATIC.'image/nopic60.png';?>" width="60" class="thumb"/></a></td>
<td>
<div class="lt">
<?php if($v['status'] == 3) {?>
<a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['title'];?></a>
<?php } else { ?>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>" class="t"><?php echo $v['title'];?></a>
<?php } ?>
<div>
更新:<span class="c_p" onclick="Dq('datetype','edittime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['edittime'], 6);?></span><br/>
添加:<span class="c_p" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['addtime'], 6);?></span>
<?php if($v['elite']) { ?> &nbsp; <span class="fb_red c_p" onclick="Dq('mode', 12);">橱窗</span><?php } ?>
<?php if($v['relate_id']) { ?> &nbsp; <span class="fb_green c_p" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=relate&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 关联商品');">关联</span><?php } ?>
<?php if($v['cod']) { ?> &nbsp; <span class="fb_gray c_p" onclick="Dq('mode', 10);">到付</span><?php } ?>
<?php if($v['prices']) { ?> &nbsp; <span class="fb_orange c_p" onclick="Dq('mode', 1);">多售价</span><?php } ?>
<?php if($v['step']) { ?> &nbsp; <span class="fb_orange c_p" onclick="Dq('mode', 2);">阶梯价</span><?php } ?>
<?php if($v['stock']) { ?> &nbsp; <span class="fb_orange c_p" onclick="Dq('mode', 3);">属性价</span><?php } ?>
<?php if($v['fprice'] > 0) { ?> &nbsp; <span class="fb_red c_p" style="color:#7049FF;background:#EBE6FE;" onclick="Dq('mode', 5);" title="粉丝价 <?php echo $DT['money_sign'];?><?php echo $v['fprice'];?>">粉丝价</span><?php } ?>
<?php if($v['sprice'] > 0) { ?> &nbsp; <span class="fb_red c_p" onclick="Dq('mode', 6);" title="秒杀价 <?php echo $DT['money_sign'];?><?php echo $v['sprice'];?>">秒杀</span><?php } ?>
</div>
</div>
</td>
<td><?php if($v['vip']) {?><a href="javascript:;" onclick="Dq('minvip','<?php echo $v['vip'];?>',0);Dq('maxvip','<?php echo $v['vip'];?>');"><img src="<?php echo DT_SKIN;?>vip_<?php echo $v['vip'];?>.gif" title="<?php echo VIP;?>:<?php echo $v['vip'];?>"/></a><?php } ?></td>
<td title="编辑:<?php echo $v['editor'];?>">
<?php if($v['username']) { ?>
	<a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a>
<?php } else { ?>
	<a href="javascript:;" onclick="_ip(this.innerHTML);" title="游客"><?php echo $v['ip'];?></a>
<?php } ?>
</td>
<td><span class="f_price"><?php echo $DT['money_sign'];?><?php echo $v['price'];?></span></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=order&id=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 订单列表');"><?php echo $v['orders'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><?php echo $v['sales'];?></td>
<td data-hide-1200="1" data-hide-1400="1"><?php echo $v['amount'];?></td>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&catid=<?php echo $v['catid'];?>&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 浏览记录');"><?php echo $v['hits'];?></a></td>
<?php if($order == 9 || $order == 10) { ?><td><a href="javascript:;" onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '点赞记录');"><?php echo $v['likes'];?></a></td><?php } ?>
<?php if($order == 11 || $order == 12) { ?><td><a href="javascript:;" onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '反对记录');"><?php echo $v['hates'];?></a></td><?php } ?>
<?php if($order == 13 || $order == 14) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=favorite&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 收藏记录');"><?php echo $v['favorites'];?></a></td><?php } ?>
<?php if($order == 15 || $order == 16) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 打赏记录');"><?php echo $v['awards'];?></a></td><?php } ?>
<?php if($order == 17 || $order == 18) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 打赏记录');"><?php echo $v['award'];?></a></td><?php } ?>
<?php if($order == 19 || $order == 20) { ?><td><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&itemid=<?php echo $v['itemid'];?>&kw=share.php', '[<?php echo $v['alt'];?>] 分享记录');"><?php echo $v['shares'];?></a></td><?php } ?>
<?php if($order == 21 || $order == 22) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&tid=<?php echo $v['itemid'];?>', '举报记录');"><?php echo $v['reports'];?></a></td><?php } ?>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=order&action=comment&mallid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 评论列表');"><?php echo $v['comments'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=relate&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 关联商品');"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="关联商品" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php } ?>
</table>
<?php include tpl('notice_chip');?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>

<?php if($action == 'check') { ?>

<input type="submit" value="通过审核" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check';"/>&nbsp;
<input type="submit" value="拒 绝" class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';"/>&nbsp;
<input type="submit" value="移动分类" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中商品吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'expire') { ?>

<input type="submit" value=" 上 架 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=onsale';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中商品吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'reject') { ?>

<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中商品吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;

<?php } else if($action == 'recycle') { ?>

<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中商品吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="还 原" class="btn" onclick="if(confirm('确定要还原选中商品吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value="清 空" class="btn-r" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>

<?php } else { ?>

<input type="submit" value="刷新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=refresh';" title="刷新时间为最新"/>&nbsp;
<input type="submit" value="更新信息" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;
<?php if($MOD['show_html']) { ?><input type="submit" value=" 生成网页 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=tohtml';"/>&nbsp; <?php } ?>
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中商品吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="移动分类" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<input type="submit" value="批量下架" class="btn-r" onclick="if(confirm('确定要批量下架选中商品吗？')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=unsale'}else{return false;}"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>&nbsp;
<?php } ?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<br/>
<script type="text/javascript">
$(function(){
	Menuon(<?php echo $menuid;?>);
	$('.thumb').on('error', function(e) {
		 $(this).attr('src', '<?php echo DT_STATIC;?>image/nopic60.png');
	});
});
</script>
<?php include tpl('footer');?>