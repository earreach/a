<?php
defined('DT_ADMIN') or exit('Access Denied');
include template('header');
load('clipboard.min.js');
?>
<style type="text/css">
.tag {padding:16px 24px;}
.tag textarea {box-sizing:border-box;width:100%;font-family:Consolas;height:48px;border:#CCCCCC 1px solid;outline:none;}
.tag textarea:hover {border:#666666 1px solid;}
</style>
<div class="m">
	<div class="head-txt"><a href="javascript:;" onclick="window.close();"><span>关闭</span></a><strong>标签预览</strong></div>
	<div class="tag"><?php echo $code_eval;?></div>

	<div class="head-txt"><a href="javascript:;" data-clipboard-action="copy" data-clipboard-target="#code_html" onclick="Dtoast('HTML调用代码已复制');"><span>复制</span></a><strong>HTML调用代码</strong></div>
	<div class="tag"><textarea id="code_html" style="height:64px;"><?php echo $code_call;?></textarea></div>

	<div class="head-txt"><a href="javascript:;" data-clipboard-action="copy" data-clipboard-target="#code_js" onclick="Dtoast('JS调用代码已复制');"><span>复制</span></a><strong>JS调用代码</strong></div>
	<div class="tag"><textarea id="code_js"><?php echo $tag_js;?></textarea></div>

	<div class="head-txt"><strong>调试结果</strong></div>
	<div class="tag"><textarea><?php echo $tag_debug;?></textarea></div>

	<div class="head-txt"><strong>源代码</strong></div>
	<div class="tag"><textarea style="height:<?php echo substr_count($code_eval, "\n")*18;?>px;"><?php echo $code_eval;?></textarea></div>
</div>
<script type="text/javascript">var clipboard = new Clipboard('[data-clipboard-action]');</script>
<?php
include template('footer');
?>