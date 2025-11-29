<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="robot">
<option value="">搜索引擎</option>
<option value="all"<?php echo $robot == 'all' ? ' selected' : '';?>>全部爬虫</option>
<?php
foreach($L['robot'] as $k=>$v) {
?>
<option value="<?php echo $k;?>"<?php echo $robot == $k ? ' selected' : '';?>><?php echo $v;?></option>
<?php
}
?>
</select>&nbsp;
<select name="pc">
<option value="-1">设备</option>
<option value="1"<?php echo $pc == 1 ? ' selected' : '';?>>电脑</option>
<option value="0"<?php echo $pc == 0 ? ' selected' : '';?>>手机</option>
</select>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="islink">
<option value="-1">链接</option>
<option value="1"<?php echo $islink == 1 ? ' selected' : '';?>>外链</option>
<option value="0"<?php echo $islink == 0 ? ' selected' : '';?>>内链</option>
</select>&nbsp;
<?php echo $module_select;?>&nbsp;
<input type="text" name="catid" value="<?php echo $catid;?>" size="5" title="分类ID" placeholder="分类ID"/>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="10" title="信息ID" placeholder="信息ID"/>&nbsp;
<input type="text" name="uk" value="<?php echo $uk;?>" size="8" title="识别号" placeholder="识别号"/>&nbsp;
<input type="text" name="ip" value="<?php echo $ip;?>" size="15" title="IP" placeholder="IP"/>&nbsp;
<?php if($ip) {?>
<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=banip&action=add&ip=<?php echo $ip;?>', '禁止IP - <?php echo $ip;?>');" class="b">禁止此IP</a>
<?php } ?>
</td>
</tr>
</table>
</form>
<table cellspacing="0" class="tb ls">
<tr>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 2 ? 1 : 2;?>');">时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>网址</th>
<th data-hide-1200="1">来源</th>
<th>IP</th>
<th data-hide-1200="1">识别号</th>
<th data-hide-1200="1">端口</th>
<th data-hide-1200="1">归属地</th>
<th>会员名</th>
<th data-hide-1200="1">所属商家</th>
<th width="40">设备</th>
<th width="40">爬虫</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td title="<?php echo $v['url'];?>"><input type="text" size="50" value="<?php echo $v['url'];?>"/> <a href="<?php echo $v['url'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td data-hide-1200="1" title="<?php echo $v['refer'];?>"><input type="text" size="40" value="<?php echo $v['refer'];?>"/> <a href="<?php echo $v['refer'] ? gourl($v['refer']) : '###';?>"<?php echo $v['refer'] ? ' target="_blank"' : '';?>><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td><a href="javascript:;" onclick="Dq('ip','<?php echo $v['ip'];?>');"><?php echo $v['ip'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('uk','<?php echo $v['uk'];?>');"><?php echo $v['uk'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('port','<?php echo $v['port'];?>');"><?php echo $v['port'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_ip('<?php echo $v['ip'];?>');"><?php echo ip2area($v['ip'], 2);?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="_user('<?php echo $v['homepage'];?>');"><?php echo $v['homepage'];?></a></td>
<td><a href="javascript:;" onclick="Dq('pc','<?php echo $v['pc'];?>');"><?php echo $v['pc'] ? '<img src="static/image/ico-pc.png" title="电脑"/>' : '<img src="static/image/ico-mb.png" title="手机"/>';?></a></td>
<td><?php if($v['robot']) { ?><img src="<?php echo DT_STATIC;?>image/robot-<?php echo $v['robot'];?>.gif" title="<?php echo $L['robot'][$v['robot']];?>"/><?php } ?></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除30天之前的记录')){Go('?file=<?php echo $file;?>&action=clear_pv');}"/>&nbsp;
</div>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>