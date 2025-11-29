<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
$menus = array (
    array('基本信息'),
    array('会员权限'),
    array('信息发布'),
    array('主页设置'),
);
show_menu($menus);
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="groupid" value="<?php echo $groupid;?>"/>
<input type="hidden" name="tab" id="tab" value="<?php echo $tab;?>"/>
<div id="Tabs0" style="display:">
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 会员组名称</td>
<td><input type="text" size="20" name="groupname" id="groupname" value="<?php echo $groupname;?>"/> <span id="dgroupname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 会员类型</td>
<td>
<label><input type="radio" name="setting[type]" value="1" <?php if($type) echo 'checked';?>/> 企业会员</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[type]" value="0" <?php if(!$type) echo 'checked';?>/> 个人会员</label>
</td>
</tr>
<tr id="mode" style="display:;">
<td class="tl"><span class="f_red">*</span> 会员模式</td>
<td>
<label><input type="radio" name="setting[fee_mode]" value="1" <?php if($fee_mode) echo 'checked';?> onclick="Ds('mode_1');Dh('mode_0');"/> 收费会员</label>&nbsp;&nbsp;
<label><input type="radio" name="setting[fee_mode]" value="0" <?php if(!$fee_mode) echo 'checked';?> onclick="Ds('mode_0');Dh('mode_1');"/> 免费会员</label>
</td>
</tr>
<tbody id="mode_1" style="display:<?php echo $fee_mode ? '' : 'none';?>">
<tr>
<td class="tl"><span class="f_red">*</span> 收费设置</td>
<td><input type="text" size="20" name="setting[fee]" id="fee" value="<?php echo $fee;?>"/> <?php echo $DT['money_unit'];?>/年 <span class="f_gray">免费会员请填0</span> <span id="dfee" class="f_red"></span></td>
</tr>
<?php
	foreach($L['account_month'] as $k=>$v) {
		$kk = 'fee_'.$k;
?>
<tr>
<td class="tl"></td>
<td><input type="text" size="20" name="setting[<?php echo $kk;?>]" value="<?php echo $$kk;?>"/> <?php echo $DT['money_unit'];?>/<?php echo $v;?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> <?php echo VIP;?>指数</td>
<td><input type="text" size="2" name="vip" id="vip" value="<?php echo $vip;?>"/> <span class="f_gray">免费会员请填0，收费会员请填1-9数字</span> <span id="dvip" class="f_red"></span></td>
</tr>
</tbody>
<tr id="mode_0" style="display:<?php echo $fee_mode ? 'none' : '';?>">
<td class="tl"><span class="f_red">*</span> 享受折扣</td>
<td><input type="text" size="2" name="setting[discount]" id="discount" value="<?php echo $discount;?>"/> % <span class="f_gray">折扣仅限系统收费，不针对会员产品</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 交易佣金</td>
<td><input type="text" size="2" name="setting[commission]" id="commission" value="<?php echo $commission;?>"/> % <span class="f_gray">会员通过商城、供应、团购完成交易后，系统扣除交易额一定比例作为网站服务费用</span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 显示顺序</td>
<td><input type="text" size="2" name="listorder" id="listorder" value="<?php echo $listorder;?>"/>  <span class="f_gray">数字越小越靠前</span></td>
</tr>
</table>
</div>
<div id="Tabs1" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">允许在会员注册页面显示</td>
<td>
<label><input type="radio" name="setting[reg]" value="1" <?php if($reg) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[reg]" value="0" <?php if(!$reg) echo 'checked';?>/> 否</label> <span class="f_gray">此设置对<?php echo VIP;?>会员无效</span>
</td>
</tr>
<tr>
<td class="tl">会员注册页默认选中</td>
<td>
<label><input type="radio" name="setting[regid]" value="1" <?php if($regid) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[regid]" value="0" <?php if(!$regid) echo 'checked';?>/> 否</label> <?php tips('此设置对'.VIP.'会员或不在注册页面显示的会员组无效，当有多个组设置默认，系统默认选中最后一个，当没有一个组设置默认，系统默认选中第一个组');?>
</td>
</tr>
<tr>
<td class="tl">允许在会员升级页面显示</td>
<td>
<label><input type="radio" name="setting[grade]" value="1" <?php if($grade) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[grade]" value="0" <?php if(!$grade) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">会员升级是否需要审核</td>
<td>
<label><input type="radio" name="setting[upgrade]" value="1" <?php if($upgrade) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[upgrade]" value="0" <?php if(!$upgrade) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许进入商户后台</td>
<td>
<label><input type="radio" name="setting[biz]" value="1" <?php if($biz) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[biz]" value="0" <?php if(!$biz) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许申请提现</td>
<td>
<label><input type="radio" name="setting[cash]" value="1" <?php if($cash) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[cash]" value="0" <?php if(!$cash) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许使用客服中心</td>
<td>
<label><input type="radio" name="setting[ask]" value="1" <?php if($ask) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ask]" value="0" <?php if(!$ask) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许使用商机订阅</td>
<td>
<label><input type="radio" name="setting[mail]" value="1" <?php if($mail) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[mail]" value="0" <?php if(!$mail) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许使用手机短信</td>
<td>
<label><input type="radio" name="setting[sms]" value="1" <?php if($sms) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[sms]" value="0" <?php if(!$sms) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许发送电子邮件</td>
<td>
<label><input type="radio" name="setting[sendmail]" value="1" <?php if($sendmail) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[sendmail]" value="0" <?php if(!$sendmail) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许管理评论</td>
<td>
<label><input type="radio" name="setting[comment]" value="1" <?php if($comment) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[comment]" value="0" <?php if(!$comment) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许商家线下收款</td>
<td>
<label><input type="radio" name="setting[bill]" value="1" <?php if($bill) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[bill]" value="0" <?php if(!$bill) echo 'checked';?>/> 否</label> <span class="f_gray">商家必须完成银行认证且自行开启</span>
</td>
</tr>

