<?php
defined('DT_ADMIN') or exit('Access Denied');
$TYPE = explode('|', $MOD['guestbook_type']);
require DT_ROOT.'/module/'.$module.'/guestbook.class.php';
$do = new guestbook();
$menus = array (
    array('留言列表', '?moduleid='.$moduleid.'&file='.$file),
    array('模块设置', 'javascript:Dwidget(\'?moduleid='.$moduleid.'&file=setting&action='.$file.'\', \'模块设置\');'),
);
if($_catids || $_areaids) require DT_ROOT.'/module/destoon/admin/check.inc.php';
if(in_array($action, array('', 'check'))) {
	$sfields = array('按条件', '留言标题', '留言类型', '留言内容', '视频地址', '回复内容', '会员名', '联系人', '联系电话', '电子邮件', 'QQ', '微信', '阿里旺旺', 'Skype', '留言IP');
	$dfields = array('title','title','type','content','video','reply','username','truename','telephone','email','qq','wx','ali','skype','ip');
	$sorder  = array('结果排序方式', '留言时间降序', '留言时间升序', '回复时间降序', '回复时间升序');
	$dorder  = array('itemid DESC', 'addtime DESC', 'addtime ASC', 'edittime DESC', 'edittime ASC');

	isset($fields) && isset($dfields[$fields]) or $fields = 0;
	isset($order) && isset($dorder[$order]) or $order = 0;
	isset($datetype) && in_array($datetype, array('edittime', 'addtime')) or $datetype = 'addtime';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$status = isset($status) ? intval($status) : 0;
	isset($type) && in_array($type, $TYPE) or $type = '';
	$thumb = isset($thumb) ? intval($thumb) : 0;
	$video = isset($video) ? intval($video) : 0;
	$guest = isset($guest) ? intval($guest) : 0;
	$reply = isset($reply) ? intval($reply) : 0;
	$hidden = isset($hidden) ? intval($hidden) : 0;
	$tid = isset($tid) ? intval($tid) : 0;
	$rid = isset($rid) ? intval($rid) : 0;
	$tid or $tid = '';
	$rid or $rid = '';

	$fields_select = dselect($sfields, 'fields', '', $fields);
	$order_select  = dselect($sorder, 'order', '', $order);
	$module_select = module_select('mid', '模块', $mid, '', '1,2');

	$condition = '1';
	if($_areaids) $condition .= " AND areaid IN (".$_areaids.")";//CITY
	if($_self) $condition .= " AND editor='$_username'";//SELF
	if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
	if($areaid) $condition .= ($ARE['child']) ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
	if($fromtime) $condition .= " AND `$datetype`>=$fromtime";
	if($totime) $condition .= " AND `$datetype`<=$totime";
	if($type) $condition .= " AND type='$type'";
	if($mid) $condition .= " AND mid='$mid'";
	if($tid) $condition .= " AND tid='$tid'";
	if($rid) $condition .= " AND rid='$rid'";
	if($thumb) $condition .= " AND thumbs<>''";
	if($video) $condition .= " AND video<>''";
	if($guest) $condition .= " AND username=''";
	if($status) $condition .= $status == 3 ? " AND status=3" : " AND status!=3";
	if($reply) $condition .= $reply == 1 ? " AND edittime>0" : " AND edittime=0";
	if($hidden) $condition .= $hidden == 1 ? " AND hidden=1" : " AND hidden=0";
}
switch($action) {
	case 'edit':
		$itemid or msg();
		$TYPE = explode('|', trim($MOD['guestbook_type']));
		$do->itemid = $itemid;
		$r = $do->get_one();
		if($submit) {
			if($do->pass($post)) {
				$do->edit($post);
				if($post['reply']) {
					$msg = isset($msg) ? 1 : 0;
					$eml = isset($eml) ? 1 : 0;
					$sms = isset($sms) ? 1 : 0;
					$wec = isset($wec) ? 1 : 0;
					if($msg == 0) $sms = $wec = 0;
					if($msg || $eml || $sms || $wec) {
						$reason = $content;
						$linkurl = $EXT['guestbook_url'].'index'.DT_EXT.'?itemid='.$itemid;
						$subject = '留言回复通知(ID:'.$itemid.')';
						$content = '尊敬的会员：<br/>您于'.timetodate($r['addtime'], 3).'的留言回复如下：<br/><br/>'.nl2br($post['reply']).'<br/><br/>';
						$content .= '请点击下面的链接查看留言详情：<br/>';
						$content .= '<a href="'.$linkurl.'" target="_blank">'.$linkurl.'</a><br/>';
						$content .= '如果您对此操作有异议，请及时与网站联系。<br/>';
						$email = $r['email'];
						if($r['username']) {							
							$user = userinfo($r['username']);
							if($msg) send_message($user['username'], $subject, $content);
							if($sms) send_sms($user['mobile'], $subject.'，详见站内信。'.$DT['sms_sign']);
							if($wec) send_weixin($user['username'], $subject.'，详见站内信。');
							if(!$email) $email = $user['email'];
						}
						if($eml) send_mail($email, $subject, $content);
					}
				}
				dmsg('修改成功', $forward);
			} else {
				msg($do->errmsg);
			}
		} else {
			$r['thumb'] = '';
			extract($r);
			$thumbs = get_thumbs($r);
			$addtime = timetodate($addtime);
			$edittime = timetodate($edittime);
			$MOD['thumb_width'] = $MOD['thumb_height'] = 200;
			include tpl('guestbook_edit', $module);
		}
	break;
	case 'check':
		$itemid or msg('请选择留言');
		$do->check($itemid, $status);
		dmsg('设置成功', $forward);
	break;
	case 'delete':
		$itemid or msg('请选择留言');
		$do->delete($itemid);
		dmsg('删除成功', $forward);
	break;
	default:
		$lists = $do->get_list($condition, $dorder[$order]);
		include tpl('guestbook', $module);
	break;
}
?>