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
<td>
&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);Dcac();"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">表名 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>注释</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">大小(M) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">记录数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="180">操作</th>
</tr>
<?php foreach($dtables as $k=>$v) {?>
<tr align="center">
<td>
<input type="checkbox" name="tables[]" value="<?php echo $v['name'];?>" onclick="Dcac();" checked/>
<input type="hidden" name="sizes[<?php echo $v['name'];?>]" value="<?php echo $v['tsize'];?>"/>
</td>
<td align="left">&nbsp;<a href="javascript:;" onclick="Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');" class="f_fd"><?php echo $v['name'];?></a></td>
<td><a href="javascript:Dcomment('<?php echo $v['name'];?>', '<?php echo urlencode($v['note']);?>');" title="点击修改注释"><?php echo $v['note'] ? $v['note'] : '--';?></a></td>
<td title="数据:<?php echo $v['size'];?> 索引:<?php echo $v['index'];?> 碎片:<?php echo $v['chip'];?> 点击导出"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=data&table=<?php echo $v['name'];?>', '导出表 - <?php echo $v['name'];?>');"><?php echo $v['tsize'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=execute&table=<?php echo $v['name'];?>', '预览表 - <?php echo $v['name'];?>');"><?php echo $v['rows'];?></a></td>
<td><a href="javascript:Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');">字典</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=repair&tables=<?php echo $v['name'];?>">修复</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=optimize&tables=<?php echo $v['name'];?>">优化</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=data&table=<?php echo $v['name'];?>', '导出表 - <?php echo $v['name'];?>');">导出</a></td>
</tr>
<?php }?>
<?php if($tables) {?>
<?php foreach($tables as $k=>$v) {?>
<tr align="center">
<td>
<input type="checkbox" name="tables[]" value="<?php echo $v['name'];?>" onclick="Dcac();"/>
<input type="hidden" name="sizes[<?php echo $v['name'];?>]" value="<?php echo $v['tsize'];?>"/>
</td>
<td align="left">&nbsp;<a href="javascript:;" onclick="Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');" class="f_fd"><?php echo $v['name'];?></a></td>
<td><a href="javascript:Dcomment('<?php echo $v['name'];?>', '<?php echo urlencode($v['note']);?>');" title="点击修改注释"><?php echo $v['note'] ? $v['note'] : '--';?></a></td>
<td title="数据:<?php echo $v['size'];?> 索引:<?php echo $v['index'];?> 碎片:<?php echo $v['chip'];?> 点击导出"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=data&table=<?php echo $v['name'];?>', '导出表 - <?php echo $v['name'];?>');"><?php echo $v['tsize'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=execute&table=<?php echo $v['name'];?>', '预览表 - <?php echo $v['name'];?>');"><?php echo $v['rows'];?></a></td>
<td><a href="javascript:;" onclick="Dict('<?php echo $v['name'];?>','<?php echo $v['note'];?>');">字典</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=repair&tables=<?php echo $v['name'];?>">修复</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="?file=<?php echo $file;?>&action=optimize&tables=<?php echo $v['name'];?>">优化</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=data&table=<?php echo $v['name'];?>', '导出表 - <?php echo $v['name'];?>');">导出</a></td>
</tr>
<?php }?>
<?php } ?>
</table>
<div class="tt"><span class="f_r">共<span id="dtotal"><?php echo count($dtables);?></span>个表 / <span id="dsize"><?php echo $dtotalsize;?></span>M</span>备份选中</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">快速选表</td>
<td>
<a href="javascript:" onclick="checkall(Dd('dform'), 1);Dcac();" class="t">反选</a> &nbsp;&nbsp;
<a href="javascript:" onclick="checkall(Dd('dform'), 2);Dcac();" class="t">全选</a> &nbsp;&nbsp;
<a href="javascript:" onclick="checkall(Dd('dform'), 3);Dcac();" class="t">全不选</a> &nbsp;&nbsp;
</tr>
<tr>
<td class="tl">分卷大小</td>
<td><input type="text" name="sizelimit" value="2048" size="5"/> K &nbsp; <span class="f_gray">1024K = 1M</span></td>
</tr>
<tr>
<td class="tl">语句格式</td>
<td><label><input type="radio" name="sqlcompat" value="" checked="checked"/> 自动</label> &nbsp; <label><input type="radio" name="sqlcompat" value="MYSQL41"/> MySQL 4.1+/5.x+</label> &nbsp; <label><input type="radio" name="sqlcompat" value="MYSQL40"/> MySQL 3.x/4.0</label> &nbsp;</td>
</tr>
<tr>
<td class="tl">字符编码</td>
<td><label><input type="radio" name="sqlcharset" value="" checked/> 自动</label> &nbsp; <label><input type="radio" name="sqlcharset" value="utf8"/> UTF-8</label> &nbsp; <label><input type="radio" name="sqlcharset" value="gbk"/> GBK</label> &nbsp; <label><input type="radio" name="sqlcharset" value="latin1"/> LATIN1</label></td>
</tr>
<tr>
<td class="tl">备注信息</td>
<td>&nbsp;<input type="text" size="68" name="note" id="note" value="" placeholder="本次备份相关的备注事项"/></td>
</tr>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);Dcac();"/></label>
<input type="submit" name="submit" value="开始备份" class="btn-g" onclick="this.form.action='?file=<?php echo $file;?>&action=&backup=1';"/> &nbsp;
<input type="submit" value="导出结构" class="btn" onclick="this.form.action='?file=<?php echo $file;?>&action=structure';"/> &nbsp;
<input type="submit" value="重建注释" class="btn" onclick="if(confirm('确定要重建注释吗？')){this.form.action='?file=<?php echo $file;?>&action=comments';}else{return false;}"/> &nbsp;
<input type="submit" value="删除表" class="btn-r" onclick="if(confirm('警告！确定要删除中表吗？此操作将不可恢复\n\n为了系统安全，系统仅删除非DESTOON系统表')){this.form.action='?file=<?php echo $file;?>&action=drop';}else{return false;}"/> &nbsp;
<input type="submit" value="修复表" class="btn" onclick="if(confirm('确定要修复选中表吗？')){this.form.action='?file=<?php echo $file;?>&action=repair';}else{return false;}"/> &nbsp;
<input type="submit" value="优化表" class="btn" onclick="if(confirm('确定要优化选中标吗？')){this.form.action='?file=<?php echo $file;?>&action=optimize';}else{return false;}"/> &nbsp;
</div>
</form>
<script type="text/javascript">
function Dict(t, n) {
	Dwidget('?file=<?php echo $file;?>&action=dict&table='+t+'&note='+n, '数据字典 - '+t+' - '+n);
}
function Dcomment(t, n) {
	Dwidget('?file=<?php echo $file;?>&action=comment&table='+t+'&note='+n, '修改注释');
}
function Dcac() {
	var s = 0;
	var t = 0;
	$(':checkbox').each(function() {
		if($(this).attr('checked') && $(this).attr('name')) {
			s += parseFloat($(this).parent().siblings('td:eq(2)').html());
			t++;
		}
	});
	$('#dtotal').html(t);
	$('#dsize').html(s.toFixed(2));
}
Menuon(0);
</script>
<?php include tpl('footer');?>