<?php
require '../../../common.inc.php';
login();
include DT_ROOT.'/api/map/qq/config.inc.php';
$map = isset($map) ? $map : '';
if(!is_lnglat($map) && $DT['lnglat_appcode']) {	
	$user = userinfo($_username);
	$address = $user['areaid'] ? area_pos($user['areaid'], '').$user['address'] : ip2area(DT_IP, 2);
	if($address) $map = cloud_lnglat($address, $DT['lnglat_appcode'], 1);
}
is_lnglat($map) or $map = $map_mid;
?>
<!doctype html>
<html>
<head>
<meta charset="<?php echo DT_CHARSET;?>"/>
<meta name="viewport" content="initial-scale=1.0,user-scalable=no"/>
<title>腾讯地图 - 双击标注位置</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0}
#container{height:100%;}
#panel {position:fixed;z-index:999999;left:12px;top:12px;overflow:hidden;background:#FFFFFF;width:280px;}
#suggestionList {
	list-style-type: none;
	padding:0;
	margin: 0;
}    
#suggestionList li a {
	margin-top: -1px; 
	background-color: #FFFFFF;  
	text-decoration: none;
	font-size: 14px; 
	color: black; 
	display: block; 
	padding:6px 10px;
}
#suggestionList li .item_info{
	padding-left:6px;
	font-size: 12px;
	color:#999999;
	
}
#suggestionList li a:hover:not(.header) {
	background-color: #eee;
}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="https://map.qq.com/api/gljs?v=1.exp&libraries=service&key=<?php echo $map_key;?>"></script>
<script type="text/javascript">
var center;
var map;
var marker;
var suggestionList = [];
var infoWindowList = Array(10);
var search;
var suggest;
var markers;
function locate() {
	//https://lbs.qq.com/webDemoCenter/glAPI/glServiceLib/ipLocation
	var ipInput = '<?php echo $DT_IP;?>';
	var params = ipInput ? { ip: ipInput } : {};
	var ipLocation = new TMap.service.IPLocation(); // 新建一个IP定位类
	var markers = new TMap.MultiMarker({
		map: map,
		geometries: [],
	});
	ipLocation
	.locate(params)
	.then((result2) => {
	// 未给定ip地址则默认使用请求端的ip
		var { result } = result2;
		markers.updateGeometries([
		{
			id: 'container',
			position: result.location, // 将所得位置绘制在地图上
		},
		]);
		map.setCenter(result.location);
	})
	.catch((error) => {
		console.log(error);
	});
}


function setSuggestion(index) {
  // 点击输入提示后，于地图中用点标记绘制该地点，并显示信息窗体，包含其名称、地址等信息
  infoWindowList.forEach((infoWindow) => {
    infoWindow.close();
  });
  infoWindowList.length = 0;
  document.getElementById('keyword').value = suggestionList[index].title;
  document.getElementById('suggestionList').innerHTML = '';
  markers.setGeometries([]);
  markers.updateGeometries([
    {
      id: '0', // 点标注数据数组
      position: suggestionList[index].location,
    },
  ]);
  var infoWindow = new TMap.InfoWindow({
    map: map,
    position: suggestionList[index].location,
    content: `<h3>${suggestionList[index].title}</h3><p>地址：${suggestionList[index].address}</p>`,
    offset: { x: 0, y: -50 },
  });
  infoWindowList.push(infoWindow);
  map.setCenter(suggestionList[index].location);
  markers.on('click', (e) => {
    infoWindowList[Number(e.geometry.id)].open();
  });
}

function searchByKeyword() {
  // 关键字搜索功能
  infoWindowList.forEach((infoWindow) => {
    infoWindow.close();
  });
  infoWindowList.length = 0;
  markers.setGeometries([]);
  search
    .searchRectangle({
      keyword: document.getElementById('keyword').value,
      bounds: map.getBounds(),
    })
    .then((result) => {
      result.data.forEach((item, index) => {
        var geometries = markers.getGeometries();
        var infoWindow = new TMap.InfoWindow({
          map: map,
          position: item.location,
          content: `<h3>${item.title}</h3><p>地址：${item.address}</p><p>电话：${item.tel}</p>`,
          offset: { x: 0, y: -50 },
        });
        infoWindow.close();
        infoWindowList[index] = infoWindow;
        geometries.push({
          id: String(index),
          position: item.location,
        });
        markers.updateGeometries(geometries);
        markers.on('click', (e) => {
          infoWindowList[Number(e.geometry.id)].open();
        });
      });
    });
}

function getSuggestions() {
  // 使用者在搜索框中输入文字时触发
  var suggestionListContainer = document.getElementById('suggestionList');
  suggestionListContainer.innerHTML = '';
  var keyword = document.getElementById('keyword').value;
  if (keyword) {
    suggest
      .getSuggestions({ keyword: keyword, location: map.getCenter() })
      .then((result) => {
        // 以当前所输入关键字获取输入提示
        suggestionListContainer.innerHTML = '';
        suggestionList = result.data;
        suggestionList.forEach((item, index) => {
          suggestionListContainer.innerHTML += `<li><a href="#" onclick="setSuggestion(${index})">${item.title}<span class="item_info">${item.address}</span></a></li>`;
        });
      })
      .catch((error) => {
        console.log(error);
      });
  }
}

var init = function() {
	center = new TMap.LatLng(<?php echo $map;?>);
    map = new TMap.Map(document.getElementById('container'),{
        center:center,
        zoom:15
    });
	//https://lbs.qq.com/webDemoCenter/glAPI/glMarker/sampleMarker
	marker = new TMap.MultiMarker({
	map: map,
	styles: {
	  // 点标记样式
	  marker: new TMap.MarkerStyle({
		width: 20, // 样式宽
		height: 30, // 样式高
		anchor: { x: 10, y: 30 }, // 描点位置
	  }),
	},
	geometries: [
	  // 点标记数据数组
	  {
		// 标记位置(纬度，经度，高度)
		position: center,
		id: 'marker',
	  },
	],
  });
<?php if($map == $map_mid) { ?>
    locate();
<?php } ?>
	map.on("dblclick", function (event) {
		var xy = event.latLng.getLat()+','+event.latLng.getLng();
		window.parent.document.getElementById('map').value = xy;
		window.parent.cDialog();
	});
	//https://lbs.qq.com/webDemoCenter/glAPI/glServiceLib/suggestion
	search = new TMap.service.Search({ pageSize: 10 }); // 新建一个地点搜索类
	suggest = new TMap.service.Suggestion({
	  // 新建一个关键字输入提示类
	  pageSize: 10, // 返回结果每页条目数
	  region: '', // 限制城市范围
	  regionFix: false, // 搜索无结果时是否固定在当前城市
	});
	markers = new TMap.MultiMarker({
	  map: map,
	  geometries: [],
	});
}
window.onload = init;
</script>
</head>
<body>
<div id="container"></div>
<div id="panel">
<input id="keyword" type="text" placeholder="输入地名" oninput="getSuggestions();" ondblclick="window.location.reload();" style="width:216px;height:30px;line-height:30px;padding:0 6px;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;"/><input id="search" type="button" class="btn" value="搜索" onclick="searchByKeyword()" style="width:48px;height:32px;line-height:32px;padding:0;margin:0;outline:none;vertical-align:middle;border:#0679D4 1px solid;background:#0679D4;color:#FFFFFF;"/>
<ul id="suggestionList">
</ul>
</div>
</body>
</html>