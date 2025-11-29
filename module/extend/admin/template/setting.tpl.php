<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
$menus = array (
    array('模块设置'),
    array('更新数据', '?moduleid=3&file=html'),
);
show_menu($menus);
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="tab" id="tab" value="<?php echo $tab;?>"/>
<input type="hidden" name="setting[oauth]" value="<?php echo $oauth;?>"/>
<input type="hidden" name="setting[weixin]" value="<?php echo $weixin;?>"/>
<div id="Tabs0" style="display:">
<div class="tt">通用设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">列表页地址规则</td>
<td>
<select name="setting[list_url]">
<option value="0"<?php if($list_url == 0) echo ' selected';?>>例 (动态) list<?php echo DT_EXT;?>?catid=1&amp;page=2</option>
<option value="1"<?php if($list_url == 1) echo ' selected';?>>例 (伪静态) list-1-2.html</option> 
<option value="2"<?php if($list_url == 2) echo ' selected';?>>例 (伪静态) list/1/</option>
</select>
</td>
</tr>
<tr>
<td class="tl">内容页地址规则</td>
<td>
<select name="setting[show_url]">
<option value="0"<?php if($show_url == 0) echo ' selected';?>>例 (动态) show<?php echo DT_EXT;?>?itemid=1&amp;page=2</option>
<option value="1"<?php if($show_url == 1) echo ' selected';?>>例 (伪静态) show-1-2.html</option> 
<option value="2"<?php if($show_url == 2) echo ' selected';?>>例 (伪静态) show/1/</option>
</select>
</td>
</tr>
</table>
<a name="mobile"></a>
<div class="tt">手机版设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">手机版功能</td>
<td>
<label><input type="radio" name="setting[mobile_enable]" value="1"  <?php if($mobile_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[mobile_enable]" value="0"  <?php if(!$mobile_enable) echo 'checked';?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.destoon.com/app/" target="_blank" class="t">[在线打包]</a>
</td>
</tr>
<tr> 
<td class="tl">手机版绑定域名</td>
<td><input name="setting[mobile_domain]" type="text" size="30" value="<?php echo $mobile_domain;?>"/><?php tips('例如 https://m.destoon.com/<br/>请将此域名绑定至网站mobile目录');?></td>
</tr>
<tr> 
<td class="tl">手机版网站简称</td>
<td><input name="setting[mobile_sitename]" type="text" size="10" value="<?php echo $mobile_sitename;?>"/><?php tips('建议控制在5个汉字以内，留空默认显示网站名称');?></td>
</tr>
<tr> 
<td class="tl">手机版首页幻灯广告位ID</td>
<td><input name="setting[mobile_pid]" type="text" size="5" value="<?php echo $mobile_pid;?>" id="mobile_pid"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=ad'+(Dd('mobile_pid').value>0 ? '&action=list&pid='+Dd('mobile_pid').value : ''), '幻灯广告');" class="t">[广告管理]</a> <?php tips('请建立一个幻灯广告位，并填写广告位ID，填0表示不显示幻灯广告');?></td>
</tr>
<tr>
<td class="tl">手机版页面动画效果</td>
<td>
<label><input type="radio" name="setting[mobile_ajax]" value="1"  <?php if($mobile_ajax) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[mobile_ajax]" value="0"  <?php if(!$mobile_ajax) echo 'checked';?>/> 关闭</label><?php tips('开启之后页面有类似APP的滚动切换效果，但是会导致百度等第三方联盟广告无法显示');?>
</td>
</tr>
<tr>
<td class="tl">手机访问自动跳转</td>
<td>
<label><input type="radio" name="setting[mobile_goto]" value="1"  <?php if($mobile_goto) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[mobile_goto]" value="0"  <?php if(!$mobile_goto) echo 'checked';?>/> 关闭</label><?php tips('手机访问电脑版时自动跳转到手机版');?>
</td>
</tr>
<tr>
<td class="tl">电脑访问手机版</td>
<td>
<label><input type="radio" name="setting[mobile_pc]" value="1"  <?php if($mobile_pc) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[mobile_pc]" value="0"  <?php if(!$mobile_pc) echo 'checked';?>/> 关闭</label><?php tips('不建议开启，开启之后可以用电脑访问手机版，因设备差异，部分功能可能异常；如果选择关闭，电脑访问手机版会自动跳转到电脑版地址');?>
</td>
</tr>
<tr> 
<td class="tl">苹果APP下载地址</td>
<td><input name="setting[mobile_ios]" type="text" size="80" value="<?php echo $mobile_ios;?>"/></td>
</tr>
<tr> 
<td class="tl">安卓APP下载地址</td>
<td><input name="setting[mobile_adr]" type="text" size="80" value="<?php echo $mobile_adr;?>"/></td>
</tr>
</table>
<a name="desktop"></a>
<div class="tt">桌面版设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">桌面版功能</td>
<td>
<label><input type="radio" name="setting[desktop_enable]" value="1"  <?php if($desktop_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[desktop_enable]" value="0"  <?php if(!$desktop_enable) echo 'checked';?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.destoon.com/app/desktop.html" target="_blank" class="t">[在线打包]</a>
</td>
</tr>
<tr>
<td class="tl">桌面版起始页模板</td>
<td><?php echo tpl_select('pc', $module, 'setting[desktop_template]', '默认模板', $desktop_template);?></td>
</tr>
<tr> 
<td class="tl">Windows版下载地址</td>
<td><input name="setting[desktop_win_x64]" type="text" size="80" value="<?php echo $desktop_win_x64;?>"/> &nbsp; <span class="f_gray">64位</span></td>
</tr>
<tr> 
<td class="tl"></td>
<td><input name="setting[desktop_win_x86]" type="text" size="80" value="<?php echo $desktop_win_x86;?>"/> &nbsp; <span class="f_gray">32位</span></td>
</tr>
<tr> 
<td class="tl"></td>
<td><input name="setting[desktop_win_x86_x64]" type="text" size="80" value="<?php echo $desktop_win_x86_x64;?>"/> &nbsp; <span class="f_gray">兼容32+64位</span></td>
</tr>
<tr> 
<td class="tl">Mac版下载地址</td>
<td><input name="setting[desktop_mac_dmg]" type="text" size="80" value="<?php echo $desktop_mac_dmg;?>"/> &nbsp; <span class="f_gray">dmg</span></td>
</tr>
<tr>
<td class="tl">Linux版下载地址</td>
<td><input name="setting[desktop_lnx_deb_x86]" type="text" size="80" value="<?php echo $desktop_lnx_deb_x86;?>"/> &nbsp; <span class="f_gray">deb x86</span></td>
</tr>
<tr> 
<td class="tl"></td>
<td><input name="setting[desktop_lnx_deb_arm64]" type="text" size="80" value="<?php echo $desktop_lnx_deb_arm64;?>"/> &nbsp; <span class="f_gray">deb arm64</span></td>
</tr>
<tr>
<td class="tl"></td>
<td><input name="setting[desktop_lnx_rpm_x86]" type="text" size="80" value="<?php echo $desktop_lnx_rpm_x86;?>"/> &nbsp; <span class="f_gray">rpm x86</span></td>
</tr>
<tr> 
<td class="tl"></td>
<td><input name="setting[desktop_lnx_rpm_arm64]" type="text" size="80" value="<?php echo $desktop_lnx_rpm_arm64;?>"/> &nbsp; <span class="f_gray">rpm arm64</span></td>
</tr>
</table>
<a name="spread"></a>
<div class="tt">排名推广</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">推广功能</td>
<td>
<label><input type="radio" name="setting[spread_enable]" value="1"  <?php if($spread_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[spread_enable]" value="0"  <?php if(!$spread_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">排名推广绑定域名</td>
<td><input name="setting[spread_domain]"  type="text" size="30" value="<?php echo $spread_domain;?>"/><?php tips('例如 https://spread.destoon.com/<br/>请将此域名绑定至网站spread目录');?></td>
</tr>
<tr> 
<td class="tl">排名默认起价</td>
<td><input name="setting[spread_price]"  type="text" size="5" value="<?php echo $spread_price;?>"/></td>
</tr>
<tr>
<td class="tl">加价幅度</td>
<td><input name="setting[spread_step]"  type="text" size="5" value="<?php echo $spread_step;?>"/><?php tips('如果设置了加价幅度，则出价必须是起价加加价幅度的倍数');?></td>
</tr>
<tr>
<td class="tl">最多可购买月数</td>
<td><input name="setting[spread_month]"  type="text" size="5" value="<?php echo $spread_month;?>"/><?php tips('以月为单位 最少为1个月');?></td>
</tr>
<tr>
<td class="tl">同一月单词最多购买次数</td>
<td><input name="setting[spread_max]"  type="text" size="5" value="<?php echo $spread_max;?>"/></td>
</tr>
<tr>
<td class="tl">购买排名需要审核</td>
<td>
<label><input type="radio" name="setting[spread_check]" value="1"  <?php if($spread_check) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[spread_check]" value="0"  <?php if(!$spread_check) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">历史排名列表</td>
<td>
<label><input type="radio" name="setting[spread_list]" value="1"  <?php if($spread_list) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[spread_list]" value="0"  <?php if(!$spread_list) echo 'checked';?>/> 关闭</label> <?php tips('如果选择关闭，只显示最新的第一页推广记录，并且不显示分页');?>
</td>
</tr>
<tr>
<td class="tl">购买排名使用</td>
<td>
<label><input type="radio" name="setting[spread_currency]" value="money"  <?php if($spread_currency == 'money') echo 'checked';?>/> <?php echo $DT['money_name'];?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[spread_currency]" value="credit"  <?php if($spread_currency == 'credit') echo 'checked';?>/> <?php echo $DT['credit_name'];?></label>
</td>
</tr>
</table>

<a name="ad"></a>
<div class="tt">广告设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">广告功能</td>
<td>
<label><input type="radio" name="setting[ad_enable]" value="1"  <?php if($ad_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ad_enable]" value="0"  <?php if(!$ad_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">广告绑定域名</td>
<td><input name="setting[ad_domain]"  type="text" size="30" value="<?php echo $ad_domain;?>"/><?php tips('例如 https://ad.destoon.com/<br/>请将此域名绑定至网站ad目录');?></td>
</tr>
<tr>
<td class="tl">广告位预览</td>
<td>
<label><input type="radio" name="setting[ad_view]" value="1"  <?php if($ad_view) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ad_view]" value="0"  <?php if(!$ad_view) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">广告在线购买</td>
<td>
<label><input type="radio" name="setting[ad_buy]" value="1"  <?php if($ad_buy) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ad_buy]" value="0"  <?php if(!$ad_buy) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">购买广告使用</td>
<td>
<label><input type="radio" name="setting[ad_currency]" value="money"  <?php if($ad_currency == 'money') echo 'checked';?>/> <?php echo $DT['money_name'];?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ad_currency]" value="credit"  <?php if($ad_currency == 'credit') echo 'checked';?>/> <?php echo $DT['credit_name'];?></label>
</td>
</tr>
</table>

