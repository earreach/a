<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 网站分类</td>
<td><span id="type_box"><?php echo type_select($TYPE, 1, 'post[typeid]', '请选择分类', $typeid, 'id="typeid"');?></span> <a href="javascript:var type_item='<?php echo $file;?>',type_name='post[typeid]',type_default='请选择分类',type_id=<?php echo $typeid;?>,type_interval=setInterval('type_reload()',500);Dwidget('?file=type&item=<?php echo $file;?>', '链接分类');"><img src="<?php echo DT_STATIC;?>image/ico-add.png" width="11" height="11" title="管理分类"/></a> <span id="dtypeid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 网站名称</td>
<td><input name="post[title]" type="text" id="title" size="70" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level);?> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 网站地址</td>
<td><input name="post[linkurl]" type="text" id="linkurl" size="70" value="<?php echo $linkurl;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-link.png" width="11" height="11" title="打开链接" class="c_p" onclick="if(Dd('linkurl').value.length>10){window.open('<?php echo gourl('?url=');?>'+encodeURIComponent(Dd('linkurl').value));}else{Dmsg('请输入链接地址', 'linkurl');}"/> &nbsp; <span id="dlinkurl" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 网站LOGO</td>
<td>
<input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php if($thumb) { ?><?php echo $thumb;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,88,31,Dd('thumb').value);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,88,31,Dd('thumb').value);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 网站介绍</td>
<td><textarea name="post[introduce]" style="width:400px;height:40px;"><?php echo $introduce;?></textarea>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 链接状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status==3) echo 'checked';?>/> 通过</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[status]" value="2" <?php if($status==2) echo 'checked';?>/> 待审</label>
</td>
</tr>
<?php if($DT['city']) { ?>
<tr style="display:<?php echo $_areaids ? 'none' : '';?>;">
<td class="tl"><span class="f_hid">*</span> 地区(分站)</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?><?php echo $status == 2 ? '&action=check' : '';?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'typeid';
	l = Dd(f).value;
	if(l == 0) {
		Dmsg('请选择分类', f);
		return false;
	}
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请输入网站名称', f);
		return false;
	}
	f = 'linkurl';
	l = Dd(f).value.length;
	if(l < 12) {
		Dmsg('请输入网站地址', f);
		return false;
	}
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>