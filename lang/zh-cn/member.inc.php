<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$L['bad_data'] = '数据发送自未被信任的域名，如有疑问，请联系管理员';
$L['info_add'] = '发布信息';
$L['error_password'] = '您的密码不正确';
$L['error_payword'] = '您的支付密码不正确';
$L['money_not_enough'] = '帐户余额不足';
$L['credit_not_enough'] = '您的'.$DT['credit_name'].'不足，请购买';
$L['pay_in_site'] = '站内支付';
$L['in_site'] = '站内';
$L['month'] = '月';
$L['forever'] = '永久';
$L['buy'] = '购买';
$L['guest'] = '游客';
$L['status'] = '状态';
$L['feature_close'] = '此功能暂未开启';
$L['limit_add'] = '最多可添加{V0}条记录,当前已添加{V1}条记录';
$L['default_type'] = '默认';
$L['all_type'] = '所有分类';
$L['choose_type'] = '请选择分类';
$L['check_sign'] = '数据校验失败';
$L['goto'] = '转到';
$L['job_name'] = '招聘';
$L['resume_name'] = '简历';
$L['module_name'] = '模块';
$L['individual_sign'] = '(个人)';
$L['process'] = array('', '<span style="color:#666666;">未开始</span>',	'<span style="color:#00B050;">进行中</span>', '<span style="color:#C00000;">已过期</span>');//9.0
$L['fee_agent'] = '分销返款';//9.0
$L['info_manage'] = '信息管理';//9.0
$L['info_spread'] = '信息推广';//9.0

$L['search_by'] = '按条件';
$L['search_by_title'] = '标题';
$L['search_by_note'] = '备注';
$L['order_by'] = '排序方式';

$L['op_add_success'] = '添加成功';
$L['op_checking'] = '请等待审核';
$L['op_del_success'] = '删除成功';
$L['op_edit_success'] = '修改成功';
$L['op_edit_check'] = '修改成功 请等待审核';
$L['op_set_success'] = '设置成功';
$L['op_update_success'] = '更新成功';
$L['op_trade_success'] = '交易成功';
$L['op_success'] = '操作成功';

$L['pass_title'] = '请填写标题';
$L['pass_content'] = '请填写内容';
$L['pass_typeid'] = '请选择分类';
$L['pass_url'] = '请填写网址';


$L['account_title'] = '账户详情';
$L['account_upgrade_title'] = '升级记录';
$L['account_renew_title'] = '续费记录';
$L['account_vip_renew'] = '续费';
$L['account_username_title'] = '修改会员名';
$L['account_passport_title'] = '修改昵称';
$L['account_login_title'] = '登录记录';
$L['account_close_title'] = '注销账号';
$L['account_close_msg'] = '提交成功，请等待审核';
$L['account_upgrade_status'] = array('', '<span style="color:gray;">已拒绝</span>', '<span style="color:blue;">待处理</span>', '<span style="color:green;">已通过</span>');
$L['account_month'] = array(1 => '1个月', 3 => '3个月', 6 => '6个月', 12 => '1年', 24 => '2年', 36 => '3年', 48 => '4年', 60 => '5年');
$L['account_scan_title'] = '扫码访问';//9.0
$L['account_setting_title'] = '账户设置';//9.0

$L['address_title'] = '收货地址';
$L['address_title_add'] = '添加地址';
$L['address_title_edit'] = '修改地址';
$L['address_msg_choose'] = '请选择地址';
$L['address_type'] = array('常用', '默认', '家', '单位', '学校', '退货');

//9.0
$L['agent_title'] = '代理管理';
$L['agent_title_add'] = '添加代理';
$L['agent_title_edit'] = '修改代理';
$L['agent_sfields'] = array($L['search_by'], '会员名', '公司/姓名', '手机', '折扣', '理由', $L['search_by_note']);
$L['agent_sorder'] = array($L['order_by'], '加入时间降序', '加入时间升序', '享受折扣降序', '享受折扣升序', '订单数量降序', '订单数量升序', '分销订单降序', '分销订单升序', '总销售额降序', '总销售额升序', '年销售额降序', '年销售额升序', '月销售额降序', '月销售额升序');
$L['agent_pass_username'] = '会员不存在';
$L['agent_pass_exists'] = '代理已存在';
$L['agent_pass_mobile'] = '请填写手机';
$L['agent_msg_choose'] = '请选择代理';

$L['alert_pass'] = '您至少选择"关键字"或"所在行业"其中的一项';
$L['alert_title'] = '贸易提醒';
$L['alert_add_title'] = '添加提醒';
$L['alert_edit_title'] = '修改提醒';//9.0

$L['ask_status'] = array('待受理', '<span style="color:blue;">受理中</span>', '<span style="color:green;">已解决</span>', '<span style="color:red;">未解决</span>');
$L['ask_title'] = '客服中心';
$L['ask_title_show'] = '问题查看';
$L['ask_title_edit'] = '修改问题';
$L['ask_title_add'] = '提交问题';
$L['ask_star_type'] = array('未评分', '<span style="color:red;">很不满</span>', '不满', '一般', '满意', '<span style="color:green;">很满意</span>');//9.0
$L['ask_star_success'] = '评分成功';
$L['ask_add_success'] = '提交成功';
$L['ask_msg_reply'] = '此问题不可再提问';//9.0

