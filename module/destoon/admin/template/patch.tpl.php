<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">备份模式</td>
<td>&nbsp;
<label><input type="radio" name="type" value="0" onclick="$('#type-0').show();$('#type-1').hide();" id="tp-0" checked/> 指定目录</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="type" value="1" onclick="$('#type-1').show();$('#type-0').hide();" id="tp-1"/> 指定文件</label>
</td>
</tr>
<tbody id="type-0">
<tr>
<td class="tl">选择目录</td>
<td>
<table cellspacing="0" width="750" class="ctb">
<?php foreach($dirs as $k=>$d) { ?>
<?php if($k%5==0) {?><tr><?php } ?>
<td width="150"><label class="f_fd"><input type="checkbox" name="filedir[]" value="<?php echo $d;?>"<?php echo in_array($d, $ds) ? ' checked' : '';?>/>&nbsp;<img src="file/ext/folder.gif" alt="" align="absmiddle"/> <?php echo $d;?></label></td>
<?php if($k%5==4) {?></tr><?php } ?>
<?php } ?>
</table>
<div style="padding:6px 12px;">
<a href="javascript:" onclick="checkall(Dd('dform'), 1);" class="t">反选</a>&nbsp;&nbsp;
<a href="javascript:" onclick="checkall(Dd('dform'), 2);" class="t">全选</a>&nbsp;&nbsp;
<a href="javascript:" onclick="checkall(Dd('dform'), 3);" class="t">全不选</a>&nbsp;&nbsp;
</div>
</td>
</tr>
<tr>
<td class="tl">文件类型</td>
<td>&nbsp;<input type="text" size="68" name="fileext" value="<?php echo $ext;?>" class="f_fd"/></td>
</tr>
<tr>
<td class="tl">修改时间</td>
<td>
&nbsp;<?php echo dcalendar('fd', $fd, '-', 1);?>
&nbsp; 至 
&nbsp;<?php echo dcalendar('td', $td, '-', 1);?>&nbsp;
&nbsp;&nbsp;<a href="javascript:;" onclick="Dd('fd').value='<?php echo $date1;?> 00:00:00';Dd('td').value='<?php echo $td;?>';" class="b">今日</a>
&nbsp;&nbsp;<a href="javascript:;" onclick="Dd('fd').value='<?php echo $date2;?> 00:00:00';Dd('td').value='<?php echo $date2;?> 23:59:59';" class="b">昨日</a>
&nbsp;&nbsp;<a href="javascript:;" onclick="Dd('fd').value='<?php echo $date3;?> 00:00:00';Dd('td').value='<?php echo $td;?>';" class="b">三天</a>
&nbsp;&nbsp;<a href="javascript:;" onclick="Dd('fd').value='<?php echo $date4;?> 00:00:00';Dd('td').value='<?php echo $td;?>';" class="b">本周</a>
&nbsp;&nbsp;<a href="javascript:;" onclick="Dd('fd').value='<?php echo $date5;?> 00:00:00';Dd('td').value='<?php echo $td;?>';" class="b">本月</a>
</td>
</tr>
</tbody>
<tbody id="type-1" style="display:none;">
<tr>
<td class="tl">文件列表</td>
<td>&nbsp;
<textarea name="files" id="files" style="width:500px;height:480px;overflow:visible;" class="f_fd"></textarea>
<div class="f_gray" style="line-height:32px;">
&nbsp;&nbsp;一行一个站点下的文件名或目录名，例如 static/script/common.js 开头不要加 / 或磁盘路径<br/>
&nbsp;&nbsp;本功能可以备份<a href="javascript:;" onclick="Dwidget('?file=cloud&action=update', '查看更新');" class="b">系统更新</a>，如果是手动更新，在上传覆盖更新文件之前，建议<a href="javascript:;" onclick="Dpatch();" class="b">点此备份</a>
</div>
</td>
</tr>
</tbody>
<tr>
<td class="tl">备注信息</td>
<td>&nbsp;<input type="text" size="68" name="note" id="note" value="" placeholder="本次备份相关的备注事项"/></td>
</tr>
<tr>
<td></td>
<td height="30">&nbsp;<input type="submit" name="submit" value="开始备份" class="btn-g" onclick="this.value='备份中..';this.blur();this.className='btn f_gray';"/></td>
</tr>
</table>
</form>

<?php if($baks) { ?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="155">备份时间</th>
<th>目录</th>
<th width="150">文件数量</th>
<th width="220">备注信息</th>
<th width="40">操作</th>
</tr>
<?php foreach($baks as $v) { ?>
<tr align="center">
<td><?php echo $v['time'];?></td>
<td align="left">&nbsp;&nbsp;<img src="file/ext/folder.gif" alt="" align="absmiddle"/> <a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=view&fid=<?php echo $v['file'];?>', '[<?php echo $v['file'];?>]文件列表');" title="位于 file/patch/<?php echo $v['file'];?> 点击查看文件列表"><?php echo $v['file'];?></a></td>
<td><?php echo $v['num'];?></td>
<td><input type="text" style="width:200px;" title="<?php echo $v['note'];?>" value="<?php echo $v['note'];?>" onblur="note('<?php echo $v['file'];?>', this.value);"/></td>
<td><a href="?file=<?php echo $file;?>&action=delete&fid=<?php echo $v['file'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php } ?>
<tr>
<td></td>
<td><input type="button" value="全部删除" class="btn-r" onclick="if(confirm('确定要删除所有备份文件吗？')){Go('?file=<?php echo $file;?>&action=clear');}"/>&nbsp;</td>
<td></td>
<td></td>
<td></td>
</tr>
</table>
<?php } ?>
<script type="text/javascript">
function note(name, note) {
	$.post('?', 'file=<?php echo $file;?>&action=note&fid='+name+'&note='+note, function(data) {
		if(data == 'ok') showmsg('备注修改成功');
	});
}
function Dpatch(i) {	
	var pid = prompt('请输入8位更新编号，例如 <?php echo DT_RELEASE;?>', ''); 
	if(pid.length == 8) {
		$('#files').val('正在加载文件列表...');
		$.post('?', 'file=<?php echo $file;?>&action=list&pid='+pid, function(data) {
			if(data.indexOf('version.inc.php') == -1) {
				Dtoast(data, 0, 10);
				$('#files').val('');
			} else {
				$('#files').val(data);
				$('#note').val('DESTOON'+pid+'更新备份');
			}
		});
	} else {
		Dtoast('更新编号填写错误');
	}
}
<?php if($release) { ?>
$(function(){
	$('#tp-1').attr('checked', 'checked');$('#type-1').show();$('#type-0').hide();
	$('#note').val('DESTOON<?php echo $release;?>更新备份');
	$('#files').val('正在加载文件列表...');
	$.post('?', 'file=<?php echo $file;?>&action=list&pid=<?php echo $release;?>', function(data) {
		if(data.indexOf('version.inc.php') == -1) {
			Dtoast(data, 0, 10);
			$('#files').val('');
		} else {
			$('#files').val(data);
		}
	});
});
<?php } ?>
Menuon(0);
</script>
<?php include tpl('footer');?>