<a name="announce"></a>
<div class="tt">公告设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">公告功能</td>
<td>
<label><input type="radio" name="setting[announce_enable]" value="1"  <?php if($announce_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[announce_enable]" value="0"  <?php if(!$announce_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">公告绑定域名</td>
<td><input name="setting[announce_domain]"  type="text" size="30" value="<?php echo $announce_domain;?>"/><?php tips('例如 https://announce.destoon.com/<br/>请将此域名绑定至网站announce目录');?></td>
</tr>
</table>

<a name="link"></a>
<div class="tt">友情链接</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">友情链接功能</td>
<td>
<label><input type="radio" name="setting[link_enable]" value="1"  <?php if($link_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[link_enable]" value="0"  <?php if(!$link_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">友情链接绑定域名</td>
<td><input name="setting[link_domain]"  type="text" size="30" value="<?php echo $link_domain;?>"/><?php tips('例如 https://link.destoon.com/<br/>请将此域名绑定至网站link目录');?></td>
</tr>
<tr>
<td class="tl">友情链接在线申请</td>
<td>
<label><input type="radio" name="setting[link_reg]" value="1"  <?php if($link_reg) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[link_reg]" value="0"  <?php if(!$link_reg) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">链接说明</td>
<td><textarea name="setting[link_request]" id="link_request" style="width:500px;height:50px;"><?php echo $link_request;?></textarea><br/>支持HTML语法， 空格 &amp;nbsp; 换行  &lt;br/&gt;
</td> 
</tr>
</table>

<a name="comment"></a>
<div class="tt">评论设置</div>
<table cellspacing="0" class="tb">
<tr> 
<td class="tl">评论绑定域名</td>
<td><input name="setting[comment_domain]"  type="text" size="30" value="<?php echo $comment_domain;?>"/><?php tips('例如 https://comment.destoon.com/<br/>请将此域名绑定至网站comment目录');?></td>
</tr>
<tr>
<td class="tl">允许评论的模块</td>
<td><?php echo module_checkbox('setting[comment_module][]', $comment_module, '1,2,3,16,18');?></td>
</tr>
<tr>
<td class="tl">第三方评论系统</td>
<td>
<select name="setting[comment_api]" id="comment_api" onchange="if(this.value){Ds('comment_api_1');Dh('comment_api_0');}else{Dh('comment_api_1');Ds('comment_api_0');}">
<option value=""<?php if($comment_api == '') echo ' selected';?>>不使用</option>
<option value="changyan"<?php if($comment_api == 'changyan') echo ' selected';?>>云评论 - changyan.kuaizhan.com</option>
</select>
</td>
</tr>
<tbody id="comment_api_1" style="display:<?php echo $comment_api ? '' : 'none';?>">
<tr>
<td class="tl">APP ID</td>
<td><input name="setting[comment_api_id]"  type="text" size="50" value="<?php echo $comment_api_id;?>"/><?php tips('云评论:填写代码里的appid');?></td>
</tr>
<tr>
<td class="tl">APP KEY</td>
<td><input name="setting[comment_api_key]"  type="text" size="50" value="<?php echo $comment_api_key;?>"/><?php tips('云评论:填写代码里的conf，prod_开头');?></td>
</tr>
</tbody>
<tbody id="comment_api_0" style="display:<?php echo $comment_api ? 'none' : '';?>">
<tr style="display:none;">
<td class="tl">内容页显示评论列表</td>
<td>
<label><input type="radio" name="setting[comment_show]" value="1"  <?php if($comment_show == 1) echo 'checked';?>> 开启&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_show]" value="0"  <?php if($comment_show == 0) echo 'checked';?>> 关闭
</td>
</tr>
<tr>
<td class="tl">允许评论的会员组</td>
<td><?php echo group_checkbox('setting[comment_group][]', $comment_group);?></td>
</tr>
<tr>
<td class="tl">允许支持反对的会员组</td>
<td><?php echo group_checkbox('setting[comment_vote_group][]', $comment_group);?></td>
</tr>
<tr>
<td class="tl">审核评论</td>
<td>
<label><input type="radio" name="setting[comment_check]" value="2"  <?php if($comment_check == 2) echo 'checked';?>> 继承会员组设置</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_check]" value="1"  <?php if($comment_check == 1) echo 'checked';?>> 全部启用</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_check]" value="0"  <?php if($comment_check == 0) echo 'checked';?>> 全部关闭</label>
</td>
</tr>
<tr>
<td class="tl">发布评论启用验证码</td>
<td>
<label><input type="radio" name="setting[comment_captcha_add]" value="2"  <?php if($comment_captcha_add == 2) echo 'checked';?>> 继承会员组设置</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_captcha_add]" value="1"  <?php if($comment_captcha_add == 1) echo 'checked';?>> 全部启用</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_captcha_add]" value="0"  <?php if($comment_captcha_add == 0) echo 'checked';?>> 全部关闭</label>
</td>
</tr>
<tr>
<td class="tl">管理员前台删除评论</td>
<td>
<label><input type="radio" name="setting[comment_admin_del]" value="1"  <?php if($comment_admin_del == 1) echo 'checked';?>> 开启&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_admin_del]" value="0"  <?php if($comment_admin_del == 0) echo 'checked';?>> 关闭
</td>
</tr>
<tr>
<td class="tl">评论支持反对</td>
<td>
<label><input type="radio" name="setting[comment_vote]" value="1"  <?php if($comment_vote) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_vote]" value="0"  <?php if(!$comment_vote) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">显示IP属地</td>
<td>
<label><input type="radio" name="setting[comment_ip]" value="3"<?php if($comment_ip == 3) echo ' checked';?>/> 省级</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_ip]" value="2"<?php if($comment_ip == 2) echo ' checked';?>/> 市级</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_ip]" value="1"<?php if($comment_ip == 1) echo ' checked';?>/> 精确</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment_ip]" value="0"<?php if($comment_ip == 0) echo ' checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">评论内容字数限制</td>
<td>
<input type="text" size="5" name="setting[comment_min]" value="<?php echo $comment_min;?>"/> 至
<input type="text" size="5" name="setting[comment_max]" value="<?php echo $comment_max;?>"/> 字节
</td>
</tr>
<tr>
<td class="tl">两次评论时间间隔</td>
<td>
<input type="text" size="5" name="setting[comment_time]" value="<?php echo $comment_time;?>"/> 秒
</td>
</tr>
<tr>
<td class="tl">每页显示评论个数</td>
<td>
<input type="text" size="5" name="setting[comment_pagesize]" value="<?php echo $comment_pagesize;?>"/> 条
</td>
</tr>
<tr>
<td class="tl">单会员或IP每日限评</td>
<td>
<input type="text" size="5" name="setting[comment_limit]" value="<?php echo $comment_limit;?>"/> 次
</td>
</tr>
<tr>
<td class="tl">发布评论增加<?php echo $DT['credit_name'];?></td>
<td>
<input type="text" size="5" name="setting[credit_add_comment]" value="<?php echo $credit_add_comment;?>"/>
</td>
</tr>
<tr>
<td class="tl">评论删除扣除<?php echo $DT['credit_name'];?></td>
<td>
<input type="text" size="5" name="setting[credit_del_comment]" value="<?php echo $credit_del_comment;?>"/>
</td>
</tr>
<tr>
<td class="tl">匿名评论昵称</td>
<td>
<input type="text" size="20" name="setting[comment_am]" value="<?php echo $comment_am;?>"/>
</td>
</tr>
<tr>
<td class="tl">置顶精选评论个数</td>
<td>
<input type="text" size="5" name="setting[comment_top]" value="<?php echo $comment_top;?>"/> 条<?php tips('精选评论为后台设置了级别的评论，填0代表不显示');?>
</td>
</tr>
<tr>
<td class="tl">评论排序方式</td>
<td>
<input type="text" size="20" name="setting[comment_order]" value="<?php echo $comment_order;?>" id="comment_order"/>
<select onchange="if(this.value) Dd('comment_order').value=this.value;">
<option value="">请选择</option>
<option value="addtime desc"<?php if($comment_order == 'addtime desc') echo ' selected';?>>添加时间降序</option>
<option value="addtime asc"<?php if($comment_order == 'addtime asc') echo ' selected';?>>添加时间升序</option>
</select>
</td>
</tr>
<tr>
<td class="tl">评论规则</td>
<td><textarea name="setting[comment_tip]" style="width:500px;height:50px;"><?php echo $comment_tip;?></textarea><br/>支持HTML语法， 空格 &amp;nbsp; 换行  &lt;br/&gt;
</td> 
</tr>
</tbody>
</table>
</div>

