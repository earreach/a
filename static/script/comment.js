/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function show_comment() {
	if($('#comment_main').html().toLowerCase().indexOf('<div>')!=-1) $('#comment_main').html('<if'+'rame src="'+DTPath+'api/comment'+DTExt+'?mid='+module_id+'&itemid='+item_id+'" name="destoon_comment" id="des'+'toon_comment" style="width:100%;height:330px;" scrolling="no" frameborder="0"></if'+'rame>');
}
$(function(){
	var cmh2 = $(window).height();
	var cmh1 = $('#comment_count').offset().top;
	if(cmh1 < cmh2) {
		show_comment();
	} else {
		$(window).on("scroll.comment", function() {
			if($(document).scrollTop() + cmh2 >= cmh1) {
				show_comment();
				$(window).off("scroll.comment");
			}
		});
	}
	$('#comment_div').mouseover(function() {
		show_comment();
	});
});