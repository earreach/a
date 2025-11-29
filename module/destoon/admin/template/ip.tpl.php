<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<?php if(!isset($js)) { ?><div class="tt"><?php echo $ip;?></div><?php } ?>
<table cellspacing="0" class="tb">
<tr>
<td style="border:none;">&nbsp;<a href="<?php echo gourl('https://www.baidu.com/s?wd='.$ip);?>" target="_blank"><?php echo ip2area($ip);?></a> &nbsp; <a href="javascript:window.parent.Dwidget('?file=banip&action=add&ip=<?php echo $ip;?>', '禁止IP访问');" class="b">禁止</a></td>
</tr>
</table>
<?php include tpl('footer');?>