<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="gradeid" value="<?php echo $gradeid;?>"/>
<input type="hidden" name="tab" id="tab" value="<?php echo $tab;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">允许个人空间</td>
<td>
<label><input type="radio" name="setting[space]" value="1" <?php if($space) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[space]" value="0" <?php if(!$space) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">个人空间模块</td>
<td>
<ul class="mods">
<?php
	$spaceids = explode(',', $spaceids);
	foreach($MODULE as $m) {
		if($m['moduleid'] > 4 && is_file(DT_ROOT.'/module/'.$m['module'].'/my.inc.php')) {
			echo '<li><label><input type="checkbox" name="setting[spaceids][]" value="'.$m['moduleid'].'" '.(in_array($m['moduleid'], $spaceids) ? 'checked' : '').'/> '.$m['name'].'</label></li>';
		}
	}
?>
</ul>
</td>
</tr>
<tr>
<td class="tl">空间默认模板</td>
<td><?php echo tpl_select('space', 'company', 'setting[template_space]', '默认模板', $template_space);?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="保 存" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="window.parent.location.reload();"/></div>
</form>
<script type="text/javascript">
var tab = <?php echo $tab;?>;
var all = <?php echo $all;?>;
$(function(){
	if(tab) Tab(tab);
	if(all) {all = 0; TabAll();}
	if(window.screen.width < 1280) {
		$('.menu div').hide();
	}
});
</script>
<?php include tpl('footer');?>