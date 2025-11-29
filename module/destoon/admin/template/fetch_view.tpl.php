<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 测试网址</td>
<td><input name="url" type="text" size="80" value="<?php echo $url;?>" id="url"/>&nbsp;&nbsp;<input type="submit" value="测试规则" class="btn-g"/><?php if($itemid) { ?>&nbsp;&nbsp;<input type="button" value="修改规则" class="btn" onclick="Dwidget('?file=<?php echo $file;?>&action=edit&itemid=<?php echo $itemid;?>', '修改规则');"/><?php } ?> <span id="durl" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 解析结果</td>
<td>
<textarea style="width:98%;height:500px;" class="f_fd">
<?php 
if($msg) {
	echo '【'.$msg.'】'."\n";
	echo '--------------------------------------------'."\n";
}
foreach($post as $k=>$v) {
	echo '【'.$list[$k].'】 '.$k."\n";
	echo '--------------------------------------------'."\n";
	echo $v."\n";
	echo '--------------------------------------------'."\n";
} 
?>
</textarea>
</td>
</tr>
<?php if($html) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 网页源码</th>
<td><textarea style="width:98%;height:500px;" class="f_fd"><?php echo $html;?></textarea></td>
</tr>
<?php } ?>
</table>
</form>
<script type="text/javascript">
function check() {
	if(Dd('url').value.length < 10) {
		Dmsg('请填写测试网址', 'url');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>