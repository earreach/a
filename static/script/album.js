/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function Album(id) {
	var t = $('#thumbs img').length;
	for(var i=0; i<t; i++) {Dd('tbs_'+i).className = i==id ? 'ab_on' : 'ab_im';}
	Dd('mid_pic').src = $('#tbs_'+id).attr('src').replace('.thumb.', '.middle.');
}
function SAlbum() {
	s = Dd('mid_pic').src;
	if(s.indexOf('nopic.') != -1) return;
	if(s.indexOf('.middle.') != -1) s = s.substring(0, s.length-8-ext(s).length);
	Dd('big_pic').src = s;
	Ds('big_div');
	Ds('zoomer');
}
function HAlbum() {Dh('zoomer');Dh('big_div');}
function VAlbum(o) {if($('#mid_pic').attr('src').indexOf('nopic.')==-1) View($('#mid_pic').attr('src'));}
function PAlbum() {
	var l = parseInt($('#thumbs').css('margin-left').match(/\d+/));
	if(l > 0) {
		$('#thumbs').css('margin-left', '-'+(l-70)+'px');
		$('#tbsn').attr('src', $('#tbsn').attr('src').replace('next-0', 'next-1'));
	}	
	if(l <= 70) $('#tbsp').attr('src', $('#tbsp').attr('src').replace('prev-1', 'prev-0'));
}
function NAlbum() {
	var l = parseInt($('#thumbs').css('margin-left').match(/\d+/));
	var m = ($('#thumbs img').length - 5)*70;
	if(l < m) {
		$('#thumbs').css('margin-left', '-'+(l+70)+'px');
		$('#tbsp').attr('src', $('#tbsp').attr('src').replace('prev-0', 'prev-1'));
	}	
	if(l >= m - 70) $('#tbsn').attr('src', $('#tbsn').attr('src').replace('next-1', 'next-0'));
	
}
function APlay(v) {
	$('#ab-video').html(player(v,400,300,1));
	$('#ab-video,.ab_hide').show();
}
function AHide() {
	$('#ab-video').children().remove();
	$('#ab-video,.ab_hide').hide();
}
$(function(){
	$('#zoomer').hide();
	var AL = $('#mid_div').offset().left + 1;
	var AT = $('#mid_div').offset().top + 1;
	var ZW = $('#zoomer').width();
	var ZH = $('#zoomer').height();
	var PW = $('#mid_pic').width();
	var PH = $('#mid_pic').height();
	$('#mid_div').on('mousemove',function(e){
		var l,t,ll,tt;
		eX = e.clientX;
		var pl = ($('#big_pic').width() - $('#big_div').width())/(PW - ZW);
		if(eX <= AL + ZW/2) {
			l = AL;
			ll = 0;
		} else if(eX >= AL + (PW - ZW/2)) {
			l = AL + PW - ZW;
			ll = $('#big_div').width() - $('#big_pic').width();
		} else {
			l = eX - ZW/2;
			ll = parseInt((AL - eX + ZW/2) * pl);
		}
		if($('#big_pic').width() < $('#big_div').width()) ll = 0;
		eY = e.clientY + $(document).scrollTop();
		var pt = ($('#big_pic').height() - $('#big_div').height())/(PH - ZH);
		if(eY <= AT + ZH/2) {
			t = AT;
			tt = 0;
		} else if(eY >= AT + (PH - ZH/2)) {
			t = AT + PH - ZH;
			tt = $('#big_div').height() - $('#big_pic').height();
		} else {
			t = eY - ZH/2;
			tt =  parseInt((AT - eY + ZH/2) * pt);
		}
		if($('#big_pic').height() < $('#big_div').height()) tt = 0;
		$('#zoomer').css({'left':l + 'px','top':t + 'px'});
		$('#big_pic').css({'left':ll + 'px','top':tt + 'px'});
	});
});