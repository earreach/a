<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr ondblclick="window.location.reload();">
<td class="tl"><span class="f_hid">*</span> 来源网址</th>
<td><a href="<?php echo gourl($linkurl);?>" target="_blank" class="t"><?php echo $linkurl;?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 解析结果</th>
<td>
<textarea style="width:98%;height:500px;" class="f_fd">
<?php 
if($msg) {
	if($msg == 'ok') {
		echo '【数据发布】验证通过'."\n";
	} else {
		echo '【数据发布】验证失败  原因：'.$msg."\n";
	}
	echo '--------------------------------------------'."\n";
}
foreach($post as $k=>$v) {
	echo '【'.$setting[$k]['nm'].'】 '.$k."\n";
	echo '--------------------------------------------'."\n";
	echo $v."\n";
	echo '--------------------------------------------'."\n";
} 
?>
</textarea>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 网页源码</th>
<td><textarea style="width:98%;height:500px;" class="f_fd"><?php echo $html;?></textarea></td>
</tr>
</table>
<?php include tpl('footer');?>