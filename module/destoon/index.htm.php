<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
if($DT['city']) {
	$DT['index_html'] = 0;
	$C = cache_read('module-2.php');
	$M = $C['linkurl'];
} else {
	$M = $MODULE[2]['linkurl'];
}
if(filemtime(DT_ROOT.'/file/script/config.js') <= filemtime(DT_CACHE.'/module.php')) {
	$data = '';
	$data .= 'var DTPath = "'.DT_PATH.'";';
	$data .= 'var DTMob = "'.DT_MOB.'";';
	$data .= 'var DTMobc = "'.($DT['color_mw'] ? $DT['color_mb'] : '').'";';
	$data .= 'var SKPath = "'.DT_SKIN.'";';
	$data .= 'var SKMob = "'.DM_SKIN.'";';
	$data .= 'var MEPath = "'.$M.'";';
	$data .= 'var DTExt = "'.DT_EXT.'";';
	$data .= 'var DTEditor = "'.DT_EDITOR.'";';
	$data .= 'var CKDomain = "'.$CFG['cookie_domain'].'";';
	$data .= 'var CKPath = "'.$CFG['cookie_path'].'";';
	$data .= 'var CKPrex = "'.$CFG['cookie_pre'].'";';
	$data .= 'if(window.console){console.clear();console.log("%cPowered By DESTOON%chttps://www.destoon.com/", "color:#FFFFFF;font-size:14px;background:#FF7418;padding:2px 12px;border-radius:10px;", "font-size:14px;padding:2px 12px;");}';
	file_put(DT_ROOT.'/file/script/config.js', $data);
	$css = cache_read('css.php');
	foreach(array('home', 'mobile', 'member', 'admin') as $v) {
		ob_start();
		include template('reset-'.$v, 'chip');
		$data = ob_get_contents();
		ob_clean();
		file_put(DT_ROOT.'/file/style/'.$v.'.reset.css', '/* reset '.$v.' */'.trim(str_replace(array('<style>', '</style>', "\r", "\n"), array('', '', '', ''), $data)));
	}
}
$filename = $CFG['com_dir'] ? DT_ROOT.'/'.$DT['index'].'.'.$DT['file_ext'] : DT_CACHE.'/index.inc.html';
if(!$DT['index_html'] || $DT['page_mid']) return html_del($filename);
if(!$db->linked) return false;
$destoon_task = "moduleid=1&html=index";
$AREA = cache_read('area.php');
if($EXT['mobile_enable']) $head_mobile = $EXT['mobile_url'];
$index = 1;
$seo_title = $DT['seo_title'];
$head_keywords = $DT['seo_keywords'];
$head_description = $DT['seo_description'];
$CSS = array('index');
ob_start();
include template('index');
$data = ob_get_contents();
ob_clean();
file_put($filename, $data);
return true;
?>