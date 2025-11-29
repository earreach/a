<?php
require '../../../common.inc.php';
login();
include DT_ROOT.'/api/map/td/config.inc.php';
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
<title>天地图 - 双击标注位置</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0;font-size:12px;}
td{font-size:12px;}
#dmap{height:100%;}
#dsch{position:fixed;z-index:999999;left:48px;top:12px;overflow:hidden;background:#FFFFFF;width:280px;}
.search {
	border: 1px solid #999999;
}

.ls {
	line-height: 27px;
	padding-left: 7px;
}

.prompt {
	display: none;
	border: 1px solid #999999;
	padding:10px;
	color:#666666;
}

.statistics {
	display: none;
	border: 1px solid #999999;
	overflow-y: scroll;
	height: 150px;
}

.suggests {
	display: none;
	border: 1px solid #999999;
}

;
.lineData {
	display: none;
	border: 1px solid #999999;
}

.result {
	display: none;
	border: 1px solid #999999;
	line-height: 32px;
	padding: 0 10px 10px 10px;
	color:#666666;
}
.result input {width:46px;height:24px;line-height:24px;padding:0;margin:0 2px 0 0;outline:none;vertical-align:middle;border:#999999 1px solid;background:#FFFFFF;}
.result input:hover{background:#F6F6F6;}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script src="//api.tianditu.gov.cn/api?v=4.0&tk=<?php echo $map_key;?>" type="text/javascript"></script>
</head>
<body>
<div id="dmap"></div>
<div id="dsch">
    <input type="text" id="keyWord" value="" placeholder="输入地名" oninput="localsearch.search(document.getElementById('keyWord').value);" ondblclick="window.location.reload();" style="width:218px;height:30px;line-height:30px;padding:0 6px;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;"/><input type="button" onclick="localsearch.search(document.getElementById('keyWord').value);" value="搜索" style="width:48px;height:32px;line-height:32px;padding:0;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;background:#0679D4;color:#FFFFFF;"/>
    <br/>
    <!-- 提示词面板 -->
    <div id="promptDiv" class="prompt"></div>
    <!-- 统计面板 -->
    <div id="statisticsDiv" class="statistics"></div>
    <!-- 建议词面板 -->
    <div id="suggestsDiv" class="suggests"></div>
    <!-- 公交提示面板 -->
    <div id="lineDataDiv" class="lineData"></div>
    <!-- 搜索结果面板 -->
    <div id="resultDiv" class="result">
        <div id="searchDiv"></div>
        <div id="pageDiv">
            <input type="button" value="首页" onClick="localsearch.firstPage()"/>
            <input type="button" value="上页" onClick="localsearch.previousPage()"/>
            <input type="button" value="下页" onClick="localsearch.nextPage()"/>
            <input type="button" value="末页" onClick="localsearch.lastPage()"/>          
            <input type="button" value="关闭" onClick="clearAll()"/>          
            <!-- <br/>转到第<input type="text" value="1" id="pageId" size="3"/>页 <input type="button" onClick="localsearch.gotoPage(parseInt(document.getElementById('pageId').value));" value="转到"/>-->
        </div>
    </div>
</div>

<script type="text/javascript">
//https://lbs.tianditu.gov.cn/api/js4.0/examples.html
var map;
var localsearch;
function localSearchResult(result) {
		//清空地图及搜索列表
		clearAll();

		//添加提示词
		prompt(result);

		//根据返回类型解析搜索结果
		switch (parseInt(result.getResultType())) {
			case 1:
				//解析点数据结果
				pois(result.getPois());
				break;
			case 2:
				//解析推荐城市
				statistics(result.getStatistics());
				break;
			case 3:
				//解析行政区划边界
				area(result.getArea());
				break;
			case 4:
				//解析建议词信息
				suggests(result.getSuggests());
				break;
			case 5:
				//解析公交信息
				lineData(result.getLineData());
				break;
		}
}

//解析提示词
function prompt(obj) {
	var prompts = obj.getPrompt();
	if (prompts) {
		var promptHtml = "";
		for (var i = 0; i < prompts.length; i++) {
			var prompt = prompts[i];
			var promptType = prompt.type;
			var promptAdmins = prompt.admins;
			var meanprompt = prompt.DidYouMean;
			if (promptType == 1) {
				promptHtml += "<p>您是否要在" + promptAdmins[0].name + "</strong>搜索更多包含<strong>" + obj.getKeyword() + "</strong>的相关内容？<p>";
			}
			else if (promptType == 2) {
				promptHtml += "<p>在<strong>" + promptAdmins[0].name + "</strong>没有搜索到与<strong>" + obj.getKeyword() + "</strong>相关的结果。<p>";
				if (meanprompt) {
					promptHtml += "<p>您是否要找：<font weight='bold' color='#035fbe'><strong>" + meanprompt + "</strong></font><p>";
				}
			}
			else if (promptType == 3) {
				promptHtml += "<p style='margin-bottom:3px;'>有以下相关结果，您是否要找：</p>"
				for (i = 0; i < promptAdmins.length; i++) {
					promptHtml += "<p>" + promptAdmins[i].name + "</p>";
				}
			}
		}
		if (promptHtml != "") {
			document.getElementById("promptDiv").style.display = "block";
			document.getElementById("promptDiv").innerHTML = promptHtml;
		}
	}
}

//解析点数据结果
function pois(obj) {
	if (obj) {
		//显示搜索列表
		var divMarker = document.createElement("div");
		//坐标数组，设置最佳比例尺时会用到
		var zoomArr = [];
		for (var i = 0; i < obj.length; i++) {
			//闭包
			(function (i) {
				//名称
				var name = obj[i].name;
				//地址
				var address = obj[i].address;
				//坐标
				var lnglatArr = obj[i].lonlat.split(",");
				var lnglat = new T.LngLat(lnglatArr[0], lnglatArr[1]);

				var winHtml = "名称:" + name + "<br/>地址:" + address;

				//创建标注对象
				var marker = new T.Marker(lnglat);
				//地图上添加标注点
				map.addOverLay(marker);
				//注册标注点的点击事件
				var markerInfoWin = new T.InfoWindow(winHtml, {autoPan: true});
				marker.addEventListener("click", function () {
					marker.openInfoWindow(markerInfoWin);
				});

				zoomArr.push(lnglat);

				//在页面上显示搜索的列表
				var a = document.createElement("a");
				a.href = "javascript://";
				a.innerHTML = name;
				a.onclick = function () {
					showPosition(marker, winHtml);
				}
				divMarker.appendChild(document.createTextNode((i + 1) + "."));
				divMarker.appendChild(a);
				divMarker.appendChild(document.createElement("br"));
			})(i);
		}
		//显示地图的最佳级别
		map.setViewport(zoomArr);
		//显示搜索结果
		divMarker.appendChild(document.createTextNode('共' + localsearch.getCountNumber() + '条记录，分' + localsearch.getCountPage() + '页,当前第' + localsearch.getPageIndex() + '页'));
		document.getElementById("searchDiv").appendChild(divMarker);
		document.getElementById("resultDiv").style.display = "block";
	}
}

//显示信息框
function showPosition(marker, winHtml) {
	var markerInfoWin = new T.InfoWindow(winHtml, {autoPan: true});
	marker.openInfoWindow(markerInfoWin);
}

//解析推荐城市
function statistics(obj) {
	if (obj) {
		//坐标数组，设置最佳比例尺时会用到
		var pointsArr = [];
		var priorityCitysHtml = "";
		var allAdminsHtml = "";
		var priorityCitys = obj.priorityCitys;
		if (priorityCitys) {
			//推荐城市显示
			priorityCitysHtml += "在中国以下城市有结果<ul>";
			for (var i = 0; i < priorityCitys.length; i++) {
				priorityCitysHtml += "<li>" + priorityCitys[i].name + "(" + priorityCitys[i].count + ")</li>";
			}
			priorityCitysHtml += "</ul>";
		}

		var allAdmins = obj.allAdmins;
		if (allAdmins) {
			allAdminsHtml += "更多城市<ul>";
			for (var i = 0; i < allAdmins.length; i++) {
				allAdminsHtml += "<li>" + allAdmins[i].name + "(" + allAdmins[i].count + ")";
				var childAdmins = allAdmins[i].childAdmins;
				if (childAdmins) {
					for (var m = 0; m < childAdmins.length; m++) {
						allAdminsHtml += "<blockquote>" + childAdmins[m].name + "(" + childAdmins[m].count + ")</blockquote>";
					}
				}
				allAdminsHtml += "</li>"
			}
			allAdminsHtml += "</ul>";
		}
		document.getElementById("statisticsDiv").style.display = "block";
		document.getElementById("statisticsDiv").innerHTML = priorityCitysHtml + allAdminsHtml;
	}
}

//解析行政区划边界
function area(obj) {
	if (obj) {
		if(obj.points){
			//坐标数组，设置最佳比例尺时会用到
			var pointsArr = [];
			var points = obj.points;
			for (var i = 0; i < points.length; i++) {
				var regionLngLats = [];
				var regionArr = points[i].region.split(",");
				for (var m = 0; m < regionArr.length; m++) {
					var lnglatArr = regionArr[m].split(" ");
					var lnglat = new T.LngLat(lnglatArr[0], lnglatArr[1]);
					regionLngLats.push(lnglat);
					pointsArr.push(lnglat);
				}
				//创建线对象
				var line = new T.Polyline(regionLngLats, {
					color: "blue",
					weight: 3,
					opacity: 1,
					lineStyle: "dashed"
				});
				//向地图上添加线
				map.addOverLay(line);
			}
			//显示最佳比例尺
			map.setViewport(pointsArr);
		}
		if(obj.lonlat){
			var regionArr = obj.lonlat.split(",");
			map.panTo(new T.LngLat(regionArr[0], regionArr[1]));
		}
	}
}

//解析建议词信息
function suggests(obj) {
	if (obj) {
		//建议词提示，如果搜索类型为公交规划建议词或公交站搜索时，返回结果为公交信息的建议词。
		var suggestsHtml = "建议词提示<ul>";
		for (var i = 0; i < obj.length; i++) {
			suggestsHtml += "<li>" + obj[i].name + "&nbsp;&nbsp;<font style='color:#666666'>" + obj[i].address + "</font></li>";
		}
		suggestsHtml += "</ul>";
		document.getElementById("suggestsDiv").style.display = "block";
		document.getElementById("suggestsDiv").innerHTML = suggestsHtml;
	}
}

//解析公交信息
function lineData(obj) {
	if (obj) {
		//公交提示
		var lineDataHtml = "公交提示<ul>";
		for (var i = 0; i < obj.length; i++) {
			lineDataHtml += "<li>" + obj[i].name + "&nbsp;&nbsp;<font style='color:#666666'>共" + obj[i].stationNum + "站</font></li>";
		}
		lineDataHtml += "</ul>";
		document.getElementById("lineDataDiv").style.display = "block";
		document.getElementById("lineDataDiv").innerHTML = lineDataHtml;
	}
}

//清空地图及搜索列表
function clearAll() {
	map.clearOverLays();
	document.getElementById("searchDiv").innerHTML = "";
	document.getElementById("resultDiv").style.display = "none";
	document.getElementById("statisticsDiv").innerHTML = "";
	document.getElementById("statisticsDiv").style.display = "none";
	document.getElementById("promptDiv").innerHTML = "";
	document.getElementById("promptDiv").style.display = "none";
	document.getElementById("suggestsDiv").innerHTML = "";
	document.getElementById("suggestsDiv").style.display = "none";
	document.getElementById("lineDataDiv").innerHTML = "";
	document.getElementById("lineDataDiv").style.display = "none";
}

var init = function() {
	map = new T.Map('dmap');
	map.centerAndZoom(new T.LngLat(<?php echo $map;?>), 15);
	control = new T.Control.Zoom();
	map.addControl(control);
	var point = new T.LngLat(<?php echo $map;?>);
	marker = new T.Marker(point);
	map.addOverLay(marker);
	<?php if($map == $map_mid) { ?>
	geocoder = new T.Geocoder();
	geocoder.getPoint('<?php echo ip2area(DT_IP, 2);?>', function(result) {
		if(result.getStatus() == 0){
			map.panTo(result.getLocationPoint(), 15);
			var marker = new T.Marker(result.getLocationPoint());
			map.addOverLay(marker);
		} else {
			console.log(result.getMsg());
		}
	});
	<?php } ?>
	map.addEventListener('dblclick', function(e) {
		var xy = e.lnglat.getLng()+','+e.lnglat.getLat();
		window.parent.document.getElementById('map').value = xy;
		window.parent.cDialog();
	});
	var config = {
		pageCapacity: 10,	//每页显示的数量
		onSearchComplete: localSearchResult	//接收数据的回调函数
	};
	//创建搜索对象
	localsearch = new T.LocalSearch(map, config);
}
window.onload = init;
</script>
</body>
</html>