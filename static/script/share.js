function Dshare(str, site) {
	if(Dbrowser == 'app') {
		if(site == 'weixin' || site == 'wxpyq') {
			obj = api.require('wxPlus');
			obj.isInstalled(function(ret, err) {
				if(ret.installed) {
					var wxthumb = DShare.thumb.substring(DShare.thumb.lastIndexOf('/')+1, DShare.thumb.length);
					api.download({
						url: DShare.thumb,
						savePath: 'fs://'+wxthumb,
						report: true,
						cache: true,
						allowResume: true
					}, function(ret, err) {
						//
					});
					obj.shareWebpage({
						apiKey : '',
						scene : site == 'wxpyq' ? 'timeline' : 'session',
						title : DShare.title,
						description : DShare.introduce,
						thumb : 'fs://'+wxthumb,
						contentUrl : DShare.linkurl
					}, function(ret, err) {
						if(ret.status) {
							Dtoast(L['share_success']);
							return;
						} else {
							alert(L['share_failure']);
						}
					});
				} else {
					alert(L['share_notapp']);
				}
			});
		} else if(site == 'qq' || site == 'qzone') {
			obj = api.require('QQPlus');
			obj.installed(function(ret, err) {
				if(ret.status) {
					obj.shareNews({
						url: DShare.linkurl,
						title: DShare.title,
						description: DShare.introduce,
						imgUrl: DShare.thumb,
						type: site == 'qzone' ? 'QZone' : 'QFriend'
					}, function(ret, err) {
						if(ret.status) {
							Dtoast(L['share_success']);
							return;
						} else {
							alert(L['share_failure']);
						}
					});
				} else {
					alert(L['share_notapp']);
				}
			});
		} else if(site == 'weibo') {
			obj = api.require('weiboPlus');
			obj.isInstalled(function(ret) {
				if(ret.status) {
					obj.shareWebPage({
						apiKey: '',
						text: DShare.introduce,
						title: DShare.title,
						description: DShare.introduce,
						thumb: DShare.thumb,
						contentUrl: DShare.linkurl
					}, function(ret, err) {
						if(ret.status) {
							Dtoast(L['share_success']);
							return;
						} else {
							alert(L['share_failure']);
						}
					});
				} else {
					alert(L['share_notapp']);
				}
			});
		}
		return;
	}
	if(str.substring(0, 4) == 'http') {
		$('.head-bar-back').hide();
		$('.poster').hide();
		$('.share').hide();
		$('.head-bar-right').show();
		$('#send').css({'display':'','width':'100%','height':(window.screen.height-48)+'px'});
		$('#send').attr('src', str);
	} else {
		$('.poster').hide();		
		$('#share-img-'+DShare.pageid).css({'width':'100%','border-radius':'12px'});
		$('.poster p').html(str);		
		$('.poster').slideDown('fast');
	}
}
function Dshare_copy() {
	var clipboard = new Clipboard('#copy-link');
	$('.qrcode').hide();
	Dtoast(js_pageid, L['share_copy']);
}
function Dshare_tips() {
	$('#share-tips-'+js_pageid).fadeIn('fast');
	setTimeout(function() {
		Go(DShare.linkurl);
	}, 3000);
}
function Dshare_FC(ctx, str, top, left, maxwidth, font, maxlen) {
	ctx.lineWidth = 1; 
	var lineWidth = 0;
	var canvasWidth = maxwidth;
	var initHeight = top;
	var lastSubStrIndex= 0;
	if(str.length > maxlen) str = str.substring(0, maxlen - 1) + '...';
	var strLength = str.length;
	for(var i = 0; i < strLength; i++) { 
		lineWidth += ctx.measureText(str[i]).width; 
		if(lineWidth >= canvasWidth) {  
			ctx.fillText(str.substring(lastSubStrIndex, i), left, initHeight);
			initHeight += font;
			lineWidth = 0;
			lastSubStrIndex = i;
		}
		if(i == strLength - 1) {
			ctx.fillText(str.substring(lastSubStrIndex, i + 1), left, initHeight);
		}
	}
}
function Dshare_Draw() {
	var id = DShare.pageid
	var Dqrcode = new QRCode(Dd('share-code-'+id), {
		text: DShare.linkurl ,
		width: 260,
		height: 260,
		colorDark: "#000000",
		colorLight: "#FFFFFF",
		correctLevel: QRCode.CorrectLevel.H
	});
	var DqrcodeImg = Dd('share-code-'+id).querySelector('canvas');
	var ctx = Dd('share-post-'+id).getContext('2d');
	ctx.scale(2, 2);
	var imgBG = new Image(); imgBG.setAttribute('crossOrigin', 'anonymous');
	var imgTB = new Image(); imgTB.setAttribute('crossOrigin', 'anonymous');
	var imgQR = new Image(); imgQR.setAttribute('crossOrigin', 'anonymous');
	imgTB.onload = function () {
		ctx.drawImage(imgQR, 220, 20, 130, 130);
		ctx.drawImage(imgTB, 36, 194, 48, 48);	
		ctx.font = '18px Verdana';
		Dshare_FC(ctx, DShare.title, 212, 96, 252, 23, 125);
		ctx.font = '8px Verdana';
		Dshare_FC(ctx, DShare.linkurl, 282, 56, 272, 23, 125);
		Dd('share-img-'+id).src = Dd('share-post-'+id).toDataURL('image/png');
	}
	imgBG.onload = function () {
		ctx.drawImage(imgBG, 0, 0, 375, 330);
		imgTB.src = DShare.thumb;
		setTimeout(function() {
			if(imgTB.width < 1) {/*Cross domain OR Error load*/
				ctx.drawImage(imgBG, 0, 0, 375, 330);
				ctx.drawImage(imgQR, 220, 20, 130, 130);
				ctx.font = '18px Verdana';
				Dshare_FC(ctx, DShare.title, 212, 46, 300, 23, 125);
				ctx.font = '8px Verdana';
				Dshare_FC(ctx, DShare.linkurl, 282, 56, 272, 23, 125);
				Dd('share-img-'+id).src = Dd('share-post-'+id).toDataURL('image/png');
			}
		}, 1000);
	}
	imgBG.src = SKMob+'poster.png';
	imgQR.src = DqrcodeImg.toDataURL('image/jpeg');
	if(Dbrowser == 'app' || Dbrowser == 'cms' || Dbrowser == 'web') {
		$('#share-img-'+id).click(function() {
			App_Save($('#share-img-'+id).attr('src'));
		});
	}
}
$(function(){
	Dshare_Draw();
});