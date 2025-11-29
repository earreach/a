<?php
require '../../../common.inc.php';
include DT_ROOT.'/api/map/amap/config.inc.php';
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
<title><?php echo $address.$DT['seo_delimiter'].$company.$DT['seo_delimiter'];?>高德地图</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0;}
#dmap{height:100%;}
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
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key=<?php echo $map_key;?>"></script>
</head>
<body>
<div id="dmap"></div>
<script type="text/javascript">
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
</script>
</body>
</html>