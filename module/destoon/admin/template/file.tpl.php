<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>
&nbsp;<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&dir=<?php echo $dir;?>');"/>
</td>
</tr>
</table>
</form>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/webuploader.min.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>" id="action"/>
<input type="hidden" name="name" value="" id="name"/>
<input type="hidden" name="text" value="" id="text"/>
</form>
<table cellspacing="0" class="tb">
<tr>
<td class="f_fd" style="border-right:none;">&nbsp;<a href="?file=<?php echo $file;?>" title="<?php echo $root;?>"><img src="file/ext/folder.gif" alt="" align="absmiddle"/></a> / <?php echo dir_nav($dir, $file);?></td>
<td align="right" width="120" class="c_p">
<span id="file-progress"></span>&nbsp;
<img src="file/ext/file-new.gif" alt="" title="新建文件" onclick="_mkfile();"/> &nbsp;
<span id="file-picker"><img src="file/ext/file-upload.gif" alt="" title="上传文件" id="file-picker"/></span> &nbsp;
<img src="file/ext/folder-new.gif" alt="" title="新建文件夹" onclick="_mkdir();"/> &nbsp;
</td>
</tr>
</table>

<table cellspacing="0" class="tb ls">
<tr>
<th align="left"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">名称 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($names) { ?><th width="140">备注名称</th><?php } ?>
<?php if($file == 'template') { ?><th width="140">模板系列</th><?php } ?>
<th width="140"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">文件大小<img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="160"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">修改时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="80">属性</th>
<th width="40">操作</th>
</tr>
<?php foreach($dirs as $v) {?>
<tr align="center">
<td align="left" ondblclick="_rename('<?php echo $v['dirname'];?>');">&nbsp;<img src="file/ext/folder.gif" alt="" align="absmiddle"/> <a href="?file=<?php echo $file;?>&dir=<?php echo $dir.$v['dirname'];?>" class="f_fd"><?php echo $v['dirname'];?></a></td>
<?php if($names) { ?><td><input type="text" style="width:130px;" id="name-<?php echo $v['id'];?>" title="<?php echo isset($names[$v['id']]) ? $names[$v['id']] : '';?>" value="<?php echo isset($names[$v['id']]) ? $names[$v['id']] : '';?>" onblur="_name('<?php echo $v['id'];?>');"/></td><?php } ?>
<?php if($file == 'template') { ?><td>&lt;目录&gt;</td><?php } ?>
<td>&lt;目录&gt;</td>
<td><?php echo $v['mtime'];?></td>
<td ondblclick="_chmod('<?php echo $v['dirname'];?>', this.innerHTML);"><?php echo $v['mod'];?></td>
<td>
<a href="?file=<?php echo $file;?>&action=delete&dir=<?php echo $dir;?>&name=<?php echo $v['dirname'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a>
</td>
</tr>
<?php } ?>

<?php foreach($files as $v) {?>
<tr align="center">
<td align="left" ondblclick="_rename('<?php echo $v['filename'];?>');">&nbsp;<a href="<?php echo $path.$dir.$v['filename'];?>" title="查看" target="_blank"><img src="file/ext/<?php echo $v['ico'];?>.gif" alt="" align="absmiddle"/></a> <a href="<?php echo $v['url'];?>" class="f_fd"><?php echo $v['filename'];?></a></td>
<?php if($names) { ?><td><input type="text" style="width:130px;" id="name-<?php echo $v['id'];?>" title="<?php echo isset($names[$v['id']]) ? $names[$v['id']] : '';?>" value="<?php echo isset($names[$v['id']]) ? $names[$v['id']] : '';?>" onblur="_name('<?php echo $v['id'];?>');"/></td><?php } ?>
<?php if($file == 'template') { ?><td><a href="javascript:;" onclick="_tpl(this.innerHTML);"><?php echo cutstr($v['id'], '', '-');?></a></td><?php } ?>
<td><?php echo $v['filesize'];?> K</td>
<td ondblclick="_touch('<?php echo $v['filename'];?>', this.innerHTML);"><?php echo $v['mtime'];?></td>
<td ondblclick="_chmod('<?php echo $v['filename'];?>', this.innerHTML);"><?php echo $v['mod'];?></td>
<td>
<a href="?file=<?php echo $file;?>&action=delete&dir=<?php echo $dir;?>&name=<?php echo $v['filename'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
</tr>
<?php } ?>
</table>

