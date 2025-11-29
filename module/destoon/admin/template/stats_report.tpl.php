<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="nav">
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=pvs"<?php echo $job == 'pvs' ? ' class="b"' : '';?>>流量趋势</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=uvs"<?php echo $job == 'uvs' ? ' class="b"' : '';?>>UV趋势</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=ips"<?php echo $job == 'ips' ? ' class="b"' : '';?>>IP趋势</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=rbs"<?php echo $job == 'rbs' ? ' class="b"' : '';?>>抓取频次</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=robot"<?php echo $job == 'robot' ? ' class="b"' : '';?>>搜索引擎</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=pc"<?php echo $job == 'pc' ? ' class="b"' : '';?>>上网设备</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=bd"<?php echo $job == 'bd' ? ' class="b"' : '';?>>手机品牌</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=os"<?php echo $job == 'os' ? ' class="b"' : '';?>>操作系统</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=bs"<?php echo $job == 'bs' ? ' class="b"' : '';?>>浏览器</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=screen"<?php echo $job == 'screen' ? ' class="b"' : '';?>>分辨率</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=network"<?php echo $job == 'network' ? ' class="b"' : '';?>>运营商</a>
</div>
<div class="nav">
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=country"<?php echo $job == 'country' ? ' class="b"' : '';?>>国家分布</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=province"<?php echo $job == 'province' ? ' class="b"' : '';?>>省份分布</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=city"<?php echo $job == 'city' ? ' class="b"' : '';?>>热点城市</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=ip"<?php echo $job == 'ip' ? ' class="b"' : '';?>>活跃IP</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=username"<?php echo $job == 'username' ? ' class="b"' : '';?>>活跃会员</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=homepage"<?php echo $job == 'homepage' ? ' class="b"' : '';?>>商家排行</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=mid"<?php echo $job == 'mid' ? ' class="b"' : '';?>>模块排行</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=catid"<?php echo $job == 'catid' ? ' class="b"' : '';?>>栏目排行</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=itemid"<?php echo $job == 'itemid' ? ' class="b"' : '';?>>信息排行</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=url"<?php echo $job == 'url' ? ' class="b"' : '';?>>页面排行</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=refer"<?php echo $job == 'refer' ? ' class="b"' : '';?>>来源排行</a>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&job=domain"<?php echo $job == 'domain' ? ' class="b"' : '';?>>外部链接</a>
</div>

<?php load('echarts.min.js'); ?>
<?php
$num = 20;
if($job == 'pvs') {
	(isset($todate) && is_date($todate)) or $todate = '';
	$totime = is_date($todate) ? datetotime($todate) : DT_TIME;
	if($totime >= DT_TIME) $totime = DT_TIME - 86400;
	$fromtime = timetodate($totime - 86400*30, 'Ymd');
	$totime = timetodate($totime, 'Ymd');
	$data = $pv = $pv_pc = $pv_mb = '';
	$result = $db->query("SELECT * FROM {$DT_PRE}stats WHERE id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
		$pv .= $r['pv'].',';
		$pv_pc .= $r['pv_pc'].',';
		$pv_mb .= $r['pv_mb'].',';
	}
	if($data) {
		$data = substr($data, 0, -1);
		$pv = substr($pv, 0, -1);
		$pv_pc = substr($pv_pc, 0, -1);
		$pv_mb = substr($pv_mb, 0, -1);
	}
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
截至日期 <?php echo dcalendar('todate', $todate);?>&nbsp;
<input type="submit" value="生 成" class="btn" title="显示截至日期30天内的数据"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>
</form>
</div>
<div id="chart" style="width:90%;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: '流量趋势'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: ['总PV', '电脑PV', '手机PV']
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
        data: [<?php echo $data;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '总PV',
            type: 'line',
            data: [<?php echo $pv;?>]
        },
        {
            name: '电脑PV',
            type: 'line',
            data: [<?php echo $pv_pc;?>]
        },
        {
            name: '手机PV',
            type: 'line',
            data: [<?php echo $pv_mb;?>]
        }
    ]
};
chart.setOption(option);
</script>


