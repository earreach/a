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
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 采集标题</td>
<td><input name="post[title]" type="text" id="title" size="70" value="<?php echo $title;?>"/> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">建议使用“网站名称-频道名称-栏目名称”</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 采集网址</td>
<td><input name="post[linkurl]" type="text" id="linkurl" size="70" value="<?php echo $linkurl;?>"/> <span id="dlinkurl" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">一般为对应列表的第一页网址</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 采入目标</td>
<td>
<select onchange="if(this.value){$('#tb').val($('#mid option:selected').attr('tb'));loadc(this.value);}" name="post[mid]" id="mid">
<option value="0" tb="">模块</option>
<option value="2" tb="<?php echo get_table(2);?>"<?php echo $mid == 2 ? ' selected' : '';?>>会员</option>
<?php 
foreach($MODULE as $m) {
	if($m['moduleid'] > 4 && !$m['islink']) {
?>
<option value="<?php echo $m['moduleid'];?>" tb="<?php echo get_table($m['moduleid']);?>"<?php echo $mid == $m['moduleid'] ? ' selected' : '';?>><?php echo $m['name'];?></option>
<?php
	}
}
?>
</select>
<select name="post[tb]" id="tb" onchange="if(this.value){$('#name').val($('#tb option:selected').attr('note'));}">
<option value="" note="">数据表</option>
<?php 
foreach($tables as $t) {
?>
<option value="<?php echo $t['name'];?>" note="<?php echo $t['note'];?>"<?php echo $tb == $t['name']  ? ' selected' : '';?>><?php echo $t['name'].' ('.$t['note'].')';?></option>
<?php
}
?>
</select>
<input type="hidden" name="post[name]" value="<?php echo $name;?>" id="name"/>
<span id="dmid" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
一般选择模块即可，如果不采入模块，可以选择具体的表<br/>
选定之后请不要频繁修改，否则采集规则需要重新设置
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 目标分类</td>
<td><?php echo ajax_category_select('post[catid]', '选择分类', $catid, $mid);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注说明</td>
<td><textarea name="post[content]" id="content" style="width:600px;height:160px;"><?php echo $content;?></textarea></td>
</tr>
<tr title="请保持时间格式">
<td class="tl"><span class="f_hid">*</span> 添加时间</td>
<td><?php echo dcalendar('post[addtime]', $addtime, '-', 1);?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	$('#name').val($('#tb option:selected').attr('note'));
	var l;
	var f;
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写采集标题', f);
		return false;
	}
	f = 'linkurl';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请填写采集网址', f);
		return false;
	}
	f = 'mid';
	l = Dd(f).value;
	if(l < 1 && Dd('tb').value.length < 3) {
		Dmsg('请选择采入目标', f);
		return false;
	}
	return true;
}
function loadc(i) {
	if(i) {
		category_moduleid[1] = i;
		load_category(0, 1);
	}
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>