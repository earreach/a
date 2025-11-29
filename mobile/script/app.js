function App_Scan(id) {	
	if(Dbrowser == 'web' || Dbrowser == 'cms') {
		var obj = api.require('scanner');
		obj.open(function(ret,err) {
			if(typeof ret.msg == 'undefined') return;
			$(id).val(ret.msg);
		});
	} else {		
		var obj = api.require('FNScanner');
		obj.open({
    		autorotation: true
		}, function(ret) {
			if(typeof ret.content == 'undefined') return;
			$(id).val(ret.content);
		});
	}
}
function App_Jump(url) {
	if(api.systemType == 'ios') {
		api.openApp({
			iosUrl:url
		});
	} else {
		api.openApp({
			androidPkg:'android.intent.action.VIEW',
			mimeType:'text/html',
			uri:url
		});
	}
}
function App_Save(url) {
	if(url.indexOf('://') != -1) {
		api.saveMediaToAlbum({
			path: url
		}, function(ret, err) {
			if(ret && ret.status) {
				Dtoast(js_pageid, L['save_success']);
			} else {			
				Dtoast(js_pageid, L['save_failure']);
			}
		});
	} else if(url.indexOf('data:') != -1) {
		var trans = api.require('trans');
		var arr = url.split('base64,');
		trans.saveImage({
			base64Str: arr[1],
			imgPath:'fs://icon/',
			imgName:'share.png'
		}, function(ret, err) {
			if(ret.status) {
				api.saveMediaToAlbum({
					path: 'fs://icon/share.png'
				}, function(ret, err) {
					if(ret && ret.status) {
						Dtoast(js_pageid, L['save_success']);
					} else {			
						Dtoast(js_pageid, L['save_failure']);
					}
				});
			} else {
				Dtoast(js_pageid, L['save_failure']);
			}
		});
	}
}
function App_Win(url, name) {
	api.openWin({
		name: name ? name : 'webwin',
		url: url,
		useWKWebView: true,
		bounces: false
	});
}