<?php
require '../../../common.inc.php';
login();
include DT_ROOT.'/api/map/baidu/config.inc.php';
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
<title>百度地图 - 双击标注位置</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0;font-size:12px;}
td{font-size:12px;}
#dmap{height:100%}
#panel {position:fixed;z-index:999999;left:12px;top:12px;overflow:hidden;background:#FFFFFF;width:280px;}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=<?php echo $map_key;?>"></script>
</head>
<body>
<div id="dmap"></div>
<div id="panel">
	<div id="r-result"><input type="text" id="suggestId" placeholder="输入地名" ondblclick="window.location.reload();" style="width:216px;height:30px;line-height:30px;padding:0 6px;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;"/><input id="search" type="button" class="btn" value="搜索" onclick="document.getElementById('suggestId').focus();" style="width:48px;height:32px;line-height:32px;padding:0;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;background:#0679D4;color:#FFFFFF;"/></div>
	<div id="searchResultPanel" style="border:1px solid #C0C0C0;width:278px;height:auto; display:none;"></div>
</div>

<script type="text/javascript">
var map = new BMap.Map("dmap");
var point = new BMap.Point(<?php echo $map;?>);
map.centerAndZoom(point, 14);
map.enableScrollWheelZoom(true);
map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));
map.addControl(new BMap.ScaleControl({anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));
map.addControl(new BMap.CityListControl({anchor: BMAP_ANCHOR_TOP_RIGHT,offset: BMap.Size(50, 30)}));
map.addOverlay(new BMap.Marker(point));
function ZoomControl() {
	this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
	this.defaultOffset = new BMap.Size(10, 10);
} 
ZoomControl.prototype = new BMap.Control();
var myZoomCtrl = new ZoomControl();
map.addControl(myZoomCtrl);
function Dclick() {
	map.addEventListener("dblclick", function(e){
		try {
			window.parent.document.getElementById('map').value = e.point.lng+','+e.point.lat;
			window.parent.cDialog();
		} catch(e) {}
	});
}
Dclick();
<?php if($map == $map_mid) { ?>
// 自动定位
var localCity = new BMap.LocalCity();
localCity.get(function (r) {
	map.centerAndZoom(r.center, 14);
	Dclick();
});
<?php } ?>
<?php if($map_key) { ?>
// 增加检索框 https://developer.baidu.com/map/jsdemo.htm#webgl0_5
    var ac = new BMap.Autocomplete({"input" : "suggestId","location" : map}); 
    ac.addEventListener("onhighlight", function(e) {
		var str = "";
        var _value = e.fromitem.value;
        var value = "";
        if (e.fromitem.index > -1) {
            value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        }
        str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value; 
        value = "";
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        }
        str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
        document.getElementById("searchResultPanel").innerHTML = str;
    }); 
    var myValue;
    ac.addEventListener("onconfirm", function(e) {
    var _value = e.item.value;
        myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
        document.getElementById("searchResultPanel").innerHTML ="onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue; 
        setPlace();
    }); 
    function setPlace(){
        map.clearOverlays();
        function myFun(){
            var pp = local.getResults().getPoi(0).point;
            map.centerAndZoom(pp, 14);
            map.addOverlay(new BMap.Marker(pp));
			var label = new BMap.Label('<div onclick="window.parent.cDialog();window.parent.document.getElementById(\'map\').value=this.innerHTML;" style="background:#007AFF;color:#FFFFFF;border-radius:6px;padding:6px;cursor:pointer;" title="点击标注">'+pp.lng+','+pp.lat+'<\/div>', {position : pp,offset : new BMap.Size(-80, -55)});
			label.setStyle({borderColor : "#FFFFFF"});
			map.addOverlay(label); 
			try {window.parent.document.getElementById('map').value = pp.lng+','+pp.lat;} catch(e) {}
        }
        var local = new BMap.LocalSearch(map, {onSearchComplete: myFun});
        local.search(myValue);
		Dclick();
    }
<?php } ?>
</script>
</body>
</html>