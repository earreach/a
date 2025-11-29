<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<div class="tt">报名详情</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">展会</td>
<td class="tr"><a href="<?php echo $item['linkurl'];?>" target="_blank" class="t"><?php echo $item['title'];?></a></td>
</tr>
<tr>
<td class="tl">发布人</td>
<td><a href="javascript:;" onclick="_user('<?php echo $item['user'];?>');" class="t"><?php echo $item['user'];?></a></td>
</tr>
<tr>
<td class="tl">报名会员</td>
<td><a href="javascript:;" onclick="_user('<?php echo $item['username'];?>');" class="t"><?php echo $item['username'];?></a></td>
</tr>
<tr>
<td class="tl">人数</td>
<td><?php echo $item['amount'];?></td>
</tr>
<tr>
<td class="tl">公司</td>
<td><?php echo $item['company'];?></td>
</tr>
<tr>
<td class="tl">姓名</td>
<td><?php echo $item['truename'];?></td>
</tr>
<tr>
<td class="tl">手机</td>
<td><?php if($item['mobile']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $item['mobile'];?>', '发送短信');"><img src="static/image/mobile.gif" title="发送短信" alt=""/></a> <?php } ?><?php echo $item['mobile'];?></td>
</tr>
<tr>
<td class="tl">地址</td>
<td><?php echo area_pos($item['areaid'], '');?><?php echo $item['address'];?></td>
</tr>
<?php if($DT['postcode']) { ?>
<tr>
<td class="tl">邮编</td>
<td><?php echo $item['postcode'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">邮件</td>
<td><?php if($item['email']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $item['email'];?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送邮件" alt="" align="absmiddle"/></a> <?php } ?><?php echo $item['email'];?></td>
</tr>
<?php if($DT['im_qq']) { ?>
<tr>
<td class="tl">QQ</td>
<td><?php if($item['qq']) { echo im_qq($item['qq']); } ?> <?php echo $item['qq'];?></td>
</tr>
<?php } ?>
<?php if($DT['im_wx']) { ?>
<tr>
<td class="tl">微信</td>
<td><?php if($item['wx']) { echo im_wx($item['wx'], $item['username']); } ?> <?php echo $item['wx'];?></td>
</tr>
<?php } ?>
<tr>
<td class="tl">报名时间</td>
<td><?php echo $item['addtime'];?></td>
</tr>
<tr>
<td class="tl">备注说明</td>
<td><?php echo nl2br($item['content']);?></td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="button" value="确 定" class="btn-g" onclick="window.history.back(-1);"/></td>
</tr>
</table>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>