<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('club.css');
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
<?php echo $level_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&tid=<?php echo $tid;?>&gid=<?php echo $gid;?>' : ''));"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" size="10" name="gid" value="<?php echo $gid;?>" placeholder="商圈ID" title="商圈ID"/>&nbsp;
<input type="text" size="10" name="tid" value="<?php echo $tid;?>" placeholder="帖子ID" title="帖子ID"/>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<label><input type="checkbox" name="guest" value="1"<?php echo $guest ? ' checked' : '';?>/> 游客&nbsp;</label>
</td>
</tr>
</table>
</form>
<form method="post">
<div id="content">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="16"></th>
<th width="100">会员</th>
<th>回复内容</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td valign="top">
<img src="<?php echo useravatar($v['username']);?>" width="64" height="64" class="avatar c_p" onclick="Dq('username','<?php echo $v['username'];?>');"/>
<div style="line-height:24px;padding-top:6px;">
<?php if($v['username']) { ?>
<a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['passport'];?></a> 
<?php } else { ?>
游客
<?php } ?>
</div>
</td>
<td align="left"  valign="top">
<div class="f_gray">
<span class="f_r c_p">
<span onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '支持记录');">支持 (<?php echo $v['likes'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '反对记录');">反对 (<?php echo $v['hates'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '举报记录');">举报 (<?php echo $v['reports'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&rid=<?php echo $v['itemid'];?>', '回复记录');">回复 (<?php echo $v['reply'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dq('tid',<?php echo $v['tid'];?>);">同贴</span> &nbsp;|&nbsp;
<a href="<?php echo $MOD['linkurl'];?>goto<?php echo DT_EXT;?>?itemid=<?php echo $v['itemid'];?>" target="_blank"><span class="f_gray">原贴</span></a>
</span>
<span class="c_p" onclick="Dq('date',this.title);" title="<?php echo $v['adddate'];?>"><?php echo timetoread($v['addtime']);?></span> &nbsp; IP:<span class="c_p" onclick="Dq('fields',4,0);Dq('kw',this.innerHTML);"><?php echo $v['ip'];?></span> - <?php echo ip2area($v['ip']);?>
</div>
<div style="padding:16px 0;line-height:200%;font-size:14px;">
<?php echo $v['content'];?>
</div>
</td>
<td valign="top"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt="" class="c_p" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>');"/></td>
</tr>
<?php }?>
</table>
</div>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<?php if($action == 'check') { ?>

<input type="submit" value="通过审核" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check';"/>&nbsp;
<input type="submit" value="拒 绝" class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>

<?php } else if($action == 'reject') { ?>

<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>

<?php } else if($action == 'recycle') { ?>

<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="还 原" class="btn" onclick="if(confirm('确定要还原选中<?php echo $MOD['name'];?>吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value="清 空" class="btn-r" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>

<?php } else { ?>

<input type="submit" value="取消审核" class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=cancel';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&recycle=1';"/>&nbsp;
<input type="submit" value="彻底删除" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>

<?php } ?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">
var content_id = 'content';
var img_max_width = <?php echo $MOD['max_width'];?>;
</script>
<?php load('content.js');?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>