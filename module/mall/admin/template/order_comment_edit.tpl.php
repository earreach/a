<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php include template('goods', 'chip');?>
<div class="tt">订单详情</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">卖家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($O['seller']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['seller'];?>');" class="t"><?php echo $O['seller'];?></a></td>
</tr>
<tr>
<td class="tl">买家</td>
<td><?php if($DT['im_web']) { ?><?php echo im_web($O['buyer']);?>&nbsp;<?php } ?><a href="javascript:;" onclick="_user('<?php echo $O['buyer'];?>');" class="t"><?php echo $O['buyer'];?></a></td>
</tr>
<?php if($logs) { ?>
<tr>
<td class="tl">订单进程</td>
<td>
<div style="line-height:24px;">
<?php if(is_array($logs)) { foreach($logs as $v) { ?>
<?php echo $v['adddate'];?> - <?php echo $v['title'];?><br/>
<?php } } ?>
</div>
</td>
</tr>
<?php } else { ?>
<tr>
<td class="tl">下单时间</td>
<td><?php echo $O['adddate'];?></td>
</tr>
<tr>
<td class="tl">最后更新</td>
<td><?php echo $O['updatedate'];?></td>
</tr>
<?php if($O['send_time']) { ?>
<tr>
<td class="tl">发货时间</td>
<td><?php echo $O['send_time'];?></td>
</tr>
<?php } ?>
<?php } ?>
<tr>
<td class="tl">交易状态</td>
<td><?php echo $dstatus[$O['status']];?></td>
</tr>
<?php if($O['buyer_reason']) { ?>
<tr>
<td class="tl">退款原因</td>
<td><?php echo $O['buyer_reason'];?></td>
</tr>
<?php } ?>
<?php if($O['refund_reason']) { ?>
<tr>
<td class="tl">操作原因</td>
<td><?php echo $O['refund_reason'];?></td>
</tr>
<tr>
<td class="tl">操作人</td>
<td><?php echo $O['editor'];?></td>
</tr>
<tr>
<td class="tl">操作时间</td>
<td><?php echo $O['updatetime'];?></td>
</tr>
<?php } ?>
</table>

