<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$itemid) show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<select name="mid">
<?php
	foreach($MODULE as $m) {
	if(!in_array($m['moduleid'], array(1,3,4)) && !$m['islink']) {
?>
<option value="<?php echo $m['moduleid'];?>"<?php echo $mid == $m['moduleid'] ? ' selected' : ''?>><?php echo $m['name'];?></option>
<?php } } ?>
</select>&nbsp;
<select name="year">
<option value="0">选择年</option>
<?php for($i = date("Y", $DT_TIME); $i >= 2000; $i--) { ?>
<option value="<?php echo $i;?>"<?php echo $i == $year ? ' selected' : ''?>><?php echo $i;?>年</option>
<?php } ?>
</select>&nbsp;
<select name="month">
<option value="0">选择月</option>
<?php for($i = 1; $i < 13; $i++) { ?>
<option value="<?php echo $i;?>"<?php echo $i == $month ? ' selected' : ''?>><?php echo $i;?>月</option>
<?php } ?>
</select>&nbsp;
<input type="submit" value="生成报表" class="btn-g"/>&nbsp;&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&mid=<?php echo $mid;?>&itemid=<?php echo $itemid;?>');"/>
</form>
</div>
<?php load('echarts.min.js'); ?>
<?php
	if($year && $month && $mid) {
	$tb = get_table($mid);
	$fd = 'addtime';
	$ym = $year.'-'.$month;
	if($mid == 2) $fd = 'regtime';
	$d = date('t', datetotime($ym.'-1'));
	$xd = $yd = '';
	for($i = 1; $i <= $d; $i++) {
		if($i > 1) { $xd .= ','; $yd .= ','; }
		$f = datetotime($ym.'-'.$i.' 00:00:00');
		$t = datetotime($ym.'-'.$i.' 23:59:59');
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$tb} WHERE `$fd`>=$f AND `$fd`<=$t");
		$xd .= "'".$i."日'";
		$yd .= $r['num'];
	}
?>
<div id="chart" style="width:90%;height:500px;margin:16px;"></div>        
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: '<?php echo $MODULE[$mid]['name'];?> <?php echo $year;?>年<?php echo $month;?>月统计报表'
    },
    tooltip: {
        trigger: 'axis',
        formatter: '{b}:{c}'
    },
    grid: {
        left: '1%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: [<?php echo $xd;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [<?php echo $yd;?>],
        type: 'bar'
    }]
};

chart.setOption(option);
</script>

<?php
	} else if($year && $mid) {
	$tb = get_table($mid);
	$fd = 'addtime';
	$ym = $year;
	if($mid == 2) $fd = 'regtime';
	$xd = $yd = '';
	for($i = 1; $i < 13; $i++) {
		if($i > 1) { $xd .= ','; $yd .= ','; }
		$f = datetotime($ym.'-'.$i.'-1 00:00:00');
		$d = date('t', $f);
		$t = datetotime($ym.'-'.$i.'-'.$d.' 23:59:59');
		$r = $db->get_one("SELECT COUNT(*) AS num FROM {$tb} WHERE `$fd`>=$f AND `$fd`<=$t");
		$xd .= "'".$i."月'";
		$yd .= $r['num'];
	}
?>
<div id="chart" style="width:90%;height:500px;margin:16px;"></div>        
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: '<?php echo $MODULE[$mid]['name'];?> <?php echo $year;?>年统计报表'
    },
    tooltip: {
        trigger: 'axis',
        formatter: '{b}:{c}'
    },
    grid: {
        left: '1%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: [<?php echo $xd;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [{
        data: [<?php echo $yd;?>],
        type: 'bar'
    }]
};

chart.setOption(option);
</script>
<?php } ?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>