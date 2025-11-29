/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
var area_id;
function load_area(areaid, id) {
	area_id = id; area_areaid[id] = areaid;
	console.log(1122211)
	console.log(AJPath)
	// console.log(data)
	// console.log($("shuxingid_"+$shuxing_id).val())
	$.post(AJPath, 'action=area&area_title='+area_title[id]+'&area_extend='+area_extend[id]+'&area_deep='+area_deep[id]+'&area_id='+area_id+'&areaid='+areaid, function(data) {
		$('#areaid_'+area_id).val(area_areaid[area_id]);
		if(data) {$('#load_area_'+area_id).html(data)
		console.log(data)
		};
	});
}