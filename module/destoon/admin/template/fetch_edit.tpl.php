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
<td class="tl"><span class="f_red">*</span> 网站名称</td>
<td><input name="sitename" type="text" id="sitename" size="50" value="<?php echo $sitename;?>"/> <span id="dsitename" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">建议使用“网站名称-频道名称-栏目名称”</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 采编域名</td>
<td><input name="domain" type="text" id="domain" size="50" value="<?php echo $domain;?>"/> <span id="ddomain" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">不带http及目录，例如 www.abc.com</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 网址标识</td>
<td><input name="mark" type="text" id="mark" size="50" value="<?php echo $mark;?>"/> <span id="dmark" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">同域名不同频道可能规则不同，填写对应频道网址里的专有字符以区分</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 网页编码</td>
<td>
<label><input type="radio" name="encode" value="utf-8"<?php echo $encode == 'utf-8' ? ' checked' : '';?>/> UTF-8</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="encode" value="gbk"<?php echo $encode == 'gbk' ? ' checked' : '';?>/> GBK</label>
</td>
</tr>
</table>
<table cellspacing="0" class="tb ls">
<tr>
<th width="155">字段</th>
<th>名称</th>
<th>开始标志</th>
<th>结束标志</th>
<th data-hide-1200="1">查找</th>
<th data-hide-1200="1">替换为</th>
<th>默认值</th>
<th>处理函数</th>
</tr>
<?php for($k = 0; $k < $max; $k++) {?>
<tr align="center">
<td><input type="text" size="10" name="setting[<?php echo $k;?>][fk]" value="<?php echo isset($setting[$k]['fk']) ? $setting[$k]['fk'] : '';?>"/></td>
<td><input type="text" size="10" name="setting[<?php echo $k;?>][nm]" value="<?php echo isset($setting[$k]['nm']) ? $setting[$k]['nm'] : '';?>"/></td>
<td><textarea name="setting[<?php echo $k;?>][fm]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['fm']) ? $setting[$k]['fm'] : '';?></textarea></td>
<td><textarea name="setting[<?php echo $k;?>][to]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['to']) ? $setting[$k]['to'] : '';?></textarea></td>
<td data-hide-1200="1"><textarea name="setting[<?php echo $k;?>][fd]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['fd']) ? $setting[$k]['fd'] : '';?></textarea></td>
<td data-hide-1200="1"><textarea name="setting[<?php echo $k;?>][rp]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['rp']) ? $setting[$k]['rp'] : '';?></textarea></td>
<td><textarea name="setting[<?php echo $k;?>][vl]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['vl']) ? $setting[$k]['vl'] : '';?></textarea></td>
<td><textarea name="setting[<?php echo $k;?>][fc]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['fc']) ? $setting[$k]['fc'] : '';?></textarea></td>
</tr>
<?php }?>
</table>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"></td>
<td class="ts">
开始标志指对应字段数据在采集对象页面源码中的起始代码片段<br/>
结束标志指对应字段数据在采集对象页面源码中的结束代码片段<br/>
代码片段尽量找唯一性的标识，以免匹配不准确<br/>
匹配到的内容如果有多余内容，可以用查找替换功能处理，多项内容可以用“|”分隔<br/>
例如查找a|b|c，替换为1||3，代表a替换为1，b替换为空，c替换为3<br/>
默认值代表无需采集或采集失败的赋值，一般为数字或者字符串<br/>
默认值支持使用系统内的变量、常量或函数，此时需要用大括号，例如{$DT_TIME}代表当前时间UNIX时间戳<br/>
处理函数中，需要处理的参数设置为*，例如datatotime(*)表示将Y-m-d格式日期转换为UNIX时间戳<br/>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $itemid ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="if(window.parent.document.getElementById('Dtop')){window.parent.location.reload();}else{Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');}"/>
</form>
<script type="text/javascript">
function check() {
	if(Dd('sitename').value.length < 2) {
		Dmsg('请填写网站名称', 'domain');
		return false;
	}
	if(Dd('domain').value.length < 5) {
		Dmsg('请填写域名', 'domain');
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $itemid ? 1 : 0;?>);</script>
<?php include tpl('footer');?>