<a name="guestbook"></a>
<div class="tt">留言设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">留言功能</td>
<td>
<label><input type="radio" name="setting[guestbook_enable]" value="1"  <?php if($guestbook_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_enable]" value="0"  <?php if(!$guestbook_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">留言绑定域名</td>
<td><input name="setting[guestbook_domain]"  type="text" size="30" value="<?php echo $guestbook_domain;?>"/><?php tips('例如 https://guestbook.destoon.com/<br/>请将此域名绑定至网站guestbook目录');?></td>
</tr>
<tr>
<td class="tl">留言类型</td>
<td><input name="setting[guestbook_type]"  type="text" size="60" value="<?php echo $guestbook_type;?>"/></td>
</tr>
<tr>
<td class="tl">显示IP属地</td>
<td>
<label><input type="radio" name="setting[guestbook_ip]" value="3"<?php if($guestbook_ip == 3) echo ' checked';?>/> 省级</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_ip]" value="2"<?php if($guestbook_ip == 2) echo ' checked';?>/> 市级</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_ip]" value="1"<?php if($guestbook_ip == 1) echo ' checked';?>/> 精确</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_ip]" value="0"<?php if($guestbook_ip == 0) echo ' checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">违规举报</td>
<td>
<label><input type="radio" name="setting[guestbook_report]" value="1"  <?php if($guestbook_report) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_report]" value="0"  <?php if(!$guestbook_report) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">留言传图片</td>
<td>
<label><input type="radio" name="setting[guestbook_thumb]" value="1"  <?php if($guestbook_thumb) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_thumb]" value="0"  <?php if(!$guestbook_thumb) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">留言传视频</td>
<td>
<label><input type="radio" name="setting[guestbook_video]" value="1"  <?php if($guestbook_video) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_video]" value="0"  <?php if(!$guestbook_video) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">游客留言</td>
<td>
<label><input type="radio" name="setting[guestbook_guest]" value="1"  <?php if($guestbook_guest == 1) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_guest]" value="0"  <?php if($guestbook_guest == 0) echo 'checked';?>/> 关闭</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_guest]" value="2"  <?php if($guestbook_guest == 2) echo 'checked';?>/> 限国内IP</label><?php tips('如果经常收到国外IP的垃圾留言，可以开启限国内IP，开启后国外IP留言强制登录，国内IP可以不登录');?>
</td>
</tr>
<tr>
<td class="tl">留言验证码</td>
<td>
<label><input type="radio" name="setting[guestbook_captcha]" value="1"  <?php if($guestbook_captcha) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[guestbook_captcha]" value="0"  <?php if(!$guestbook_captcha) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">留言转发邮箱</td>
<td><input name="setting[guestbook_email]"  type="text" size="30" value="<?php echo $guestbook_email;?>"/><?php tips('填写负责处理留言的管理员邮箱<br/>系统收到留言时自动转发到此邮箱');?></td>
</tr>
</table>

