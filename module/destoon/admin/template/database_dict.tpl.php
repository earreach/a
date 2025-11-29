<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<input type="hidden" name="table" value="<?php echo $table;?>"/>
<input type="hidden" name="nt" value="<?php echo $note;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th>字段名</th>
<th>注释</th>
<th>备注</th>
<th>类型</th>
<?php if($job == 'verify') { ?><th width="100">校验结果</th><?php } ?>
</tr>
<?php foreach($fields as $k=>$v) {?>
<tr>
<td>&nbsp;&nbsp;<strong><?php echo $v['Field'];?></strong></td>
<td>&nbsp;<input type="text" size="20" name="name[<?php echo $v['Field'];?>]" value="<?php echo $v['cn_name'];?>"/></td>
<td>&nbsp;<input type="text" size="20" name="note[<?php echo $v['Field'];?>]" value="<?php echo $v['cn_note'];?>" title="<?php echo $v['cn_note'];?>"/></td>
<td>&nbsp;<strong><?php echo $v['Type'];?></strong></td>
<?php if($job == 'verify') { ?><td align="center"><?php echo $R[$v['Field']];?></td><?php } ?>
</tr>
<?php } ?>
<?php if($job == 'verify' && $V) { ?>
<?php foreach($V as $k=>$v) {?>
<tr>
<td>&nbsp;&nbsp;<strong><?php echo $k;?></strong></td>
<td></td>
<td></td>
<td>&nbsp;<strong><?php echo cutstr($v, '', ' ');?></strong></td>
<td align="center" class="f_grey">缺失字段</td>
</tr>
<?php } ?>
<?php } ?>
<tr>
<td>&nbsp;</td>
<td colspan="<?php echo $job == 'verify' ? 4 : 3;?>">&nbsp;<input type="submit" name="submit" value="更 新" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="parent.cDialog();"/></td>
</tr>
</table>
</form>
<?php include tpl('footer');?>