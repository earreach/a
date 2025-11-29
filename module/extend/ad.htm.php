<?php 
defined('IN_DESTOON') or exit('Access Denied');
if(!$aid) return false;
$a or $a = $db->get_one("SELECT * FROM {$DT_PRE}ad WHERE aid=$aid");
$p = $db->get_one("SELECT * FROM {$DT_PRE}ad_place WHERE pid=$a[pid]");
if(!$p || !$a) return false;
$DT_PC = $GLOBALS['DT_PC'] = 1;
$ad_moduleid = $p['moduleid']; 
$pid = $p['pid'];
$typeid = $p['typeid'];
$width = $p['width'];
$height = $p['height'];
$sign = $p['sign'];
$areaid = intval($a['areaid']);
$fileroot = DT_CACHE.'/htm/';
$filename = $fileroot.ad_name($a);
$template = $p['template'] ? $p['template'] : 'ad';
if($p['code']) {
	$default = $typeid > 5 ? 'ad_'.$ad_moduleid.'_d'.$typeid.'.htm' : 'ad_'.$pid.'_d0.htm';
	file_put($fileroot.$default, '<!--'.($DT_TIME+86400*365*10).'-->'.$p['code']);
	if($EXT['mobile_enable']) {
		$default = substr($default, 0, -4).'_m.htm';
		file_put($fileroot.$default, '<!--'.($DT_TIME+86400*365*10).'-->'.$p['code']);
	}
}
if($typeid == 7) {
	$totime = 0;
	$code = '';
	$ad = $db->query("SELECT * FROM {$DT_PRE}ad WHERE pid=$p[pid] AND status=3 AND key_moduleid=$a[key_moduleid] AND key_catid=$a[key_catid] AND key_word='$a[key_word]' AND fromtime<$DT_TIME AND totime>$DT_TIME AND areaid=$areaid ORDER BY listorder ASC,addtime ASC");
	while($t = $db->fetch_array($ad)) {
		if($t['totime'] > $totime) $totime = $t['totime'];
		$code .= $t['code'];
	}
	if($code) {
		file_put($filename, '<!--'.$totime.'-->'.$code.'<div class="b10">&nbsp;</div>');
	} else {
		file_del($filename);
	}
	if($EXT['mobile_enable']) {
		$filename = substr($filename, 0, -4).'_m.htm';
		if($code) {
			file_put($filename, '<!--'.$totime.'-->'.$code.'<div class="b10">&nbsp;</div>');
		} else {
			file_del($filename);
		}
	}
} else if($typeid == 6) {
	$totime = 0;
	$bmid = $moduleid;
	$moduleid = $ad_moduleid;
	$tags = array();
	$ad_module = $MODULE[$ad_moduleid]['module'];
	$showpage = 0;
	$id = $ad_moduleid == 4 ? 'userid' : 'itemid';
	$pages = '';
	$datetype = 5;
	$ad = $db->query("SELECT * FROM {$DT_PRE}ad WHERE pid=$p[pid] AND status=3 AND key_moduleid=$a[key_moduleid] AND key_catid=$a[key_catid] AND key_word='$a[key_word]' AND fromtime<$DT_TIME AND totime>$DT_TIME AND areaid=$areaid ORDER BY listorder ASC,addtime ASC");
	while($t = $db->fetch_array($ad)) {
		if(!isset($MODULE[$moduleid])) continue;
		if($t['totime'] > $totime) $totime = $t['totime'];
		$d = $db->get_one("SELECT * FROM ".get_table($ad_moduleid)." WHERE `{$id}`=$t[key_id]");
		if($d) {
			if($t['stats']) {
				$d['linkurl'] = gourl('?aid='.$t['aid']);
			} else {
				if(strpos($d['linkurl'], '://') === false) $d['linkurl'] = $MODULE[$ad_moduleid]['linkurl'].$d['linkurl'];
			}
			if($moduleid == 4) {
				$d['alt'] = $d['company'];
				if($sign) $d['company'] = '<s class="adname">'.$L['ad_sign'].'</s>'.$d['company'];
			} else {
				$d['alt'] = $d['title'];
				$d['title'] = set_style($d['title'], $d['style']);
				if($sign) $d['title'] = '<s class="adname">'.$L['ad_sign'].'</s>'.$d['title'];
			}
			$tags[] = $d;
		}
	}
	$sign = 0;
	if($tags) {
		ob_start();
		include template($template, 'chip');
		$data = ob_get_contents();
		ob_clean();
		file_put($filename, '<!--'.$totime.'-->'.$data);
	} else {
		file_del($filename);
	}
	if($EXT['mobile_enable']) {
		$filename = substr($filename, 0, -4).'_m.htm';
		include DT_ROOT.'/include/mobile.htm.php';
		if($tags) {
			foreach($tags as $i=>$t) {
				$tags[$i]['linkurl'] = moburl($t['linkurl'], $ad_moduleid);
			}
			ob_start();
			include template($template, 'chip');
			$data = ob_get_contents();
			ob_clean();
			file_put($filename, '<!--'.$totime.'-->'.$data);
		} else {
			file_del($filename);
		}
	}
	$moduleid = $bmid;
} else if($typeid == 5) {
	$totime = 0;
	$tags = array();
	$ad = $db->query("SELECT * FROM {$DT_PRE}ad WHERE pid=$p[pid] AND status=3 AND fromtime<$DT_TIME AND totime>$DT_TIME AND areaid=$areaid ORDER BY listorder ASC,addtime ASC");
	while($t = $db->fetch_array($ad)) {
		if(strpos($t['image_src'], '://') === false) $t['image_src'] = DT_PATH.$t['image_src'];
		$t['alt'] = $t['image_alt'];
		$t['thumb'] = $t['image_src'];
		$t['linkurl'] = $t['stats'] ? gourl('?aid='.$t['aid']) : $t['url'];
		if($t['totime'] > $totime) $totime = $t['totime'];
		$tags[] = $t;
	}
	if($tags) {
		ob_start();
		include template($template, 'chip');
		$data = ob_get_contents();
		ob_clean();
		file_put($filename, '<!--'.$totime.'-->'.$data);
	} else {
		file_del($filename);
	}
	if($EXT['mobile_enable']) {
		$filename = substr($filename, 0, -4).'_m.htm';
		include DT_ROOT.'/include/mobile.htm.php';
		if($tags) {
			ob_start();
			include template($template, 'chip');
			$data = ob_get_contents();
			ob_clean();
			file_put($filename, '<!--'.$totime.'-->'.$data);
		} else {
			file_del($filename);
		}
	}
} else {
	$ad = $db->get_one("SELECT * FROM {$DT_PRE}ad WHERE pid=$p[pid] AND status=3 AND fromtime<$DT_TIME AND totime>$DT_TIME AND areaid=$areaid ORDER BY fromtime DESC");
	if($ad) {
		extract($ad);
		if($url && $stats) $url = gourl('?aid='.$aid);
		if($typeid == 2) {
			$text_name = set_style($text_name, $text_style);
		} else if($typeid == 3) {
			if(strtolower(file_ext($image_src)) == 'mp4') {
				$typeid = 4;
				$video_src = $image_src;
			}
		} else if($typeid == 4) {
			if(in_array(strtolower(file_ext($video_src)), array('jpg', 'jpeg', 'png', 'gif', 'bmp'))) {
				$typeid = 3;
				$image_src = $video_src;
			}
		}
		ob_start();
		include template($template, 'chip');
		$data = ob_get_contents();
		ob_clean();
		file_put($filename, '<!--'.$totime.'-->'.$data);
		if($typeid > 1) {
			$data = 'document.write(\''.dwrite($data).'\');';
			file_put(DT_ROOT.'/file/script/A'.$p['pid'].'.js', $data);
		}
		if($EXT['mobile_enable']) {
			$filename = substr($filename, 0, -4).'_m.htm';
			include DT_ROOT.'/include/mobile.htm.php';
			ob_start();
			include template($template, 'chip');
			$data = ob_get_contents();
			ob_clean();
			file_put($filename, '<!--'.$totime.'-->'.$data);
		}
	} else {
		file_del($filename);
		if($typeid > 1) {
			if($p['code']) {
				file_put(DT_ROOT.'/file/script/A'.$p['pid'].'.js', $p['code']);
			} else {
				file_del(DT_ROOT.'/file/script/A'.$p['pid'].'.js');
			}
		}
		if($EXT['mobile_enable']) {
			$filename = substr($filename, 0, -4).'_m.htm';
			include DT_ROOT.'/include/mobile.htm.php';
			file_del($filename);
		}
	}
}
return true;
?>