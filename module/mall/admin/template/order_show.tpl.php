<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<?php include template('goods', 'chip');?>
<?php if($job != 'express') { ?>
<table cellspacing="0" class="tb">
<?php
	if($O['admin_note']) {
		echo '<tr><th>时间</th><th>备注内容</th><th width="150">管理员</th></tr>';
		$N = explode('--------------------', $O['admin_note']);
		foreach($N as $n) {
			if(strpos($n, '|') === false) continue;
			list($_time, $_name, $_note) = explode('|', $n);
			if(strlen(trim($_time)) == 16 && check_name($_name) && $_note) echo '<tr><td align="center">'.trim($_time).'</td><td style="padding:6px 10px;line-height:24px;">'.nl2br(trim($_note)).'</td><td align="center"><a href="javascript:;" onclick="_user(\''.$_name.'\')">'.$_name.'</a></td></tr>';
		}
	}
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="note_add"/>
<input type="hidden" name="id" value="<?php echo $id;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<tr>
<td class="tl">追加备注</td>
<td align="center">
<textarea name="note" style="width:99%;height:24px;overflow:visible;padding:0;"></textarea></td>
<td align="center" width="174"><input type="submit" name="submit" value="追加" class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:$('#edit_note').toggle();" class="t">修改</a></td>
</tr>
</form>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="note_edit"/>
<input type="hidden" name="id" value="<?php echo $id;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<tr id="edit_note" style="display:none;">
<td class="tl">修改备注</td>
<td align="center" class="f_gray">
<textarea name="note" style="width:99%;height:100px;overflow:visible;padding:0;"><?php echo $O['admin_note'];?></textarea><br/>请只修改备注文字，不要改动 | 和 - 符号以及时间和管理员</td>
<td align="center"><input type="submit" name="submit" value="修改" class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=note_edit&itemid=<?php echo $itemid;?>&id=<?php echo $id;?>&note=" class="t" onclick="return confirm('确定要清空此会员的备注信息吗？此操作将不可撤销');">清空</a></td>
</tr>
</form>
</table>
<?php } ?>
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
<td><a href="<?php echo DT_PATH;?>api/express<?php echo DT_EXT;?>?e=<?php echo urlencode($O['send_type']);?>&n=<?php echo $O['send_no'];?>" target="_blank"><?php echo $O['send_no'];?></a>
<?php if($O['send_type'] && $O['send_no']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#copy-no" onclick="Dtoast('快递单号已复制');"/><?php if($job != 'express') { ?> &nbsp; <a href="javascript:;" class="t" onclick="Ds('express_t');$('#express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo $auth;?>');">[快递追踪]</a><?php } ?><?php } ?></td>
</tr>
<tr id="express_t" style="display:none;">
<td class="tl">追踪结果</td>
<td style="line-height:200%;"><div id="express"><img src="<?php echo DT_SKIN;?>loading.gif" align="absmiddle"/> 正在查询...</div>
</td>
</tr>
<?php } ?>
</table>
<?php if($job != 'express') { ?>
<div class="tt">订单详情</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">卖家</td>
<td><span style="display:inline-block;width:200px;"><?php if($DT['im_web']) { ?><?php echo im_web($O['seller']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['seller'];?>');" class="t"><?php echo $O['seller'];?></a></span> &nbsp; <a href="<?php echo gourl(userurl($O['seller']));?>" target="_blank" class="t" title="打开店铺"><?php echo $O['shop'];?></a></td>
</tr>
<tr>
<td class="tl">买家</td>
<td><span style="display:inline-block;width:200px;"><?php if($DT['im_web']) { ?><?php echo im_web($O['buyer']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['buyer'];?>');" class="t"><?php echo $O['buyer'];?></a></span> &nbsp; <a href="<?php echo gourl(userurl($O['buyer'], 'file=space'));?>" target="_blank" class="t" title="打开空间"><?php echo $O['buyer_passport'];?></a></td>
</tr>
<?php if($logs) { ?>
<tr>
<td class="tl">订单进程</td>
<td>
<div style="line-height:24px;">
<?php if(is_array($logs)) { foreach($logs as $v) { ?>
<?php echo $v['adddate'];?> - <?php echo $v['title'];?> &nbsp; <?php echo $v['note'];?><br/>
<?php } } ?>
</div>
</td>
</tr>
<?php } ?>
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
<tr>
<td class="tl">交易状态</td>
<td><?php echo $dstatus[$O['status']];?></td>
</tr>
<?php if($O['bill']) { ?>
<tr>
<td class="tl">付款凭证</td>
<td><a href="javascript:;" onclick="_preview('<?php echo $O['bill'];?>');" class="t">查看凭证</a></td>
</tr>
<?php } ?>
<?php if($O['contract']) { ?>
<tr>
<td class="tl">合同状态</td>
<td>
	<?php if($O['contract']) { ?>
	<span class="f_green">已签署</span>
	<?php } else {?>
	<span class="f_blue">签署中</span>
	<?php }?>
	&nbsp;&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=contract_show&oid=<?php echo $itemid;?>', '合同详情');" class="t">合同详情</a>
</td>
</tr>
<?php } ?>
<?php if($O['invoice']) { ?>
<tr>
<td class="tl">发票状态</td>
<td>
	<?php if($O['invoice'] == 2) { ?>
	<span class="f_green">已开票</span>
	<?php } else if($O['invoice'] == 1) { ?>
	<span class="f_blue">已申请</span>
	<?php } else {?>
	<span class="f_gray">待申请</span>
	<?php }?>
	&nbsp;&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=invoice_show&oid=<?php echo $itemid;?>', '发票详情');" class="t">发票详情</a>
</td>
</tr>
<?php } ?>
<?php if($O['seller_note']) { ?>
<tr>
<td class="tl">商家备注</td>
<td><?php echo nl2br($O['seller_note']);?></td>
</tr>
<?php } ?>
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
<tr>
<td class="tl">售后服务</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=service&itemid=<?php echo $itemid;?>', '服务记录');" class="t">服务记录</a></td>
</tr>
</table>
<?php if($comments) { ?>
<?php load('player.js');?>
<div class="tt">买家评价<a name="comment1"></a></div>
<table cellspacing="0" class="tb">
<?php foreach($lists as $k => $v) { ?>
<tr>
<td class="tl">商品名称</td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['title'];?></a></td>
</tr>
<?php if($comments[$k]['seller_star']) { ?>
<tr>
<td class="tl">商品打分</td>
<td><img src="<?php echo DT_STATIC;?>image/star<?php echo $comments[$k]['seller_star'];?>.gif" alt="" align="absmiddle"/> <?php echo $STARS[$comments[$k]['seller_star']];?></td>
</tr>
<tr>
<td class="tl">物流服务</td>
<td><img src="<?php echo DT_STATIC;?>image/star<?php echo $comments[$k]['seller_star_express'];?>.gif" alt="" align="absmiddle"/> <?php echo $STARS[$comments[$k]['seller_star_express']];?></td>
</tr>
<tr>
<td class="tl">商家态度</td>
<td><img src="<?php echo DT_STATIC;?>image/star<?php echo $comments[$k]['seller_star_service'];?>.gif" alt="" align="absmiddle"/> <?php echo $STARS[$comments[$k]['seller_star_service']];?></td>
</tr>
<tr>
<td class="tl">买家评论</td>
<td><?php echo nl2br($comments[$k]['seller_comment']);?></td>
</tr>
<?php if($comments[$k]['seller_thumbs']) { ?>
<tr>
<td class="tl">买家图片</td>
<td>
<?php if(is_array($comments[$k]['seller_thumbs'])) { foreach($comments[$k]['seller_thumbs'] as $v) { ?>
<div class="thumbs"><img src="<?php echo $v;?>" onclick="_preview(this.src, 1);"/></div>
<?php } } ?>
</td>
</tr>
<?php } ?>
<?php if($comments[$k]['seller_video']) { ?>
<tr>
<td class="tl">买家视频</td>
<td><script type="text/javascript">document.write(player('<?php echo $comments[$k]['seller_video'];?>',400,300,0));</script></td>
</tr>
<?php } ?>
<tr>
<td class="tl">评论时间</td>
<td><?php echo timetodate($comments[$k]['seller_ctime'], 6);?></td>
</tr>
<?php if($comments[$k]['buyer_reply']) { ?>
<tr>
<td class="tl">卖家解释</td>
<td style="color:#D9251D;"><?php echo nl2br($comments[$k]['buyer_reply']);?></td>
</tr>
<tr>
<td class="tl">解释时间</td>
<td><?php echo timetodate($comments[$k]['buyer_rtime'], 6);?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td class="tl">买家评论</td>
<td>暂未评论</td>
</tr>
<?php } ?>
<?php } ?>
</table>

<div class="tt">卖家评价<a name="comment2"></a></div>
<table cellspacing="0" class="tb">
<?php foreach($lists as $k => $v) { ?>
<tr>
<td class="tl">商品名称</td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['title'];?></a></td>
</tr>
<?php if($comments[$k]['buyer_star']) { ?>
<tr>
<td class="tl">卖家评分</td>
<td>
<img src="<?php echo DT_STATIC;?>image/star<?php echo $comments[$k]['buyer_star'];?>.gif" alt="" align="absmiddle"/> <?php echo $STARS[$comments[$k]['buyer_star']];?>
</td>
</tr>
<tr>
<td class="tl">卖家评论</td>
<td><?php echo nl2br($comments[$k]['buyer_comment']);?></td>
</tr>
<tr>
<td class="tl">评论时间</td>
<td><?php echo timetodate($comments[$k]['buyer_ctime'], 6);?></td>
</tr>
<?php if($comments[$k]['seller_reply']) { ?>
<tr>
<td class="tl">买家解释</td>
<td style="color:#D9251D;"><?php echo nl2br($comments[$k]['seller_reply']);?></td>
</tr>
<tr>
<td class="tl">解释时间</td>
<td><?php echo timetodate($comments[$k]['seller_rtime'], 6);?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
<td class="tl">卖家评论</td>
<td>暂未评论</td>
</tr>
<?php } ?>
<?php } ?>
</table>
<?php } ?>
<?php } ?>


<?php if($O['status'] == 5 && $O['buyer_reason']) { ?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="refund"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="mallid" value="<?php echo $mallid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt">受理退款</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">受理结果</td>
<td id="status">
<label><input type="radio" name="status" value="6"/> 将交易金额退还给买家</label><br/>
<label><input type="radio" name="status" value="4"/> 将交易金额支付给卖家</label> <span id="dstatus" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl">操作理由</td>
<td>
<textarea name="content" id="content" class="dsn"></textarea>
<?php echo deditor($moduleid, 'content', 'Simple', '100%', 200);?>
<br/>请在和买卖双方沟通后谨慎填写，一经提交将不可更改 <span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl">通知双方</td>
<td>
<input type="checkbox" name="msg" id="msg" value="1" onclick="Dn();" checked/><label for="msg"> 站内通知</label>
<input type="checkbox" name="eml" id="eml" value="1" onclick="Dn();"/><label for="eml"> 邮件通知</label>
<input type="checkbox" name="sms" id="sms" value="1" onclick="Dn();"/><label for="sms"> 短信通知</label>
<input type="checkbox" name="wec" id="wec" value="1" onclick="Dn();"/><label for="wec"> 微信通知</label>
</td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" name="submit" value=" 确 定 " class="btn"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value=" 返 回 " class="btn" onclick="history.back(-1);"/></td>
</tr>
</table>
</form>
<?php } ?>
<div style="z-index:1000;position:absolute;top:-10000px;"><textarea id="copy-no"><?php echo $O['send_no'];?></textarea></div>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
<?php if($job == 'express') { ?>
$(function(){
	Ds('express_t');
	$('#express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo $auth;?>');
});
<?php } ?>
function check() {
	var l;
	l = checked_count('status');
	if(l == 0) {
		Dmsg('请选择受理结果', 'status');
		return false;
	}
	l = EditorLen();
	if(l < 5) {
		Dmsg('操作理由不能少于5个字，当前已输入'+l+'个字', 'content');
		return false;
	}
	return confirm('确定要进行此操作吗？提交后将不可恢复');
}
Menuon(0);
</script>
<?php include tpl('footer');?>