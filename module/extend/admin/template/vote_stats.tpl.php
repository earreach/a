<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('echarts.min.js'); ?>
<div id="chart-pie" style="width:600px;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart-pie'));
var option = {
    title: {
        text: '<?php echo $title;?>',
        subtext: '',
        left: 'center'
    },
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c} ({d}%)'
    },
    legend: {
        orient: 'vertical',
        left: 'left',
        data: [<?php echo $xd;?>]
    },
    series: [
        {
            name: '<?php echo $title;?>',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [<?php echo $yd;?>],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};
chart.setOption(option);
</script>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>