<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="20" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="admin">
<option value="-1" <?php echo $admin == -1 ? 'selected' : '';?>>位置</option>
<option value="1" <?php echo $admin == 1 ? 'selected' : '';?>>后台</option>
<option value="0" <?php echo $admin == 0 ? 'selected' : '';?>>前台</option>
</select>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</form>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th width="150">登录时间</th>
<th>会员名</th>
<th>位置</th>
<th>结果</th>
<th>入口</th>
<th>IP</th>
<th>端口</th>
<th>地区</th>
<th>系统</th>
<th>浏览器</th>
<th data-hide-1200="1" width="200">客户端</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['logintime'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('admin','<?php echo $v['admin'];?>');"><?php echo $v['admin'] ? '<span class="f_red">后台</span>' : '前台';?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',1,0);Dq('kw','<?php echo $v['message'];?>');"><?php echo $v['message'] == '成功' ? '<span class="f_green">'.$v['message'].'</span>' : $v['message'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',6,0);Dq('kw','='+this.innerHTML);"><?php echo $v['type'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['loginip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',5,0);Dq('kw','='+this.innerHTML);"><?php echo $v['loginport'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['loginip'];?>');"><?php echo ip2area($v['loginip'], 2);?></a></td>
<td><?php echo $v['os'];?></td>
<td><?php echo $v['bs'];?></td>
<td data-hide-1200="1" title="<?php echo $v['agent'];?>"><input type="text" value="<?php echo $v['agent'];?>" style="width:180px;" onmouseover="this.select();"/></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除60天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=clear');}"/>
</div>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>