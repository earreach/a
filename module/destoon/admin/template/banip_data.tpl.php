<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="180">库文件</th>
<th width="150">更新时间</th>
<th width="150">最新版本</th>
<?php if($get) { ?>
<th width="150">在线更新</th>
<?php } else { ?>
<th width="150">手动下载<?php echo tips('提示：无法在线更新，请手动下载wry.rar，解压其中的wry.dat，覆盖上传至file/ipdata/目录');?></th>
<?php } ?>
<th></th>
</tr>
<tr align="center">
<td align="left" class="f_fd" height="32">&nbsp;<img src="file/ext/sql.gif" width="16" height="16" alt="" align="absmiddle"/> file/ipdata/wry.dat</td>
<td><?php echo $now?></td>
<td><a href="<?php echo gourl('https://www.destoon.com/doc/skill/28.html');?>" target="_blank"><?php echo $new;?></a></td>
<?php if($get) { ?>
<?php if($update) { ?>
<td><a href="?file=<?php echo $file;?>&action=update" class="t" title="文件较大，更新可能用时稍长，请耐心等待" onclick="Dtoast('更新中，请稍后...', '', 600);">立即更新</a></td>
<?php } else { ?>
<td class="f_gray">暂无更新</td>
<?php } ?>
<?php } else { ?>
<td><a href="?file=<?php echo $file;?>&action=down" class="t">立即下载</a></td>
<?php } ?>
<td></td>
</tr>
</table>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>