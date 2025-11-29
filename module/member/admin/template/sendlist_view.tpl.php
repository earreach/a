<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr align="center">
<td><textarea name="content" id="content" class="f_fd" style="width:560px;height:260px;padding:10px;"><?php echo $content;?></textarea></td>
</tr>
<tr align="center">
<td><input type="button" value="关 闭" class="btn" onclick="window.parent.cDialog();"/></td>
</tr>
</table>
<?php include tpl('footer');?>