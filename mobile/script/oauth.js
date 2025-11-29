function Doauth(url) {
	if(Dbrowser == 'app') {
		if(url.indexOf('/wechat/') != -1) {
			obj = api.require('wxPlus');
			obj.isInstalled(function(ret, err) {
				if(ret.installed) {
					obj.auth({
						apiKey : '',
						scope : 'snsapi_userinfo',
					}, function(ret, err) {
						if(ret.status && ret.code) {
							obj.getToken({
								apiKey: '',
								apiSecret: '',
								code: ret.code
							}, function(ret, err) {
								if(ret.status && ret.accessToken && ret.openId) {
									obj.getUserInfo({
										accessToken: ret.accessToken,
										openId: ret.openId
									}, function(ret, err) {
										if(ret.status && ret.openid) {
											alert(JSON.stringify(ret));
										} else {
											alert('ERROR_GET_USER');
										}
									});
								} else {
									alert('ERROR_GET_TOKEN');
								}
							});
						} else {
							alert('ERROR_GET_CODE');
						}
					});
				} else {
					alert('ERROR_INSTALL');
				}
			});
			return;
		} else if(url.indexOf('/qq/') != -1) {
			obj = api.require('QQPlus');
			obj.installed(function(ret, err) {
				if(ret.status) {
					obj.login({
						apiKey: ''
					}, function(ret, err) {
						if(ret.status && ret.accessToken) {
							obj.getUserInfo(function(ret, err) {
								if(ret.status && ret.info) {
									alert(JSON.stringify(ret));
								} else {
									alert('ERROR_GET_USER');
								}
							});
						} else {
							alert('ERROR_GET_TOKEN');
						}
					});
				} else {
					alert('ERROR_INSTALL');
				}
			});	
			return;
		} else if(url.indexOf('/weibo/') != -1) {
			obj = api.require('weiboPlus');
			obj.isInstalled(function(ret) {
				if(ret.status) {
					obj.auth({
						apiKey: '',
						registUrl: ''
					}, function(ret, err) {
						if(ret.status && ret.token) {
							obj.getUserInfo({
								token: ret.token,
								userId: ret.userId
							}, function(ret, err) {
								if(ret.status && ret.userInfo) {
									alert(JSON.stringify(ret));
								} else {
									alert('ERROR_GET_USER');
								}
							});
						} else {
							alert('ERROR_GET_TOKEN');
						}
					});
				} else {
					alert('ERROR_INSTALL');
				}
			});
			return;
		}
	}
	$('.head-bar-right').html('<img src="'+SKMob+'icon-cancel.png" width="24" height="24" onclick="window.location.reload();"/>');
	$('.head-bar-back').hide();
	Dframe(js_pageid, url, 48);
	var forward = $("[name='forward']").val();
	if(!forward) forward = DTMob+'my'+DTExt;
	var interval = window.setInterval(
		function() {
			$.get('?action=oauth', function(data) {
				if(data == 'ok') {
					clearInterval(interval);
					Go(forward);
				} else if(data == 'bd') {
					clearInterval(interval);
					Go('oauth'+DTExt+'?action=bind');
				}
			});
	},  2000);
}
$(function(){
	if(Dbrowser == 'wxmini') {
		$('#oauth a').hide();
		$('#oauth-wechat').show();
	} else if(Dbrowser == 'weixin') {
		$('#oauth-taobao').hide();
	} else if(Dbrowser == 'qq' || Dbrowser == 'tim') {
		$('#oauth-wechat').hide();
	} else {
		$('#oauth-wechat').hide();
		/*
		$('#oauth a').each(function(){
			if($(this).attr('id') == 'oauth-qq') return true;
			if($(this).attr('id') == 'oauth-douyin') return true;
			$(this).attr('href', 'javascript:Doauth(\''+$(this).attr('href')+'\');');
		});
		*/
	}
});