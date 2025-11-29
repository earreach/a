<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('webuploader.min.js');?>
<?php load('player.js');?>
<?php load('url2video.js');?>
<form method="post" action="?file=<?php echo $file;?>" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="post[type]" value=""/>
<input type="hidden" name="post[mid]" value="<?php echo $mid;?>"/>
<input type="hidden" name="post[tid]" value="<?php echo $tid;?>"/>
<input type="hidden" name="post[rid]" value="<?php echo $rid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 留言人</td>
<td><a href="javascript:;" onclick="_user('<?php echo $username;?>');" class="t"><?php echo $username ? $username : 'Guest';?></a> &nbsp; <input type="checkbox" name="post[hidden]" value="1" <?php if($hidden) echo 'checked';?>/> 匿名留言</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> IP</td>
<td><?php echo $ip;?> - <?php echo ip2area($ip);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 留言时间</td>
<td><?php echo $addtime;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 留言类型</td>
<td><?php echo $type;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 留言标题</td>
<td><?php echo $title;?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 留言内容</td>
<td><textarea name="post[content]" id="content"  rows="8" cols="70"><?php echo $content;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 留言图片</td>
<td><?php include template('upload-album', 'chip');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 留言视频</td>
<td><?php include template('upload-video', 'chip');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 联系人</td>
<td><?php echo $truename;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 联系电话</td>
<td><?php echo $telephone;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 电子邮件</td>
<td><?php echo $email;?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> QQ</td>
<td><?php echo $qq ? im_qq($qq).' '.$qq : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 微信</td>
<td><?php echo $wx ? im_wx($wx, $username).' '.$wx : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 阿里旺旺</td>
<td><?php echo $ali ? im_ali($ali).' '.$ali : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Skype</td>
<td><?php echo $skype ? im_skype($skype).' '.$skype : '';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 回复留言</td>
<td>
<textarea name="post[reply]" id="reply" class="dsn"><?php echo $reply;?></textarea>
<?php echo deditor($moduleid, 'reply', 'Destoon', '100%', 350);?><br/><span id="dreply" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 回复时间</td>
<td><?php echo dcalendar('post[edittime]', $edittime, '-', 1);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 通知对方</td>
<td>
<input type="checkbox" name="msg" id="msg" value="1" onclick="Dn();"/><label for="msg"> 站内通知</label>
<input type="checkbox" name="eml" id="eml" value="1" onclick="Dn();"/><label for="eml"> 邮件通知</label>
<input type="checkbox" name="sms" id="sms" value="1" onclick="Dn();"/><label for="sms"> 短信通知</label>
<input type="checkbox" name="wec" id="wec" value="1" onclick="Dn();"/><label for="wec"> 微信通知</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 前台显示</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 否</label>
</td>
</tr>
<?php if($DT['city']) { ?>
<tr style="display:<?php echo $_areaids ? 'none' : '';?>;">
<td class="tl"><span class="f_hid">*</span> 地区(分站)</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>