<tr>
<td class="tl">允许管理订单</td>
<td>
<label><input type="radio" name="setting[trade_order]" value="1" <?php if($trade_order) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[trade_order]" value="0" <?php if(!$trade_order) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许管理团购订单</td>
<td>
<label><input type="radio" name="setting[group_order]" value="1" <?php if($group_order) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[group_order]" value="0" <?php if(!$group_order) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许发展代理</td>
<td>
<label><input type="radio" name="setting[agent]" value="1" <?php if($agent) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[agent]" value="0" <?php if(!$agent) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许代理分销</td>
<td>
<label><input type="radio" name="setting[partner]" value="1" <?php if($partner) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[partner]" value="0" <?php if(!$partner) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许竞价排名</td>
<td>
<label><input type="radio" name="setting[spread]" value="1" <?php if($spread) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[spread]" value="0" <?php if(!$spread) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">允许广告预定</td>
<td>
<label><input type="radio" name="setting[ad]" value="1" <?php if($ad) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[ad]" value="0" <?php if(!$ad) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许发起聊天请求</td>
<td>
<label><input type="radio" name="setting[chat]" value="1" <?php if($chat) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[chat]" value="0" <?php if(!$chat) echo 'checked';?>/> 否</label>
</td>
</tr>



<tr>
<td class="tl">邮件认证</td>
<td>
<label><input type="radio" name="setting[vemail]" value="2" <?php if($vemail == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vemail]" value="1" <?php if($vemail == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vemail]" value="0" <?php if($vemail == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">手机认证</td>
<td>
<label><input type="radio" name="setting[vmobile]" value="2" <?php if($vmobile == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vmobile]" value="1" <?php if($vmobile == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vmobile]" value="0" <?php if($vmobile == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">姓名认证</td>
<td>
<label><input type="radio" name="setting[vtruename]" value="2" <?php if($vtruename == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vtruename]" value="1" <?php if($vtruename == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vtruename]" value="0" <?php if($vtruename == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">公司认证</td>
<td>
<label><input type="radio" name="setting[vcompany]" value="2" <?php if($vcompany == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vcompany]" value="1" <?php if($vcompany == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vcompany]" value="0" <?php if($vcompany == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">银行认证</td>
<td>
<label><input type="radio" name="setting[vbank]" value="2" <?php if($vbank == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vbank]" value="1" <?php if($vbank == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vbank]" value="0" <?php if($vbank == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">商铺认证</td>
<td>
<label><input type="radio" name="setting[vshop]" value="2" <?php if($vshop == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vshop]" value="1" <?php if($vshop == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vshop]" value="0" <?php if($vshop == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">缴纳保证金</td>
<td>
<label><input type="radio" name="setting[vdeposit]" value="2" <?php if($vdeposit == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vdeposit]" value="1" <?php if($vdeposit == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vdeposit]" value="0" <?php if($vdeposit == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">关注微信公众号</td>
<td>
<label><input type="radio" name="setting[vweixin]" value="2" <?php if($vweixin == 2){ ?>checked <?php } ?>/> 强制</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vweixin]" value="1" <?php if($vweixin == 1){ ?>checked <?php } ?>/> 开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[vweixin]" value="0" <?php if($vweixin == 0){ ?>checked <?php } ?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl">设置说明</td>
<td class="ts">数量限制填 <b>0</b> 则表示不限&nbsp;&nbsp;&nbsp;填 <b>-1</b> 表示禁止使用</td>
</tr>
<tr>
<td class="tl">收件箱数量限制</td>
<td>
<input type="text" name="setting[inbox_limit]" size="5" value="<?php echo $inbox_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">好友数量限制</td>
<td>
<input type="text" name="setting[friend_limit]" size="5" value="<?php echo $friend_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">收藏数量限制</td>
<td>
<input type="text" name="setting[favorite_limit]" size="5" value="<?php echo $favorite_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">贸易提醒数量限制</td>
<td>
<input type="text" name="setting[alert_limit]" size="5" value="<?php echo $alert_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">收货地址数量限制</td>
<td>
<input type="text" name="setting[address_limit]" size="5" value="<?php echo $address_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">运费模板数量限制</td>
<td>
<input type="text" name="setting[express_limit]" size="5" value="<?php echo $express_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">库存商品数量限制</td>
<td>
<input type="text" name="setting[stock_limit]" size="5" value="<?php echo $stock_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">优惠活动数量限制</td>
<td>
<input type="text" name="setting[promo_limit]" size="5" value="<?php echo $promo_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">每日可发站内信限制</td>
<td>
<input type="text" name="setting[message_limit]" size="5" value="<?php echo $message_limit;?>"/> <?php echo tips('询盘和报价为特殊的站内信，发送一次询盘或者报价会消耗一次站内信发送机会');?>
</td>
</tr>
<tr>
<td class="tl">每日询盘次数限制</td>
<td>
<input type="text" name="setting[inquiry_limit]" size="5" value="<?php echo $inquiry_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">每日报价次数限制</td>
<td>
<input type="text" name="setting[price_limit]" size="5" value="<?php echo $price_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">自定义分类限制</td>
<td>
<input type="text" name="setting[type_limit]" size="5" value="<?php echo $type_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">子帐号数量限制</td>
<td>
<input type="text" name="setting[child_limit]" size="5" value="<?php echo $child_limit;?>"/>
</td>
</tr>
</table>
</div>
<div id="Tabs2" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">发布信息需要审核</td>
<td>
<label><input type="radio" name="setting[check]" value="1" <?php if($check) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[check]" value="0" <?php if(!$check) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">发布信息启用验证码</td>
<td>
<label><input type="radio" name="setting[captcha]" value="1" <?php if($captcha) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[captcha]" value="0" <?php if(!$captcha) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">发布信息启用验证问题</td>
<td>
<label><input type="radio" name="setting[question]" value="1" <?php if($question) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[question]" value="0" <?php if(!$question) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许发布信息的模块</td>
<td>
<ul class="mods">
<?php
	$moduleids = explode(',', $moduleids);
	foreach($MODULE as $m) {
		if($m['moduleid'] > 4 && is_file(DT_ROOT.'/module/'.$m['module'].'/my.inc.php')) {
			echo '<li><label><input type="checkbox" name="setting[moduleids][]" value="'.$m['moduleid'].'" '.(in_array($m['moduleid'], $moduleids) ? 'checked' : '').'/> '.$m['name'].'</label></li>';
		}
	}
?>
</ul>
</td>
</tr>
<tr>
<td class="tl">允许删除信息</td>
<td>
<label><input type="radio" name="setting[delete]" value="1" <?php if($delete) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[delete]" value="0" <?php if(!$delete) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许复制信息</td>
<td>
<label><input type="radio" name="setting[copy]" value="1" <?php if($copy) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[copy]" value="0" <?php if(!$copy) echo 'checked';?>/> 否</label> 

复制信息可显著提高发布信息效率
</td>
</tr>
<tr>
<td class="tl">发布信息时间间隔</td>
<td>
<input type="text" name="setting[add_limit]" size="5" value="<?php echo $add_limit;?>"/>
&nbsp;&nbsp;单位： 秒&nbsp;&nbsp;填 0 表示不限制&nbsp;&nbsp;填正数表示发布两次发布时间间隔
</td>
</tr>
<tr>
<td class="tl">1小时发布信息数量</td>
<td>
<input type="text" name="setting[hour_limit]" size="5" value="<?php echo $hour_limit;?>"/>
&nbsp;&nbsp;填 0 表示不限制&nbsp;&nbsp;填正数表示1小时内在单模块发布信息数量限制（防灌水）
</td>
</tr>
<tr>
<td class="tl">24小时发布信息数量</td>
<td>
<input type="text" name="setting[day_limit]" size="5" value="<?php echo $day_limit;?>"/>
&nbsp;&nbsp;填 0 表示不限制&nbsp;&nbsp;填正数表示24小时内在单模块发布信息数量限制（防灌水）
</td>
</tr>
<tr>
<td class="tl">刷新信息时间间隔</td>
<td>
<input type="text" name="setting[refresh_limit]" size="5" value="<?php echo $refresh_limit;?>"/>
&nbsp;&nbsp;单位： 秒&nbsp;&nbsp;填 -1 表示不允许刷新&nbsp;&nbsp;填 0 表示不限制时间间隔&nbsp;&nbsp;填正数表示限制两次刷新时间
</td>
</tr>
<tr>
<td class="tl">允许修改信息时间</td>
<td>
<input type="text" name="setting[edit_limit]" size="5" value="<?php echo $edit_limit;?>"/>
&nbsp;&nbsp;单位： 天&nbsp;&nbsp;填 -1 表示不允许修改&nbsp;&nbsp;填 0 表示不限制时间修改&nbsp;&nbsp;填正数表示发布时间超出后不可修改
</td>
</tr>
<tr>
<td class="tl">编辑器工具按钮</td>
<td>
<select name="setting[editor]">
<option value="Default"<?php if($editor == 'Default') echo ' selected';?>>全部</option>
<option value="Destoon"<?php if($editor == 'Destoon') echo ' selected';?>>精简</option>
<option value="Simple"<?php if($editor == 'Simple') echo ' selected';?>>简洁</option>
<option value="Basic"<?php if($editor == 'Basic') echo ' selected';?>>基础</option>
</select>&nbsp;
</td>
</tr>
<tr>
<td class="tl">允许上传文件</td>
<td>
<label><input type="radio" name="setting[upload]" value="1" <?php if($upload) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[upload]" value="0" <?php if(!$upload) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许上传的文件类型</td>
<td><input name="setting[uploadtype]" type="text" value="<?php echo $uploadtype;?>" size="60"/> <?php tips('用|号隔开文件后缀，留空表示继承网站设置');?></td>
</tr>
<tr>
<td class="tl">允许上传大小限制</td>
<td><input name="setting[uploadsize]" type="text" value="<?php echo $uploadsize;?>" size="10"/> Kb (1024Kb=1M) 不填或填0表示继承网站设置</td>
</tr>
<tr>
<td class="tl">单条信息上传数量限制</td>
<td><input name="setting[uploadlimit]" type="text" value="<?php echo $uploadlimit;?>" size="5"/> <?php tips('一条信息内最多上传文件数量限制，0为不限制');?></td>
</tr>
<tr>
<td class="tl">24小时上传数量限制</td>
<td><input name="setting[uploadday]" type="text" value="<?php echo $uploadday;?>" size="5"/> <?php tips('24小时内最大文件上传数量限制，0为不限制<br/>此项会增加服务器压力，且在开启上传记录的情况下有效');?></td>
</tr>
<tr>
<td class="tl">上传一张图片扣积分</td>
<td><input name="setting[uploadcredit]" type="text" value="<?php echo $uploadcredit;?>" size="5"/> <?php tips('积分不足时将无法上传，0为不限制');?></td>
</tr>
</table>
</div>
<div id="Tabs3" style="display:none">
<table cellspacing="0" class="tb">
<tr>
<td class="tl">允许个人空间</td>
<td>
<label><input type="radio" name="setting[space]" value="1" <?php if($space) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[space]" value="0" <?php if(!$space) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">个人空间模块</td>
<td>
<ul class="mods">
<?php
	$spaceids = explode(',', $spaceids);
	foreach($MODULE as $m) {
		if($m['moduleid'] > 4 && in_array($m['moduleid'], $moduleids)) {
			echo '<li><label><input type="checkbox" name="setting[spaceids][]" value="'.$m['moduleid'].'" '.(in_array($m['moduleid'], $spaceids) ? 'checked' : '').'/> '.$m['name'].'</label></li>';
		}
	}
?>
</ul>
</td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">仅能选择允许发布信息的模块</td>
</tr>
<tr>
<td class="tl">空间默认模板</td>
<td><?php echo tpl_select('space', 'company', 'setting[template_space]', '默认模板', $template_space);?></td>
</tr>
<tr>
<td class="tl">拥有公司主页</td>
<td>
<label><input type="radio" name="setting[homepage]" value="1" <?php if($homepage) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[homepage]" value="0" <?php if(!$homepage) echo 'checked';?>/> 否</label>
<?php tips('如果关闭公司主页，企业会员会显示企业介绍页，个人会员会显示个人空间');?>
</td>
</tr>
<tr>
<td class="tl">公司默认模板</td>
<td><?php echo tpl_select('show', 'company', 'setting[template_show]', '默认模板', $template_show);?> <?php tips('关闭公司时，企业会员会展示的企业介绍页模板');?></td>
</tr>
<tr>
<td class="tl">默认主页模板</td>
<td>
<?php echo homepage_select('setting[styleid]', '请选择', $groupid, $styleid, 'id="styleid"');?> &nbsp;
<img src="<?php echo DT_STATIC;?>image/ico-sch.png" width="16" height="16" title="预览" class="c_p" onclick="if(Dd('styleid').value>0){window.open('?moduleid=2&file=style&action=show&itemid='+Dd('styleid').value);}else{Dtoast('请选择模板');}"/>
</td>
</tr>
<tr>
<td class="tl">允许自定义主页设置</td>
<td>
<label><input type="radio" name="setting[home]" value="1" <?php if($home) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[home]" value="0" <?php if(!$home) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">默认横幅[宽X高]</td>
<td>
<input type="text" size="3" name="setting[bannerw]" value="<?php echo $bannerw;?>"/>
X
<input type="text" size="3" name="setting[bannerh]" value="<?php echo $bannerh;?>"/> px
</td>
</tr>
<tr>
<td class="tl">主页设置需要审核</td>
<td>
<label><input type="radio" name="setting[home_check]" value="1" <?php if($home_check) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[home_check]" value="0" <?php if(!$home_check) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许自定义菜单</td>
<td>
<label><input type="radio" name="setting[home_menu]" value="1" <?php if($home_menu) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[home_menu]" value="0" <?php if(!$home_menu) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">菜单设置</td>
<td>
	<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
	<tr bgcolor="#F5F5F5" align="center">
	<td>可用功能</td>
	<td>排序</td>
	<td>默认名称</td>
	<td>默认分页</td>
	<td>状态</td>
	</tr>
	<?php 
		foreach($G_MENU as $k=>$v) {
			if(!isset($D_MENU[$k])) continue;
	?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $D_MENU[$k];?></td>
	<td><input type="text" name="home[menu][<?php echo $k;?>][listorder]" size="5" value="<?php echo $v['listorder'];?>"/></td>
	<td><input type="text" name="home[menu][<?php echo $k;?>][name]" size="20" value="<?php echo $v['name'];?>"/></td>
	<td><input type="text" name="home[menu][<?php echo $k;?>][pagesize]" size="5" value="<?php echo $v['pagesize'];?>"/></td>
	<td>
	<label><input type="radio" name="home[menu][<?php echo $k;?>][status]" value="0" <?php if($v['status'] == 0) echo 'checked';?>/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[menu][<?php echo $k;?>][status]" value="1" <?php if($v['status'] == 1) echo 'checked';?>/> 可用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[menu][<?php echo $k;?>][status]" value="2" <?php if($v['status'] == 2) echo 'checked';?>/> 默认</label>	
	</td>
	</tr>
	<?php } ?>
	<?php 
		foreach($D_MENU as $k=>$v) {
			if(isset($G_MENU[$k])) continue;
	?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $D_MENU[$k];?></td>
	<td><input type="text" name="home[menu][<?php echo $k;?>][listorder]" size="5" value="0"/></td>
	<td><input type="text" name="home[menu][<?php echo $k;?>][name]" size="20" value="<?php echo $D_MENU[$k];?>"/></td>
	<td><input type="text" name="home[menu][<?php echo $k;?>][pagesize]" size="5" value="20"/></td>
	<td>
	<label><input type="radio" name="home[menu][<?php echo $k;?>][status]" value="0"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[menu][<?php echo $k;?>][status]" value="1"/> 可用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[menu][<?php echo $k;?>][status]" value="2" checked/> 默认</label>	
	</td>
	</tr>
	<?php } ?>
	</table>
</td>
</tr>

<tr>
<td class="tl">允许自定义侧栏</td>
<td>
<label><input type="radio" name="setting[home_side]" value="1" <?php if($home_side) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[home_side]" value="0" <?php if(!$home_side) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">侧栏设置</td>
<td>
	<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
	<tr bgcolor="#F5F5F5" align="center">
	<td>可用功能</td>
	<td>排序</td>
	<td>默认名称</td>
	<td>默认数量</td>
	<td>状态</td>
	</tr>
	<?php 
		foreach($G_SIDE as $k=>$v) {
			if(!isset($D_SIDE[$k])) continue;
	?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $D_SIDE[$k];?></td>
	<td><input type="text" name="home[side][<?php echo $k;?>][listorder]" size="5" value="<?php echo $v['listorder'];?>"/></td>
	<td><input type="text" name="home[side][<?php echo $k;?>][name]" size="20" value="<?php echo $v['name'];?>"/></td>
	<td><input type="text" name="home[side][<?php echo $k;?>][pagesize]" size="5" value="<?php echo $v['pagesize'];?>"/></td>
	<td>
	<label><input type="radio" name="home[side][<?php echo $k;?>][status]" value="0" <?php if($v['status'] == 0) echo 'checked';?>/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[side][<?php echo $k;?>][status]" value="1" <?php if($v['status'] == 1) echo 'checked';?>/> 可用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[side][<?php echo $k;?>][status]" value="2" <?php if($v['status'] == 2) echo 'checked';?>/> 默认</label>	
	</td>
	</tr>
	<?php } ?>
	<?php 
		foreach($D_SIDE as $k=>$v) {
			if(isset($G_SIDE[$k])) continue;
	?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $D_SIDE[$k];?></td>
	<td><input type="text" name="home[side][<?php echo $k;?>][listorder]" size="5" value="0"/></td>
	<td><input type="text" name="home[side][<?php echo $k;?>][name]" size="20" value="<?php echo $D_SIDE[$k];?>"/></td>
	<td><input type="text" name="home[side][<?php echo $k;?>][pagesize]" size="5" value="10"/></td>
	<td>
	<label><input type="radio" name="home[side][<?php echo $k;?>][status]" value="0"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[side][<?php echo $k;?>][status]" value="1"/> 可用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[side][<?php echo $k;?>][status]" value="2" checked/> 默认</label>	
	</td>
	</tr>
	<?php } ?>
	</table>
</td>
</tr>

<tr>
<td class="tl">允许自定义首页</td>
<td>
<label><input type="radio" name="setting[home_main]" value="1" <?php if($home_main) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[home_main]" value="0" <?php if(!$home_main) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl">首页设置</td>
<td>
	<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
	<tr bgcolor="#F5F5F5" align="center">
	<td>可用功能</td>
	<td>排序</td>
	<td>默认名称</td>
	<td>默认数量</td>
	<td>状态</td>
	</tr>
	<?php 
		foreach($G_MAIN as $k=>$v) {
			if(!isset($D_MAIN[$k])) continue;
	?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $D_MAIN[$k];?></td>
	<td><input type="text" name="home[main][<?php echo $k;?>][listorder]" size="5" value="<?php echo $v['listorder'];?>"/></td>
	<td><input type="text" name="home[main][<?php echo $k;?>][name]" size="20" value="<?php echo $v['name'];?>"/></td>
	<td><input type="text" name="home[main][<?php echo $k;?>][pagesize]" size="5" value="<?php echo $v['pagesize'];?>"/></td>
	<td>
	<label><input type="radio" name="home[main][<?php echo $k;?>][status]" value="0" <?php if($v['status'] == 0) echo 'checked';?>/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[main][<?php echo $k;?>][status]" value="1" <?php if($v['status'] == 1) echo 'checked';?>/> 可用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[main][<?php echo $k;?>][status]" value="2" <?php if($v['status'] == 2) echo 'checked';?>/> 默认</label>	
	</td>
	</tr>
	<?php } ?>
	<?php 
		foreach($D_MAIN as $k=>$v) {
			if(isset($G_MAIN[$k])) continue;
	?>
	<tr bgcolor="#FFFFFF" align="center">
	<td><?php echo $D_MAIN[$k];?></td>
	<td><input type="text" name="home[main][<?php echo $k;?>][listorder]" size="5" value="0"/></td>
	<td><input type="text" name="home[main][<?php echo $k;?>][name]" size="20" value="<?php echo $D_MAIN[$k];?>"/></td>
	<td><input type="text" name="home[main][<?php echo $k;?>][pagesize]" size="5" value="8"/></td>
	<td>
	<label><input type="radio" name="home[main][<?php echo $k;?>][status]" value="0"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[main][<?php echo $k;?>][status]" value="1"/> 可用</label>&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="radio" name="home[main][<?php echo $k;?>][status]" value="2" checked/> 默认</label>	
	</td>
	</tr>
	<?php } ?>
	</table>
</td>
</tr>

<tr>
<td class="tl">允许选择模板</td>
<td>
<label><input type="radio" name="setting[style]" value="1" <?php if($style) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[style]" value="0" <?php if(!$style) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许查看店铺统计</td>
<td>
<label><input type="radio" name="setting[stats_view]" value="1" <?php if($stats_view) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[stats_view]" value="0" <?php if(!$stats_view) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许使用第三方地图</td>
<td>
<label><input type="radio" name="setting[map]" value="1" <?php if($map) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[map]" value="0" <?php if(!$map) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许使用第三方统计</td>
<td>
<label><input type="radio" name="setting[stats]" value="1" <?php if($stats) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[stats]" value="0" <?php if(!$stats) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">允许使用第三方客服</td>
<td>
<label><input type="radio" name="setting[kf]" value="1" <?php if($kf) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="setting[kf]" value="0" <?php if(!$kf) echo 'checked';?>/> 否</label>
</td>
</tr>
<tr>
<td class="tl">公司新闻数量限制</td>
<td>
<input type="text" name="setting[news_limit]" size="5" value="<?php echo $news_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">公司单页数量限制</td>
<td>
<input type="text" name="setting[page_limit]" size="5" value="<?php echo $page_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">荣誉资质数量限制</td>
<td>
<input type="text" name="setting[honor_limit]" size="5" value="<?php echo $honor_limit;?>"/>
</td>
</tr>
<tr>
<td class="tl">友情链接数量限制</td>
<td>
<input type="text" name="setting[link_limit]" size="5" value="<?php echo $link_limit;?>"/>
</td>
</tr>
</table>
</div>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '保 存' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="window.parent.location.reload();"/></div>
</form>
<script type="text/javascript">
var tab = <?php echo $tab;?>;
var all = <?php echo $all;?>;
$(function(){
	if(tab) Tab(tab);
	if(all) {all = 0; TabAll();}
	if(window.screen.width < 1280) {
		$('.menu div').hide();
	}
});
function check() {
	var l;
	var f;
	f = 'groupname';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写会员组名称', f);
		return false;
	}
	return true;
}
<?php if($groupid == 5 || $groupid == 6) { ?>
Dh('mode');
<?php } ?>
</script>
<?php include tpl('footer');?>