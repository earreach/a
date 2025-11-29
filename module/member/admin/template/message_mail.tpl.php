<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="send" value="1"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 功能说明</td>
<td class="ts">可以通过此功能将会员的未读站内信发送至其注册邮箱</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 时间范围</td>
<td>
<input type="text" size="5" name="hour" id="hour" value="48"/> 小时<?php tips('发送超过此时间未读的站内信 建议设置24小时以上<br/>每封站内信只发送一次，已经发送过的不会重复发送');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 每轮发送</td>
<td><input type="text" size="5" name="pernum" id="pernum" value="5"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 时间间隔</td>
<td><input type="text" size="5" name="pertime" id="pertime" value="3"/><?php tips('例如设置为3，则系统在每轮发送之后暂停3秒，以免因为发送过快而被收件服务器拒收');?></td>
</tr>
<?php if($lasttime) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 上次发送</td>
<td><?php echo $lasttime;?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 未读数量</td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&read=0&send=0&status=3"><?php echo $db->count($DT_PRE.'message', 'isread=0 AND issend=0 AND status=3');?></a></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 开始发送 " class="btn-g" onclick="if(!confirm('确定发送超过 '+Dd('hour').value+' 小时未读的站内信至会员信箱吗？')) return false;"></div>
</form>
<script type="text/javascript">Menuon(5);</script>
<?php include tpl('footer');?>