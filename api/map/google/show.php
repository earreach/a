<?php
require '../../../common.inc.php';
include DT_ROOT.'/api/map/google/config.inc.php';
$auth = isset($auth) ? decrypt($auth, DT_KEY.'MAP') : '';
substr_count($auth, '|') == 2 or exit;
list($address, $company, $map) = explode('|', $auth);
is_lnglat($map) or $map = $map_mid;
($company && $address) or exit;
?>
<!doctype html>
<html>
<head>
<meta charset="<?php echo DT_CHARSET;?>"/>
<meta name="viewport" content="initial-scale=1.0,user-scalable=no"/>
<title><?php echo $address.$DT['seo_delimiter'].$company.$DT['seo_delimiter'];?>Google Map</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0px;padding:0px}
</style>
</head>
<body>
<div id="dmap" style="width:100%;height:100%;"></div>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $map_key;?>"></script>
<script type="text/javascript">
var map;
var point;
var geocoder;
$(function(){
	geocoder = new google.maps.Geocoder();
	point = new google.maps.LatLng(<?php echo $map;?>);
	var myOptions = {
		zoom: 17,
		center: point,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById('dmap'), myOptions);
	marker = new google.maps.Marker({
		map: map,
		position: point
	});
	var infowindow = new google.maps.InfoWindow({
		content: '<div style="padding:6px 12px;line-height:20px;"><b style="font-size:14px;"><?php echo $company;?></b><br/><?php echo $address;?></div>'
	});
	infowindow.open(map, marker);
});
</script>
<noscript>The map requires javascript to be enabled.</noscript>
</body>
</html>