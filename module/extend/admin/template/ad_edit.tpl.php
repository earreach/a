<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menusad);
?>
<form method="post" action="?" id="runcode_form" target="_blank">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="runcode"/>
<input type="hidden" name="codes" id="codes" value=""/>
</form>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="aid" value="<?php echo $aid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="pid" value="<?php echo $p['pid'];?>"/>
<input type="hidden" name="post[pid]" value="<?php echo $p['pid'];?>"/>
<input type="hidden" name="post[typeid]" value="<?php echo $p['typeid'];?>"/>
<input type="hidden" name="post[key_moduleid]" value="<?php echo $p['moduleid'];?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 广告位</td>
<td>&nbsp;<b><?php echo $p['name'];?></b><?php if($action == 'add') { ?> &nbsp; <a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=list_place" class="t">[重选]</a><?php } ?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告类型</td>
<td class="f_gray">&nbsp;<?php echo $TYPE[$p['typeid']];?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 广告名称</td>
<td><input name="post[title]" id="title" type="text" size="30" value="<?php echo $title;?>"/> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告介绍</td>
<td><input name="post[introduce]" type="text" size="50" value="<?php echo $introduce;?>"/></td>
</tr>
<?php if($p['typeid'] == 1) { ?>
<tr>
<td class="tl"><span class="f_red">*</span> 广告代码</td>
<td><textarea name="post[code]" id="code" style="width:98%;height:150px;overflow:visible;" class="f_fd"><?php echo $code;?></textarea><br/>
<input type="button" value=" 运行代码 " class="btn" onclick="runcode();"/> <span id="dcode" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 上传文件</td>
<td class="f_gray"><input type="text" size="70" id="upload"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dfile(<?php echo $moduleid;?>, Dd('upload').value, 'upload', '<?php echo $DT['uploadtype'];?>');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('upload').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-copy.png" title="复制" data-clipboard-action="copy" data-clipboard-target="#upload" onclick="if(Dd('upload').value) Dtoast('文件地址已复制');"/>
</span>
<?php tips('从这里上传文件后，把地址复制到代码里即可使用');?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">var clipboard = new Clipboard('[data-clipboard-action]');</script>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 链接地址</td>
<td><input type="text" size="50" name="post[url]" value="<?php echo $url;?>"/></td>
</tr>
<?php } ?>
<?php if($p['typeid'] == 2) { ?>
<tr>
<td class="tl"><span class="f_red">*</span> 链接文字</td>
<td class="f_gray"><input type="text" size="50" name="post[text_name]" id="text_name" value="<?php echo $text_name;?>"/> <?php echo dstyle('post[text_style]', $text_style);?> [支持HTML语法] <span id="dtext_name" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 链接地址</td>
<td><input type="text" size="50" name="post[text_url]" id="text_url" value="<?php echo $text_url;?>"/> <span id="dtext_url" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Title提示</td>
<td><input type="text" size="50" name="post[text_title]" value="<?php echo $text_title;?>"/></td>
</tr>
<?php } ?>
<?php if($p['typeid'] == 3 || $p['typeid'] == 5) { ?>
<tr>
<td class="tl"><span class="f_red">*</span> 广告图片</td>
<td>
<input type="hidden" name="post[image_src]" id="thumb" value="<?php echo $image_src;?>"/>
<div class="thumbu">
<div><img src="<?php if($image_src) { ?><?php echo $image_src;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,<?php echo $p['width'];?>,<?php echo $p['height'];?>, Dd('thumb').value);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $p['width'];?>,<?php echo $p['height'];?>, Dd('thumb').value);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 链接地址</td>
<td><input type="text" size="50" name="post[image_url]" value="<?php echo $image_url;?>" id="image_url"/> <span id="dimage_url" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 标题ALT</td>
<td><input type="text" size="50" name="post[image_alt]" value="<?php echo $image_alt;?>"/></td>
</tr>
<?php } ?>
<?php if($p['typeid'] == 4) { ?>
<tr>
<td class="tl"><span class="f_red">*</span> 视频地址</td>
<td class="f_gray"><input type="text" size="70" name="post[video_src]" id="video" value="<?php echo $video_src;?>" onblur="UpdateURL();"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dfile(<?php echo $moduleid;?>, Dd('video').value, 'video', 'mp4');"/>
<img src="<?php echo DT_STATIC;?>image/ico-play.png" title="预览" onclick="Dplay();"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('video').value='';"/>
</span>
<span id="dvideo" class="f_red"></span></td>
<?php load('player.js');?>
<?php load('url2video.js');?>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 自动播放</td>
<td>
<label><input type="radio" name="post[video_auto]" value="1" <?php if($video_auto == 1) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[video_auto]" value="0" <?php if($video_auto == 0) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 循环播放</td>
<td>
<label><input type="radio" name="post[video_loop]" value="1" <?php if($video_loop == 1) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[video_loop]" value="0" <?php if($video_loop == 0) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 链接地址</td>
<td><input type="text" size="50" name="post[video_url]" value="<?php echo $video_url;?>"/></td>
</tr>
<?php } ?>
<?php if($p['typeid'] == 6) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 所属模块</td>
<td class="f_gray">&nbsp;<?php echo $MODULE[$p['moduleid']]['name'];?><?php tips('如果行业与关键字未设置，则参与'.$MODULE[$p['moduleid']]['name'].'首页列表排名');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 所属行业</td>
<td><?php echo ajax_category_select('post[key_catid]', '请选择', $key_catid, $p['moduleid']);?><?php tips('如果选择，则参与行业列表排名');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 关键字</td>
<td><input type="text" size="30" name="post[key_word]" value="<?php echo $key_word;?>"/><?php tips('如果填写，则参与搜索结果排名<br/>请勿过长，建议控制10个汉字内');?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 信息ID</td>
<td><input type="text" size="10" name="post[key_id]" id="key_id" value="<?php echo $key_id;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-sort.png" width="11" height="11" title="选择信息ID" class="c_p" onclick="select_item('<?php echo $p['moduleid'];?>&itemid='+Dd('key_id').value);"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-link.png" width="11" height="11" title="打开信息" class="c_p" onclick="if(Dd('key_id').value){window.open('<?php echo gourl('?mid='.$p['moduleid'].'&itemid=');?>'+Dd('key_id').value);}else{Dmsg('请填写信息ID','key_id');}"/> <span id="dkey_id" class="f_red"></span></td>
</tr>
<?php } ?>
<?php if($p['typeid'] == 7) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 所属模块</td>
<td class="f_gray">&nbsp;<?php echo $MODULE[$p['moduleid']]['name'];?><?php tips('如果行业与关键词未设置，则显示在'.$MODULE[$p['moduleid']]['name'].'首页');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 所属分类</td>
<td><?php echo ajax_category_select('post[key_catid]', '请选择', $key_catid, $p['moduleid']);?><?php tips('如果选择，则显示在列表页');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 关键词</td>
<td><input type="text" size="30" name="post[key_word]" value="<?php echo $key_word;?>"/><?php tips('如果填写，则显示在搜索结果<br/>请勿过长，建议控制10个汉字内');?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 广告代码</td>
<td><textarea name="post[code]" id="code" style="width:98%;height:150px;overflow:visible;" class="f_fd"><?php echo $code;?></textarea><br/>
<input type="button" value=" 运行代码 " class="btn" onclick="runcode();"/> <span id="dcode" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 上传文件</td>
<td class="f_gray"><input type="text" size="70" id="upload"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dfile(<?php echo $moduleid;?>, Dd('upload').value, 'upload', '<?php echo $DT['uploadtype'];?>');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('upload').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-copy.png" title="复制" data-clipboard-action="copy" data-clipboard-target="#upload" onclick="if(Dd('upload').value) Dtoast('文件地址已复制');"/>
</span>
<?php tips('从这里上传文件后，把地址复制到代码里即可使用');?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">var clipboard = new Clipboard('[data-clipboard-action]');</script>
</td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> 投放时段</td>
<td><?php echo dcalendar('post[fromtime]', $fromtime, '-', 1);?> 至 <?php echo dcalendar('post[totime]', $totime, '-', 1);?>&nbsp;
<select onchange="Dd('posttotime').value=this.value;">
<option value="">快捷选择</option>
<?php $FTIME = datetotime($fromtime);?>
<option value="<?php echo timetodate($FTIME+86400*7, 3);?> 23:59:59">一周</option>
<option value="<?php echo timetodate($FTIME+86400*15, 3);?> 23:59:59">半月</option>
<option value="<?php echo timetodate($FTIME+86400*30, 3);?> 23:59:59">一月</option>
<option value="<?php echo timetodate($FTIME+86400*90, 3);?> 23:59:59">三月</option>
<option value="<?php echo timetodate($FTIME+86400*182, 3);?> 23:59:59">半年</option>
<option value="<?php echo timetodate($FTIME+86400*365, 3);?> 23:59:59">一年</option>
<option value="<?php echo timetodate($FTIME+86400*365*2, 3);?> 23:59:59">二年</option>
<option value="<?php echo timetodate($FTIME+86400*365*3, 3);?> 23:59:59">三年</option>
</select>&nbsp;<span id="dtime" class="f_red"></span></td>
</tr>
<?php if($DT['city']) { ?>
<tr style="display:<?php echo $_areaids ? 'none' : '';?>;">
<td class="tl"><span class="f_hid">*</span> 地区(分站)</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员名</td>
<td><input name="post[username]" type="text" size="20" value="<?php echo $username;?>" id="ad_username"/>&nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('ad_username').value);"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注</td>
<td><input name="post[note]" type="text" size="50" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 点击统计</td>
<td>
<label><input type="radio" name="post[stats]" value="1" <?php if($stats) echo 'checked';?>/> 开启</label>&nbsp; &nbsp; &nbsp; &nbsp;
<label><input type="radio" name="post[stats]" value="0" <?php if(!$stats) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 点击次数</td>
<td><input type="text" size="5" name="post[hits]" value="<?php echo $hits;?>"/></td>
</tr>
<?php if($action == 'edit') { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告位ID</td>
<td><input name="post[pid]" type="text" size="5" value="<?php echo $p['pid'];?>"/><?php tips('修改广告位ID可以移动此广告至其他广告位，必须在同类广告位之间移动');?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status==3) echo 'checked';?>/> 已通过</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[status]" value="2" <?php if($status==2) echo 'checked';?>/> 审核中</label>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=list&pid=<?php echo $pid;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	var t = <?php echo $p['typeid'];?>;
	f = 'title';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写广告名称', f);
		return false;
	}
	if(Dd('postfromtime').value.length != 19 || Dd('posttotime').value.length != 19) {
		Dmsg('请填写投放时段', 'time');
		return false;
	}
	if(t == 1 || t == 7) {
		f = 'code';
		l = Dd(f).value.length;
		if(l < 5) {
			Dmsg('请填写广告代码', f);
			return false;
		}
	} else if(t == 2) {
		f = 'text_name';
		l = Dd(f).value.length;
		if(l < 2) {
			Dmsg('请填写链接文字', f);
			return false;
		}
		f = 'text_url';
		l = Dd(f).value.length;
		if(l < 12) {
			Dmsg('请填写链接地址', f);
			return false;
		}
	} else if(t == 3 || t == 5) {
		f = 'thumb';
		l = Dd(f).value.length;
		if(l < 2) {
			Dmsg('请填写图片地址', f);
			return false;
		}
	} else if(t == 4) {
		f = 'video';
		l = Dd(f).value.length;
		if(l < 5) {
			Dmsg('请填写视频地址', f);
			return false;
		}
	} else if(t == 6) {
		f = 'key_id';
		l = Dd(f).value.length;
		if(l < 1) {
			Dmsg('请填写信息ID', f);
			return false;
		}
	}
	return true;
}
function runcode() {
	if(Dd('code').value.length < 3) {
		Dmsg('请填写代码', 'code');
		return false;
	}
	Dd('codes').value = Dd('code').value;
	Dd('runcode_form').submit();
}
function Dplay() {
	UpdateURL();
	if(Dd('video').value.length > 5) {
		mkDialog('', '<div style="width:<?php echo $p['width'];?>px;height:<?php echo $p['height'];?>px;background:#000000;">'+player(Dd('video').value,<?php echo $p['width'];?>,<?php echo $p['height'];?>,1)+'</div>', '视频预览', <?php echo $p['width'];?>);
	} else {
		Dmsg('视频地址为空', 'video');
	}
}
function UpdateURL() {
	var str = url2video(Dd('video').value);
	if(str) Dd('video').value = str;
}
Menuon(<?php echo $menuid;?>);
</script>
<?php include tpl('footer');?>