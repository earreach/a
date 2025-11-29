<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
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
<?php foreach($ECK as $k=>$v) { ?>
<?php if(isset($E[$k]) && $E[$k] != $U[$k]) {?>
<tr>
<?php if(!$edittime) {?><td align="center"><label><input type="radio" name="pass[<?php echo $k;?>]" value="1" data-pass="1" checked/> 通过</label> &nbsp; <label><input type="radio" name="pass[<?php echo $k;?>]" value="0" data-unpass="1"/> 拒绝</label></td><?php } ?>
<td align="center"><?php echo $v;?></td>
<td<?php if($k == 'content') { ?> valign="top"<?php } ?>>
<?php 
if($k == 'thumb') {
	echo '<img src="'.imgurl($E[$k]).'" width="80"/>';
} else if($k == 'cover') {	
	echo '<img src="'.imgurl($E[$k]).'" width="600"/>';
} else if($k == 'areaid') {
	echo area_pos($E[$k], ' / ');
} else if($k == 'capital') {
	echo $E[$k].(isset($E['regunit']) ? ' '.$E['regunit'] : '');
} else if($k == 'gzhqr') {
	echo '<img src="'.imgurl($E[$k]).'" width="128"/>';
} else if($k == 'homepage') {
	echo '<a href="'.gourl($E[$k]).'" target="_blank" class="t"/>'.$E[$k].'</a>';
} else {
	echo $E[$k];
}
?>
</td>
<td<?php if($k == 'content') { ?> valign="top"<?php } ?>>
<?php 
if($k == 'thumb') {
	echo '<img src="'.imgurl($U[$k]).'" width="80"/>';
} else if($k == 'cover') {	
	echo '<img src="'.imgurl($U[$k]).'" width="600"/>';
} else if($k == 'areaid') {
	echo area_pos($U[$k], ' / ');
} else if($k == 'capital') {
	echo $U[$k].(isset($E['regunit']) ? ' '.$U['regunit'] : '');
} else if($k == 'gzhqr') {	
	echo '<img src="'.imgurl($U[$k]).'" width="128"/>';
} else if($k == 'homepage') {
	echo '<a href="'.gourl($U[$k]).'" target="_blank" class="t"/>'.$U[$k].'</a>';
} else {
	echo $U[$k];
}
?>
</td>
</tr>
<?php if($k == 'content') { ?>
<tr align="center">
<?php if(!$edittime) {?><td></td><?php } ?>
<td>内容源码</td>
<td><textarea style="width:98%;height:320px;" class="f_fd"><?php echo $E[$k]; ?></textarea></td>
<td><textarea style="width:98%;height:320px;" class="f_fd"><?php echo $U[$k]; ?></textarea></td>
<?php } ?>
<?php } ?>
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
Menuon(0);
$(function(){
	$('#fix-th').width(($(document).width()-<?php echo $edittime ? 200 : 400;?>)/2);
	if(window.screen.width<1366) {
		$('.tab a').css('padding', '0 12px');
	}
});
</script>
<?php include tpl('footer');?>