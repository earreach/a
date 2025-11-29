/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function m(i) { try { Dd(i).className = 'tab_on'; } catch(e) {} }
function s(i) { try { Dd(i).className = 'side_b'; } catch(e) {} }
function oh(o) {
	if(o.className == 'side_h') {
		Dh('side');o.className = 'side_s';
		$('.sbt-fix,.btns-fix').css('left', '0');
		set_local('m_side', 'Y');
	} else {
		Ds('side');o.className = 'side_h';
		set_local('m_side', 'N');
		$('.sbt-fix,.btns-fix').css('left', '180px');
	}
}
function Msize() {
	var h1 = $(window).height(),h2 = $('#main').height();
	if(h1 > h2) $('#main').height(h1 - 72);
}