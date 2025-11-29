/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
if(destoon_oauth.length > 1) {
	Ds('weibo_sync');
	if(destoon_oauth.indexOf('moment') != -1) {
		$('#weibo_show').append('<input type="hidden" name="post[sync_moment]" value="0" id="sync_moment_inp"/>');
		$('#weibo_show').append('<img src="'+DTPath+'static/image/sync_moment.gif" id="sync_moment_img" onclick="sync_site(\'moment\');" class="c_p" title="'+L['sync_moment']+'"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
		if(get_cookie('auth') && get_local('moment_sync') != 'N') {
			Dd('sync_moment_inp').value = 1;
			Dd('sync_moment_img').src = DTPath+'static/image/sync_moment_on.gif';
		}
	}
	if(destoon_oauth.indexOf('sina') != -1) {
		$('#weibo_show').append('<input type="hidden" name="post[sync_sina]" value="0" id="sync_sina_inp"/>');
		$('#weibo_show').append('<img src="'+DTPath+'static/image/sync_sina.gif" id="sync_sina_img" onclick="sync_site(\'sina\');" class="c_p" title="'+L['sync_sina']+'"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
		if(get_cookie('sina_token') && get_local('sina_sync') == 'Y') {
			Dd('sync_sina_inp').value = 1;
			Dd('sync_sina_img').src = DTPath+'static/image/sync_sina_on.gif';
		}
	}
}

function sync_site(n) {
	if(Dd('sync_'+n+'_inp').value == 1) {
		Dd('sync_'+n+'_inp').value = 0;
		Dd('sync_'+n+'_img').src = DTPath+'static/image/sync_'+n+'.gif';
		set_local(n+'_sync', 'N');
	} else {
		if(n == 'moment') {
			if(!get_cookie('auth')) {
				if(confirm(L['sync_login_'+n]) && $('#link_login')) { window.open($('#link_login').attr('href')); }
				return;
			}
		} else if(n == 'sina') {
			if(!get_cookie(n+'_token')) {
				if(confirm(L['sync_login_'+n])) { window.open(DTPath+'api/oauth/'+n+'/connect'+DTExt); }
				return;
			}
		}
		Dd('sync_'+n+'_inp').value = 1;
		Dd('sync_'+n+'_img').src = DTPath+'static/image/sync_'+n+'_on.gif';
		set_local(n+'_sync', 'Y');
	}
}