<?php
require '../../../common.inc.php';
include DT_ROOT.'/api/map/baidu/config.inc.php';
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
<title><?php echo $address.$DT['seo_delimiter'].$company.$DT['seo_delimiter'];?>百度地图</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0;}
#dmap{height:100%;}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=<?php echo $map_key;?>"></script>
</head>
<body>
<div id="dmap"></div>
<script type="text/javascript">
var map = new BMap.Map("dmap");
var point = new BMap.Point(<?php echo $map;?>);
map.centerAndZoom(point,16);
map.enableScrollWheelZoom(true);
map.addControl(new BMap.NavigationControl());
map.addControl(new BMap.ScaleControl());
map.addOverlay(new BMap.Marker(point));
var opts = {
  width : 240,
  height: 40,
  title : "<b style=\"font-size:14px;\"><?php echo $company;?><\/b>"
}
var infoWindow = new BMap.InfoWindow("<span style=\"font-size:12px;\"><?php echo $address;?><\/span>", opts);
map.openInfoWindow(infoWindow, map.getCenter());
</script>
</body>
</html>