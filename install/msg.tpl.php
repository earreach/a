<?php
defined('IN_DESTOON') or exit('Access Denied');
include IN_ROOT.'/header.tpl.php';
?>
<div class="head">
	<div onclick="window.open('https://www.destoon.com/');" style="cursor:pointer;">
		<strong>提示信息</strong><br/>
		<?php echo isset($sub) ? strip_tags($sub) : '如果对此提示信息有疑问，请访问官网 www.destoon.com'; ?>
	</div>
</div>
<div class="body">
<p><?php echo $msg;?></p>
</div>
<div class="foot">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td width="220">
<div class="progress" style="display:none;">
<div id="progress"></div>
</div>
</td>
<td id="percent" style="display:none;"></td>
<td height="60" align="right">
<input type="button" value=" 返回(R) " onclick="history.back(-1);"/>
<input type="button" value=" 刷新(F5) " onclick="window.location.reload();"/>
&nbsp;&nbsp;
<input type="button" value=" 关闭(C) " onclick="window.close();"/>
<?php
include IN_ROOT.'/footer.tpl.php';
?>