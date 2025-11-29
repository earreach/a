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
<td>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="typeid">
<?php
foreach($MANAGE as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $typeid ? ' selected' : '';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<select name="message">
<option value="-1">通知</option>
<option value="1"<?php echo $message==1 ? ' selected' : '';?>>已发</option>
<option value="0"<?php echo $message==0 ? ' selected' : '';?>>未发</option>
</select>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&gid=<?php echo $gid;?>' : ''));"/>
</td>
</tr>
<tr>
<td>
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" size="10" name="gid" value="<?php echo $gid;?>" placeholder="商圈ID" title="商圈ID"/>&nbsp;
<input type="text" size="10" name="tid" value="<?php echo $tid;?>" placeholder="帖子ID" title="帖子ID"/>&nbsp;
<input type="text" size="10" name="rid" value="<?php echo $rid;?>" placeholder="回复ID" title="回复ID"/>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th>商圈</th>
<th>帖子/回复</th>
<th width="40">操作</th>
<th width="80">操作内容</th>
<th>操作人</th>
<th width="130">操作时间</th>
<th width="120">操作原因</th>
<th width="40">通知</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td ondblclick="Dq('gid','<?php echo $v['gid'];?>');"><a href="<?php echo $v['groupurl'];?>" target="_blank"><?php echo $v['groupname'];?></a></td>
<td ondblclick="<?php if($v['tid']) {?>Dq('tid','<?php echo $v['tid'];?>');<?php } else {?>Dq('rid','<?php echo $v['rid'];?>');<?php } ?>" align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>"<?php echo $v['blank'] ? ' target="_blank"' : '';?>><?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><?php echo $MANAGE[$v['typeid']];?></a></td>
<td><?php echo $v['value'];?></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td><textarea style="width:100px;height:16px;" title="<?php echo $v['reason'];?>"><?php echo $v['reason'];?></textarea></td>
<td><a href="javascript:;" onclick="Dq('message','<?php echo $v['message'];?>');"><?php echo $v['message'] ? '<span class="f_green">已发</span>' : '<span class="f_red">未发</span>';?></a></td>
</tr>
<?php }?>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>