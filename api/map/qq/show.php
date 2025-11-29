<?php
require '../../../common.inc.php';
include DT_ROOT.'/api/map/qq/config.inc.php';
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
<title><?php echo $address.$DT['seo_delimiter'].$company.$DT['seo_delimiter'];?>腾讯地图</title>
<style type="text/css">
html{height:100%;overflow:hidden;}
body{height:100%;margin:0;padding:0}
#dmap{height:100%;}
</style>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="https://map.qq.com/api/gljs?v=1.exp&key=<?php echo $map_key;?>"></script>
<script type="text/javascript">
var init = function() {
	var center = new TMap.LatLng(<?php echo $map;?>);
    var map = new TMap.Map(document.getElementById("dmap"),{
        center:center,
        zoom:16
    });
    var InfoWindow = new TMap.InfoWindow({
        position: center,
        map: map,
        content:'<div style="line-height:20px;text-align:left;"><b style="font-size:14px;"><?php echo $company;?></b><br/><span style="font-size:12px;color:#666666;"><?php echo $address;?></span></div>'
    });
}
window.onload = init;
</script>
</head>
<body>
<div id="dmap"></div>
</body>
</html>