<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">var clipboard = new Clipboard('[data-clipboard-action]');</script>
<form method="post" action="?" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 收款银行</td>
<td><input type="text" style="width:300px;" value="<?php echo $item['bank'];?>" id="bank"/> <img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#bank" onclick="showmsg('收款银行已复制');"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 开户网点</td>
<td><input type="text" style="width:300px;" value="<?php echo $item['branch'];?>" id="branch"/> <img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#branch" onclick="showmsg('开户网点已复制');"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 收款户名</td>
<td><input type="text" style="width:300px;" value="<?php echo $item['truename'];?>" id="truename"/> <img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#truename" onclick="showmsg('收款户名已复制');"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 收款帐号</td>
<td><input type="text" style="width:300px;" value="<?php echo $item['account'];?>" id="account"/> <img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#account" onclick="showmsg('收款帐号已复制');"/></td>
</tr>
<tr class="on">
<td class="tl"><span class="f_hid">*</span> 实付金额</td>
<td class="f_red"><input type="text" style="width:300px;" value="<?php echo $item['amount'];?>" id="amount"/> <img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#amount" onclick="showmsg('实付金额已复制');"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员名</td>
<td><a href="javascript:;" onclick="_user('<?php echo $item['username'];?>');" class="t"><?php echo $item['username'];?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 资金流水</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=record&username=<?php echo $item['username'];?>', '资金流水');" class="t">点击查看</a></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 申请时间</td>
<td><?php echo $item['addtime'];?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 申请IP</td>
<td><?php echo $item['ip'];?> - <?php echo ip2area($item['ip']);?></td>
</tr>
<tr class="on">
<td class="tl"><span class="f_red">*</span> 受理结果</td>
<td>
<?php
unset($dstatus[0]);
foreach($dstatus as $k=>$v) {
?>
<label><input name="status" type="radio" value="<?php echo $k;?>"/> <?php echo $v;?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<?php } ?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 原因及备注</td>
<td><input name="note" type="text" size="40"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 注意</td>
<td class="f_red">此表单一经提交，将不可再修改或删除，请务必谨慎操作</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>