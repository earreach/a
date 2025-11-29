<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<style type="text/css">
.quote{border:1px solid #dcdcdc;background:#FFF;padding:10px;margin-bottom:10px;}
.quote_title {font-size:12px;color:#1B4C7A;}
.quote_time {font-size:11px;color:#666666;}
.quote_floor {float:right;font-size:10px;}
.quote_content {clear:both;}
.b5 {height:5px;}
</style>
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
<?php echo $star_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&itemid=<?php echo $itemid;?>&qid=<?php echo $qid;?>' : ''));"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') { ?> selected<?php } ?>>评论时间</option>
<option value="replytime"<?php if($datetype == 'replytime') { ?> selected<?php } ?>>回复时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="reply">
<option value="0"<?php if($reply == 0) { ?> selected<?php } ?>>回复类型</option>
<option value="1"<?php if($reply == 1) { ?> selected<?php } ?>>全部回复</option>
<option value="2"<?php if($reply == 2) { ?> selected<?php } ?>>作者回复</option>
<option value="3"<?php if($reply == 3) { ?> selected<?php } ?>>网站回复</option>
</select>&nbsp;
<?php echo $module_select;?>&nbsp;
<input type="text" size="10" name="itemid" value="<?php echo $itemid;?>" placeholder="原文ID" title="原文ID"/>&nbsp;
<input type="text" size="10" name="qid" value="<?php echo $qid;?>" placeholder="引用ID" title="引用ID"/>&nbsp;
<label><input type="checkbox" name="hide" value="1"<?php if($hide) { ?> checked<?php } ?>/> 匿名</label>&nbsp;
<label><input type="checkbox" name="guest" value="1"<?php if($guest) { ?> checked<?php } ?>/> 游客</label>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="16"></th>
<th width="100">会员</th>
<th>评论内容</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td valign="top">
<img src="<?php echo useravatar($v['username']);?>" width="64" height="64" class="avatar c_p" onclick="_user('<?php echo $v['username'];?>');"/>
<div style="line-height:24px;padding-top:6px;">
<?php if($v['username']) { ?>
<a href="javascript:Dq('username','<?php echo $v['username'];?>');"><?php echo $v['passport'] ? $v['passport'] : $v['username'];?></a> 
<?php } else { ?>
<span class="f_gray c_p" onclick="Dq('guest',1);">游客</span>
<?php } ?>
<?php if($v['hidden']) { ?>
<br/><span class="f_gray c_p" onclick="Dq('hide',1);">匿名</span>
<?php } ?>
</div>
</td>
<td align="left" valign="top">
<div class="f_gray">
<span class="f_r c_p">
<span onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '支持记录');">支持 (<?php echo $v['likes'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '反对记录');">反对 (<?php echo $v['hates'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?moduleid=3&file=<?php echo $file;?>&qid=<?php echo $v['itemid'];?>', '引用记录');">引用 (<?php echo $v['quotes'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&rid=<?php echo $v['itemid'];?>', '举报记录');">举报 (<?php echo $v['reports'];?>)</span> &nbsp;|&nbsp; 
<span onclick="Dq('mid',<?php echo $v['item_mid'];?>,0);Dq('itemid',<?php echo $v['item_id'];?>);">同文</span> &nbsp;|&nbsp;
<a href="javascript:;" onclick="Dq('star','<?php echo $v['star'];?>');"><img src="<?php echo DT_STATIC;?>image/star<?php echo $v['star'];?>.gif" align="absmiddle" title="<?php echo $sstar[$v['star']];?>"/></a>&nbsp;
</span>

<?php if($v['level'] == 2) { ?>
<span class="fb_red c_p" onclick="Dq('level',2);" title="<?php echo $v['replyer'];?>">置顶</span>
<?php } else if($v['level'] == 1) { ?>
<span class="fb_orange c_p" onclick="Dq('level',1);" title="<?php echo $v['replyer'];?>">精选</span>
<?php } ?>

<span class="c_p" onclick="Dq('date',this.title);" title="<?php echo $v['adddate'];?>"><?php echo timetoread($v['addtime']);?></span> &nbsp; IP:<span class="c_p" onclick="Dq('fields',10,0);Dq('kw','='+this.innerHTML);"><?php echo $v['ip'];?></span> - <span class="c_p" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo ip2area($v['ip']);?></span>
</div>
<div style="padding:16px 0;line-height:200%;font-size:14px;">
<?php echo $v['quotation'] ? $v['quotation'] : $v['content'];?>
<?php if($v['reply']) { ?>
<div style="font-size:12px;">
<?php if($v['replyer']==$v['item_username']) { ?><span class="fb_green c_p" onclick="Dq('reply',2);" title="<?php echo $v['replyer'];?>">作者</span><?php } else { ?><span class="fb_orange c_p" onclick="Dq('reply',3);" title="<?php echo $v['replyer'];?>">网站</span><?php } ?> 
<span class="c_p" title="<?php echo $v['replydate'];?>" onclick="Dq('datetype','replytime',0);Dq('date',this.title);"><?php echo timetoread($v['replytime'], 5);?></span> 
<span class="c_p" onclick="Dq('reply',1);">回复</span></div>
<?php echo nl2br($v['reply']);?>
<?php } ?>
</div>
<div><a href="<?php echo $EXT['comment_url'].rewrite('index'.DT_EXT.'?mid='.$v['item_mid'].'&itemid='.$v['item_id']);?>" target="_blank" class="t"><?php echo $v['item_title'];?></a></div>
</td>
<td valign="top"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<?php if($action == 'check') { ?>
<input type="submit" value=" 通过审核 " class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=3';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=recycle';"/>&nbsp;
<?php } else if($action == 'recycle') { ?>

<input type="submit" value="还 原" class="btn" onclick="if(confirm('确定要还原选中<?php echo $MOD['name'];?>吗？状态将被设置为已通过')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=restore'}else{return false;}"/>&nbsp;
<input type="submit" value="清 空" class="btn-r" onclick="if(confirm('确定要清空回收站吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear';}else{return false;}"/>&nbsp;
<?php } else { ?>
<input type="submit" value=" 取消审核 " class="btn-r" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=check&status=2';"/>&nbsp;
<input type="submit" value="回收站" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=recycle';"/>&nbsp;
<?php } ?>
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中评论吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>