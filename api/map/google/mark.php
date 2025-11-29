<?php
#https://developers.google.com/maps/documentation/javascript/examples/places-searchbox#maps_places_searchbox-javascript
require '../../../common.inc.php';
login();
include DT_ROOT.'/api/map/google/config.inc.php';
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
<title>Google Map - Ë«»÷±ê×¢Î»ÖÃ</title>
<style type="text/css">
#dmap {
  height: 100%;
}
/* 
 * Optional: Makes the sample page fill the window. 
 */
html,
body {
  height: 100%;
  margin: 0;
  padding: 0;
}
#description {
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
}

#infowindow-content .title {
  font-weight: bold;
}

#infowindow-content {
  display: none;
}

#dmap #infowindow-content {
  display: inline;
}

.pac-card {
  background-color: #fff;
  border: 0;
  border-radius: 2px;
  box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
  margin: 10px;
  padding: 0 0.5em;
  font: 400 18px Roboto, Arial, sans-serif;
  overflow: hidden;
  font-family: Roboto;
  padding: 0;
}

#pac-container {
  padding-bottom: 12px;
  margin-right: 12px;
}

.pac-controls {
  display: inline-block;
  padding: 5px 11px;
}

.pac-controls label {
  font-family: Roboto;
  font-size: 13px;
  font-weight: 300;
}

#pac-input {
  background-color: #fff;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  margin-left: 12px;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 400px;
}

#pac-input:focus {
  border-color: #4d90fe;
}

#title {
  color: #fff;
  background-color: #4d90fe;
  font-size: 25px;
  font-weight: 500;
  padding: 6px 12px;
}

#target {
  width: 345px;
}
</style>
</head>
<body>
<div id="dmap" style="width:100%;height:100%;"></div>
<input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $map_key;?>&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
<script type="text/javascript">
var map;
var point;
var geocoder;
var marker;
function setlatlng(ll) {
	var latlng = new String(ll);
	latlng = latlng.replace(', ', ',');
	window.parent.document.getElementById('dmap').value = latlng.substring(1, latlng.length-1);
	window.parent.cDialog();
}
function initAutocomplete() {
	<?php if($map) { ?>
	point = new google.maps.LatLng(<?php echo $map;?>);
	var myOptions = {
		zoom: 17,
		center: point,
		mapTypeId: "roadmap",
		//mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById('dmap'), myOptions);
	marker = new google.maps.Marker({
		position: point,
		map: map
	});
	marker.setDraggable(true);
	google.maps.event.addListener(marker, 'click', function(e) {
		setlatlng(marker.getPosition());
		return false;
	});

	// Create the search box and link it to the UI element.
	var input = document.getElementById("pac-input");
	var searchBox = new google.maps.places.SearchBox(input);

	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
		// Bias the SearchBox results towards current map's viewport.
		map.addListener("bounds_changed", () => {
		searchBox.setBounds(map.getBounds());
	});
	let markers = [];

	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener("places_changed", () => {
		var places = searchBox.getPlaces();

		if (places.length == 0) {
			return;
		}

		// Clear out the old markers.
		markers.forEach((marker) => {
			marker.setMap(null);
		});
		markers = [];

		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();

		places.forEach((place) => {
			if (!place.geometry || !place.geometry.location) {
				console.log("Returned place contains no geometry");
				return;
			}

			var icon = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25),
			};

			// Create a marker for each place.
			markers.push(
				new google.maps.Marker({
					map,
					icon,
					title: place.name,
					position: place.geometry.location,
				}),
			);
			if (place.geometry.viewport) {
				// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}
		});
		map.fitBounds(bounds);
	});

	<?php } else { ?>
	geocoder = new google.maps.Geocoder();
	var myOptions = {
		zoom: 17,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById('dmap'), myOptions);
	geocoder.geocode({
		'address': '<?php echo ip2area(DT_IP, 2);?>'
	}, function(results, status) {
		if(status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			marker = new google.maps.Marker({
				map: map,
				position: results[0].geometry.location
			});
			marker.setDraggable(true);
			google.maps.event.addListener(marker, 'click', function(e) {
				setlatlng(marker.getPosition());
				return false;
			});
		} else {
			console.log(results);
		}
	});
	<?php } ?>
	google.maps.event.addListener(map, 'dblclick', function(e) {
		setlatlng(e.latLng);
		return false;
	});
}
window.initAutocomplete = initAutocomplete;
</script>
<noscript><center>The map requires javascript to be enabled.</center></noscript>
</body>
</html>