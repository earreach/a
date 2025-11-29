<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<input type="hidden" name="fid" value="<?php echo $fid;?>"/>
<input type="hidden" name="qid" value="<?php echo $qid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 选项名称</td>
<td><input name="post[name]" type="text"  size="30" id="name" value="<?php echo $name;?>"/> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">建议使用中文，例如 年龄</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 添加方式</td>
<td>
<select name="post[type]" id="type" onchange="c();">
<?php
foreach($TYPES as $k=>$v) {
	echo '<option value="'.$k.'"'.($k == $type ? ' selected' : '').'>'.$v.'</option>';
}
?>
</select>
</td>
</tr>
<tr>
<td class="tl" id="v_l"><span class="f_hid">*</span> 默认值</td>
<td><textarea name="post[value]" style="width:80%;height:30px;overflow:visible;" id="value"><?php echo $value;?></textarea>
<div class="tip" id="v_r"></div>
</td>
</tr>
<tr id="up" style="display:none;">
<td class="tl"><span class="f_hid">*</span> 上传图片</td>
<td class="f_gray"><input type="text" size="70" id="thumb"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(<?php echo $moduleid;?>, 0, 0, '', 1);"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('thumb').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-copy.png" title="复制" data-clipboard-action="copy" data-clipboard-target="#thumb" onclick="if(Dd('thumb').value) Dtoast('图片地址已复制');"/>
</span>
<?php tips('从这里上传图片后，把地址复制到选项里即可使用');?>
<?php load('clipboard.min.js');?>
<script type="text/javascript">var clipboard = new Clipboard('[data-clipboard-action]');</script>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 输入限制</td>
<td><input type="text" name="post[required]" id="required" size="20" value="<?php echo $required;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
直接填数字表示限制最小长度,如果要限制长度范围例如6到20之间,则填写 6-20<br/>
对于多选框,填非0数字表示必选个数 填长度范围表示必选个数范围<br/>
其他类型填非0数字表示必填或必选<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 扩展代码</td>
<td><textarea name="post[extend]" style="width:80%;height:30px;overflow:visible;"><?php echo $extend;?></textarea></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">可以添加表单属性、JS事件或CSS样式 如果有单引号请加 \</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 填写说明</td>
<td><textarea name="post[note]" style="width:80%;height:30px;overflow:visible;"><?php echo $note;?></textarea></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">字段填写说明或规则要求或其他提示信息，显示在字段输入框下方</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $job == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $job == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=question&fid=<?php echo $fid;?>');"/></div>
</form>
<script type="text/javascript">
function c() {
	var h = $('#type').val();
	if(h == 'select') {
		Dd('v_l').innerHTML = '<span class="f_red">*</span> 备选值';
		Dd('v_r').innerHTML = '多个选项用 | 分隔，例如 红色|绿色(*)|蓝色 (*)表示默认选中';
	} else if(h == 'checkbox' || h == 'radio') {
		Dd('v_l').innerHTML = '<span class="f_red">*</span> 备选值';
		Dd('v_r').innerHTML = '多个选项用 | 分隔，例如 红色|绿色(*)|蓝色 (*)表示默认选中<br/>如果选项为其他，其后会显示一个输入框<br/>如果选项为文字+图片地址，会自动显示图片 <span class="jt" onclick="$(\'#up\').show();">上传图片</span>';
	} else {
		Dd('v_l').innerHTML = '<span class="f_hid">*</span> 默认值';
		Dd('v_r').innerHTML = '';
	}
}
c();
function r(id) {
	if(id == 'notnull') {
		Dd('required').value = '1';
	} else if(id == 'numeric') {
		Dd('required').value = '[0-9]{1,}';
	} else if(id == 'letter') {
		Dd('required').value = '[a-z]{1,}';
	} else if(id == 'nl') {
		Dd('required').value = '[a-z0-9]{1,}';
	} else if(id == 'email') {
		Dd('required').value = 'is_email';
	} else if(id == 'date') {
		Dd('required').value = 'is_date';
	} else {
		Dd('required').value = '';
	}
}
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写选项名称', f);
		return false;
	}
	return true;
}
Menuon(<?php echo $menuid;?>);
</script>
<?php include tpl('footer');?>