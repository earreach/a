<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 采集目标</td>
<td><a href="<?php echo gourl($s['linkurl']);?>" target="_blank" class="b"><?php echo $s['title'];?> <?php echo $s['linkurl'];?></a></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 网页编码</td>
<td>
<select name="setting[0][encode]">
<option value="UTF-8"<?php if($encode == 'UTF-8') echo ' selected';?>>UTF-8</option>
<option value="GBK"<?php if($encode == 'GBK') echo ' selected';?>>GBK</option>
</select>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分页规则</td>
<td><input name="setting[0][page_from]" type="text" size="50" value="<?php echo $page_from;?>"/>  <input name="setting[0][page_to]" type="text" size="15" value="<?php echo $page_to;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
例如：第二页的网址为https://www.abc.com/news/list-100-2.html<br/>
则分别填写 https://www.abc.com/news/list-100- &nbsp; .html<br/>
如果不设置，系统仅抓取采集网址最新更新的内容
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 翻页限制</td>
<td><input name="setting[0][page_max]" type="text" size="10" value="<?php echo $page_max;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
例如：设置为10，则第10页之后的内容不采集，如果是首次采集，可以填写实际的页码<br/>
全部采集一次之后建议填写1-5，系统自动只采集最新更新的内容<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 列表区域</td>
<td>
	<table cellspacing="0" class="ctb">
	<tr>
	<td><textarea name="setting[0][list_from]" style="width:200px;height:32px;" class="f_fd"><?php echo $list_from;?></textarea></td>
	<td>至</td>
	<td><textarea name="setting[0][list_to]" style="width:200px;height:32px;" class="f_fd"><?php echo $list_to;?></textarea></td>
	</tr>
	</table>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
分别填写列表区域开始标志html代码和结束标志html代码<br/>
如果不设置，系统会抓取采集网址页面的所有网址
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容页基准URL</td>
<td><input name="setting[0][show_basehref]" type="text" size="70" value="<?php echo $show_basehref;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
当列表里的内容网址链接为相对地址时，需要用基准URL补全为绝对地址才能继续抓取<br/>
系统会自动删除相对地址前面所有的../ ./ / 符号，基准URL需要以/结尾去连接<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 内容页网址包含</td>
<td><input name="setting[0][show_include]" type="text" size="70" value="<?php echo $show_include;?>" id="show_include"/> <span id="dshow_include" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
例如：内容页网址为https://www.abc.com/news/show-1.html 则填写，show-<br/>
如果有多个标志，且为与的关系，可以用&分隔，例如news&show&html，代表news show html 必须同时存在<br/>
如果有多个标志，且为或的关系，可以用|分隔，例如news|show|html，代表news show html 任意存在一个<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容页网址不包含</td>
<td><input name="setting[0][show_exclude]" type="text" size="70" value="<?php echo $show_exclude;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
如果匹配到了非内容页网址，可以通过非内容页网址特征来排除<br/>
例如：匹配到https://www.abc.com/sell/show-1.html 则填写，sell<br/>
如果有多个标志，且为与的关系，可以用&分隔，例如sell&list，代表sell list 必须同时存在<br/>
如果有多个标志，且为或的关系，可以用|分隔，例如sell|list，代表sell list 任意存在一个<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容页标题区域</td>
<td>
	<table cellspacing="0" class="ctb">
	<tr>
	<td><textarea name="setting[0][text_from]" style="width:200px;height:32px;" class="f_fd"><?php echo $text_from;?></textarea></td>
	<td>至</td>
	<td><textarea name="setting[0][text_to]" style="width:200px;height:32px;" class="f_fd"><?php echo $text_to;?></textarea></td>
	</tr>
	</table>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
