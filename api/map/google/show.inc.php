<?php
defined('IN_DESTOON') or exit('Access Denied');
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
<script type="text/javascript" src="//maps.google.com/maps/api/js?key=<?php echo $map_key;?>"></script>
<script type="text/javascript">
var map;
var point;
var geocoder;
$(function() {
	<?php if($DT_PC) { ?>
	<?php } else { ?>
	$('#dmap').css('height', ($(window).height()-48)+'px');
	<?php } ?>
	point = new google.maps.LatLng(<?php echo $map;?>);
	var myOptions = {
		zoom: 17,
		center: point,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById('dmap'), myOptions);
	<?php if($mapmid) { ?>
			var marker = new google.maps.Marker({
				map: map,
				position: point
			});
			var infowindow = new google.maps.InfoWindow({
				content: '<div style="padding:6px 12px;line-height:20px;"><b style="font-size:14px;"><?php echo $company;?></b><br/><?php echo $address;?></div>'
			});
			infowindow.open(map, marker);
	<?php } else { ?>
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({
			'address': '<?php echo $address;?>'
		}, function(results, status) {
			if(status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
				var infowindow = new google.maps.InfoWindow({
					content: '<div style="padding:6px 12px;line-height:20px;"><b style="font-size:14px;"><?php echo $company;?></b><br/><?php echo $address;?></div>'
				});
				infowindow.open(map, marker);
			} else {
				alert("Map Error:" + status);
			}
		});
	<?php } ?>
});
</script>
<?php
} else {
	echo '<iframe src="'.DT_PATH.'api/map/google/show.php?auth='.$map_auth.'" style="width:100%;height:'.$map_height.'px;" scrolling="no" frameborder="0"></iframe>';
}
?>