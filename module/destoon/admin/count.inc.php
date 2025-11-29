<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
	array('重名检测', '?file='.$file.'&action=repeat'),
	array('信息统计', '?file='.$file),
	array('统计报表', '?file='.$file.'&action=stats'),
);
switch($action) {
	case 'js':
		@header("Content-type:text/javascript");
		$db->halt = 0;
		$today = datetotime(timetodate($DT_TIME, 3).' 00:00:00');

		$num = $db->count($DT_PRE.'finance_charge', "status=0");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("charge").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'finance_cash', "status=0");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("cash").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'keyword', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("keyword").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'guestbook', "edittime=0");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("guestbook").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'member_check', "edittime=0");//待审核资料修改
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("edit_check").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'company_check', "edittime=0");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("home_check").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'ask', "status=0 AND qid=0");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("ask").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'alert', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("alert").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'news', "status=2");//待审核公司新闻
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("news").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'honor', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("honor").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'page', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("page").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'link', "status=2 AND username<>''");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("comlink").innerHTML="'.$num.'";}catch(e){}';

		foreach(array('company', 'truename', 'mobile', 'close') as $v) {
			$num = $db->count($DT_PRE.'validate', "type='$v' AND status=2");//待审核认证
			$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
			echo 'try{document.getElementById("v'.$v.'").innerHTML="'.$num.'";}catch(e){}';
		}

		$num = $db->count($DT_PRE.'ad', "status=2");//广告
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("ad").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'spread', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("spread").innerHTML="'.$num.'";}catch(e){}'; 
		$num = $db->count($DT_PRE.'comment', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("comment").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'link', "status=2 AND username=''");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("link").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'gift_order', "status='处理中'");//礼品订单
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("gift").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'spider_url', "status=0");//采集网址
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("spideru").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'spider_url', "status=2");//采集数据
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("spiderd").innerHTML="'.$num.'";}catch(e){}';

		$num = $db->count($DT_PRE.'member');//会员
		echo 'try{document.getElementById("member").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'member_upgrade', "status=2");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("member_upgrade").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'member', "groupid=4");
		$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
		echo 'try{document.getElementById("member_check").innerHTML="'.$num.'";}catch(e){}';
		$num = $db->count($DT_PRE.'member', "regtime>$today");
		echo 'try{document.getElementById("member_new").innerHTML="'.$num.'";}catch(e){}';

		foreach($MODULE as $m) {
			if($m['moduleid'] < 5 || $m['islink']) continue;
			$mid = $m['moduleid'];
			$table = get_table($mid);
			$num = $db->count($table, '1');
			echo 'try{Dd("m_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';
			$num = $db->count($table, "status=3");
			echo 'try{Dd("m_'.$mid.'_1").innerHTML="'.$num.'";}catch(e){}';
			$num = $db->count($table, "status=2");
			$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
			echo 'try{Dd("m_'.$mid.'_2").innerHTML="'.$num.'";}catch(e){}';
			$num = $db->count($table, "addtime>$today");
			echo 'try{Dd("m_'.$mid.'_3").innerHTML="'.$num.'";}catch(e){}';

			if($m['module'] == 'mall' || $m['module'] == 'sell') {
				$num = $db->count($DT_PRE.'order', "mid=$mid");
				echo 'try{document.getElementById("order_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'order', "mid=$mid AND status=5");
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("order_'.$mid.'_5").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'order', "mid=$mid AND status=4");
				echo 'try{document.getElementById("order_'.$mid.'_4").innerHTML="'.$num.'";}catch(e){}';
			}

			if($m['module'] == 'group') {
				$num = $db->count($DT_PRE.'group_order_'.$mid, "1");
				echo 'try{document.getElementById("order_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'group_order_'.$mid, "status=4");
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("order_'.$mid.'_4").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'group_order_'.$mid, "status=3");
				echo 'try{document.getElementById("order_'.$mid.'_3").innerHTML="'.$num.'";}catch(e){}';
			}

			if($m['module'] == 'quote') {
				$num = $db->count($DT_PRE.'quote_product_'.$mid, "1");
				echo 'try{document.getElementById("product_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'quote_price_'.$mid, "1");
				echo 'try{document.getElementById("price_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'quote_price_'.$mid, "status=2");
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("price_'.$mid.'_2").innerHTML="'.$num.'";}catch(e){}';
			}

			if($m['module'] == 'exhibit') {
				$num = $db->count($DT_PRE.'exhibit_sign_'.$mid, "1");
				echo 'try{document.getElementById("sign_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'exhibit_sign_'.$mid, "addtime>$today");
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("sign_'.$mid.'_3").innerHTML="'.$num.'";}catch(e){}';
			}

			if($m['module'] == 'know') {
				$num = $db->count($DT_PRE.'know_expert_'.$mid, "1");
				echo 'try{document.getElementById("expert_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'know_answer_'.$mid, "1");
				echo 'try{document.getElementById("answer_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'know_answer_'.$mid, "status=2");
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("answer_'.$mid.'_2").innerHTML="'.$num.'";}catch(e){}';
			}

			if($m['module'] == 'club') {
				$num = $db->count($DT_PRE.'club_group_'.$mid, "status=2");//商圈
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("club_group_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'club_reply_'.$mid, "status=2");//商圈回复
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("club_reply_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';

				$num = $db->count($DT_PRE.'club_fans_'.$mid, "status=2");//商圈粉丝
				$num = $num ? '<strong class=\"f_red\">'.$num.'</strong>' : 0;
				echo 'try{document.getElementById("club_fans_'.$mid.'").innerHTML="'.$num.'";}catch(e){}';
			}
		}
	break;
	case 'todo':
		$db->halt = 0;
		$today = datetotime(timetodate($DT_TIME, 3).' 00:00:00');
		$htm = '';
		$arr = cache_read('doctor.php');
		if(isset($arr['warn']) && $arr['warn'] > 0) $htm .= '<li><a href="?file=doctor">系统体检待修复 (<b>'.$arr['warn'].'</b>)</a></li>';
		if($DT['mail_type'] != 'close') {
			$t = $db->get_one("SELECT status FROM {$DT_PRE}mail_log ORDER BY itemid DESC");
			if($t && $t['status'] != 3) $htm .= '<li><a href="?moduleid=2&file=sendmail&action=record&status=2">邮件发送失败 (<b>1+</b>)</a></li>';
		}
		if($DT['sms']) {
			$t = $db->get_one("SELECT status FROM {$DT_PRE}sms ORDER BY itemid DESC");
			if($t && $t['status'] != 3) $htm .= '<li><a href="?moduleid=2&file=sendsms&action=record&status=2">短信发送失败 (<b>1+</b>)</a></li>';
		}
		if($DT['push_appkey'] && $DT['push_secret']) {
			$t = $db->get_one("SELECT status FROM {$DT_PRE}app_push ORDER BY itemid DESC");
			if($t && $t['status'] != 3) $htm .= '<li><a href="?moduleid=2&file=sendpush&action=record&status=2">消息推送失败 (<b>1+</b>)</a></li>';
		}
		$num = $db->count($DT_PRE.'finance_charge', "status=0");
		if($num) $htm .= '<li><a href="?moduleid=2&file=charge&status=0">待受理在线充值 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'finance_cash', "status=0");
		if($num) $htm .= '<li><a href="?moduleid=2&file=cash&status=0">待受理资金提现 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'keyword', "status=2");
		if($num) $htm .= '<li><a href="?file=keyword&status=2">待审核搜索关键词 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'guestbook', "edittime=0");
		if($num) $htm .= '<li><a href="?moduleid=3&file=guestbook">待回复网站留言 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'member_check', "edittime=0");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=member&status=1">待审核资料修改 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'company_check', "edittime=0");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=home&status=1">待审核商铺设置 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'ask', "status=0 AND qid=0");
		if($num) $htm .= '<li><a href="?moduleid=2&file=ask&status=0">待受理客服中心 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'alert', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=alert&action=check">待审核贸易提醒 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'gift_order', "status='处理中'");
		if($num) $htm .= '<li><a href="?moduleid=3&file=gift&action=order&fields=5&kw=%E5%A4%84%E7%90%86%E4%B8%AD">待处理礼品订单 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'spider_url', "status=0");
		if($num) $htm .= '<li><a href="?moduleid=3&file=spider&action=url&status=0">待采集内容网址 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'spider_url', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=3&file=spider&action=data&status=2">待发布采集数据 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'news', "status=2");//待审核公司新闻
		if($num) $htm .= '<li><a href="?moduleid=2&file=news&action=check">待审核公司新闻 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'honor', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=honor&action=check">待审核荣誉资质 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'page', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=page&action=check">待审核公司单页 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'link', "status=2 AND username<>''");
		if($num) $htm .= '<li><a href="?moduleid=2&file=link&action=check">待审核公司链接 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'validate', "type='company' AND status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=company&status=2">待审核公司认证 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'validate', "type='truename' AND status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=truename&status=2">待核审实名认证 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'validate', "type='mobile' AND status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=mobile&status=2">待审核手机认证 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'validate', "type='email' AND status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=email&status=2">待审核邮件认证 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'validate', "type='close' AND status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=validate&action=close&status=2">待审核注销申请 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'ad', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=3&file=ad&action=list&job=check">待审广告购买 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'spread', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=3&file=spread&action=check">待审核排名推广 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'comment', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=3&file=comment&action=check">待审核评论 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'link', "status=2 AND username=''");
		if($num) $htm .= '<li><a href="?moduleid=3&file=link&action=check">待审核友情链接 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'member_upgrade', "status=2");
		if($num) $htm .= '<li><a href="?moduleid=2&file=grade&action=check">待审核会员升级 (<b>'.$num.'</b>)</a></li>';
		$num = $db->count($DT_PRE.'member', "groupid=4");
		if($num) $htm .= '<li><a href="?moduleid=2&action=check">待审核会员注册 (<b>'.$num.'</b>)</a></li>';
        // 设备维修报价系统：待审核报价
        $num = $db->count($DT_PRE.'article_quote_21', "status=0");
        if($num) $htm .= '<li><a href="?moduleid=21&file=quote&status=0">报价审核待处理 (<b>'.$num.'</b>)</a></li>';

        foreach($MODULE as $m) {
			if($m['moduleid'] < 5 || $m['islink']) continue;
			$mid = $m['moduleid'];
			$table = get_table($mid);
			$num = $db->count($table, "status=2");
			if($num) $htm .= '<li><a href="?moduleid='.$mid.'&action=check">待审核'.$m['name'].' (<b>'.$num.'</b>)</a></li>';

			if($m['module'] == 'mall' || $m['module'] == 'sell') {
				$num = $db->count($DT_PRE.'order', "mid=$mid AND status=5");
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=order&status=5">待受理'.$m['name'].'订单 (<b>'.$num.'</b>)</a></li>';
			}
			if($m['module'] == 'group') {
				$num = $db->count($DT_PRE.'group_order_'.$mid, "status=4");
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=order&status=4">待受理'.$m['name'].'订单 (<b>'.$num.'</b>)</a></li>';
			}
			if($m['module'] == 'quote') {
				$num = $db->count($DT_PRE.'quote_price_'.$mid, "status=2");
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=price&action=check">待审核'.$m['name'].'报价 (<b>'.$num.'</b>)</a></li>';
			}
			if($m['module'] == 'exhibit') {
				$num = $db->count($DT_PRE.'exhibit_sign_'.$mid, "addtime>$today");
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=sign">'.$m['name'].'今日报名 (<b>'.$num.'</b>)</a></li>';
			}
			if($m['module'] == 'know') {
				$num = $db->count($DT_PRE.'know_answer_'.$mid, "status=2");
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=answer&action=check">待审核'.$m['name'].'回答 (<b>'.$num.'</b>)</a></li>';
			}
			if($m['module'] == 'club') {
				$num = $db->count($DT_PRE.'club_group_'.$mid, "status=2");//商圈
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=group&action=check">待审核'.$m['name'].'申请 (<b>'.$num.'</b>)</a></li>';

				$num = $db->count($DT_PRE.'club_reply_'.$mid, "status=2");//商圈回复
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=reply&action=check">待审核'.$m['name'].'回复 (<b>'.$num.'</b>)</a></li>';

				$num = $db->count($DT_PRE.'club_fans_'.$mid, "status=2");//商圈粉丝
				if($num) $htm .= '<li><a href="?moduleid='.$mid.'&file=fans&action=check">待审核'.$m['name'].'粉丝 (<b>'.$num.'</b>)</a></li>';
			}
		}		
		@header("Content-type:text/javascript");
		if($htm) {
			$htm = '<div class="tt"><span class="f_r"><a href="?file=count" style="font-weight:normal;font-size:12px;">更多<span style="font-family:simsun;font-weight:bold;padding:0 2px;">&gt;</span></a></span>待办事项</div><ul>'.$htm.'</ul></div>';
			echo 'try{document.getElementById("todo").innerHTML=\''.$htm.'\';document.getElementById("todo").style.display=\'table\';}catch(e){}';
		}
	break;
	case 'repeat':
		$mid or $mid = 21;
		$key = isset($key) ? trim($key) : 'title';
		$num = isset($num) ? intval($num) : 100;
		$status = isset($status) ? intval($status) : 3;
		$lists = array();
		if(isset($ok)) {
			$submit = 1;
			$act = '';
			if($status == 4) $act = 'expire';
			if($status == 2) $act = 'check';
			if($status == 1) $act = 'reject';
			if($status == 0) $act = 'recycle';
			$condition = "status=$status";
			if($keyword) $condition .= match_kw($key, $keyword);
			$result = $db->query("SELECT COUNT(`$key`) AS num,`$key` FROM ".get_table($mid)." WHERE {$condition} GROUP BY `$key` ORDER BY num DESC LIMIT 0,$num");
			while($r = $db->fetch_array($result)) {
				if($r['num'] < 2) continue;
				$r['kw'] = urlencode($r[$key]);
				$lists[] = $r;
			}
		}
		include tpl('count_repeat');
	break;
	case 'stats':
		$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
		$year or $year = date('Y', $DT_TIME);
		$month = isset($month) ? intval($month) : 0;
		if($mid == 1 || $mid == 3) $mid = 0;
		if($mid == 4) $mid = 2;
		include tpl('count_stats');
	break;
	default:
		$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
		$year or $year = date('Y', $DT_TIME);
		$month = isset($month) ? intval($month) : 0;
		if($mid == 1 || $mid == 3) $mid = 0;
		if($mid == 4) $mid = 2;
		include tpl('count');
	break;
}
?>