<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menusad);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<input type="hidden" name="pid" value="<?php echo $pid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="40" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>&pid=<?php echo $pid ? $pid : 0;?>&aid=<?php echo $aid ? $aid : 0;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="pc">
<option value="-1">设备</option>
<option value="1"<?php echo $pc == 1 ? ' selected' : '';?>>电脑</option>
<option value="0"<?php echo $pc == 0 ? ' selected' : '';?>>手机</option>
</select>&nbsp;
<input type="text" name="pid" value="<?php echo $pid;?>" size="10" class="t_c" title="广告位ID" placeholder="广告位ID"/>&nbsp;
<input type="text" name="aid" value="<?php echo $aid;?>" size="10" class="t_c" title="广告ID" placeholder="广告ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<table cellspacing="0" class="tb ls">
<tr>
<th width="80">广告位ID</th>
<th width="80">广告ID</th>
<th width="150">时间</th>
<th>会员名</th>
<th>IP</th>
<th>归属地</th>
<th>操作系统</th>
<th>浏览器</th>
<th data-hide-1200="1" width="250">客户端</th>
<th width="40">设备</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('pid',this.innerHTML);"><?php echo $v['pid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('aid',this.innerHTML);"><?php echo $v['aid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',2,0);Dq('kw','='+this.innerHTML);"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo ip2area($v['ip'], 2);?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['os'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',5,0);Dq('kw','='+this.innerHTML);"><?php echo $v['bs'];?></a></td>
<td data-hide-1200="1"><input type="text" size="30" value="<?php echo $v['ua'];?>" title="<?php echo $v['ua'];?>" id="copy-<?php echo $v['itemid'];?>"/><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#copy-<?php echo $v['itemid'];?>" onclick="Dtoast('客户端已复制');"/></td>
<td><a href="javascript:;" onclick="Dq('pc','<?php echo $v['pc'];?>');"><?php echo $v['pc'] ? '<img src="static/image/ico-pc.png" title="电脑"/>' : '<img src="static/image/ico-mb.png" title="手机"/>';?></a></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
Menuon(3);
</script>
<?php include tpl('footer');?>