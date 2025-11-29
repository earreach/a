<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="pollid" value="<?php echo $pollid;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="itemid">
<option value="0">投票选项</option>
<?php
foreach($I as $k=>$v) {
?>
<option value="<?php echo $k;?>" <?php echo $k == $itemid ? ' selected' : '';?>><?php echo $v['title'];?></option>
<?php
}
?>
</select>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&pollid=<?php echo $pollid;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="150">投票时间</th>
<th>会员名</th>
<th>IP</th>
<th>归属地</th>
<th>选项</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['rid'];?>"/></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['polldate'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',2,0);Dq('kw','='+this.innerHTML);"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo ip2area($v['ip'], 2);?></a></td>
<td><a href="javascript:;" onclick="Dq('itemid','<?php echo $v['itemid'];?>');"><?php echo $I[$v['itemid']]['title'];?></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="删 除" class="btn-r" onclick="if(confirm('确定要删除选中投票吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=del'}else{return false;}"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>