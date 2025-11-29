<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
$menus = array (
    array('基本设置'),
    array('SEO优化'),
    array('服务器优化'),
    array('安全中心'),
    array('图片处理'),
    array('邮件发送'),
    array('页面细节'),
    array('色彩配置'),
    array('云服务'),
);
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="tab" id="tab" value="<?php echo $tab;?>"/>
<div id="Tabs0" style="display:">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">网站名称</td>
<td><input name="setting[sitename]" type="text" value="<?php echo $sitename;?>" size="40"/></td>
</tr>
<tr>
<td class="tl">网站地址</td>
<td><input name="config[url]" type="text" value="<?php echo $url;?>" size="40"/><?php tips('请添写完整URL地址,例如https://www.destoon.com/<br/>注意以 / 结尾');?></td>
</tr>
<tr>
<td class="tl">网站LOGO</td>
<td><input name="setting[logo]" type="text" value="<?php echo $logo;?>" id="logo" size="70" ondblclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('logo').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('logo').value='';Dd('plogo').src='<?php echo DT_SKIN;?>logo.gif';"/>
</span>
<div style="padding:10px 0 0 0;"><img src="<?php echo $logo ? $logo : DT_SKIN.'logo.gif';?>" id="plogo" onclick="Dthumb(1,180,60, Dd('logo').value, 0, 'logo');"/></div></td>
</tr>
<tr>
<td class="tl">版权信息</td>
<td><textarea name="setting[copyright]" id="copyright" style="width:500px;height:50px;"><?php echo $copyright;?></textarea></td> 
</tr>
<tr>
<td class="tl"></td>
<td class="ts">支持HTML语法，常用代码：版权&copy; &amp;copy; 空格 &amp;nbsp; 换行  &lt;br/&gt;</td> 
</tr>
<tr>
<td class="tl">客服电话</td>
<td><input name="setting[telephone]" type="text" value="<?php echo $telephone;?>" size="30"/></td>
</tr>
<tr>
<td class="tl">ICP备案序号</td>
<td><input name="setting[icpno]" type="text" value="<?php echo $icpno;?>" size="30"/></td>
</tr>
<tr>
<td class="tl">网安备案序号</td>
<td><input name="setting[wano]" type="text" value="<?php echo $wano;?>" size="30"/></td>
</tr>
<tr> 
<td class="tl">APP备案号</td>
<td><input name="setting[appno]" type="text" value="<?php echo $appno;?>" size="30"/></td>
</tr>
<tr>
<td class="tl">网站状态</td>
<td>
<label><input type="radio" name="setting[close]" value="0"<?php if(!$close){ ?> checked<?php } ?> onclick="Dh('dclose');"/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[close]" value="1"<?php if($close){ ?> checked<?php } ?> onclick="Ds('dclose');"/> 关闭</label>
</td>
</tr>
<tr id="dclose" style="display:<?php if(!$close) echo 'none';?>">
<td class="tl">关闭原因</td>
<td><textarea name="setting[close_reason]" id="close_reason" style="width:500px;height:50px;overflow:visible;"><?php echo $close_reason;?></textarea><br/>支持HTML语法，网站关闭不影响后台管理
</td> 
</tr>
<tr>
<td class="tl">城市分站</td>
<td>
<label><input type="radio" name="setting[city]" value="1"<?php if($city){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[city]" value="0"<?php if(!$city){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;<?php tips('如果开启城市分站，网站首页、模块首页、模块列表页请关闭生成静态');?></a>
</td>
</tr>
<tr>
<td class="tl">根据IP自动跳转分站</td>
<td>
<label><input type="radio" name="setting[city_ip]" value="1"<?php if($city_ip){ ?> checked <?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[city_ip]" value="0"<?php if(!$city_ip){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;<?php tips('此项比较耗费系统资源。为了防止搜索引擎重复收录，系统在判断访客为搜索引擎时不自动跳转');?></a>
</td>
</tr>
<tr>
<td class="tl">网站默认语言</td>
<td>
<?php
$select = '';
$dirs = list_dir('lang');
foreach($dirs as $v) {
	$selected = ($v['dir'] == DT_LANG) ? 'selected' : '';
	$select .= "<option value='".$v['dir']."' ".$selected.">".$v['name']."</option>";
}
$select = '<select name="config[language]">'.$select.'</select>';
echo $select;
?>
</td> 
</tr>

<tr>
<td class="tl">网站默认风格</td>
<td>
<?php
$select = '';
$dirs = list_dir('static/skin');
foreach($dirs as $v) {
	if(is_file(DT_ROOT.'/static/skin/'.$v['dir'].'/chat.css')) continue;
	$selected = ($CFG['skin'] && $v['dir'] == $CFG['skin']) ? 'selected' : '';
	$select .= "<option value='".$v['dir']."' ".$selected.">".$v['name']."</option>";
}
$select = '<select name="config[skin]">'.$select.'</select>';
echo $select;
tips('位于./static/skin/目录,一个目录即为一套风格');
?>
</td> 
</tr>
<tr>
<td class="tl">手机默认风格</td>
<td>
<?php
$select = '';
$dirs = list_dir('static/skin');
foreach($dirs as $v) {
	if(!is_file(DT_ROOT.'/static/skin/'.$v['dir'].'/chat.css')) continue;
	$selected = ($CFG['skin_mobile'] && $v['dir'] == $CFG['skin_mobile']) ? 'selected' : '';
	$select .= "<option value='".$v['dir']."' ".$selected.">".$v['name']."</option>";
}
$select = '<select name="config[skin_mobile]">'.$select.'</select>';
echo $select;
tips('位于./static/skin/目录,一个目录即为一套风格');
?>
</td> 
</tr>
<tr>
<td class="tl">网站默认模板</td>
<td>
<?php
$select = '';
$dirs = list_dir('template');
foreach($dirs as $v) {
	if(is_dir(DT_ROOT.'/template/'.$v['dir'].'/mobile/')) continue;
	$selected = ($CFG['template'] && $v['dir'] == $CFG['template']) ? 'selected' : '';
	$select .= "<option value='".$v['dir']."' ".$selected.">".$v['name']."</option>";
}
$select = '<select name="config[template]">'.$select.'</select>';
echo $select;
tips('位于./template/目录,一个目录即为一套模板');
?>
</td> 
</tr>
<tr>
<td class="tl">手机默认模板</td>
<td>
<?php
$select = '';
$dirs = list_dir('template');
foreach($dirs as $v) {
	if(!is_dir(DT_ROOT.'/template/'.$v['dir'].'/mobile/')) continue;
	$selected = ($CFG['template_mobile'] && $v['dir'] == $CFG['template_mobile']) ? 'selected' : '';
	$select .= "<option value='".$v['dir']."' ".$selected.">".$v['name']."</option>";
}
$select = '<select name="config[template_mobile]">'.$select.'</select>';
echo $select;
tips('位于./template/目录,一个目录即为一套模板');
?>
</td> 
</tr>
<tr>
<td class="tl">网页编辑器</td>
<td>
<?php
$select = '';
$dirs = list_dir($MODULE[2]['moduledir'].'/editor');
foreach($dirs as $v) {
	$selected = ($CFG['editor'] && $v['dir'] == $CFG['editor']) ? 'selected' : '';
	$select .= "<option value='".$v['dir']."' ".$selected.">".$v['name']."</option>";
}
$select = '<select name="config[editor]">'.$select.'</select>';
echo $select;
tips('位于./'.$MODULE[2]['moduledir'].'/editor/目录,一个目录即为一套模板');
?>
</td> 
</tr>
<tr>
<td class="tl">VIP会员名称</td>
<td><input name="config[com_vip]" type="text" value="<?php echo $com_vip;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">真实货币名称</td>
<td><input name="setting[money_name]" type="text" value="<?php echo $money_name;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">真实货币单位</td>
<td><input name="setting[money_unit]" type="text" value="<?php echo $money_unit;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">真实货币符号</td>
<td><input name="setting[money_sign]" type="text" value="<?php echo $money_sign;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">虚拟积分名称</td>
<td><input name="setting[credit_name]" type="text" value="<?php echo $credit_name;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">虚拟积分单位</td>
<td><input name="setting[credit_unit]" type="text" value="<?php echo $credit_unit;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">小额免密支付</td>
<td><input name="setting[quick_pay]" type="text" value="<?php echo $quick_pay;?>" size="10"/><?php tips('请填写数字，小于此额度的支付无需用户输入支付密码');?></td>
</tr>
<tr>
<td class="tl">购物车最大容量</td>
<td><input name="setting[max_cart]" type="text" value="<?php echo $max_cart;?>" size="10"/><?php tips('请填写数字，如果未启用商城功能，请设置为0');?></td>
</tr>
<tr>
<td class="tl">后台左侧栏宽度</td>
<td><input name="setting[admin_left]" type="text" value="<?php echo $admin_left;?>" size="5"/> px</td>
</tr>
<tr>
<td class="tl">后台<a href="javascript:;" onclick="Dwidget('?file=fetch', '管理规则');" class="t">单页采编</a></td>
<td><?php echo module_checkbox('setting[fetch_module][]', $fetch_module, '1,2,3,4');?></td>
</tr>
<tr>
<td class="tl">显示地区邮政编码</td>
<td>
<label><input type="radio" name="setting[postcode]" value="1"<?php if($postcode){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[postcode]" value="0"<?php if(!$postcode){ ?> checked<?php } ?>/> 关闭</label><?php tips('快递无需邮政编码，为了减少用户填写，建议关闭');?>
</td>
</tr>
<tr>
<td class="tl">显示会员手机号码</td>
<td>
<label><input type="radio" name="setting[im_mob]" value="1"<?php if($im_mob){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[im_mob]" value="0"<?php if(!$im_mob){ ?> checked<?php } ?>/> 关闭</label><?php tips('为了会员隐私和帐号安全，建议关闭');?>
</td>
</tr>
<tr>
<td class="tl">粉丝关注功能</td>
<td>
<label><input type="radio" name="setting[follow]" value="1"<?php if($follow){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[follow]" value="0"<?php if(!$follow){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">即时通讯站内交谈</td>
<td>
<label><input type="radio" name="setting[im_web]" value="1"<?php if($im_web){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[im_web]" value="0"<?php if(!$im_web){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">即时通讯QQ</td>
<td>
<label><input type="radio" name="setting[im_qq]" value="1"<?php if($im_qq){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[im_qq]" value="0"<?php if(!$im_qq){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">即时通讯微信</td>
<td>
<label><input type="radio" name="setting[im_wx]" value="1"<?php if($im_wx){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[im_wx]" value="0"<?php if(!$im_wx){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">即时通讯阿里旺旺</td>
<td>
<label><input type="radio" name="setting[im_ali]" value="1"<?php if($im_ali){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[im_ali]" value="0"<?php if(!$im_ali){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">即时通讯Skype</td>
<td>
<label><input type="radio" name="setting[im_skype]" value="1"<?php if($im_skype){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[im_skype]" value="0"<?php if(!$im_skype){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
</table>
</div>

<div id="Tabs1" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">标题分隔符</td>
<td><input name="setting[seo_delimiter]" type="text" value="<?php echo $seo_delimiter;?>" size="10"/></td>
</tr>
<tr>
<td class="tl">Title(网站标题)</td>
<td><input name="setting[seo_title]" type="text" value="<?php echo $seo_title;?>" size="61"><?php tips('针对搜索引擎设置的网页标题');?></td>
</tr>
<tr>
<td class="tl">Meta Keywords<br/>(网页关键词)</td>
<td><textarea name="setting[seo_keywords]" cols="60" rows="3"><?php echo $seo_keywords;?></textarea><?php tips('针对搜索引擎设置的关键词');?></td>
</tr>
<tr>
<td class="tl">Meta Description<br/>(网页描述)</td>
<td><textarea name="setting[seo_description]" cols="60" rows="3"><?php echo $seo_description;?></textarea><?php tips('针对搜索引擎设置的网页描述');?></td>
</tr>
<tr>
<td class="tl">目录首页文件名</td>
<td><input name="setting[index]" type="text" value="<?php echo $index;?>" size="8"/>
</td>
</tr>
<tr>
<td class="tl">生成文件扩展名</td>
<td>
<select name="setting[file_ext]">
<option value="html"<?php if($file_ext == 'html') echo ' selected';?>>.html</option>
<option value="htm"<?php if($file_ext == 'htm') echo ' selected';?>>.htm</option>
<option value="shtm"<?php if($file_ext == 'shtm') echo ' selected';?>>.shtm</option>
<option value="shtml"<?php if($file_ext == 'shtml') echo ' selected';?>>.shtml</option>
</select>
</td>
</tr>
<tr>
<td class="tl">PHP网址扩展名</td>
<td><input name="config[ext]" type="text" value="<?php echo $ext;?>" size="8"/> <?php tips('例如：留空，则index.php?acb=123会变成index?abc=123，填写.jsp，则index.php?acb=123会变成index.jsp?abc=123<br/>此项会增加服务器负担，且必须在服务器端设置成功对应伪静态规则方可更改');?>
</td>
</tr>
<tr>
<td class="tl">网站首页生成html</td>
<td>
<label><input type="radio" name="setting[index_html]" value="1"<?php if($index_html){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[index_html]" value="0"<?php if(!$index_html){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">网址伪静态</td>
<td>
<label><input type="radio" name="setting[rewrite]" value="1"<?php if($rewrite){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[rewrite]" value="0"<?php if(!$rewrite){ ?> checked<?php } ?>/> 关闭</label> <?php tips('请确认服务器已做过规则配置，否则请勿开启<br/>ReWrite规则见帮助文档<br/>请点击下面的地址，如果可以正常显示，说明规则配置成功<br/><a href=index-htm-url-rule.html target=_blank>index-htm-url-rule.html</a>');?>
</td>
</tr>
<tr>
<td class="tl">公司主页</td>
<td>
<label><input type="radio" name="setting[homepage]" value="1"<?php if($homepage){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[homepage]" value="0"<?php if(!$homepage){ ?> checked<?php } ?>/> 关闭</label> <?php tips('关闭后，公司主页将直接显示个人空间');?>
</td>
</tr>
<tr>
<td class="tl">公司主页绑定二级域名</td>
<td><input name="config[com_domain]" type="text" value="<?php echo $com_domain;?>" size="30"/> <?php tips('如果填写 .destoon.com 同时需要将域名泛解析 *.destoon.com 指向服务器IP，并且在服务器端绑定泛域名至 网站根目录/company 或者 网站根目录，生成的主页形式为username.destoon.com<br/>如果填写 shop.destoon.com 同时需要将域名泛解析 shop.destoon.com 指向服务器IP，并且在服务器端绑定域名至网站根目录/company 目录，生成的主页形式为shop.destoon.com/username/(注：此方式必须支持伪静态)');?></td>
</tr>
<tr>
<td class="tl">泛解析绑定目录</td>
<td>
<select name="config[com_dir]">
<option value="0"<?php echo $com_dir == 0 ? ' selected' : '';?>>根目录</option>
<option value="1"<?php echo $com_dir == 1 ? ' selected' : '';?>>company目录</option>
</select>&nbsp;
<?php tips('如果服务器支持，推荐绑定至company目录');?>
</td>
</tr>
<tr>
<td class="tl">会员商铺虚拟目录</td>
<td><input name="setting[com_mark]" type="text" value="<?php echo $com_mark;?>" size="30"/> <?php tips('如果开启伪静态且没有绑定二级域名，默认生成的商铺地址示例为 www.destoon.com/com/username，username前面的com属于一个不存在的虚拟目录，此处如果填写shop，则地址会变为 www.destoon.com/shop/username 同时需要修改服务端伪静态规则里的com为shop才能生效');?></td>
</tr>
<tr>
<td class="tl">公司二级域名加www</td>
<td>
<label><input type="radio" name="setting[com_www]" value="1"<?php if($com_www){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[com_www]" value="0"<?php if(!$com_www){ ?> checked<?php } ?>/> 关闭</label> <?php tips('例如二级域名为sell.destoon.com<br/>添加www后为 www.sell.destoon.com');?>
</td>
</tr>
<tr>
<td class="tl">公司二级域名https</td>
<td>
<label><input type="radio" name="setting[com_https]" value="1"<?php if($com_https){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[com_https]" value="0"<?php if(!$com_https){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">会员顶级域名伪静态</td>
<td>
<label><input type="radio" name="config[com_rewrite]" value="1"<?php if($com_rewrite){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="config[com_rewrite]" value="0"<?php if(!$com_rewrite){ ?> checked<?php } ?>/> 关闭</label> <?php tips('部分服务器可能无法开启会员绑定的顶级域名伪静态，如果无法开启，可在此关闭，以免出现打不开页面的情况，此项仅针对会员顶级域名，不影响其他页面伪静态');?>
</td>
</tr>
<tr>
<td class="tl">搜索页伪静态</td>
<td>
<label><input type="radio" name="setting[search_rewrite]" value="1"<?php if($search_rewrite){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[search_rewrite]" value="0"<?php if(!$search_rewrite){ ?> checked<?php } ?>/> 关闭</label> <?php tips('搜索结果地址伪静态，此项会增加服务器负载');?>
</td>
</tr>
<tr>
<td class="tl">服务器中文路径编码</td>
<td>
<label><input type="radio" name="setting[pcharset]" value="0"<?php if(!$pcharset){ ?> checked<?php } ?>/> 未用</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[pcharset]" value="gbk"<?php if($pcharset == 'gbk'){ ?> checked<?php } ?>/> GBK</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[pcharset]" value="utf-8"<?php if($pcharset == 'utf-8'){ ?> checked<?php } ?>/> UTF-8</label> <?php tips('当生成包含中文文件名的文件出现乱码或者下载带有中文名文件提示找不到文件时，可尝试设置此项');?>
</td>
</tr>
<tr>
<td class="tl">404错误日志</td>
<td>
<label><input type="radio" name="setting[log_404]" value="1"<?php if($log_404){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[log_404]" value="0"<?php if(!$log_404){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=stats&action=404', '404错误日志');" class="t">[查看日志]</a>
<?php tips('开启404日志有利于分析站内死链接和用户或搜索引擎蜘蛛的错误记录<br/>同时需要设置站点的404页面至网站根目录404.php');?>
</td>
</tr>
<tr>
<td class="tl">百度推送准入密钥</td>
<td><input name="setting[baidu_push]" type="text" value="<?php echo $baidu_push;?>" size="30"/> <a href="<?php echo gourl('https://ziyuan.baidu.com/linksubmit/index?site='.DT_PATH);?>" target="_blank" class="t">[帐号申请]</a> <?php tips('主站域名以及模块二级域名需要在百度站长平台认证<br/>本地测试未上线的网站请勿填写');?></td>
</tr>
</table>
</div>

<div id="Tabs2" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">首页自动更新频率</td>
<td><input name="setting[task_index]" type="text" value="<?php echo $task_index;?>" size="5"/> 秒 <?php tips('仅对生成的静态网页有效，建议设置300以上，低于60按300计算');?></td>
</tr>
<tr>
<td class="tl">列表页自动更新频率</td>
<td><input name="setting[task_list]" type="text" value="<?php echo $task_list;?>" size="5"/> 秒 <?php tips('仅对生成的静态网页有效，建议设置1800以上，低于300按1800计算');?></td>
</tr>
<tr>
<td class="tl">内容页自动更新频率</td>
<td><input name="setting[task_item]" type="text" value="<?php echo $task_item;?>" size="5"/> 秒 <?php tips('仅对生成的静态网页有效，建议设置3600以上，低于1800按3600计算');?></td>
</tr>
<tr>
<td class="tl">SQL查询缓存更新周期</td>
<td><input type="text" name="config[db_expires]" value="<?php echo $db_expires;?>" size="5"/> 秒<?php tips('此项可明显减轻数据库查询对服务器的压力');?></td>
</tr>
<tr>
<td class="tl">点击次数缓存更新周期</td>
<td><input type="text" name="setting[cache_hits]" value="<?php echo $cache_hits;?>" size="5"/> 秒<?php tips('此项可明显减轻数据库服务压力，但是会造成浏览次数的延迟显示');?></td>
</tr>
<tr>
<td class="tl">搜索结果缓存更新周期</td>
<td><input type="text" name="setting[cache_search]" value="<?php echo $cache_search;?>" size="5"/> 秒<?php tips('此项可减轻搜索等大量耗费资源的操作对服务器的压力');?></td>
</tr>
<tr>
<td class="tl">搜索结果最多显示页数</td>
<td><input type="text" name="setting[max_search]" value="<?php echo $max_search;?>" size="5"/> 页<?php tips('显示所有搜索结果可能被采集数据且增加服务器压力');?></td>
</tr>
<tr>
<td class="tl">栏目分类最多显示页数</td>
<td><input type="text" name="setting[max_list]" value="<?php echo $max_list;?>" size="5"/> 页<?php tips('此项可减轻服务器压力，但对搜索引擎不友好，请谨慎设置<br/>如果列表生成静态，此设置无效');?></td>
</tr>
<tr>
<td class="tl">游客最大翻页数</td>
<td><input type="text" name="setting[max_guest]" value="<?php echo $max_guest;?>" size="5"/> 页<?php tips('超过此限制的游客将转到登录页面，此设置不限制搜索引擎蜘蛛');?></td>
</tr>
<tr>
<td class="tl">列表页缓存页数</td>
<td><input type="text" name="setting[cache_page]" value="<?php echo $cache_page;?>" size="5"/> 页<?php tips('分类列表、搜索结果等列表页前几页点击概率较高，缓存之后可以减轻服务器压力');?></td>
</tr>
<tr>
<td class="tl">数据缓存方式</td>
<td>
<select name="config[cache]" onchange="if(this.options[this.selectedIndex].innerHTML.indexOf('不支持')!=-1){alert('系统不支持 '+this.value);$('#ccf').val('<?php echo $cache;?>');}" id="ccf">
<option value="file"<?php echo $cache == 'file' ? ' selected' : '';?>>文件 (支持) - 不推荐</option>
<option value="memcache"<?php echo $cache == 'memcache' ? ' selected' : '';?>>Memcache (<?php echo class_exists('Memcache') ? '支持' : '不支持'?>) - 推荐</option>
<option value="redis"<?php echo $cache == 'redis' ? ' selected' : '';?>>Redis (<?php echo class_exists('Redis') ? '支持' : '不支持'?>) - 推荐</option>
<option value="eaccelerator"<?php echo $cache == 'eaccelerator' ? ' selected' : '';?>>eAccelerator (<?php echo function_exists('eaccelerator_get') ? '支持' : '不支持'?>)</option>
<option value="xcache"<?php echo $cache == 'xcache' ? ' selected' : '';?>>Xcache (<?php echo function_exists('xcache_get') ? '支持' : '不支持'?>)</option>
<option value="wincache"<?php echo $cache == 'wincache' ? ' selected' : '';?>>WinCache (<?php echo function_exists('wincache_ucache_get') ? '支持' : '不支持'?>)</option>
<option value="apc"<?php echo $cache == 'apc' ? ' selected' : '';?>>apc (<?php echo function_exists('apc_fetch') ? '支持' : '不支持'?>)</option>
</select>
<?php tips('除了文件缓存，其他缓存方式需要服务器端支持，具体请查看phpinfo信息。<br/>请在确认服务器环境支持的情况下开启，否则可能导致未知的错误');?>&nbsp;&nbsp;
<a href="javascript:;" onclick="Diframe('?file=<?php echo $file;?>&action=cache&job='+Dd('ccf').value, 0, 0, 1);" class="t">[缓存测试]</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=html&action=cacheclear', '清空缓存');" class="t">[清空缓存]</a>
</td>
</tr>
<tr>
<td class="tl">模板缓存自动更新</td>
<td>
<label><input type="radio" name="config[template_refresh]" value="1"<?php if($template_refresh){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="config[template_refresh]" value="0"<?php if(!$template_refresh){ ?> checked<?php } ?>/> 关闭</label> <?php tips('如果网站模板无需修改，建议您关闭此功能');?></td>
</tr>
<tr title="将页面内容以gzip压缩后传输，可以加快传输速度，需PHP 4.0.4以上且支持Zlib模块才能使用">
<td class="tl">页面Gzip压缩</td>
<td>
<label><input type="radio" name="setting[gzip_enable]" value="1"<?php if($gzip_enable){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[gzip_enable]" value="0"<?php if(!$gzip_enable){ ?> checked<?php } ?>/> 关闭</label> <?php tips(function_exists('ob_gzhandler') ? '当前服务器支持Gzip，建议开启' : '当前服务器不支持Gzip，请关闭');?>
</td>
</tr>
<tr>
<td class="tl">图片延时加载</td>
<td>
<label><input type="radio" name="setting[lazy]" value="1"<?php if($lazy){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[lazy]" value="0"<?php if(!$lazy){ ?> checked<?php } ?>/> 关闭</label> <?php tips('页面图片仅在浏览器的当前窗口时再加载，可明显降低访问量很大的站点的服务器负担');?></td>
</tr>
<tr>
<td class="tl">分页显示方式</td>
<td>
<label><input type="radio" name="setting[pages_mode]" value="0"<?php if(!$pages_mode){ ?> checked<?php } ?>/> 默认</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[pages_mode]" value="1"<?php if($pages_mode){ ?> checked<?php } ?>/> 简洁</label>
</td>
</tr>
<tr>
<td class="tl">流量统计</td>
<td>
<label><input type="radio" name="setting[stats]" value="1"<?php if($stats){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[stats]" value="0"<?php if(!$stats){ ?> checked<?php } ?>/> 关闭</label> <?php tips('开启此项会增加访问量大的站点的服务器负担');?></td>
</tr>
<tr>
<td class="tl">积分记录</td>
<td>
<label><input type="radio" name="setting[log_credit]" value="1"<?php if($log_credit){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[log_credit]" value="0"<?php if(!$log_credit){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">记录浏览历史的模块</td>
<td><?php echo module_checkbox('setting[history_module][]', $history_module, '1,2,3');?></td>
</tr>
<tr>
<td class="tl">显示联系方式图片化</td>
<td>
<label><input type="radio" name="setting[anti_spam]" value="1"<?php if($anti_spam){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[anti_spam]" value="0"<?php if(!$anti_spam){ ?> checked<?php } ?>/> 关闭</label> <?php tips('将电话、传真、Email等重要信息显示为图片格式，防止采集和复制');?>
</td>
</tr>
<tr>
<td class="tl">智能搜索提示</td>
<td>
<label><input type="radio" name="setting[search_tips]" value="1"<?php if($search_tips){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[search_tips]" value="0"<?php if(!$search_tips){ ?> checked<?php } ?>/> 关闭</label></td>
</tr>
<tr>
<td class="tl">编辑器自动保存草稿</td>
<td>
<label><input type="radio" name="setting[save_draft]" value="1"<?php if($save_draft == 1){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[save_draft]" value="0"<?php if($save_draft == 0){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[save_draft]" value="2"<?php if($save_draft == 2){ ?> checked<?php } ?>/> 后台开启</label> <?php tips('后台开启指仅在后台开启，前台将不开启<br/>注意：开启此功能会占用一定的服务器空间');?></td>
</tr>
<tr>
<td class="tl">搜索关键词自动记录</td>
<td>
<label><input type="radio" name="setting[search_kw]" value="1"<?php if($search_kw == 1){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[search_kw]" value="0"<?php if(!$search_kw){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[search_kw]" value="2" <?php if($search_kw == 2){ ?> checked<?php } ?>/> 审核</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=keyword', '搜索关键词记录');" class="t">[查看记录]</a></td>
</tr>
<tr>
<td class="tl">搜索关键词长度限制</td>
<td><input type="text" size="3" name="setting[min_kw]" value="<?php echo $min_kw;?>"/>
至
<input type="text" size="3" name="setting[max_kw]" value="<?php echo $max_kw;?>"/>
字符<?php tips('一个汉字的长度为3个字符，建议设置为3-30个字符之间');?></td>
</tr>
<tr>
<td class="tl">两次搜索时间间隔</td>
<td><input type="text" size="3" name="setting[search_limit]" value="<?php echo $search_limit;?>"/> 秒<?php tips('填0为不限制');?></td>
</tr>

<tr>
<td class="tl">会员在线保持时间</td>
<td><input type="text" name="setting[online]" value="<?php echo $online;?>" size="5"/> 秒<?php tips('超过此时间未有活动的会员将视为离线');?></td>
</tr>

<tr>
<td class="tl">定时更新会员新消息</td>
<td><input type="text" name="setting[pushtime]" value="<?php echo $pushtime;?>" size="5"/> 秒<?php tips('当会员停留在前台页面时，每隔一段时间，系统自动发送一次服务器请求，以更新会员站内信、新对话、购物车数量，以便会员及时收到新消息，填0为关闭，此项会增加服务器压力，建议设置30秒以上');?></td>
</tr>

<tr>
<td class="tl">列表每页默认信息条数</td>
<td><input name="setting[pagesize]" type="text" value="<?php echo $pagesize;?>" size="3"/> 条</td>
</tr>
<tr>
<td class="tl">搜索分类返回结果数限制</td>
<td><input type="text" size="3" name="setting[schcate_limit]" value="<?php echo $schcate_limit;?>"/> 条<?php tips('填0为禁用分类搜索');?></td>
</tr>
<tr>
<td class="tl">信息内容长度限制</td>
<td><input type="text" size="5" name="setting[max_len]" value="<?php echo $max_len;?>"/> 字符<?php tips('一个汉字占3个字符，填0为不限，建议设置，以免内容过长');?></td>
</tr>
<tr>
<td class="tl">静态文件分离部署地址</td>
<td><input name="config[static]" type="text" value="<?php echo $static;?>" size="40"/>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&action=static', '静态文件分离部署', 486, 200);" class="t">[使用说明]</a></td>
</tr>
<tr>
<td class="tl">远程FTP文件上传</td>
<td>
<label><input type="radio" name="setting[ftp_remote]" value="1"<?php if($ftp_remote){ ?> checked<?php } ?> onclick="Ds('ftp');"/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ftp_remote]" value="0"<?php if(!$ftp_remote){ ?> checked<?php } ?> onclick="Dh('ftp');"/> 关闭</label><?php tips('开启远程文件上传后，所有上传文件将被FTP移动到远程服务器上，可以极大的缓解主站流量压力');?></td>
</tr>
<tbody id="ftp" style="display:<?php echo $ftp_remote ? '' : 'none';?>">
<?php if(!extension_loaded("ftp")){ ?>
<tr>
<td class="tl">系统提示</td>
<td class="f_red">当前PHP环境不支持FTP功能</td>
</tr>
<?php }?>
<tr> 
<td class="tl">FTP主机</td>
<td><input name="setting[ftp_host]" id="ftp_host" type="text" size="30" value="<?php echo $ftp_host;?>"/><?php tips('可以是 FTP 服务器的 IP 地址或域名');?></td>
</tr>
<tr> 
<td class="tl">FTP端口</td>
<td><input name="setting[ftp_port]" id="ftp_port" type="text" size="30" value="<?php echo $ftp_port;?>"/><?php tips('默认为 21');?></td>
</tr>
<tr> 
<td class="tl">FTP帐号</td>
<td><input name="setting[ftp_user]" id="ftp_user" type="text" size="30" value="<?php echo $ftp_user;?>"/><?php tips('该帐号必需具有以下权限：读取文件、写入文件、删除文件、创建目录、子目录继承');?></td>
</tr>
<tr> 
<td class="tl">FTP密码<br></td>
<td><input name="setting[ftp_pass]" type="text" id="ftp_pass" size="30" value="<?php echo $ftp_pass;?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
<tr>
<td class="tl">SSL连接</td>
<td>
<label><input type="radio" name="setting[ftp_ssl]" value="1"<?php if($ftp_ssl){ ?> checked<?php } ?> id="ftp_ssl"/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ftp_ssl]" value="0"<?php if(!$ftp_ssl){ ?> checked<?php } ?>/> 否</label><?php tips('FTP 服务器必需开启了 SSL 才可以启用');?></td>
</tr>
<tr>
<td class="tl">被动模式(PASV)连接</td>
<td>
<label><input type="radio" name="setting[ftp_pasv]" value="1"<?php if($ftp_pasv){ ?> checked<?php } ?> id="ftp_pasv"/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ftp_pasv]" value="0"<?php if(!$ftp_pasv){ ?> checked<?php } ?>/> 否</label><?php tips('一般情况下非被动模式即可，如果存在上传失败问题，可尝试打开此设置');?>
</td>
</tr>
<tr>
<td class="tl">保留源文件</td>
<td>
<label><input type="radio" name="setting[ftp_save]" value="1"<?php if($ftp_save){ ?> checked<?php } ?> id="ftp_del"/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ftp_save]" value="0"<?php if(!$ftp_save){ ?> checked<?php } ?>/> 否</label><?php tips('如果选择是，上传到本服务器上的文件在上传到FTP服务器后，不自动删除，相当于在本服务器多了一份备份，文件保存于file/upload目录');?>
</td>
</tr>
<tr> 
<td class="tl">远程存储目录</td>
<td><input name="setting[ftp_path]" id="ftp_path" type="text" size="30" value="<?php echo $ftp_path;?>"/><?php tips('例如 / 或者 /www/<br/>具体以实际情况为准');?></td>
</tr>
<tr>
<td class="tl">远程访问URL</td>
<td><input name="setting[remote_url]" id="ftp_remote_url" type="text" value="<?php echo $remote_url;?>" size="70"/><?php tips('例如 http://static.destoon.com/，注意以 / 结尾，建议设置为域名根目录且不能包含file/upload');?></td>
</tr>
<tr> 
<td class="tl">测试FTP连接</td>
<td><input name="testftp" type="button" class="btn" value="点击测试" onclick="TestFTP();"/></td>
</tr>
</table>
<script type="text/javascript">
function TestFTP() {
	if(Dd('ftp_host').value.length < 4) {
		Dalert('FTP主机不能为空');
		Dd('ftp_host').focus();
		return false;
	}
	if(Dd('ftp_remote_url').value.indexOf('file/upload') != -1) {
		Dalert('远程访问URL不能包含file/upload');
		Dd('ftp_remote_url').focus();
		return false;
	}
	var fssl = Dd('ftp_ssl').checked ? 1 : 0;
	var fpasv = Dd('ftp_pasv').checked ? 1 : 0;
	var url = '?file=setting&action=ftp&ftp_host='+Dd('ftp_host').value+'&ftp_port='+Dd('ftp_port').value+'&ftp_user='+Dd('ftp_user').value+'&ftp_pass='+Dd('ftp_pass').value+'&ftp_path='+Dd('ftp_path').value+'&ftp_ssl='+fssl+'&ftp_pasv='+fpasv;
	Diframe(url, 0, 0 , 1);
}
</script>
</div>
<div id="Tabs3" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">后台登录验证码 </td>
<td>
<label><input type="radio" name="setting[captcha_admin]" value="1"<?php if($captcha_admin){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[captcha_admin]" value="0"<?php if(!$captcha_admin){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">后台强制短信登录 </td>
<td>
<label><input type="radio" name="setting[sms_admin]" value="1"<?php if($sms_admin){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[sms_admin]" value="0"<?php if(!$sms_admin){ ?> checked<?php } ?>/> 关闭</label><?php tips('请先在会员模块设置开启短信登录，且保证创始人手机号已认证');?>
</td>
</tr>
<tr>
<td class="tl">允许登录后台的地区</td>
<td><input name="setting[admin_area]" type="text" value="<?php echo $admin_area;?>" size="70"/><?php tips('设置工作人员常用的登录地区，多个地区用|分隔<br/>例如“北京|上海|广州”，非常用地区的IP将无法登录后台');?></td>
</tr>
<tr>
<td class="tl">允许登录后台的时段</td>
<td><input name="setting[admin_hour]" type="text" value="<?php echo $admin_hour;?>" size="70"/><?php tips('留空表示不限制，建议设置为工作人员下班时间。多个时间段请用|分隔，单时间段用-分隔，时间请使用24小时制<br/>例如：8:30-18:00 表示时间段上午8:30至下午18:00<br/>22:30-2:05|5:00-13:15表示晚上22:30至次日凌晨2:05和凌晨5:00至下午13:15两个时间段<br/>网站创始人帐号不受此限制');?></td>
</tr>
<tr>
<td class="tl">允许登录后台的日期</td>
<td>
<?php for($i = 0; $i < 7; $i++) { ?>
<input type="checkbox" name="setting[admin_week][]" value="<?php echo $i;?>"<?php echo strpos(','.$admin_week.',', ','.$i.',') !== false ? ' checked' : '';?>/> 星期<?php echo $W[$i];?> 
<?php } ?>
<?php tips('不选择表示不限制，网站创始人帐号不受此限制');?>
</td>
</tr>
<tr>
<td class="tl">发布信息转为待审的时段</td>
<td><input name="setting[check_hour]" type="text" value="<?php echo $check_hour;?>" size="70"/><?php tips('此项针对前台会员发布信息，留空表示不限制，建议设置为工作人员下班时间。时间设置请参考允许登录后台的时段 Tips');?></td>
</tr>
<tr>
<td class="tl">发布信息转为待审的日期</td>
<td>
<?php for($i = 0; $i < 7; $i++) { ?>
<label><input type="checkbox" name="setting[check_week][]" value="<?php echo $i;?>"<?php echo strpos(','.$check_week.',', ','.$i.',') !== false ? ' checked' : '';?>/> 星期<?php echo $W[$i];?></label> 
<?php } ?>
</td>
</tr>
<tr>
<td class="tl">验证码组成字符</td>
<td><input name="setting[captcha_chars]" type="text" value="<?php echo $captcha_chars;?>" size="70"/><?php tips('可填写0-9的数字、a-z的字母组合，勿填特殊符号或中文<br/>例如只显示数字验证码可填写0123456789<br/>留空系统自动显示大小写字母和数字的组合');?></td>
</tr>
<tr>
<td class="tl">中文验证码 </td>
<td>
<label><input type="radio" name="setting[captcha_cn]" value="1"<?php if($captcha_cn){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[captcha_cn]" value="0"<?php if(!$captcha_cn){ ?> checked<?php } ?>/> 关闭</label><?php tips('开启中文验证码必须在图片处理里设置中文字体');?>
</td>
</tr>
<tr>
<td class="tl">后台在线记录</td>
<td>
<label><input type="radio" name="setting[admin_online]" value="1"<?php if($admin_online){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[admin_online]" value="0"<?php if(!$admin_online){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=admin&action=online', '后台在线记录');" class="t">[点击查看]</a>
</td>
</tr>
<tr>
<td class="tl">后台菜单记录</td>
<td>
<label><input type="radio" name="setting[admin_hit]" value="1"<?php if($admin_hit){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[admin_hit]" value="0"<?php if(!$admin_hit){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=panel&action=menu', '后台菜单记录');" class="t">[点击查看]</a>
<?php tips('开启后，会对左侧栏点击进行记录，根据记录频次生成常用菜单');?>
</td>
</tr>
<tr>
<td class="tl">后台操作日志</td>
<td>
<label><input type="radio" name="setting[admin_log]" value="0"<?php if(!$admin_log){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[admin_log]" value="1"<?php if($admin_log == 1){ ?> checked<?php } ?>/> 部分开启</label><?php tips('仅记录添加、修改、删除、设置等关键操作');?>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[admin_log]" value="2"<?php if($admin_log == 2){ ?> checked<?php } ?>/> 完全开启</label><?php tips('记录后台所有操作');?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=admin&action=log', '后台操作日志');" class="t">[点击查看]</a>
</td>
</tr>
<tr>
<td class="tl">会员登录日志</td>
<td>
<label><input type="radio" name="setting[login_log]" value="0"<?php if(!$login_log){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[login_log]" value="1"<?php if($login_log == 1){ ?> checked<?php } ?>/> 后台开启</label><?php tips('仅记录网站后台登录日志');?>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[login_log]" value="2"<?php if($login_log == 2){ ?> checked<?php } ?>/> 完全开启</label><?php tips('记录所有登录日志');?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=loginlog', '会员登录日志');" class="t">[点击查看]</a>
</td>
</tr>
<tr>
<td class="tl">同一帐号同时异地登录</td>
<td>
<label><input type="radio" name="setting[ip_login]" value="0"<?php if(!$ip_login){ ?> checked<?php } ?>/> 允许</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ip_login]" value="1"<?php if($ip_login == 1){ ?> checked<?php } ?>/> 仅限前台</label><?php tips('仅限前台允许同一帐号同时异地登录<br/>后台将不允许同一帐号同时异地登录');?>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ip_login]" value="2"<?php if($ip_login == 2){ ?> checked<?php } ?>/> 完全禁止</label><?php tips('完全禁止同一帐号同时异地登录');?>
</td>
</tr>
<tr>
<td class="tl">前台编辑器源码模式</td>
<td>
<label><input type="radio" name="setting[editor_html]" value="1"<?php if($editor_html){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[editor_html]" value="0"<?php if(!$editor_html){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">内容页禁止复制</td>
<td>
<label><input type="radio" name="setting[anticopy]" value="1"<?php if($anticopy){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[anticopy]" value="0"<?php if(!$anticopy){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">文件上传记录</td>
<td>
<label><input type="radio" name="setting[uploadlog]" value="1"<?php if($uploadlog){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[uploadlog]" value="0"<?php if(!$uploadlog){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=upload', '文件上传记录');" class="t">[点击查看]</a>
</td>
</tr>
<tr>
<td class="tl">允许上传的文件类型</td>
<td><input name="setting[uploadtype]" type="text" value="<?php echo $uploadtype;?>" size="90"/> <?php tips('用|号隔开文件后缀');?></td>
</tr>
<tr>
<td class="tl">允许上传大小限制</td>
<td><input name="setting[uploadsize]" type="text" value="<?php echo $uploadsize;?>" size="10"/> Kb (1024Kb=1M) <?php tips('当前服务器最大支持'.ini_get('upload_max_filesize').'文件上传<br/>如果需要修改最大值，可以修改php.ini的upload_max_filesize参数');?></td>
</tr>
<tr>
<td class="tl">文件保存目录</td>
<td>
<select name="setting[uploaddir]">
<option value="Ym/d"<?php if($uploaddir == 'Ym/d') echo ' selected';?>>年月/日</option>
<option value="Ym/d/H"<?php if($uploaddir == 'Ym/d/H') echo ' selected';?>>年月/日/时</option>
<option value="Ym/d/H/i"<?php if($uploaddir == 'Ym/d/H/i') echo ' selected';?>>年月/日/时/分</option>
</select> 
<?php tips('上传文件保存于 file/upload 目录');?>
</td>
</tr>
<tr>
<td class="tl">文件格式转换</td>
<td>
<label><input type="radio" name="setting[convert]" value="1"<?php if($convert == 1){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[convert]" value="0"<?php if($convert == 0){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[convert]" value="2"<?php if($convert == 2){ ?> checked<?php } ?>/> 远程</label> 
<?php tips('苹果的mov和安卓的3gp视频格式需要换成mp4格式才能播放，Word、Excel和PPT文件需要转换为pdf格式才能预览，格式转换配置文件为file/config/convert.inc.php，此项需要服务器端支持，服务器端如未配置，请勿开启<br/>开启代表在主站服务器端转换，远程代表在附件服务器端转换');?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?file=upload&action=convert', '转换记录');" class="t">[转换记录]</a>
</td>
</tr>
<tr>
<td class="tl">验证数据来源</td>
<td>
<label><input type="radio" name="setting[check_referer]" value="1"<?php if($check_referer){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[check_referer]" value="0"<?php if(!$check_referer){ ?> checked<?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">信任域名</td>
<td><input name="setting[safe_domain]" type="text" value="<?php echo $safe_domain;?>" size="70"/><?php tips('不填写则默认为当前域名<br/>多个域名请用|分开 例如destoon.com|destoon.cn');?></td>
</tr>
<tr>
<td class="tl">系统负载系数</td>
<td><input name="setting[defend_cc]" type="text" value="<?php echo $defend_cc;?>" size="5"/><?php tips('仅适用部分Unix/Linux主机，系统高于此值时会禁止新用户访问，通常情况可设置为5到10，0为不限制');?>
</td>
</tr>

<tr>
<td class="tl">加密传输密码</td>
<td>
<select name="setting[md5_pass]">
<option value="0"<?php echo $md5_pass == 0 ? ' selected' : '';?>>关闭</option>
<option value="1"<?php echo $md5_pass == 1 ? ' selected' : '';?>>登录</option>
<option value="2"<?php echo $md5_pass == 2 ? ' selected' : '';?>>登录+注册</option>
<option value="3"<?php echo $md5_pass == 3 ? ' selected' : '';?>>登录+注册+修改密码</option>
</select>&nbsp;
<?php tips('密码在选中场景下先加密再传输，如果网站开启了HTTPS则无需启用');?>
</td>
</tr>
<tr>
<td class="tl">CDN加速</td>
<td>
<select name="config[cdn]">
<option value="0"<?php echo $cdn == 0 ? ' selected' : '';?>>未使用</option>
<option value="1"<?php echo $cdn == 1 ? ' selected' : '';?>>已使用</option>
</select>&nbsp;
<?php tips('如果域名解析开启过CND加速，需要选择已使用，否则可能会导致IP获取错误，具体请在系统体检里检查');?>
</td>
</tr>
<tr>
<td class="tl">Cookie作用域</td>
<td><input name="config[cookie_domain]" type="text" value="<?php echo $cookie_domain;?>" size="20"/><?php tips('例如要保证顶级域名destoon.com所有二级域名均可正常登录注销，则填写.destoon.com(注意顶级域名前加.)');?></td>
</tr>
<tr>
<td class="tl">用户注册文件名</td>
<td><input name="setting[file_register]" type="text" value="<?php echo $file_register;?>" size="20"/><input name="old_file_register" type="hidden" value="<?php echo $file_register;?>"/> <a href="<?php echo $MODULE[2]['linkurl'];?><?php echo $file_register;?>" target="_blank" class="t">访问</a><?php tips('请保证对应文件可写，提交后系统会尝试修改，如果系统修改失败，请通过ftp修改<br/>文件名建议使用数字和字母，文件保存于 member/ 和 mobile/member/ 目录');?></td>
</tr>
<tr>
<td class="tl">用户登录文件名</td>
<td><input name="setting[file_login]" type="text" value="<?php echo $file_login;?>" size="20"/><input name="old_file_login" type="hidden" value="<?php echo $file_login;?>"/> <a href="<?php echo $MODULE[2]['linkurl'];?><?php echo $file_login;?>" target="_blank" class="t">访问</a></td>
</tr>
<tr>
<td class="tl">用户发布信息文件名</td>
<td><input name="setting[file_my]" type="text" value="<?php echo $file_my;?>" size="20"/><input name="old_file_my" type="hidden" value="<?php echo $file_my;?>"/> <a href="<?php echo $MODULE[2]['linkurl'];?><?php echo $file_my;?>" target="_blank" class="t">访问</a></td>
</tr>
</table>
</div>

<div id="Tabs4" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">水印类型</td>
<td>
<label><input type="radio" name="setting[water_type]" value="0"<?php if($water_type==0){ ?> checked<?php } ?> /> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[water_type]" value="1"<?php if($water_type==1){ ?> checked<?php } ?> /> 文字水印</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[water_type]" value="2"<?php if($water_type==2){ ?> checked<?php } ?> /> 图片水印</label>
</td>
</tr>

<tr>
<td class="tl">水印图片或文字边距</td>
<td><input name="setting[water_margin]" type="text" value="<?php echo $water_margin;?>" size="5"> px <?php tips('水印图片或文字在原图的边距');?>
</td>
</tr>
<tr>
<td class="tl">图片处理条件</td>
<td><input name="setting[water_min_wh]" type="text" value="<?php echo $water_min_wh;?>" size="5"> px <?php tips('图片宽度或者高度小于此值将不做水印处理');?>
</td>
</tr>
<tr>
<td class="tl">水印位置</td>
<td>
	<table cellspacing="1" cellpadding="5" width="150" bgcolor="#DDDDDD" class="ctb">
	<tr align="center" bgcolor="#FFFFFF">
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="1" <?php if($water_pos==1){ ?> checked<?php } ?>/></label> </td>
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="2" <?php if($water_pos==2){ ?> checked<?php } ?>/></label> </td>
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="3" <?php if($water_pos==3){ ?> checked<?php } ?>/></label> </td>
	</tr>

	<tr align="center" bgcolor="#FFFFFF">
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="4" <?php if($water_pos==4){ ?> checked<?php } ?>/></label> </td>
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="5" <?php if($water_pos==5){ ?> checked<?php } ?>/></label> </td>
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="6" <?php if($water_pos==6){ ?> checked<?php } ?>/></label> </td>
	</tr>

	<tr align="center" bgcolor="#FFFFFF">
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="7" <?php if($water_pos==7){ ?> checked<?php } ?>/></label> </td>
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="8" <?php if($water_pos==8){ ?> checked<?php } ?>/></label> </td>
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'"> <label><input type="radio" name="setting[water_pos]" value="9" <?php if($water_pos==9){ ?> checked<?php } ?>/></label> </td>
	</tr>
	<tr align="center" bgcolor="#FFFFFF">
	<td onmouseover="this.style.backgroundColor='#FEB685'" onmouseout="this.style.backgroundColor='#FFFFFF'" colspan="3"><label>随机 <input type="radio" name="setting[water_pos]" value="0"<?php if($water_pos==0){ ?> checked<?php } ?>/></label></td>
	</tr>
	</table>
</tr>
<tr>
<td class="tl">BMP图片转JPG格式</td>
<td>
<label><input type="radio" name="setting[bmp_jpg]" value="1"<?php if($bmp_jpg==1){ ?> checked<?php } ?> /> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[bmp_jpg]" value="0"<?php if($bmp_jpg==0){ ?> checked<?php } ?> /> 关闭</label>
<?php tips('BMP格式图片体积较大，且不能生成缩略图，建议开启');?>
</td>
</tr>
<tr>
<td class="tl">GIF图片保留动画</td>
<td>
<label><input type="radio" name="setting[gif_ani]" value="1"<?php if($gif_ani==1){ ?> checked<?php } ?> /> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[gif_ani]" value="0"<?php if($gif_ani==0){ ?> checked<?php } ?> /> 关闭</label>
<?php tips('开启之后，系统对有动画效果的GIF图片不添加水印或缩略');?>
</td>
</tr> 
<tr>
<td class="tl">产品标题图数量限制</td>
<td>
<input name="setting[thumb_max]" type="text" value="<?php echo $thumb_max;?>" size="3"/><?php tips('请填写3至99之间的数字');?>
</td>
</tr>
<tr>
<td class="tl">产品大图加公司名水印</td>
<td>
<label><input type="radio" name="setting[water_com]" value="1"<?php if($water_com==1){ ?> checked<?php } ?> /> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[water_com]" value="0"<?php if($water_com==0){ ?> checked<?php } ?> /> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">产品中图加水印</td>
<td>
<label><input type="radio" name="setting[water_middle]" value="1"<?php if($water_middle==1){ ?> checked<?php } ?> /> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[water_middle]" value="0"<?php if($water_middle==0){ ?> checked<?php } ?> /> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">产品中图缩略大小</td>
<td><input name="setting[middle_w]" type="text" value="<?php echo $middle_w;?>" size="3"/> X <input name="setting[middle_h]" type="text" value="<?php echo $middle_h;?>" size="3"/> px
</td>
</tr>
<tr>
<td class="tl">产品图片缩略模式</td>
<td>
<label><input type="radio" name="setting[thumb_album]" value="0"<?php if($thumb_album==0){ ?> checked<?php } ?> /> 裁剪</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[thumb_album]" value="1"<?php if($thumb_album==1){ ?> checked<?php } ?> /> 压缩</label>
<?php tips('裁剪模式，图片显示清晰，缩略图可能会被裁多余部分<br/>压缩模式，图片显示完整，缩略图可能会留白边');?>
</td>
</tr>
<tr>
<td class="tl">标题图片缩略模式</td>
<td>
<label><input type="radio" name="setting[thumb_title]" value="0"<?php if($thumb_title==0){ ?> checked<?php } ?> /> 裁剪</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[thumb_title]" value="1"<?php if($thumb_title==1){ ?> checked<?php } ?> /> 压缩</label>
</td>
</tr>
<tr>
<td class="tl">图片文件最大宽度</td>
<td><input name="setting[max_image]" type="text" value="<?php echo $max_image;?>" size="5"/> px
<?php tips('由于显示器宽度有限，超过此宽度的图片将被等比调整为此宽度以节省存储空间');?>
</td>
</tr>
<tr>
<td class="tl">水印图片</td>
<td><input name="setting[water_mark]" type="text" value="<?php echo $water_mark;?>" size="40"/> <?php tips('水印图片请上传到static/image目录');?><br/>
<img src="static/image/<?php echo $water_mark;?>"/></td>
</tr>
<tr>
<td class="tl">水印透明度</td>
<td><input name="setting[water_transition]" type="text" value="<?php echo $water_transition;?>" size="5"/><?php tips('如果水印图为gif格式，请设置范围为 1~100 的整数,数值越小水印图片越透明。PNG 类型水印本身具有真彩透明效果，无须此设置');?></td>
</tr>
<tr>
<td class="tl">JPEG 水印质量</td>
<td><input name="setting[water_jpeg_quality]" type="text" value="<?php echo $water_jpeg_quality;?>" size="5"/><?php tips('范围为 0~100 的整数,数值越大结果图片效果越好,但尺寸也越大');?></td>
</tr>
<tr>
<td class="tl">水印文字</td>
<td><input name="setting[water_text]" type="text" id="water_text" value="<?php echo $water_text;?>" size="30" style="color:<?php echo $water_fontcolor;?>;font-size:<?php echo $water_fontsize;?>px;"/></td>
</tr>
<tr>
<td class="tl">中文字体</td>
<td><input name="setting[water_font]" type="text" value="<?php echo $water_font;?>" size="30"> <?php tips('字体文件请上传到file/font目录');?> <?php if($water_font && !is_file(DT_ROOT."/file/font/".$water_font)){ ?><span class="f_red">字体不存在,请上传字体到./file/font/目录</span><?php } ?></td>
</tr>
<tr>
<td class="tl">文字大小</td>
<td><input name="setting[water_fontsize]" type="text" value="<?php echo $water_fontsize;?>" size="8" style="font-size:<?php echo $water_fontsize;?>px;" onblur="this.style.fontSize=this.value+'px';Dd('water_text').style.fontSize=this.value+'px';"/> px</td>
</tr>
<tr>
<td class="tl">文字颜色</td>
<td><input name="setting[water_fontcolor]" type="text" value="<?php echo $water_fontcolor;?>" size="8" style="color:<?php echo $water_fontcolor;?>" onblur="this.style.color=this.value == '#FFFFFF' ? '#333333' : this.value;Dd('water_text').style.color=this.value;"/></td>
</tr>
</table>
</div>

<div id="Tabs5" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">发送方式</td>
<td>
<label><input type="radio" name="setting[mail_type]" value="close" <?php if($mail_type=="close"){ ?> checked<?php } ?> id="mailtype_close"/> 关闭邮件发送</label><br/>
<label><input type="radio" name="setting[mail_type]" value="smtp" <?php if($mail_type=="smtp"){ ?> checked<?php } ?> onclick="Ds('dsmtp');Ds('demail');Dd('l_rn').checked=true;" id="mailtype_smtp"/> 通过SMTP SOCKET 连接 SMTP 服务器发送(支持ESMTP验证)</label><br/>
<label><input type="radio" name="setting[mail_type]" value="mail"  <?php if($mail_type=="mail"){ ?> checked<?php } ?> onclick="Dh('dsmtp');Dh('demail');Dd('l_n').checked=true;" id="mailtype_mail"/> 通过PHP mail 函数发送(通常为Unix/Linux 主机)</label><br/>
<label><input type="radio" name="setting[mail_type]" value="psmtp"  <?php if($mail_type=="psmtp"){ ?> checked<?php } ?> onclick="Ds('dsmtp');Dh('demail');Dd('l_rn').checked=true;" id="mailtype_psmtp"/> 通过PHP mail 函数SMTP发送(通常为WIN主机)</label><br/>
<label><input type="radio" name="setting[mail_type]" value="sc"  <?php if($mail_type=="sc"){ ?> checked<?php } ?> onclick="Dh('dsmtp');Ds('demail');Dd('l_n').checked=true;" id="mailtype_sc"/> 通过 <a href="<?php echo gourl('https://sendcloud.sohu.com/');?>" target="_blank" class="t">SendCloud</a> 发送<?php tips('SendCloud邮件发送可以保证更高的送达率，详情可以进入官方网站了解<br/>邮箱帐号请填写申请到的API_USER，邮箱密码请填写申请到的API_KEY');?></label>
</td>
</tr>
<tr>
<td class="tl">邮件头的分隔符</td>
<td>
<label><input type="radio" name="setting[mail_delimiter]" value="1"<?php if($mail_delimiter==1){ ?> checked<?php } ?> id="l_rn"/> 使用 CRLF 作为分隔符(通常为Windows主机)</label><br/>
<label><input type="radio" name="setting[mail_delimiter]" value="2"<?php if($mail_delimiter==2){ ?> checked<?php } ?> id="l_n"/> 使用 LF 作为分隔符(通常为Unix/Linux主机)</label><br/>
<label><input type="radio" name="setting[mail_delimiter]" value="3"<?php if($mail_delimiter==3){ ?> checked<?php } ?> id="l_r"/> 使用 CR 作为分隔符(通常为Mac主机)</label>
</td>
</tr>
<tbody id="dsmtp" style="display:<?php if($mail_type == "mail") echo 'none';?>">
<tr> 
<td class="tl">SMTP服务器</td>
<td><input name="setting[smtp_host]" id="smtp_host" type="text" size="40" value="<?php echo $smtp_host;?>"/><?php tips('SMTP服务器,例如smtp.xxx.com<br/>提示:目前大部分新申请的免费邮箱并不支持smtp发信');?></td>
</tr>
<tr> 
<td class="tl">SMTP端口</td>
<td><input name="setting[smtp_port]" id="smtp_port" type="text" size="5" value="<?php echo $smtp_port;?>"/></td>
</tr>
</tbody>
<tbody id="demail" style="display:<?php if($mail_type != "smtp") echo 'none';?>">
<tr> 
<td class="tl">SMTP服务器是否验证</td>
<td>
<label><input type="radio" name="setting[smtp_auth]" value="1"<?php if($smtp_auth==1){ ?> checked<?php } ?> id="smtp_auth" onclick="Ds('dsmtp_user');Ds('dsmtp_pass');"/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[smtp_auth]" value="0"<?php if($smtp_auth==0){ ?> checked<?php } ?> onclick="Dh('dsmtp_user');Dh('dsmtp_pass');"/> 否</label>
</tr>
<tr id="dsmtp_user" style="display:<?php if(!$smtp_auth) echo 'none';?>">
<td class="tl">邮箱帐号</td>
<td><input name="setting[smtp_user]" id="smtp_user" type="text" size="40" value="<?php echo $smtp_user;?>"/><?php tips('SMTP服务器的用户帐号,一般为邮件地址');?></td>
</tr>
<tr id="dsmtp_pass" style="display:<?php if(!$smtp_auth) echo 'none';?>"> 
<td class="tl">邮箱密码</td>
<td><input name="setting[smtp_pass]" type="text" id="smtp_pass" size="40" value="<?php echo $smtp_pass;?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
</tbody>
<tr> 
<td class="tl">邮件签名</td>
<td><textarea name="setting[mail_sign]" id="mail_sign" cols="60" rows="4"><?php echo $mail_sign;?></textarea></td>
</tr>
<tr> 
<td class="tl"></td>
<td class="ts">支持HTML语法 <a href="javascript:;" class="t" onclick="Dalert(Dd('mail_sign').value, 500);">[预览签名]</a></td>
</tr>
<tr>
<td class="tl">发件人邮箱</td>
<td><input name="setting[mail_sender]" id="mail_sender" type="text" size="40" value="<?php echo $mail_sender;?>"/><?php tips('系统发送的信件将以此邮箱名义发送');?></td>
</tr>
<tr> 
<td class="tl">发件人名称</td>
<td><input name="setting[mail_name]" id="mail_name" type="text" size="40" value="<?php echo $mail_name;?>"/><?php tips('系统发送的信件将显示此名称，不填则显示网站名');?></td>
</tr>
<tr> 
<td class="tl">测试收件人</td>
<td><input name="testemail" type="text" id="testemail" value="<?php echo $_email;?>" size="30"/> <input type="button" class="btn" value="测试发送" onclick="TestMail();"/><?php tips('请在左侧输入一个接收测试邮件的邮件地址');?></td>
</tr>
<tr> 
<td class="tl">邮件发送记录</td>
<td>
<label><input type="radio" name="setting[mail_log]" value="1"<?php if($mail_log) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[mail_log]" value="0"<?php if(!$mail_log) echo 'checked';?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&action=record', '邮件发送记录');" class="t">[查看记录]</a>
</td>
</td>
</tr>
<tr> 
<td class="tl">自动转发未读站内信</td>
<td>
<label><input type="radio" name="setting[message_email]" value="1"<?php if($message_email) echo 'checked';?> onclick="Ds('dmessage');"/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[message_email]" value="0"<?php if(!$message_email) echo 'checked';?> onclick="Dh('dmessage');"/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=message', '站内信记录');" class="t">[查看记录]</a>
</td>
</tr>
<tbody id="dmessage" style="display:<?php if(!$message_email) echo 'none';?>">
<tr> 
<td class="tl">自动转发会员组</td>
<td><?php echo group_checkbox('setting[message_group][]', $message_group, '1,2,3,4');?></td>
</tr>
<tr> 
<td class="tl">未读时间限制</td>
<td><input name="setting[message_time]" type="text" size="5" value="<?php echo $message_time;?>"/> 分钟
<?php tips('未读时间超过此时间时开始转发');?></td>
</tr>
<tr> 
<td class="tl">转发信件类型</td>
<td>
<?php
$NAME = array('普通', '询价', '报价', '留言', '信使');
$message_type = explode(',', $message_type);
foreach($NAME as $k=>$v) {
	$checked = in_array($k, $message_type) ? ' checked' : '';
	echo '<input type="checkbox" name="setting[message_type][]" value="'.$k.'"'.$checked.'/> '.$v.'&nbsp;';
}
?>
</td>
</tr>
<tr> 
<td class="tl">同时推送给微信</td>
<td>
<label><input type="radio" name="setting[message_weixin]" value="1"<?php if($message_weixin) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[message_weixin]" value="0"<?php if(!$message_weixin) echo 'checked';?>/> 关闭</label><?php tips('仅在开启了微信公众号，且会员绑定了微信，且会员在48小时内打开过公众号才能推送成功');?>
</td>
</tr>
</tbody>
</table>
</div>

<div id="Tabs6" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">设置模块为网站首页</td>
<td>
<select name="setting[page_mid]">
<option value="0">选择</option>
<?php 
foreach($MODULE as $v) {
	if($v['moduleid'] < 4 || $v['islink']) continue;
	echo '<option value="'.$v['moduleid'].'"'.($page_mid == $v['moduleid'] ? ' selected' : '').'>'.$v['name'].'</option>';
} 
?>
</select><?php tips('如果选择了具体模块，网站首页将直接显示模块首页内容');?>
</td>
</tr>
<tr>
<td class="tl">首页推荐商品数量</td>
<td><input type="text" name="setting[page_mall]" value="<?php echo $page_mall;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐供应数量</td>
<td><input type="text" name="setting[page_sell]" value="<?php echo $page_sell;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐招商数量</td>
<td><input type="text" name="setting[page_info]" value="<?php echo $page_info;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐团购数量</td>
<td><input type="text" name="setting[page_group]" value="<?php echo $page_group;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页图片资讯数量</td>
<td><input type="text" name="setting[page_newst]" value="<?php echo $page_newst;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页显示头条资讯</td>
<td><label><input type="radio" name="setting[page_newsh]" value="1"<?php if($page_newsh){ ?> checked<?php } ?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[page_newsh]" value="0"<?php if(!$page_newsh){ ?> checked<?php } ?>/> 否</label></td>
</tr>
<tr>
<td class="tl">首页推荐资讯数量</td>
<td><input type="text" name="setting[page_news]" value="<?php echo $page_news;?>" size="5"/> X 2</td>
</tr>
<tr>
<td class="tl">首页推荐专题数量</td>
<td><input type="text" name="setting[page_special]" value="<?php echo $page_special;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐视频数量</td>
<td><input type="text" name="setting[page_video]" value="<?php echo $page_video;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐图库数量</td>
<td><input type="text" name="setting[page_photo]" value="<?php echo $page_photo;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页品牌展示数量</td>
<td><input type="text" name="setting[page_brand]" value="<?php echo $page_brand;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页行业展会数量</td>
<td><input type="text" name="setting[page_exhibit]" value="<?php echo $page_exhibit;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐招聘数量</td>
<td><input type="text" name="setting[page_job]" value="<?php echo $page_job;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页行业知道数量</td>
<td><input type="text" name="setting[page_know]" value="<?php echo $page_know;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页资料下载数量</td>
<td><input type="text" name="setting[page_down]" value="<?php echo $page_down;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页推荐商圈数量</td>
<td><input type="text" name="setting[page_club]" value="<?php echo $page_club;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页图片链接数量</td>
<td><input type="text" name="setting[page_logo]" value="<?php echo $page_logo;?>" size="5"/></td>
</tr>
<tr>
<td class="tl">首页文字链接数量</td>
<td><input type="text" name="setting[page_text]" value="<?php echo $page_text;?>" size="5"/></td>
</tr>
</table>
</div>

<div id="Tabs7" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl t_c">配置说明</td>
<td class="ts">
通过对页面主要颜色的自定义，可使网站快速呈现不同的视觉效果<br/>
颜色值为16进制，规则为#加6位颜色代码，例如#FF0000<br/>
如果保存后对应页面刷新不改变，可以尝试按Ctrl+F5强制刷新<br/>
此处未列出的属性可以点下方修改模板实现重写<br/>
</td>
</tr>
</table>

<?php
$C = array(
	array('默认文字', 'home_text', '#333333', ''),
	array('高亮链接', 'home_link', '#024893', ''),
	array('链接经过', 'home_hover', '#FF3300', ''),
	array('菜单背景', 'home_menu', '#0679D4', ''),
	array('菜单选中', 'home_menu_on', '#00599C', ''),
	array('菜单经过', 'home_menu_ov', '#0065BD', ''),
);
?>
<table cellspacing="0" class="tb ls" id="color-home">
<tr>
<th width="155">电脑站</th>
<th width="100">预览</th>
<th width="100">颜色代码</th>
<th width="100">默认</th>
<th width="100">颜色代码</th>
<th></th>
</tr>
<?php foreach($C as $v) { ?>
<tr align="center">
<td title="$css[<?php echo $v[1];?>]"><?php echo $v[0];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $CSS[$v[1]];?>;" id="color-<?php echo $v[1];?>"/></td>
<td><input type="text" name="css[<?php echo $v[1];?>]" value="<?php echo $CSS[$v[1]];?>" style="color:<?php echo $CSS[$v[1]] == '#FFFFFF' ? '#333333' : $CSS[$v[1]];?>;" id="<?php echo $v[1];?>" size="10" maxlength="7" ondblclick="this.value='';" onblur="if(this.value.length==7){this.style.color=this.value == '#FFFFFF' ? '#333333' : this.value;Dd('color-<?php echo $v[1];?>').style.backgroundColor=this.value;}else if(this.value.length==0){Dd('color-<?php echo $v[1];?>').style.backgroundColor='';}"/></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[2];?>;"/></td>
<td style="color:<?php echo $v[2];?>;"><?php echo $v[2];?></td>
<td align="left"><?php echo $v[3] ? tips($v[3]) : '';?></td>
</tr>
<?php } ?>
<tr align="center">
<td><a href="javascript:;" class="t" onclick="Dwidget('?file=template&action=edit&dir=chip&fileid=reset-home', '修改模板');">修改模板</a></td>
<td><a href="file/style/home.reset.css?v=<?php echo DT_TIME;?>" class="t" target="_blank" title="点击打开生成的样式重写文件">CSS</a></td>
<td><a href="javascript:;" class="t" onclick="if(confirm('确定要恢复默认吗？当前设置将被清空')) $('#color-home input').val('');">恢复默认</a></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>

<?php
$C = array(
	array('头部背景', 'mobile_head', '#F7F7F7', ''),
	array('头部文字', 'mobile_text', '#444444', '如果设置了深色背景，必须将头部文字颜色设置为白色#FFFFFF'),
);
?>
<table cellspacing="0" class="tb ls" id="color-mobile">
<tr>
<th width="155">手机站</th>
<th width="100">预览</th>
<th width="100">颜色代码</th>
<th width="100">默认</th>
<th width="100">颜色代码</th>
<th></th>
</tr>
<?php foreach($C as $v) { ?>
<tr align="center">
<td title="$css[<?php echo $v[1];?>]"><?php echo $v[0];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $CSS[$v[1]];?>;" id="color-<?php echo $v[1];?>"/></td>
<td><input type="text" name="css[<?php echo $v[1];?>]" value="<?php echo $CSS[$v[1]];?>" style="color:<?php echo $CSS[$v[1]] == '#FFFFFF' ? '#333333' : $CSS[$v[1]];?>;" id="<?php echo $v[1];?>" size="10" maxlength="7" ondblclick="this.value='';" onblur="if(this.value.length==7){this.style.color=this.value == '#FFFFFF' ? '#333333' : this.value;Dd('color-<?php echo $v[1];?>').style.backgroundColor=this.value;}else if(this.value.length==0){Dd('color-<?php echo $v[1];?>').style.backgroundColor='';}"/></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[2];?>;"/></td>
<td style="color:<?php echo $v[2];?>;"><?php echo $v[2];?></td>
<td align="left"><?php echo $v[3] ? tips($v[3]) : '';?></td>
</tr>
<?php } ?>
<tr align="center">
<td><a href="javascript:;" class="t" onclick="Dwidget('?file=template&action=edit&dir=chip&fileid=reset-mobile', '修改模板');">修改模板</a></td>
<td><a href="file/style/mobile.reset.css?v=<?php echo DT_TIME;?>" class="t" target="_blank" title="点击打开生成的样式重写文件">CSS</a></td>
<td><a href="javascript:;" class="t" onclick="if(confirm('确定要恢复默认吗？当前设置将被清空')) $('#color-mobile input').val('');">恢复默认</a></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>

<?php
$C = array(
	array('默认文字', 'member_text', '#000000', ''),
	array('高亮链接', 'member_link', '#024893', ''),
	array('链接经过', 'member_hover', '#FF6600', ''),
	array('头部背景', 'member_head', '#0679D4', ''),
	array('头部选中', 'member_head_on', '#00599C', ''),
	array('头部经过', 'member_head_ov', '#0065BD', ''),
);
?>
<table cellspacing="0" class="tb ls" id="color-member">
<tr>
<th width="155">会员中心</th>
<th width="100">预览</th>
<th width="100">颜色代码</th>
<th width="100">默认</th>
<th width="100">颜色代码</th>
<th></th>
</tr>
<?php foreach($C as $v) { ?>
<tr align="center">
<td title="$css[<?php echo $v[1];?>]"><?php echo $v[0];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $CSS[$v[1]];?>;" id="color-<?php echo $v[1];?>"/></td>
<td><input type="text" name="css[<?php echo $v[1];?>]" value="<?php echo $CSS[$v[1]];?>" style="color:<?php echo $CSS[$v[1]] == '#FFFFFF' ? '#333333' : $CSS[$v[1]];?>;" id="<?php echo $v[1];?>" size="10" maxlength="7" ondblclick="this.value='';" onblur="if(this.value.length==7){this.style.color=this.value == '#FFFFFF' ? '#333333' : this.value;Dd('color-<?php echo $v[1];?>').style.backgroundColor=this.value;}else if(this.value.length==0){Dd('color-<?php echo $v[1];?>').style.backgroundColor='';}"/></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[2];?>;"/></td>
<td style="color:<?php echo $v[2];?>;"><?php echo $v[2];?></td>
<td align="left"><?php echo $v[3] ? tips($v[3]) : '';?></td>
</tr>
<?php } ?>
<tr align="center">
<td><a href="javascript:;" class="t" onclick="Dwidget('?file=template&action=edit&dir=chip&fileid=reset-member', '修改模板');">修改模板</a></td>
<td><a href="file/style/member.reset.css?v=<?php echo DT_TIME;?>" class="t" target="_blank" title="点击打开生成的样式重写文件">CSS</a></td>
<td><a href="javascript:;" class="t" onclick="if(confirm('确定要恢复默认吗？当前设置将被清空')) $('#color-member input').val('');">恢复默认</a></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>

<?php
$C = array(
	array('默认文字', 'admin_text', '#000000', ''),
	array('高亮链接', 'admin_link', '#2B579A', ''),
	array('链接经过', 'admin_hover', '#FF3300', ''),
	array('右侧标签', 'admin_tab', '#2B579A', ''),
	array('左侧工具条', 'admin_side_bar', '#000000', ''),
	array('左侧栏背景', 'admin_side_bg', '#2E2E2E', ''),
	array('左侧链接经过', 'admin_side_ov', '#4A4A4A', ''),
	array('左侧菜单经过', 'admin_side_menu', '#666666', ''),
);
?>
<table cellspacing="0" class="tb ls" id="color-admin">
<tr>
<th width="155">网站后台</th>
<th width="100">预览</th>
<th width="100">颜色代码</th>
<th width="100">默认</th>
<th width="100">颜色代码</th>
<th></th>
</tr>
<?php foreach($C as $v) { ?>
<tr align="center">
<td title="$css[<?php echo $v[1];?>]"><?php echo $v[0];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $CSS[$v[1]];?>;" id="color-<?php echo $v[1];?>"/></td>
<td><input type="text" name="css[<?php echo $v[1];?>]" value="<?php echo $CSS[$v[1]];?>" style="color:<?php echo $CSS[$v[1]] == '#FFFFFF' ? '#333333' : $CSS[$v[1]];?>;" id="<?php echo $v[1];?>" size="10" maxlength="7" ondblclick="this.value='';" onblur="if(this.value.length==7){this.style.color=this.value == '#FFFFFF' ? '#333333' : this.value;Dd('color-<?php echo $v[1];?>').style.backgroundColor=this.value;}else if(this.value.length==0){Dd('color-<?php echo $v[1];?>').style.backgroundColor='';}"/></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[2];?>;"/></td>
<td style="color:<?php echo $v[2];?>;"><?php echo $v[2];?></td>
<td align="left"><?php echo $v[3] ? tips($v[3]) : '';?></td>
</tr>
<?php } ?>
<tr align="center">
<td><a href="javascript:;" class="t" onclick="Dwidget('?file=template&action=edit&dir=chip&fileid=reset-admin', '修改模板');">修改模板</a></td>
<td><a href="file/style/admin.reset.css?v=<?php echo DT_TIME;?>" class="t" target="_blank" title="点击打开生成的样式重写文件">CSS</a></td>
<td><a href="javascript:;" class="t" onclick="if(confirm('确定要恢复默认吗？当前设置将被清空')) $('#color-admin input').val('');">恢复默认</a></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>

<?php
$C = array(
	array('蓝', '#0078D4', '#006AD6', '#007FFF'),
	array('绿', '#00B042', '#009036', '#44B549'),
	array('红', '#E2231A', '#C81623', '#E8554D'),
	array('橙', '#FF6600', '#FF4400', '#FF6D38'),
	array('黄', '#F4C800', '#E3AC1A', '#FEE151'),
	array('紫', '#B963D3', '#B03CD3', '#C187D2'),
	array('黑', '#2E2E2E', '#000000', '#4A4A4A'),
);
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="155">色系参考</th>
<th width="100">主色</th>
<th width="100">颜色代码</th>
<th width="100">较深</th>
<th width="100">颜色代码</th>
<th width="100">较浅</th>
<th width="100">颜色代码</th>
<th></th>
</tr>
<?php foreach($C as $v) { ?>
<tr align="center">
<td><?php echo $v[0];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[1];?>;"/></td>
<td style="color:<?php echo $v[1];?>;"><?php echo $v[1];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[2];?>;"/></td>
<td style="color:<?php echo $v[2];?>;"><?php echo $v[2];?></td>
<td><img src="static/image/spacer.png" style="width:24px;height:24px;background:<?php echo $v[3];?>;"/></td>
<td style="color:<?php echo $v[3];?>;"><?php echo $v[3];?></td>
<td></td>
</tr>
<?php } ?>
</table>
</div>

<div id="Tabs8" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl f_b">官方云接口</td>
<td class="tr"></td>
</tr>
<tr>
<td class="tl">服务帐号</td>
<td><input name="config[cloud_uid]" type="text" id="cloud_uid" value="<?php echo $cloud_uid;?>" size="30"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?file=cloud&action=key" target="_blank" class="t">[帐号申请]</a></td> 
</tr>
<tr>
<td class="tl">服务密钥</td>
<td><input name="config[cloud_key]" type="text" id="cloud_key" value="<?php echo $cloud_key;?>" size="30" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
<tr>
<td class="tl">提示信息</td>
<td class="f_red">以下官方云服务需要先填写正确的帐号和密钥方可开启成功</td>
</tr>
<tr>
<td class="tl">HTPPS连接</td>
<td>
<label><input type="radio" name="config[cloud_ssl]" value="1"<?php if($cloud_ssl == 1){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="config[cloud_ssl]" value="0"<?php if($cloud_ssl == 0){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Diframe('?file=<?php echo $file;?>&action=https', 0, 0, 1);" class="t">[连接测试]</a> <?php tips('如果测试通过，建议开启并使用更安全的HTPPS连接，如果测试未通过，必须选择关闭');?>
</td>
</tr>
<tr>
<td class="tl">手机短信</td>
<td>
<label><input type="radio" name="setting[sms]" value="1"<?php if($sms){ ?> checked<?php } ?> onclick="Ds('dsms');"/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[sms]" value="0"<?php if(!$sms){ ?> checked<?php } ?> onclick="Dh('dsms');"/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&action=record', '短信记录');" class="t">[查看记录]</a>
</td>
</tr>
<tbody id="dsms" style="display:<?php if(!$sms) echo 'none';?>">
<?php if(DT_CLOUD_UID && DT_CLOUD_KEY) { ?>
<tr>
<td class="tl">短信余额</td>
<td><span class="f_red" id="sms_balance"></span> 条&nbsp;&nbsp;&nbsp;&nbsp;<a href="?file=cloud&action=smsbuy" target="_blank" class="t">[在线购买]</a></td> 
</tr>
<?php } ?>
<tr>
<td class="tl">短信单价</td>
<td><input name="setting[sms_fee]" type="text" value="<?php echo $sms_fee;?>" size="5"/> <?php echo $DT['money_unit'];?>/条 <?php tips('此项针对会员收费');?></td> 
</tr>
<tr>
<td class="tl">每日上限</td>
<td><input name="setting[sms_max]" type="text" value="<?php echo $sms_max;?>" size="5"/> 条 <?php tips('特指会员注册、找回密码、手机验证等需要发送验证码场景，同一手机号码或会员每日最大发送数量，填0为不限制，建议填5左右的数字，以免恶意发送');?></td> 
</tr>
<tr>
<td class="tl">短信长度</td>
<td><input name="setting[sms_len]" type="text" value="<?php echo $sms_len;?>" size="5"/> 字/条 <?php tips('一条短信长度，一般为66至70字，以接口实际返回数量为准，超出长度会增加费用，建议单条短信控制在66字以内');?></td> 
</tr>
<tr>
<td class="tl">成功标识</td>
<td><input name="setting[sms_ok]" type="text" value="<?php echo $sms_ok;?>" size="10"/> <?php tips('短信发送成功标识字符，系统根据此字符确定是否扣除会员短信余额');?></td> 
</tr>
<tr>
<td class="tl">短信内容签名</td>
<td><input name="setting[sms_sign]" type="text" value="<?php echo $sms_sign;?>" size="30"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?file=cloud&action=smssign" target="_blank" class="t">[签名申请]</a> <?php tips('将显示在短信内容结尾，以便会员识别，请尽量简短，正确的格式为【签名】，例如 【某某网】');?></td> 
</tr>
</tbody>
<tr>
<td class="tl">快递追踪</td>
<td>
<label><input type="radio" name="setting[cloud_express]" value="1"<?php if($cloud_express){ ?> checked<?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[cloud_express]" value="0"<?php if(!$cloud_express){ ?> checked<?php } ?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&action=express', '快递记录');" class="t">[查看记录]</a>
</td>
</tr>
<tr>
<td class="tl f_b">第三方接口</td>
<td class="tr"></td>
</tr>
<tr>
<td class="tl">短信验证码</td>
<td>
<input type="hidden" name="setting[sms_code]" value="验证码"/>
<?php
$select = '';
$apis = glob(DT_ROOT.'/api/sms/*.php');
$nms = array();
$nms['aliyun'] = '阿里云';
$nms['gyytz'] = '国阳云';
$nms['juhe'] = '聚合数据';
foreach($apis as $v) {
	$v = basename($v, '.php');
	if(strpos($v, '.') !== false) continue;
	$selected = $v == $sms_api ? 'selected' : '';
	$select .= "<option value='".$v."' ".$selected.">".(isset($nms[$v]) ? $nms[$v] : $v)."</option>";
}
$select = '<select name="setting[sms_api]" id="sms_api" onchange="SmsApi();"><option value="">选择接口</option>'.$select.'</select>';
echo $select;
?>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://market.aliyun.com/apimarket/detail/cmapi00066996');?>" target="_blank" class="t" id="sms_link">[账号申请]</a><?php tips('设置此接口代表短信验证码通过此接口发送');?></td> 
</tr>
<tr id="tr_sms_appid">
<td class="tl" id="nm_sms_appid">APP ID</td>
<td><input name="setting[sms_appid]" type="text" value="<?php echo $sms_appid;?>" size="50"/></td> 
</tr>
<tr id="tr_sms_appsecret">
<td class="tl" id="nm_sms_appsecret">APP Secret</td>
<td><input name="setting[sms_appsecret]" type="text" value="<?php echo $sms_appsecret;?>" size="50"/></td> 
</tr>
<tr>
<td class="tl">短信模板</td>
<td><input name="setting[sms_template]" type="text" value="<?php echo $sms_template;?>" size="50"/></td> 
</tr>
<tr id="tr_sms_par">
<td class="tl" id="nm_sms_par">备用参数</td>
<td><input name="setting[sms_par]" type="text" value="<?php echo $sms_par;?>" size="50"/></td> 
</tr>
<tr>
<td class="tl">IP属地AppCode</td>
<td><input name="setting[ip_appcode]" type="text" value="<?php echo $ip_appcode;?>" size="50"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://market.aliyun.com/apimarket/detail/cmapi00066996');?>" target="_blank" class="t">[账号申请]</a><?php tips('填写AppCode代表开启此功能，此功能会根据IP地址返回属地，支持ipv6地址<br/>默认ipv6地址云端查询，ipv4地址本地解析，如果需要ipv4使用云端查询，可以删除本地数据文件 file/ipdata/wry.dat');?></td> 
</tr>
<tr>
<td class="tl">手机属地AppCode</td>
<td><input name="setting[mobile_appcode]" type="text" value="<?php echo $mobile_appcode;?>" size="50"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://market.aliyun.com/apimarket/detail/cmapi00047726');?>" target="_blank" class="t">[账号申请]</a><?php tips('填写AppCode代表开启此功能，此功能会根据手机号返回属地及运营商');?></td> 
</tr>
<tr>
<td class="tl">地址转坐标AppCode</td>
<td><input name="setting[lnglat_appcode]" type="text" value="<?php echo $lnglat_appcode;?>" size="50"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://market.aliyun.com/apimarket/detail/cmapi00054668');?>" target="_blank" class="t">[账号申请]</a><?php tips('填写AppCode代表开启此功能，此功能会根据地址返回地图坐标<br/>地图接口一般自带转换功能，但是可能存在收费或频率限制');?></td> 
</tr>
<tr>
<td class="tl">敏感词监测AppCode</td>
<td><input name="setting[spam_appcode]" type="text" value="<?php echo $spam_appcode;?>" size="50"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://market.aliyun.com/apimarket/detail/cmapi00063146');?>" target="_blank" class="t">[帐号申请]</a> <?php tips('填写AppCode代表开启此功能，此功能会根据第三方接口返回值判断是否拦截会员中心发布的敏感内容<br/>注意：开启之后可能会导致前台信息发布和修改变慢');?></td> 
</tr>
<tr>
<td class="tl">标签生成器AppCode</td>
<td><input name="setting[split_appcode]" type="text" value="<?php echo $split_appcode;?>" size="50"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://market.aliyun.com/apimarket/detail/cmapi018397');?>" target="_blank" class="t">[帐号申请]</a> <?php tips('填写AppCode代表开启此功能，此功能可以根据文章标题云分词生成Tag标签');?></td> 
</tr>
<tr>
<td class="tl">极光推送AppKey</td>
<td><input name="setting[push_appkey]" type="text" value="<?php echo $push_appkey;?>" size="50"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://www.jiguang.cn/');?>" target="_blank" class="t">[帐号申请]</a> <?php tips('填写AppCode代表开启此功能，此功能仅对 <a href=https://www.destoon.com/buy/ target=_blank class=b>独立APP</a> 可用');?></td> 
</tr>
<tr>
<td class="tl">极光推送Master Secret</td>
<td><input name="setting[push_secret]" type="text" value="<?php echo $push_secret;?>" size="50"/></td> 
</tr>
<tr>
<td class="tl">极光推送成功标识</td>
<td><input name="setting[push_ok]" type="text" value="<?php echo $push_ok;?>" size="10"/></td> 
</tr>
</table>
</div>
<script type="text/javascript">
function SmsApi() {
	var api = $('#sms_api').val();
	if(api == 'juhe') {
		$('#sms_link').attr('href', '<?php echo gourl("https://www.juhe.cn/docs/api/id/486");?>');
		$('#nm_sms_appid').html('Key');
		$('#tr_sms_appsecret').hide();
		$('#nm_sms_par').html('备用参数');
	} else if(api == 'gyytz') {
		$('#sms_link').attr('href', '<?php echo gourl("https://market.aliyun.com/apimarket/detail/cmapi00037415");?>');
		$('#nm_sms_appid').html('AppCode');
		$('#tr_sms_appsecret').hide();
		$('#nm_sms_par').html('签名ID');
	} else {
		$('#sms_link').attr('href', '<?php echo gourl("https://market.aliyun.com/apimarket/detail/cmapi00066996");?>');
		$('#nm_sms_appid').html('APP ID');
		$('#tr_sms_appsecret').show();
		$('#nm_sms_par').html('备用参数');
	}
}
function TestMail() {
	if(Dd('testemail').value == '') {
		Dalert('请先输入一个接收测试邮件的邮件地址');
		Dd('testemail').focus();
		return false;
	}
	if(Dd('testemail').value == Dd('mail_sender').value) {
		Dalert('测试收件人请不要与发件人相同');
		Dd('testemail').focus();
		return false;
	}
	var url = '?file=setting&action=mail';
	var mail_type = '';
	if(Dd('mailtype_close').checked) mail_type = 'close';
	if(Dd('mailtype_mail').checked) mail_type = 'mail';
	if(Dd('mailtype_smtp').checked) mail_type = 'smtp';
	if(Dd('mailtype_psmtp').checked) mail_type = 'psmtp';
	if(Dd('mailtype_sc').checked) mail_type = 'sc';
	var mail_delimiter = Dd('l_rn').checked ? 1 : (Dd('l_n').checked ? 2 : 3);
	var smtp_auth = Dd('smtp_auth').checked ? 1 : 0;
	url += '&mail_type='+mail_type+'&mail_delimiter='+mail_delimiter+'&smtp_host='+Dd('smtp_host').value+'&smtp_auth='+smtp_auth+'&smtp_user='+Dd('smtp_user').value+'&smtp_pass='+Dd('smtp_pass').value+'&smtp_port='+Dd('smtp_port').value+'&mail_sender='+Dd('mail_sender').value+'&testemail='+Dd('testemail').value+'&mail_name='+Dd('mail_name').value;
	Diframe(url, 0, 0, 1);
}
</script>
<div class="sbt">
<input type="submit" name="submit" value="保 存" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="展 开" id="ShowAll" class="btn" onclick="TabAll();" title="展 开/合 并"/>
</div>
</form>
<script type="text/javascript">
var tab = <?php echo $tab;?>;
var all = <?php echo $all;?>;
$(function(){
	if(tab) Tab(tab);
	if(all) {all = 0; TabAll();}
	SmsApi();
	if(window.screen.width < 1280) {
		$('.menu div').hide();
	}
});
</script>
<script type="text/javascript" src="<?php echo $sms_url;?>"></script>
<?php include tpl('footer');?>