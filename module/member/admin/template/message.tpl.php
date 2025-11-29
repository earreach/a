<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="text" name="touser" value="<?php echo $touser;?>" size="10" placeholder="收件人" title="收件人 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="fromuser" value="<?php echo $fromuser;?>" size="10" placeholder="发件人" title="发件人 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<?php echo $status_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="read">
<option value="-1">阅读</option>
<option value="1"<?php echo $read==1 ? ' selected' : '';?>>已读</option>
<option value="0"<?php echo $read==0 ? ' selected' : '';?>>未读</option>
</select>&nbsp;
<select name="send">
<option value="-1">转发</option>
<option value="1"<?php echo $send==1 ? ' selected' : '';?>>已发</option>
<option value="0"<?php echo $send==0 ? ' selected' : '';?>>未发</option>
</select>&nbsp;
<select name="typeid">
<option value="-1">类型</option>
<?php foreach($NAME as $k=>$v) { ?>
<option value="<?php echo $k;?>"<?php echo $k==$typeid ? ' selected' : '';?>><?php echo $v;?></option>
<?php } ?>
</select>&nbsp;
<select name="mid">
<option value="0">模块</option>
<?php foreach($MODULE as $k=>$v) { ?>
<?php if(in_array($v['module'], array('sell', 'buy', 'info', 'brand'))) { ?>
<option value="<?php echo $k;?>"<?php echo $k==$mid ? ' selected' : '';?>><?php echo $v['name'];?></option>
<?php } ?>
<?php } ?>
</select>&nbsp;
<input type="text" name="tid" value="<?php echo $tid;?>" size="10" title="信息ID" placeholder="信息ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="20"></th>
<th width="60">状态</th>
<th>标题</th>
<th width="100">收件人</th>
<th data-hide-1200="1" width="100">收件昵称</th>
<th width="100">发件人</th>
<th data-hide-1200="1" width="100">发件昵称</th>
<th data-hide-1200="1" width="100">发送IP</th>
<th width="150">发送时间</th>
<th width="30">已读</th>
<th width="30" title="邮件转发">转发</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('typeid','<?php echo $v['typeid'];?>');"><img src="<?php echo DT_STATIC;?>member/message_<?php echo $v['typeid'];?>.gif" width="16" height="16" title="<?php echo $NAME[$v['typeid']];?>" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $S[$v['status']];?></a></td>
<td align="left"><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&itemid=<?php echo $v['itemid'];?>" title="<?php echo $v['alt'];?>">&nbsp;<?php echo $v['title'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['touser'];?>');"><?php echo $v['touser'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('touser','<?php echo $v['touser'];?>');"><?php echo $v['tpassport'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['fromuser'];?>');"><?php echo $v['fromuser'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('fromuser','<?php echo $v['fromuser'];?>');"><?php echo $v['fpassport'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');" title="显示IP所在地"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.title);" title="<?php echo timetodate($v['addtime'], 6);?>"><?php echo timetoread($v['addtime'], 6);?></a></td>
<td><a href="javascript:;" onclick="Dq('read','<?php echo $v['isread'];?>');"><?php echo $v['isread'] ? '是' : '否';?></a></td>
<td><a href="javascript:;" onclick="Dq('send','<?php echo $v['issend'];?>');"><?php echo $v['issend'] ? '是' : '否';?></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value=" 批量删除 " class="btn-r" onclick="if(confirm('确定要删除选中信件吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<br/>
<?php include tpl('footer');?>