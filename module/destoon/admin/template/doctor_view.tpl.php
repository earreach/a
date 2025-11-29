<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<style>#content {width:96%;padding:10px;height:300px;background:#2E2E2E;color:#FFFFFF;border:none;outline:none;font-family:'Monaco','Menlo','Ubuntu Mono','Consolas','source-code-pro','monospace';}</style>
<div class="f_fd" style="padding:16px;"><span class="f_r c_p"><img src="<?php echo DT_STATIC;?>admin/tool-reload.png" width="16" height="16" title="刷新" onclick="window.location.reload();" alt=""/></span><img src="file/ext/<?php echo $ico;?>.gif" alt="" align="absmiddle"/> <?php echo str_replace(DT_ROOT, '', $filepath);?></div>
<div style="background:#2E2E2E;"><textarea name="content" id="content"><?php echo $content;?></textarea></div>
<div class="btns" style="border:none;">
<input type="button" value="确 定" class="btn-g" onclick="window.parent.cDialog();"/> &nbsp; &nbsp; 
<input type="button" value="刷 新" class="btn" onclick="window.location.reload();"/> &nbsp; &nbsp; 
<input type="button" value="下 载" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=down&auth=<?php echo $auth;?>');"/> &nbsp; &nbsp; 
<input type="button" value="关 闭" class="btn" onclick="window.parent.cDialog();"/> &nbsp; &nbsp; 
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#content').css({'width':parseInt($('html').width()-20)+'px','height':parseInt($('html').height()-140)+'px'});
});
</script>
<?php include tpl('footer');?>