$L['avatar_title'] = '管理头像';
$L['avatar_delete'] = '删除成功';
$L['avatar_img_t'] = '图片格式错误';
$L['avatar_img_w'] = '图片宽度小于128px';
$L['avatar_img_h'] = '图片高度小于128px';
$L['avatar_img_e'] = '上传失败，请重试';

$L['award_title'] = '我要打赏';
$L['award_record_view'] = '信息打赏';
$L['award_record_back'] = '打赏返利';
$L['award_msg_self'] = '不能打赏自己发布的信息';
$L['award_msg_fee'] = '打赏金额错误';
$L['award_msg_success'] = '打赏成功，正在返回...';

$L['biz_title'] = '商户后台';
$L['biz_day'] = '日';
$L['biz_month'] = '月';
$L['biz_title_month'] = '{V0}年{V1}月交易报表(单位:{V2})';
$L['biz_title_year'] = '{V0}年交易报表(单位:{V1})';

$L['cash_status'] = array('<span style="color:blue;">等待受理</span>', '<span style="color:#666666;">拒绝申请</span>', '<span style="color:red;">支付失败</span>', '<span style="color:green;">付款成功</span>');
$L['cash_title_record'] = '提现记录';
$L['cash_title_setting'] = '帐号设置';
$L['cash_title_confirm'] = '提现确认';
$L['cash_title'] = '申请提现';
$L['cash_pass_bank'] = '请选择收款方式';
$L['cash_pass_branch'] = '请填写开户网点';
$L['cash_pass_account'] = '请填写收款帐号';
$L['cash_pass_amount'] = '请填写提现金额';
$L['cash_pass_amount_min'] = '单次提现最小金额为:';
$L['cash_pass_amount_max'] = '单次提现最大金额为:';
$L['cash_pass_amount_day'] = '24小时内最多可提现{V0}次，请稍候再操作';
$L['cash_pass_amount_large'] = '提现金额大于可用余额';
$L['cash_msg_success'] = '您的提现申请已经提交，请等待工作人员的处理<br/>在此期间，该笔'.$DT['money_name'].'将被冻结';
$L['cash_msg_account'] = '请先设置收款帐号';

$L['charge'] = '支付';
$L['charge_id'] = '流水号';
$L['charge_online'] = '在线支付';
$L['charge_card'] = '充值卡支付';
$L['charge_reward'] = '支付奖励';
$L['charge_card_name'] = '充值卡';
$L['charge_card_number'] = '卡号';
$L['charge_status'] = array('<span style="color:blue;">等待支付</span>', '<span style="color:red;">支付失败</span>', '<span style="color:red;">支付失败</span>', '<span style="color:green;">支付成功</span>', '<span style="color:green;">支付成功</span>');
$L['charge_title_record'] = '支付记录';
$L['charge_title_confirm'] = '支付确认';
$L['charge_title_card'] = '充值卡支付';
$L['charge_title_pay'] = '在线支付';
$L['charge_title'] = '完成支付';
$L['charge_pass_card_number'] = '请填写正确的充值卡卡号';
$L['charge_pass_card_password'] = '请填写正确的充值卡密码';
$L['charge_pass_card_used'] = '充值卡无效';
$L['charge_pass_card_expired'] = '充值卡已过有效期';
$L['charge_pass_card_error_password'] = '充值卡密码错误';
$L['charge_pass_card_error_number'] = '无效的充值卡卡号';
$L['charge_pass_type_amount'] = '请填写支付金额';
$L['charge_pass_choose_amount'] = '请选择支付金额';
$L['charge_pass_amount_min'] = '支付金额最少:';
$L['charge_pass_bank'] = '请选择支付平台';
$L['charge_pass_bank_close'] = '此支付平台尚未启用';
$L['charge_pass_stock'] = '库存不足';
$L['charge_msg_card_success'] = '充值卡充值成功';
$L['charge_msg_order_fail'] = '订单状态为失败，ID:';
$L['charge_msg_order_cancel'] = '订单状态为作废，ID:';
$L['charge_msg_not_order'] = '未找到支付纪录';
$L['charge_reason'] = '会员支付';
$L['charge_reason_deposit'] = '保证金';
$L['charge_reason_credit'] = '积分购买';
$L['charge_reason_sms'] = '短信购买';
$L['charge_reason_vip'] = VIP.'续费';
$L['charge_reason_grade'] = '会员升级';
$L['charge_reason_muti'] = '批量付款';
$L['charge_reason_style'] = '模板购买';
$L['charge_reason_spread'] = '排名购买';
$L['charge_reason_ad'] = '广告购买';
$L['charge_reason_pay'] = '信息支付';
$L['charge_reason_award'] = '信息打赏';
//9.0
$L['charge_title_payed'] = '支付结果';
$L['charge_pass_qrcode'] = '收款码不存在';
$L['charge_scan_msg'] = '会员{V0}正在{V1}支付{V2}元，请注意审核';
$L['charge_bank_bill'] = '请上传汇款凭证';
$L['charge_bank_ext'] = '汇款凭证文件格式错误';

