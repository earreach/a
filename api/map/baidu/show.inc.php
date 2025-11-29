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
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=<?php echo $map_key;?>&services=true"></script>
<script type="text/javascript">
$(function() {
	<?php if($DT_PC) { ?>
	<?php } else { ?>
	$('#dmap').css('height', ($(window).height()-48)+'px');
	<?php } ?>
	var map = new BMap.Map("dmap");
	var point = new BMap.Point(<?php echo $map;?>);
	map.centerAndZoom(point,16);
	map.enableScrollWheelZoom(true);
	map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));
	map.addControl(new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));
	<?php if($mapmid) { ?>
		var marker = new BMap.Marker(point);
		map.addOverlay(marker);
		var opts = {
		  width : 240,
		  height: 40,
		  title : "<b style=\"font-size:14px;\"><?php echo $company;?><\/b>"
		}
		var infoWindow = new BMap.InfoWindow("<span style=\"font-size:12px;\"><?php echo $address;?><\/span>", opts);
		map.openInfoWindow(infoWindow, map.getCenter());
	<?php } else { ?>
		var myGeo = new BMap.Geocoder();
		myGeo.getPoint('<?php echo $address;?>', function(point){
			if(point) {
				map.centerAndZoom(point, 16);
				var marker = new BMap.Marker(point);
				map.addOverlay(marker);
				var opts = {
				  width : 240,
				  height: 40,
				  title : "<b style=\"font-size:14px;\"><?php echo $company;?><\/b>"
				}
				var infoWindow = new BMap.InfoWindow("<span style=\"font-size:12px;\"><?php echo $address;?><\/span>", opts);
				map.openInfoWindow(infoWindow, map.getCenter());
			} else {
				alert('您选择地址没有解析到结果');
			}
		}, '中国');
	<?php } ?>
});
</script>
<?php
} else {
	echo '<iframe src="'.DT_PATH.'api/map/baidu/show.php?auth='.$map_auth.'" style="width:100%;height:'.$map_height.'px;" scrolling="no" frameborder="0"></iframe>';
}
?>