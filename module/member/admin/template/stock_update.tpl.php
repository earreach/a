<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="type" value="<?php echo $type;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<?php if($title) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 商品名称</td>
<td><?php echo $title;?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> <?php if($type) { ?>出库<?php } else { ?>入库<?php } ?>数量</td>
<td><input name="amount" type="text" size="10" value="<?php echo $amount;?>" id="amount"/> <span id="damount" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 操作事由</td>
<td><input name="reason" type="text" size="20" value="<?php echo $reason;?>" id="reason"/>&nbsp;
<select onchange="Dd('reason').value=this.value;">
<option value="">常用事由</option>
<?php if($type) { ?>
<option value="商品售出">商品售出</option>
<option value="库存修正">库存修正</option>
<?php } else { ?>
<option value="商品进货">商品进货</option>
<option value="买家退货">买家退货</option>
<option value="库存修正">库存修正</option>
<?php } ?>
</select>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><input name="note" type="text" size="60" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 所属会员</td>
<td><input name="username" type="text" id="username" size="20" value="<?php echo $username;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 条形编码</td>
<td><input name="skuid" type="text" id="skuid" size="60" value="<?php echo $skuid;?>" placeholder="请用扫描枪扫描或填写"<?php if($skuid) { ?> disabled<?php } ?>/> <span id="dskuid" class="f_red"></span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $type ? '出 库' : '入 库';?>" class="btn-<?php echo $type ? 'b' : 'g';?>"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="if(window.parent.document.getElementById('Dtop')){window.parent.location.reload();}else{Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=record');}"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'amount';
	l = parseInt(Dd(f).value);
	if(l < 1) {
		Dmsg('请填写数量', f);
		return false;
	}
	f = 'skuid';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写条形编码', f);
		return false;
	}
	return true;
}
<?php if(!$skuid) { ?>Dd('skuid').focus();<?php } ?>
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>