$L['chat_title'] = '站内交谈';
$L['chat_group'] = '我的群聊';
$L['chat_add'] = '发起交谈';
$L['chat_friend'] = '我的好友';
$L['chat_setting'] = '交谈设置';
$L['chat_online'] = '[在线]';
$L['chat_offline'] = '[离线]';
$L['chat_empty'] = '暂无对话';
$L['chat_record'] = '与【{V0}】聊天记录';
$L['chat_with'] = '与【{V0}】交谈中';
$L['chat_msg_black'] = '未指定屏蔽对象';
$L['chat_msg_black_success'] = '屏蔽成功';
$L['chat_msg_self'] = '不能与自己对话';
$L['chat_msg_no_rights'] = '您所在的会员组没有权限发起对话';
$L['chat_msg_user'] = '会员不存在';
$L['chat_msg_refuse'] = '对方拒绝与您对话';
$L['chat_msg_friend'] = '好友不存在';//9.0

$L['child_title'] = '子账号';
$L['child_title_add'] = '添加子账号';
$L['child_title_edit'] = '修改子账号';
$L['child_permission'] = '请选择权限';
$L['child_title_password'] = '修改密码';//9.0
$L['child_title_home'] = '会员中心';//9.0

$L['credit_exchange_title'] = $DT['credit_name'].'兑换';
$L['credit_buy_title'] = $DT['credit_name'].'购买';
$L['credit_title'] = $DT['credit_name'].'记录';
$L['credit_pass_ex_min'] = '兑换额度不足';
$L['credit_pass_ex_max'] = '最多可兑换:';
$L['credit_msg_amount'] = '兑换成功';
$L['credit_msg_active'] = '您的帐号未在论坛激活';
$L['credit_msg_less'] = '积分不足，无法进行此操作';
$L['credit_msg_buy_amount'] = '请选择购买额度';
$L['credit_msg_buy_success'] = '购买成功';
$L['credit_fields'] = array($L['search_by'], '金额', '事由', $L['search_by_note']);
$L['credit_grade_title'] = $DT['credit_name'].'级别';//9.0
$L['credit_less_title'] = $DT['credit_name'].'不足';//9.0

//9.0
$L['comment_title'] = '评论管理';
$L['comment_title_my'] = '我的评论';
$L['comment_sfields'] = array($L['search_by'], '评论', '回复', '原文', '会员', '昵称');
$L['comment_sfields_my'] = array($L['search_by'], '评论', '回复', '原文', '作者');
$L['comment_msg_choose'] = '请选择评论';

$L['coupon_title'] = '我的优惠券';
$L['coupon_promo_title'] = '领券中心';
$L['coupon_msg_got'] = '该优惠券已经领取过';
$L['coupon_msg_exists'] = '优惠活动不存在';
$L['coupon_msg_self'] = '不能领取自己的店铺优惠券';
$L['coupon_msg_none'] = '该优惠券已抢光，看看其他优惠券吧';
$L['coupon_msg_time'] = '该优惠还没有开始，看看其他优惠券吧';
$L['coupon_msg_timeout'] = '该优惠已经结束，看看其他优惠券吧';
$L['coupon_msg_success'] = '优惠券领取成功';
$L['coupon_msg_choose'] = '未指定优惠券';

$L['deposit_title'] = '保证金记录';
$L['deposit_title_add'] = '保证金增资';

$L['edit_title'] = '修改资料';
$L['edit_invite'] = '会员推广';
$L['edit_profile'] = '完善资料';
$L['edit_msg_success'] = '资料保存成功';
$L['edit_msg_password'] = '，您修改了登录密码，请重新登录...';

$L['express_title'] = '运费模板';
$L['express_msg_choose'] = '请选择运费模板';

$L['favorite_title_add'] = '添加收藏';
$L['favorite_title_edit'] = '修改收藏';
$L['favorite_title'] = '我的收藏';
$L['favorite_msg_choose'] = '请选择收藏';
$L['favorite_sfields'] = array($L['search_by'], $L['search_by_title'], '网址', $L['search_by_note']);
//9.0
$L['follow_title'] = '我的关注';
$L['follow_title_fans'] = '我的粉丝';
$L['follow_edit_follow'] = '修改关注';
$L['follow_edit_fans'] = '修改粉丝';
$L['follow_black_follow'] = '拉黑关注';
$L['follow_black_fans'] = '拉黑粉丝';
$L['follow_sfields'] = array($L['search_by'], '会员名', '昵称', $L['search_by_note']);
$L['follow_msg_choose'] = '请选择会员';

