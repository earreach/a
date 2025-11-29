<?php
defined('IN_DESTOON') or exit('Access Denied');
if(isset($mapmid)) {
$map = '';
if(is_lnglat($mapmid)) {
	$map = $mapmid;
} else if($DT['lnglat_appcode']) {
	$map = cloud_lnglat($address, $DT['lnglat_appcode'], 0);
	if(is_lnglat($map)) $mapmid = $map;
}
is_lnglat($map) or $map = $map_mid;
?>
<style type="text/css">
.content-window-card{
	position: relative;
	width: 240px;
	padding:6px;
	box-shadow: none;
	bottom: 0;
	left: 0;
}
.content-window-card b {font-size:14px;}
.content-window-card p {padding:0;margin:0;font-size:12px;color:#666666;}
</style>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key=<?php echo $map_key;?>"></script>
<script type="text/javascript">
$(function() {
	<?php if($DT_PC) { ?>
	<?php } else { ?>
	$('#dmap').css('height', ($(window).height()-48)+'px');
	<?php } ?>
	var infoWindow;
	var map = new AMap.Map("dmap", {
		resizeEnable: true,
		center: [<?php echo $map;?>],
		zoom: 16
	});

	AMap.plugin('AMap.ToolBar',function(){ 
	  var toolbar = new AMap.ToolBar(); //缩放工具条实例化
	  map.addControl(toolbar); //添加控件
	});

<?php if($mapmid) { ?>
	const position = new AMap.LngLat(<?php echo $map;?>); //Marker 经纬度
	const marker = new AMap.Marker({
	  position: position,
	  content: '<img src="//a.amap.com/jsapi_demos/static/demo-center/icons/poi-marker-red.png" width="24"/>', //将 html 传给 content
	  offset: new AMap.Pixel(-13, -30), //以 icon 的 [center bottom] 为原点
	});
	map.add(marker);

	var info = [];
	info.push("<div class='input-card content-window-card'>");
	info.push("<div><b><?php echo $company;?></b>");
	info.push("<p class='input-item'><?php echo $address;?></p></div></div>");
	infoWindow = new AMap.InfoWindow({
		content: info.join("")  //使用默认信息窗体框样式，显示信息内容
	});
	infoWindow.open(map, map.getCenter());
<?php } else { ?>
	//https://lbs.amap.com/api/javascript-api-v2/guide/services/geocoder
	AMap.plugin("AMap.Geocoder", function () {
		var geocoder = new AMap.Geocoder({
				city: "", //城市设为北京，默认：“全国”
		});
		var marker = new AMap.Marker();
		var address  = '<?php echo $address;?>';
		console.log('test3');
		geocoder.getLocation(address, function(status, result) {
			if (status === 'complete'&&result.geocodes.length) {
				var lnglat = result.geocodes[0].location
				marker.setPosition(lnglat);
				map.add(marker);
				map.setFitView(marker);
				var info = [];
				info.push("<div class='input-card content-window-card'>");
				info.push("<div><b><?php echo $company;?></b>");
				info.push("<p class='input-item'><?php echo $address;?></p></div></div>");
				infoWindow = new AMap.InfoWindow({
					content: info.join("")  //使用默认信息窗体框样式，显示信息内容
				});
				infoWindow.open(map, map.getCenter());
			}else{
				alert('您选择地址没有解析到结果');
			}
		});
	});
<?php } ?>
});
</script>
<?php
} else {
	echo '<iframe src="'.DT_PATH.'api/map/amap/show.php?auth='.$map_auth.'" style="width:100%;height:'.$map_height.'px;" scrolling="no" frameborder="0"></iframe>';
}
?>