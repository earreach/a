/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function type_reload() {
	if($('#widget-'+js_pageid).html().indexOf('frame') == -1) { 
		$.get(AJPath+'?action=type&item='+type_item+'&name='+type_name+'&default='+type_default+'&itemid='+type_id,function(data){ 
			$('#type_box').html(data);
		});
		clearInterval(type_interval);
	}
}
