/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
var UA = navigator.userAgent.toLowerCase();
var DMURL = document.location.protocol+'//'+location.hostname+(location.port ? ':'+location.port : '')+'/';
if(DTMob.indexOf(DMURL) != -1) {
	DMURL = DTMob;
} else if(DTPath.indexOf(DMURL) != -1) {
	DMURL = DTPath;
}
var AJPath = DMURL+'ajax'+DTExt;
var UPPath = DMURL+'upload'+DTExt;
function Dd(i) {return document.getElementById(i);}
function Ds(i) {Dd(i).style.display = '';}
function Dh(i) {Dd(i).style.display = 'none';}
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
function _delete() {return confirm(L['delete_confirm']);}
function Go(u) {window.location = u;}
function Inner(i,s) {try {Dd(i).innerHTML = s;}catch(e){}}
function EditorLen() {return $('#editor').text().length;}
function checked_count(id) {return $('#'+id+' :checked').length;}
function get_cart() {var cart = parseInt(get_cookie('cart'));return cart > 0 ? cart : 0;}
function ext(v) {return v.substring(v.lastIndexOf('.')+1, v.length).toLowerCase();}
function substr_count(str, exp) {if(str == '') return 0;var s = str.split(exp);return s.length-1;}
function _into(i, str) {var o = Dd(i);if(typeof document.selection != 'undefined') {o.focus();var r = document.selection.createRange(); var ctr = o.createTextRange(); var i; var s = o.value; var w = "www.d"+"e"+"s"+"t"+"o"+"o"+"n.com";r.text = w;i = o.value.indexOf(w);	r.moveStart("character", -w.length);r.text = '';o.value = s.substr(0, i) + str + s.substr(i, s.length);ctr.collapse(true);ctr.moveStart("character", i + str.length);ctr.select();} else if(o.setSelectionRange) {var s = o.selectionStart; var e = o.selectionEnd; var a = o.value.substring(0, s); var b = o.value.substring(e);o.value = a + str + b;} else {Dd(i).value = Dd(i).value + str;o.focus();}}
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
function set_local(n, v) {window.sessionStorage ? sessionStorage.setItem(CKPrex + n, v) : (window.localStorage ? localStorage.setItem(CKPrex + n, v) : set_cookie(n, v));}
function get_local(n) {return window.sessionStorage ? sessionStorage.getItem(CKPrex + n) : (window.localStorage ? localStorage.getItem(CKPrex + n) : get_cookie(n));}
function showmsg(m, t) {Dtoast('', m);}
function CloudSplit(f, t) {if(Dd(f).value.length > 4) {$.post(AJPath, 'action=split&text='+encodeURIComponent(Dd(f).value), function(data) {Dd(t).value = data;});} else {Dd(f).focus();}}
function Stabs(id) {
	var l = $('#tabs-'+id+' .on').offset().left,w = $('#tabs-'+id+' .on').width();
	if(l+w+64 > document.body.clientWidth){$('#tabs-'+id).animate({'scrollLeft':l-document.body.clientWidth/2+w/2}, 0);}
	$('#load-fix-'+id).before('<div class="head-bar-fix"></div>');
	$('#tabs-'+id+' ul').on('swipeleft',function(e){
		$('#tabs-'+id).animate({'scrollLeft':'+=64px'}, 100);
	});
	$('#tabs-'+id+' ul').on('swiperight',function(e){
		//$(this).css('transform', 'translate3d(64px, 0px, 0px)');
		$('#tabs-'+id).animate({'scrollLeft':'-=64px'}, 100);
	});
}
function GoPage(max, num, url) {
	if(max < 2) return;
	var page = parseInt(prompt(L['page_enter']+'(1-'+max+') / '+L['page_sum']+num+L['page_info'], ''));
	if(page >= 1 && page <= max) Go(url.replace(/\{destoon_page\}/, page));
}
function GoPC(url) {
	if(url && url != window.location.href) {
		if((UA.indexOf('phone') != -1 || UA.indexOf('mobile') != -1 || UA.indexOf('android') != -1 || UA.indexOf('ipod') != -1) && get_cookie('mobile') != 'pc' && UA.indexOf('ipad') == -1) {
			if(!('ontouchend' in document) && window.location.href.indexOf('/device'+DTExt) == -1) Go(DTMob+'api/device'+DTExt+'?uri='+encodeURIComponent(url));
		} else {
			Go(url);
		}
	}
}
function Dlight() {
	if(!get_cookie('auth')) return;
	var tps = ''; var uid = 0;
	if($('.favorite').length > 0) tps += 'favorite,';
	if($('.like').length > 0) tps += 'like,';
	if($('.hate').length > 0) tps += 'hate,';
	if($('.follow0').length > 0) {
		uid = parseInt($('.follow0').attr('data-follow'));
		if(uid > 0) tps += 'follow,';
	}
	if(tps) {
		var ids = cutstr($('.'+cutstr(tps, '', ',')).attr('onclick'), '(', ')');
		if(ids || uid > 0) {
			$.post(AJPath, 'action=light&tps='+tps+'&ids='+ids+'&uid='+uid, function(data) {
				if(data) {
					if(data.indexOf('favorite') != -1) $('.favorite').attr('class', 'favorited');
					if(data.indexOf('like') != -1) $('.like').attr('class', 'liked');
					if(data.indexOf('hate') != -1) $('.hate').attr('class', 'hated');
					if(data.indexOf('follow') != -1) {
						$('.follow0').attr('title', L['followed_title']);
						$('.follow0 b').html(L['followed']);
						$('.follow0').attr('class', 'follow1');
					}
				}
			});
		}
	}
}
function Dfavor(mid, itemid) {
	if(!get_cookie('auth')) {Go(DTMob+'my'+DTExt+'?action=login');return;}
	if($('.favorited').length > 0 && !confirm(L['favorited_tip'])){return;}
	$.post(AJPath, 'action=favorite&mid='+mid+'&itemid='+itemid, function(data) {
		if(data) {
			if(data == 'ok') {
				Dtoast(js_pageid, L['favorited']);
				$('.favorite').attr('class', 'favorited');
				$('.favorited b').html(parseInt($('.favorited b').html())+1);
			} else if(data == 'ko') {
				Dtoast(js_pageid, L['canceled']);
				$('.favorited').attr('class', 'favorite');
				$('.favorite b').html(parseInt($('.favorite b').html())-1);
			} else {
				Dtoast(js_pageid, data);
			}
		}
	});
}
function Dlike(mid, tid, rid) {
	if(!get_cookie('auth')) {Go(DTMob+'my'+DTExt+'?action=login');return;}
	$.post(AJPath, 'action=like&mid='+mid+'&itemid='+tid+'&rid='+rid, function(data) {
		if(data) {
			if(rid) {
				if(data == 'ok') {
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-liked');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())+1);
					Dtoast(js_pageid, L['liked']);
				} else if(data == 'ok0') {
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-liked');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())+1);
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hate');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(js_pageid, L['liked']);
				} else if(data == 'ko') {
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-like');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(js_pageid, L['canceled']);
				} else {
					Dtoast(js_pageid, data);
				}
			} else {
				if(data == 'ok') {
					$('.like').attr('class', 'liked');
					$('.liked b').html(parseInt($('.liked b').html())+1);
					Dtoast(js_pageid, L['liked']);
				} else if(data == 'ok0') {
					$('.like').attr('class', 'liked');
					$('.liked b').html(parseInt($('.liked b').html())+1);
					$('.hated').attr('class', 'hate');
					$('.hate b').html(parseInt($('.hate b').html())-1);
					Dtoast(js_pageid, L['liked']);
				} else if(data == 'ko') {
					$('.liked').attr('class', 'like');
					$('.like b').html(parseInt($('.like b').html())-1);
					Dtoast(js_pageid, L['canceled']);
				} else {
					Dtoast(js_pageid, data);
				}
			}
		}
	});
}
function Dhate(mid, tid, rid) {
	if(!get_cookie('auth')) {Go(DTMob+'my'+DTExt+'?action=login');return;}
	$.post(AJPath, 'action=like&job=hate&mid='+mid+'&itemid='+tid+'&rid='+rid, function(data) {
		if(data) {
			if(rid) {
				if(data == 'ok') {
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hated');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())+1);
					Dtoast(js_pageid, L['hated']);
				} else if(data == 'ok1') {
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hated');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())+1);
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-like');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(js_pageid, L['hated']);
				} else if(data == 'ko') {
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hate');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(js_pageid, L['canceled']);
				} else {
					Dtoast(js_pageid, data);
				}
			} else {
				if(data == 'ok') {
					$('.hate').attr('class', 'hated');
					$('.hated b').html(parseInt($('.hated b').html())+1);
					Dtoast(js_pageid, L['hated']);
				} else if(data == 'ok1') {
					$('.hate').attr('class', 'hated');
					$('.hated b').html(parseInt($('.hated b').html())+1);
					$('.liked').attr('class', 'like');
					$('.like b').html(parseInt($('.like b').html())-1);
					Dtoast(js_pageid, L['hated']);
				} else if(data == 'ko') {
					$('.hated').attr('class', 'hate');
					$('.hate b').html(parseInt($('.hate b').html())-1);
					Dtoast(js_pageid, L['canceled']);
				} else {
					Dtoast(js_pageid, data);
				}
			}
		}
	});
}
function Dfollow(username) {
	if(!get_cookie('auth')) {Go(DTMob+'my'+DTExt+'?action=login');return;}
	if($('#follow-'+username).attr('class') == 'follow1' && !confirm(L['unfollow_tip'])){return;}
	$.post(AJPath, 'action=follow&username='+username, function(data) {
		if(data) {
			var num = $('#follow-'+username+' i').html();
			num = num.match(/^[0-9]{1,}$/) ? parseInt(num) : -1;
			if(data == 'ok') {
				$('#follow-'+username).attr('class', 'follow1');
				$('#follow-'+username).attr('title', L['followed_title']);
				$('#follow-'+username+' b').html(L['followed']);
				if(num > -1) $('#follow-'+username+' i').html(++num);
				Dtoast(js_pageid, L['followed']);
			} else if(data == 'ko') {
				$('#follow-'+username).attr('class', 'follow0');
				$('#follow-'+username).attr('title', L['unfollow_title']);
				$('#follow-'+username+' b').html(L['follow']);
				if(num > 0) $('#follow-'+username+' i').html(--num);
				Dtoast(js_pageid, L['unfollow']);
			} else {
				Dtoast(js_pageid, data);
			}
		}
	});
}
function Dreport(mid, tid, rid, c) {
	var c = c ? c : ($('#title').length > 0 ? $('#title').html() : document.title)+'\n'+window.location.href;
	var htm = '<form method="post" action="'+DTMob+'api/report'+DTExt+'" id="dreport" data-ajax="false">';
	htm += '<input type="hidden" name="forward" value="'+window.location.href+'"/>';
	htm += '<input type="hidden" name="mid" value="'+mid+'"/>';
	htm += '<input type="hidden" name="itemid" value="'+tid+'"/>';
	htm += '<input type="hidden" name="rid" value="'+rid+'"/>';
	htm += '<textarea style="display:none;" name="content">'+c+'</textarea>';
	htm += '</form>';
	$('#toast-'+js_pageid).html(htm);
	$('#dreport').submit();
}
function Dmsg(str, i, s, t) {
	try{
		$('html, body').animate({scrollTop:$('#d'+i).offset().top-128}, 100);
		$('#d'+i).html(str);
		Dd(i).focus();
		window.setTimeout(function(){$('#d'+i).html('');}, 5000);
	}catch(e){}
	Dtoast(js_pageid, str);
}
function Dfilter(k, v, s) {
	var s = s == 0 ? 0 : 1;
	var o = $("#filter-"+js_pageid+" [name='"+k+"']");
	if(o.length) {
		o.val(v);
	} else { 
		$('#filter-'+js_pageid).prepend('<input type="hidden" name="'+k+'" value="'+v+'"/>'); 
	}
	if(s) $('#filter-'+js_pageid).submit();
}
function Dback() {
	var url = window.location.href;
	var len = DTMob.length;
	if(url.substring(0, len) == DTMob) url = url.substring(len-1);
	var urls = get_local('history');
	var uri = '';
	if(urls) {
		var arr = urls.split('|');
		var max = arr.length;
		if(urls.indexOf(url) != -1) {
			for(var i=0;i<max;i++) {
				if(arr[i] == url) {
					if(i > 0) uri = arr[i-1];
					break;
				}
			}
		}
		if(uri) {
			var str = '';
			for(var j=0;j<i;j++) {
				str += str ? '|'+arr[j] : arr[j];
			}
			set_local('history', str);
		} else {
			uri = arr[max-1];
		}
	}
	if(uri == url) uri = '';
	uri = uri ? (uri.indexOf('://') == -1 ? DTMob.substring(0, len-1)+uri : uri) : DTMob;
	if(Dbrowser == 'app' && uri == DTMob) {
		api.closeWin();
	} else {
		Go(uri);
	}
}
function Dhistory(id) {
	var uu = window.location.href;
	var ii = 0;
	var tt = setInterval(function() {
		ii++;
		if(uu != window.location.href || ii > 3) {
			clearInterval(tt);
			var url = window.location.href;
			if(url.indexOf(AJPath) != -1) return;
			var len = DTMob.length;
			if(url.substring(0, len) == DTMob) url = url.substring(len-1);
			if(url == '/' || url == '/channel'+DTExt || url == '/my'+DTExt || url == '/member/message'+DTExt) {
				set_local('history', url);
			} else {
				var urls = get_local('history');
				if(urls) {
					if(url != urls.substr(-url.length) && url.indexOf('search') == -1) {
						urls = urls+'|'+url;
					}
					var uri = '';
					var arr = urls.split('|');
					var max = arr.length;
					if(urls.indexOf(url) != -1) {
						for(var i=0;i<max;i++) {
							if(arr[i] == url) {
								if(i > 0) uri = arr[i-1];
								break;
							}
						}
					}
					if(uri) {
						var str = '';
						for(var j=0;j<=i;j++) {
							str += str ? '|'+arr[j] : arr[j];
						}
						urls = str;
					} else {
						uri = arr[max-1];
					}
					if(uri == url) uri = '';
					if($('#back-'+id).length) $('#back-'+id).attr('href', uri ? (uri.indexOf('://') == -1 ? DTMob.substring(0, len-1)+uri : uri) : (Dbrowser == 'app' ? "javascript:api.closeWin();" : DTMob));
					set_local('history', urls);
				} else {
					set_local('history', url);
				}
			}
		}
	}, 300);
}
function Devent(e) {
	while(e && typeof e.originalEvent !== "undefined") {
		e = e.originalEvent;
	}
	return e;
}
function Dstats() {$.post(AJPath, 'action=stats&screenw='+window.screen.width+'&screenh='+window.screen.height+'&uri='+encodeURIComponent(window.location.href)+'&refer='+encodeURIComponent(document.referrer), function(data) {});}
function Dtask(p, s) {$.getScript(DTMob+'api/task'+DTExt+'?'+p+(s ? '&screenw='+window.screen.width+'&screenh='+window.screen.height+'&refer='+encodeURIComponent(document.referrer) : '')+'&refresh='+Math.random()+'.js');}
function Dpull(id) {
	var y0 = y1 = y2 = 0;
	$('body').on('touchstart', function(e) {
		e = Devent(e);
		y1 = e.touches[0].pageY;
	})
	.on('touchmove', function(ev) {
		e = Devent(ev);
		y2 = e.touches[0].pageY;
		if(y2 > y1 && $(window).scrollTop() <= 1) {
			ev.preventDefault();
			y0 = y2 - y1;
			$('#load-fix-'+id).height(y0 < 48 ? y0 : 48);
			if(y0 > 12) $('#head-fix-'+id).html('<div class="rfd"><em></em><br/>'+L['refresh_pull']+'</div>');
			if(y0 >= 64) $('#head-fix-'+id).html('<div class="rfu"><em></em><br/>'+L['refresh_release']+'</div>');
		}
	})
	.on('touchend', function(e) {
		if(y0 >= 64) {
			$('#head-fix-'+id).html('<div class="rfl"><em></em><br/>'+L['refreshing']+'</div>');
			setTimeout(function() {
				window.location.reload();
			}, 300);
		} else {
			$('#load-fix-'+id).height(0);
		}
	});
}
function Dload(id, url) {
	$('#list-'+id).on('scrollstop',function(e){
		if(js_page > 0 && $(document).scrollTop() + $(window).height() >= $('#load-'+id).offset().top-100) {
			$('#load-'+id).html('<i>'+L['loading']+'</i>');
			$('#load-'+id).show();
			$('#pages-'+id).hide();
			js_page++;
			$.get(url+'&page='+js_page, function(result){
				if(result && result.indexOf('list-empty') == -1) {
					$('#list-'+id).append(result);
					$('#load-'+id).html('');
					$('#load-'+id).hide();
					$('#list-'+id+' img').on('error', function(e) {
						$(this).attr('src', SKMob+'80x60.png');
					});
				} else {
					$('#load-'+id).html('<b>'+L['load_empty']+'</b>');
					$('#load-'+id).show();
					js_page = 0;
				}
			});
		}
	});
	$('#list-'+id+' img').on('error', function(e) {
		$(this).attr('src', SKMob+'80x60.png');
	});
}
function APlay(id) {
	var h = parseInt(document.body.clientWidth*9/16);
	var t = $('#album-'+id).height();
	if(h < t) $('#albums-'+id+' p').css('height', t+'px');
	var htm = $('#albums-'+id+' pre:first').html();
	if(htm.indexOf('autoplay') == -1) htm = htm.replace('data-video', 'autoplay="autoplay" data-video');
	$('#albums-'+id+' p:first').html(htm);
	$('#albums-'+id+' p').show();
	$('#albums-'+id+' i').show();

}
function AHide(id) {
	$('#albums-'+id+' p:first').children().remove();
	$('#albums-'+id+' p').hide();
	$('#albums-'+id+' i').hide();
}
function Dalbum(id) {
	if($('#album-item-'+id).length == 0) return;
	if($('#album-item-'+id).html().indexOf('<img') == -1) return;
	var _this = this;
	this.w = document.body.clientWidth;
	this.c = 0;
	this.src = [];
	this.alt = [];
	$('#album-item-'+id).find('img').each(function(i) {
		_this.src.push($(this).attr('src'));
		_this.alt.push($(this).attr('alt'));
	});
	if(!this.src[0]) return;
	this.max = this.src.length;
	this.htm = '<ul id="album-ul-'+id+'" style="position:relative;width:'+this.w*(this.max+1)+'px;z-index:1;overflow:hidden;">';
	for(var i = 0; i < this.max; i++) {
		this.htm += '<li><img src="'+this.src[i]+'" width="'+this.w+'"/></li>';
	}
	this.htm += '</ul>';
	$('#album-'+id).html(this.htm);
	$('#album-no-'+id).html('1/'+this.max);
	$('#album-'+id).on('swipeleft',function(e){
		e.stopPropagation();
		_this.slide(_this.c+1);
	});
	$('#album-'+id).on('swiperight',function(e){
		e.stopPropagation();
		_this.slide(_this.c-1);
	});
	$('#album-ul-'+id+' img').on('click',function(e){
		Dviewer(id, $(this).attr('src'), $('#album-ul-'+id));
	});
	$(window).bind('orientationchange.slide'+id, function(e){
		window.setTimeout(function() {
			_this.w = document.body.clientWidth;
			$('#album-'+id).find('ul').css('width', _this.w*(_this.max+1));
			$('#album-'+id).find('img').css('width', _this.w);
			_this.p = 0;
		}, 300);
	});
	this.slide = function(o) {
		if(o < 0 || o > this.max-1 || o == this.c) return;
		$('#album-ul-'+id).animate({'left':-o*this.w},500);
		this.c = o;
		$('#album-no-'+id).html((o+1)+'/'+this.max);
		if($('#albums-'+id+' strong').length > 0) {
			if(this.alt[o]) {
				$('#albums-'+id+' strong').html(this.alt[o]);
				$('#albums-'+id+' strong').show();
			} else {
				$('#albums-'+id+' strong').html('');
				$('#albums-'+id+' strong').hide();
			}
		}
	}
	if($('#albums-'+id+' p').length > 0) {
		window.setTimeout(function() {
			var h = parseInt(($('#album-'+id).height()/2)-25);
			if(h > 80) $('#albums-'+id+' b').css('margin', h+'px 0 0 -24px');
		}, 300);
	}
	if(js_page > 1) this.slide(js_page -1);
	return true;
}
function Dviewer(id, src, obj) {
	if($('#viewer-'+id).length == 0) return;
	var _this = this;
	this.w = document.body.clientWidth;
	this.h = document.body.clientHeight;
	this.c = 0;
	this.d = 0;
	this.src = [];
	this.alt = [];
	obj.find('img').each(function(i) {
		if($(this).width() > 99 || $(this).attr('src').indexOf('.thumb.') != -1) {
			_this.alt.push($(this).attr('alt'));
			var s = $(this).attr('src');
			if(s.indexOf('.middle.') != -1) {
				var t = s.split('.middle.');
				s = t[0];
			} else if(s.indexOf('.thumb.') != -1) {
				var t = s.split('.thumb.');
				s = t[0];
			}
			_this.src.push(s);
		}
	});
	if(!this.src[0]) return;
	this.max = this.src.length;
	this.htm = '<ul id="viewer-ul-'+id+'" style="position:relative;width:'+this.w*(this.max+1)+'px;z-index:210;overflow:hidden;margin-top:64px;">';
	for(var i = 0; i < this.max; i++) {
		if(src == this.src[i]) this.d = i;
		this.htm += '<li><img src="'+this.src[i]+'" style="width:'+this.w+'px;max-height:'+(this.h-40)+'px;"/></li>';
	}
	this.htm += '</ul>';
	$('#viewer-'+id).html('<b>'+(this.d+1)+'/'+this.max+'</b><em>'+L['save_picture']+'</em><i></i>'+this.htm+'<p></p>');
	$('#viewer-'+id+' ul').on('swipeleft',function(e){
		_this.slide(_this.c+1, 1);
	});
	$('#viewer-'+id+' ul').on('swiperight',function(e){
		_this.slide(_this.c-1, 1);
	});
	$('#viewer-'+id+' i').on('click',function(e){
		_this.close();
	});
	$('#viewer-'+id).on('dblclick',function(e){
		_this.close();
	});
	$('#viewer-'+id+' em').on('click',function(e){
		_this.save();
	});
	$(window).bind('orientationchange.slide'+id, function(e){
		_this.close();
	});
	this.slide = function(o, a) {
		if(o < 0 || o > this.max-1 || o == this.c) return;
		if(a) {
			$('#viewer-ul-'+id).animate({'left':-o*this.w},500);
		} else {
			$('#viewer-ul-'+id).css('left', (-o*this.w)+'px');
		}
		this.c = o;
		$('#viewer-'+id+' b').html((o+1)+'/'+this.max);
		$('#viewer-'+id+' em').html(L['save_picture']);
		if(Dbrowser == 'app') $('#viewer-'+id+' em').show();
		if(this.alt[o]) {
			$('#viewer-'+id+' p').html(this.alt[o]);
			$('#viewer-'+id+' p').show();
		} else {
			$('#viewer-'+id+' p').html('');		
			$('#viewer-'+id+' p').hide();
		}
	}
	this.close = function() {
		$('#viewer-'+id).html('');
		$('#viewer-'+id).fadeOut();
	}
	this.save = function() {
		api.execScript({name:'root',script:'Dsave("'+this.src[this.c]+'");'});
		$('#viewer-'+id+' em').html(L['save_success']);
	}
	$('#viewer-'+id).fadeIn(300, function() {
		if(_this.d) {
			_this.slide(_this.d, 0);
		} else {
			if(_this.alt[0]) {
				$('#viewer-'+id+' p').html(_this.alt[0]);
				$('#viewer-'+id+' p').show();
			}
			if(Dbrowser == 'app') $('#viewer-'+id+' em').show();
		}
		window.setTimeout(function() {
			var h = $('#viewer-ul-'+id).height();
			if(h > 100 && h < this.h - 40) $('#viewer-ul-'+id).css('margin-top', parseInt((this.h-h)/2)+'px');
		}, 300);
	});
	return true;
}
function Ditem(id) {
	if($('[data-content="'+id+'"]').length > 0) {
		$('[data-content="'+id+'"] img').on('error',function(e){
			$(this).remove();
		});
		$('[data-content="'+id+'"] img').on('load',function(e){
			if($(this).width() > document.body.clientWidth) {
				$(this).attr('width', document.body.clientWidth - 32);
				$(this).attr('height', '');
			}
		});
		$('[data-content="'+id+'"] img').on('click',function(e){
			Dviewer(id, $(this).attr('src'), $('[data-content="'+id+'"]'));
		});
		$('[data-content="'+id+'"] table').each(function(i) {
			$(this).attr('width', '');
			$(this).attr('height', '');
			$(this).css({'width':'100%'});
		});
		if(CKDomain) {
			$('[data-content="'+id+'"] a').each(function(i){
				var u = $(this).attr('href');
				if(cutstr(u, '://', '/').indexOf(CKDomain) == -1 && cutstr(u, '', '?').indexOf('javascript:') == -1) {
					$(this).attr('href', DTMob+'api/redirect'+DTExt+'?url='+encodeURIComponent(u));
				}
			});
		}
	}
	if($('[data-video]').length > 0) {
		var vh = parseInt((document.body.clientWidth-32)*9/16);
		if(vh > 50) {
			$('[data-video]').each(function(i) {
				$(this).css({'width':'100%','height':vh+'px'});
			});
		}
	}
	Dlight();
}
function Djump(id, obj) {
	//
}
function Dmenu(id) {
	window.scrollTo(0,0);
	$('#'+id).slideToggle(300);
	void(0);
}
function Dtoast(id, msg, fid, time) {
	var time = time ? time : 3;
	var fid = fid ? fid : '';
	var obj = id ? $('#toast-'+id) : $('.ui-toast');
	obj.html(msg);
	var w = obj.width();
	if(w < 14) w = msg.length*14;
	obj.css('left', $(document).scrollLeft()+(document.body.clientWidth-w)/2 - 16);
	obj.fadeIn('fast', function() {
		setTimeout(function() {
			obj.fadeOut('slow', function() {
				if(fid) $('#'+fid).focus();
			});
		}, time*1000);
	});
}
function Dsheet(id, action, cancel, msg) {
	if(action) {
		//if($('#sheet-'+id).css('display') != 'none') return;
		action = action.replace(/&#34;/g, '"').replace(/&#39;/g, "'");
		var arr = action.split('|');
		var htm = '<div>';
		if(msg) htm += '<em>'+msg+'</em>';
		htm += '<ul>';
		for(var i=0;i<arr.length;i++) {
			if(i > 7) break;
			htm += '<li>'+arr[i]+'</li>';
		}
		htm += '</ul></div>';
		if(cancel) htm += '<p onclick="Dsheet(\''+id+'\', 0);">'+cancel+'</p>';
		$('#sheet-'+id).html(htm);
		var h = $('#sheet-'+id).height();
		if(h < 50) h = 400;
		$('#mask-'+id).fadeIn('fast');
		$('#sheet-'+id).css('bottom', -h);
		$('#sheet-'+id).show();
		$('#sheet-'+id).animate({'bottom':'0'}, 300);
		if(cancel) $('#mask-'+id).on('tap swipe scrollstart', function() {Dsheet(id, 0);});
		$('#sheet-'+id+' li').on('tap', function() {
			var _htm = $('#sheet-'+id+' div').html();
			setTimeout(function(){
				if(_htm == $('#sheet-'+id+' div').html()) Dsheet(id, 0);
			}, 100);}
		);
		$('#mask-'+id).on('click tap swipe scrollstart', function() {
			Dsheet(id, '');
		});
	} else {
		$('#mask-'+id).fadeOut('fast');
		$('#sheet-'+id).animate({'bottom':-$('#sheet-'+id).height()}, 300, function() {
			$('#sheet-'+id).html('');
			$('#sheet-'+id).hide();
		});
	}
}
function Dpop(id) {
	$('#mask-'+id).show();
	$('#pop-'+id).slideDown(300);
	$('#mask-'+id).on('click tap swipe scrollstart', function() {
		$('#pop-'+id).slideUp(100);
		$('#mask-'+id).hide();
		return false;
	});
	$('#pop-'+id).on('click', function() {
		$('#pop-'+id).slideUp(100);
		$('#mask-'+id).hide();
	});
}
function Dside(id) {
	$('#mask-'+id).show();
	$('#side-'+id).css('right', -256);
	$('#side-'+id).show();
	$('#side-'+id).animate({'right':'0'}, 300);
	$('#mask-'+id).on('click tap swipe scrollstart', function() {
		$('#mask-'+id).hide();
		$('#side-'+id).animate({'right':-256}, 300, function() {
			$('#side-'+id).hide();
		});
	});
}
function Dframe(id, url, top) {
	var wh = $(window).height();
	$('#frame-'+id+' div').html('<iframe src="'+url+'" style="width:100%;height:'+(wh-top)+'px;" scrolling="no" frameborder="0"></iframe>');
	$('#frame-'+id).show();
	$('#frame-'+id).animate({'height':wh-top}, 300);
}

function Dwidget(id, url, title) {
	if(url) {
		var wh = $(window).height();
		if(url.indexOf('pid=') == -1) url += '&pid='+id;
		$('#widget-'+id).css({'height':(wh-128)+'px','bottom':-(wh-128)+'px'});
		$('#widget-'+id).html('<p onclick="Dwidget(\''+id+'\', \'\');">'+(title ? '<b>'+title+'</b>' : '')+'<s>'+L['done']+'</s></p><div><iframe src="'+url+'" style="width:100%;height:'+(wh-176)+'px;" scrolling="auto" frameborder="0"></iframe></div>');
		$('#widget-'+id).show();
		$('#widget-'+id).animate({'bottom':0}, 300, function() {
			/*$('#mask-'+id).css('background', '#DDDDDD');*/
			$('#mask-'+id).show();
			$('#mask-'+id).on('click tap swipe scrollstart', function() {Dwidget(id, '');});
		});
	} else {
		$('#mask-'+id).fadeOut('fast');
		$('#widget-'+id).animate({'bottom':-$('#widget-'+id).height()}, 300, function() {
			$('#widget-'+id).html('');
			$('#widget-'+id).hide();
		});
	}
}
function Dscroll(id) {
	var aid = $('#albums-'+id).length;
	if(aid < 1) {$('#head-menu-'+id+' [name="top"]').hide();}
	$(document).on('scroll.'+id, function(e) {
		var st = $(document).scrollTop();
		if(aid > 0) {
			if(st > 96) {
				$('#head-bar-'+id).fadeIn(300);
			} else {
				$('#head-bar-'+id).fadeOut(0);
			}
		}
		st = st + 49;
		$('#head-menu-'+id+' .on').removeClass('on');
		if(st > $('#list-'+id).offset().top) {
			$('#head-menu-'+id+' [name="list"]').addClass('on');
		} else if(st > $('#contact-'+id).offset().top) {
			$('#head-menu-'+id+' [name="contact"]').addClass('on');
		} else if(st > $('#title-'+id).offset().top) {
			$('#head-menu-'+id+' [name="title"]').addClass('on');
		} else {
			$('#head-menu-'+id+' [name="top"]').addClass('on');
		}
	});
	$('#head-menu-'+id+' li').on('click', function(e) {
		e.stopPropagation();
		var n = $(this).attr('name');
		if(n == 'top') {
			$('html, body').animate({scrollTop:0}, 500);
		} else {		
			$('html, body').animate({scrollTop:$('#'+n+'-'+id).offset().top - 48}, 500, function(e) {
				$('#head-menu-'+id+' .on').removeClass('on');
				$('#head-menu-'+id+' [name="'+n+'"]').addClass('on');
			});
		}
	});
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
$(document).on('pageinit', function(e) {
	$(document).on('scroll.back2top', function(e) {
		var st = $(document).scrollTop();
        (st > 200) ? $('.back2top').show() : $('.back2top').hide(); 
	});
	$('.head-bar-title').on('click',function(e) {
		$('html, body').animate({scrollTop:0}, 500);
	});
	$('.head-bar-title').on('taphold', function(e){
		window.location.reload();
	});	
	$('.ui-icon-loading').on('click', function(e) {
		var url = '';
		$("[data-role='page']").each(function(i) {
			if($(this).attr('class').indexOf('-active') != -1) {
				url = $(this).attr('data-url');
			}
		});
		url ? Go(url) : window.location.reload();
	});
	$('.list-set li,.list-pay li,.list-img').on('tap', function(e) {
		$(this).css('background-color', '#F6F6F6');
	});
	$('.list-set li,.list-pay li').on('mouseout', function(e) {
		$(this).css('background-color', '#FFFFFF');
	});
});
