<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
$L['send_status'] = array(
	'<span style="color:#888888;">未知</span>',
	'<span style="color:#008080;">在途</span>',
	'<span style="color:#0000FF;">派送中</span>',
	'<span style="color:#008000;">已签收</span>',
	'<span style="color:#FF0000;">寄回</span>',
	'<span style="color:#FF6600;">其他</span>',
	'<span style="color:#FF0000;">无记录</span>',
);
$L['group_status'] = array(
	'<span style="color:#0000FF;">已付款</span>',
	'<span style="color:#FF0000;">已发货</span>',
	'<span style="color:#FF6600;">已消费</span>',
	'<span style="color:#008000;">交易成功</span>',
	'<span style="color:#FF0000;">申请退款</span>',
	'<span style="color:#999999;text-decoration:line-through;">已退款</span>',
	'<span style="color:#FF6600;">待付款</span>',
);
$L['contract_status'] = array(
	'<span style="color:#FF6600;">未生成</span>',
	'<span style="color:#0000FF;">待签署</span>',
	'<span style="color:#008000;">已签署</span>',
);

$L['group_order_credit'] = '团购订单';
$L['group_msg_deny'] = '您无权进行此操作';
$L['group_msg_null'] = '订单不存在';
$L['group_success'] = '恭喜！此订单交易成功';
$L['group_detail_title'] = '订单详情';
$L['group_express_title'] = '物流追踪';
$L['group_title'] = '订单管理';
$L['group_sfields'] = array('按条件', '商品', '金额', '密码', '买家', '买家姓名', '买家地址', '买家邮编', '买家手机', '买家电话', '发货方式', '物流号码', '备注');
$L['group_order_title'] = '团购订单';
$L['group_order_sfields'] = array('按条件', '商品', '金额', '密码', '卖家', '发货方式', '物流号码', '备注');
$L['group_order_id'] = '团购单号';
$L['group_send_title'] = '商家发货';
$L['group_addtime_null'] = '请填写延长的时间';
$L['group_addtime_success'] = '买家确认时间延长成功';
$L['group_addtime_title'] = '延长买家确认时间';
$L['group_record_pay'] = '交易成功';
$L['group_order_id'] = '团购单号:';
$L['group_buyer_timeout'] = '团购单号:{V0}[买家超时]';
$L['group_pay_order_success'] = '订单支付成功';
$L['group_pay_order_title'] = '订单支付';
//9.0
$L['group_edit_title'] = '修改地址';
$L['group_edit_aid'] = '请选择收货地址';
$L['group_edit_addr'] = '收货地址不存在';
$L['group_edit_success'] = '地址修改成功';

$L['trade_status'] = array(
	'<span style="color:#008080;">待确认</span>',
	'<span style="color:#FF6600;">待付款</span>',
	'<span style="color:#0000FF;">待发货</span>',
	'<span style="color:#FF0000;">已发货</span>',
	'<span style="color:#008000;">交易成功</span>',
	'<span style="color:#FF0000;">申请退款</span>',
	'<span style="color:#999999;text-decoration:line-through;">已退款</span>',
	'<span style="color:#008080;">货到付款</span>',
	'<span style="color:#999999;text-decoration:line-through;">买家关闭</span>',
	'<span style="color:#999999;text-decoration:line-through;">卖家关闭</span>',
);

$L['trade_msg_deny'] = '您无权进行此操作';
$L['trade_msg_null'] = '订单不存在';
$L['trade_msg_pay_bind'] = '系统采用了{V0}担保交易，请先绑定您的{V0}帐号';
$L['trade_msg_less_fee'] = '附加金额不能小于{V0}';
$L['trade_msg_confirm'] = '此订单需要卖家确认';
$L['trade_msg_deny_comment'] = '此订单不支持评价';
$L['trade_msg_comment_again'] = '您已经评价过此交易';
$L['trade_msg_comment_success'] = '评价提交成功';
$L['trade_msg_empty_explain'] = '解释内容不能为空';
$L['trade_msg_explain_again'] = '您已经解释过此评价';
$L['trade_msg_explain_success'] = '解释成功';
$L['trade_msg_secured_close'] = '系统未开启担保交易接口';
$L['trade_msg_bind_edit'] = '您的帐号已经绑定，不可再修改<br/>如果需要修改，请与网站联系';
$L['trade_msg_bind_exists'] = '帐号绑定已经存在，请检查您的帐号';
$L['trade_msg_bind_success'] = '更新成功';
$L['trade_msg_muti_choose'] = '请选择需要支付的订单';
$L['trade_msg_muti_empty'] = '暂无符合条件的订单';
$L['trade_msg_svae_note'] = '保存成功';
$L['trade_bind_title'] = '绑定{V0}帐号';
$L['trade_bind_error'] = '{V0}帐号格式不正确';
$L['trade_muti_title'] = '批量付款';
$L['trade_muti_send_title'] = '批量发货';
$L['trade_comment_title'] = '交易评价';
$L['trade_comment_show_title'] = '交易详情';
$L['trade_msg_cod'] = '此订单为货到付款';//V9.0

