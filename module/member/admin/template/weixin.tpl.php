<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="openid" value="<?php echo $openid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $openid ? '' : $fields_select.'&nbsp;';?>
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&openid=<?php echo $openid;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="type">
<option value="">消息类型</option>
<?php
foreach($TYPE as $k=>$v) {
	echo '<option value="'.$k.'" '.($type == $k ? 'selected' : '').'>'.$v.'</option>';
}
?>
</select>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb">
<tr>
<?php if(!$openid) { ?>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="60">头像</th>
<th width="150">昵称</th>
<th width="100">会员名</th>
<?php } ?>
<th width="100">消息类型</th>
<th width="150">发送时间</th>
<th>消息内容</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<?php if(!$openid) { ?>
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['headimgurl'];?>');"><img src="<?php echo $v['headimgurl'];?>" width="48" height="48" class="avatar"/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&openid=<?php echo $v['openid'];?>&action=chat', '与[<?php echo $v['nickname'];?>]交谈', 800, 520);"><?php echo $v['nickname'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>')"><?php echo $v['username'];?></a></td>
<?php } ?>
<td><a href="javascript:;" onclick="Dq('type', '<?php echo $v['type'];?>');"><?php echo $TYPE[$v['type']];?></a></td>
<td class="c_p" title="<?php echo $v['adddate'];?>" onclick="Dq('date', this.title);"><?php echo timetoread($v['adddate'], 6);?></td>
<td align="left"><div style="padding:6px;"><?php echo $v['msg'];?></div></td>
</tr>
<?php }?>
</table>
<?php if(!$openid) { ?>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&openid=<?php echo $openid;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除30天之前的记录')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>&nbsp;
</div>
<?php } ?>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $action == 'event' ? 1 : 0;?>);</script>
<?php include tpl('footer');?>