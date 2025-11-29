/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function dslide(id, time) {
	if($('#'+id).length == 0) return;
	if($('#'+id).html().indexOf('<ul') != -1) return;
	if(!time) time = 5000;
	var _this = this;
	this.w = document.body.clientWidth;
	this.h = 1;
	this.c = 0;
	this.src = [];
	this.url = [];
	this.alt = [];
	this.tar = [];
	this.rel = [];
	$('#'+id).find('a').each(function(i) {
		_this.src.push($(this).find('img')[0].src);
		_this.alt.push($(this).find('img')[0].alt);
		_this.url.push(this.href);
		_this.tar.push(this.target);
		_this.rel.push(this.rel);
	});
	if(!this.src[0]) return;
	this.max = this.src.length;
	this.htm = '<ul id="'+id+'_ul" style="position:relative;width:'+this.w*(this.max+1)+'px;z-index:1;overflow:hidden;">';
	for(var i = 0; i < this.max; i++) {
		this.htm += '<li style="float:left;"><a href="'+this.url[i]+'"'+(this.tar[i] ? ' target="'+this.tar[i]+'"' : '')+(this.rel[i] ? ' rel="'+this.rel[i]+'"' : '')+'><img src="'+this.src[i]+'" width="'+this.w+'"/></a></li>';
	}
	this.htm += '</ul>';
	if(this.alt[0]) this.htm += '<div id="'+id+'_alt" style="width:'+(this.w-32)+'px;height:32px;line-height:32px;overflow:hidden;z-index:3;position:absolute;margin-top:-32px;padding:0 16px;font-weight:bold;color:#FFFFFF;background:#384349;filter:Alpha(Opacity=80);opacity:0.8;">'+this.alt[0]+'</div>';
	this.htm += '<div style="width:'+this.w+'px;height:20px;overflow:hidden;z-index:4;position:absolute;margin-top:-'+(this.alt[0] ? 60 : 30)+'px;text-align:center;padding-left:6px;">';
	for(var i = 0; i < this.max; i++) {
		this.htm += '<span id="'+id+'_no_'+i+'" style="display:inline-block;width:8px;height:8px;border-radius:4px;margin-right:8px;background:#FFFFFF;'+(i == this.c ? 'opacity:1.0;' : 'opacity:0.5')+'"></span>';
	}
	this.htm += '</div>';
	$('#'+id).html(this.htm);
	if(this.max == 1) return;
	this.t;
	this.p = 0;
	$('#'+id).on('swipeleft',function(){
		_this.slide(_this.c+1);
	});
	$('#'+id).on('swiperight',function(){
		_this.slide(_this.c-1);
	});
	$('#'+id).on('touchstart',function(){
		_this.p = 1;
	});
	$('#'+id).on('touchend',function(){
		_this.p = 0;
	});
	$(window).bind('orientationchange.slide'+id, function(e){
		window.setTimeout(function() {
			_this.w = document.body.clientWidth;
			$('#'+id).find('ul').css('width', _this.w*(_this.max+1));
			$('#'+id).find('img').css('width', _this.w);
			$('#'+id).find('div').css('width', _this.w);
			_this.p = 0;
		}, 300);
	});
	this.slide = function(o) {
		if(o == this.c) return;
		if(o < 0 || o >= this.max) return;
		if(o == 0 && this.c == this.max - 1) {
			$('#'+id+'_ul').append($('#'+id+'_ul li:first').clone());
			$('#'+id+'_ul').stop(true, true).animate({'left':-this.w*this.max},500,function() {
				$('#'+id+'_ul').css('left','0');
				$('#'+id+'_ul li:last').remove();
			});
		} else {
			$('#'+id+'_ul').stop(true, true).animate({'left':-o*this.w},500);
		}
		$('#'+id+'_no_'+this.c).css('opacity','0.5');
		$('#'+id+'_no_'+o).css('opacity','1.0');
		if(this.alt[0]) $('#'+id+'_alt').html(this.alt[o]);
		this.c = o;
	}
	this.start = function() {
		if(this.p) return;
		if(this.c == this.max - 1) {
			this.slide(0);
		} else {
			this.slide(this.c+1);
		}
	}
	this.t = setInterval(function() {_this.start();}, time);
	return true;
}