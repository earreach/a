<?php
defined('DT_ADMIN') or exit('Access Denied');
$tab = isset($tab) ? intval($tab) : 0;
$all = isset($all) ? intval($all) : 0;
if($submit) {
	foreach($pay as $k=>$v) {
		$pay[$k] = array_map('trim', $v);
	}
	foreach($oauth as $k=>$v) {
		$oauth[$k] = array_map('trim', $v);
	}
	if($setting['minpassword'] < 6) $setting['minpassword'] = 6;
	if($setting['maxpassword'] > 30) $setting['maxpassword'] = 30;
	if($setting['deposit'] < 100) $setting['deposit'] = 100;
	$DT['sms'] or $setting['login_sms'] = 0;
	($DT['sms'] && $setting['login_sms']) or $setting['verify_login'] = 0;
	$P = cache_read('pay.php');
	$pay['weixin']['keycode'] = pass_decode($pay['weixin']['keycode'], $P['weixin']['keycode']);
	$pay['alipay']['keycode'] = pass_decode($pay['alipay']['keycode'], $P['alipay']['keycode']);
	$pay['alipay']['public'] = pass_decode($pay['alipay']['public'], $P['alipay']['public']);
	$pay['aliwap']['keycode'] = pass_decode($pay['aliwap']['keycode'], $P['aliwap']['keycode']);
	$pay['aliwap']['public'] = pass_decode($pay['aliwap']['public'], $P['aliwap']['public']);
	$pay['chinabank']['keycode'] = pass_decode($pay['chinabank']['keycode'], $P['chinabank']['keycode']);
	$pay['yeepay']['keycode'] = pass_decode($pay['yeepay']['keycode'], $P['yeepay']['keycode']);
	$pay['paypal']['keycode'] = pass_decode($pay['paypal']['keycode'], $P['paypal']['keycode']);
	$setting['uc_dbpwd'] = pass_decode($setting['uc_dbpwd'], $MOD['uc_dbpwd']);
	$setting['ex_pass'] = pass_decode($setting['ex_pass'], $MOD['ex_pass']);
	$setting['edit_check'] = (isset($setting['edit_check']) && is_array($setting['edit_check'])) ? implode(',', $setting['edit_check']) : '';
	$setting['login_time'] = $setting['login_time'] >= 86400 ? $setting['login_time'] : 0;
	DB::query("DELETE FROM ".DT_PRE."setting WHERE item LIKE 'pay-%'");
	foreach($pay as $k=>$v) {
		update_setting('pay-'.$k, strip_sql($v, 0));
	}
	DB::query("DELETE FROM ".DT_PRE."setting WHERE item LIKE 'oauth-%'");
	$setting['oauth'] = 0;
	$WX = cache_read('weixin.php');
	if($WX['appid'] && $WX['appsecret']) $oauth['weixin'] = array ('name' => '公众号','order' => $oauth['wechat']['order'],'enable' => '0');
	if($WX['wxmini_appid'] && $WX['wxmini_appsecret']) $oauth['wxmini'] = array ('name' => '小程序','order' => $oauth['wechat']['order'],'enable' => '0');
	foreach($oauth as $k=>$v) {
		if($v['enable']) $setting['oauth'] = 1;
		update_setting('oauth-'.$k, $v);
	}
	update_setting($moduleid, $setting);
	cache_module($moduleid);
	$ext_oauth = $setting['oauth'];
	if($oauth['sina']['enable'] && $oauth['sina']['sync']) $ext_oauth .= ',sina';
	if(isset($MODULE[20])) $ext_oauth .= ',moment';
	$db->query("UPDATE {$DT_PRE}setting SET item_value='$ext_oauth' WHERE item_key='oauth' AND item='3'");
	cache_module(3);
	dmsg('设置保存成功', '?moduleid='.$moduleid.'&file='.$file.'&tab='.$tab);
} else {
	$GROUP = cache_read('group.php');
	extract(dhtmlspecialchars($MOD));
	cache_oauth();	
	$O = cache_read('oauth.php');
	if(isset($O['weixin'])) unset($O['weixin']);
	if(isset($O['wxmini'])) unset($O['wxmini']);
	extract($O);
	cache_pay();
	extract(cache_read('pay.php'));
	$weixin['keycode'] = pass_encode($weixin['keycode']);
	$alipay['keycode'] = pass_encode($alipay['keycode']);
	$alipay['public'] = pass_encode($alipay['public']);
	$aliwap['keycode'] = pass_encode($aliwap['keycode']);
	$aliwap['public'] = pass_encode($aliwap['public']);
	$chinabank['keycode'] = pass_encode($chinabank['keycode']);
	$yeepay['keycode'] = pass_encode($yeepay['keycode']);
	$paypal['keycode'] = pass_encode($paypal['keycode']);
	$uc_dbpwd = pass_encode($uc_dbpwd);
	$ex_pass = pass_encode($ex_pass);
	if($kw) {
		$all = 1;
		ob_start();
	}
	include tpl('setting', $module);
	if($kw) {
		$data = $content = ob_get_contents();
		ob_clean();
		$data = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$kw.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', '<span class=highlight>'.$kw.'</span>', $data);
		$data = preg_replace('/<span class=highlight>/', '<a name=high></a><span class=highlight>', $data, 1);
		echo $data ? $data : $content;
	}
}
?>