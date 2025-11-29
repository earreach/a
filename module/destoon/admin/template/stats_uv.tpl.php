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
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
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
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/></td>
</tr>
<tr>
<td>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="pc">
<option value="-1">设备</option>
<option value="1"<?php echo $pc == 1 ? ' selected' : '';?>>电脑</option>
<option value="0"<?php echo $pc == 0 ? ' selected' : '';?>>手机</option>
</select>&nbsp;
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
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 2 ? 1 : 2;?>');">入站时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>IP</th>
<th data-hide-1200="1">识别号</th>
<th data-hide-1200="1">端口</th>
<th>地区</th>
<th>网络</th>
<th>操作系统</th>
<th>浏览器</th>
<th>分辨率</th>
<th>品牌</th>
<th data-hide-1200="1" width="248">客户端</th>
<th width="40">设备</th>
<th width="40">爬虫</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td title="归属地：<?php echo $v['area'];?>" ondblclick="Dq('ip','<?php echo $v['ip'];?>');"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=pv&ip=<?php echo $v['ip'];?>', '<?php echo $v['ip'];?> - PV记录');"><?php echo $v['ip'];?></a></td>
<td data-hide-1200="1" ondblclick="Dq('uk','<?php echo $v['uk'];?>');"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=pv&ip=<?php echo $v['ip'];?>&uk=<?php echo $v['uk'];?>', '<?php echo $v['uk'];?>@<?php echo $v['ip'];?> - PV记录');"><?php echo $v['uk'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('port',this.innerHTML);"><?php echo $v['port'];?></a></td>
<td><?php echo $v['location'];?></td>
<td><a href="javascript:;" onclick="Dq('network','<?php echo $v['network'];?>');"><?php echo $v['network'];?></td>
<td><a href="javascript:;" onclick="Dq('os','<?php echo $v['os'];?>');"><?php echo $v['os'];?></td>
<td><a href="javascript:;" onclick="Dq('bs','<?php echo $v['bs'];?>');"><?php echo $v['bs'];?></td>
<td><a href="javascript:;" onclick="Dq('screen','<?php echo $v['screen'];?>');"><?php echo $v['screen'];?></td>
<td><a href="javascript:;" onclick="Dq('bd','<?php echo $v['bd'];?>');"><?php echo $v['bd'];?></td>
<td data-hide-1200="1"><input type="text" size="30" value="<?php echo $v['ua'];?>" title="<?php echo $v['ua'];?>" id="copy-<?php echo $v['itemid'];?>"/><img src="<?php echo DT_STATIC;?>image/ico-copy.png" class="cp" title="复制" data-clipboard-action="copy" data-clipboard-target="#copy-<?php echo $v['itemid'];?>" onclick="Dtoast('客户端已复制');"/></td>
<td><a href="javascript:;" onclick="Dq('pc','<?php echo $v['pc'];?>');"><?php echo $v['pc'] ? '<img src="static/image/ico-pc.png" title="电脑"/>' : '<img src="static/image/ico-mb.png" title="手机"/>';?></a></td>
<td><?php if($v['robot']) { ?><img src="<?php echo DT_STATIC;?>image/robot-<?php echo $v['robot'];?>.gif" title="<?php echo $L['robot'][$v['robot']];?>"/><?php } ?></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除1年之前的记录')){Go('?file=<?php echo $file;?>&action=clear_uv');}"/>&nbsp;
</div>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
Menuon(1);
</script>
<?php include tpl('footer');?>