$L['friend_title_add'] = '添加好友';
$L['friend_title_edit'] = '修改好友';
$L['friend_title_apply'] = '好友申请';
$L['friend_title_refuse'] = '拒绝申请';
$L['friend_title_show'] = '好友详情';
$L['friend_title_list'] = '黑名单';
$L['friend_title'] = '我的好友';
$L['friend_sfields'] = array($L['search_by'], '姓名', '别名', '公司', '职位', '电话', '手机', '主页', '邮箱', 'QQ', '微信', '阿里旺旺', 'Skype', '会员', '昵称', $L['search_by_note']);
//9.0
$L['friend_title_black'] = '拉黑好友';
$L['friend_title_find'] = '查找好友';
$L['friend_pass_username'] = '会员名不能为空';
$L['friend_pass_self'] = '不能添加自己为好友';
$L['friend_pass_reject'] = '对方拒绝添加好友';
$L['friend_pass_again'] = '对方已经是好友了';
$L['friend_pass_auth'] = '请求已失效';
$L['friend_pass_member'] = '会员不存在';
$L['friend_pass_black_self'] = '不能拉黑自己';
$L['friend_msg_black_fail'] = '拉黑失败';
$L['friend_msg_success'] = '好友添加成功';
$L['friend_msg_check'] = '好友申请已经提交，请等待对方处理';
$L['friend_msg_reject'] = '拒绝成功';
$L['friend_msg_choose'] = '请选择好友';
$L['friend_msg_t0'] = '{V0}添加您为好友提醒';
$L['friend_msg_c0'] = '添加了您为好友';
$L['friend_msg_t1'] = '{V0}同意了您的好友请求';
$L['friend_msg_c1'] = '同意了您的好友请求';
$L['friend_msg_t2'] = '{V0}拒绝了您的好友请求';
$L['friend_msg_c2'] = '拒绝了您的好友请求';
$L['friend_msg_t3'] = '{V0}添加您为好友提醒';
$L['friend_msg_c3'] = '添加了您为好友';
$L['friend_msg_t4'] = '{V0}申请添加您为好友';
$L['friend_msg_c4'] = '申请添加您为好友';
$L['friend_msg_member'] = '会员';
$L['friend_msg_at'] = '于';
$L['friend_msg_or'] = '或者';
$L['friend_msg_do'] = '您可以将对方';
$L['friend_msg_can'] = '您可以';
$L['friend_msg_r0'] = '未填写';
$L['friend_msg_r1'] = '拒绝理由：';
$L['friend_msg_r2'] = '申请理由：';
$L['friend_msg_d0'] = '忽略此请求';
$L['friend_msg_d1'] = '加为好友';
$L['friend_msg_d2'] = '加入黑名单';
$L['friend_msg_d3'] = '同意申请';
$L['friend_msg_d4'] = '拒绝申请';

$L['grade_title'] = '会员级别';
$L['grade_fail'] = '您的会员组升级({V0})失败';
$L['grade_success'] = '您的会员组升级({V0})成功';
$L['grade_return'] = '升级失败返款';
$L['grade_upto'] = '升级为:';
$L['grade_auto'] = '自主升级';
$L['grade_pass_balance'] = '会员余额不足';
$L['grade_pass_company'] = '请填写公司名';
$L['grade_pass_company_exisits'] = '公司名称已存在';
$L['grade_pass_truename'] = '请填写联系人';
$L['grade_pass_telephone'] = '请填写电话号码';
$L['grade_msg_bad_promo'] = '无效的优惠码';
$L['grade_msg_time_promo'] = '可获有效期:{V0}天';
$L['grade_msg_money_promo'] = '可充抵金额:{V0}'.$DT['money_unit'];
$L['grade_msg_check'] = '您的申请已经成功提交，请等待工作人员处理';
$L['grade_msg_checking'] = '您有升级申请正在等待处理中';
$L['grade_msg_success'] = '会员升级成功';

$L['history_title'] = '浏览历史';
$L['history_title_visit'] = '我的访客';

$L['home_title'] = '商铺设置';
$L['home_msg_reset'] = '恢复成功';
$L['home_msg_save'] = '保存成功';
$L['home_msg_check'] = '保存成功，请等待审核';//9.0

$L['honor_title_add'] = '添加资质';
$L['honor_title_edit'] = '修改资质';
$L['honor_title'] = '荣誉资质';
$L['honor_pass_title'] = '请填写证书名称';
$L['honor_pass_authority'] = '请填写发证机构';
$L['honor_pass_thumb'] = '请上传证书图片';
$L['honor_pass_fromdate'] = '请选择证书发证时间';
$L['honor_pass_fromdate_error'] = '证书发证时间必须在当前时间之前';
$L['honor_pass_todate'] = '请选择证书到期时间';
$L['honor_pass_todate_error'] = '证书到期时间必须在当前时间之后';
$L['honor_reward_reason'] = '证书上传';
$L['honor_punish_reason'] = '证书删除';
$L['honor_msg_choose'] = '请选择证书';

$L['index_msg_logout'] = '注销成功';
$L['index_msg_note_limit'] = '便笺限1000字';

$L['invite_title'] = '邀请注册';
$L['invite_title_record'] = '注册记录';//9.0
$L['invite_sfields'] = array($L['search_by'], '会员名', '昵称', '公司名');//9.0

$L['link_title'] = '友情链接';
$L['link_title_add'] = '添加链接';
$L['link_title_edit'] = '修改链接';
$L['link_pass_username'] = '会员名不能为空';
$L['link_pass_title'] = '请填写网站名称';
$L['link_pass_linkurl'] = '请填写网站地址';
$L['link_reward_reason'] = '友链发布';
$L['link_punish_reason'] = '友链删除';
$L['link_msg_choose'] = '请选择链接';

$L['login_title'] = '会员登录';
$L['login_title_reg'] = '注册成功，请登录';
$L['login_title_sms'] = '短信登录';
$L['login_title_scan'] = '扫码登录';
$L['login_title_weixin'] = '微信扫码';
$L['login_title_child'] = '子账号登录';
$L['login_msg_username'] = '请输入登录名称';
$L['login_msg_password'] = '请输入密码';
$L['login_msg_not_member'] = '登录名称不存在';
$L['login_msg_success'] = '登录成功';
$L['login_msg_bad_mobile'] = '手机号不存在或未通过验证';
$L['login_msg_bad_code'] = '短信验证失败';
$L['login_msg_close'] = '管理员关闭了用户登录';//9.0

$L['logout_msg_success'] = '退出成功';