<?php
} else if($job == 'uvs') {
	(isset($todate) && is_date($todate)) or $todate = '';
	$totime = is_date($todate) ? datetotime($todate) : DT_TIME;
	if($totime >= DT_TIME) $totime = DT_TIME - 86400;
	$fromtime = timetodate($totime - 86400*30, 'Ymd');
	$totime = timetodate($totime, 'Ymd');
	$data = $uv = $uv_pc = $uv_mb = '';
	$result = $db->query("SELECT * FROM {$DT_PRE}stats WHERE id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
		$uv .= $r['uv'].',';
		$uv_pc .= $r['uv_pc'].',';
		$uv_mb .= $r['uv_mb'].',';
	}
	if($data) {
		$data = substr($data, 0, -1);
		$uv = substr($uv, 0, -1);
		$uv_pc = substr($uv_pc, 0, -1);
		$uv_mb = substr($uv_mb, 0, -1);
	}
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
截至日期 <?php echo dcalendar('todate', $todate);?>&nbsp;
<input type="submit" value="生 成" class="btn" title="显示截至日期30天内的数据"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>
</form>
</div>
<div id="chart" style="width:90%;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: 'UV趋势'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: ['总UV', '电脑UV', '手机UV']
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
        data: [<?php echo $data;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '总UV',
            type: 'line',
            data: [<?php echo $uv;?>]
        },
        {
            name: '电脑UV',
            type: 'line',
            data: [<?php echo $uv_pc;?>]
        },
        {
            name: '手机UV',
            type: 'line',
            data: [<?php echo $uv_mb;?>]
        }
    ]
};
chart.setOption(option);
</script>

<?php
} else if($job == 'ips') {
	(isset($todate) && is_date($todate)) or $todate = '';
	$totime = is_date($todate) ? datetotime($todate) : DT_TIME;
	if($totime >= DT_TIME) $totime = DT_TIME - 86400;
	$fromtime = timetodate($totime - 86400*30, 'Ymd');
	$totime = timetodate($totime, 'Ymd');
	$data = $ip = $ip_pc = $ip_mb = '';
	$result = $db->query("SELECT * FROM {$DT_PRE}stats WHERE id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
		$ip .= $r['ip'].',';
		$ip_pc .= $r['ip_pc'].',';
		$ip_mb .= $r['ip_mb'].',';
	}
	if($data) {
		$data = substr($data, 0, -1);
		$ip = substr($ip, 0, -1);
		$ip_pc = substr($ip_pc, 0, -1);
		$ip_mb = substr($ip_mb, 0, -1);
	}
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
截至日期 <?php echo dcalendar('todate', $todate);?>&nbsp;
<input type="submit" value="生 成" class="btn" title="显示截至日期30天内的数据"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>
</form>
</div>
<div id="chart" style="width:90%;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: 'IP趋势'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: ['总IP', '电脑IP', '手机IP']
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
        data: [<?php echo $data;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '总IP',
            type: 'line',
            data: [<?php echo $ip;?>]
        },
        {
            name: '电脑IP',
            type: 'line',
            data: [<?php echo $ip_pc;?>]
        },
        {
            name: '手机IP',
            type: 'line',
            data: [<?php echo $ip_mb;?>]
        }
    ]
};
chart.setOption(option);
</script>


<?php
} else if($job == 'rbs') {
	(isset($todate) && is_date($todate)) or $todate = '';
	$totime = is_date($todate) ? datetotime($todate) : DT_TIME;
	if($totime >= DT_TIME) $totime = DT_TIME - 86400;
	$fromtime = timetodate($totime - 86400*30, 'Ymd');
	$totime = timetodate($totime, 'Ymd');
	$data = $rb = $rb_pc = $rb_mb = '';
	$result = $db->query("SELECT * FROM {$DT_PRE}stats WHERE id>$fromtime AND id<=$totime ORDER BY id ASC LIMIT 30", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$data .= "'".substr($r['id'], 4, 2).'-'.substr($r['id'], 6, 2)."',";
		$rb .= $r['rb'].',';
		$rb_pc .= $r['rb_pc'].',';
		$rb_mb .= $r['rb_mb'].',';
	}
	if($data) {
		$data = substr($data, 0, -1);
		$rb = substr($rb, 0, -1);
		$rb_pc = substr($rb_pc, 0, -1);
		$rb_mb = substr($rb_mb, 0, -1);
	}
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
截至日期 <?php echo dcalendar('todate', $todate);?>&nbsp;
<input type="submit" value="生 成" class="btn" title="显示截至日期30天内的数据"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>
</form>
</div>
<div id="chart" style="width:90%;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
    title: {
        text: '抓取频次'
    },
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: ['总PV', '电脑PV', '手机PV']
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
        data: [<?php echo $data;?>]
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '总PV',
            type: 'line',
            data: [<?php echo $rb;?>]
        },
        {
            name: '电脑PV',
            type: 'line',
            data: [<?php echo $rb_pc;?>]
        },
        {
            name: '手机PV',
            type: 'line',
            data: [<?php echo $rb_mb;?>]
        }
    ]
};
chart.setOption(option);
</script>

