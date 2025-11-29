<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$ext = 'form';
$MOD[$ext.'_enable'] or message($L['closed'], $DT_PC ? DT_PATH : DT_MOB, 6);
$url = $EXT[$ext.'_url'];
$mob = $EXT[$ext.'_mob'];
$TYPE = get_type($ext, 1);
$_TP = sort_type($TYPE);
require DT_ROOT.'/module/'.$module.'/'.$ext.'.class.php';
$do = new $ext();
$typeid = isset($typeid) ? intval($typeid) : 0;

if($action == 'result') {
	$code = isset($code) ? intval($code) : 0;
	$uri = ($DT_PC ? $url : $mob).show_url($itemid);
	$head_title = $L['form_title'];
	$template = $ext;
} else {
	if($itemid) {
		$do->itemid = $itemid;
		$f = $do->get_one();
		$f or dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=10');
		unset($f['answer']);
		require DT_ROOT.'/include/content.class.php';
		extract($f);
		(isset($item) && preg_match("/^[a-z0-9_\-]{1,}$/i", $item)) or $item = '';
		$could_form = true;
		$error = 0;
		if($maxanswer) {
			$condition = $_username ? "AND username='$_username'" : "AND ip='$DT_IP'";
			$num = $db->count($DT_PRE.'form_record', "fid=$itemid $condition");
			if($num >= $maxanswer) dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=11');
		}
		if($fromtime && $DT_TIME < $fromtime) dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=12');
		if($totime && $DT_TIME > $totime) dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=13');
		if(!check_group($_groupid, $groupid)) {
			if(!$_userid && $groupid && strpos(','.$groupid.',', ',3,') === false) dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=14');
			dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=15');
		}
		if($submit) {
			if($verify == 1) captcha($captcha, 1);
			if($verify == 2) question($answer, 1);
			if($could_form) {
				$post = $other = array();
				$uploads = '';
				$result = $db->query("SELECT * FROM {$DT_PRE}form_question WHERE fid=$itemid ORDER BY listorder ASC,qid ASC LIMIT 100");
				while($r = $db->fetch_array($result)) {
					$qid = $r['qid'];
					$t = explode('-', $r['required']);
					$r['min'] = isset($t[0]) ? intval($t[0]) : 0;
					$r['max'] = isset($t[1]) ? intval($t[1]) : 0;
					if($r['min'] && $r['max'] <= $r['min']) $r['max'] = 0;
					$r['option'] = array();
					if($r['type'] == 'text' || $r['type'] == 'textarea') {
						if(isset($a[$qid])) {
							if($r['min'] && strlen($a[$qid]) < $r['min']) message(lang($L['form_min_word'], array($r['name'], $r['min'])));
							if($r['max'] && strlen($a[$qid]) > $r['max']) message(lang($L['form_max_word'], array($r['name'], $r['max'])));
							$post[$qid] = dhtmlspecialchars(trim($a[$qid]));
						} else {
							message(lang($L['form_input'], array($r['name'])));
						}
					} else if($r['type'] == 'select') {
						if(isset($a[$qid])) {
							if($r['min'] && strlen($a[$qid]) == 0) message(lang($L['form_choose'], array($r['name'])));
							$post[$qid] = dhtmlspecialchars(trim($a[$qid]));
						} else {
							message(lang($L['form_choose'], array($r['name'])));
						}
					} else if($r['type'] == 'checkbox') {
						if(isset($a[$qid])) {
							if($r['min'] && count($a[$qid]) < $r['min']) message(lang($L['form_min_choose'], array($r['name'], $r['min'])));
							if($r['max'] && count($a[$qid]) > $r['max']) message(lang($L['form_max_choose'], array($r['name'], $r['max'])));
							$str = ',';
							$val = '|'.str_replace('(*)', '', $r['value']);
							foreach($a[$qid] as $s) {
								if(strpos($val, '|'.$s) === false) message(lang($L['form_choose'], array($r['name'])));
								$str .= $s.',';
								if($s == $L['form_other'] && isset($o[$qid])) $other[$qid] = dhtmlspecialchars(trim($o[$qid]));
							}
							$post[$qid] = dhtmlspecialchars(trim($str));
						} else {
							if($r['min'] == 0) {
								$post[$qid] = '';
							} else {
								message(lang($L['form_choose'], array($r['name'])));
							}
						}
					} else if($r['type'] == 'radio') {
						if(isset($a[$qid])) {
							if($r['min'] && strlen($a[$qid]) == 0) message(lang($L['form_choose'], array($r['name'])));
							$val = '|'.str_replace('(*)', '', $r['value']);
							if(strpos($val, '|'.$a[$qid]) === false) message(lang($L['form_choose'], array($r['name'])));
							if($a[$qid] == $L['form_other'] && isset($o[$qid])) $other[$qid] = dhtmlspecialchars(trim($o[$qid]));
							$post[$qid] = dhtmlspecialchars(trim($a[$qid]));
						} else {
							if($r['min'] == 0) {
								$post[$qid] = '';
							} else {
								message(lang($L['form_choose'], array($r['name'])));
							}
						}				
					} else if($r['type'] == 'date') {
						if(isset($a[$qid])) {
							if($r['min'] && !is_date($a[$qid])) message(lang($L['form_choose'], array($r['name'])));
							$post[$qid] = is_date($a[$qid]) ? $a[$qid] : '';
						} else {
							message(lang($L['form_choose'], array($r['name'])));
						}
					} else if($r['type'] == 'time') {
						if(isset($a[$qid])) {
							if($r['min'] && !is_time($a[$qid])) message(lang($L['form_choose'], array($r['name'])));
							$post[$qid] = is_time($a[$qid]) ? $a[$qid] : '';
						} else {
							message(lang($L['form_choose'], array($r['name'])));
						}
					} else if($r['type'] == 'area') {
						if(isset($a[$qid])) {
							$a[$qid] = intval($a[$qid]);
							if($r['min'] && $a[$qid] < 1) message(lang($L['form_choose'], array($r['name'])));
							$post[$qid] = $a[$qid] > 0 ? $a[$qid] : 0;
						} else {
							message(lang($L['form_choose'], array($r['name'])));
						}
					} else if($r['type'] == 'file') {
						if(isset($a[$qid])) {
							if($r['min'] && !is_url($a[$qid])) message(lang($L['form_choose'], array($r['name'])));
							$post[$qid] = is_url($a[$qid]) ? $a[$qid] : '';
							$uploads .= $post[$qid];
						} else {
							message(lang($L['form_upload'], array($r['name'])));
						}
					}
				}
				if($uploads) clear_upload($uploads, $itemid);
				$db->query("INSERT INTO {$DT_PRE}form_record (fid,username,ip,addtime,item) VALUES ('$itemid','$_username','$DT_IP','$DT_TIME','$item')");
				$rid = $db->insert_id();
				foreach($post as $k=>$v) {
					$o = isset($other[$k]) ? $other[$k] : '';
					$db->query("INSERT INTO {$DT_PRE}form_answer (fid,rid,qid,username,ip,addtime,content,other,item) VALUES ('$itemid','$rid','$k','$_username','$DT_IP','$DT_TIME','$v','$o','$item')");
				}
				$db->query("UPDATE {$DT_PRE}form SET answer=answer+1 WHERE itemid=$itemid");
				dheader('index'.DT_EXT.'?action=result&itemid='.$itemid);
			} else {
				dheader('index'.DT_EXT.'?action=result&itemid='.$itemid.'&code=99');
			}
		}
		$back = $DT_PC ? $linkurl : str_replace($url, $mob, $linkurl);
		$adddate = timetodate($addtime, 3);
		$fromdate = $fromtime ? timetodate($fromtime, 3) : $L['timeless'];
		$todate = $totime ? timetodate($totime, 3) : $L['timeless'];
		$content = DC::format($content, $DT_PC);
		$lists = array();
		$result = $db->query("SELECT * FROM {$DT_PRE}form_question WHERE fid=$itemid ORDER BY listorder ASC,qid ASC LIMIT 1000");
		while($r = $db->fetch_array($result)) {
			$t = explode('-', $r['required']);
			$r['min'] = isset($t[0]) ? intval($t[0]) : 0;
			$r['max'] = isset($t[1]) ? intval($t[1]) : 0;
			if($r['min'] && $r['max'] <= $r['min']) $r['max'] = 0;
			$r['option'] = array();
			if($r['type'] == 'text') {
				if(strpos($r['extend'], 'size=') === false) $r['extend'] .= ' size="50"';
			} else if($r['type'] == 'textarea') {
				if(strpos($r['extend'], 'rows=') === false) $r['extend'] .= ' rows="5"';
				if(strpos($r['extend'], 'cols=') === false) $r['extend'] .= ' cols="80"';
			} else if($r['type'] == 'select' || $r['type'] == 'checkbox' || $r['type'] == 'radio') {
				$t = explode('|', $r['value']);
				foreach($t as $k=>$v) {
					$o = str_replace('(*)', '', $v);
					if(strpos($v, 'http') === false) {
						$r['option'][$k]['name'] = $o;
						$r['option'][$k]['img'] = '';
					} else {
						$r['option'][$k]['name'] = cutstr($o, '', 'http');
						$r['option'][$k]['img'] = 'http'.cutstr($o, 'http', '');
						if(!is_url($r['option'][$k]['img']) && !is_image($r['option'][$k]['img'])) $r['option'][$k]['img'] = '';
					}
					$r['option'][$k]['on'] = strpos($v, '(*)') !== false ? 1 : 0;
				}
			} else if($r['type'] == 'date') {
				if(!is_date($r['value'])) $r['value'] = '';
			} else if($r['type'] == 'time') {
				if(!is_time($r['value'])) $r['value'] = '';
			} else if($r['type'] == 'area') {
				$r['value'] = intval($r['value']);
			} else if($r['type'] == 'file') {
				if(!$r['extend']) $r['extend'] = $DT['uploadtype'];
				$r['extends'] = str_replace('|', ',', $r['extend']);
			}
			$lists[] = $r;
		}
		//$display = 0;
		if(!$DT_BOT) $db->query("UPDATE LOW_PRIORITY {$DT_PRE}{$ext} SET hits=hits+1 WHERE itemid=$itemid", 'UNBUFFERED');
		$head_title = $title.$DT['seo_delimiter'].$L['form_title'];
		$template = $f['template'] ? $f['template'] : $ext;
	} else {
		$head_title = $L['form_title'];
		if($catid) $typeid = $catid;
		$condition = '1';
		if($keyword) $condition .= match_kw('title', $keyword);
		if($typeid) {
			isset($TYPE[$typeid]) or dheader($url);
			$condition .= " AND typeid IN (".type_child($typeid, $TYPE).")";
			$head_title = $TYPE[$typeid]['typename'].$DT['seo_delimiter'].$head_title;
		}
		if($cityid) $condition .= ($AREA[$cityid]['child']) ? " AND areaid IN (".$AREA[$cityid]['arrchildid'].")" : " AND areaid=$cityid";
		$lists = $do->get_list($condition, 'addtime DESC');
		$template = $ext;
	}
}
if($DT_PC) {
	$destoon_task = rand_task();
	if($EXT['mobile_enable']) $head_mobile = str_replace($url, $mob, $DT_URL);
} else {
	$foot = '';
	if($itemid) {
		$js_item = 1;
	} else {
		$pages = mobile_pages($items, $page, $pagesize);
	}
	if($sns_app) $seo_title = $site_name;
}
include template($template, $module);
?>