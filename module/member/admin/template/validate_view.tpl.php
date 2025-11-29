<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('player.js');
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">会员名</td>
<td>&nbsp;<a href="javascript:;" onclick="_user('<?php echo $U['username'];?>');"><?php echo $U['username'];?></a></td>
</tr>
<tr>
<td class="tl">公司名</td>
<td>&nbsp;<a href="<?php echo $U['linkurl'];?>" target="_blank"><?php echo $U['company'];?></a></td>
</tr>
<tr>
<td class="tl">提交时间</td>
<td>&nbsp;<?php echo $addtime;?></td>
</tr>
<tr>
<td class="tl">IP</td>
<td>&nbsp;<a href="javascript:;" onclick="_ip('<?php echo $ip;?>');"><?php echo $ip;?></a> - <?php echo ip2area($ip);?></td>
</tr>
<?php if($edittime) {?>
<tr>
<td class="tl">审核时间</td>
<td>&nbsp;<?php echo timetodate($edittime, 6);?></td>
</tr>
<tr>
<td class="tl">审核人</td>
<td>&nbsp;<a href="javascript:;" onclick="_user('<?php echo $c['editor'];?>');"><?php echo $c['editor'];?></a></td>
</tr>
<tr>
<td class="tl">审核结果</td>
<td class="lh20"><?php echo $c['note'];?></td>
</tr>
<?php } ?>
</table>
<form method="post" action="?" onsubmit="return confirm('确定要提交审核吗？此操作将不可撤销');">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<?php if(!$edittime) {?><th width="155">审核结果</th><?php } ?>
<th width="155">项目</th>
<th>修改为</th>
<th id="fix-th">修改前</th>
</tr>
<?php 
	foreach($S as $k=>$v) { 
		if($E[$k] == $H[$k]) continue;
?>
<tr>
<?php if(!$edittime) {?><td align="center"><label><input type="radio" name="pass[<?php echo $k;?>]" value="1" data-pass="1" checked/> 通过</label> &nbsp; <label><input type="radio" name="pass[<?php echo $k;?>]" value="0" data-unpass="1"/> 拒绝</label></td><?php } ?>
<td align="center"><?php echo $v;?></td>
<td>
<?php 
	if(in_array($k, array('background', 'logo', 'banner', 'banner1', 'banner2', 'banner3', 'banner4', 'banner5'))) {
		echo $E[$k] ? '<a href="javascript:;" onclick="_preview(\''.$E[$k].'\');"><img src="'.$E[$k].'" style="max-width:320px;"/></a>' : '';
	} elseif(in_array($k, array('bannerlink1', 'bannerlink2', 'bannerlink3', 'bannerlink4', 'bannerlink5'))) {
		echo $E[$k] ? '<a href="" target="_blank">'.$E[$k].'</a>' : '';
	} elseif(in_array($k, array('bannerf', 'video'))) {
		echo $E[$k] ? '<script type="text/javascript">document.write(player(\''.$E[$k].'\',320,180,0));</script>' : '';
	} elseif($k == 'bannert') {
		echo $E[$k] ? ($E[$k] == 2 ? '幻灯片' : '视频') : '图片';
	} elseif($k == 'side_pos') {
		echo $E[$k] ? '侧栏在右' : '侧栏在左';
	} elseif($k == 'show_stats') {
		echo $E[$k] ? '显示' : '不显示';
	} else {
		echo $E[$k];
	}
?>
</td>
<td>
<?php 
	if(in_array($k, array('background', 'logo', 'banner', 'banner1', 'banner2', 'banner3', 'banner4', 'banner5'))) {
		echo $H[$k] ? '<a href="javascript:;" onclick="_preview(\''.$H[$k].'\');"><img src="'.$H[$k].'" style="max-width:320px;"/></a>' : '';
	} elseif(in_array($k, array('bannerlink1', 'bannerlink2', 'bannerlink3', 'bannerlink4', 'bannerlink5'))) {
		echo $H[$k] ? '<a href="" target="_blank">'.$H[$k].'</a>' : '';
	} elseif(in_array($k, array('bannerf', 'video'))) {
		echo $H[$k] ? '<script type="text/javascript">document.write(player(\''.$H[$k].'\',320,180,0));</script>' : '';
	} elseif($k == 'bannert') {
		echo $H[$k] ? ($H[$k] == 2 ? '幻灯片' : '视频') : '图片';
	} elseif($k == 'side_pos') {
		echo $H[$k] ? '侧栏在右' : '侧栏在左';
	} elseif($k == 'show_stats') {
		echo $H[$k] ? '显示' : '不显示';
	} else {
		echo $H[$k];
	}
?>
</td>
</tr>
<?php } ?>
<?php 
	foreach($M as $kk=>$vv) { 
		if($M[$kk] == $O[$kk]) continue;
?>
<tr>
<?php if(!$edittime) {?><td align="center"><label><input type="radio" name="pass[<?php echo $kk;?>]" value="1" data-pass="1" checked/> 通过</label> &nbsp; <label><input type="radio" name="pass[<?php echo $kk;?>]" value="0" data-unpass="1"/> 拒绝</label></td><?php } ?>
<td align="center"><?php echo $N[$kk];?></td>
<td>
	<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
	<tr bgcolor="#F5F5F5" align="center">
	<td>启用</td>
	<td>排序</td>
	<td>名称</td>
	<td>数量</td>
	</tr>
	<?php foreach($vv as $k=>$v) { ?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $v['status'] ? '<img src="'.DT_STATIC.'image/yes.png"/>' : '';?></td>
	<td><?php echo $v['listorder'];?></td>
	<td><?php echo $v['name'];?></td>
	<td><?php echo $v['pagesize'];?></td>
	</tr>
	<?php } ?>
	</table>

</td>
<td>
<?php if($O[$kk]) { ?>
	<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
	<tr bgcolor="#F5F5F5" align="center">
	<td>启用</td>
	<td>排序</td>
	<td>名称</td>
	<td>数量</td>
	</tr>
	<?php foreach($O[$kk] as $k=>$v) { ?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $v['status'] ? '<img src="'.DT_STATIC.'image/yes.png"/>' : '';?></td>
	<td><?php echo $v['listorder'];?></td>
	<td><?php echo $v['name'];?></td>
	<td><?php echo $v['pagesize'];?></td>
	</tr>
	<?php } ?>
	</table>
<?php } else { ?>
	会员组默认设置
<?php } ?>
</td>
</tr>
<?php } ?>
</table>
<?php if($edittime) {?>
<div class="sbt"><input type="button" value="确 定" class="btn-g" onclick="history.back(-1);"/></div>
<?php } else { ?>
<div class="btns">
<textarea style="width:300px;height:16px;" name="reason" id="reason" onfocus="if(this.value=='操作原因')this.value='';">操作原因</textarea>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="msg" id="msg" value="1" onclick="Dn();" checked/><label for="msg"> 站内通知</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="eml" id="eml" value="1" onclick="Dn();"/><label for="eml"> 邮件通知</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="sms" id="sms" value="1" onclick="Dn();"/><label for="sms"> 短信通知</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="checkbox" name="wec" id="wec" value="1" onclick="Dn();"/><label for="wec"> 微信通知</label>&nbsp;&nbsp;&nbsp;&nbsp;
</div>
<div class="btns">
<label style="display:inline-block;width:160px;text-align:center;" class="jt"><span onclick="$('[data-pass]').prop('checked','checked');">全部通过</span> &nbsp; <span onclick="$('[data-unpass]').prop('checked','checked');">全部拒绝</span></label>
<input type="submit" name="submit" value="确 定" class="btn-g"/>
</div>
<?php } ?>
</form>
<script type="text/javascript">
Menuon(1);
$(function(){
	$('#fix-th').width(($(document).width()-200)/2);
	if(window.screen.width<1366) {
		$('.tab a').css('padding', '0 12px');
	}
});
</script>
<?php include tpl('footer');?>