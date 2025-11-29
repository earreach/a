<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="120">功能</th>
<th></th>
</tr>
<?php
foreach($menu as $k=>$v) {
?>
<tr align="center">
<td><a href="<?php echo $v[1];?>"><?php echo $v[0];?></a></td>
<td></td>
</tr>
<?php } ?>
</table>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>