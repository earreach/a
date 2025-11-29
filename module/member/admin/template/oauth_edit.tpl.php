<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb"><tr>
<td class="tl">平台</td>
<td><?php echo $OAUTH[$user['site']]['name'];?></td>
</tr>
<tr>
<td class="tl">昵称</td>
<td><?php echo $user['nickname'];?></td>
</tr>
<tr>
<td class="tl">头像</td>
<td><a href="<?php echo $user['avatar'] ? $user['avatar'] : 'api/oauth/avatar.png';?>" target="_blank"><img src="<?php echo $user['avatar'];?>" width="48" onerror="this.src='api/oauth/avatar.png';"/></a></td>
</tr>
<tr>
<td class="tl">主页</td>
<td><a href="<?php echo gourl($user['url']);?>" target="_blank" class="t"><?php echo $user['url'];?></a></td>
</tr>
<tr>
<td class="tl">性别</td>
<td><?php echo $sgender[$user['gender']];?></td>
</tr>
<tr>
<td class="tl">城市</td>
<td><?php echo $user['city'];?></td>
</tr>
<tr>
<td class="tl">省份</td>
<td><?php echo $user['province'];?></td>
</tr>
<tr>
<td class="tl">国家</td>
<td><?php echo $user['country'];?></td>
</tr>
<tr>
<td class="tl">登录次数</td>
<td><?php echo $user['logintimes'];?></td>
</tr>
<tr>
<td class="tl">上次登录</td>
<td><?php echo timetodate($user['logintime'], 6);?></td>
</tr>
<tr>
<td class="tl">首次登录</td>
<td><?php echo timetodate($user['addtime'], 6);?></td>
</tr>
<tr>
<td class="tl">OpenID</td>
<td><?php echo $user['openid'];?></td>
</tr>
<tr>
<td class="tl">UnionID</td>
<td><?php echo $user['unionid'];?></td>
</tr>
<tr>
<td class="tl">会员名 <span class="f_red">*</span></td>
<td><input type="text" size="20" name="name" id="name" value="<?php echo $user['username'];?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('name').value);"/> &nbsp; <span id="dname" class="f_red"></span></td>
</tr>
</table>
<div class="sbt">
<input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="window.parent.cDialog();"/>
</div>
</form>
<br/>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>