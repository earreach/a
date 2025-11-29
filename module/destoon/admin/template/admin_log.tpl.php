<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" size="10" name="username" value="<?php echo $username;?>" placeholder="管理员" title="管理员"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th>时间</th>
<th>URL</th>
<th>模块</th>
<th>文件</th>
<th>操作</th>
<th>ID</th>
<th data-hide-1200="1">IP</th>
<th data-hide-1200="1">端口</th>
<th>归属地</th>
<th>管理员</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['logdate'];?></a></td>
<td title="<?php echo $v['qstring'];?>"><input type="text" size="60" value="<?php echo $v['qstring'];?>"/> <a href="?<?php echo $v['qstring'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td><a href="javascript:;" onclick="Dq('kw','moduleid=<?php echo $v['mid'];?>');"><?php echo $v['module_name'];?></a></td>
<td><a href="javascript:;" onclick="Dq('kw','file=<?php echo $v['file'];?>');"><?php echo $v['file_name'];?></a></td>
<td><a href="javascript:;" onclick="Dq('kw','action=<?php echo $v['action'];?>');"><?php echo $v['action_name'];?></a></td>
<td><a href="<?php echo gourl('?mid='.$v['mid'].'&itemid='.$v['itemid']);?>" target="_blank"><?php echo $v['itemid'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('fields',3,1);Dq('kw','='+this.innerHTML);"><?php echo $v['ip'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('fields',4,1);Dq('kw','='+this.innerHTML);"><?php echo $v['port'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo ip2area($v['ip'], 2);?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="日志清理" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除30天之前的日志')){Go('?file=<?php echo $file;?>&action=clear');}"/>&nbsp;
</div>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>