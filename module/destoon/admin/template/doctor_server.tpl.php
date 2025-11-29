<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<?php echo dcalendar('date', $date);?>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<?php if($lists) { ?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="192">时间</th>
<th width="106">代码</th>
<th width="40">状态</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><?php echo $v['time'];?></td>
<td><?php echo $v['code'];?></td>
<td><?php echo $v['code'] ? '<img src="'.DT_STATIC.'image/check-'.($v['code'] == 'ok' ? 'ok' : 'ko').'.png" alt="" align="absmiddle"/>' : '';?></td>
<td></td>
</tr>
<?php }?>
</table>
<?php } ?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>