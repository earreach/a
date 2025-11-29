<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="update" value="1"/>
<input type="hidden" name="muti_upload" id="muti_upload"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">所属<?php echo $MOD['name'];?></td>
<td>
<span class="f_r f_grey">
已上传：<?php echo $items;?> / <?php echo $MOD['maxitem'];?> &nbsp; 
</span>
 &nbsp; <a href="<?php echo $MOD['linkurl'];?><?php echo $item['linkurl'];?>" target="_blank" class="t"><?php echo $item['title'];?></a></td>
</tr>
<tr>
<td class="tl">图片列表</td> 
<td>
<?php if($items < $MOD['maxitem']) { ?>
<div style="width:130px;float:left;">
	<input type="hidden" name="post[0][thumb]" id="thumb0"/>
	<table width="120" class="ctb">
	<tr align="center" height="110" class="c_p">
	<td width="120"><img src="<?php echo DT_STATIC;?>image/upload-image.png" id="showthumb0" title="预览图片" alt="" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dphoto(0,<?php echo $moduleid;?>,100,100, Dd('thumb0').value, true);}" style="border:#DDDDDD 1px solid;"/></td>
	</tr>
	<tr align="center">
	<td height="20" onclick="Dphoto(0,<?php echo $moduleid;?>,100,100, Dd('thumb0').value, true);" class="jt"><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传"/></td>
	</tr>
	<tr align="center" title="简介">
	<td><textarea name="post[0][introduce]" style="width:90px;height:40px;" placeholder="简介："></textarea></td>
	</tr>
	<tr align="center" title="排序">
	<td><input type="text" style="width:90px;" name="post[0][listorder]" value="" placeholder="排序："/></td>
	</tr>
	</table>
</div>
<?php } ?>
<?php foreach($lists as $k=>$v) { ?>
<div style="width:130px;float:left;">
	<input type="hidden" name="post[<?php echo $v['itemid'];?>][thumb]" id="thumb<?php echo $v['itemid'];?>" value="<?php echo $v['thumb'];?>"/>
	<table width="120" class="ctb">
	<tr align="center" height="110" class="c_p">
	<td width="120"><img src="<?php echo $v['thumb'];?>" width="100" height="100" id="showthumb<?php echo $v['itemid'];?>" title="预览图片" alt="" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dphoto(<?php echo $v['itemid'];?>,<?php echo $moduleid;?>,100,100, Dd('thumb<?php echo $v['itemid'];?>').value, true);}" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';" style="border:#DDDDDD 1px solid;"/></td>
	</tr>
	<tr align="center">
	<td height="20">
	<span onclick="Dphoto(<?php echo $v['itemid'];?>,<?php echo $moduleid;?>,100,100, Dd('thumb<?php echo $v['itemid'];?>').value, true);" class="jt"><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传"/></span>&nbsp;
	<a href="?moduleid=<?php echo $moduleid;?>&action=item_delete&itemid=<?php echo $v['itemid'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除"/></a>
	<input type="checkbox" name="id[]" value="<?php echo $v['itemid'];?>" title="选择并删除" style="margin:0 0 0 48px;"/>
	</td>
	</tr>
	<tr align="center" title="<?php echo $v['introduce'];?>">
	<td><textarea name="post[<?php echo $v['itemid'];?>][introduce]" style="width:90px;height:40px;" placeholder="简介："><?php echo $v['introduce'];?></textarea></td>
	</tr>
	<tr align="center" title="排序">
	<td><input type="text" style="width:90px;" name="post[<?php echo $v['itemid'];?>][listorder]" value="<?php echo $v['listorder'] > 0 ? $v['listorder'] : '';?>" placeholder="排序："/></td>
	</tr>
	</table>
</div>
<?php } ?>
</td>
</tr>
<tr>
<td class="tl"><label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label></td>
<td height="48">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保存数据" class="btn-g" onclick="this.form.action='?job=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删除选中" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'张选中图片吗？此操作将不可撤销')) {this.form.action='?job=delete';}else{return false;}}else{confirm('请选择要删除的图片');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="完成上传" class="btn" onclick="window.parent.cDialog();"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<div class="tt">方法二、批量上传图片</div>
<?php load('webuploader.min.js');?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">批量上传</td> 
<td>
<?php if(strpos($DT_MBS, 'IE') === false) { ?>
<div id="file-picker"><img src="<?php echo DT_STATIC;?>image/upload-image.png" title="批量上传图片" alt="" style="border:#DDDDDD 1px solid;"/></div>
<script type="text/javascript">
var muti_max = parseInt(<?php echo $MOD['maxitem']-$items;?>);
if(muti_max < 1) muti_max = 0;
var fileu = WebUploader.create({
	auto: true,
    server: UPPath+'?moduleid=<?php echo $moduleid;?>&action=webuploader&from=photo&width=100&height=100',
    pick: '#file-picker',
    accept: {
		title: 'image',
        extensions: 'jpg,jpeg,png,gif,bmp',
		mimeTypes: 'image/*'
    },
    resize: false
});
fileu.on('beforeFileQueued', function(file) {
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
	if(data.error) {
		Dmsg(data.message, 'muti');
	} else {
		if(substr_count(Dd('muti_upload').value, '|') < muti_max) Dd('muti_upload').value += data.url+'|';
	}
});
fileu.on( 'uploadError', function(file, data) {
    Dmsg(data.message, 'muti');
});
fileu.on('uploadFinished', function(file) {
    $('#file-progress').html('100%');
	Dd('dform').submit();
});
</script>
<?php } else { ?>
<span class="f_grey">此功能不支持IE浏览器</span>
<?php } ?>
</td>
</tr>
<tr>
<td class="tl">提示信息</td>
<td class="tr f_gray">&nbsp;按Ctrl键或拖动鼠标可多选图片 <span id="file-progress"></span> <span id="dmuti" class="f_red"></span></td>
</tr>
</table>
<div class="tt">方法三、上传zip压缩文件</div>
<form method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="zip"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">选择文件</td>
<td><input name="uploadfile" type="file" size="25"/></td>
</tr>
<tr>
<td class="tl">提示信息</td>
<td class="f_gray">&nbsp;如果同时上传多张图片，可以将图片压缩为zip格式上传，目录结构不限</td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" value="上 传" class="btn-b"/></td>
</tr>
</table>
</form>
<div class="tt">方法四、FTP上传目录或者zip压缩包</div>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="dir"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">请选择</td>
<td>
&nbsp;<select name="name">
<option>请选择目录或者zip文件</option>
<?php
foreach(glob(DT_ROOT.'/file/temp/*') as $v) {
	if(is_dir($v)) {
		$v = basename($v);
		echo '<option value="'.$v.'">/'.$v.'/</option>';
	} else if(file_ext($v) == 'zip') {
		$v = basename($v);		
		echo '<option value="'.$v.'">/'.$v.'</option>';
	}
}
?>
</select>
&nbsp;&nbsp;
<a href="javascript:window.location.reload();" class="t">刷新</a>
</td>
</tr>
<tr>
<td class="tl">提示信息</td>
<td class="f_gray">&nbsp;可以创建目录存放图片，并FTP上传目录至 file/temp/ 目录，或者直接打包为zip格式上传至 file/temp/ 目录</td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" value="读 取" class="btn-b"/></td>
</tr>
</table>
</form>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>