<?php
} else if($job == 'pc') {
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$condition = '1';
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	$key = $job;
	$xd = $yd = '';
	$result = $db->query("SELECT COUNT(`{$key}`) AS num,`{$key}` FROM {$DT_PRE}stats_uv WHERE {$condition} GROUP BY `{$key}` ORDER BY num DESC LIMIT 0,2", 'CACHE');
	while($r = $db->fetch_array($result)) {
		$r[$key] = $r[$key] ? '电脑' : '手机';
		$xd .= "'".$r[$key]."',";
		$yd .= "{value:".$r['num'].", name:'".$r[$key]."'},";
	}
	if($xd) {
		$xd = substr($xd, 0, -1);
		$yd = substr($yd, 0, -1);
	}
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="submit" value="生 成" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>
</form>
</div>
<div id="chart-pie" style="width:600px;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart-pie'));
var option = {
    title: {
        text: '上网设备',
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
            name: '设备类型',
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


<?php
} else if(in_array($job, array('robot', 'screen', 'bs', 'bd', 'os', 'country', 'province', 'city', 'domain', 'ip', 'homepage', 'username', 'mid', 'catid', 'itemid', 'url', 'refer'))) {
	$sv = in_array($job, array('robot', 'screen', 'bs', 'bd', 'os', 'country', 'province', 'city')) ? 'uv' : 'pv';
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$maxlist = isset($maxlist) ? intval($maxlist) : 50;
	$module_select = '';
	if(in_array($job, array('catid', 'itemid'))) {
		if($mid < 5) {
			foreach($MODULE as $v) {
				if($v['islink'] || $v['moduleid'] < 5) continue;
				$mid = $v['moduleid'];
				break;
			}
		}
		$module_select = module_select('mid', '模块', $mid, '', '1,2,3,4');
	}
	$condition = '1';
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	if($mid > 4) $condition .= " AND mid=$mid";
	$key = $job;
	$xd = $yd = '';
	$lists = array();
	$result = $db->query("SELECT COUNT(`{$key}`) AS num,`{$key}`".($sv == 'pv' ? ',`mid`' : '')." FROM {$DT_PRE}stats_{$sv} WHERE {$condition} GROUP BY `{$key}` ORDER BY num DESC LIMIT 0,$maxlist", 'CACHE');
	while($r = $db->fetch_array($result)) {
		if(!$r[$key]) continue;
		$r['linkurl'] = '?file='.$file.'&action='.$sv.'&fromdate='.$fromdate.'&todate='.$todate.'&'.$job.'='.urlencode($r[$key]);
		if($job == 'homepage') {
			$uname = $r[$key];
			if(!check_name($uname)) continue;
			$t = $db->get_one("SELECT company FROM {$DT_PRE}member WHERE username='$uname'");
			if(!$t) continue;
			$r[$key] = $t['company'];
		} else if($job == 'username') {
			$r['linkurl'] = '?moduleid=2&action=show&username='.$r[$key];
		} else if($job == 'mid') {
			$r[$key] = $MODULE[$r[$key]]['name'];		
		} else if($job == 'catid') {
			$t = get_cat($r[$key]);
			if(!$t) continue;
			$r[$key] = $t['catname'];
		} else if($job == 'itemid') {
			$itemid = $r[$key];
			$r['linkurl'] .= '&mid='.$r['mid'];
			$t = $db->get_one("SELECT title FROM ".get_table($r['mid'])." WHERE itemid=$itemid");
			if(!$t) continue;
			$r[$key] = dsubstr($t['title'], 30, '...');
		} else if($job == 'domain') {
			$r['uri'] = $r[$job];
			if(strpos($r[$job], DT_DOMAIN ? DT_DOMAIN : cutstr(DT_PATH, '://', '/')) !== false) continue;
			$r['url'] = gourl('http://'.$r[$job]);
		} else if($job == 'url') {
			$r['uri'] = linkurl($r[$job], 1);
			$r['url'] = gourl($r['uri']);
		} else if($job == 'refer') {
			$r['uri'] = linkurl($r[$job], 1);
			$r['url'] = gourl($r['uri']);
		} else if($job == 'ip') {
			$r[$key] = $r[$key].'('.ip2area($r[$key], 2).')';
		}
		$lists[] = $r;
	}
	$max = $tt = 0;
	if($lists) {
		$max = count($lists);
		for($i = $max - 1; $i >= 0; $i--) {			
			$xd .= "'".$lists[$i][$key]."'".($i ? "," : "");
			$yd .= "{value:".$lists[$i]['num'].",name:'".$lists[$i][$key]."',url:'".$lists[$i]['linkurl']."'}".($i ? "," : "");
			$tt += $lists[$i]['num'];
		}
	}
	$height = $max ? ($max*32)+100 : 600;
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<span data-hide-1200="1"><?php echo $module_select ? $module_select.'&nbsp; ' : '';?></span>
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="maxlist" value="<?php echo $maxlist;?>" size="6" class="t_c" placeholder="数量" title="数量"/>&nbsp;
<input type="submit" value="生 成" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>&nbsp;&nbsp;&nbsp;&nbsp;
<?php $date = timetodate($DT_TIME, 3);?>
<a href="javascript:;" onclick="Dq('date','<?php echo $date;?>');" class="t">今日</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('date','<?php echo timetodate($DT_TIME - 86400, 3);?>');" class="t">昨日</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('date','<?php echo timetodate($DT_TIME - 86400*2, 3);?>');" class="t">前日</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('todate','<?php echo $date;?> 23:59:59', 0);Dq('fromdate','<?php echo timetodate($DT_TIME - 86400*3, 3);?> 00:00:00');" class="t">近三日</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('todate','<?php echo $date;?> 23:59:59', 0);Dq('fromdate','<?php echo timetodate($DT_TIME - 86400*7, 3);?> 00:00:00');" class="t">近七日</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('todate','<?php echo $date;?> 23:59:59', 0);Dq('fromdate','<?php echo timetodate($DT_TIME - 86400*30, 3);?> 00:00:00');" class="t">近一月</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('todate','<?php echo $date;?> 23:59:59', 0);Dq('fromdate','<?php echo timetodate($DT_TIME - 86400*91, 3);?> 00:00:00');" class="t">近三月</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('todate','<?php echo $date;?> 23:59:59', 0);Dq('fromdate','<?php echo timetodate($DT_TIME - 86400*183, 3);?> 00:00:00');" class="t">近半年</a>&nbsp;&nbsp;
<a href="javascript:;" onclick="Dq('todate','<?php echo $date;?> 23:59:59', 0);Dq('fromdate','<?php echo timetodate($DT_TIME - 86400*365, 3);?> 00:00:00');" class="t">近一年</a>&nbsp;&nbsp;
</form>
</div>
<?php if(in_array($job, array('domain', 'url', 'refer'))) { ?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="700">网址</th>
<th width="100">PV</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td title="<?php echo $v['uri'];?>"><input type="text" size="100" value="<?php echo $v['uri'];?>"/> <a href="<?php echo $v['url'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td><a href="javascript:;" onclick="Dwidget('<?php echo $v['linkurl'];?>', 'PV记录');"><?php echo $v['num'];?></a></td>
<td></td>
</tr>
<?php }?>
</table>

<?php } else { ?>
<div id="chart" style="width:90%;height:<?php echo $height;?>px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart'));
var option = {
	color: ['#61A0A8'],
    title: {
        text: '',
        subtext: ''
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        },
		formatter: function(params) {
			return '<?php echo strtoupper($sv);?>:'+params[0].value+' ('+(params[0].value/<?php echo $tt;?>*100).toFixed(2) + '%)'
		}
    },
    grid: {
		top: '16px;',
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'value',
        boundaryGap: [0, 0.01]
    },
    yAxis: {
        type: 'category',
        data: [<?php echo $xd;?>]
    },
    series: [
        {
            type: 'bar',
            data: [<?php echo $yd;?>]
        }
    ]
};
chart.setOption(option);
chart.on('click', function(e) {
	if(e.data.url) {
		if(e.data.url.substring(0, 1) == '?') {
			Dwidget(e.data.url, e.data.name);
		} else {
			window.open(e.data.url);
		}
	}
});
</script>
<?php } ?>
<?php
} else if($job == 'network') {
	(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
	$fromtime = $fromdate ? datetotime($fromdate) : 0;
	(isset($todate) && is_time($todate)) or $todate = '';
	$totime = $todate ? datetotime($todate) : 0;
	$condition = '1';
	if($fromtime) $condition .= " AND addtime>=$fromtime";
	if($totime) $condition .= " AND addtime<=$totime";
	$key = $job;
	$xd = $yd = '';
	$result = $db->query("SELECT COUNT(`{$key}`) AS num,`{$key}` FROM {$DT_PRE}stats_uv WHERE {$condition} GROUP BY `{$key}` ORDER BY num DESC LIMIT 0,20", 'CACHE');
	while($r = $db->fetch_array($result)) {
		if(!$r[$key]) continue;
		$xd .= "'".$r[$key]."',";
		$yd .= "{value:".$r['num'].", name: '".$r[$key]."'},";
	}
	if($xd) {
		$xd = substr($xd, 0, -1);
		$yd = substr($yd, 0, -1);
	}
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="job" value="<?php echo $job;?>"/>
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="submit" value="生 成" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=<?php echo $job;?>');"/>
</form>
</div>
<div id="chart-pie" style="width:600px;height:400px;margin:16px;"></div>
<script type="text/javascript">
var chart = echarts.init(Dd('chart-pie'));
var option = {
    title: {
        text: '运营商',
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
            name: '运营商',
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

<?php
}
?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>