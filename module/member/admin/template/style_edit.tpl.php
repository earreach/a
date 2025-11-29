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
<td class="tl"><span class="f_hid">*</span> 模板分类</td>
<td><span id="type_box"><?php echo type_select($TYPE, 1, 'post[typeid]', '请选择分类', $typeid, 'id="typeid"');?></span> <a href="javascript:var type_item='<?php echo $file;?>',type_name='post[typeid]',type_default='请选择分类',type_id=<?php echo $typeid;?>,type_interval=setInterval('type_reload()',500);Dwidget('?file=type&item=<?php echo $file;?>', '模板分类');"><img src="<?php echo DT_STATIC;?>image/ico-add.png" width="11" height="11" title="管理分类"/></a> <span id="dtypeid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 模板名称</td>
<td><input name="post[title]" type="text" id="title" size="30" value="<?php echo $title;?>"/> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 风格目录</td>
<td>
<input name="post[skin]" type="text" id="skin" size="30" value="<?php echo $skin;?>"/>&nbsp;
<select onchange="if(this.value){Dd('skin').value=this.value;Dskin(this.value);}">
<option value="">快捷选择</option>
<?php
$dirs = list_dir('static/home');
foreach($dirs as $v) {
	if(!is_file(DT_ROOT.'/static/home/'.$v['dir'].'/style.css')) continue;
	echo '<option value="'.$v['dir'].'"'.($skin == $v['dir'] ? ' selected' : '').'>'.$v['dir'].($v['name'] == $v['dir'] ? '' : ' / '.$v['name']).'</option>';
}
?>
</select>
<?php tips('请上传目录至 ./static/homepage/ 名称为数字、字母组合');?> <span id="dskin" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 模板目录</td>
<td>
<input name="post[template]" type="text" id="template" size="30" value="<?php echo $template;?>"/>&nbsp;
<select onchange="if(this.value){Dd('template').value=this.value;}">
<option value="">快捷选择</option>
<?php
$d = DT_ROOT.'/template/'.$CFG['template'].'/';
$dirs = list_dir('template/'.$CFG['template']);
foreach($dirs as $v) {
	if(!is_file(DT_ROOT.'/template/'.$CFG['template'].'/'.$v['dir'].'/main_elite.htm')) continue;
	echo '<option value="'.$v['dir'].'"'.($template == $v['dir'] ? ' selected' : '').'>'.$v['dir'].($v['name'] == $v['dir'] ? '' : ' / '.$v['name']).'</option>';
}
?>
</select>
<?php tips('请上传目录至 ./template/'.$CFG['template'].'/ 名称为数字、字母组合');?> <span id="dtemplate" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 截图预览</td>
<td id="preview"></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 模板作者</td>
<td><input name="post[author]" type="text" size="20" value="<?php echo $author;?>" /></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员组</td>
<td><?php echo group_checkbox('post[groupid][]', $groupid, '1,2,3,4');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 价格(/月)</td>
<td><input name="post[fee]" type="text" size="10" value="<?php echo $fee;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 收费方式</td>
<td>
<label><input type="radio" name="post[currency]" value="money"<?php if($currency == 'money') echo ' checked';?>/> <?php echo $DT['money_name'];?></label>&nbsp;&nbsp;
<label><input type="radio" name="post[currency]" value="credit"<?php if($currency == 'credit') echo ' checked';?>/> <?php echo $DT['credit_name'];?></label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 手机模板</td>
<td>
<label><input type="radio" name="post[mobile]" value="1"<?php if($mobile == 1) echo ' checked';?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="post[mobile]" value="0"<?php if($mobile == 0) echo ' checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 人气</td>
<td><input type="text" size="10" name="post[hits]" value="<?php echo $hits;?>"/></td>
</tr>
<tr title="请保持时间格式">
<td class="tl"><span class="f_hid">*</span> 安装时间</td>
<td><?php echo dcalendar('post[addtime]', $addtime, '-', 1);?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '安 装';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function Dskin(i) {
	var nopic = '<?php echo DT_STATIC;?>home/image/thumb.gif';
	Dd('preview').innerHTML = '<img src="<?php echo DT_STATIC;?>home/'+i+'/thumb.gif" onerror="this.src=\''+nopic+'\';"/>';
}
Dskin('<?php echo $skin;?>');
function check() {
	var f;
	f = 'title';
	if(Dd(f).value == '') {
		Dmsg('请填写模板名称', f);
		return false;
	}
	f = 'skin';
	if(Dd(f).value == '') {
		Dmsg('请填写风格风格目录', f);
		return false;
	}
	f = 'template';
	if(Dd(f).value == '') {
		Dmsg('请填写模板目录', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>