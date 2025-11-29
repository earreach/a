<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
$could_comment = false;
if($DT_PC) {
	$itemid or dheader($MOD['linkurl']);
	if(!check_group($_groupid, $MOD['group_show'])) include load('403.inc');
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	if($item['groupid'] == 2) include load('404.inc');
	if($item && $item['status'] > 2) {
		if($MOD['show_html'] && is_file(DT_ROOT.'/'.$MOD['moduledir'].'/'.$item['linkurl'])) d301($MOD['linkurl'].$item['linkurl']);
		require DT_ROOT.'/include/content.class.php';
		extract($item);
	} else {
		include load('404.inc');
	}
	$CAT = get_cat($catid);
	if(!check_group($_groupid, $CAT['group_show'])) include load('403.inc');
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t['content'];
	if($content) {
		if($MOD['keylink']) $content = DC::keylink($content, $moduleid, $DT_PC);
		if($lazy) $content = DC::lazy($content);
		$content = DC::format($content, $DT_PC);
	}
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	$sec = get_sec($item);
	$jsdate = $stotime ? timetodate($stotime, 'Y,').(timetodate($stotime, 'n')-1).timetodate($stotime, ',j,H,i,s') : '';
	$RL = $relate_id ? get_relate($item) : array();
	$P1 = get_nv($n1, $v1);
	$P2 = get_nv($n2, $v2);
	$P3 = get_nv($n3, $v3);
	if($step) {
		@extract(unserialize($step), EXTR_SKIP);
		$mode = 2;
	} else {
		$a1 = 1;
		$p1 = $item['price'];
		$a2 = $a3 = $p2 = $p3 = '';
		$mode = $prices ? 1 : 0;
	}
	$stocks = '';
	if($stock) {
		$stocks = json_encode(get_stocks($stock));
		$mode = 3;
	}
	if($subtext && $sublink) {
		if(strpos($subtitle, $subtext) === false) {
			$subtitle .= '<a href="'.$sublink.'" target="_blank"><span>'.$subtext.'</span></a>';
		} else {
			$subtitle = str_replace($subtext, '<a href="'.$sublink.'" target="_blank"><span>'.$subtext.'</span></a>', $subtitle);
		}
	}
	$mina = $a1;
	if($minamount > $mina && $minamount > 0) $mina = $minamount;
	$maxa = $amount;
	if($maxamount < $maxa && $maxamount > 0) $maxa = $maxamount;
	$unit or $unit = $L['unit'];
	$adddate = timetodate($addtime, 3);
	$editdate = timetodate($edittime, 3);
	$linkurl = $MOD['linkurl'].$linkurl;
	$albums = get_albums($item);
	$pics = count($albums);
	$pics_width = $pics*70;
	$promos = get_promos($username, $moduleid, $itemid);
	$fee = DC::fee($item['fee'], $MOD['fee_view']);
	$update = '';
	$sku_amount = get_amount($item);
	if($sku_amount != $amount) {
		$amount = $sku_amount;
		$update .= ",amount=$amount";
	}
	if(check_group($_groupid, $MOD['group_contact'])) {
		if($fee) {
			$user_status = 4;
			$destoon_task = "moduleid=$moduleid&html=show&itemid=$itemid";
		} else {
			$user_status = 3;
			$member = $item['username'] ? userinfo($item['username']) : array();
			if($member) {
				if($member['shop']) $member['company'] = $member['shop'];
				$update_user = update_user($member, $item);
				if($MOD['edit_areaid']) $update_user = cutstr($update_user, '', ',areaid');
				if($update_user) $db->query("UPDATE LOW_PRIORITY {$table} SET ".substr($update_user, 1)." WHERE username='$username'", 'UNBUFFERED');
			}
		}
	} else {
		$user_status = $_userid ? 1 : 0;
	}
	if($user_status != 3 && $_username && $item['username'] == $_username) {
		$member = userinfo($item['username']);
		$user_status = 3;
		$destoon_task = '';
	}
	if($EXT['mobile_enable']) $head_mobile = str_replace($MOD['linkurl'], $MOD['mobile'], $linkurl);
} else {
	$itemid or dheader($MOD['mobile']);
	$item = $db->get_one("SELECT * FROM {$table} WHERE itemid=$itemid");
	($item && $item['status'] > 2) or message($L['msg_not_exist']);
	if($item['groupid'] == 2) message($L['msg_not_exist']);
	require DT_ROOT.'/include/content.class.php';
	extract($item);
	$CAT = get_cat($catid);
	if(!check_group($_groupid, $MOD['group_show']) || !check_group($_groupid, $CAT['group_show'])) message($L['msg_no_right']);
	$member = array();
	$fee = DC::fee($item['fee'], $MOD['fee_view']);
	include DT_ROOT.'/mobile/api/contact.inc.php';
	$content_table = content_table($moduleid, $itemid, $MOD['split'], $table_data);
	$t = $db->get_one("SELECT content FROM {$content_table} WHERE itemid=$itemid");
	$content = $t['content'];
	if($content) {
		if($MOD['keylink']) $content = DC::keylink($content, $moduleid, $DT_PC);
		if($share_icon) $share_icon = DC::icon($thumb, $content);
		$content = DC::format($content, 0);
	}
	$CP = $MOD['cat_property'] && $CAT['property'];
	if($CP) {
		require DT_ROOT.'/include/property.func.php';
		$options = property_option($catid);
		$values = property_value($moduleid, $itemid);
	}
	$sec = get_sec($item);
	$jsdate = $stotime ? timetodate($stotime, 'Y,').(timetodate($stotime, 'n')-1).timetodate($stotime, ',j,H,i,s') : '';
	$RL = $relate_id ? get_relate($item) : array();
	$P1 = get_nv($n1, $v1);
	$P2 = get_nv($n2, $v2);
	$P3 = get_nv($n3, $v3);
	if($step) {
		@extract(unserialize($step), EXTR_SKIP);
		$mode = 2;
	} else {
		$a1 = 1;
		$p1 = $item['price'];
		$a2 = $a3 = $p2 = $p3 = '';
		$mode = $prices ? 1 : 0;
	}
	$stocks = '';
	if($stock) {
		$stocks = json_encode(get_stocks($stock));
		$mode = 3;
	}
	if($subtext && $sublink) {
		if(strpos($subtitle, $subtext) === false) {
			$subtitle .= '<a href="'.$sublink.'" target="_blank"><span>'.$subtext.'</span></a>';
		} else {
			$subtitle = str_replace($subtext, '<a href="'.$sublink.'" target="_blank"><span>'.$subtext.'</span></a>', $subtitle);
		}
	}
	$mina = $a1;
	if($minamount > $mina && $minamount > 0) $mina = $minamount;
	$maxa = $amount;
	if($maxamount < $maxa && $maxamount > 0) $maxa = $maxamount;
	$unit or $unit = $L['unit'];
	$albums = get_albums($item);
	$promos = get_promos($username, $moduleid, $itemid);
	$editdate = timetodate($edittime, 5);
	$update = '';
	$sku_amount = get_amount($item);
	if($sku_amount != $amount) {
		$amount = $sku_amount;
		$update .= ",amount=$amount";
	}
	$head_title = $head_name = $CAT['catname'];
	$js_item = $js_album = 1;
	$foot = '';
}
if(!$DT_BOT) include DT_ROOT.'/include/update.inc.php';
$seo_file = 'show';
include DT_ROOT.'/include/seo.inc.php';
$template = $item['template'] ? $item['template'] : ($CAT['show_template'] ? $CAT['show_template'] : ($MOD['template_show'] ? $MOD['template_show'] : 'show'));
include template($template, $module);
?>