$L['trade_price_fee_null'] = '请填写附加金额';
$L['trade_price_fee_name'] = '请填写附加金额名称';
$L['trade_price_edit_success'] = '订单修改成功';
$L['trade_price_title'] = '修改订单';
$L['trade_detail_title'] = '订单详情';
$L['trade_exprss_title'] = '物流追踪';
$L['trade_confirm_success'] = '订单已确认，请等待买家付款';
$L['trade_pay_order_success'] = '订单支付成功，请等待卖家发货';
$L['trade_pay_order_title'] = '订单支付';
$L['trade_send_success'] = '已经确认发货，请等待买家确认收货';
$L['trade_send_title'] = '确认发货';
$L['trade_receive_title'] = '确认到货';
$L['trade_addtime_null'] = '请填写延长的时间';
$L['trade_addtime_success'] = '买家确认时间延长成功';
$L['trade_addtime_title'] = '延长买家确认时间';
$L['trade_success'] = '恭喜！此订单交易成功';
$L['trade_close_success'] = '交易已关闭';
$L['trade_delete_success'] = '订单删除成功';
$L['trade_pay_seller'] = '请填写收款会员名';
$L['trade_pay_self'] = '收款人不能是自己';
$L['trade_pay_seller_bad'] = '收款会员名不存在，请确认';
$L['trade_pay_amount'] = '请填写付款金额';
$L['trade_pay_note'] = '请填写付款说明';
$L['trade_pay_goods'] = '请填写商品或服务名称';
$L['trade_pay_title'] = '我要付款';
$L['trade_pay1_success'] = '直接付款成功，会员[{V0}]将直接收到您的付款';
$L['trade_pay0_success'] = '订单已经发出，请等待卖家确认';
$L['trade_order_sfields'] = array('按条件', '商品', '金额', '附加金额', '附加名称', '卖家', '发货方式', '物流号码', '备注');
$L['trade_order_title'] = '我的订单';
$L['trade_sfields'] = array('按条件', '商品', '金额', '附加金额', '附加名称', '买家', '买家姓名', '买家地址', '买家邮编', '买家手机', '买家电话', '发货方式', '物流号码', '买家备注', '商家备注');
$L['trade_title'] = '订单管理';
$L['trade_record_pay'] = '交易成功';
$L['trade_record_payfor'] = '站内付款';
$L['trade_record_receive'] = '站内收款';
$L['trade_record_new'] = '通知卖家确认订单';
$L['trade_refund'] = '申请售后';
$L['trade_refund_title'] = '申请售后';
$L['trade_refund_success'] = '您的售后申请已经提交，请等待卖家处理';
$L['trade_refund_agree_title'] = '同意退款';
$L['trade_refund_agree_success'] = '订单退款成功';
$L['trade_refund_by_seller'] = '[卖家]';

$L['trade_split_order'] = '请选择要拆离的订单';
$L['trade_split_from'] = '拆分订单';
$L['trade_split_to'] = '拆离订单';
$L['trade_split_oid'] = '原订单号:';
$L['trade_split_nid'] = '新订单号:';
$L['trade_split_success'] = '订单拆分成功';
$L['trade_split_title'] = '拆分订单';
$L['trade_bill_upload'] = '买家未上传支付凭证';
$L['trade_bill_confirm'] = '确认收款';
$L['trade_bill_check'] = '凭证审核';
$L['trade_bill_pass'] = '付款已通过，请及时发货';
$L['trade_bill_reject'] = '收款失败';
$L['trade_bill_fail'] = '付款未通过，请等待买家重新提交凭证';
$L['trade_bill_title'] = '审核凭证';
$L['trade_invoice_item'] = '发票不存在';
$L['trade_invoice_upload'] = '请上传发票';
$L['trade_invoice_format'] = '发票文件格式错误';
$L['trade_invoice_express'] = '请选择快递类型';
$L['trade_invoice_print'] = '发票开具';
$L['trade_invoice_sms'] = '开票通知';
$L['trade_invoice_success'] = '发票信息保存成功';
$L['trade_invoice_title'] = '发票详情';
$L['trade_contract_upload'] = '请上传合同';
$L['trade_contract_format'] = '合同文件格式错误';
$L['trade_contract_again'] = '重签合同';
$L['trade_contract_resign'] = '合同重签成功';
$L['trade_contract_send'] = '上传合同';
$L['trade_contract_sign'] = '签订合同';
$L['trade_contract_success'] = '合同上传成功';
$L['trade_contract_title'] = '合同详情';

