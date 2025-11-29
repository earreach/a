<?php 

defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';


if($DT_PC) {
	$username = $domain = '';
	if(isset($homepage) && check_name($homepage)) {
		$username = $homepage;
	} else if(!$cityid) {
		$host = get_env('host');
		if(substr($host, 0, 4) == 'www.') {
			$whost = $host;
			$host = substr($host, 4);
		} else {
			$whost = $host;
		}
		if($host && strpos(DT_PATH, $host) === false) {
			if(substr($host, -strlen($CFG['com_domain'])) == $CFG['com_domain']) {
				$www = substr($host, 0, -strlen($CFG['com_domain']));
				if(check_name($www)) {
					$username = $homepage = $www;
				} else {
					include load('company.lang');
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
		$moduleid = 4;
		$module = 'company';
		$MOD = cache_read('module-'.$moduleid.'.php');
		include load('company.lang');
		require DT_ROOT.'/module/'.$module.'/common.inc.php';
		include DT_ROOT.'/module/'.$module.'/init.inc.php';
	} else {
		if($DT['safe_domain']) {
			$pass_domain = false;
			foreach(explode('|', $DT['safe_domain']) as $v) {
				if(strpos($DT_URL, $v) !== false) { $pass_domain = true; break; }
			}
			$pass_domain or dhttp(404);
		}
		if($DT['page_mid']) {
			if(isset($MODULE[$DT['page_mid']])) {
				$moduleid = $DT['page_mid'];
				$html_file = $moduleid == 4 ? DT_CACHE.'/htm/company.htm' : DT_ROOT.'/'.$MODULE[$moduleid]['moduledir'].'/'.$DT['index'].'.'.$DT['file_ext'];
				if(is_file($html_file)) exit(include($html_file));
				$module = $MODULE[$moduleid]['module'];
				$MOD = cache_read('module-'.$moduleid.'.php');
				include DT_ROOT.'/module/'.$module.'/index.inc.php';
				exit;
			}
		}
		if($DT['index_html']) {
			$html_file = $CFG['com_dir'] ? DT_ROOT.'/'.$DT['index'].'.'.$DT['file_ext'] : DT_CACHE.'/index.inc.html';
			if(!is_file($html_file)) tohtml('index');		
			if(is_file($html_file)) exit(include($html_file));
		}
		$AREA or $AREA = cache_read('area.php');
		if($EXT['mobile_enable']) $head_mobile = DT_MOB;
		$index = 1;
		$seo_title = $DT['seo_title'];
		$head_keywords = $DT['seo_keywords'];
		$head_description = $DT['seo_description'];
		$CSS = array('index');
		if($city_template) {
			include template($city_template, 'city');
		} else {		
			include template('index');
		}
	}
} else {



	 // 手机端部分
	if($DT['page_mid']) {
		if(isset($MODULE[$DT['page_mid']])) {
			$moduleid = $DT['page_mid'];
			$html_file = $moduleid == 4 ? DT_CACHE.'/htm/company.mob.htm' : DT_ROOT.'/mobile/'.$MODULE[$moduleid]['moduledir'].'/'.$DT['index'].'.'.$DT['file_ext'];
			if(is_file($html_file)) exit(include($html_file));
			$module = $MODULE[$moduleid]['module'];
			$MOD = cache_read('module-'.$moduleid.'.php');
			include DT_ROOT.'/module/'.$module.'/index.inc.php';
			exit;
		}
	}



// check_referer() or dheader($MOD['linkurl']);


  // 获取down模块中指定ID（1,2,3,4）的下载内容
    $target_downloads = array();
    // down模块的模块ID（需要您确认，通常是模块管理中的ID）
    $down_moduleid = 15; // 根据表名dt_down_data_15，模块ID可能是15
    
    // 获取指定itemid为1,2,3,4的内容
    $item_ids = array(1, 2, 3, 4);
    $itemid_sql = implode(',', $item_ids);
    
    // 从down数据表获取内容
    $result = $db->query("SELECT itemid, title,filesize, status,fee,fileurl,introduce, download, addtime 
                         FROM {$DT_PRE}down_15 
                         WHERE itemid IN ($itemid_sql) AND status=3 
                         ORDER BY FIELD(itemid, $itemid_sql)");

    while($r = $db->fetch_array($result)) {
        // 使用down模块的正确URL格式
        $r['download_url'] = $MODULE[$down_moduleid]['mobile'].'down.php?itemid='.$r['itemid'];
        // var_dump( $r['download_url']);die();
        $target_downloads[] = $r;
    }
// var_dump($target_downloads['fileurl']);die();


// while($r = $db->fetch_array($result)) {
//     // 调试输出
//     echo "文件ID: {$r['itemid']}, 状态: {$r['status']},费用: {$r['fee']}<br>";
    
//     $r['download_url'] = $MODULE[15]['linkurl'].'down.php?itemid='.$r['itemid'];
//     $target_downloads[] = $r;
// }

// 如果什么都没输出，说明查询有问题
// if(empty($target_downloads)) {
//     echo "查询失败或文件不存在！";
// }

	$ads = array();
	$pid = intval($EXT['mobile_pid']);
	if($pid > 0) {
		$condition = "pid=$pid AND status=3 AND totime>$DT_TIME";
		if($cityid) {
			$areaid = $cityid;
			$ARE = get_area($areaid);
			$condition .= $ARE['child'] ? " AND areaid IN (".$ARE['arrchildid'].")" : " AND areaid=$areaid";
		}
		$result = $db->query("SELECT * FROM {$DT_PRE}ad WHERE {$condition} ORDER BY listorder ASC,addtime ASC LIMIT 10", 'CACHE');
		while($r = $db->fetch_array($result)) {
			$r['image_src'] = linkurl($r['image_src']);
			$r['url'] = $r['stats'] ? gourl('?aid='.$r['aid']) : linkurl($r['url']);
			$ads[] = $r;
		}
	}
	$MOD_MY = array();
	$data = '';
	$local = get_cookie('mobile_setting');
	if($local) {
		$data = $local;
	} else if($_userid) {
		$data = file_get(DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/mobile-setting.php');
		if($data) {
			$data = substr($data, 13);
			set_cookie('mobile_setting', $data, $DT_TIME + 30*86400);
		}
	}
	if($data) {
		$MOB_MOD = array();
		foreach($MOB_MODULE as $m) {
			$MOB_MOD[$m['moduleid']] = $m;
		}
		foreach(explode(',', $data) as $id) {
			if(isset($MOB_MOD[$id])) $MOD_MY[] = $MOB_MOD[$id];
		}
	}
	if(count($MOD_MY) < 2) $MOD_MY = $MOB_MODULE;

 


	$head_title = $head_name = $site_name;;
	$seo_title = $sns_app ? $site_name : $DT['seo_title'];
	$head_keywords = $DT['seo_keywords'];
	$head_description = $DT['seo_description'];
	$foot = 'home';
	include template('index');
}
?>