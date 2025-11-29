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
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" size="10" name="qid" value="<?php echo $qid;?>" placeholder="问题ID" title="问题ID"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&qid=<?php echo $qid;?>' : ''));"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<label><input type="checkbox" name="guest" value="1"<?php echo $guest ? ' checked' : '';?>/> 游客&nbsp;</label>
<label><input type="checkbox" name="hidden" value="1"<?php echo $hidden ? ' checked' : '';?>/> 匿名&nbsp;</label>
<label><input type="checkbox" name="expert" value="1"<?php echo $expert ? ' checked' : '';?>/> 专家&nbsp;</label>
</td>
</tr>
</table>
</form>
<form method="post">
<div id="content">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="100">会员</th>
<th>答案内容</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td valign="top">
<img src="<?php echo useravatar($v['username']);?>" width="64" height="64" class="avatar c_p" onclick="Dq('username','<?php echo $v['username'];?>');"/>
<div style="line-height:24px;padding-top:6px;">
<?php if($v['username']) { ?>
<a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['passport'];?></a> 
<?php } else { ?>
游客
<?php } ?>
<?php if($v['hidden']) { ?>
<br/><span class="f_gray c_p" onclick="Dq('hidden',1);">匿名</span>
<?php } ?>
<?php if($v['expert']) {?><br/><span class="f_red c_p" onclick="Dq('expert',1);">专家</span><?php } ?>
</div>
</td>
<td align="left" valign="top">
<div class="f_gray">
<span class="f_r c_p">
<span onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '支持记录');">支持 (<?php echo $v['likes'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '反对记录');">反对 (<?php echo $v['hates'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '举报记录');">举报 (<?php echo $v['reports'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=vote&aid=<?php echo $v['itemid'];?>', '投票记录');">票数 (<?php echo $v['vote'];?>)</span> &nbsp;|&nbsp;
<span onclick="Dq('qid',<?php echo $v['qid'];?>);">同问</span> &nbsp;|&nbsp;
<a href="<?php echo $MOD['linkurl'].'goto.php?itemid='.$v['qid'];?>" target="_blank"><span class="f_gray">原文</span></a>
</span>
<span class="c_p" onclick="Dq('date',this.title);" title="<?php echo $v['adddate'];?>"><?php echo timetoread($v['addtime']);?></span> &nbsp; IP:<span class="c_p" onclick="Dq('fields',3,0);Dq('kw',this.innerHTML);"><?php echo $v['ip'];?></span> - <?php echo ip2area($v['ip']);?>
</div>
<div style="padding:16px 0;line-height:200%;font-size:14px;">
<?php echo $v['content'];?>
</div>
<?php if($v['url']) { ?>
<div style="padding-top:16px;color:#999999;">参考资料：<a href="<?php echo gourl($v['url']);?>" target="_blank" class="b"><?php echo $v['url'];?></a></div>
<?php } ?>
</td>
<td valign="top"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt="" class="c_p" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>');"/></td>
</tr>
<?php }?>
</table>
</div>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<?php if($action == 'check') { ?>
<input type="submit" value="通过审核" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=3';"/>&nbsp;
<input type="submit" value="拒 绝" class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=reject';"/>&nbsp;
<?php } else if($action == 'reject') { ?>
<?php } else { ?>
<input type="submit" value="取消审核" class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=2';"/>&nbsp;
<?php } ?>
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中答案吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">var content_id = 'content';var img_max_width = <?php echo $MOD['max_width'];?>;</script>
<?php load('content.js');?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>