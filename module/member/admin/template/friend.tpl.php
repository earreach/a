<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="归属会员名" title="归属会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="fusername" value="<?php echo $fusername;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="48">头像</th>
<th>会员</th>
<th>昵称</th>
<th data-hide-1200="1">别名</th>
<th data-hide-1200="1">姓名</th>
<th data-hide-1200="1" data-hide-1400="1">公司</th>
<th colspan="8">联系方式</th>
<th>归属会员</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">添加时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><img src="<?php echo useravatar($v['fusername']);?>" width="48" height="48" class="avatar c_p" onclick="_user('<?php echo $v['fusername'];?>');"/></td>
<td ondblclick="Dq('fusername','<?php echo $v['fusername'];?>');"><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['fusername'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fusername','<?php echo $v['fusername'];?>');"><?php echo $v['fpassport'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);"><?php echo $v['alias'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['truename'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1" align="left">&nbsp;<a href="javascript:;" onclick="Dq('fields',5,0);Dq('kw','='+this.innerHTML);"><?php echo $v['company'];?></a></td>
<td width="20"><?php if($v['homepage']) { ?><a href="<?php echo gourl($v['homepage']);?>" target="_blank"><img width="16" height="16" src="static/image/homepage.gif" title="公司主页" alt=""/></a><?php } else { ?>&nbsp;<?php } ?></td>
<td width="20"><?php if($v['mobile']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $v['mobile'];?>', '发送短信');"><img src="static/image/mobile.gif" title="发送短信" alt=""/></a><?php } else { ?>&nbsp;<?php } ?></td>
<td width="20"><?php if($v['username']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=message&action=send&touser=<?php echo $v['username'];?>', '发送消息');"><img width="16" height="16" src="static/image/msg.gif" title="发送消息" alt=""/></a><?php } else { ?>&nbsp;<?php } ?></td> 
<td width="20"><?php if($v['email']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $v['email'];?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送邮件" alt=""/></a><?php } else { ?>&nbsp;<?php } ?></td>
<td width="20"><?php if($v['qq']) { echo im_qq($v['qq']); } else { echo '&nbsp;'; } ?></td>
<td width="20"><?php if($v['wx']) { echo im_wx($v['wx'], $v['username']); } else { echo '&nbsp;'; } ?></td>
<td width="20"><?php if($v['ali']) { echo im_ali($v['ali']); } else { echo '&nbsp;'; } ?></td>
<td width="20"><?php if($v['skype']) { echo im_skype($v['skype']); } else { echo '&nbsp;'; } ?></td>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="批量删除" class="btn-r" onclick="if(confirm('确定要删除选中好友吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>