<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$id) show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&id=<?php echo $id;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
报名人数&nbsp;
<input type="text" name="minamount" value="<?php echo $minamount;?>" size="6"/> 至 
<input type="text" name="maxamount" value="<?php echo $maxamount;?>" size="6"/>&nbsp;
<input type="text" name="id" value="<?php echo $id;?>" size="10" placeholder="展会ID" title="展会ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">报名时间 <img src="static/image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if(!$id) { ?>
<th>展会</th>
<th>发布人</th>
<?php } ?>
<th>报名会员</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">人数 <img src="static/image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>姓名</th>
<th>手机</th>
<th width="20"></th>
<th width="20"></th>
<th width="20"></th>
<?php if($DT['im_web']) { ?><th width="20"></th><?php } ?>
<th width="20"></th>
<th width="20"></th>
<th width="20"></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<?php if(!$id) { ?>
<td align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['user'];?>');"><?php echo $v['user'];?></a></td>
<?php } ?>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('minamount',this.innerHTML,0);Dq('maxamount',this.innerHTML);"><?php echo $v['amount'];?></a></td>
<td><?php echo $v['truename'];?></td>
<td><?php echo $v['mobile'];?></td>
<td><?php if($v['mobile']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $v['mobile'];?>', '发送短信');"><img src="static/image/mobile.gif" title="发送短信" alt=""/></a><?php } ?></td>
<td><?php if($v['username']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=message&action=send&touser=<?php echo $v['username'];?>', '发送消息');"><img width="16" height="16" src="static/image/msg.gif" title="发送消息" alt=""/></a><?php } ?></td> 
<td><?php if($v['email']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $v['email'];?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送邮件" alt=""/></a><?php } ?></td>
<?php if($DT['im_web']) { ?><td><?php if($v['username']) { echo im_web($v['username']); } ?></td><?php } ?>
<td><?php if($v['qq']) { echo im_qq($v['qq']); } ?></td>
<td><?php if($v['wx']) { echo im_wx($v['wx'], $v['username']); } ?></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $v['itemid'];?>&id=<?php echo $id;?>"><img src="<?php echo DT_STATIC;?>admin/view.png" width="16" height="16" title="查看" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="批量删除" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？请谨慎!此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>