<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<style>
.td1 {text-align:center;}
.td1 img {width:64px;height:64px;border-radius:50%;}
.td1 div {line-height:32px;font-size:12px;font-weight:bold;}
.td1 div b {color:#D9251D;}
.td1 p {margin:0;line-height:32px;color:#666666;}
</style>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 问题标题</td>
<td><?php echo $title;?></td>
<td width="40"></td>
<td width="40"></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 问题分类</td>
<td><?php echo $TYPE[$typeid]['typename'];?></td>
<td></td>
<td></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理状态</td>
<td><?php echo $dstatus[$status];?></td>
<td></td>
<td></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 提问时间</td>
<td title="<?php echo $adddate;?>"><?php echo timetoread($addtime, 5);?></td>
<td></td>
<td></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 最后更新</td>
<td title="<?php echo $editdate;?>"><?php echo timetoread($edittime, 5);?></td>
<td></td>
<td></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员评分</td>
<td><img src="static/image/star<?php echo $star;?>.gif"/> <?php echo $stars[$star];?></td>
<td></td>
<td></td>
</tr>
<tr>
<td class="tl" valign="top">
<div class="td1">
<a href="javascript:;" onclick="_user('<?php echo $username;?>');">
<img src="<?php echo useravatar($username, 'large');?>"/>
<div><?php echo $username;?></div>
</a>
<p title="<?php echo $adddate;?>"><?php echo timetoread($addtime, 5);?></p>
<span class="f_green">用户提问</span>
</div>
<br/>
</td>
<td valign="top"><?php echo $content;?></td>
<td align="center" valign="top">修改</td>
<td align="center" valign="top">删除</td>
</tr>
<?php if($lists) { ?>
<?php foreach($lists as $k=>$v) { ?>
<tr>
<td class="tl" valign="top">
<div class="td1">
<a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');">
<img src="<?php echo useravatar($v['username'], 'large');?>"/>
<div><?php echo $v['editor'] ? '<b>'.$v['editor'].'</b>' : $v['username'];?></div>
</a>
<p title="<?php echo timetodate($v['addtime'], 5);?>"><?php echo timetoread($v['addtime'], 5);?></p>
<?php echo $v['editor'] ? '<span class="f_red">客服回复</span>' : '<span class="f_green">用户追问</span>';?>
</div>
<br/>
</td>
<td valign="top"><?php echo $v['content'];?></td>
<td align="center" valign="top"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>', '修改内容');"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
<td align="center" valign="top"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&itemid=<?php echo $v['itemid'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php } ?>
<?php } ?>

<?php if($star < 1) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 问题回复</td>
<td><textarea name="content" id="content" class="dsn"></textarea><?php echo deditor($moduleid, 'content', 'Destoon', '100%', 300);?></td>
<td></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 受理状态</td>
<td>
<label><input type="radio" name="status" value="0" id="status_0" onclick="Dh('notice');"<?php echo $status == 0 ? ' checked' : '';?>/> 待受理</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="status" value="1" id="status_1" onclick="Dh('notice');"<?php echo $status == 1 ? ' checked' : '';?>/> 受理中</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="status" value="2" id="status_2" onclick="Ds('notice');"<?php echo $status == 2 ? ' checked' : '';?>/> 已解决</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="status" value="3" id="status_3" onclick="Ds('notice');"<?php echo $status == 3 ? ' checked' : '';?>/> 未解决</label>
</td>
<td></td>
</tr>
<tr style="display:none;" id="notice">
<td class="tl"><span class="f_hid">*</span> 通知会员</td>
<td>
<input type="checkbox" name="msg" id="msg" value="1" onclick="Dn();" checked/><label for="msg"> 站内通知</label>
<input type="checkbox" name="eml" id="eml" value="1" onclick="Dn();"/><label for="eml"> 邮件通知</label>
<input type="checkbox" name="sms" id="sms" value="1" onclick="Dn();"/><label for="sms"> 短信通知</label>
<input type="checkbox" name="wec" id="wec" value="1" onclick="Dn();"/><label for="wec"> 微信通知</label>
</td>
<td></td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" name="submit" value="回 复" class="btn-g">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&status=<?php echo $status;?>');"/></td>
<td></td>
</tr>
<?php } ?>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>