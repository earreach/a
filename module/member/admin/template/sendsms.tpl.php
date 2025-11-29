<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="send" value="1"/>
<input type="hidden" name="preview" id="preview" value="0"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 发送方式</td>
<td>
	<label><input type="radio" name="sendtype" value="1" id="s1" onclick="ck(1);"<?php echo $sendtype == 1 ? ' checked' : '';?>/> 单收信人</label>&nbsp;&nbsp;
	<label><input type="radio" name="sendtype" value="2" id="s2" onclick="ck(2);"<?php echo $sendtype == 2 ? ' checked' : '';?>/> 多收信人</label>&nbsp;&nbsp;
	<label><input type="radio" name="sendtype" value="3" id="s3" onclick="ck(3);"<?php echo $sendtype == 3 ? ' checked' : '';?>/> 列表群发</label>&nbsp;&nbsp;
	<label><input type="radio" name="sendtype" value="4" id="s4" onclick="ck(4);"<?php echo $sendtype == 4 ? ' checked' : '';?>/> 条件群发</label>&nbsp;&nbsp;
</td>
</tr>
<tbody id="t1" style="display:;">
<tr>
<td class="tl"><span class="f_red">*</span> 接收号码</td>
<td><input type="text" size="35" name="mobile" value="<?php echo $mobile;?>"/></td>
</tr>
</tbody>
<tbody id="t2" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 接收号码</td>
<td><textarea name="mobiles" rows="4" cols="35"><?php echo $mobiles;?></textarea></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">一行一个接收号码</td>
</tr>
</tbody>
<tbody id="t3" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 号码列表</td>
<td class="f_red">
<?php
	$files = glob(DT_ROOT.'/file/'.$path.'/*.txt');
	echo '<select name="list" id="list"><option value="0">请选择号码列表</option>';
	if($files) {
		foreach($files as $v) {
			$c = file_get($v);
			$name = basename($v);
			$note = cutstr($c, '#', "\r\n");
			$nums = substr_count($c, "\n") + ($note ? 0 : 1);
			echo '<option value="'.$name.'" class="f_fd">'.$name.($note ? ' '.$note : '').' '.$nums.'条</option>';
		}
	} else {
		echo '<option value="">暂无列表</option>';
	}
	echo '</select> &nbsp; ';
?>
<img src="<?php echo DT_STATIC;?>image/ico-link.png" width="11" height="11" title="查看" class="c_p" onclick="if(Dd('list').value != 0){Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&filename='+Dd('list').value, $('#list').find('option:selected').text(), 600, 400);}else{alert('请先选择文件');Dd('list').focus();}"/> &nbsp;
<img src="<?php echo DT_STATIC;?>image/ico-add.png" width="11" height="11" title="获取" class="c_p" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=make', '获取列表');"/> &nbsp; 
</td>
</tr>
</tbody>
<tbody id="t4" style="display:none;">
<?php include tpl('sendlist_chip', $module);?>
</tbody>
<tbody id="t0" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 每轮发送</td>
<td><input type="text" size="5" name="pernum" id="pernum" value="5"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 时间间隔</td>
<td><input type="text" size="5" name="pertime" id="pertime" value="3"/><?php tips('例如设置为3，则系统在每轮发送之后暂停3秒，以免因为发送过快而被收件服务器拒收');?></td>
</tr>
</tbody>
<tr>
<td class="tl"><span class="f_red">*</span> 短信内容</td>
<td>
<table cellpadding="0" cellspacing="0" width="100%" class="ctb">
<tr>
<td valign="top" width="250" style="padding:0;"><textarea name="content" id="content" rows="15" cols="35" onkeyup="S();" onblur="S();"></textarea></td>
<td valign="top" class="f_gray lh20">
- 当前已输入<b id="len1">0</b>字，签名<b id="len2">0</b>字，共<b id="len3" class="f_red">0</b>字，分<b id="len4" class="f_blue">0</b>条短信 (约<?php echo $DT['sms_len'];?>字/条)<br/>
<span class="f_red">- 以上分条仅为系统估算，实际分条以运营商返回数据为准</span><br/>
- 内容支持变量，会员资料保存于$user数组<br/>
- 例 {$user[username]} 表示会员名<br/>
- 例 {$user[company]} 表示公司名<br/>
- 如果是给非会员发送短信，请不要使用变量<br/>
<?php if(!$DT['sms'] || !DT_CLOUD_UID || !DT_CLOUD_KEY) { ?>
<span class="f_red">- 注意：无法发送，未设置发送参数</span> <a href="?file=setting&tab=8" class="t">点此设置</a><br/>
<?php } else { ?>
<span class="f_red">
- 由于政策原因，并非所有内容都可以正常发送...<a href="<?php echo gourl('https://www.destoon.com/doc/use/29.html');?>" target="_blank" class="t">了解详情</a><br/>
- 发送任何违法信息，帐号会被禁用且不退款<br/>
</span>
- <a href="javascript:;" onclick="rand_sms();" class="t">测试发送一条随机验证码短信</a><br/>
<?php } ?>
<span id="dcontent" class="f_red"></span>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 短信签名</td>
<td><input type="text" size="35" name="sign" id="sign" value="<?php echo $DT['sms_sign'];?>" onkeyup="S();" onblur="S();"/></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="发 送" class="btn-g" onclick="Dd('preview').value=0;this.form.target='';"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="预 览" class="btn" onclick="Dd('preview').value=1;this.form.target='_blank';"/></div>
</form>
<script type="text/javascript">
var sms_len = <?php echo $DT['sms_len'];?>;
function S() {
	var sms_sign = Dd('sign').value;
	var len_1 = Dd('content').value.length;
	var len_2 = sms_sign.length;
	Dd('len1').innerHTML = len_1;
	Dd('len2').innerHTML = len_2;
	Dd('len3').innerHTML = len_1+len_2;
	Dd('len4').innerHTML = Math.ceil((len_1+len_2)/sms_len);
}
S();
var i = 1;
function ck(id) {
	Dd('t'+i).style.display='none';
	Dd('t'+id).style.display='';
	Dd('t0').style.display=(id==3||id==4) ? '' : 'none';
	i = id;
}
ck(<?php echo $sendtype;?>);
function rand_sms() {
	var chars = "0123456789";
	var code = '';
	for(i=0;i<6;i++){code += chars.charAt(Math.floor( Math.random()*chars.length));}
	Dd('content').value='您的短信验证码为:'+code+'，有效期30分钟，切勿透露给他人';

}
function check() {
	var l;
	var f;
	f = 'content';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('内容不能为空', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>