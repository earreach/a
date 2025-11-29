<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 授权管理员</td>
<td><input type="text" size="20" name="username" id="username" value="<?php echo $link_user;?>"<?php echo $_founder ? '' : ' disabled';?>/>
&nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">授权对方通过链接直接以此管理员帐号登录后台</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 授权有效期</td>
<td><input type="text" size="6" name="minute" id="minute" value="<?php echo $link_minute;?>"/> 分钟</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">最少10分钟，最多600分钟，超过此时间自动失效退出</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 地区或IP</td>
<td><input type="text" size="20" name="ip" id="ip" value="<?php echo $link_ip;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">建议填写，如果填写城市或者省份，对方IP归属地需匹配，如果填写IP，对方IP需一致</td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" name="submit" value="生成链接" class="btn-g"></td>
</tr>
<?php if($link) {?>
<tr>
<td class="tl"><span class="f_hid">*</span> 授权链接</td>
<td><input type="text" style="width:90%" id="link" value="<?php echo $link;?>"/> <img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#link" onclick="Dtoast('链接已复制');"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">请将以上链接发送给授权对象授权对方登录后台 &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=2&file=loginlog&username=<?php echo $link_user;?>&fromdate=<?php echo timetodate(DT_TIME, 6);?>', '登录记录');" class="b">登录记录</a> &nbsp; <a href="javascript:;" onclick="Dwidget('?file=admin&action=log&username=<?php echo $link_user;?>&fromdate=<?php echo timetodate(DT_TIME, 6);?>', '操作日志');" class="b">操作日志</a></td>
</tr>
<?php } ?>
</table>
</form>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
Menuon(4);
</script>
<?php include tpl('footer');?>