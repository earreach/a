<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
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
卖家：<input type="text" name="seller" value="<?php echo $seller;?>" size="10"/>&nbsp;
<input type="submit" value="生成报表" class="btn-g"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<?php load('echarts.min.js'); ?>
<div id="chart" style="width:90%;height:500px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: '<?php echo $title;?>'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: ['交易成功', '退款给买家', '付款给卖家']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    toolbox: {
        feature: {
            saveAsImage: {}
        }
    },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: [<?php echo $xd;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '交易成功',
            type: 'line',
            data: [<?php echo $y0;?>]
        },
        {
            name: '退款给买家',
            type: 'line',
            data: [<?php echo $y1;?>]
        },
        {
            name: '付款给卖家',
            type: 'line',
            data: [<?php echo $y2;?>]
        }
    ]
};
chart.setOption(option);
</script>
<script type="text/javascript">Menuon(6);</script>
<?php include tpl('footer');?>