</div>
<script type="text/javascript">
function _edit(name) {
	Dwidget('?file=<?php echo $file;?>&action=edit&dir=<?php echo $dir;?>&name='+name, '文件修改(ctrl+s保存)');
}
function _tpl(name) {
	Dwidget('?file=<?php echo $file;?>&action=add&dir=<?php echo $dir;?>&type='+name, '创建模板');
}
function _mkdir(v) {
	var name = prompt('【新建文件夹】请输入文件夹名称', v);
	if(!name) return;
	if(name.length > 0) {
		$('#action').val('mkdir');
		$('#name').val(name);
		$('#dform').submit();
	}
}
function _touch(name, time) {
	var text = prompt('【修改时间】请输入修改时间', time);
	if(!text) return;
	if(text.length == 19) {
		$('#action').val('touch');
		$('#name').val(name);
		$('#text').val(text);
		$('#dform').submit();
	} else {
		alert('时间格式错误');
	}
}
function _chmod(name, mod) {
	var text = prompt('【修改属性】请输入属性值', mod);
	if(!text) return;
	if(text.length == 3 || text.length == 4) {
		$('#action').val('chmod');
		$('#name').val(name);
		$('#text').val(text);
		$('#dform').submit();
	} else {
		alert('属性格式错误');
	}
}
function _rename(name) {
	var text = prompt('【重命名】重命名'+name+'为', name);
	if(!text) return;
	if(text.length > 0) {
		$('#action').val('rename');
		$('#name').val(name);
		$('#text').val(text);
		$('#dform').submit();
	} else {
		alert('名称格式错误');
	}
}
function _mkfile(v) {
	var exts = '<?php echo implode(',', $exts_edit);?>';
	var name = prompt('【新建文件】请输入文件名称，后缀限制为:'+exts, v);
	if(!name) return;
	if(name.indexOf('.') == -1 && exts.indexOf(',') == -1) name = name+'.'.exts;
	if(name.length > 3 && (','+exts).indexOf((','+ext(name))) != -1) {
		_edit(name);
	} else {
		alert('文件后缀限制为:'+exts);
	}
}
function _name(name) {
	var note = $('#name-'+name).val();
	if(note == $('#name-'+name).attr('title')) { return; }
	$.post('?', 'file=<?php echo $file;?>&dir=<?php echo $dir;?>&action=name&name='+name+'&note='+note, function(data) {
		if(data == 'ok') {
			showmsg('备注名称修改成功');
			$('#name-'+name).attr('title', note);
		}
	});
}
$(function(){
	var num = 0;
	var fileu = WebUploader.create({
	auto: true,
		server: '?file=<?php echo $file;?>&action=upload&dir=<?php echo $dir;?>',
		pick: '#file-picker',
		accept: {
			title: 'Files',
			extensions: '<?php echo implode(',', $exts_upload);?>',
			mimeTypes: '*/*'
		},
		resize: false
	});
	fileu.on('beforeFileQueued', function(file) {
		num++;
		var exts = fileu.options.accept[0].extensions;
		if((','+exts).indexOf(','+ext(file.name)) == -1) {
			alert(L['upload_ext']+ext(file.name)+' '+L['upload_allow']+exts);
			return false;
		}
	});
	fileu.on('fileQueued', function(file) {
		$('#file-progress').html('0%');
	});
	fileu.on('uploadProgress', function(file, percentage) {
		var p = parseInt(percentage * 100);
		if(p >= 100) p = 100;
		$('#file-progress').html(p+'%');
	});
	fileu.on( 'uploadSuccess', function(file, data) {
		num--;
		if(data.error) {
			alert(data.message);
		} else {
			window.setTimeout(function() {
				if(num < 1) window.location.reload();
			}, 500);
		}
	});
	fileu.on( 'uploadError', function(file, data) {
		alert(data.message);
	});
	fileu.on('uploadComplete', function(file) {
		$('#file-progress').html('100%');
	});
});
Menuon(<?php echo $menuid;?>);
</script>
<?php include tpl('footer');?>