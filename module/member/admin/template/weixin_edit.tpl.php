<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">微信</td>
<td><?php echo $user['nickname'];?></td>
</tr>
<tr>
<td class="tl">OpenID</td>
<td><?php echo $user['openid'];?></td>
</tr>
<tr>
<td class="tl">头像</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&openid=<?php echo $user['openid'];?>&action=chat', '与[<?php echo $user['nickname'];?>]交谈', 800, 520);"><img src="<?php echo $user['headimgurl'];?>" width="48" onerror="this.src='api/weixin/image/headimg.jpg';"/></a></td>
</tr>
<tr>
<td class="tl">会员 <span class="f_red">*</span></td>
<td><input type="text" size="20" name="name" id="name" value="<?php echo $user['username'];?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('name').value);"/> &nbsp; <span id="dname" class="f_red"></span></td>
</tr>
</table>
<div class="sbt">
<input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=user');"/>
</div>
</form>
<br/>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>