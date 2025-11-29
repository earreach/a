<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 修改内容</td>
<td><textarea name="content" id="content" class="dsn"><?php echo $content;?></textarea><?php echo deditor($moduleid, 'content', 'Destoon', '100%', 300);?></td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" name="submit" value="修 改" class="btn-g">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="关 闭" class="btn" onclick="window.parent.location.reload();"/></td>
</tr>
</table>
</form>
<?php include tpl('footer');?>