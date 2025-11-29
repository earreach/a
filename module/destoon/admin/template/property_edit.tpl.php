<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
<input type="hidden" name="post[catid]" value="<?php echo $catid;?>"/>
<input type="hidden" name="oid" value="<?php echo $oid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 属性名称</td>
<td><input name="post[name]" type="text"  size="30" id="name" value="<?php echo $name;?>"/> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">建议使用中文，例如 颜色</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 添加方式</td>
<td>
<?php
foreach($TYPE as $k=>$v) { 
?>
<label><input type="radio" name="post[type]" value="<?php echo $k;?>" id="t_<?php echo $k;?>" onclick="c(<?php echo $k;?>)" <?php echo $k == $type ? 'checked' : '';?>/> <?php echo $v;?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<?php }?>
</td>
</tr>
<tr style="display:">
<td class="tl" id="v_l"><span class="f_hid">*</span> 默认值</td>
<td><textarea name="post[value]" style="width:80%;height:30px;overflow:visible;" id="value"><?php echo $value;?></textarea></td>
</tr>
<tr id="v_t" style="display:none;">
<td class="tl"></td>
<td class="ts" id="v_r"></td>
</tr>
<tr style="display:none;" id="s_c">
<td class="tl"><span class="f_red">*</span> 参与搜索</td>
<td>
<label><input type="radio" name="post[search]" value="1" <?php echo $search == 1 ? 'checked' : '';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[search]" value="0" <?php echo $search == 0 ? 'checked' : '';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">仅列表选择(select)和复选框(checkbox)类型可以直接参与搜索</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 是否必填</td>
<td>
<label><input type="radio" name="post[required]" value="1" <?php echo $required == 1 ? 'checked' : '';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[required]" value="0" <?php echo $required == 0 ? 'checked' : '';?>/> 否</label>
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
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&catid=<?php echo $catid;?>');"/></div>
</form>
<script type="text/javascript">
function c(id) {
	if(id == 2 || id == 3) {
		Dd('v_l').innerHTML = '<span class="f_red">*</span> 备选值';
		Dd('v_r').innerHTML = '多个选项用 | 分隔，例如 红色|绿色(*)|蓝色 (*)表示默认选中';
		Ds('v_t');
		Ds('s_c');
	} else if(id == 0 || id == 1) {
		Dd('v_l').innerHTML = '<span class="f_hid">*</span> 默认值';
		Dd('v_r').innerHTML = '';
		Dh('v_t');
		Dh('s_c');
	}
}
c(<?php echo $type;?>);
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写属性名称', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $action=='add' ? 0 : 1;?>);</script>
<?php include tpl('footer');?>