<?php
defined('IN_DESTOON') or exit('Access Denied');
if(!IN_ADMIN) {
	require_once DT_ROOT.'/include/client.func.php';
	$robot = is_robot() ? get_robot() : '';
	$os = get_os();
	$pc = is_mob() ? 0 : 1;
	$pt = get_env('port');
	$uk = get_cookie('uk');
	is_numeric($uk) or $uk = '';
	if($uk) {
		$fp = $uk;
	} else {
		$fp = mt_rand(100000, 999999);
		set_cookie('uk', $fp, $DT_TODAY);
	}
	$hp = '';
	if(isset($homepage) && check_name($homepage)) {
		$hp = $homepage;
	} else if($itemid && $moduleid > 4) {
		if(isset($username) && check_name($username)) $hp = $username;
	}
	$url = addslashes(dhtmlspecialchars(str_replace(DT_PATH, '', $DT_URL)));
	$refer = is_url($DT_REF) ? addslashes(dhtmlspecialchars($DT_REF == DT_PATH ? '/' : str_replace(DT_PATH, '', $DT_REF))) : '';
	$domain = is_uri($DT_REF) ? addslashes(dhtmlspecialchars(cutstr($DT_REF, '://', '/'))) : '';
	if(is_uri($DT_URL) && strpos($DT_URL, 'task'.DT_EXT) === false && strpos($DT_URL, 'stats'.DT_EXT) === false) $db->query("INSERT INTO {$DT_PRE}stats_pv (mid,catid,itemid,url,refer,domain,homepage,username,ip,uk,port,robot,pc,addtime) VALUES ('".($mid ? $mid : $moduleid)."','$catid','$itemid','$url','$refer','$domain','$hp','$_username','$DT_IP','$fp','$pt','$robot','$pc','$DT_TIME')");
	$id = md5(DT_IP.$DT_TODAY.DT_UA);
	$ua = addslashes(dhtmlspecialchars(strip_sql(strip_tags(DT_UA))));
	$uv = get_cookie('uv');
	if($uv == $id) {
		//
	} else {
		$uv = $dc->get('uv-'.$id);
		$in = 0;
		if($uv == $id) {
			if($uk) {
				$t = $db->get_one("SELECT ip FROM {$DT_PRE}stats_uv WHERE uk='$uk' AND ip='$DT_IP'");
				if(!$t) $in = 1;
			}
		} else {
			$dc->set('uv-'.$id, $id, $DT_TODAY - $DT_TIME);
			 $in = 1;
		}
		if($in) {
			$area = ip2area(DT_IP);
			$country = area_parse($area, 'country');
			$province = area_parse($area, 'province');
			$city = area_parse($area, 'city');
			$network = area_parse($area, 'network');
			$bs = get_bs();
			$bd = get_bd();
			$db->query("INSERT INTO {$DT_PRE}stats_uv (ip,uk,port,robot,area,country,province,city,network,screen,pc,ua,os,bs,bd,addtime) VALUES ('$DT_IP','$fp','$pt','$robot','$area','$country','$province','$city','$network','','$pc','$ua','$os','$bs','$bd','$DT_TIME')");
			set_cookie('uv', $id, $DT_TODAY);
			echo 'Dstats();';
		}
	}
	if(isset($screenw) && isset($screenh)) {
		$screenw = intval($screenw);
		$screenh = intval($screenh);
		if($screenw > 100 && $screenh > 100) {
			$swh = $dc->get('sn-'.$id.$uk);
			if($swh == $screenw.'*'.$screenh) {
				//
			} else {
				$dc->set('sn-'.$id.$uk, $screenw.'*'.$screenh, $DT_TODAY - $DT_TIME);
				$db->query("INSERT INTO {$DT_PRE}stats_screen (ip,uk,width,height,ua,url,refer,addtime) VALUES ('$DT_IP','$uk','$screenw','$screenh','$ua','$url','$refer','$DT_TIME')");
			}
		}
	}
}
?>