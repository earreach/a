<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
$MG['biz'] or dheader(($DT_PC ? $MOD['linkurl'] : $MOD['mobile']).'account'.DT_EXT.'?action=group&itemid=1');
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$head_title = $L['biz_title'];
if($DT_PC) {
	$year = isset($year) ? intval($year) : date('Y', $DT_TIME);
	$year or $year = date('Y', $DT_TIME);
	$month = isset($month) ? intval($month) : date('n', $DT_TIME);
	$xd = $y0 = $y1 = '';
	$t0 = $t1 = 0;
	if($month) {
		$tt = date('t', datetotime($year.'-'.$month.'-01'));
		for($i = 1; $i <= $tt; $i++) {
			if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; }
			$xd .= "'".$i.$L['biz_day']."'";
			$F = datetotime($year.'-'.$month.'-'.$i.' 00:00:00');
			$T = datetotime($year.'-'.$month.'-'.$i.' 23:59:59');
			$condition = "addtime>=$F AND addtime<=$T AND seller='$_username' AND pid=0";
			$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$DT_PRE}order WHERE {$condition} AND status=4");
			$num1 = $t['num1'] ? dround($t['num1']) : 0;
			$num2 = $t['num2'] ? dround($t['num2']) : 0;
			$num = $num1 + $num2;
			$y0 .= $num; $t0 += $num;
			$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$DT_PRE}order WHERE {$condition} AND status=6");
			$num1 = $t['num1'] ? dround($t['num1']) : 0;
			$num2 = $t['num2'] ? dround($t['num2']) : 0;
			$num = $num1 + $num2;
			$y1 .= $num; $t1 += $num;
		}
		$title = lang($L['biz_title_month'], array($year, $month, $DT['money_unit']));
	} else {
		for($i = 1; $i < 13; $i++) {
			if($i > 1) { $xd .= ','; $y0 .= ','; $y1 .= ','; }
			$xd .= "'".$i.$L['biz_month']."'";
			$F = datetotime($year.'-'.$i.'-01 00:00:00');
			$T = datetotime($year.'-'.$i.'-'.date('t', $F).' 23:59:59');
			$condition = "addtime>=$F AND addtime<=$T AND seller='$_username' AND pid=0";
			$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$DT_PRE}order WHERE {$condition} AND status=4");
			$num1 = $t['num1'] ? dround($t['num1']) : 0;
			$num2 = $t['num2'] ? dround($t['num2']) : 0;
			$num = $num1 + $num2;
			$y0 .= $num; $t0 += $num;
			$t = $db->get_one("SELECT SUM(`amount`) AS num1,SUM(`fee`) AS num2 FROM {$DT_PRE}order WHERE {$condition} AND status=6");
			$num1 = $t['num1'] ? dround($t['num1']) : 0;
			$num2 = $t['num2'] ? dround($t['num2']) : 0;
			$num = $num1 + $num2;
			$y1 .= $num; $t1 += $num;
		}
		$title = lang($L['biz_title_year'], array($year, $DT['money_unit']));
	}
	$user = userinfo($_username);
	$deposit = $user['deposit'];
	$menu_id = 2;
} else {
	$head_name = $head_title;
	$foot = 'my';
}
include template('biz', $module);
?>