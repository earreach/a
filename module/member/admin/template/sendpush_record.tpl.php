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
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="status">
<option value="0"<?php if($status == 0) echo ' selected';?>>状态</option>
<option value="2"<?php if($status == 2) echo ' selected';?>>失败</option>
<option value="3"<?php if($status == 3) echo ' selected';?>>成功</option>
</select>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="12" placeholder="会员名" title="推送人 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="uuid" value="<?php echo $uuid;?>" size="20" placeholder="设备标识"/>&nbsp;
<input type="text" name="ip" value="<?php echo $ip;?>" size="12" placeholder="IP" title="IP"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);"/></th>
<th width="300">内容</th>
<th data-hide-1200="1" data-hide-1400="1" data-hide-1600="1">标题</th>
<th data-hide-1200="1" data-hide-1400="1" data-hide-1600="1" width="320">链接</th>
<th>会员</th>
<th>设备标识</th>
<th data-hide-1200="1">推送人</th>
<th width="100" data-hide-1200="1">IP</th>
<th width="150">推送时间</th>
<th width="40">状态</th>
<th width="180">返回代码</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td align="left" class="lh20"><?php echo $v['content'];?></td>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1" align="left"><?php echo $v['title'];?></td>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1" title="<?php echo $v['linkurl'];?>"><input type="text" size="40" value="<?php echo $v['linkurl'];?>"/> <a href="<?php echo $v['linkurl'] ? gourl($v['linkurl']) : '###';?>"<?php echo $v['linkurl'] ? ' target="_blank"' : '';?>><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=bind&uuid=<?php echo $v['uuid'];?>', '设备查看');"><?php echo $v['uuid'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['editor'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.title);" title="<?php echo $v['senddate'];?>"><?php echo timetoread($v['sendtime'], 6);?></a></td>
<td><a href="javascript:;" onclick="Dq('status','<?php echo $v['status'];?>');"><?php echo $v['status'] == 3 ? '<span class="f_green">成功</span>' : '<span class="f_red">失败</span>';?></a></td>
<td><textarea style="width:150px;height:24px;"><?php echo $v['code'];?></textarea></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="批量删除" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除90天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<br/>
<?php include tpl('footer');?>