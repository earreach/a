<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<div class="tt">商品信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">订单编号</td>
<td><?php echo $O['itemid'];?></td>
</tr>
<tr>
<td class="tl">商品名称</td>
<td><a href="<?php echo $O['linkurl'];?>" target="_blank" class="t"><?php echo $O['title'];?></a></td>
</tr>
<tr>
<td class="tl">商品图片</td>
<td><a href="<?php echo $O['linkurl'];?>" target="_blank"><img src="<?php if($O['thumb']) { ?><?php echo $O['thumb'];?><?php } else { ?><?php echo DT_STATIC;?>image/nopic60.png<?php } ?>
" width="60" height="60"/></a></td>
</tr>
<tr>
<td class="tl">卖家</td>
<td><span style="display:inline-block;width:200px;"><?php if($DT['im_web']) { ?><?php echo im_web($O['seller']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['seller'];?>');" class="t"><?php echo $O['seller'];?></a></span> &nbsp; <a href="<?php echo gourl(userurl($O['seller']));?>" target="_blank" class="t" title="打开店铺"><?php echo $O['shop'];?></a></td>
</tr>
<tr>
<td class="tl">买家 </td>
<td><span style="display:inline-block;width:200px;"><?php if($DT['im_web']) { ?><?php echo im_web($O['buyer']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['buyer'];?>');" class="t"><?php echo $O['buyer'];?></a></span> &nbsp; <a href="<?php echo gourl(userurl($O['buyer'], 'file=space'));?>" target="_blank" class="t" title="打开空间"><?php echo $O['buyer_passport'];?></a></td>
</tr>
</table>
<?php if($O['logistic']) { ?>
<div class="tt">快递信息</div>
<table cellspacing="0" class="tb">
<?php if($DT['postcode']) { ?>
<tr>
<td class="tl">邮编</td>
<td><?php echo $O['buyer_postcode'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">地址</td>
<td><?php echo $O['buyer_address'];?></td>
</tr>
<tr>
<td class="tl">姓名</td>
<td><?php echo $O['buyer_name'];?></td>
</tr>
<tr>
<td class="tl">手机</td>
<td><?php echo $O['buyer_mobile'];?></td>
</tr>
<?php if($O['send_time'] > 0) { ?>
<tr>
<td class="tl">快递类型</td>
<td><a href="<?php echo DT_PATH;?>api/express/home<?php echo DT_EXT;?>?e=<?php echo urlencode($O['send_type']);?>&n=<?php echo $O['send_no'];?>" target="_blank"><?php echo $O['send_type'];?></a></td>
</tr>
<tr>
<td class="tl">快递单号</td>
<td><a href="<?php echo DT_PATH;?>api/express<?php echo DT_EXT;?>?e=<?php echo urlencode($O['send_type']);?>&n=<?php echo $O['send_no'];?>" target="_blank"><?php echo $O['send_no'];?></a></td>
</tr>
<?php if($O['send_type'] && $O['send_no']) { ?>
<tr>
<td class="tl">追踪结果</td>
<td style="line-height:200%;"><div id="express"><img src="<?php echo DT_SKIN;?>loading.gif" align="absmiddle"/> 正在查询...</div>
<script type="text/javascript">
$(function(){
	$('#express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo encrypt('group|'.$O['send_type'].'|'.$O['send_no'].'|'.$O['send_status'].'|'.$O['buyer_mobile'].'|'.$O['itemid'], DT_KEY.'EXPRESS');?>');
});
</script>
</td>
</tr>
<?php } ?>
<?php } ?>
</table>
<?php } else { ?>
<div class="tt">验证信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">手机</td>
<td><?php echo $O['buyer_mobile'];?></td>
</tr>
<tr>
<td class="tl">密码</td>
<td><?php echo $O['password'] ? $O['password'] : '未分配';?></td>
</tr>
</table>
<?php } ?>
<div class="tt">价格信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">商品单价</td>
<td><?php echo $DT['money_sign'];?><?php echo $O['price'];?></td>
</tr>
<tr>
<td class="tl">购买数量</td>
<td><?php echo $O['number'];?></td>
</tr>
<tr>
<td class="tl">订单总额</td>
<td class="tr f_red"><?php echo $DT['money_sign'];?><?php echo $O['money'];?></td>
</tr>
</table>
<div class="tt">订单详情</div>
<table cellspacing="0" class="tb">
<?php if($logs) { ?>
<tr>
<td class="tl">订单进程</td>
<td>
<div style="line-height:24px;">
<?php if(is_array($logs)) { foreach($logs as $v) { ?>
<?php echo $v['adddate'];?> - <?php echo $v['title'];?><br/>
<?php } } ?>
</div>
</td>
</tr>
<?php } else { ?>
<tr>
<td class="tl">下单时间</td>
<td><?php echo $O['adddate'];?></td>
</tr>
<tr>
<td class="tl">最后更新</td>
<td><?php echo $O['updatedate'];?></td>
</tr>
<?php if($O['send_time']) { ?>
<tr>
<td class="tl">发货时间</td>
<td><?php echo $O['send_time'];?></td>
</tr>
<?php } ?>
<?php } ?>
<tr>
<td class="tl">买家备注</td>
<td><?php if($O['note']) { ?><?php echo $O['note'];?><?php } else { ?>无<?php } ?>
</td>
</tr>
<tr>
<td class="tl">交易状态</td>
<td><?php echo $dstatus[$O['status']];?></td>
</tr>
<?php if($O['buyer_reason']) { ?>
<tr>
<td class="tl">退款原因</td>
<td><?php echo $O['buyer_reason'];?></td>
</tr>
<?php } ?>
<?php if($O['refund_reason']) { ?>
<tr>
<td class="tl">操作原因</td>
<td><?php echo $O['refund_reason'];?></td>
</tr>
<tr>
<td class="tl">操作人</td>
<td><?php echo $O['editor'];?></td>
</tr>
<tr>
<td class="tl">操作时间</td>
<td><?php echo $O['updatetime'];?></td>
</tr>
<?php } ?>
</table>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>