$L['mail_title'] = '我的订阅';
$L['mail_title_list'] = '邮件列表';
$L['mail_msg_not_add'] = '您尚未订阅任何商机';
$L['mail_msg_cancel'] = '退订成功';
$L['mail_msg_update'] = '订阅更新成功';
$L['mail_msg_choose'] = '请选择商机分类，如果要取消订阅，请直接点击退订按钮';
$L['mail_msg_not_item'] = '邮件列表不存在';

$L['member_username_match'] = '会员名格式错误';
$L['member_username_len'] = '会员登录名长度应在{V0}-{V1}之间';
$L['member_username_ban'] = '此登录名已经被禁止注册';
$L['member_username_reg'] = '会员登录名已经被注册';
$L['member_passport_len'] = '昵称长度应在{V0}-{V1}之间';
$L['member_passport_char'] = '昵称不能含有特殊符号';
$L['member_passport_ban'] = '此昵称已经被禁止注册';
$L['member_passport_reg'] = '昵称已经被注册';
$L['member_password_null'] = '会员登录密码不能为空';
$L['member_password_match'] = '两次输入的密码不一致';
$L['member_password_len'] = '登录密码长度应在{V0}-{V1}之间';
$L['member_password_1'] = '密码必须包含数字';
$L['member_password_2'] = '密码必须包含小写字母';
$L['member_password_3'] = '密码必须包含大写字母';
$L['member_password_4'] = '密码必须包含标点符号';
$L['member_payword_null'] = '支付密码不能为空';
$L['member_payword_match'] = '两次输入的密码不一致';
$L['member_payword_len'] = '支付密码长度应在{V0}-{V1}之间';
$L['member_groupid_null'] = '请选择会员组';
$L['member_truename_null'] = '请填写真实姓名';
$L['member_email_null'] = '邮件格式不正确';
$L['member_email_ban'] = '此邮件域名已经被禁止注册';
$L['member_email_reg'] = '邮件地址已经被注册';
$L['member_mobile_null'] = '手机号码格式不正确';
$L['member_mobile_reg'] = '手机号码已经被注册';
$L['member_areaid_null'] = '请选择所在地区';
$L['member_company_null'] = '请填写公司名称';
$L['member_company_bad'] = '无效的公司名称';
$L['member_company_reg'] = '公司名称已经存在';
$L['member_company_ban'] = '此公司名已经被禁止注册';
$L['member_shop_reg'] = '商铺名称已经存在';
$L['member_type_null'] = '请选择公司类型';
$L['member_telephone_null'] = '请填写公司电话';
$L['member_regyear_null'] = '请填写公司注册年份';
$L['member_address_null'] = '请填写公司地址';
$L['member_introduce_null'] = '公司介绍不能少于5字';
$L['member_business_null'] = '请填写公司主要经营范围';
$L['member_catid_null'] = '请选择公司主营行业';
$L['member_login_username_bad'] = '用户名格式错误';
$L['member_login_password_bad'] = '密码错误,请重试';
$L['member_login_not_member'] = '会员不存在';
$L['member_login_ban'] = '累计{V0}次错误尝试 您在{V1}小时内不能登录系统';
$L['member_login_member_ban'] = '该帐号已被禁止访问';
$L['member_login_ok'] = '成功';
$L['member_founder_del'] = '创始人不可删除';
$L['member_founder_move'] = '创始人不可移动';
$L['member_rename_not_member'] = '当前会员名不存在';
$L['member_record_reg'] = '注册奖励';
$L['member_record_login'] = '登录奖励';
$L['member_qq_null'] = '请填写QQ号码';//V9.0
$L['member_wx_null'] = '请填写微信号码';//V9.0
$L['member_reason_reg'] = '请填写注册原因';//V9.0
$L['member_agent_reason'] = '邀请注册';//V9.0

$L['message_title'] = '站内信件';
$L['message_title_black'] = '黑名单';
$L['message_title_inbox'] = '收件箱';
$L['message_title_outbox'] = '已发送';
$L['message_title_draft'] = '草稿箱';
$L['message_title_recycle'] = '回收站';
$L['message_limit'] = '今日可发送{V0}次 当前已发送{V1}次';
$L['message_send_max'] = '最多同时给{V0}个人发送信件';
$L['message_list_date'] = 'Y年m月d日 H:i';
$L['message_names'] = array(1=>'草稿箱', 2=>'已发送', 3=>'收件箱', 4=>'回收站');
$L['message_feedback_title'] = '您的来信 [{V0}] 已经阅读';
$L['message_feedback_content'] = '{V0} 于 <small style="color:blue;">{V1}</small> 阅读了您发送的信件<br/><div style="padding:10px;margin:10px 10px 0 0;border-left:#E5EBFA 3px solid;line-height:180%;background:#FFFFFF;"><strong>标题:</strong>{V2}<br/><strong>时间:</strong>{V3}<br/><strong>原文:</strong><br/>{V4}</div>';
$L['message_msg_edit'] = '信件不存在或无权修改';
$L['message_msg_null'] = '指定范围暂无信件';
$L['message_msg_save_draft'] = '草稿保存成功';
$L['message_msg_edit_draft'] = '草稿修改成功';
$L['message_msg_send'] = '信件发送成功';
$L['message_msg_choose'] = '请选择信件';
$L['message_msg_deny'] = '信件不存在或无权限';
$L['message_msg_clear'] = '成功清空';
$L['message_msg_mark'] = '已标记为已读';
$L['message_msg_restore'] = '成功还原';
$L['message_msg_empty'] = '清理成功';
$L['message_msg_inbox_limit'] = '收件箱已满，请清理信件';
$L['message_pass_groupid'] = '请选择会员组';
$L['message_pass_touser'] = '收件人不能为空';
$L['message_pass_title'] = '标题或内容不能为空';
$L['message_msg_black'] = '对方拒收';//9.0
$L['message_black'] = '拒收信件';//9.0
$L['message_from_system'] = '系统消息';//9.0
$L['message_from_notice'] = '系统广播';//9.0
$L['message_sfields'] = array($L['search_by'], $L['search_by_title'], '内容', '发件人', '收件人');//V9.0

