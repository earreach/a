<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" id="price" value="<?php echo $GROUP[$groupid]['fee'];?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><a href="javascript:;" onclick="_user('<?php echo $username;?>');" class="t"><?php echo $username;?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员组</td>
<td>
<?php 
$select = '';
foreach($GROUP as $g) {
	if($g['vip'] > 0) {
		echo '<label><input type="radio" name="post[groupid]" value="'.$g['groupid'].'" '.($groupid == $g['groupid'] ? 'checked' : '').' onclick="vG('.$g['groupid'].');Dd(\'amount\').value=\''.$g['fee'].'\';Dd(\'price\').value=\''.$g['fee'].'\';"/> '.$g['groupname'].'</label>&nbsp;';
		$TG = cache_read('group-'.$g['groupid'].'.php');
		$select .= '<select id="fee-'.$g['groupid'].'" onchange="vS(this.value);" style="display:none;">';
		$select .= '<option value="">快捷选择</option>';
		foreach($L['account_month'] as $k=>$v) {
			if($TG['fee_'.$k]) {
				$select .= '<option value="'.$TG['fee_'.$k].'|'.timetodate(datetotime('+'.$k.' month') + $diff, 3).'"'.($k == 12 ? ' selected' : '').'>'.$v.'</option>';
			}
		}
		$select .= '</select>';
	}
}
?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 开始时间</td>
<td><input type="text" value="<?php echo $fromdate;?>" disabled style="width:100px;"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 到期时间</td>
<td>
<?php echo dcalendar('post[totime]', $todate);?>&nbsp;
<span id="ftime"><?php echo $select;?></span>&nbsp;
<span id="dtime" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 应付金额</td>
<td><input type="text" name="amount" size="10" id="amount" value="<?php echo $GROUP[$groupid]['fee'];?>"/> <?php echo $DT['money_unit'];?> <span id="damount" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 余额扣除</td>
<td>
<label><input type="radio" name="pay" value="1" checked/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay" value="0"/> 否</label> <?php tips('从会员账户余额里扣除相关费用，请选是<br/>如果已经通过其他方式收款，请选否');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><input type="text" name="note" size="60" value=""/></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="续 费" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=vip');"/></div>
</form>
<script type="text/javascript">
function check() {
	if(Dd('posttotime').value.length != 10) {
		Dmsg('请选择服务有效期', 'time', 1);
		return false;
	}
	return true;
}
function vG(gid) {
	$('#ftime select').hide();
	$('#fee-'+gid).show();
	vS($('#fee-'+gid).val());
}
function vS(str) {
	if(str.indexOf('|') != -1) {
		var t = str.split('|');
		Dd('amount').value = t[0];
		Dd('price').value = t[0];
		Dd('posttotime').value = t[1];
	}
}
vG(<?php echo $groupid;?>);
Menuon(1);
</script>
<?php include tpl('footer');?>