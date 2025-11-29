<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">订单编号</td>
<td class="tr"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $C['oid'];?>', '订单详情');" class="t"><?php echo $C['oid'];?></a></td>
</tr>
<tr>
<td class="tl">合同编号</td>
<td class="tr"><?php echo $itemid;?></td>
</tr>
<tr>
<td class="tl">商品</td>
<td><?php echo $C['title'];?></td>
</tr>
<tr>
<td class="tl">甲方</td>
<td class="tr"><?php echo $C['buyer_company'];?></td>
</tr>
<tr>
<td class="tl">乙方</td>
<td class="tr"><?php echo $C['seller_company'];?></td>
</tr>
<tr>
<td class="tl">金额</td>
<td class="tr f_red"><?php echo $DT['money_sign'];?><?php echo $C['amount'];?></td>
</tr>
<tr>
<td class="tl">买家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($C['buyer']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $C['buyer'];?>');" class="t"><?php echo $C['buyer'];?></a></td>
</tr>
<tr>
<td class="tl">卖家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($C['seller']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $C['seller'];?>');" class="t"><?php echo $C['seller'];?></a></td>
</tr>
<tr>
<td class="tl">下单时间</td>
<td class="tr"><?php echo timetodate($C['addtime'], 5);?></td>
</tr>
<tr>
<td class="tl">甲方签署</td>
<td class="tr"><?php echo $C['buyer_time'] ? timetodate($C['buyer_time'], 5) : 'N/A';?><?php if($C['buyer_contract']) { ?> &nbsp; <a href="javascript:;" onclick="_preview('<?php echo $C['buyer_contract'];?>');" class="t">查看合同</a> &nbsp; <a href="<?php echo DT_PATH;?>api/attach.php?url=<?php echo urlencode($C['buyer_contract']);?>&name=<?php echo urlencode($C['buyer_company'].'-甲方合同-'.$C['oid'].'.'.file_ext($C['buyer_contract']));?>" target="_blank" class="t">下载合同</a><?php } ?></td>
</tr>
<tr>
<td class="tl">乙方签署</td>
<td class="tr"><?php echo $C['seller_time'] ? timetodate($C['seller_time'], 5) : 'N/A';?><?php if($C['seller_contract']) { ?> &nbsp; <a href="javascript:;" onclick="_preview('<?php echo $C['seller_contract'];?>');" class="t">查看合同</a> &nbsp; <a href="<?php echo DT_PATH;?>api/attach.php?url=<?php echo urlencode($C['seller_contract']);?>&name=<?php echo urlencode($C['seller_company'].'-乙方合同-'.$C['oid'].'.'.file_ext($C['seller_contract']));?>" target="_blank" class="t">下载合同</a><?php } ?></td>
</tr>
<tr>
<td class="tl">状态</td>
<td class="tr"><?php echo $dstatus[$C['status']];?></td>
</tr>
</table>
<div class="sbt"><input type="button" value="确 定" class="btn-g" onclick="window.parent.cDialog();"/></div>
<?php include tpl('footer');?>