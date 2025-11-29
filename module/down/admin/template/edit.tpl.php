<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('webuploader.min.js');?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<?php if($history) { ?>
<tr>
<td class="tl" style="background:#FDE7E7;"><span class="f_red">*</span> 审核提示</td>
<td style="background:#FDE7E7;">该信息存在修改记录，<a href="javascript:;" onclick="Dwidget('?file=history&mid=<?php echo $moduleid;?>&itemid=<?php echo $itemid;?>', '修改详情');" class="t">点击查看</a> 修改详情</td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> 所属分类</td>
<td><?php echo $_admin == 1 ? category_select('post[catid]', '选择分类', $catid, $moduleid) : ajax_category_select('post[catid]', '选择分类', $catid, $moduleid);?> <span id="dcatid" class="f_red"></span></td>
</tr>
<?php if($CP) { ?>
<script type="text/javascript">
var property_catid = <?php echo $catid;?>;
var property_itemid = <?php echo $itemid;?>;
var property_admin = 1;
</script>
<?php load('property.js');?>
<tbody id="load_property" style="display:none;">
<tr><td></td><td></td></tr>
</tbody>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> <?php echo $MOD['name'];?>标题</td>
<td><input name="post[title]" type="text" id="title" size="70" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level);?> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 标题图片</td>
<td>
<input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php if($thumb) { ?><?php echo $thumb;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb').value);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb').value);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 文件地址</td>
<td><input name="post[fileurl]" id="fileurl" type="text" size="70" value="<?php echo $fileurl;?>"/>
<span class="upl">
<span id="file-picker"><img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传"/></span>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('fileurl').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('fileurl').value='';$('#file-progress').html('');"/>
</span>
<span id="file-progress" class="f_gray"></span> <span id="dfileurl" class="f_red"></span>
<script type="text/javascript">
<?php if(strpos($DT_MBS, 'IE') === false) { ?>
	var fileu;
	$(function(){
		fileu = WebUploader.create({
		auto: true,
			server: UPPath+'?moduleid=<?php echo $moduleid;?>&action=webuploader&from=file',
			pick: '#file-picker',
			accept: {
				title: 'Files',
				extensions: '<?php echo str_replace('|', ',', $MOD['upload']);?>',
				mimeTypes: '*/*'
			},
			fileNumLimit: 1,
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
				Dmsg(data.message, 'fileurl');
			} else {
				$('#file-progress').html('100%');
				$('#fileurl').val(data.url);
				initd(data.size);
			}
		});
		fileu.on( 'uploadError', function(file, data) {
			Dmsg(data.message, 'fileurl');
		});
		fileu.on('uploadComplete', function(file) {
			$('#file-progress').html('100%');
			window.setTimeout(function() {$('#file-progress').html('');}, 1000);
		});
		window.setTimeout(function() {fileu.refresh();}, 1000);
		window.setTimeout(function() {fileu.refresh();}, 2000);
	});