$L['news_title'] = '公司新闻';
$L['news_title_add'] = '添加新闻';
$L['news_title_edit'] = '修改新闻';
$L['news_record_add'] = '新闻发布';
$L['news_record_del'] = '新闻删除';
$L['news_msg_choose'] = '请选择新闻';

$L['oauth_title'] = '一键登录';
$L['oauth_login_title'] = '登录记录';
$L['oauth_quit'] = '解除成功';
$L['oauth_bind'] = '帐号绑定';

$L['page_title'] = '公司单页';
$L['page_title_add'] = '添加单页';
$L['page_title_edit'] = '修改单页';
$L['page_record_add'] = '单页发布';
$L['page_record_del'] = '单页删除';
$L['page_msg_choose'] = '请选择单页';

//9.0
$L['partner_title'] = '代理分销';
$L['partner_title_add'] = '申请代理';
$L['partner_title_list'] = '商品列表';
$L['partner_sfields'] = array($L['search_by'], '会员名', '公司', '折扣', '理由', $L['search_by_note']);
$L['partner_sorder'] = array($L['order_by'], '加入时间降序', '加入时间升序', '享受折扣降序', '享受折扣升序', '订单数量降序', '订单数量升序', '分销订单降序', '分销订单升序', '总销售额降序', '总销售额升序', '年销售额降序', '年销售额升序', '月销售额降序', '月销售额升序');
$L['partner_goods_sorder'] = array($L['order_by'], '发布时间降序', '发布时间升序', '更新时间降序', '更新时间升序', '商品价格降序', '商品价格升序', '订单数量降序', '订单数量升序', '库存数量降序', '库存数量升序', '评论数量降序', '评论数量升序', '商品人气降序', '商品人气升序');
$L['partner_pass_username'] = '会员不存在';
$L['partner_pass_exists'] = '申请已存在';
$L['partner_pass_mobile'] = '请填写手机';
$L['partner_pass_reason'] = '请填写申请理由';
$L['partner_msg_choose'] = '请选择代理';

$L['pay_title'] = '站内支付';
$L['pay_record_view'] = '信息查看';
$L['pay_record_back'] = '信息返利';
$L['pay_msg_self'] = '不能支付自己发布的信息';
$L['pay_msg_fee'] = '支付金额错误';
$L['pay_msg_success'] = '支付成功，正在返回...';

$L['promo_title'] = '优惠促销';
$L['promo_coupon_title'] = '领券记录';
$L['promo_title_add'] = '添加促销';
$L['promo_title_edit'] = '修改促销';
$L['promo_msg_title'] = '请填写优惠名称';
$L['promo_msg_price'] = '请填写优惠金额';
$L['promo_msg_cost'] = '最低消费必须大于优惠金额';
$L['promo_msg_amount'] = '请填写数量限制';
$L['promo_msg_date'] = '有效时间设置错误';

$L['record_title'] = $DT['money_name'].'流水';
$L['record_title_pay'] = '信息付费';
$L['record_title_award'] = '打赏记录';
$L['record_sfields'] = array($L['search_by'], '金额', '银行', '事由', $L['search_by_note']);

$L['register_title'] = '会员注册';
$L['register_msg_error'] = '错误请求';
$L['register_msg_close'] = '管理员关闭了用户注册';
$L['register_msg_agent'] = '您的客户端信息已经被网站屏蔽<br/>如有疑问，请与我们联系';
$L['register_msg_ip'] = '同一IP{V0}小时内只能注册一次';
$L['register_msg_passport'] = '昵称已经存在\n\n如果此会员是您注册的，请填写正确的密码\n\n如果不是您注册的，请更换昵称再试';
$L['register_msg_activate'] = $DT['sitename'].'用户注册激活信';
$L['register_msg_welcome'] = '欢迎加入'.$DT['sitename'];
$L['register_pass_groupid'] = '请选择会员组';
$L['register_msg_emailcode'] = $DT['sitename'].'用户邮件验证码';
$L['register_pass_emailcode'] = '邮件验证码错误';
$L['register_pass_mobilecode'] = '手机验证码错误';
$L['register_proxy'] = '请不要使用代理访问本站注册';

