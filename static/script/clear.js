/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
var _sbt = false; var _frm = _frm ? _frm : 'dform';
$('#'+_frm).submit(function() {_sbt = true;});
if(isIE) {
	$(window).bind('unload', function(e) {//beforeunload
		if(!_sbt){$.post(AJPath, 'action=clear');}
	});
} else {
	$(window).on('unload', function(e) {//beforeunload
		if(!_sbt){$.post(AJPath, 'action=clear');}
	});
}