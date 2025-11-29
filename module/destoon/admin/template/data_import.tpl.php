<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" enctype="multipart/form-data" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="upload"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 导入说明</td>
<td style="line-height:32px;">
&nbsp;&nbsp;<a href="api/excel/demo.xls" class="b" target="_blank">点此下载</a>示例Excel文件模板，根据需要导入的表调整字段和录入数据<br/>
&nbsp;&nbsp;第一行为字段中文名，仅为方便录入，对导入数据无影响，可留空；第二行为数据表对应字段名，必须和数据表内字段一致，必须填写；第三行及以后的行需要录入待导入的数据<br/>
&nbsp;&nbsp;导入会员时，可以在第二行加入company和member_misc表的字段，系统会自动保存到对应内容表里<br/>
&nbsp;&nbsp;导入模块时，可以在第二行加入content字段，系统会自动保存到对应内容表里<br/>
&nbsp;&nbsp;如果数据为时间格式，建议将Excel单元格设置为文本格式，防止其对数据自动转换导致格式错误<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 数据文件</td>
<td><input name="uploadfile" id="uploadfile" type="file" size="25"/> <span class="f_gray">限*.xls</span> <span id="duploadfile" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 导入目标</td>
<td>
<select name="table" id="table">
<option value="">请选择</option>
<?php 
foreach($tables as $t) {
?>
<option value="<?php echo $t['name'];?>"><?php echo $t['name'].' ('.$t['note'].')';?></option>
<?php
}
?>
</select>
&nbsp;&nbsp;
<select onchange="if(this.value)$('#table').val(this.value);">
<option value="0">快捷选择</option>
<option value="<?php echo get_table(2);?>">会员</option>
<?php 
foreach($MODULE as $m) {
	if($m['moduleid'] > 4 && !$m['islink']) {
?>
<option value="<?php echo get_table($m['moduleid']);?>"><?php echo $m['name'];?></option>
<?php
	}
}
?>
<?php 
foreach($MODULE as $m) {
	if($m['module'] == 'quote') {
?>
<option value="<?php echo DT_PRE.'quote_price_'.$m['moduleid'];?>"><?php echo $m['name'];?>报价</option>
<?php
	}
}
?>
</select>
<span id="dtable" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"> </td>
<td><input type="submit" value="下一步" class="btn-g"/></td>
</tr>
</table>
</form>
<script type="text/javascript">
function check() {
	if(Dd('uploadfile').value.indexOf('.xls') == -1) {
		Dmsg('请上传xls数据文件', 'uploadfile');
		return false;
	}
	if(Dd('table').value == '') {
		Dmsg('请选择导入目标表', 'table');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(7);</script>
<?php include tpl('footer');?>