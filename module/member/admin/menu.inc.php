<?php
defined('DT_ADMIN') or exit('Access Denied');
$menu = array(
	array('会员列表', '?moduleid=2'),
	array(VIP.'列表', '?moduleid=4&action=vip'),
	array('会员分组', '?moduleid=2&file=group'),
	array('资料审核', '?moduleid=2&file=validate'),
	array('会员升级', '?moduleid=2&file=grade'),
	array('微信管理', '?moduleid=2&file=weixin'),
	array('一键登录', '?moduleid=2&file=oauth'),
	array('更新数据', '?moduleid=2&file=html'),
	array('模块设置', '?moduleid=2&file=setting'),
);
$menu_finance = array(
	array($DT['money_name'].'管理', '?moduleid=2&file=record'),
	array($DT['credit_name'].'管理', '?moduleid=2&file=credit'),
	array('短信管理', '?moduleid=2&file=sms&action=record'),
	array('支付记录', '?moduleid=2&file=charge'),
	array('提现记录', '?moduleid=2&file=cash'),
	array('信息支付', '?moduleid=2&file=pay'),
	array('信息打赏', '?moduleid=2&file=award'),
	array('优惠促销', '?moduleid=2&file=promo'),
	array('保证金管理', '?moduleid=2&file=deposit'),
	array('充值卡管理', '?moduleid=2&file=card'),
);
$menu_relate = array(
	array('粉丝关注', '?moduleid=2&file=follow'),
	array('在线交谈', '?moduleid=2&file=chat'),
	array('站内信件', '?moduleid=2&file=message'),
	array('电子邮件', '?moduleid=2&file=sendmail&action=record'),
	array('手机短信', '?moduleid=2&file=sendsms&action=record'),
	array('消息推送', '?moduleid=2&file=sendpush&action=record'),
	array('客服中心', '?moduleid=2&file=ask'),
	array('会员好友', '?moduleid=2&file=friend'),
	array('站内收藏', '?moduleid=2&file=favorite'),
	array('浏览历史', '?moduleid=2&file=history'),
	array('收货地址', '?moduleid=2&file=address'),
	array('贸易提醒', '?moduleid=2&file=alert'),
	array('邮件订阅', '?moduleid=2&file=mail'),
	array('登录日志', '?moduleid=2&file=loginlog'),
);
?>