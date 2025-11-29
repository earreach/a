<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb ls">
<tr>
<th>文件</th>
<th width="150">大小</th>
<th width="150">修改时间</th>
</tr>
<?php foreach($lists as $v) { ?>
<tr align="center">
<td align="left" class="f_fd">&nbsp;<img src="file/ext/<?php echo is_file(DT_ROOT.'/file/ext/'.file_ext($v).'.gif') ? file_ext($v) : 'oth';?>.gif" alt="" align="absmiddle"/> <?php echo str_replace(DT_ROOT.'/file/patch/'.$fid.'/', '', $v);?></td>
<td><?php echo dround(filesize($v)/1024);?> Kb</td>
<td><?php echo timetodate(filemtime($v), 6);?></td>
</tr>
<?php } ?>
</table>
<?php include tpl('footer');?>