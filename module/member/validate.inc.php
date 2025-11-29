<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MOD['vmember'] or dheader('./');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$username = $_username;
$user = userinfo($username);
$step = isset($step) ? intval($step) : 0;
$could_email = $DT['mail_type'] == 'close' ? 0 : 1;
$could_mobile = $DT['sms'] ? 1 : 0;
$seconds = 0;
$status = 0;
$V = array();
if(in_array($action, array('email', 'mobile', 'company', 'truename', 'bank', 'shop'))) {
	$V = $db->get_one("SELECT * FROM {$DT_PRE}validate WHERE type='$action' AND username='$username' ORDER BY itemid DESC");
	$va = 'v'.$action;
	if($user[$va]) {
		$status = 3;
		if($job == 'reset') {
			$db->query("UPDATE {$DT_PRE}member SET `{$va}`=0 WHERE userid=$_userid");
			userclean($username);
			dheader('?action='.$action.'&reload='.$DT_TIME);
		}
	} else {
		$status = $V && $V['status'] == 2 ? 2 : 1;
	}
}
switch($action) {
	case 'email':
		$MOD['vemail'] or dheader('?action=index');
		$could_email or message($L['send_mail_close']);
		if($status == 1) {
			(isset($email) && is_email($email)) or $email = '';
			$session = new dsession();
			isset($_SESSION['email_send']) or $_SESSION['email_send'] = 0;
			isset($_SESSION['email_time']) or $_SESSION['email_time'] = 0;
			$second = $DT_TIME - $_SESSION['email_time'];
			if($step == 2) {
				$email = $_SESSION['email_save'];
				is_email($email) or dheader('?action='.$action);
				$code = isset($code) ? trim($code) : '';
				$_SESSION['email_oppo'] = $_SESSION['email_oppo'] + 1;
				if($_SESSION['email_oppo'] > 3 || $DT_TIME - $_SESSION['email_time'] > $MOD['auth_days']*60) $_SESSION['email_code'] = '';
				(preg_match("/^[0-9]{6}$/", $code) && $_SESSION['email_code'] == md5($email.'|'.$code.'|'.$username.'|VE')) or message($L['register_pass_emailcode']);
				$history = $user['email'] ? $user['email'] : $email;
				$db->query("UPDATE {$DT_PRE}member SET vemail=0 WHERE email='$email'");
				$db->query("UPDATE {$DT_PRE}member SET email='$email',vemail=1 WHERE userid=$_userid");
				$db->query("INSERT INTO {$DT_PRE}validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$email','$history','email','$username','$DT_IP','$DT_TIME','3','validate','$DT_TIME')");
				$user['vemail'] = 1;
				update_validate($user, $MG);
				userclean($username);
				unset($_SESSION['email_save'], $_SESSION['email_code'], $_SESSION['email_time'], $_SESSION['email_send']);
			} else if($step == 1) {
				captcha($captcha);
				is_email($email) or message($L['member_email_null']);
				if($user['vemail'] && $user['email'] == $email) message($L['send_email_exist']);
				$emailcode = random(6, '0-9');
				$_SESSION['email_save'] = $email;
				$_SESSION['email_code'] = md5($email.'|'.$emailcode.'|'.$username.'|VE');
				$_SESSION['email_time'] = $DT_TIME;
				$_SESSION['email_oppo'] = 0;
				$_SESSION['email_send'] = $_SESSION['email_send'] + 1;
				$title = $L['register_msg_emailcode'];
				$content = ob_template('emailcode', 'mail');
				send_mail($email, $title, stripslashes($content));
				#log_write($content, 'mail', 1);
			} else {
				$seconds = $second < 180 ? 180 - $second : 0;
				$email or $email = $_email;
				if(substr($email, -4) == '.sns') $email = '';
			}
		}
		$head_title = $L['validate_email_title'];
	break;
	case 'mobile':
		$MOD['vmobile'] or dheader('?action=index');
		$could_mobile or message($L['send_sms_close']);
		if($status == 1) {
			(isset($mobile) && is_mobile($mobile)) or $mobile = '';
			$session = new dsession();
			isset($_SESSION['mobile_send']) or $_SESSION['mobile_send'] = 0;
			isset($_SESSION['mobile_time']) or $_SESSION['mobile_time'] = 0;
			$second = $DT_TIME - $_SESSION['mobile_time'];
			if($step == 2) {
				$mobile = $_SESSION['mobile_save'];
				is_mobile($mobile) or dheader('?action='.$action);
				$code = isset($code) ? trim($code) : '';
				$_SESSION['mobile_oppo'] = $_SESSION['mobile_oppo'] + 1;
				if($_SESSION['mobile_oppo'] > 3 || $DT_TIME - $_SESSION['mobile_time'] > $MOD['auth_days']*60) $_SESSION['mobile_code'] = '';
				(preg_match("/^[0-9]{6}$/", $code) && $_SESSION['mobile_code'] == md5($mobile.'|'.$code.'|'.$username.'|VM')) or message($L['register_pass_mobilecode']);
				$history = $user['mobile'] ? $user['mobile'] : $mobile;
				$db->query("UPDATE {$DT_PRE}member SET vmobile=0 WHERE mobile='$mobile'");
				$db->query("UPDATE {$DT_PRE}member SET mobile='$mobile',vmobile=1 WHERE userid=$_userid");
				$db->query("INSERT INTO {$DT_PRE}validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$mobile','$history','mobile','$username','$DT_IP','$DT_TIME','3','validate','$DT_TIME')");
				$user['vmobile'] = 1;
				update_validate($user, $MG);
				userclean($username);
				unset($_SESSION['mobile_save'], $_SESSION['mobile_code'], $_SESSION['mobile_time'], $_SESSION['mobile_send']);
			} else if($step == 1) {
				captcha($captcha);
				if(!is_mobile($mobile)) message($L['member_mobile_null']);				
				if($user['vmobile'] && $user['mobile'] == $mobile) message($L['send_mobile_exist']);
				if(max_sms($mobile)) message($L['sms_msg_max']);
				$mobilecode = random(6, '0-9');
				$_SESSION['mobile_save'] = $mobile;
				$_SESSION['mobile_code'] = md5($mobile.'|'.$mobilecode.'|'.$username.'|VM');
				$_SESSION['mobile_time'] = $DT_TIME;
				$_SESSION['mobile_oppo'] = 0;
				$_SESSION['mobile_send'] = $_SESSION['mobile_send'] + 1;
				$content = lang('sms->sms_code', array($mobilecode, $MOD['auth_days'])).$DT['sms_sign'];
				send_sms($mobile, $content);
				#log_write($content, 'sms', 1);
			} else {
				$seconds = $second < 180 ? 180 - $second : 0;
				$mobile or $mobile = $_mobile;
			}
		}
		$head_title = $L['validate_mobile_title'];
	break;
	case 'truename':
		$MOD['vtruename'] or dheader('?action=index');
		if($status == 1) {
			$types = explode('|', $MOD['id_types']);
			if($submit) {
				captcha($captcha);
				if(!$truename) message($L['validate_truename_name']);
				if(!in_array($idtype, $types)) message($L['validate_truename_idtype']);
				$idno = trim(strtoupper($idno));
				if(!preg_match("/^[0-9X-Z]{18,}$/", $idno)) message($L['validate_truename_idno']);
				(isset($thumb) &&  is_url($thumb) ) or $thumb  = '';
				(isset($thumb1) && is_url($thumb1)) or $thumb1 = '';
				(isset($thumb2) && is_url($thumb2)) or $thumb2 = '';
				if($MOD['vtruename_v1'] == 2 && $MOD['vtruename_v1_name'] && !$thumb ) message($L['validate_upload'].$MOD['vtruename_v1_name']);
				if($MOD['vtruename_v2'] == 2 && $MOD['vtruename_v2_name'] && !$thumb1) message($L['validate_upload'].$MOD['vtruename_v2_name']);
				if($MOD['vtruename_v3'] == 2 && $MOD['vtruename_v3_name'] && !$thumb2) message($L['validate_upload'].$MOD['vtruename_v3_name']);
				$truename = dhtmlspecialchars($truename);
				$history = $user['truename'] ? $user['truename'] : $truename;
				$totime = (isset($totime) && is_time($totime)) ? datetotime($totime) : 0;
				$totime1 = (isset($totime1) && is_time($totime1)) ? datetotime($totime1) : 0;
				$totime2 = (isset($totime2) && is_time($totime2)) ? datetotime($totime2) : 0;
				$db->query("INSERT INTO {$DT_PRE}validate (title,title1,title2,history,type,username,ip,addtime,status,editor,edittime,thumb,thumb1,thumb2,totime,totime1,totime2) VALUES ('$truename','$idtype','$idno','$history','$action','$username','$DT_IP','$DT_TIME','2','system','$DT_TIME','$thumb','$thumb1','$thumb2','$totime','$totime1','$totime2')");
				clear_upload($thumb.$thumb1.$thumb2, $db->insert_id(), 'validate');
				dmsg($L['validate_truename_success'], '?action='.$action);
			}
		}
		$head_title = $L['validate_truename_title'];
	break;
	case 'company':
		($MOD['vcompany'] && $MG['type']) or dheader('?action=index');
		if($status == 1) {
			if($submit) {
				captcha($captcha);
				if(!$company) message($L['validate_company_name']);
				$taxid = trim(strtoupper(strip_sql($taxid, 0)));
				if(!preg_match("/^[0-9A-Z]{12,21}$/", $taxid)) message($L['validate_company_taxid']);
				(isset($thumb) &&  is_url($thumb) ) or $thumb  = '';
				(isset($thumb1) && is_url($thumb1)) or $thumb1 = '';
				(isset($thumb2) && is_url($thumb2)) or $thumb2 = '';
				if($MOD['vcompany_v1'] == 2 && $MOD['vcompany_v1_name'] && !$thumb ) message($L['validate_upload'].$MOD['vcompany_v1_name']);
				if($MOD['vcompany_v2'] == 2 && $MOD['vcompany_v2_name'] && !$thumb1) message($L['validate_upload'].$MOD['vcompany_v2_name']);
				if($MOD['vcompany_v3'] == 2 && $MOD['vcompany_v3_name'] && !$thumb2) message($L['validate_upload'].$MOD['vcompany_v3_name']);
				$company = dhtmlspecialchars($company);
				$history = $user['company'] ? $user['company'] : $company;
				$totime = (isset($totime) && is_time($totime)) ? datetotime($totime) : 0;
				$totime1 = (isset($totime1) && is_time($totime1)) ? datetotime($totime1) : 0;
				$totime2 = (isset($totime2) && is_time($totime2)) ? datetotime($totime2) : 0;
				$db->query("INSERT INTO {$DT_PRE}validate (title,title1,history,type,username,ip,addtime,status,editor,edittime,thumb,thumb1,thumb2,totime,totime1,totime2) VALUES ('$company','$taxid','$history','$action','$username','$DT_IP','$DT_TIME','2','system','$DT_TIME','$thumb','$thumb1','$thumb2','$totime','$totime1','$totime2')");
				clear_upload($thumb.$thumb1.$thumb2, $db->insert_id(), 'validate');
				dmsg($L['validate_company_success'], '?action='.$action);
			}
		}
		$head_title = $L['validate_company_title'];
	break;
	case 'shop':
		($MOD['vshop'] && $MG['homepage']) or dheader('?action=index');
		if($status == 1) {
			if($submit) {
				captcha($captcha);
				if(!$shop) message($L['validate_shop_name']);
				$shop = dhtmlspecialchars($shop);
				(isset($thumb) &&  is_url($thumb) ) or $thumb  = '';
				(isset($thumb1) && is_url($thumb1)) or $thumb1 = '';
				(isset($thumb2) && is_url($thumb2)) or $thumb2 = '';
				if($MOD['vshop_v1'] == 2 && $MOD['vshop_v1_name'] && !$thumb ) message($L['validate_upload'].$MOD['vshop_v1_name']);
				if($MOD['vshop_v2'] == 2 && $MOD['vshop_v2_name'] && !$thumb1) message($L['validate_upload'].$MOD['vshop_v2_name']);
				if($MOD['vshop_v3'] == 2 && $MOD['vshop_v3_name'] && !$thumb2) message($L['validate_upload'].$MOD['vshop_v3_name']);
				$shop = dhtmlspecialchars($shop);
				$history = $user['shop'] ? $user['shop'] : $shop;
				$totime = (isset($totime) && is_time($totime)) ? datetotime($totime) : 0;
				$totime1 = (isset($totime1) && is_time($totime1)) ? datetotime($totime1) : 0;
				$totime2 = (isset($totime2) && is_time($totime2)) ? datetotime($totime2) : 0;
				$db->query("INSERT INTO {$DT_PRE}validate (title,history,type,username,ip,addtime,status,editor,edittime,thumb,thumb1,thumb2,totime,totime1,totime2) VALUES ('$shop','$history','$action','$username','$DT_IP','$DT_TIME','2','system','$DT_TIME','$thumb','$thumb1','$thumb2','$totime','$totime1','$totime2')");
				clear_upload($thumb.$thumb1.$thumb2, $db->insert_id(), 'validate');
				dmsg($L['validate_shop_success'], '?action='.$action);
			}
		}
		$head_title = $L['validate_shop_title'];
	break;
	case 'bank':
		$BANKS = explode('|', trim($MOD['cash_banks']));
		if($status == 1) {
			if($submit) {
				captcha($captcha);
				if(!in_array($bank, $BANKS)) message($L['validate_bank_name']);
				if(strlen($branch) < 9) message($L['validate_bank_branch']);
				if(!preg_match("/^[0-9a-z]{8,20}$/i", $account) && !is_email($account)) message($L['validate_bank_account']);
				(isset($thumb) &&  is_url($thumb) ) or $thumb  = '';
				(isset($thumb1) && is_url($thumb1)) or $thumb1 = '';
				(isset($thumb2) && is_url($thumb2)) or $thumb2 = '';
				if($MOD['vbank_v1'] == 2 && $MOD['vbank_v1_name'] && !$thumb ) message($L['validate_upload'].$MOD['vbank_v1_name']);
				if($MOD['vbank_v2'] == 2 && $MOD['vbank_v2_name'] && !$thumb1) message($L['validate_upload'].$MOD['vbank_v2_name']);
				if($MOD['vbank_v3'] == 2 && $MOD['vbank_v3_name'] && !$thumb2) message($L['validate_upload'].$MOD['vbank_v3_name']);
				$branch = dhtmlspecialchars($branch);
				$history = $user['bank'] ? $user['bank'] : $bank;
				$totime = (isset($totime) && is_time($totime)) ? datetotime($totime) : 0;
				$totime1 = (isset($totime1) && is_time($totime1)) ? datetotime($totime1) : 0;
				$totime2 = (isset($totime2) && is_time($totime2)) ? datetotime($totime2) : 0;
				$db->query("INSERT INTO {$DT_PRE}validate (title,title1,title2,history,type,username,ip,addtime,status,editor,edittime,thumb,thumb1,thumb2,totime,totime1,totime2) VALUES ('$bank','$account','$branch','$history','$action','$username','$DT_IP','$DT_TIME','2','system','$DT_TIME','$thumb','$thumb1','$thumb2','$totime','$totime1','$totime2')");
				clear_upload($thumb.$thumb1.$thumb2, $db->insert_id(), 'validate');
				dmsg($L['validate_shop_success'], '?action='.$action);
			}
		}
		$head_title = $L['validate_bank_title'];
	break;
	default:
		function vcheck($action) {
			global $username;
			$V = DB::get_one("SELECT * FROM ".DT_PRE."validate WHERE type='$action' AND username='$username' ORDER BY itemid DESC");
			return $V && $V['status'] == 2 ? 1 : 0;
		}
		update_validate($user, $MG);
		extract($user);
		$head_title = $L['validate_title'];
	break;
}
if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('validate', $module);
?>