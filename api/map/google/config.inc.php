<?php
#https://developers.google.com/maps/documentation/javascript/tutorial
$map_mid = '39.92184916337801,116.39190673828125';
$map_key = '';
function is_latlng($map) {
	return preg_match("/^[0-9\.\,\-]{10,50}$/", $map);
}
?>