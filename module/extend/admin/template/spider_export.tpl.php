<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 规则内容</td>
<td><textarea id="rule" style="width:98%;height:500px;"><?php echo $rule;?></textarea></td>
</tr>
</table>
<div class="sbt">
<input type="button" value="复 制" class="btn-g" data-clipboard-action="copy" data-clipboard-target="#rule" onclick="Dtoast('规则内容已复制');"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="关 闭" class="btn" onclick="window.parent.cDialog();"/>
</div>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
</script>
<?php include tpl('footer');?>