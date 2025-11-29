<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('echarts.min.js'); ?>
<?php foreach($lists as $k=>$v) {?>
<div class="tt"><?php echo $v['title'];?></div>
<a name="q<?php echo $k;?>"></a>
<table cellspacing="0" class="tb">
<tr>
<td>
<div id="chart-pie<?php echo $k;?>" style="width:600px;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart-pie<?php echo $k;?>'));
var option = {
    title: {
        text: '',
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
        data: [<?php echo $v['xd'];?>]
    },
    series: [
        {
            name: '<?php echo $v['title'];?>',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [<?php echo $v['yd'];?>],
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
</td>
</tr>
</table>
<?php } ?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>