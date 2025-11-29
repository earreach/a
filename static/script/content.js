/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function fontZoom(z, i) {
	var i = i ? i : 'content';
	var size = $('#'+i).css('font-size');
	var new_size = Number(size.replace('px', ''));
	new_size += z == '+' ? 1 : -1;
	if(new_size < 12) new_size = 12;
	$('#'+i).css('font-size', new_size+'px');
	$('#'+i+' *').css('font-size', new_size+'px');
}
$(function(){
	$(content_id ? '#'+content_id+' img' : 'img').each(function(i){
		var m = img_max_width ? img_max_width : 550;
		var w = $(this).width();
		if(w >= m) {
			$(this).css({'width':m+'px','height':parseInt($(this).height()*m/w)+'px'});
			$(this).attr('title', L['click_open']);
			$(this).click(function(){window.open(DTPath+'api/view'+DTExt+'?img='+$(this).attr('src'));});
		} else if(w == 0) {
			//$(this).css({'display':'none'});
		}
	});
	if(CKDomain) {
		$('#'+(content_id ? content_id : 'content')+' a').each(function(i){
			var u = $(this).attr('href');
			if(cutstr(u, '://', '/').indexOf(CKDomain) == -1 && cutstr(u, '', '?').indexOf('javascript:') == -1) {
				$(this).attr('target', '_blank');
				$(this).attr('href', DTPath+'api/redirect'+DTExt+'?url='+encodeURIComponent(u));
			}
		});
	}
	Dlight();
});