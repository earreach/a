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
<script type="text/javascript" src="//api.tianditu.gov.cn/api?v=4.0&tk=<?php echo $map_key;?>"></script>
<script type="text/javascript">
var map;
$(function() {
	<?php if($DT_PC) { ?>
	<?php } else { ?>
	$('#dmap').css('height', ($(window).height()-48)+'px');
	<?php } ?>
	map = new T.Map('dmap');
	map.centerAndZoom(new T.LngLat(<?php echo $map;?>), 16);
	control = new T.Control.Zoom();
	map.addControl(control);
	map.clearOverLays();
	<?php if($mapmid) { ?>
			var marker = new T.Marker(new T.LngLat(<?php echo $map;?>));
			map.addOverLay(marker);
			var markerInfoWin = new T.InfoWindow('<div style="padding:6px 12px;line-height:20px;"><b style="font-size:14px;"><?php echo $company;?></b><br/><?php echo $address;?></div>');
			marker.openInfoWindow(markerInfoWin);
	<?php } else { ?>
		geocoder = new T.Geocoder();
		geocoder.getPoint('<?php echo $address;?>', function(result) {
			if(result.getStatus() == 0){
				map.panTo(result.getLocationPoint(), 16);
				var marker = new T.Marker(result.getLocationPoint());
				map.addOverLay(marker);
				var markerInfoWin = new T.InfoWindow('<div style="padding:6px 12px;line-height:20px;"><b style="font-size:14px;"><?php echo $company;?></b><br/><?php echo $address;?></div>');
				marker.openInfoWindow(markerInfoWin);
			} else {
				alert(result.getMsg());
			}
		});
	<?php } ?>
});
</script>
<?php
} else {
	echo '<iframe src="'.DT_PATH.'api/map/td/show.php?auth='.$map_auth.'" style="width:100%;height:'.$map_height.'px;" scrolling="no" frameborder="0"></iframe>';
}
?>