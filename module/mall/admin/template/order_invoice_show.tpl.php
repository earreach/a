<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">订单编号</td>
<td class="tr"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $I['oid'];?>', '订单详情');" class="t"><?php echo $I['oid'];?></a></td>
</tr>
<tr>
<td class="tl">商品</td>
<td><?php echo $I['title'];?></td>
</tr>
<tr>
<td class="tl">卖家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($I['seller']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $I['seller'];?>');" class="t"><?php echo $I['seller'];?></a></td>
</tr>
<tr>
<td class="tl">买家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($I['buyer']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $I['buyer'];?>');" class="t"><?php echo $I['buyer'];?></a></td>
</tr>
<tr>
<td class="tl">申请时间</td>
<td class="tr"><?php echo timetodate($I['addtime'], 5);?></td>
</tr>
<tr>
<td class="tl">发票金额</td>
<td class="tr f_red"><?php echo $DT['money_sign'];?><?php echo $I['amount'];?></td>
</tr>
<tr>
<td class="tl">发票类型</td>
<td class="tr"><?php echo $I['type'];?></td>
</tr>
<tr>
<td class="tl">发票抬头</td>
<td class="tr"><?php echo $I['company'];?></td>
</tr>
<tr>
<td class="tl">纳税人识别号</td>
<td class="tr"><?php echo $I['taxid'];?></td>
</tr>
<tr>
<td class="tl">地址电话</td>
<td class="tr"><?php echo $I['address'];?> <?php echo $I['telephone'];?></td>
</tr>
<tr>
<td class="tl">开户行及账号</td>
<td class="tr"><?php echo $I['bank'];?> <?php echo $I['account'];?></td>
</tr><?php if($I['url']) { ?>
<tr>
<td class="tl">开票状态</td>
<td class="tr f_green">已上传</td>
</tr>
<tr>
<td class="tl">电子发票</td>
<td class="tr"><a href="javascript:;" onclick="_preview('<?php echo $I['url'];?>');" class="t">查看发票</a> &nbsp; <a href="<?php echo DT_PATH;?>api/attach.php?url=<?php echo urlencode($I['url']);?>&name=<?php echo urlencode($I['company'].'-'.$I['type'].'-'.$I['oid'].'.'.file_ext($I['url']));?>" target="_blank" class="t">下载发票</a></td>
</tr>
<tr>
<td class="tl">开票时间</td>
<td class="tr"><?php echo timetodate($I['updatetime'], 5);?></td>
</tr>
<tr>
<td class="tl">备注信息</td>
<td class="tr"><?php if($I['note']) { ?><?php echo $I['note'];?><?php } else { ?>无<?php } ?></td>
</tr>
<?php } else if($I['send_type']) { ?>
<tr>
<td class="tl">开票状态</td>
<td class="tr f_green">已快递</td>
</tr>
<tr>
<td class="tl">快递名称</td>
<td class="tr"><?php echo $I['send_type'];?></td>
</tr>
<?php if($I['send_no']) { ?>
<tr>
<td class="tl">快递单号</td>
<td class="tr"><?php echo $I['send_no'];?><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#copy-no" onclick="Dtoast('快递单号已复制');"/>
<div style="z-index:1000;position:absolute;top:-10000px;"><textarea id="copy-no"><?php echo $I['send_no'];?></textarea></div>
<?php load('clipboard.min.js');?>

<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
$(function(){
	$('#express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo $auth;?>');
});
</script>
</td>
</tr>
<tr>
<td class="tl">追踪结果</td>
<td class="tr" style="line-height:200%;"><div id="express"><img src="<?php echo DT_SKIN;?>loading.gif" align="absmiddle"/> 正在查询...</div></td>
</tr>
<?php } ?>
<tr>
<td class="tl">开票时间</td>
<td class="tr"><?php echo timetodate($I['updatetime'], 5);?></td>
</tr>
<tr>
<td class="tl">备注信息</td>
<td class="tr"><?php if($I['note']) { ?><?php echo $I['note'];?><?php } else { ?>无<?php } ?></td>
</tr>
<?php } else { ?>
<tr>
<td class="tl">开票状态</td>
<td class="tr f_gray">待开票</td>
</tr>
<?php } ?>
</table>

<div class="tt">买家信息</div>
<table cellspacing="0" class="tb">
<?php if($DT['postcode']) { ?>
<tr>
<td class="tl">邮编</td>
<td><?php echo $I['buyer_postcode'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">地址</td>
<td><?php echo $I['buyer_address'];?></td>
</tr>
<tr>
<td class="tl">姓名</td>
<td><?php echo $I['buyer_name'];?></td>
</tr>
<tr>
<td class="tl">手机</td>
<td><?php echo $I['buyer_mobile'];?></td>
</tr>
<tr>
<td class="tl">邮件</td>
<td><?php echo $I['buyer_email'];?></td>
</tr>
</table>
<div class="sbt"><input type="button" value="确 定" class="btn-g" onclick="window.parent.cDialog();"/></div>
<?php include tpl('footer');?>