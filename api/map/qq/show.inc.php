<?php
defined('IN_DESTOON') or exit('Access Denied');
#https://lbs.qq.com/service/webService/webServiceGuide/address/SmartGeocoder
if(isset($mapmid)) {
$map = '';
if(is_lnglat($mapmid)) {
	$map = $mapmid;
} else if($DT['lnglat_appcode']) {
	$map = cloud_lnglat($address, $DT['lnglat_appcode'], 1);
	if(is_lnglat($map)) $mapmid = $map;
}
is_lnglat($map) or $map = $map_mid;
?>
<script type="text/javascript" src="https://map.qq.com/api/gljs?v=1.exp&libraries=service&key=<?php echo $map_key;?>"></script>
<script type="text/javascript">
var geocoder;
var markers;
var map;
function convert() {
	//服务类库 地址至坐标转换 https://lbs.qq.com/webDemoCenter/glAPI/glServiceLib/geocoderGetLocation
	markers.setGeometries([]);
	// 将给定的地址转换为坐标位置
	geocoder
	.getLocation({address: '<?php echo $address;?>'})
	.then((result) => {
	markers.updateGeometries([
	{
		id: 'main',
		position: result.result.location, // 将得到的坐标位置用点标记标注在地图上
	},
	]);
	map.setCenter(result.result.location);
    var InfoWindow = new TMap.InfoWindow({
        position: center,
        map: map,
        content:'<div style="line-height:20px;text-align:left;"><b style="font-size:14px;"><?php echo $company;?></b><br/><span style="font-size:12px;color:#666666;"><?php echo $address;?></span></div>'
    });
	});
}
$(function() {
	<?php if($DT_PC) { ?>
	<?php } else { ?>
	$('#dmap').css('height', ($(window).height()-48)+'px');
	<?php } ?>
	var center = new TMap.LatLng(<?php echo $map;?>);
    map = new TMap.Map(document.getElementById('dmap'),{
        center:center,
        zoom:16
    });
    var InfoWindow = new TMap.InfoWindow({
        position: center,
        map: map,
        content:'<div style="line-height:20px;text-align:left;"><b style="font-size:14px;"><?php echo $company;?></b><br/><span style="font-size:12px;color:#666666;"><?php echo $address;?></span></div>'
    });
	<?php if($map == $map_mid) { ?>
	geocoder = new TMap.service.Geocoder(); // 新建一个正逆地址解析类
	markers = new TMap.MultiMarker({
	  map: map,
	  geometries: [],
	});
	convert();
	<?php } ?>
});
</script>
<?php
} else {
	echo '<iframe src="'.DT_PATH.'api/map/qq/show.php?auth='.$map_auth.'" style="width:100%;height:'.$map_height.'px;" scrolling="no" frameborder="0"></iframe>';
}
?>