$L['order_update_contract'] = '该订单包含未签署合同，请签署后继续操作';
$L['order_edit_aid'] = '请选择收货地址';
$L['order_edit_addr'] = '收货地址不存在';
$L['order_edit_success'] = '地址修改成功';
$L['order_edit_title'] = '修改地址';
$L['order_bill_wait'] = '请等待卖家发货';
$L['order_bill_stock'] = '抱歉，商品库存不足，请重新选择';
$L['order_bill_error'] = '抱歉，该订单不支持线下支付';
$L['order_bill_upload'] = '请上传交易凭证';
$L['order_bill_format'] = '交易凭证须为图片格式';
$L['order_bill_send'] = '上传凭证';
$L['order_bill_success'] = '凭证上传成功，请等待卖家审核';
$L['order_bill_title'] = '线下付款';
$L['order_invoice_timeout'] = '申请超过，订单已完成超过90日';
$L['order_invoice_empty'] = '卖家未设置开票类型';
$L['order_invoice_type'] = '请选择发票类型';
$L['order_invoice_company'] = '请填写发票抬头';
$L['order_invoice_taxid'] = '请填写纳税人识别号';
$L['order_invoice_sign'] = '专';
$L['order_invoice_address'] = '请填写公司注册地址';
$L['order_invoice_telephone'] = '请填写公司电话';
$L['order_invoice_bank'] = '请填写开户银行';
$L['order_invoice_account'] = '请填写银行帐号';
$L['order_invoice_apply'] = '申请开票';
$L['order_invoice_success'] = '申请提交成功，请等待卖家处理';
$L['order_invoice_title'] = '发票详情';
$L['order_contract_item'] = '合同不存在';
$L['order_contract_sign'] = '签署合同';
$L['order_contract_success'] = '合同签署成功';
$L['order_contract_title'] = '合同详情';
$L['order_refund_cod'] = '该订单为货到付款，请与卖家联系';
$L['order_refund_bill'] = '该订单为线下支付，请与卖家联系';
$L['order_refund_goods'] = '请选择商品';
$L['order_refund_type'] = '请选择服务类型';
$L['order_refund_reason'] = '请选择申请原因';
$L['order_refund_apply'] = '申请退款';

$L['process_item'] = '售后服务单不存在';
$L['process_check_aid'] = '请选择寄回地址';
$L['process_check_money'] = '账户余额不足';
$L['process_check_amount'] = '实付金额错误';
$L['process_check_reason'] = '请选择操作原因';
$L['process_check_addr'] = '寄回地址不存在';
$L['process_check_agree'] = '同意售后';
$L['process_check_refund'] = '订单退款';
$L['process_check_reject'] = '售后拒绝';
$L['process_check_seller_agree'] = '卖家同意';
$L['process_check_seller_reject'] = '卖家拒绝';
$L['process_check_success'] = '受理成功';
$L['process_check_title'] = '受理申请';
$L['process_receive_pass'] = '验收通过';
$L['process_receive_refund'] = '退款成功';
$L['process_receive_success'] = '验收成功';
$L['process_receive_title'] = '受理申请';
$L['process_send_time'] = '发货时间格式错误';
$L['process_send_addr'] = '收货地址不存在';
$L['process_send_aid'] = '原订单不存在，请选择收货地址';
$L['process_seller_send'] = '卖家寄回';
$L['process_buyer_send'] = '买家寄回';
$L['process_buyer_back'] = '退回商品';
$L['process_send_back'] = '售后寄回';
$L['process_send_title'] = '寄回商品';
$L['process_title'] = '售后详情';
$L['process_close_status'] = '售后服务单状态错误';
$L['process_close_success'] = '撤销成功';

