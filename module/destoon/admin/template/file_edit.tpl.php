<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<?php if($action == 'add') { ?>
<form method="post" id="dform" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 目录</td>
<td class="f_fd"><?php echo $dir;?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 文件名</td>
<td class="f_fd"><input type="text" size="30" name="name" id="name" value="<?php if(isset($type)) echo $type.'-';?>" class="f_fd"/><?php echo $file == 'template' ? '.htm' : '';?> <span id="dname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">只能为小写字母、数字、中划线、下划线</td>
</tr>
<tr>
<td class="tl"></td>
<td><input type="submit" name="submit" value="创 建" class="btn-g"/> &nbsp; &nbsp; <input type="button" value="取 消" class="btn" onclick="window.parent.cDialog();"/></td>
</tr>
</table>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'name';
	l = Dd(f).value;
	if(l.length < 1 || l.substring(l.length-1) == '-') {
		Dmsg('请填写文件名', f);
		return false;
	}
	return true;
}
</script>
<?php } else { ?>
<style>#editor * {font-family:'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;}</style>
<form method="post" action="?" id="dform">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<input type="hidden" name="name" value="<?php echo $name;?>"/>
<div class="f_fd" style="padding:16px 0 0 16px;"><img src="file/ext/<?php echo $ico;?>.gif" alt="" align="absmiddle"/> <?php echo str_replace(DT_ROOT, '', $filepath);?></div>
<div><pre id="editor"><?php echo $content;?></pre><textarea name="content" id="content" class="dsn"></textarea></div>
<div class="btns"><span class="f_r"><label><input type="checkbox" name="backup" value="1"<?php echo $backup ? ' checked' : '';?>/> 保存时，创建一个备份文件</label></span> &nbsp; <input type="button" value="保 存" class="btn-g" onclick="check();"/> &nbsp; &nbsp; <input type="button" value="取 消" class="btn" onclick="window.parent.location.reload();"/>
</div>
</form>
<script src="<?php echo DT_PATH;?>api/ace/src/ace.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script src="<?php echo DT_PATH;?>api/ace/src/ext-language_tools.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript">
var editor = ace.edit("editor", {
	theme: "ace/theme/tomorrow_night_eighties",//monokai chrome
	mode: "ace/mode/<?php echo $mode;?>",
	fontSize: 14,
	maxLines: 30,
	minLines: 30,
	wrap: true,
	showPrintMargin: false,
	autoScrollEditorIntoView: true,
	enableBasicAutocompletion: true,
	enableSnippets: true,
	enableLiveAutocompletion: true
});
function check() {
	Dd('content').value = editor.getSession().getValue();
	$('#dform').submit();
}
$(function(){
	$('body').keydown(function(e) {
		if(e.ctrlKey && e.keyCode == 83) {
			check();
			return false;
		}
	});
});
</script>
<?php } ?>
<?php include tpl('footer');?>