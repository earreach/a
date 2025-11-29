<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('webuploader.min.js');?>
<?php load('url2video.js');?>
<?php load('player.js');?>
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
<td class="tl"><span class="f_red">*</span> 标题图片</td>
<td>
<input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php if($thumb) { ?><?php echo $thumb;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb').value);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb').value);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 视频地址</td>
<td><input name="post[video]" type="text" id="video" size="70" value="<?php echo $video;?>" onblur="UpdateURL();"/>
<span class="upl">
<span id="video-picker"><img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传"/></span>
<img src="<?php echo DT_STATIC;?>image/ico-play.png" title="预览" onclick="Dplay();"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('video').value='';$('#video-progress').html('');"/>
</span>
<span id="video-progress" class="f_gray"></span> <span id="dvideo" class="f_red"></span>
<script type="text/javascript">
<?php if(strpos($DT_MBS, 'IE') === false) { ?>
	var filev;
	$(function(){
		filev = WebUploader.create({
			auto: true,
			server: UPPath+'?moduleid=<?php echo $moduleid;?>&action=webuploader&from=file',
			pick: '#video-picker',
			accept: {
				title: 'video',
				extensions: '<?php echo str_replace('|', ',', $MOD['upload']);?>', 
				mimeTypes: 'video/*'
			},
			fileNumLimit: 1,
			resize: false
		});
		filev.on('beforeFileQueued', function(file) {
			var exts = filev.options.accept[0].extensions;
			if((','+exts).indexOf(','+ext(file.name)) == -1) {
				alert(L['upload_ext']+ext(file.name)+' '+L['upload_allow']+exts);
				return false;
			}
		});
		filev.on('fileQueued', function(file) {
			$('#video-progress').html('0%');
		});
		filev.on('uploadProgress', function(file, percentage) {
			var p = parseInt(percentage * 100);
			if(p >= 100) p = 100;
			$('#video-progress').html(p+'%');
		});
		filev.on( 'uploadSuccess', function(file, data) {
			if(data.error) {
				Dmsg(data.message, 'video');
			} else {
				$('#video-progress').html('100%');
				$('#video').val(data.url);
				/*duration*/
				var url = data.url;
				if(ext(url) == 'mp4' || ext(url) == 'mp3') {
					var audio = new Audio(url);
					audio.addEventListener("loadedmetadata", function (e) {
						if(audio.duration) Dd('duration').value = parseInt(audio.duration);
					});
				}
			}
		});
		filev.on( 'uploadError', function(file, data) {
			Dmsg(data.message, 'video');
		});
		filev.on('uploadComplete', function(file) {
			$('#video-progress').html('100%');
			window.setTimeout(function() {$('#video-progress').html('');}, 1000);
		});
		window.setTimeout(function() {filev.refresh();}, 1000);
		window.setTimeout(function() {filev.refresh();}, 2000);
	});
<?php } else { ?>
	$(function(){
		$('#video-picker').click(function() {
			Dfile(<?php echo $moduleid;?>, Dd('video').value, 'video', '<?php echo $MOD['upload'];?>');
		});
	});
<?php } ?>
function Dplay() {
	UpdateURL();
	if(Dd('video').value.length > 5) {
		var w = parseInt(Dd('width').value);
		var h = parseInt(Dd('height').value);
		if(w < 100 || h < 100) {
			Dtoast('视频宽度或高度不能小于100');
			return;
		}
		if(w > 800 || w < 100) {
			h = parseInt(800*h/w);
			w = 800;
		}
		mkDialog('', '<div style="width:'+w+'px;height:'+h+'px;background:#000000;">'+player(Dd('video').value,w,h,1)+'</div>', '视频预览', w);
	} else {		
		Dtoast('视频地址为空');
	}
}
function UpdateURL() {
	var str = url2video(Dd('video').value);
	if(str) Dd('video').value = str;
	var url = Dd('video').value;
	if(!Dd('duration').value && (ext(url) == 'mp4' || ext(url) == 'mp3')) {
		var audio = new Audio(url);
		audio.addEventListener("loadedmetadata", function (e) {
			if(audio.duration) Dd('duration').value = parseInt(audio.duration);
		});
	}
}
</script>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 视频宽度</td>
<td>
<input name="post[width]" id="width" type="text" size="5" value="<?php echo $width;?>"/> px <img src="<?php echo DT_SKIN;?>ico-swc.png" title="宽高互换" width="16" height="16" class="c_p" align="absmiddle" onclick="var swc=Dd('width').value;Dd('width').value=Dd('height').value;Dd('height').value=swc;"/> 高度 <input name="post[height]" id="height" type="text" size="5" value="<?php echo $height;?>"/> px &nbsp; 
<select onchange="if(this.value){var tmp=this.value.split('|');Dd('width').value=tmp[0];Dd('height').value=tmp[1];}">
<option value="">常用尺寸</option>
<option value="4096|2160"<?php if($width==4096&&$height==2160) { ?> selected<?php } ?>>4K 超清</option>
<option value="1920|1080"<?php if($width==1920&&$height==1080) { ?> selected<?php } ?>>1080P 高清</option>
<option value="1280|720"<?php if($width==1280&&$height==720) { ?> selected<?php } ?>>720P 高清</option>
<option value="720|480"<?php if($width==720&&$height==480) { ?> selected<?php } ?>>480P 清晰</option>
<option value="480|360"<?php if($width==480&&$height==360) { ?> selected<?php } ?>>360P 流畅</option>
</select>
<span id="dsize" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 封面图片</td>
<td>
<input type="hidden" name="post[poster]" id="poster" value="<?php echo $poster;?>"/>
<div class="thumbu">
<div><img src="<?php if($poster) { ?><?php echo $poster;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pposter" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('poster').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,Dd('width').value,Dd('height').value,Dd('poster').value,0,'poster');}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,Dd('width').value,Dd('height').value,Dd('poster').value,0,'poster');"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('poster').value='';Dd('pposter').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dposter" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 视频时长</td>
<td><input name="post[duration]" id="duration" type="text" size="10" value="<?php echo $duration;?>"/> 秒 <span class="f_gray">支持hh:mm:ss格式，例如10:16</span> <span id="dduration" class="f_red"></span></td>
</tr>
<?php echo $FD ? fields_html('<td class="tl">', '<td>', $item) : '';?>
<tr>
<td class="tl"><span class="f_hid">*</span> 视频说明</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '98%', 350);?><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 视频专辑</td>
<td><input name="post[album]" type="text" size="30" value="<?php echo $album;?>"/> <?php tips('填写一个视频的关键词或者专辑名称，以便关联同专辑的视频');?></td>
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
<td><input name="post[username]" type="text"  size="20" value="<?php echo $username;?>" id="username"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
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
		Dmsg('请填写视频名称', f);
		return false;
	}
	f = 'thumb';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请上传标题图片', f);
		return false;
	}
	UpdateURL();
	f = 'video';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请填写视频地址', f);
		return false;
	}
	if(!Dd('width').value) {
		Dmsg('请填写视频宽度', 'size');
		return false;
	}
	if(!Dd('height').value) {
		Dmsg('请填写视频高度', 'size');
		return false;
	}
	<?php echo $FD ? fields_js() : '';?>
	<?php echo $CP ? property_js() : '';?>
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>