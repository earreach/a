/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
var UA = navigator.userAgent.toLowerCase();
var isIE = (document.all && window.ActiveXObject && !window.opera) ? true : false;
var isGecko = UA.indexOf('webkit') != -1;
var DMURL = document.location.protocol+'//'+location.hostname+(location.port ? ':'+location.port : '')+'/';
if(DTPath.indexOf(DMURL) != -1) DMURL = DTPath;
var AJPath = DMURL+'ajax'+DTExt;
var UPPath = DMURL+'upload'+DTExt;
if(isIE) try {document.execCommand("BackgroundImageCache", false, true);} catch(e) {}
function Dd(i) {return document.getElementById(i);}
function Ds(i) {$('#'+i).show();}
function Dh(i) {$('#'+i).hide();}
function Dsh(i) {$('#'+i).toggle();}
function Df(i) {Dd(i).focus();}
function Dq(k, v, s) {
	s = s == 0 ? 0 : 1;
	if(k == 'date') {
		var d = v.indexOf('-') == -1 ? v.substring(0, 8) : v.substring(0, 10);
		$('#fromdate').val(d+' 00:00:00');
		$('#todate').val(d+' 23:59:59');
	} else {
		var o = $("#search [name='"+k+"']");
		if(o.length) {
			if(o.attr('type') == 'checkbox') {
				o.attr('checked', v ? true : false);
			} else {
				o.val(v);
			}
		} else { 
			$('#search').prepend('<input type="hidden" name="'+k+'" value="'+v+'"/>'); 
		}
	}
	if(s) $('#search').submit();
}
function Tab(ID) {
	var i = 0;
	while($('#Tab'+i).length > 0) {
		if(ID == i) {$('#Tab'+i).attr('class', 'tab_on');$('#Tabs'+i).show();} else {$('#Tab'+i).attr('class', 'tab');$('#Tabs'+i).hide();}
		i++;
	}
	if($('#tab').length > 0) $('#tab').val(ID);
}
function checkall(f, t) {
	var t = t ? t : 1;
	for(var i = 0; i < f.elements.length; i++) {
		var e = f.elements[i];
		if(e.type != 'checkbox' || !e.name || e.name == 'msg' || e.name == 'eml' || e.name == 'sms' || e.name == 'wec') continue;
		if(t == 1) e.checked = e.checked ? false : true;
		if(t == 2) e.checked = true;
		if(t == 3) e.checked = false;	
	}
}
function Dmsg(str, i, s, t) {
	var t = t ? t : 5; var s = s ? 1 : 0; var h = i == 'content' ? 420 : 180;
	Dtoast(str, '', t);
	try{
		$("html, body").animate({scrollTop:$('#d'+i).offset().top-h}, 100);
		Dd('d'+i).innerHTML = '<img src="'+DTPath+'static/image/check-ko.png" width="16" height="16" align="absmiddle"/> '+str;
		Dd(i).focus();
	}catch(e){}
	window.setTimeout(function(){Dd('d'+i).innerHTML = '';}, t*1000);
}
function Inner(i,s) {try {Dd(i).innerHTML = s;}catch(e){}}
function Go(u) {window.location = u;}
function confirmURI(m,f) {if(confirm(m)) Go(f);}
function showmsg(m, t) {
	var t = t ? t : 5000; var s = (m.indexOf(L['str_delete']) != -1 || m.indexOf(L['str_clear']) != -1) ? 'delete' : 'ok';
	try{Dd('msgbox').style.display = '';Dd('msgbox').innerHTML = m+sound(s);window.setTimeout('closemsg();', t);}catch(e){}
}
function closemsg() {try{Dd('msgbox').innerHTML = '';Dd('msgbox').style.display = 'none';}catch(e){}}
function sound(f) {return '<div style="float:left;" class="destoon-sound"><audio src="'+DTPath+'file/sound/'+f+'.mp3" height="0" width="0" autoplay="autoplay"></audio></div>';}
function Eh(t) {
	var t = t ? t : 'select';
	if(isIE) {
		var arVersion = navigator.appVersion.split("MSIE"); var IEversion = parseFloat(arVersion[1]);		
		if(IEversion >= 7 || IEversion < 5) return;
		$(t).css('visibility', 'hidden');
	}
}
function Es(t) {
	var t = t ? t : 'select';
	if(isIE) {
		var arVersion = navigator.appVersion.split("MSIE"); var IEversion = parseFloat(arVersion[1]);		
		if(IEversion >= 7 || IEversion < 5) return;
		$(t).css('visibility', 'visible');
	}
}
function EditorLen(i) {return EditorAPI(i, 'len');}
function Tb(o) {
	if(o.className == 'on') return;
	var t = o.id.split('-h-'); var p = t[0]; var k = t[1];
	$('#'+p+'-h').children().each(function() {
		var i = $(this).attr('id').replace(p+'-h-', '');
		if(i == k) {
			$('#'+p+'-h-'+i).attr('class', 'on');
			$('#'+p+'-b-'+i).fadeIn(100);
		} else {
			$('#'+p+'-h-'+i).attr('class', '');
			$('#'+p+'-b-'+i).hide();
		}
	});
}
function ext(v) {return v.substring(v.lastIndexOf('.')+1, v.length).toLowerCase();}
function Dstats() {
	$.post(AJPath, 'action=stats&screenw='+window.screen.width+'&screenh='+window.screen.height+'&uri='+encodeURIComponent(window.location.href)+'&refer='+encodeURIComponent(document.referrer), function(data) {});
}
function Dtoast(msg, fid, time) {
	var time = time ? time : 3;
	var fid = fid ? fid : '';
	if($('.ui-toast').length) {
		$('.ui-toast').html(msg);
	} else {
		$('body').append('<div class="ui-toast">'+msg+'</div>');
	}
	var w = $('.ui-toast').width();
	if(w < 14) w = msg.length*14;
	$('.ui-toast').css('left', $(document).scrollLeft()+(document.body.clientWidth-w)/2 - 16);
	$('.ui-toast').fadeIn('fast', function() {
		setTimeout(function() {
			$('.ui-toast').fadeOut('slow', function() {
				if(fid) $('#'+fid).focus();
			});
		}, time*1000);
	});
}
function Dwindow(u, w, h) {
	var ww = document.body.scrollWidth;
	var wh = $(window).height();
	w = w ? w : ww - 100;
	h = h ? h : wh - 200;
	dWin = window.open(u,'dwindow','height='+h+'px,width='+w+'px,top='+parseInt((wh-h)/2)+'px,left='+parseInt((ww-w)/2)+'px,resizable=no,scrollbars=yes');
}
function Dchat(u) {
	Dwindow(u, 1000, 600);
}
function GoMobile(url) {
	if(url && url != window.location.href && (UA.indexOf('phone') != -1 || UA.indexOf('mobile') != -1 || UA.indexOf('android') != -1 || UA.indexOf('ipod') != -1) && get_cookie('mobile') != 'pc' && UA.indexOf('ipad') == -1) {Go(url);}
}
function PushNew() {
	$('#destoon_push').remove();
	s = document.createElement("script");
	s.type = "text/javascript";
	s.id = "destoon_push";
	s.src = DTPath+"api/push"+DTExt+"?refresh="+Math.random()+".js";
	document.body.appendChild(s);
}
function set_cookie(n, v, d) {
	var e = ''; 
	var f = d ? d : 365;
	e = new Date((new Date()).getTime() + f * 86400000);
	e = "; expires=" + e.toGMTString();
	document.cookie = CKPrex + n + "=" + v + ((CKPath == "") ? "" : ("; path=" + CKPath)) + ((CKDomain =="") ? "" : ("; domain=" + CKDomain)) + e; 
}
function get_cookie(n) {
	var v = ''; var s = CKPrex + n + "=";
	if(document.cookie.length > 0) {
		o = document.cookie.indexOf(s);
		if(o != -1) {	
			o += s.length;
			end = document.cookie.indexOf(";", o);
			if(end == -1) end = document.cookie.length;
			v = unescape(document.cookie.substring(o, end));
		}
	}
	return v;
}
function del_cookie(n) {var e = new Date((new Date()).getTime() - 1 ); e = "; expires=" + e.toGMTString(); document.cookie = CKPrex + n + "=" + escape("") +";path=/"+ e;}
function set_local(n, v) {window.localStorage ? localStorage.setItem(CKPrex + n, v) : set_cookie(n, v);}
function get_local(n) {return window.localStorage ? localStorage.getItem(CKPrex + n) : get_cookie(n);}
function del_local(n) {window.localStorage ? localStorage.removeItem(CKPrex + n) : del_cookie(n);}
function substr_count(str, exp) {if(str == '') return 0;var s = str.split(exp);return s.length-1;}
function checked_count(id) {return $('#'+id+' :checked').length;}
function lang(s, a) {for(var i = 0; i < a.length; i++) {s = s.replace('{V'+i+'}', a[i]);} return s;}
function get_cart() {var cart = parseInt(get_cookie('cart'));return cart > 0 ? cart : 0;}
function cutstr(str, mark1, mark2) {
	if(mark1) {
		var p1 = str.indexOf(mark1);
		if(p1 != -1) str = str.substr(p1 + mark1.length);
	}
	if(mark2) {
		var p2 = str.indexOf(mark2);
		if(p2 != -1) str = str.substr(0, p2);
	}
	return str;
}
function Dpwd(pwd, min, max, mix) {
	min = parseInt(min);
	if(min < 6) min = 6;
	max = parseInt(max);
	if(max > 30) max = 30;
	if(pwd.length == 32) return ['pwd', 'ok'];
	if(pwd.length < min) return ['min', min];
	if(pwd.length > max) return ['max', max];
	if((','+mix+',').indexOf(',1,') != -1 && !pwd.match(/[0-9]/)) return ['mix', '09'];
	if((','+mix+',').indexOf(',2,') != -1 && !pwd.match(/[a-z]/)) return ['mix', 'az'];
	if((','+mix+',').indexOf(',3,') != -1 && !pwd.match(/[A-Z]/)) return ['mix', 'AZ'];
	if((','+mix+',').indexOf(',4,') != -1) {
		var str = pwd.replace(/[0-9a-z]/gi, '');
		if(str.length < 1) return ['mix', '..'];
	}
	return ['pwd', 'ok'];
}
document.onkeydown = function(e) {
	var k = typeof e == 'undefined' ? event.keyCode : e.keyCode;
	if(k == 37) {
		try{if(Dd('destoon_previous').value && typeof document.activeElement.name == 'undefined')Go(Dd('destoon_previous').value);}catch(e){}
	} else if(k == 39) {
		try{if(Dd('destoon_next').value && typeof document.activeElement.name == 'undefined')Go(Dd('destoon_next').value);}catch(e){}
	} else if(k == 38 || k == 40 || k == 13) {
		try{if(Dd('search_tips').style.display != 'none' || Dd('search_tips').innerHTML != ''){SCTip(k);return false;}}catch(e){}
	}
}
if(isIE && !window.XMLHttpRequest) {document.write('<style type="text/css">.head_s,.menu-fix,.adsign {display:none;}</style>');}
$(function(){
	if(isIE) {
		$(window).bind("scroll.back2top", function() {
			var st = $(document).scrollTop(), winh = $(window).height();
			(st > 0) ? $('.back2top').show() : $('.back2top').hide();
		});
	} else {
		$(window).on("scroll.back2top", function() {
			$(document).scrollTop() > 0 ? $('.back2top').show() : $('.back2top').hide();
		});
	}
	$('.back2top').click(function() {
		$('html, body').animate({scrollTop:0}, 200);
	});
});