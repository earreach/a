<?php
require '../../../common.inc.php';
login();
include DT_ROOT.'/api/map/amap/config.inc.php';
$map = isset($map) ? $map : '';
if(!is_lnglat($map) && $DT['lnglat_appcode']) {	
	$user = userinfo($_username);
	$address = $user['areaid'] ? area_pos($user['areaid'], '').$user['address'] : ip2area(DT_IP, 2);
	if($address) $map = cloud_lnglat($address, $DT['lnglat_appcode'], 0);
}
is_lnglat($map) or $map = $map_mid;
?>
<!doctype html>
<html>
<head>
<meta charset="<?php echo DT_CHARSET;?>"/>
<meta name="viewport" content="initial-scale=1.0,user-scalable=no"/>
<title>高德地图 - 双击标注位置</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0;font-size:12px;}
td{font-size:12px;}
#dmap{height:100%}
#panel {position:fixed;z-index:999999;left:12px;top:12px;overflow:hidden;background:#FFFFFF;width:280px;}
#res {
	position: absolute;
	background-color: white;
	max-height: 90%;
	overflow-y: auto;
	top: 10px;
	right: 10px;
	width: 280px;
}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="https://webapi.amap.com/maps?v=2.0&key=<?php echo $map_key;?>&plugin=AMap.AutoComplete,AMap.CitySearch,AMap.ToolBar"></script>
<script type="text/javascript" src="https://cache.amap.com/lbs/static/addToolbar.js"></script>
</head>
<body>
<div id="dmap"></div>
<div id="panel">
	<div id="r-result"><input type="text" id="tipinput" placeholder="输入地名" ondblclick="window.location.reload();" style="width:216px;height:30px;line-height:30px;padding:0 6px;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;"/><input id="search" type="button" class="btn" value="搜索" onclick="document.getElementById('tipinput').focus();" style="width:48px;height:32px;line-height:32px;padding:0;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;background:#0679D4;color:#FFFFFF;"/></div>
	<div id="res"></div>
</div>

<script type="text/javascript">
function Dclick() {
	//https://lbs.amap.com/api/javascript-api-v2/guide/map/map-bind
	map.on("dblclick", function (ev) {
		try {
			window.parent.document.getElementById('map').value = ev.lnglat.lng+','+ev.lnglat.lat;
			window.parent.cDialog();
		} catch(e) {}
	});
}
var map = new AMap.Map("dmap", {
  viewMode: '2D', //默认使用 2D 模式
  zoom: 13, //地图级别
  center: [<?php echo $map;?>], //地图中心点
});
AMap.plugin('AMap.AutoComplete',function(){ 
	//输入提示 https://lbs.amap.com/demo/javascript-api-v2/example/input/input-prompt
	var auto = new AMap.AutoComplete({
		input: "tipinput"
	});
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
Dclick();
<?php if($map == $map_mid) { ?>
//IP城市定位 https://lbs.amap.com/demo/javascript-api-v2/example/location/get-city-name-by-ip-location
//实例化城市查询类
AMap.plugin('AMap.CitySearch',function(){ 
	var citysearch = new AMap.CitySearch();
	//自动获取用户IP，返回当前城市
	citysearch.getLocalCity(function(status, result) {
		if (status === 'complete' && result.info === 'OK') {
			if (result && result.city && result.bounds) {
				var cityinfo = result.city;
				var citybounds = result.bounds;
				//地图显示当前城市
				map.setBounds(citybounds);
				Dclick();
			}
		}
	});
});
<?php } ?>
</script>
</body>
</html>