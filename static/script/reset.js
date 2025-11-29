/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/

if(window.screen.width<1280) {
	document.write('<style type="text/css">.lt{max-width:200px;}</style>');
} else if(window.screen.width<1367) {
	document.write('<style type="text/css">.lt{max-width:200px;}</style>');
} else if(window.screen.width<1441) {
	document.write('<style type="text/css">.lt{max-width:240px;}</style>');
} else if(window.screen.width<1601) {
	document.write('<style type="text/css">.lt{max-width:320px;}</style>');
} else if(window.screen.width<1921) {
	document.write('<style type="text/css">.lt{max-width:360px;}</style>');
}
$(function(){
	if(window.screen.width<1280) {
		$('body').width(1280);
		$('[data-hide-1200]').hide();
	} else if(window.screen.width<1367) {
		$('[data-hide-1200]').hide();
	} else if(window.screen.width<1441) {
		$('[data-hide-1400]').hide();
	} else if(window.screen.width<1601) {
		$('[data-hide-1600]').hide();
	}
	if(isIE) $('[data-ie]').hide();
});