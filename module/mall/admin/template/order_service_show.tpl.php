<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<?php echo load('mall.css');?>
<?php echo load('mall.js');?>
<?php echo load('player.js');?>
<?php include template('goods_service', 'chip');?>
<div class="tt">服务详情</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">所属订单</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $O['pid'] ? $O['pid'] : $O['oid'];?>', '订单详情');" class="t">订单详情</a></td>
</tr>
<tr>
<td class="tl">卖家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($O['seller']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['seller'];?>');" class="t"><?php echo $O['seller'];?></a></td>
</tr>
<tr>
<td class="tl">买家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($O['buyer']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['buyer'];?>');" class="t"><?php echo $O['buyer'];?></a></td>
</tr>
</tr>
<tr>
<td class="tl">申请时间</td>
<td><?php echo $O['adddate'];?></td>
</tr>
<tr>
<td class="tl">最后更新</td>
<td><?php echo $O['updatedate'];?></td>
</tr>
<tr>
<td class="tl">服务类型</td>
<td><?php echo $dservice[$O['typeid']];?></td>
</tr>
<tr>
<td class="tl">申请原因</td>
<td><?php echo $O['buyer_title'];?></td>
</tr>
<?php if($O['buyer_reason']) { ?>
<tr>
<td class="tl">补充说明</td>
<td><?php echo nl2br($O['buyer_reason']);?></td>
</tr>
<?php } ?>
<?php if($O['buyer_thumbs'] || $O['buyer_video']) { ?>
<tr>
<td class="tl">申请凭据</td>
<td>
<div class="comment-r">
<ul id="thumbs-<?php echo $itemid;?>0">
<?php if($O['buyer_video']) { ?><li><img src="<?php echo DT_STATIC;?>image/play.gif" onclick="comment_thumb_show(<?php echo $itemid;?>0, this);" data-video="<?php echo $O['buyer_video'];?>"/></li><?php } ?>
<?php if(is_array($O['buyer_thumbs'])) { foreach($O['buyer_thumbs'] as $t) { ?>
<?php if($t) { ?><li><img src="<?php echo $t;?>" onclick="comment_thumb_show(<?php echo $itemid;?>0, this);"/></li><?php } ?>
<?php } } ?>
</ul>
<p id="thumbshow-<?php echo $itemid;?>0" onclick="comment_thumb_next(<?php echo $v['itemid'];?>0);"></p>
</div>
</td>
</tr>
<?php } ?>
<tr>
<td class="tl">服务状态</td>
<td><?php echo $dservice_status[$O['status']];?></td>
</tr>
<?php if($O['seller_title']) { ?>
<tr>
<td class="tl">操作原因</td>
<td><?php echo $O['seller_title'];?></td>
</tr>
<?php } ?>
<?php if($O['seller_reason']) { ?>
<tr>
<td class="tl">补充说明</td>
<td><?php echo nl2br($O['seller_reason']);?></td>
</tr>
<?php } ?>
<?php if($O['seller_thumbs'] || $O['seller_video']) { ?>
<tr>
<td class="tl">操作凭据</td>
<td>
<div class="comment-r">
<ul id="thumbs-<?php echo $itemid;?>1">
<?php if($O['seller_video']) { ?><li><img src="<?php echo DT_STATIC;?>image/play.gif" onclick="comment_thumb_show(<?php echo $itemid;?>1, this);" data-video="<?php echo $O['seller_video'];?>"/></li><?php } ?>
<?php if(is_array($O['seller_thumbs'])) { foreach($O['seller_thumbs'] as $t) { ?>
<?php if($t) { ?><li><img src="<?php echo $t;?>" onclick="comment_thumb_show(<?php echo $itemid;?>1, this);"/></li><?php } ?>
<?php } } ?>
</ul>
<p id="thumbshow-<?php echo $itemid;?>1" onclick="comment_thumb_next(<?php echo $itemid;?>1);"></p>
</div>
</td>
</tr>
<?php } ?>
<?php if($O['seller_note']) { ?>
<tr>
<td class="tl">备注事项</td>
<td><?php echo nl2br($O['seller_note']);?></td>
</tr>
<?php } ?>
</table>

<?php if($O['seller_address']) { ?>
<div class="tt">寄回地址</div>
<table cellspacing="0" class="tb">
<?php if($DT['postcode']) { ?>
<tr>
<td class="tl">邮政编码</td>
<td><?php echo $O['seller_postcode'];?><?php if($O['seller_postcode']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#seller_postcode" onclick="Dtoast('邮编已复制');"/><?php } ?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">收件地址</td>
<td><?php echo $O['seller_address'];?><?php if($O['seller_address']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#seller_address" onclick="Dtoast('地址已复制');"/><?php } ?></td>
</tr>
<tr>
<td class="tl">收件姓名</td>
<td><?php echo $O['seller_name'];?><?php if($O['seller_name']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#seller_name" onclick="Dtoast('姓名已复制');"/><?php } ?></td>
</tr>
<tr>
<td class="tl">收件手机</td>
<td><?php echo $O['seller_mobile'];?><?php if($O['seller_mobile']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#seller_mobile" onclick="Dtoast('手机已复制');"/><?php } ?></td>
</tr>
<?php if($O['seller_send_time']) { ?>
<tr>
<td class="tl">发货日期</td>
<td><?php echo timetodate($O['seller_send_time'], 3);?></td>
</tr>
<tr>
<td class="tl">快递类型</td>
<td><?php echo $O['seller_send_type'];?></td>
</tr>
<tr>
<td class="tl">快递单号</td>
<td><?php echo $O['seller_send_no'];?><?php if($O['seller_send_type'] && $O['seller_send_no']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#seller_send_no" onclick="Dtoast('快递单号已复制');"/> &nbsp; <a href="javascript:;" class="t" onclick="Ds('express_seller');$('#seller_express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo $seller_auth;?>');">[快递追踪]</a><?php } ?></td>
</tr>
<tr id="express_seller" style="display:none;">
<td class="tl">追踪结果</td>
<td style="line-height:200%;"><div id="seller_express"><img src="<?php echo DT_STATIC;?>member/loading.gif" align="absmiddle"/> 正在查询...</div></td>
</tr>
<tr>
<td class="tl">快递状态</td>
<td><?php echo $dsend_status[$O['seller_send_status']];?></td>
</tr>
<?php } ?>
</table>
<?php } ?>


<?php if($O['buyer_address']) { ?>
<div class="tt">收货地址</div>
<table cellspacing="0" class="tb">
<?php if($DT['postcode']) { ?>
<tr>
<td class="tl">邮政编码</td>
<td><?php echo $O['buyer_postcode'];?><?php if($O['buyer_postcode']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#buyer_postcode" onclick="Dtoast('邮编已复制');"/><?php } ?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">收件地址</td>
<td><?php echo $O['buyer_address'];?><?php if($O['buyer_address']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#buyer_address" onclick="Dtoast('地址已复制');"/><?php } ?></td>
</tr>
<tr>
<td class="tl">收件姓名</td>
<td><?php echo $O['buyer_name'];?><?php if($O['buyer_name']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#buyer_name" onclick="Dtoast('姓名已复制');"/><?php } ?></td>
</tr>
<tr>
<td class="tl">收件手机</td>
<td><?php echo $O['buyer_mobile'];?><?php if($O['buyer_mobile']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#buyer_mobile" onclick="Dtoast('手机已复制');"/><?php } ?></td>
</tr>
<?php if($O['buyer_send_time']) { ?>
<tr>
<td class="tl">发货日期</td>
<td><?php echo timetodate($O['buyer_send_time'], 3);?></td>
</tr>
<tr>
<td class="tl">快递类型</td>
<td><?php echo $O['buyer_send_type'];?></td>
</tr>
<tr>
<td class="tl">快递单号</td>
<td><?php echo $O['buyer_send_no'];?><?php if($O['buyer_send_type'] && $O['buyer_send_no']) { ?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#buyer_send_no" onclick="Dtoast('快递单号已复制');"/> &nbsp; <a href="javascript:;" class="t" onclick="Ds('express_buyer');$('#buyer_express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo $buyer_auth;?>');">[快递追踪]</a><?php } ?></td>
</tr>
<tr id="express_buyer" style="display:none;">
<td class="tl">追踪结果</td>
<td style="line-height:200%;"><div id="buyer_express"><img src="<?php echo DT_STATIC;?>member/loading.gif" align="absmiddle"/> 正在查询...</div></td>
</tr>
<tr>
<td class="tl">快递状态</td>
<td><?php echo $dsend_status[$O['buyer_send_status']];?></td>
</tr>
<?php } ?>
</table>
<?php } ?>


<?php if(($O['status'] == 0 || $O['status'] == 2) && $O['typeid'] < 2) { ?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="mallid" value="<?php echo $mallid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<div class="tt">受理退款</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">退款原因</td>
<td>
<select name="reason" id="reason">
<option value="网站介入">网站介入</option>
<option value="买家理由合理">买家理由合理</option>
<option value="卖家长时间未处理">卖家长时间未处理</option>
<option value="卖家无法联系">卖家无法联系</option>
<option value="卖家拒绝退款">卖家拒绝退款</option>
<option value="其他">其他</option>
</select>
</td>
</tr>
<tr>
<td class="tl">补充说明</td>
<td>
<textarea rows="8" cols="80"  name="content" id="content"></textarea>
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
<td><input type="submit" name="submit" value=" 立即退款 " class="btn-g"/></td>
</tr>
</table>
</form>
<?php } ?>

<div style="z-index:1000;position:absolute;top:-10000px;">
<textarea id="seller_postcode"><?php echo $O['seller_postcode'];?></textarea>
<textarea id="seller_address"><?php echo $O['seller_address'];?></textarea>
<textarea id="seller_name"><?php echo $O['seller_name'];?></textarea>
<textarea id="seller_mobile"><?php echo $O['seller_mobile'];?></textarea>
<textarea id="seller_send_no"><?php echo $O['seller_send_no'];?></textarea>
<textarea id="buyer_postcode"><?php echo $O['buyer_postcode'];?></textarea>
<textarea id="buyer_address"><?php echo $O['buyer_address'];?></textarea>
<textarea id="buyer_name"><?php echo $O['buyer_name'];?></textarea>
<textarea id="buyer_mobile"><?php echo $O['buyer_mobile'];?></textarea>
<textarea id="buyer_send_no"><?php echo $O['buyer_send_no'];?></textarea>
</div>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
function check() {
	return confirm('确定要退款给买家吗？提交后将不可恢复');
}
Menuon(5);
</script>
<?php include tpl('footer');?>