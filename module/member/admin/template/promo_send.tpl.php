<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><input type="text" size="20" name="username" id="username" value=""/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠名称</td>
<td><?php echo $title;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 所属商家</td>
<td><a href="javascript:;" onclick="_user('<?php echo $username;?>');" class="t"><?php echo $username ? $username : '全站通用';?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 优惠金额</td>
<td><?php echo $price;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 最低消费</td>
<td><?php echo $cost;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 数量限制</td>
<td><?php echo $amount;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 有效时间</td>
<td><?php echo $fromtime;?> 至 <?php echo $totime;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 频道限制</td>
<td><?php echo $mid ? $MODULE[$mid]['name'] : '不限';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 类目限制</td>
<td>
<?php 
$CAT = $catid ? get_cat($catid) : array();
echo $CAT ? '<a href="'.$MODULE[$mid]['linkurl'].$CAT['linkurl'].'" target="_blank" class="t">'.$CAT['catname'].'</a>' : '不限';
?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 指定商品</td>
<td>
<?php 
if($mid && $itemids) {
	echo '<div style="line-height:24px;">';
	$result = $db->query("SELECT * FROM ".get_table($mid)." WHERE itemid IN ($itemids) ORDER BY edittime DESC");
	while($r = $db->fetch_array($result)) {
		echo '<a href="'.$MODULE[$mid]['linkurl'].$r['linkurl'].'" target="_blank" class="t">'.$r['title'].'</a><br/>';
	}
	echo '</div>';
} else {
	echo '不限';
}
?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><input name="note" type="text" id="note" size="60" value="<?php echo $note;?>"/> <span id="dnote" class="f_red"></span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="赠 送" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="parent.cDialog();"/></div>
</form>
<script type="text/javascript">
function check() {
	var f;
	var l;
	f = 'username';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写会员名', f);
		return false;
	}
	return true;
}
</script>
<?php include tpl('footer');?>