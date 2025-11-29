<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb ls">
<tr>
<?php foreach($T as $k=>$v) { ?>
<th><?php echo convert($v, 'GBK', 'UTF-8');?></th>
<?php } ?>
</tr>
<?php foreach($D as $vv) { ?>
<tr>
<?php foreach($vv as $v) { ?>
<td><?php echo convert($v, 'GBK', 'UTF-8');?></td>
<?php } ?>
</tr>
<?php } ?>
</table>
<div class="tt">导入数据</div>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="name" value="<?php echo $name;?>"/>
<input type="hidden" name="table" value="<?php echo $table;?>"/>
<input type="hidden" name="action" value="save"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">提示</td>
<td class="f_red">总计<?php echo $t1;?>条数据，以上为前<?php echo $t2;?>条数据预览，请确认无误再导入</td>
</tr>
<tr>
<td class="tl"> </td>
<td><input type="submit" value="确定导入" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取消导入" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=import');"/></td>
</tr>
</table>
</form>
<script type="text/javascript">
function check() {
	return confirm('确定要导入当前数据吗？');
}
</script>
<script type="text/javascript">Menuon(6);</script>
<?php include tpl('footer');?>