列表内容页链接对应的标题文字可能包括图标、日期等不必要的内容<br/>
可以设置标题在链接代码中的范围排除不必要内容<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容页标题不包含</td>
<td><input name="setting[0][text_exclude]" type="text" size="70" value="<?php echo $text_exclude;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
如果匹配到了非内容页标题，可以通过非内容页标题特征来排除<br/>
例如：匹配到 详情 则填写，详情<br/>
如果有多个标志，且为与的关系，可以用&分隔，例如详情&阅读全文，代表详情 阅读全文 必须同时存在<br/>
如果有多个标志，且为或的关系，可以用|分隔，例如详情|阅读全文，代表详情 阅读全文 任意存在一个<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容页抓取成功标志</td>
<td><input name="setting[0][html_include]" type="text" size="70" value="<?php echo $html_include;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
抓取成功时，内容页源代码一定会存在的片段，例如 class="content"，多个标志可以用|分隔<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容页抓取失败标志</td>
<td><input name="setting[0][html_exclude]" type="text" size="70" value="<?php echo $html_exclude;?>"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
抓取失败时，内容页源代码一定会存在的片段，例如 请登录，多个标志可以用|分隔<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 自定义函数</td>
<td>
<select name="setting[0][func]">
<option value="">请选择</option>
<?php
$F = glob(DT_ROOT.'/api/spider/*.func.php');
foreach($F as $f) {
	$f = substr(basename($f), 0, -9);
	if(check_name($f)) echo '<option value="'.$f.'"'.($func == $f ? ' selected' : '').'>api/spider/'.$f.'.func.php</option>';
}
?>
</select>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
对于部分字段采集数据需要特殊处理，可以自定义函数处理<br/>
文件命名为xxx.func.php并保存到api/spider/目录<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 自定义文件</td>
<td>
<select name="setting[0][inc]">
<option value="">请选择</option>
<?php
$F = glob(DT_ROOT.'/api/spider/*.inc.php');
foreach($F as $f) {
	$f = substr(basename($f), 0, -8);
	if(check_name($f)) echo '<option value="'.$f.'"'.($inc == $f ? ' selected' : '').'>api/spider/'.$f.'.inc.php</option>';
}
?>
</select>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
数据解析完成发布之前，系统自动包含此文件，对已有数据(保存在$post数组)进行二次处理<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 清除内容链接</td>
<td>
<label><input type="radio" name="setting[0][clear]" value="1"<?php if($clear == 1){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[0][clear]" value="0"<?php if($clear == 0){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 下载远程图片</td>
<td>
<label><input type="radio" name="setting[0][save]" value="1"<?php if($save == 1){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[0][save]" value="0"<?php if($save == 0){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 默认发布状态</td>
<td>
<select name="setting[0][status]">
<option value="3"<?php if($status == '3') echo ' selected';?>>已发布</option>
<option value="2"<?php if($status == '2') echo ' selected';?>>待审核</option>
<option value="1"<?php if($status == '1') echo ' selected';?>>已拒绝</option>
<option value="0"<?php if($status == '0') echo ' selected';?>>回收站</option>
</select>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
数据发布到对应模块之后默认状态，如果需要人工审核，可以选择待审核<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 模拟客户端</td>
<td><input name="setting[0][agent]" type="text" size="70" value="<?php echo $agent;?>" id="agent"/> &nbsp; 
<select onchange="bot(this.value);">
<option value="">常用</option>
<option value="baidu">百度</option>
<option value="google">谷歌</option>
<option value="bing">必应</option>
<option value="wx">微信</option>
<option value="ios">苹果</option>
<option value="adr">安卓</option>
</select>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
例如模拟搜索引擎的客户端，防止被对方屏蔽<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 模拟IP</td>
<td><input name="setting[0][ip]" type="text" size="70" value="<?php echo $ip;?>" id="ip"/></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
多个IP可以用“|”分隔，系统随机使用一个<br/>
模拟不同的IP地址，防止对方屏蔽服务器真实地址<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 发送COOKIES</td>
<td><textarea name="setting[0][cookie]" style="width:432px;height:60px;"><?php echo $cookie;?></textarea></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
对于部分需要登录的站点，发送COOKIES可以模拟登录状态<br/>
由于网络环境的变化，登录状态可能不生效<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 发送HEADER</td>
<td><textarea name="setting[0][header]" style="width:432px;height:60px;"><?php echo $header;?></textarea></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
对于部分需要HEADER信息验证的网站，可以发送验证信息<br/>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 页面抓取时间间隔</td>
<td><input name="setting[0][time]" type="text" size="10" value="<?php echo $time;?>"/> 秒</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">
过快的抓取频率可能会触发对方服务器屏蔽，可以设置时间间隔规避<br/>
间隔时间会影响采集速度，具体请按实际情况调试<br/>
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
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><?php echo $k;?></td>
<td><?php echo $v;?><input type="hidden" name="setting[<?php echo $k;?>][nm]" value="<?php echo $v;?>"/></td>
<td><textarea name="setting[<?php echo $k;?>][fm]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['fm']) ? $setting[$k]['fm'] : '';?></textarea></td>
<td><textarea name="setting[<?php echo $k;?>][to]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['to']) ? $setting[$k]['to'] : '';?></textarea></td>
<td data-hide-1200="1"><textarea name="setting[<?php echo $k;?>][fd]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['fd']) ? $setting[$k]['fd'] : '';?></textarea></td>
<td data-hide-1200="1"><textarea name="setting[<?php echo $k;?>][rp]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['rp']) ? $setting[$k]['rp'] : '';?></textarea></td>
<td><textarea name="setting[<?php echo $k;?>][vl]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['vl']) ? $setting[$k]['vl'] : '';?></textarea></td>
<td><textarea name="setting[<?php echo $k;?>][fc]" style="width:148px;height:32px;" class="f_fd"><?php echo isset($setting[$k]['fc']) ? $setting[$k]['fc'] : '';?></textarea></td>
</tr>
<?php }?>
<tr>
<th>字段</th>
<th>名称</th>
<th>开始标志</th>
<th>结束标志</th>
<th data-hide-1200="1">查找</th>
<th data-hide-1200="1">替换为</th>
<th>默认值</th>
<th>处理函数</th>
</tr>
</table>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">设置说明</td>
<td class="ts">
开始标志指对应字段数据在采集对象页面源码中的起始代码片段<br/>
结束标志指对应字段数据在采集对象页面源码中的结束代码片段<br/>
代码片段尽量找唯一性的标识，以免匹配不准确<br/>
匹配到的内容如果有多余内容，可以用查找替换功能处理，多项内容可以用“|”分隔<br/>
例如查找a|b|c，替换为1||3，代表a替换为1，b替换为空，c替换为3<br/>
默认值代表无需采集或采集失败的赋值，一般为数字或者字符串<br/>
默认值支持使用系统内的变量、常量或函数，此时需要用大括号，例如{$DT_TIME}代表当前时间UNIX时间戳<br/>
处理函数中，需要处理的参数设置为*，需要多个函数处理可以嵌套书写，或者多个函数以;结尾<br/>
例如 cutstr(substr(*, 0, -12), '>') 或 substr(*, 0, -12);cutstr(*, '>');<br/>
默认数据字段较多，但并非每个字段都需要配置，最重要的是标题、内容、图片等，测试采集时会提示必填的字段<br/>
规则配置完成后，请先保存并测试成功后再开始采集<br/>
成功采集的规则，可以通过导出规则分享给他人，如果包含了自定义函数或文件，需要一并分享<br/>
他人分享的规则，可以通过导入规则导入，导入并保存之后，需要先测试成功再开始采集<br/>
</td>
</tr>
<tr>
<td class="tl">常用函数</td>
<td class="ts">
substr(*, 6) 表示删除数据前6个字符<br/>
substr(*, 0, -6) 表示删除数据后6个字符<br/>
substr(*, 6, -6) 表示删除数据前6个字符和后6个字符<br/>
cutstr(*, 'Hello') 表示取数据第一次出现Hello字符之后的内容<br/>
cutstr(*, '', 'Hello') 表示取数据第一次出现Hello字符之前的内容<br/>
cutstr(*, 'Hello', 'World') 表示取数据第一次出现Hello和第一次出现World之间的内容<br/>
datetotime(*) 表示将Y-m-d格式日期转换为UNIX时间戳<br/>
strip_tags(*) 表示保留纯文本并删除所有html标签<br/>
trim(*) 表示删除前后空格<br/>
dtrim(*) 表示删除数据所有空白字符<br/>
intval(*) 表示将字符转换为整数<br/>
</td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="保 存" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="window.parent.cDialog();"/></div>
</form>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'show_include';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写内容页网址包含', f);
		return false;
	}
	return true;
}
function bot(b) {
	if(b == 'baidu') {
		$('#agent').val('Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)');
		$('#ip').val('220.181.108.174|220.181.57.217|123.125.71.113');
	} else if(b == 'google') {
		$('#agent').val('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
		$('#ip').val('66.249.92.48|203.208.60.24|72.14.192.18');
	} else if(b == 'bing') {
		$('#agent').val('Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)');
		$('#ip').val('40.77.167.5|207.46.13.100|157.55.39.168');
	} else if(b == 'wx') {
		$('#agent').val('Mozilla/5.0 (iPhone; CPU iPhone OS 15_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.2(0x18000223) NetType/4G Language/zh_CN');
		$('#ip').val('');
	} else if(b == 'ios') {
		$('#agent').val('Mozilla/5.0 (iPhone; CPU OS 11_0 like Mac OS X) AppleWebKit/604.1.25 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1');
		$('#ip').val('');
	} else if(b == 'adr') {
		$('#agent').val('Mozilla/5.0 (Linux; Android 10; HarmonyOS; CDY-AN00; HMSCore 6.7.0.322) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.105 HuaweiBrowser/12.1.4.302 Mobile Safari/537.36');
		$('#ip').val('');
	}
}
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>