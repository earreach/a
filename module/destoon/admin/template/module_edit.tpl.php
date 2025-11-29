<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php if($action == 'add') { ?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="add"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 模块类型</td>
<td>
<label><input type="radio" name="post[islink]" value="0" onclick="Dd('link0').style.display='';Dd('link1').style.display='none';" id="islink" checked/> 内置模型</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[islink]" value="1" onclick="Dd('link0').style.display='none';Dd('link1').style.display='';"/> 外部链接</label></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 模块名称</td>
<td><input name="post[name]" type="text" id="name" size="30"/> <?php echo dstyle('post[style]');?> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 导航菜单</td>
<td><label><input type="radio" name="post[ismenu]" value="1" checked/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp; <label><input type="radio" name="post[ismenu]" value="0" /> 否</label></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 新窗口打开</td>
<td><label><input type="radio" name="post[isblank]" value="1"/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp; <label><input type="radio" name="post[isblank]" value="0" checked /> 否</label></td>
</tr>
<tbody id="link1" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 链接地址</td>
<td><input name="post[linkurl]" type="text" id="linkurl" size="70"/> <span id="dlinkurl" class="f_red"></span></td>
</tr>
</tbody>
<tbody id="link0" style="display:;">
<tr>
<td class="tl"><span class="f_red">*</span> 所属模型</td>
<td><?php echo $module_select;?> <span id="dmodule" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 安装目录</td>
<td><input name="post[moduledir]" type="text" id="moduledir" size="30"/> <?php tips('限英文、数字、中划线、下划线');?> <span id="dmoduledir" class="f_red"></span> </td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 电脑版绑定域名</td>
<td><input name="post[domain]" type="text" id="domain" size="70"/><?php tips('例如https://sell.destoon.com/,以 / 结尾<br/>如果不绑定请勿填写');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 手机版绑定域名</td>
<td><input name="post[mobile]" type="text" id="mobile" size="70"/><?php tips('例如https://m.sell.destoon.com/,以 / 结尾<br/>如果不绑定请勿填写');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> LOGO</td>
<td>
<input name="post[logo]" type="text" value="" id="logo" size="70" ondblclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('logo').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('logo').value='';"/>
</span>
<?php tips('模块独立LOGO，显示在电脑版左上角');?>
</td>
</tr>
</tbody>
<tr>
<td class="tl"><span class="f_hid">*</span> 模块图标</td>
<td>
<input name="post[icon]" type="text" value="" id="icon" size="70" ondblclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('icon').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('icon').value='';"/>
</span>
<?php tips('模块图标，显示在手机版首页和频道页，建议48x48透明PNG格式');?>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="添 加" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value;
	if(l == '') {
		Dmsg('请填写模块名称', f);
		return false;
	}
	if(Dd('islink').checked) {
		f = 'module';
		l = Dd(f).value;
		if(l == 0) {
			Dmsg('请选择所属模型', f);
			return false;
		}
		f = 'moduledir';
		l = Dd(f).value;
		if(l == '') {
			Dmsg('请填写安装目录', f);
			return false;
		}
	} else {
		f = 'linkurl';
		l = Dd(f).value.length;
		if(l < 2) {
			Dmsg('请填写链接地址', f);
			return false;
		}
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php } else { ?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="edit"/>
<input type="hidden" name="mid" value="<?php echo $mid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 模块类型</td>
<td><?php echo $islink ? '外部链接' : $modulename.'模型';?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 模块名称</td>
<td><input name="post[name]" type="text" id="name" size="30" value="<?php echo $name;?>"/> <?php echo dstyle('post[style]', $style);?> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 导航菜单</td>
<td><label><input type="radio" name="post[ismenu]" value="1" <?php if($ismenu) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp; <label><input type="radio" name="post[ismenu]" value="0"  <?php if(!$ismenu) echo 'checked';?>/> 否</label></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 新窗口打开</td>
<td><label><input type="radio" name="post[isblank]" value="1" <?php if($isblank) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp; <label><input type="radio" name="post[isblank]" value="0"  <?php if(!$isblank) echo 'checked';?>/> 否</label></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 禁用模块</td>
<td><label><input type="radio" name="post[disabled]" value="1" <?php if($disabled) echo 'checked';?><?php if($mid < 5) {?> disabled<?php } ?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp; <label><input type="radio" name="post[disabled]" value="0"  <?php if(!$disabled) echo 'checked';?>/> 否</label></td>
</tr>
<?php if($islink) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 链接地址</td>
<td><input name="post[linkurl]" type="text" id="linkurl" size="70" value="<?php echo $linkurl;?>"/> <span id="dlinkurl" class="f_red"></span></td>
</tr>
<?php } else { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 安装目录</td>
<td><input name="post[moduledir]" type="text" id="moduledir" size="30" value="<?php echo $moduledir;?>"<?php if($mid == 4) {?> disabled<?php } ?>/> <?php tips('限英文、数字、中划线、下划线');?> <span id="dmoduledir" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 系统提示</td>
<td class="f_red">如果不是十分必要，建议不要频繁更改安装目录
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 电脑版绑定域名</td>
<td><input name="post[domain]" type="text" id="domain" size="70" value="<?php echo $domain;?>"/><?php tips('例如https://sell.destoon.com/,以 / 结尾<br/>如果不绑定请勿填写');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 手机版绑定域名</td>
<td><input name="post[mobile]" type="text" id="mobile" size="70" value="<?php echo $mobile;?>"/><?php tips('例如https://m.sell.destoon.com/,以 / 结尾<br/>如果不绑定请勿填写');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> LOGO</td>
<td>
<input name="post[logo]" type="text" value="<?php echo $logo;?>" id="logo" size="70" ondblclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('logo').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('logo').value='';"/>
</span>
<?php tips('模块独立LOGO，显示在电脑版左上角');?>
</td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 模块图标</td>
<td>
<input name="post[icon]" type="text" value="<?php echo $icon;?>" id="icon" size="70" ondblclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('icon').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('icon').value='';"/>
</span>
<?php tips('模块图标，显示在手机版首页和频道页，建议48x48透明PNG格式');?>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value;
	if(l == '') {
		Dmsg('请填写模块名称', f);
		return false;
	}
<?php if($islink) { ?>
	f = 'linkurl';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写链接地址', f);
		return false;
	}
<?php } else { ?>
	f = 'moduledir';
	l = Dd(f).value;
	if(l == '') {
		Dmsg('请填写安装目录', f);
		return false;
	}
<?php } ?>
	return true;
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php } ?>
<?php include tpl('footer');?>