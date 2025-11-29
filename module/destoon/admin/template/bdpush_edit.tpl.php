<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 网址列表</td>
<td><textarea name="content" rows="20" cols="150" id="content"></textarea>
<br/><span id="dcontent" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="lh20 f_gray">
一行一个网址，网址对应的域名必须在百度站长平台已认证<br/>
已经推送成功的网址，系统会自动过滤，无需重新提交<br/>
所有模块内容页面在点击次数等于10时自动提交，无需手动提交<br/>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $itemid ? '修 改' : '推 送';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $itemid ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	if(Dd('content').value.length < 10) {
		Dmsg('请填写网址列表', 'content');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $itemid ? 1 : 0;?>);</script>
<?php include tpl('footer');?>