<?php load('player.js');?>
<?php load('url2video.js');?>
<div class="tt">修改评价<a name="comment"></a></div>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">买家评分</td>
<td>
<label><input type="radio" name="post[seller_star]" value="5"<?php echo $cm['seller_star'] == 5 ? ' checked' : '';?>/> 5星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star]" value="4"<?php echo $cm['seller_star'] == 4 ? ' checked' : '';?>/> 4星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star]" value="3"<?php echo $cm['seller_star'] == 3 ? ' checked' : '';?>/> 3星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star]" value="2"<?php echo $cm['seller_star'] == 2 ? ' checked' : '';?>/> 2星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star]" value="1"<?php echo $cm['seller_star'] == 1 ? ' checked' : '';?>/> 1星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star]" value="0"<?php echo $cm['seller_star'] == 0 ? ' checked' : '';?>/> 待评</label>
</td>
</tr>
<tr>
<td class="tl">物流评分</td>
<td>
<label><input type="radio" name="post[seller_star_express]" value="5"<?php echo $cm['seller_star_express'] == 5 ? ' checked' : '';?>/> 5星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_express]" value="4"<?php echo $cm['seller_star_express'] == 4 ? ' checked' : '';?>/> 4星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_express]" value="3"<?php echo $cm['seller_star_express'] == 3 ? ' checked' : '';?>/> 3星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_express]" value="2"<?php echo $cm['seller_star_express'] == 2 ? ' checked' : '';?>/> 2星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_express]" value="1"<?php echo $cm['seller_star_express'] == 1 ? ' checked' : '';?>/> 1星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_express]" value="0"<?php echo $cm['seller_star_express'] == 0 ? ' checked' : '';?>/> 待评</label>
</td>
</tr>
<tr>
<td class="tl">商家评分</td>
<td>
<label><input type="radio" name="post[seller_star_service]" value="5"<?php echo $cm['seller_star_service'] == 5 ? ' checked' : '';?>/> 5星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_service]" value="4"<?php echo $cm['seller_star_service'] == 4 ? ' checked' : '';?>/> 4星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_service]" value="3"<?php echo $cm['seller_star_service'] == 3 ? ' checked' : '';?>/> 3星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_service]" value="2"<?php echo $cm['seller_star_service'] == 2 ? ' checked' : '';?>/> 2星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_service]" value="1"<?php echo $cm['seller_star_service'] == 1 ? ' checked' : '';?>/> 1星</label>&nbsp;&nbsp; 
<label><input type="radio" name="post[seller_star_service]" value="0"<?php echo $cm['seller_star_service'] == 0 ? ' checked' : '';?>/> 待评</label>
</td>
</tr>
<tr>
<td class="tl">买家评价</td>
<td><textarea name="post[seller_comment]" style="width:360px;height:60px;"><?php echo $cm['seller_comment'];?></textarea></td>
</tr>
<tr>
<td class="tl">买家图片</td>
<td>
<div id="thumbs">
<?php if(is_array($thumbs)) { foreach($thumbs as $k => $v) { ?>
<div class="thumbs">
<input type="hidden" name="post[thumbs][]" id="thumb<?php echo $k;?>" value="<?php echo $v;?>"/>
<div><img src="<?php if($v) { ?><?php echo $v;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" width="100" height="100" id="showthumb<?php echo $k;?>" title="上传/预览图片" alt="" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(Dd('showthumb<?php echo $k;?>').src, 1);}else{Dalbum(<?php echo $k;?>,<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb<?php echo $k;?>').value, true);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dalbum(<?php echo $k;?>,<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb<?php echo $k;?>').value, true);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="delAlbum(<?php echo $k;?>);"/></p>
</div>
<?php } } ?>
</div>
<div class="dsn" id="thumbtpl">
<div class="thumbs">
<input type="hidden" name="post[thumbs][]" id="thumb-99" value="" autocomplete="off"/>
<div><img src="<?php echo DT_STATIC;?>image/upload-image.png" id="showthumb-99" title="上传/预览图片" alt="" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(Dd('showthumb-99').src, 1);}else{Dalbum(-99,<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb-99').value, true);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dalbum(-99,<?php echo $moduleid;?>,<?php echo $MOD['thumb_width'];?>,<?php echo $MOD['thumb_height'];?>, Dd('thumb<?php echo $thumbs ? $k : '0';?>').value, true);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="delAlbum(-99);"/></p>
</div>
</div>
<div class="thumbs" id="thumbmuti" title="批量上传图片，按Ctrl键多选">
<div id="file-picker"><img src="<?php echo DT_STATIC;?>image/upload-muti.png" alt=""/></div>
<p>批量上传<span id="file-progress"></span></p>
</div>
<span id="dthumb" class="f_red"></span>
<?php load('webuploader.min.js');?>
<script type="text/javascript">
var album_max = parseInt(<?php echo $DT['thumb_max'];?>);
if(album_max < 5 || album_max > 99) album_max = 9;
<?php if(strpos($DT_MBS, 'IE') === false) { ?>
	var fileu = WebUploader.create({
		auto: true,
		server: UPPath+'?moduleid=<?php echo $moduleid;?>&action=webuploader&from=album&width=<?php echo $MOD['thumb_width'];?>&height=<?php echo $MOD['thumb_height'];?>',
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
			Dmsg(data.message, 'thumb');
		} else {
			addAlbum(data.url, album_max);
			newAlbum(album_max);
		}
	});
	fileu.on( 'uploadError', function(file, data) {
		Dmsg(data.message, 'thumb');
	});
	fileu.on('uploadFinished', function(file) {
		$('#file-progress').html('');
		if($("#thumbs input[value!='']").length >= album_max) $('#thumbmuti').hide();
	});
	$(function(){
		if($("#thumbs input[value!='']").length >= album_max) window.setTimeout(function(){$('#thumbmuti').hide();}, 3000);
		newAlbum(album_max);
	});
<?php } else { ?>
	$(function(){
		$('#thumbmuti').hide();
		newAlbum(album_max);
	});
<?php } ?>
</script>
</td>
</tr>
<tr>
<td class="tl">买家视频</td>
<td><input name="post[seller_video]" type="text" id="video" size="70" value="<?php echo $cm['seller_video'];?>" onblur="UpdateURL();"/>
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
				extensions: 'mp4,mov,3gp,mp3', 
				mimeTypes: 'video/*'
			},
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
		}
		});
		filev.on( 'uploadError', function(file, data) {
			Dmsg(data.message, 'video');
		});
		filev.on('uploadComplete', function(file) {
			$('#video-progress').html('100%');
			window.setTimeout(function() {
				$('#video-progress').html(' ');
			}, 1000);
		});
	});
