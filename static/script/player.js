/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function player(u, w, h, a) {
	var w = w ? w : 480;
	var h = h ? h : 270;
	var m = '', e =  '', t =  '', c =  '', x =  '', d =  '', u5 =  '', p1 =  '', p2 = '';
	x = ext_url(u);
	if(w) {
		if(w < 0) {
			w = parseInt(window.screen.width+w);
		}
	} else {
		w = 480;
	}
	if(!h) h = parseInt(w*9/16);
	var ua = navigator.userAgent.toLowerCase();
	if(ua.indexOf('mac os')!=-1) m = 'Mac';
	if(ua.indexOf('ipad')!=-1) m = 'iPad';
	if(ua.indexOf('iphone')!=-1) m = 'iPhone';
	if(ua.indexOf('ipod')!=-1) m = 'iPod';
	if(ua.indexOf('android')!=-1) m = 'Android';
	x = ext_url(u);
	d = _cuturl(u, '://', '/');
	switch(d) {
		case 'player.youku.com':
		case 'v.qq.com':
		case 'm.iqiyi.com':
		case 'player.bilibili.com':
		case 'www.bilibili.com':
		case 'www.acfun.cn':
		case 'www.huya.com':
		case 'liveshare.huya.com':
			return html_frame(u, w, h);
		break;
		default:
		break;
	}
	if(m) {
		if(x == 'mp4' || x == 'ogg' || x == 'webm') {
			u5 = u;
		} else if(x == 'mp3') {
			return '<audio src="'+u+'"'+(a ? ' autoplay="autoplay"' : '')+' controls="controls"></audio>';
		} else if(d.indexOf('.youtube.com')!=-1) {
			u5 = url2video5(u);
			if(u5) return html_frame(u5, w, h);
		} else if(u.indexOf('.huya.com')!=-1) {
			u5 = url2video5(u);
			if(u5) return html_play(u5);
		}
		return u5 ? '<video src="'+u5+'" width="'+w+'" height="'+h+'"'+(a ? ' autoplay="autoplay"' : '')+' controls="controls" webkit-playsinline="" playsinline="" x5-playsinline="" x-webkit-airplay="allow">'+html_play(u5)+'</video>' : html_play(u);
	}
	if(x && x.length < 5) {
		if(x == 'mp4' || x == 'ogg' || x == 'webm') {
			return '<video src="'+u+'" width="'+w+'" height="'+h+'"'+(a ? ' autoplay="autoplay"' : '')+' controls="controls"></video>';
		} else if(x == 'mp3') {
			return '<audio src="'+u+'"'+(a ? ' autoplay="autoplay"' : '')+' controls="controls"></audio>';
		} else if(x == 'rm' || x == 'rmvb' || x == 'ram') {
			t = 'audio/x-pn-realaudio-extend';
		} else if(x == 'wma' || x == 'wmv') {
			t = 'application/x-mplayer2';
			c = 'controls="imagewindow,controlpanel,statusbar"';
		}
		return '<embed src="'+u+'" width="'+w+'" height="'+h+'" type="'+t+'" autostart="'+(a ? 'true' : 'false')+'" '+c+'></embed>';
	}
	return html_play(u, w, h);
}
function _cuturl(str, mark1, mark2) {
	var p1 = str.indexOf(mark1);
	if(p1 == -1) return '';
	str = str.substr(p1 + mark1.length);
	var p2 = str.indexOf(mark2);
	if(p2 == -1) return str;
	return str.substr(0, p2);
}
function ext_url(v) {return v.substring(v.lastIndexOf('.')+1, v.length).toLowerCase();}
function html_frame(u, w, h) {return '<iframe src="'+u+'" width="'+w+'" height="'+h+'" frameborder="0" scrolling="no" allowfullscreen="true" allowtransparency="true"></iframe>';}
function html_play(u, w, h) {return '<a href="'+u+'" target="_blank" rel="external"><div style="width:'+(w ? w+'px' : '100%')+';height:'+(h ? h+'px' : '300px')+';background:#000000 url('+DTPath+'static/image/play.png) no-repeat center center;background-size:48px 48px;"></div></a>';}
function url2video5(u) {
	var p = '';
	var d = _cuturl(u, '://', '/');
	var h = _cuturl(u, 'http', ':') == 's' ? 'https://' : 'http://';
	switch(d) {
		case 'www.youtube.com':
			p = _cuturl(u, '/v/', '&');
			if(p) return h+'www.youtube.com/embed/'+p;
		break;
		case 'liveshare.huya.com':
			p = _cuturl(u, '/iframe/', '/');
			if(p) return h+'m.huya.com/'+p;
		break;
		case 'staticlive.douyucdn.cn':
			p = _cuturl(u, 'room_id=', '&');
			if(p) return h+'m.douyu.com/'+p;
		break;
	}
	return u;
}