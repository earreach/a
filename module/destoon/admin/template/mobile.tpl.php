<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<?php if(!isset($js)) { ?><div class="tt"><?php echo $mobile;?></div><?php } ?>
<table cellspacing="0" class="tb">
<tr>
<td style="border:none;">&nbsp;<a href="<?php echo gourl('https://www.baidu.com/s?wd='.$mobile);?>" target="_blank"><?php echo mobile2area($mobile);?></a></td>
</tr>
</table>
<?php include tpl('footer');?>