<?php } else { ?>
	$(function(){
		$('#video-picker').click(function() {
			Dfile(<?php echo $moduleid;?>, Dd('video').value, 'video', 'mp4|mov|3gp|mp3');
		});
	});
<?php } ?>
function Dplay() {
	UpdateURL();
	if(Dd('video').value.length > 5) {
		mkDialog('', '<div style="width:800px;height:450px;background:#000000;">'+player(url,800,450,1)+'</div>', '视频预览', 800);
	} else {
		Dmsg('视频地址为空', 'video');
	}
}
function UpdateURL() {
	var str = url2video(Dd('video').value);
	if(str) Dd('video').value = str;
}
</script>
</td>
</tr>
<tr>
<td class="tl">评价时间</td>
<td><input type="text" style="width:150px;" name="post[seller_ctime]" value="<?php echo $cm['seller_ctime'] ? timetodate($cm['seller_ctime'], 6) : '';?>"/></td>
</tr>
<tr>
<td class="tl">卖家解释</td>
<td><textarea name="post[buyer_reply]" style="width:360px;height:60px;"><?php echo $cm['buyer_reply'];?></textarea></td>
</tr>
<tr>
<td class="tl">解释时间</td>
<td><input type="text" style="width:150px;" name="post[buyer_rtime]" value="<?php echo $cm['buyer_rtime'] ? timetodate($cm['buyer_rtime'], 6) : '';?>"/></td>
</tr>

<tr>
<td class="tl">卖家评分</td>
<td>
<label><input type="radio" name="post[buyer_star]" value="5"<?php echo $cm['buyer_star'] == 5 ? ' checked' : '';?>/> 5星&nbsp;&nbsp;  
<label><input type="radio" name="post[buyer_star]" value="4"<?php echo $cm['buyer_star'] == 4 ? ' checked' : '';?>/> 4星&nbsp;&nbsp; 
<label><input type="radio" name="post[buyer_star]" value="3"<?php echo $cm['buyer_star'] == 3 ? ' checked' : '';?>/> 3星&nbsp;&nbsp; 
<label><input type="radio" name="post[buyer_star]" value="2"<?php echo $cm['buyer_star'] == 2 ? ' checked' : '';?>/> 2星&nbsp;&nbsp; 
<label><input type="radio" name="post[buyer_star]" value="1"<?php echo $cm['buyer_star'] == 1 ? ' checked' : '';?>/> 1星&nbsp;&nbsp; 
<label><input type="radio" name="post[buyer_star]" value="0"<?php echo $cm['buyer_star'] == 0 ? ' checked' : '';?>/> 待评
</td>
</tr>
<tr>
<td class="tl">卖家评价</td>
<td><textarea name="post[buyer_comment]" style="width:360px;height:60px;"><?php echo $cm['buyer_comment'];?></textarea></td>
</tr>
<tr>
<td class="tl">评价时间</td>
<td><input type="text" style="width:150px;" name="post[buyer_ctime]" value="<?php echo $cm['buyer_ctime'] ? timetodate($cm['buyer_ctime'], 6) : '';?>"/></td>
</tr>
<tr>
<td class="tl">买家解释</td>
<td><textarea name="post[seller_reply]" style="width:360px;height:60px;"><?php echo $cm['seller_reply'];?></textarea></td>
</tr>
<tr>
<td class="tl">解释时间</td>
<td><input type="text" style="width:150px;" name="post[seller_rtime]" value="<?php echo $cm['seller_rtime'] ? timetodate($cm['seller_rtime'], 6) : '';?>"/></td>
</tr>
</table>
<input type="hidden" name="file" value="<?php echo $file;/*WebUploader会覆盖file*/?>"/>
<div class="sbt"><input type="submit" name="submit" value=" 修 改 " class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value=" 取 消 " class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=comment');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	return confirm('确定要修改该订单的评价吗？');
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>