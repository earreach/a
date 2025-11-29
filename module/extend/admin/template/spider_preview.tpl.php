<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr ondblclick="window.location.reload();">
<th width="50%">抓取网址</th>
<th>采集内容</th>
</tr>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<tr>
<td title="列表地址，抓取网址，修改后按回车提交测试"><input name="list_url" type="text" value="<?php echo $list_url;?>" style="width:98%;" class="f_fd"/></td>
<td title="内容地址，采集内容，修改后按回车提交测试"><input name="show_url" type="text" value="<?php echo $show_url;?>" style="width:98%;" class="f_fd"/>
<input type="submit" name="submit" value="提交" class="dsn"/></td>
</tr>
</form>
<tr>
<th>网址列表</th>
<th>解析结果</th>
</tr>
<tr>
<td>
<textarea style="width:98%;height:500px;" class="f_fd">
<?php 
foreach($lists as $k=>$v) {
	echo $v['linkurl'].($v['title'] ? ' '.$v['title'] : '')."\n";
} 
?>
</textarea>
</td>
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
<th>列表源码</th>
<th>内容源码</th>
</tr>
<tr>
<td><textarea style="width:98%;height:500px;" class="f_fd"><?php echo $list_html;?></textarea></td>
<td><textarea style="width:98%;height:500px;" class="f_fd"><?php echo $show_html;?></textarea></td>
</tr>
</table>
<?php include tpl('footer');?>