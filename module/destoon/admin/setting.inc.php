<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
switch($action) {
	case 'ftp':
		require DT_ROOT.'/include/ftp.class.php';
		if(strpos($ftp_pass, '***') !== false) $ftp_pass = $DT['ftp_pass'];
		$ftp = new dftp($ftp_host, $ftp_user, $ftp_pass, $ftp_port, $ftp_path, $ftp_pasv, $ftp_ssl);
		if(!$ftp->connected) dialog('FTP无法连接，请检查设置');
		if(!$ftp->dftp_chdir()) dialog('FTP无法进入远程存储目录，请检查远程存储目录');
		dialog('FTP设置正常,可以使用');
	break;
	case 'mail':
		define('TESTMAIL', true);
		if(strpos($smtp_pass, '***') !== false) $smtp_pass = $DT['smtp_pass'];
		$DT['mail_type'] = $mail_type;
		$DT['smtp_host'] = $smtp_host;
		$DT['smtp_port'] = $smtp_port;
		$DT['smtp_auth'] = $smtp_auth;
		$DT['smtp_user'] = $smtp_user;
		$DT['smtp_pass'] = $smtp_pass;
		$DT['mail_sender'] = $mail_sender;
		$DT['mail_name'] = $mail_name;
		$DT['mail_delimiter'] = $mail_delimiter;
		$DT['mail_sign'] = '';
		if($mail_type == 'sc') {
			$subject = '来自SendCloud的第一封邮件！';
			$body = '你太棒了！你已成功的从SendCloud发送了一封测试邮件，接下来快登录前台去完善账户信息吧！';
		} else {
			$subject = $DT['sitename'].'邮件发送测试';
			$body = '<b>恭喜！您的站点['.$DT['sitename'].']邮件发送设置成功！</b><br/>------------------------------------<br><a href="https://www.destoon.com/" target="_blank">Send By DESTOON Mail Tester</a>';
		}	
		if(send_mail($testemail, $subject, $body)) dialog('邮件已发送至'.$testemail.'，请注意查收', $mail_sender);
		dialog('邮件发送失败，请检查设置');
	break;
	case 'static':
		include tpl('static');
	break;
	case 'cache':
		if($job && $job != $CFG['cache']) {
			$class = DT_ROOT.'/include/cache_'.$job.'.class.php';
			if(is_file($class)) {
				cache_write('cache.test.php', str_replace('dcache', 'tcache', file_get($class)));
				require DT_CACHE.'/cache.test.php';
				$dc = new tcache();
				$dc->pre = $CFG['cache_pre'];
				$CFG['cache'] = $job;
			}
		}
		$dc->set('destoon', 'com', 3600);
		$pass = $dc->get('destoon') == 'com' ? 1 : 0;
		dialog('<div style="padding:16px 16px 0 16px;">测试结果：'.($pass ? '<span class="f_green">成功</span>' : '<span class="f_red">失败</span>').'&nbsp; &nbsp;缓存类型：'.$CFG['cache'].'<div style="padding:10px 0;">如果类型不正确，请先保存设置再测试</div></div>');
	break;
	case 'https':
		$pass = strpos(dcurl('https://cloud.destoon.com/connect.php', 'action=test'), 'OK') === false ? 0 : 1;
		dialog('<div style="padding:16px 16px 0 16px;">HTTPS连接测试结果：'.($pass ? '<span class="f_green">成功</span> &nbsp; 建议开启' : '<span class="f_red">失败</span> &nbsp; 切勿开启').'</div>');
	break;
	case 'html':
		tohtml('index');
		$dc->get('destoon') == 'com' or dalert('缓存类型'.$CFG['cache'].'测试失败，'.($CFG['cache'] == 'file' ? '请检查file目录是否可写' : '请立即更换'), '?moduleid='.$moduleid.'&file='.$file.'&tab=2');
		dmsg('设置保存成功', '?moduleid='.$moduleid.'&file='.$file.'&tab='.$tab);
	break;
	default:
		$tab = isset($tab) ? intval($tab) : 0;
		$all = isset($all) ? intval($all) : 0;
		if($submit) {
			foreach($setting as $k=>$v) {
				if(strpos($k, 'seo_') === false) continue;
				seo_check($v) or msg('SEO信息包含非法字符');
			}
			if(strpos($setting['remote_url'], 'file/upload') !== false) msg('FTP远程访问URL不能包含file/upload');
			if(!is_write(DT_ROOT.'/config.inc.php')) msg('根目录config.inc.php无法写入，请设置可写权限');
			if($setting['safe_domain']) {
				$setting['safe_domain'] = str_replace('http://', '', $setting['safe_domain']);
				if(substr($setting['safe_domain'], 0, 4) == 'www.') $setting['safe_domain'] = substr($setting['safe_domain'], 4);
			}
			$setting['gano'] = $setting['wano'] ? cutstr($setting['wano'], '备', '号') : '';
			$setting['smtp_pass'] = pass_decode($setting['smtp_pass'], $DT['smtp_pass']);
			$setting['ftp_pass'] = pass_decode($setting['ftp_pass'], $DT['ftp_pass']);
			$setting['admin_week'] = is_array($setting['admin_week']) ? implode(',', $setting['admin_week']) : '';
			$setting['check_week'] = is_array($setting['check_week']) ? implode(',', $setting['check_week']) : '';
			if($setting['logo'] != $DT['logo']) clear_upload($setting['logo'], $_userid, 'setting');
			$setting['thumb_max'] = intval($setting['thumb_max']);
			if($setting['thumb_max'] > 99 || $setting['thumb_max'] < 5) $setting['thumb_max'] = 10;
			in_array($setting['file_ext'], array('html', 'htm', 'shtml', 'shtm')) or $setting['file_ext'] = 'html';	
			$setting['color_pc'] = strlen($css['home_menu']) == 7 ? $css['home_menu'] : '#0679D4';
			$setting['color_mb'] = strlen($css['mobile_head']) == 7 ? $css['mobile_head'] : '#F7F7F7';
			$setting['color_mw'] = strtoupper($css['mobile_text']) == '#FFFFFF' ? 1 : 0;
			if(substr($config['url'], -1) != '/') $config['url'] = $config['url'].'/';
			if($config['cookie_domain'] && substr($config['cookie_domain'], 0, 1) != '.') $config['cookie_domain'] = '.'.$config['cookie_domain'];
			if($config['cookie_domain'] != $CFG['cookie_domain']) $config['cookie_pre'] = 'D'.random(2).'_';
			if(!is_numeric($config['cloud_uid']) || strlen($config['cloud_key']) != 16) $setting['sms'] = $setting['cloud_express'] = 0;
			$config['cloud_key'] = pass_decode($config['cloud_key'], DT_CLOUD_KEY);
			$setting['biz'] = is_file(DT_ROOT.'/license.php') ? 1 : 0;
			$setting['file_register_bak'] = $setting['file_register'];
			$setting['file_login_bak'] = $setting['file_login'];
			$setting['file_my_bak'] = $setting['file_my'];
			if(DT_EXT != '.php') {
				$setting['file_register'] = str_replace('.php', DT_EXT, $setting['file_register']);
				$setting['file_login'] = str_replace('.php', DT_EXT, $setting['file_login']);
				$setting['file_my'] = str_replace('.php', DT_EXT, $setting['file_my']);
			}
			$tmp = file_get(DT_ROOT.'/config.inc.php');
			foreach($config as $k=>$v) {
				if(in_array($k, array('url', 'language', 'skin', 'skin_mobile', 'template', 'template_mobile', 'editor', 'com_vip', 'com_domain', 'com_dir', 'com_rewrite', 'db_expires', 'cache', 'template_refresh', 'static', 'cdn', 'cookie_domain', 'cloud_uid', 'cloud_key'))) $tmp = preg_replace("/[$]CFG\['$k'\]\s*\=\s*[\"'].*?[\"']/is", "\$CFG['$k'] = '$v'", $tmp);
			}
			file_put(DT_ROOT.'/config.inc.php', $tmp);
			update_setting($moduleid, $setting);
			update_setting('css', array_map('trim', $css));
			cache_module(1);
			cache_module();
			cache_css();
			file_put(DT_ROOT.'/file/avatar/remote.html', $setting['ftp_remote'] && $setting['remote_url'] ? $setting['remote_url'] : 'URL');
			$filename = DT_ROOT.'/'.$setting['index'].'.'.$setting['file_ext'];
			if(!$setting['index_html'] && $setting['file_ext'] != 'php') file_del($filename);
			$pdir = DT_ROOT.'/'.$MODULE[2]['moduledir'].'/';
			$mdir = DT_ROOT.'/mobile/'.$MODULE[2]['moduledir'].'/';
			if($setting['file_register_bak'] != $old_file_register) {
				@rename($pdir.$old_file_register, $pdir.$setting['file_register_bak']);
				@rename($mdir.$old_file_register, $mdir.$setting['file_register_bak']);
			}
			if($setting['file_login_bak'] != $old_file_login) {
				@rename($pdir.$old_file_login, $pdir.$setting['file_login_bak']);
				@rename($mdir.$old_file_login, $mdir.$setting['file_login_bak']);
			}
			if($setting['file_my_bak'] != $old_file_my) {
				@rename($pdir.$old_file_my, $pdir.$setting['file_my_bak']);
				@rename($mdir.$old_file_my, $mdir.$setting['file_my_bak']);
			}
			$dc->set('destoon', 'com', 3600);
			dheader('?moduleid='.$moduleid.'&file='.$file.'&action=html&tab='.$tab);
		} else {
			include DT_ROOT.'/config.inc.php';
			extract(dhtmlspecialchars($CFG));
			extract(dhtmlspecialchars($DT));
			$CSS = cache_read('css.php');
			$W = array('天', '一', '二', '三', '四', '五', '六');
			if($file_register_bak) $file_register = $file_register_bak;
			if($file_login_bak) $file_login = $file_login_bak;
			if($file_my_bak) $file_my = $file_my_bak;
			$smtp_pass = pass_encode($smtp_pass);
			$ftp_pass = pass_encode($ftp_pass);
			$cloud_key = pass_encode($cloud_key);
			$sms_url = base64_decode('aHR0cHM6Ly93d3cuZGVzdG9vbi5jb20vc21zLnBocD9hY3Rpb249Z2V0JnVpZD0=').DT_CLOUD_UID.'&key='.md5(DT_CLOUD_KEY.'|'.DT_CLOUD_UID);
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
	break;
}
?>