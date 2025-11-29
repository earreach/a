/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
var shuxing_id;
// console.log(AJPath)
// http://b.com/ajax.php
/*
#define('DT_ADMIN', true);
require 'common.inc.php';
if($DT_BOT) dhttp(403);
if($action != 'mobile') {
	//check_referer() or exit;
}
// var_dump($action);

require DT_ROOT.'/include/post.func.php';
// die();
@include DT_ROOT.'/api/ajax/'.$action.'.inc.php';
*/

function load_shuxing(shuxingid, id) {
	shuxing_id = id; shuxing_shuxingid[id] = shuxingid;
	console.log(AJPath)
	console.log(shuxing_title[id])
	console.log(1155)
	console.log(shuxing_extend[id])
	console.log(1155)
	console.log(shuxing_deep[id])
	console.log(1155)



	console.log(shuxing_id+'&shuxingid='+shuxingid)
	console.log(1155)
	console.log($('#shuxingid_'+shuxing_id).val())
	console.log(1122222222222222222222222255)
	console.log(shuxing_shuxingid[shuxing_id])
	console.log(1155)
	console.log("data")
	// console.log(data)
	// '&shuxing_moduleid='+shuxing_moduleid[id]+  加了也没用
	$.post(AJPath, 'action=shuxing&shuxing_title='+shuxing_title[id]+'&shuxing_extend='+shuxing_extend[id]+'&shuxing_deep='+shuxing_deep[id]+'&shuxing_id='+shuxing_id+'&shuxingid='+shuxingid, function(data) {
		$('#shuxingid_'+shuxing_id).val(shuxing_shuxingid[shuxing_id]);
		if(data) {$('#load_shuxing_'+shuxing_id).html(data)
		console.log(data)
		};
	});
}