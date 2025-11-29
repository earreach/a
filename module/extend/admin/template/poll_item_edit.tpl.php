<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="pollid" value="<?php echo $pollid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 标题</td>
<td><input name="post[title]" type="text" id="title" size="70" value="<?php echo $title;?>"/> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 图片</td>
<td>
<input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php if($thumb) { ?><?php echo $thumb;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,<?php echo $P['thumb_width'];?>,<?php echo $P['thumb_height'];?>, Dd('thumb').value);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,<?php echo $P['thumb_width'];?>,<?php echo $P['thumb_height'];?>, Dd('thumb').value);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 链接</td>
<td><input name="post[linkurl]" type="text" id="linkurl" size="70" value="<?php echo $linkurl;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 简介</td>
<td><textarea name="post[introduce]" style="width:500px;height:100px;overflow:visible;" id="introduce"><?php echo $introduce;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 票数</td>
<td><input type="text" size="10" name="post[polls]" value="<?php echo $polls;?>" id="polls"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 排序</td>
<td><input type="text" size="10" name="post[listorder]" value="<?php echo $listorder;?>" id="listorder"/></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'item_edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=item&pollid=<?php echo $pollid;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('标题最少2字，当前已输入'+l+'字', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>