$L['send_mail_close'] = '系统未开启邮件发送';
$L['send_sms_close'] = '系统未开启短信发送';
$L['send_check_success'] = '您的帐号激活成功';
$L['send_check_email_bad'] = '请填写正确的邮件地址';
$L['send_check_email_repeat'] = '您填写的邮件地址已经被使用，请更换';
$L['send_check_username_bad'] = '您的会员名输入错误';
$L['send_check_password_bad'] = '您的会员名和密码不匹配';
$L['send_check_deny'] = '您的帐号无需发送验证信';
$L['send_check_mail'] = $DT['sitename'].'用户注册激活信';
$L['send_check_username_null'] = '您输入会员名不存在';
$L['send_check_title'] = '重发验证信';
$L['send_payword_success'] = '支付密码修改成功';
$L['send_payword_mail'] = $DT['sitename'].'用户修改支付密码';
$L['send_payword_title'] = '支付密码';
$L['send_email_empty'] = '个人资料未填写电子邮件';
$L['send_email_exist'] = '邮件地址已经被注册，请更换';
$L['send_email_success'] = '邮件修改成功';
$L['send_email_mail'] = $DT['sitename'].'用户修改邮件';
$L['send_email_title'] = '修改邮件';
$L['send_mobile_empty'] = '个人资料未填写手机号码';
$L['send_mobile_exist'] = '手机号码已经被注册，请更换';
$L['send_mobile_fail'] = '短信发送失败，请重试';
$L['send_mobile_success'] = '手机修改成功';
$L['send_mobile_code_error'] = '验证码错误';
$L['send_mobile_bad'] = '手机号码格式不正确';
$L['send_mobile_record'] = '修改手机';
$L['send_mobile_title'] = '修改手机';
$L['send_password_success'] = '登录密码重设成功';
$L['send_password_checking'] = '您的帐号尚未通过审核';
$L['send_password_error'] = '提供的信息不匹配';
$L['send_password_mail'] = $DT['sitename'].'用户找回密码';
$L['send_password_title'] = '找回密码';

$L['sendmail_title'] = '发送电子邮件';
$L['sendmail_content'] = '您的好友 <strong><a href="{V0}" target="_blank">{V1}</a></strong> 向您推荐如下信息:<br/><br/>{V2}<br/><a href="{V3}" target="_blank">{V3}</a><br/><br/>附言：';
$L['sendmail_title_new'] = '推荐《{V0}》';
$L['sendmail_pass_mailto'] = '请填写正确的收件人地址';
$L['sendmail_success'] = '邮件已发送至{V0}';
$L['sendmail_fail'] = '邮件发送失败，请重试';

$L['sms_code'] = '验证码';
$L['sms_msg_max'] = '今日已达发送上限，请明日再试';
$L['sms_msg_validate'] = '请先认证您的手机号码';
$L['sms_msg_buy'] = '请先购买短信';
$L['sms_msg_mobile'] = '请填写正确的手机号码';
$L['sms_msg_content'] = '请填写短信内容';
$L['sms_add_record'] = '手动';
$L['sms_add_success'] = '成功发送{V0}条短信';
$L['sms_add_title'] = '发送短信';
$L['sms_msg_no_price'] = '系统未设置单价，无法购买';
$L['sms_msg_buy_num'] = '请填写购买数量';
$L['sms_buy_note'] = '购买短信';
$L['sms_buy_record'] = '在线购买';
$L['sms_buy_success'] = '购买成功';
$L['sms_buy_title'] = '短信购买';
$L['sms_record_title'] = '接收记录';
$L['sms_send_title'] = '发送记录';
$L['sms_title'] = '短信记录';
$L['sms_sfields'] = array($L['search_by'], '金额', '事由', $L['search_by_note']);

$L['stats_title'] = '流量统计';
$L['stats_title_record'] = '浏览记录';
$L['stats_title_report'] = '统计报表';
$L['stats_weeks'] = array('天', '一', '二', '三', '四', '五', '六');
$L['stats_week'] = '星期';
$L['stats_msg_update'] = '正在更新数据';
$L['stats_record_sfields'] = array($L['search_by'], '网址', '来源', '来源域名', '搜索引擎', '会员', '所属商家', 'IP');
$L['stats_sorder'] = array($L['order_by'], '总IP降序', '总IP升序', '电脑IP降序', '电脑IP升序', '手机IP降序', '手机IP升序', '总PV降序', '总PV升序', '电脑PV降序', '电脑PV升序', '手机PV降序', '手机PV升序', '爬虫PV降序', '爬虫PV升序', '电脑爬虫PV降序', '电脑爬虫PV升序', '手机爬虫PV降序', '手机爬虫PV升序', '日期降序', '日期升序');

