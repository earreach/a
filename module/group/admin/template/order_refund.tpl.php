<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="mallid" value="<?php echo $mallid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt">商品信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">订单单号</td>
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
<?php if($O['seller'] == $_username) { ?>
<tr>
<td class="tl">买家 </td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($O['buyer']);?>&nbsp;<?php } ?>
<a href="message<?php echo DT_EXT;?>?action=send&touser=<?php echo $O['buyer'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>member/ico_message.gif" title="发送站内信" align="absmiddle"/></a> <a href="<?php echo userurl($O['buyer'], 'file=contact');?>" target="_blank" class="t"><?php echo $O['buyer'];?></a></td>
</tr>
<?php } else if($O['buyer'] == $_username) { ?>
<tr>
<td class="tl">卖家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($O['seller']);?>&nbsp;<?php } ?>
<a href="message<?php echo DT_EXT;?>?action=send&touser=<?php echo $O['seller'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>member/ico_message.gif" title="发送站内信" align="absmiddle"/></a> <a href="<?php echo userurl($O['seller'], 'file=contact');?>" target="_blank" class="t"><?php echo $O['seller'];?></a></td>
</tr>
<?php } ?>
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
<tr>
<td class="tl">电话</td>
<td><?php echo $O['buyer_phone'];?></td>
</tr>
<tr>
<td class="tl">买家备注</td>
<td><?php if($O['note']) { ?><?php echo $O['note'];?><?php } else { ?>无<?php } ?>
</td>
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
<td class="tl">密码</td>
<td><?php echo $O['password'];?></td>
</tr>
<tr>
<td class="tl">手机</td>
<td><?php echo $O['buyer_mobile'];?></td>
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
<td class="tl">交易状态</td>
<td><?php echo $_status[$O['status']];?></td>
</tr>
<?php if($O['buyer_reason']) { ?>
<tr>
<td class="tl">退款原因</td>
<td><?php echo $O['buyer_reason'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">受理结果</td>
<td id="status">
<label><input type="radio" name="status" value="5"/> 将交易金额退还给买家</label><br/>
<label><input type="radio" name="status" value="3"/> 将交易金额支付给卖家</label> <span id="dstatus" class="f_red"></span>
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
</table>
<div class="sbt"><input type="submit" name="submit" value="确 定" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="history.back(-1);"/></div>
</form>
<script type="text/javascript">
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
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>