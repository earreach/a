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
<?php echo $fields_select;?>&nbsp;
<input type="text" size="20" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="site">
<option value="">平台接口</option>
<?php
foreach($OAUTH as $k=>$v) {
	echo '<option value="'.$k.'" '.($site == $k ? 'selected' : '').'>'.$v['name'].'</option>';
}
?>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th width="60">平台</th>
<th width="150">登录时间</th>
<th>会员名</th>
<th>IP</th>
<th>端口</th>
<th>地区</th>
<th>系统</th>
<th>浏览器</th>
<th width="200">客户端</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td title="<?php echo $OAUTH[$v['site']]['name'];?>"><a href="javascript:;" onclick="Dq('site','<?php echo $v['site'];?>');"><img src="api/oauth/<?php echo $v['site'];?>/ico.png"/></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['logintime'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);"><?php echo $v['loginip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['loginport'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['loginip'];?>');"><?php echo ip2area($v['loginip'], 2);?></a></td>
<td><?php echo $v['agent'] ? get_os($v['agent']) : '';?></td>
<td><?php echo $v['agent'] ? get_bs($v['agent']) : '';?></td>
<td title="<?php echo $v['agent'];?>"><input type="text" value="<?php echo $v['agent'];?>" style="width:180px;" onmouseover="this.select();"/></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除60天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>