$L['stock_title'] = '商品库存';
$L['stock_title_open'] = '公用数据';
$L['stock_title_in'] = '商品入库';
$L['stock_title_out'] = '商品出库';
$L['stock_title_add'] = '添加商品';
$L['stock_title_edit'] = '修改商品';
$L['stock_title_update'] = '库存管理';
$L['stock_title_record'] = '库存记录';
$L['stock_msg_skuid'] = '请填写条形编码';
$L['stock_msg_barcode'] = '条形编码不存在对应商品';
$L['stock_msg_amount'] = '请填写数量';
$L['stock_record_sfields'] = array($L['search_by'], '商品名称', '条形编码', '操作事由', '备注信息', '操作人');
$L['stock_record_sorder'] = array($L['order_by'], '数量降序', '数量升序', '库存降序', '库存升序', '时间降序', '时间升序');
$L['stock_open_sfields'] = array($L['search_by'], '商品名称', '条形编码', '仓储货位', '商品品牌', '计量单位', '属性名1', '属性名2', '属性名3', '属性值1', '属性值2', '属性值3', '备注', '操作人');
$L['stock_open_sorder'] = array($L['order_by'], '复制次数降序', '复制次数升序', '商品售价降序', '商品售价升序', '商品进价降序', '商品进价升序', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序');
$L['stock_sfields'] = array($L['search_by'], '商品名称', '条形编码', '仓储货位', '商品品牌', '计量单位', '属性名1', '属性名2', '属性名3', '属性值1', '属性值2', '属性值3', '备注', '操作人');
$L['stock_sorder'] = array($L['order_by'], '库存数量降序', '库存数量升序', '商品售价降序', '商品售价升序', '商品进价降序', '商品进价升序', '添加时间降序', '添加时间升序', '更新时间降序', '更新时间升序');

$L['style_title'] = '模板设置';
$L['style_title_buy'] = '模板购买';
$L['style_title_order'] = '我的模板';
$L['style_sfields'] = array($L['search_by'], '名称', '作者');
$L['style_sorder'] = array($L['order_by'], '价格降序', '价格升序', '人气降序', '人气升序');
$L['style_record_buy'] = '{V0}模板购买{V1}月';
$L['style_msg_not_exist'] = '模板不存在';
$L['style_msg_group'] = '抱歉！此模板未对您所在的会员组开放';
$L['style_msg_month'] = '请选择购买时长';
$L['style_msg_expired'] = '模板已过期，请续费';
$L['style_msg_buy_success'] = '模板购买成功';
$L['style_msg_use_success'] = '模板启用成功';
$L['style_pass_title'] = '请填写模板名称';
$L['style_pass_skin'] = '请填写风格目录';
$L['style_pass_skin_match'] = '只能使用字母(A-Z,a-z)、数字(0-9)、中划线(-)、下划线(_)作为风格目录名称';
$L['style_pass_css'] = 'CSS文件不存在';
$L['style_pass_template'] = '请填写模板目录';
$L['style_pass_template_match'] = '只能使用字母(A-Z,a-z)、数字(0-9)、中划线(-)、下划线(_)作为模板目录名称';
$L['style_pass_dir'] = '模板目录不存在';
$L['style_pass_mdir'] = '手机版目录不存在';
$L['style_pass_groupid'] = '请选择会员组';

$L['support_title'] = '客服专员';
$L['support_error_1'] = '系统暂未为您分配客服专员';
$L['support_error_2'] = '客服专员不存在，请与网站联系';

$L['type_title'] = '{V0}分类管理';
$L['type_names'] = array('friend'=>'好友', 'favorite'=>'收藏', 'follow'=>'关注', 'fans'=>'粉丝', 'sell'=>'供应', 'mall'=>'商品', 'news'=>'新闻', 'stock'=>'库存', 'agent'=>'代理');//9.0
$L['type_msg_limit'] = '最多可添加{V0}个分类';
$L['type_parent'] = '上级分类';


$L['validate_title'] = '身份认证';
$L['validate_email_exist'] = '邮件地址已经被使用，请更换';
$L['validate_email_success'] = '您的邮件认证成功';
$L['validate_email_bad'] = '邮箱格式不正确';
$L['validate_email_mail'] = $DT['sitename'].'用户邮件认证';
$L['validate_email_title'] = '邮件认证';
$L['validate_mobile_exist'] = '手机号码已经被占用，请更换';
$L['validate_mobile_title'] = '手机认证';
$L['validate_mobile_success'] = '您的手机认证成功';
$L['validate_mobile_fail'] = '短信发送失败，请重试';
$L['validate_mobile_code_error'] = '验证码错误';
$L['validate_mobile_bad'] = '手机号码格式不正确';
$L['validate_mobile_record'] = '手机认证';
$L['validate_truename_title'] = '实名认证';
$L['validate_truename_name'] = '请填写真实姓名';
$L['validate_truename_image'] = '请上传证件图片';
$L['validate_truename_success'] = '提交成功';
$L['validate_company_title'] = '公司认证';
$L['validate_company_name'] = '请填写公司名';
$L['validate_company_image'] = '请上传证件图片';
$L['validate_company_success'] = '提交成功';
$L['validate_shop_title'] = '商铺认证';
$L['validate_shop_name'] = '请填写商铺名称';
$L['validate_shop_image'] = '请上传证件图片';
$L['validate_shop_success'] = '提交成功';
$L['validate_bank_title'] = '银行认证';
$L['validate_company_taxid'] = '社会信用代码格式错误';//9.0
$L['validate_truename_idtype'] = '请选择证件类型';//9.0
$L['validate_truename_idno'] = '请填写正确的证件号码';//9.0
$L['validate_bank_name'] = '请选择开户银行';//9.0
$L['validate_bank_branch'] = '请填写银行全称';//9.0
$L['validate_bank_account'] = '请填写银行帐号';//9.0
$L['validate_upload'] = '请上传';//9.0

$L['vip_title'] = VIP.'信息';
$L['vip_renew'] = VIP.'续费';
$L['vip_msg_fee'] = '支付金额错误';
$L['vip_msg_success'] = '续费成功';

$L['weixin_title'] = '微信关注';
$L['weixin_push_open'] = '开启成功';
$L['weixin_push_close'] = '关闭成功';

$L['send_too_many'] = '尝试次数过多，请稍后再试';
$L['send_too_quick'] = '发送频率过快，请稍后再试';
$L['send_bad_email'] = '邮件地址不存在';
$L['send_bad_mobile'] = '认证手机号码不存在';
?>