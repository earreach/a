<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 禁止内容</td>
<td><input type="text" size="40" name="ip" id="ip" value="<?php echo $ip;?>"/> <span id="dip" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="lh20 f_gray">
&nbsp;- 支持填写IP或IP段，例如填192.168.*.*将禁用所有192.168开头的IP<br/>
&nbsp;- 支持填写客户端的特征代码，例如Yisou，可以禁止包含Yisou的客户端访问<br/>
&nbsp;- 客户端的特征代码尽量简短，具体可以在流量统计或站点日志里分析<br/>
&nbsp;- 禁止仅对网站前台生效，建议不要添加过多，以免影响程序效率<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 有效时间</td>
<td><?php echo dcalendar('totime', $totime, '-', 1);?>&nbsp;
<select onchange="Dd('totime').value=this.value;">
<option value="">快捷选择</option>
<option value="">永久禁用</option>
<option value="<?php echo timetodate($DT_TIME, 3);?> 23:59:59">今天</option>
<option value="<?php echo timetodate($DT_TIME+86400*3, 3);?> 23:59:59">三天</option>
<option value="<?php echo timetodate($DT_TIME+86400*7, 3);?> 23:59:59">一周</option>
<option value="<?php echo timetodate($DT_TIME+86400*15, 3);?> 23:59:59">半月</option>
<option value="<?php echo timetodate($DT_TIME+86400*30, 3);?> 23:59:59">一月</option>
<option value="<?php echo timetodate($DT_TIME+86400*182, 3);?> 23:59:59">半年</option>
<option value="<?php echo timetodate($DT_TIME+86400*365, 3);?> 23:59:59">一年</option>
</select>&nbsp;
<span id="dtotime" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="lh20 f_gray">
&nbsp;有效时间不填表示永久禁用<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><input type="text" size="60" name="note" id="note" value="<?php echo $note;?>"/> <span id="dnote" class="f_red"></span></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $itemid ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $itemid ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function check() {
	if(Dd('ip').value.length < 3 || Dd('ip').value.length > 50) {
		Dmsg('禁止内容限3-50字符', 'ip');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $itemid ? 1 : 0;?>);</script>
<?php include tpl('footer');?>