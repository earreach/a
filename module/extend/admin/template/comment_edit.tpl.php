<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 会员</td>
<td><a href="javascript:;" onclick="_user('<?php echo $username;?>');" class="t"><?php echo $username ? $passport : 'Guest';?></a> <input type="checkbox" name="post[hidden]" value="1" <?php if($hidden) echo 'checked';?>/> 匿名评论</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 时间</td>
<td><?php echo $adddate;?> - <?php echo timetoread($addtime);?> </td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> IP</td>
<td><?php echo $ip;?> - <?php echo ip2area($ip);?> </td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 原文</td>
<td><a href="<?php echo $url;?>" target="_blank" class="t"><?php echo $item_title;?></a></td>
</tr>
<?php if($item_username) {?>
<tr>
<td class="tl"><span class="f_hid">*</span> 作者</td>
<td><a href="javascript:;" onclick="_user('<?php echo $item_username;?>');" class="t"><?php echo $item_username;?></a></td>
</tr>
<?php } ?>
<?php if($replyer) {?>
<tr>
<td class="tl"><span class="f_hid">*</span> 回复</td>
<td><a href="javascript:;" onclick="_user('<?php echo $replyer;?>');" class="t"><?php echo $replyer;?></a></td>
</tr>
<?php } ?>
<?php if($editor) {?>
<tr>
<td class="tl"><span class="f_hid">*</span> 编辑</td>
<td><a href="javascript:;" onclick="_user('<?php echo $editor;?>');" class="t"><?php echo $editor;?></a></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 引用</td>
<td><textarea name="post[quotation]" id="quotation"  rows="8" cols="70"><?php echo $quotation;?></textarea><br/>请不要修改代码结构，仅可修改文字内容</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 评分</td>
<td>
<label><input type="radio" name="post[star]" value="5" id="star_5"<?php echo $star == 5 ? ' checked' : '';?>/> <img src="<?php echo DT_STATIC;?>image/star5.gif" width="60" height="12" alt="" align="absmiddle"/></label>
<label><input type="radio" name="post[star]" value="4" id="star_4"<?php echo $star == 4 ? ' checked' : '';?>/> <img src="<?php echo DT_STATIC;?>image/star4.gif" width="60" height="12" alt="" align="absmiddle"/></label>
<label><input type="radio" name="post[star]" value="3" id="star_3"<?php echo $star == 3 ? ' checked' : '';?>/> <img src="<?php echo DT_STATIC;?>image/star3.gif" width="60" height="12" alt="" align="absmiddle"/></label>
<label><input type="radio" name="post[star]" value="2" id="star_2"<?php echo $star == 2 ? ' checked' : '';?>/> <img src="<?php echo DT_STATIC;?>image/star2.gif" width="60" height="12" alt="" align="absmiddle"/></label>
<label><input type="radio" name="post[star]" value="1" id="star_1"<?php echo $star == 1 ? ' checked' : '';?>/> <img src="<?php echo DT_STATIC;?>image/star1.gif" width="60" height="12" alt="" align="absmiddle"/></label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 内容</td>
<td><textarea name="post[content]" id="content"  rows="8" cols="70"><?php echo $content;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 级别</td>
<td><?php echo level_select('post[level]', '级别', $level);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 回复</td>
<td><textarea name="post[reply]" id="reply" rows="8" cols="70"><?php echo $reply;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 时间</td>
<td><?php echo dcalendar('post[replytime]', $replydate, '-', 1);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 通过&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 待审
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?><?php echo $status == 2 ? '&action=check' : '';?>');"/></div>
</form>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>