<a name="gift"></a>
<div class="tt">积分换礼设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">积分换礼功能</td>
<td>
<label><input type="radio" name="setting[gift_enable]" value="1"  <?php if($gift_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[gift_enable]" value="0"  <?php if(!$gift_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">两次兑换时间间隔</td>
<td><input type="text" size="5" name="setting[gift_time]" value="<?php echo $gift_time;?>"/> 秒</td>
</tr>
<tr> 
<td class="tl">积分换礼绑定域名</td>
<td><input name="setting[gift_domain]"  type="text" size="30" value="<?php echo $gift_domain;?>"/><?php tips('例如 https://gift.destoon.com/<br/>请将此域名绑定至网站gift目录');?></td>
</tr>
</table>

<a name="vote"></a>
<div class="tt">投票设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">投票功能</td>
<td>
<label><input type="radio" name="setting[vote_enable]" value="1"  <?php if($vote_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vote_enable]" value="0"  <?php if(!$vote_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">投票绑定域名</td>
<td><input name="setting[vote_domain]"  type="text" size="30" value="<?php echo $vote_domain;?>"/><?php tips('例如 https://vote.destoon.com/<br/>请将此域名绑定至网站vote目录');?></td>
</tr>
</table>

<a name="poll"></a>
<div class="tt">票选设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">票选功能</td>
<td>
<label><input type="radio" name="setting[poll_enable]" value="1"  <?php if($poll_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[poll_enable]" value="0"  <?php if(!$poll_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">票选绑定域名</td>
<td><input name="setting[poll_domain]"  type="text" size="30" value="<?php echo $poll_domain;?>"/><?php tips('例如 https://poll.destoon.com/<br/>请将此域名绑定至网站poll目录');?></td>
</tr>
</table>

<a name="form"></a>
<div class="tt">表单设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">表单功能</td>
<td>
<label><input type="radio" name="setting[form_enable]" value="1"  <?php if($form_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[form_enable]" value="0"  <?php if(!$form_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">表单绑定域名</td>
<td><input name="setting[form_domain]"  type="text" size="30" value="<?php echo $form_domain;?>"/><?php tips('例如 https://form.destoon.com/<br/>请将此域名绑定至网站form目录');?></td>
</tr>
</table>

<a name="archiver"></a>
<div class="tt">无图版设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">无图版功能</td>
<td>
<label><input type="radio" name="setting[archiver_enable]" value="1"  <?php if($archiver_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[archiver_enable]" value="0"  <?php if(!$archiver_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">无图版绑定域名</td>
<td><input name="setting[archiver_domain]"  type="text" size="30" value="<?php echo $archiver_domain;?>"/><?php tips('例如 https://archiver.destoon.com/<br/>请将此域名绑定至网站archiver目录');?></td>
</tr>
</table>


<a name="sitemap"></a>
<div class="tt">网站地图设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">网站地图功能</td>
<td>
<label><input type="radio" name="setting[sitemap_enable]" value="1"  <?php if($sitemap_enable) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[sitemap_enable]" value="0"  <?php if(!$sitemap_enable) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr> 
<td class="tl">网站地图绑定域名</td>
<td><input name="setting[sitemap_domain]"  type="text" size="30" value="<?php echo $sitemap_domain;?>"/><?php tips('例如 https://sitemap.destoon.com/<br/>请将此域名绑定至网站sitemap目录');?></td>
</tr>
</table>

<a name="feed"></a>
<div class="tt">RSS设置</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">RSS功能</td>
<td>
<label><input type="radio" name="setting[feed_enable]"  value="2" <?php if($feed_enable==2){ ?>checked <?php } ?>/> 完全开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[feed_enable]"  value="1" <?php if($feed_enable==1){ ?>checked <?php } ?>/> 部分开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[feed_enable]" value="0"  <?php if(!$feed_enable){ ?>checked <?php } ?>/> 关闭</label><?php tips('选择完全开启将允许用户自定义条件订阅<br/>选择部分开启仅支持按模型订阅');?>
</td>
</tr>
<tr> 
<td class="tl">RSS绑定域名</td>
<td><input name="setting[feed_domain]"  type="text" size="30" value="<?php echo $feed_domain;?>"/><?php tips('例如 https://feed.destoon.com/<br/>请将此域名绑定至网站feed目录');?></td>
</tr>
<tr> 
<td class="tl">RSS输出数量</td>
<td><input name="setting[feed_pagesize]"  type="text" size="10" value="<?php echo $feed_pagesize;?>"/></td>
</tr>
<tr>
<td class="tl">RSS输出推荐信息</td>
<td>
<label><input type="radio" name="setting[feed_level]" value="1"  <?php if($feed_level) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[feed_level]" value="0"  <?php if(!$feed_level) echo 'checked';?>/> 关闭</label><?php tips('开启后系统只输出推荐信息');?>
</td>
</tr>
<tr>
<td class="tl">RSS输出全文</td>
<td>
<label><input type="radio" name="setting[feed_content]" value="1"  <?php if($feed_content) echo 'checked';?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[feed_content]" value="0"  <?php if(!$feed_content) echo 'checked';?>/> 关闭</label><?php tips('开启输出全文会增加服务器压力');?>
</td>
</tr>
</table>

<a name="sitemaps"></a>
<div class="tt">Sitemaps</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">生成Sitemaps</td>
<td>
<label><input type="radio" name="setting[sitemaps]" value="1"  <?php if($sitemaps == 1) echo 'checked';?>> 开启&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[sitemaps]" value="0"  <?php if($sitemaps == 0) echo 'checked';?>> 关闭
</td>
</tr>
<tr>
<td class="tl">内容页更新频率</td>
<td>
<select name="setting[sitemaps_changefreq]">
<option value="always"<?php echo $sitemaps_changefreq == 'always' ? ' selected' : ''?>>一直</option>
<option value="hourly"<?php echo $sitemaps_changefreq == 'hourly' ? ' selected' : ''?>>时</option>
<option value="daily"<?php echo $sitemaps_changefreq == 'daily' ? ' selected' : ''?>>日</option>
<option value="weekly"<?php echo $sitemaps_changefreq == 'weekly' ? ' selected' : ''?>>周</option>
<option value="monthly"<?php echo $sitemaps_changefreq == 'monthly' ? ' selected' : ''?>>月</option>
<option value="yearly"<?php echo $sitemaps_changefreq == 'yearly' ? ' selected' : ''?>>年</option>
<option value="never"<?php echo $sitemaps_changefreq == 'never' ? ' selected' : ''?>>从不</option>
</select>
&nbsp;
<select name="setting[sitemaps_priority]">
<option value="1.0"<?php echo $sitemaps_priority == '1.0' ? ' selected' : ''?>>1.0</option>
<option value="0.9"<?php echo $sitemaps_priority == '0.9' ? ' selected' : ''?>>0.9</option>
<option value="0.8"<?php echo $sitemaps_priority == '0.8' ? ' selected' : ''?>>0.8</option>
<option value="0.7"<?php echo $sitemaps_priority == '0.7' ? ' selected' : ''?>>0.7</option>
<option value="0.6"<?php echo $sitemaps_priority == '0.6' ? ' selected' : ''?>>0.6</option>
<option value="0.5"<?php echo $sitemaps_priority == '0.5' ? ' selected' : ''?>>0.5</option>
<option value="0.4"<?php echo $sitemaps_priority == '0.4' ? ' selected' : ''?>>0.4</option>
<option value="0.3"<?php echo $sitemaps_priority == '0.3' ? ' selected' : ''?>>0.3</option>
<option value="0.2"<?php echo $sitemaps_priority == '0.2' ? ' selected' : ''?>>0.2</option>
<option value="0.1"<?php echo $sitemaps_priority == '0.1' ? ' selected' : ''?>>0.1</option>
</select>
</td>
</tr>
<tr>
<td class="tl">允许生成的模块</td>
<td><?php echo module_checkbox('setting[sitemaps_module][]', $sitemaps_module, '1,2,3');?></td>
</tr>
<tr>
<td class="tl">更新周期</td>
<td><input type="text" size="5" name="setting[sitemaps_update]" value="<?php echo $sitemaps_update;?>"/> 分钟</td>
</tr>
<tr>
<td class="tl">生成数量</td>
<td><input type="text" size="5" name="setting[sitemaps_items]" value="<?php echo $sitemaps_items;?>"/></td>
</tr>
<tr>
<td class="tl">URL地址</td>
<td>
<a href="<?php echo DT_PATH.'sitemaps.xml';?>" target="_blank"><?php echo DT_PATH.'sitemaps.xml';?></a>
<?php
	$mods = explode(',', $MOD['sitemaps_module']);
	foreach($MODULE as $m) {
		if($m['domain'] && !$m['islink'] && in_array($m['moduleid'], $mods)) {
			if($m['moduleid'] == 4 && $CFG['com_domain']) continue;
			echo '<br/><a href="'.$m['linkurl'].'sitemaps.xml" target="_blank">'.$m['linkurl'].'sitemaps.xml</a>';
		}
	}
?>
</td>
</tr>
<tr>
<td class="tl">上次更新</td>
<td><?php echo timetodate(filemtime(DT_ROOT.'/sitemaps.xml'));?>&nbsp;&nbsp; <a href="?moduleid=<?php echo $moduleid;?>&file=sitemap&action=sitemaps" class="t">立即更新</a></td>
</tr>
<tr>
<td class="tl">详细了解Sitemaps?</td>
<td><a href="<?php echo gourl('https://www.google.com/support/webmasters/bin/topic.py?topic=8476');?>" target="_blank">https://www.google.com/support/webmasters/bin/topic.py?topic=8476</a></td>
</tr>
</table>
</div>

</table>
</div>
<div class="sbt"><input type="submit" name="submit" value="保 存" class="btn-g"/></div>
</form>
<script type="text/javascript">
var tab = <?php echo $tab;?>;
var scr = '<?php echo $action;?>'
$(function(){
	if(tab) Tab(tab);
	if(scr) $('html,body').animate({scrollTop:$("[name='"+scr+"']").offset().top}, 500);
});
</script>
<?php include tpl('footer');?>