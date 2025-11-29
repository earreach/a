<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
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
<input type="hidden" name="pid" value="<?php echo $pid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 广告位名称</td>
<td><input name="post[name]" id="name" type="text" size="30" value="<?php echo $name;?>"/> <?php echo dstyle('post[style]', $style);?> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告位示意图</td>
<td><input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php if($thumb) { ?><?php echo $thumb;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,0,0, Dd('thumb').value,true);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,0,0, Dd('thumb').value,true);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告位介绍</td>
<td><input name="post[introduce]" type="text" size="70" value="<?php echo $introduce;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 广告位类型</td>
<td>
<?php foreach($TYPE as $k=>$v) {
	if($k) echo '<label><input type="radio" name="post[typeid]" value="'.$k.'" '.($k == $typeid ? 'checked' : '').' id="p'.$k.'" onclick="sh('.$k.');"/> '.$v.'&nbsp; &nbsp;</label>';
}
?>
<?php if($action == 'edit_place') { ?><?php tips('如果修改了广告位类型，请务必修改此广告位下所有广告');?><?php } ?>
</td>
</tr>
<tr id="wh" style="display:<?php echo $typeid == 3 || $typeid == 4 || $typeid == 5 ? '' : 'none';?>">
<td class="tl"><span class="f_red">*</span> 广告位大小</td>
<td><input name="post[width]" id="width" type="text" size="5" value="<?php echo $width;?>"/> px X <input name="post[height]" id="height" type="text" size="5" value="<?php echo $height;?>"/> px <span class="f_gray">[宽 X 高]</span> <span id="dsize" class="f_red"></span>
</td>
</tr>
<tr id="md" style="display:<?php echo $typeid == 6 || $typeid == 7 ? '' : 'none';?>">
<td class="tl"><span class="f_red">*</span> 所属模块</td>
<td><?php echo module_select('post[moduleid]', '请选择', $mid, 'id="mids"');?> <span id="dmids" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 广告位价格</td>
<td><input name="post[price]" type="text" size="10" value="<?php echo $price;?>"/> <?php echo $unit;?>/月 &nbsp; <span class="f_gray">0表示面议</span></td>
</tr>
<?php 
foreach($PTYPE as $k=>$v) {
	if($k != 'm1') {
?>
<tr>
<td class="tl"></td>
<td><input name="post[setting][<?php echo $k;?>]" type="text" size="10" value="<?php echo isset($$k) ? $$k : '';?>"/> <?php echo $unit;?>/<?php echo $v;?></td>
</tr>
<?php } } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 默认广告代码</td>
<td><textarea name="post[code]" id="code" style="width:98%;height:50px;overflow:visible;" class="f_fd"><?php echo $code;?></textarea><br/>
<input type="button" value=" 运行代码 " class="btn" onclick="runcode();"/> &nbsp; <span class="f_gray">当广告位下无广告时，显示此代码，支持html、css、js 如果广告位采用js调用，此处不建议使用js代码</span><span id="dcode" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 网站前台显示</td>
<td>
<label><input type="radio" name="post[open]" value="1" <?php if($open) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[open]" value="0" <?php if(!$open) echo 'checked';?>/> 否</label>&nbsp;&nbsp;&nbsp;&nbsp;<?php tips('如果选择否，将不在前台广告列表里显示，此时会员不能在线订购，并非不显示广告');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 显示广告字样</td>
<td>
<label><input type="radio" name="post[sign]" value="1" <?php if($sign) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[sign]" value="0" <?php if(!$sign) echo 'checked';?>/> 否</label>&nbsp;&nbsp;&nbsp;&nbsp;<?php tips('如果选择是，将在广告位右下角显示广告字样');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告代码模板</td>
<td><?php echo tpl_select('ad', 'chip', 'post[template]', '默认模板', $template, 'id="template"');?></td>
</tr>
<?php if($action == 'edit_place') { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 广告位ID</td>
<td><input name="post[pid]" type="text" size="5" value="<?php echo $pid;?>"/><?php tips('修改广告位ID可以恢复误删除的广告位，如非特殊情况，不建议修改');?></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit_place' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit_place' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function sh(id) {
	if(id == 6 || id == 7) {
		Ds('md');Dh('wh');
	} else if(id == 3 || id == 4 || id == 5) {
		Dh('md');Ds('wh');
	} else {
		Dh('md');Dh('wh');
	}
}
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写广告位名称', f);
		return false;
	}
	if(Dd('p3').checked || Dd('p4').checked || Dd('p5').checked) {
		if(Dd('width').value.length < 2 || Dd('height').value.length < 2) {
			Dmsg('请填写广告位大小', 'size');
			return false;
		}
	}
	if(Dd('p6').checked || Dd('p7').checked) {
		if(Dd('mids').value == 0) {
			Dmsg('请选择所属模块', 'mids');
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
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>