<?php } else { ?>
	$(function(){
		$('#file-picker').click(function() {
			Dfile(<?php echo $moduleid;?>, Dd('fileurl').value, 'fileurl', '<?php echo $MOD['upload'];?>');
		});
	});
<?php } ?>
</script>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 文件类型</td>
<td><?php echo ext_select('post[fileext]', $fileext, 'id="fileext"');?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 文件大小</td>
<td><input name="post[filesize]" id="filesize" type="text" size="10" value="<?php echo $filesize;?>"/>&nbsp;<?php echo unit_select('post[unit]', $unit, 'id="unit"');?>&nbsp;<span id="dfilesize" class="f_red"></span></td>
</tr>
<?php echo $FD ? fields_html('<td class="tl">', '<td>', $item) : '';?>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>说明</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '98%', 350);?><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>专辑</td>
<td><input name="post[album]" type="text" size="30" value="<?php echo $album;?>"/> <?php tips('填写一个下载的关键词或者专辑名称，以便关联同专辑的下载');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 标签(Tag)</td>
<td>
<input name="post[tag]" type="text" size="80" value="<?php echo $tag;?>" id="tag"/>
<?php if($DT['split_appcode']) { ?> &nbsp; <a href="javascript:;" onclick="CloudSplit('title', 'tag');" class="t">[生成]</a> &nbsp; <?php } ?> 
<?php tips('多个标签请用空格隔开');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><input name="post[username]" type="text" size="20" value="<?php echo $username;?>" id="username"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $MOD['name'];?>状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 通过</label>
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 待审</label>
<label><input type="radio" name="post[status]" value="1" <?php if($status == 1) echo 'checked';?> onclick="if(this.checked) Dd('note').style.display='';"/> 拒绝</label>
<label><input type="radio" name="post[status]" value="0" <?php if($status == 0) echo 'checked';?>/> 删除</label>
</td>
</tr>
<tr id="note" style="display:<?php echo $status==1 ? '' : 'none';?>">
<td class="tl"><span class="f_red">*</span> 拒绝理由</td>
<td><input name="post[note]" type="text"  size="40" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 添加时间</td>
<td><?php echo dcalendar('post[addtime]', $addtime, '-', 1);?></td>
</tr>
<?php if($DT['city']) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 地区(分站)</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 浏览次数</td>
<td><input name="post[hits]" type="text" size="10" value="<?php echo $hits;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 下载次数</td>
<td><input name="post[download]" type="text" size="10" value="<?php echo $download;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容收费</td>
<td><input name="post[fee]" type="text" size="10" value="<?php echo $fee;?>"/><?php tips('不填或填0表示继承模块设置价格，-1表示不收费<br/>大于0的数字表示具体收费价格');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容模板</td>
<td><?php echo tpl_select('show', $module, 'post[template]', '默认模板', $template, 'id="template"');?><?php tips('如果没有特殊需要，一般不需要选择<br/>系统会自动继承分类或模块设置');?></td>
</tr>
<?php if($MOD['show_html']) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 自定义文件路径</td>
<td><input type="text" size="70" name="post[filepath]" value="<?php echo $filepath;?>" id="filepath"/>&nbsp;<input type="button" value="重名检测" onclick="ckpath(<?php echo $moduleid;?>, <?php echo $itemid;?>);" class="btn"/>&nbsp;<?php tips('可以包含目录和文件 例如 destoon/about.html<br/>请确保目录和文件名合法且可写入，否则可能生成失败');?>&nbsp; <span id="dfilepath" class="f_red"></span></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<?php if($action == 'add' && in_array($moduleid, explode(',', $DT['fetch_module']))) { ?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<div class="tt">单页采编</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 目标网址</td>
<td><input name="url" type="text" size="80" value="<?php echo $url;?>"/>&nbsp;&nbsp;<input type="submit" value=" 获 取 " class="btn"/>&nbsp;&nbsp;<input type="button" value=" 管理规则 " class="btn" onclick="Dwidget('?file=fetch', '管理规则');"/></td>
</tr>
</table>
</form>
<?php } ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'catid_1';
	if(Dd(f).value == 0) {
		Dmsg('请选择所属分类', 'catid', 1);
		return false;
	}
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写下载名称', f);
		return false;
	}
	f = 'fileurl';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请填写下载地址', f);
		return false;
	}
	f = 'filesize';
	l = Dd(f).value;
	if(!l) {
		Dmsg('请填写文件大小', f);
		return false;
	}
	<?php echo $FD ? fields_js() : '';?>
	<?php echo $CP ? property_js() : '';?>
	return true;
}
function auto_type() {
	var file_url = Dd('fileurl').value;
	var file_ext = ext(file_url);
	var file_type = '';
	if('rar|zip'.indexOf(file_ext) != -1) {
		file_type = 'rar';
	} else if('jpg|jpeg|png|gif|bmp'.indexOf(file_ext) != -1) {
		file_type = 'img';
	} else if('mp4|mov|3gp|wma|wav|rm|rmvb|ram|flv'.indexOf(file_ext) != -1) {
		file_type = 'mov';
	} else if('mp3|m4a|flac'.indexOf(file_ext) != -1) {
		file_type = 'mp3';
	} else if('exe|pdf|doc|xls|ppt|swf|chm|hlp'.indexOf(file_ext) != -1) {
		file_type = file_ext;
	} else if('docx|xlsx|pptx'.indexOf(file_ext) != -1) {
		file_type = file_ext.substring(0, 3);
	}
	if(file_type) $('#fileext').val(file_type);
}
function initd(file_size) {
	auto_type();
	Dd('filesize').value = file_size;
	$('#unit').val('M');
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>