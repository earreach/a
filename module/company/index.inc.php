<?php
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$username = $domain = '';
if(isset($homepage) && check_name($homepage)) {
	$username = $homepage;
} else {
	$host = get_env('host');
	if(substr($host, 0, 4) == 'www.') {
		$whost = $host;
		$host = substr($host, 4);
	} else {
		$whost = $host;
	}
	if($host && strpos($MODULE[4]['linkurl'], $host) === false && strpos(DT_MOB, $host) === false) {
		if(substr($host, -strlen($CFG['com_domain'])) == $CFG['com_domain']) {
			$www = substr($host, 0, -strlen($CFG['com_domain']));
			if(check_name($www)) {
				$username = $homepage = $www;
			} else {
				$head_title = $L['not_company'];
				if($DT_BOT) dhttp(404, $DT_BOT);
				include template('com-notfound', 'message');
				exit;
			}
		} else {
			if($whost == $host) {//301 xxx.com to www.xxx.com
				$w3 = 'www.'.$host;
				$c = $db->get_one("SELECT userid FROM {$DT_PRE}company WHERE domain='$w3'");
				if($c) d301('http://'.$w3);
			}
			$c = $db->get_one("SELECT username,domain FROM {$DT_PRE}company WHERE domain='$whost'".($host == $whost ? '' : " OR domain='$host'"), 'CACHE');
			if($c) {
				$username = $homepage = $c['username'];
				$domain = $c['domain'];
			}
		}
	}
}
if($username) {
	include DT_ROOT.'/module/'.$module.'/init.inc.php';
} else {
	if($DT_PC) {
		if($DT['safe_domain']) {
			$pass_domain = false;
			foreach(explode('|', $DT['safe_domain']) as $v) {
				if(strpos($DT_URL, $v) !== false) { $pass_domain = true; break; }
			}
			$pass_domain or dhttp(404);
		}
		if(!check_group($_groupid, $MOD['group_index'])) include load('403.inc');
		$condition = "groupid IN (".($MOD['gids'] ? $MOD['gids'] : get_gids()).")";
		if($MOD['index_html']) {	
			$html_file = DT_CACHE.'/htm/company.htm';
			if(!is_file($html_file)) tohtml('index', $module);
			if(is_file($html_file)) exit(include($html_file));
		}
		if($page == 1) $head_canonical = $MOD['linkurl'];
		if($EXT['mobile_enable']) $head_mobile = $MOD['mobile'];
		$destoon_task = "moduleid=$moduleid&html=index";
		$CSS = array('catalog');
	} else {
		$condition = "groupid IN (".($MOD['gids'] ? $MOD['gids'] : get_gids()).")";
		if($cityid) {
			$areaid = $cityid;
			$ARE = get_area($areaid);
			$condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		}
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$table} WHERE {$condition}", 'CACHE');
		$items = $r['num'];
		$pages = mobile_pages($items, $page, $pagesize);
		$tags = array();
		if($items) {
			$result = $db->query("SELECT ".$MOD['fields']." FROM {$table} WHERE {$condition} ORDER BY ".$MOD['order']." LIMIT {$offset},{$pagesize}", ($CFG['db_expires'] && $page <= $DT['cache_page']) ? 'CACHE' : '', $CFG['db_expires']);
			while($r = $db->fetch_array($result)) {
				$tags[] = $r;
			}
			$db->free_result($result);
			$js_load = $MOD['mobile'].'search'.DT_EXT.'?job=ajax';
		}
		$head_title = $head_name = $MOD['name'];
	}
	$seo_file = 'index';
	include DT_ROOT.'/include/seo.inc.php';
	include template($MOD['template_index'] ? $MOD['template_index'] : 'index', $module);
}
?>