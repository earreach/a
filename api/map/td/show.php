<?php
require '../../../common.inc.php';
include DT_ROOT.'/api/map/td/config.inc.php';
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
<title><?php echo $address.$DT['seo_delimiter'].$company.$DT['seo_delimiter'];?>天地图</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0;}
#dmap{height:100%;}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script src="//api.tianditu.gov.cn/api?v=4.0&tk=<?php echo $map_key;?>" type="text/javascript"></script>
</head>
<body>
<div id="dmap"></div>
<script type="text/javascript">
var map;
var init = function() {
	map = new T.Map('dmap');
	map.centerAndZoom(new T.LngLat(<?php echo $map;?>), 16);
	control = new T.Control.Zoom();
	map.addControl(control);
	var point = new T.LngLat(<?php echo $map;?>);
	marker = new T.Marker(point);
	map.addOverLay(marker);
	var markerInfoWin = new T.InfoWindow('<div style="padding:6px 12px;line-height:20px;"><b style="font-size:14px;"><?php echo $company;?></b><br/><?php echo $address;?></div>');
	marker.openInfoWindow(markerInfoWin);
}
window.onload = init;
</script>
</body>
</html>