$L['trade_order_id'] = '订单号:';
$L['trade_fee'] = '网站服务费';
$L['trade_buyer_timeout'] = '单号:{V0}[买家超时]';
$L['trade_sms_confirm'] = '通知买家付款';
$L['trade_sms_pay'] = '通知卖家发货';
$L['trade_sms_send'] = '通知买家已发货';
$L['trade_sms_income'] = '站内付款通知';
$L['trade_sms_receive'] = '通知卖家已收货';
$L['trade_message_t1'] = '站内交易提醒，您有一笔交易需要付款(单号{V0})';
$L['trade_message_c1'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 更新了您的订单<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_t2'] = '站内交易提醒，您有一笔交易需要发货(单号{V0})';
$L['trade_message_c2'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 支付了您的订单<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_t3'] = '站内交易提醒，您有一笔交易需要收货(单号{V0})';
$L['trade_message_c3'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 已经发货<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_t4'] = '站内交易提醒，您有一笔交易已经成功(单号{V0})';
$L['trade_message_c4'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 确认收货，交易完成<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_t5'] = '站内收入提醒，您收到一笔付款';
$L['trade_message_c5'] = '<a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 向您支付了 <span class="f_blue">{V3}'.$DT['money_unit'].'</span> 的站内付款<br/>备注：<span class="f_gray">{V4}</span>';
$L['trade_message_t6'] = '站内交易提醒，您有一笔交易需要确认(单号{V0})';
$L['trade_message_c6'] = '<a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 向您订购了：<br/>{V3}<br/>订单编号：<span class="f_red">T{V4}</span> &nbsp;订单金额为：<span class="f_blue f_b">{V5}'.$DT['money_unit'].'</span><br/><a href="{V6}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_t7'] = '站内交易提醒，您有一笔交易线下付款成功(单号{V0})';
$L['trade_message_c7'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 确认了您的线下付款凭证<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s7'] = '卖家已经收到订单(单号{V0})线下付款，请等待发货';
$L['trade_message_t8'] = '站内交易提醒，您有一笔交易线下付款失败(单号{V0})';
$L['trade_message_c8'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 拒绝了您的线下付款凭证<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s8'] = '卖家未收到订单(单号{V0})线下付款，请重新上传凭据';
$L['trade_message_t9'] = '站内交易提醒，您有一笔交易开票成功(单号{V0})';
$L['trade_message_c9'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 开具了发票<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s9'] = '卖家已经开具订单(单号{V0})的发票，请注意查收';
$L['trade_message_t10'] = '站内交易提醒，您有一笔交易需要重签合同(单号{V0})';
$L['trade_message_c10'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 重新上传了合同<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s10'] = '卖家已经重新上传订单(单号{V0})的合同，请重新签订';
$L['trade_message_t11'] = '站内交易提醒，您有一笔交易需要签订合同(单号{V0})';
$L['trade_message_c11'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 上传了合同<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s11'] = '卖家已经上传订单(单号{V0})的合同，请及时签订';
$L['trade_message_t12'] = '站内交易提醒，您有一笔交易需退回商品(单号{V0})';
$L['trade_message_c12'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 同意了您的售后申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s12'] = '卖家已经同意订单(单号{V0})的售后申请，请退回商品';
$L['trade_message_t13'] = '站内交易提醒，您有一笔交易已退款(单号{V0})';
$L['trade_message_c13'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 同意了您的退款申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s13'] = '卖家已经同意订单(单号{V0})的退款申请，请检查站内资金余额';
$L['trade_message_t14'] = '站内交易提醒，您有一笔交易售后申请未通过(单号{V0})';
$L['trade_message_c14'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 拒绝了您的售后申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s14'] = '卖家已经拒绝订单(单号{V0})的售后申请，请及时处理';
$L['trade_message_t15'] = '站内交易提醒，您有一笔交易已退款(单号{V0})';
$L['trade_message_c15'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 同意了您的退款申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s15'] = '卖家已经同意订单(单号{V0})的退款申请，请检查站内资金余额';
$L['trade_message_t16'] = '站内交易提醒，您有一笔交易售后申请未通过(单号{V0})';
$L['trade_message_c16'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 拒绝了您的售后申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s16'] = '卖家已经拒绝订单(单号{V0})的售后申请，请及时处理';
$L['trade_message_t17'] = '站内交易提醒，您有一笔交易售后已寄回(单号{V0})';
$L['trade_message_c17'] = '卖家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 寄回了您的售后商品<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s17'] = '卖家已经寄回订单(单号{V0})的售后商品，请注意查收';
$L['trade_message_t18'] = '站内交易提醒，您有一笔交易需要确认线下收款(单号{V0})';
$L['trade_message_c18'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 上传了您的订单线下付款凭证<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s18'] = '买家已经上传订单(单号{V0})线下付款凭证，请及时审核';
$L['trade_message_t19'] = '站内交易提醒，您有一笔交易需要开具发票(单号{V0})';
$L['trade_message_c19'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 提交了开票申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s19'] = '买家已经提交订单(单号{V0})发票申请，请及时开票';
$L['trade_message_t20'] = '站内交易提醒，您有一笔交易合同签署成功(单号{V0})';
$L['trade_message_c20'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 上传了交易合同<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s20'] = '买家已经上传订单(单号{V0})合同，合同签署成功';
$L['trade_message_t21'] = '站内交易提醒，您有一笔交易需要{V1}(单号{V0})';
$L['trade_message_c21'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 提交了{V4}售后申请<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s21'] = '买家已经申请订单(单号{V0}){V1}，请及时处理';
$L['trade_message_t22'] = '站内交易提醒，您有一笔售后服务待收货(单号{V0})';
$L['trade_message_c22'] = '买家 <a href="{V0}" class="t">{V1}</a> 于 <span class="f_gray">{V2}</span> 寄回了商品<br/><a href="{V3}" class="t" target="_blank">&raquo; 请点这里立即处理或查看详情</a>';
$L['trade_message_s22'] = '买家已经退回订单(单号{V0})的商品，请注意收货';

$L['purchase_title'] = '确认订单';
$L['purchase_msg_address'] = '请先创建收货地址';
$L['purchase_msg_goods'] = '商品不存在';
$L['purchase_msg_self'] = '不能购买自己的商品';
$L['purchase_msg_group_finish'] = '团购已结束';
$L['purchase_msg_online_buy'] = '此商品不支持在线购买';
$L['post_free'] = '包邮';
$L['msg_express_no'] = '请填写快递单号';
$L['msg_express_type'] = '请填写快递类型';
$L['msg_express_no_error'] = '快递单号格式错误';
$L['msg_express_date_error'] = '发货时间格式错误';

$L['express_sfields'] = array('按条件', '商品名称', '快递公司', '快递单号', '收件手机', '收件地址');
$L['express_title'] = '我的快递';

$L['contract_trade_sfields'] = array('按条件', '商品', '金额', '甲方', '买家');
$L['contract_order_sfields'] = array('按条件', '商品', '金额', '乙方', '卖家');
$L['contract_title'] = '我的合同';

$L['invoice_trade_sfields'] = array('关键词', '发票抬头', '发票类型', '纳税人识别号', '金额', '商品名称', '买家', '买家手机', '买家邮件', '备注');
$L['invoice_order_sfields'] = array('关键词', '发票抬头', '发票类型', '纳税人识别号', '金额', '商品名称', '卖家');
$L['invoice_title'] = '我的发票';

$L['service_sfields'] = array('按条件', '商品', '申请原因', '补充说明');
$L['service_name'] = '类型';
$L['service_title'] = '售后服务';
$L['service_type'] = array('仅退款', '退货退款', '换货', '维修');
$L['service_status'] = array(
	'<span style="color:#008080;">待受理</span>',
	'<span style="color:#999999;">已撤销</span>',
	'<span style="color:#FF6600;">已拒绝</span>',
	'<span style="color:#0000FF;">已同意</span>',
	'<span style="color:#FF0000;">待验收</span>',
	'<span style="color:#0000FF;">处理中</span>',
	'<span style="color:#0000FF;">已发出</span>',
	'<span style="color:#008000;">已完成</span>',
);

$L['log_buy'] = '买家下单';
$L['log_pay'] = '买家付款';
$L['log_get'] = '买家收货';
$L['log_use'] = '买家消费';
$L['log_success'] = '交易成功';
$L['log_refund'] = array('申请退款', '退货退款', '申请换货', '申请维修');
$L['log_remind'] = '提醒发货';
$L['log_addtime'] = '延长收货';
$L['log_buyer_coment'] = '买家评价';
$L['log_buyer_close'] = '买家关闭';
$L['log_seller_coment'] = '卖家评价';
$L['log_seller_close'] = '卖家关闭';
$L['log_send'] = '卖家发货';
$L['log_getpay'] = '卖家收款';
$L['log_agree'] = '卖家退款';
$L['log_edit'] = '修改订单';

$L['stock_pay'] = '订单支付';
$L['stock_refund'] = '订单退款';
$L['stock_no'] = '单号:';
$L['stock_less'] = '库存不足';
?>