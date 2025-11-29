/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function mm_pic_show(id, obj) {
	$('#thumbs-'+id+' img').each(function() {
		if($(this).attr('src') == obj.src) {
			$(this).css('border', '#FF6600 2px solid');
		} else {
			$(this).css('border', '#EEEEEE 1px solid');
		}
	});
	if(obj.src.indexOf('play.gif') == -1) {
		$('#thumbshow-'+id).html('<img src="'+obj.src.replace('.thumb.'+ext(obj.src), '')+'"/>');
	} else {
		var w = $('#thumbshow-'+id).parent().width() - 62;
		if(w < 160) w = 160;
		if(w > 640) w = 640;
		var h = parseInt(w*9/16);
		$('#thumbshow-'+id).html(player($(obj).data('video'),w,h,1));
	}
	$('#thumbshow-'+id).show();
}
function mm_pic_next(id) {
	var i = 0;
	var obj;
	$('#thumbs-'+id+' img').each(function() {
		if(i) {	
			obj = this;
			return false;
		}
		if($(this).attr('style').indexOf('2px') != -1) { i = 1; }
	});
	if(obj) {
		mm_pic_show(id, obj);
	} else {
		mm_pic_hide(id);
	}
}
function mm_pic_hide(id) {
	$('#thumbshow-'+id).html('');
	$('#thumbshow-'+id).hide();
	$('#thumbs-'+id+' img').each(function() {
		$(this).css('border', '#EEEEEE 1px solid');
	});
}
function mm_more(mid, id, pid) {
	$.get(AJPath+'?action=moment&job=more&moduleid='+mid+'&itemid='+id,function(data) { 
		if(data) {
			$('#content-'+id+(pid ? '-'+pid : '')).html(data);
		} else {
			$('#content-'+id+(pid ? '-'+pid : '')+' i').hide();
		}
	});
}
function mm_list_topic(mid) {
	$('.mm-topics ul').html('');
	$('#topic-kw').focus();
	$.get(AJPath+'?action=moment&job=topic&moduleid='+mid+'&kw='+encodeURIComponent($('#topic-kw').val()),function(data) { 
		if(data.length) {
			for(var k in data) {
				$('.mm-topics ul').append('<li class="jt" onclick="mm_set_topic('+data[k].itemid+', this)">#'+data[k].title+'</li>');
			}
		} else {
			$('.mm-topics ul').html('<li><b>'+L['none_topic']+'</b></li>');
		}
	}, 'json');	
	$('.mm-topics').slideDown();
}
function mm_set_topic(id, obj) {
	$('#topic-id').val(id);
	$('#topic-title').html($(obj).html());
	$('.mm-topics').slideUp();
	$('#topic-kw').val('');
	$('#topic-del').show();
}
function mm_del_topic(id, obj) {
	$('#topic-id').val('0');
	$('#topic-title').html(L['choose_topic']);
	$('.mm-topics').slideUp();
	$('#topic-kw').val('');
	$('#topic-del').hide();
}
function mm_sheet(mid, id, uid) {
	if($('#sheet-'+id).html().indexOf('<ul>') != -1) return;
	$.get(AJPath+'?action=moment&moduleid='+mid+'&job=followed&userid='+uid,function(data) {
		var html = '<ul>';
		if(data == 'ok') {
			html += '<li onclick="mm_follow('+uid+');" uid-'+uid+'="1">'+L['mm_unfollow']+'</li>';
		} else {
			html += '<li onclick="mm_follow('+uid+');" uid-'+uid+'="1">'+L['mm_follow']+'</li>';
		}
		html += '<li onclick="Dfavor('+mid+', '+id+');">'+L['mm_favor']+'</li>';
		html += '<li onclick="mm_report('+mid+', '+id+');">'+L['mm_report']+'</li>';
		html += '</ul>';
		$('#sheet-'+id).html(html);
	});
}
function mm_follow(uid) {
	$.post(AJPath, 'action=follow&userid='+uid, function(data) {
		if(data == 'ok') {
			Dtoast(L['followed']);
			$('[uid-'+uid+']').html(L['mm_unfollow']);
		} else if(data == 'ko') {
			Dtoast(L['unfollow']);
			$('[uid-'+uid+']').html(L['mm_follow']);
		} else {
			Dtoast(data);
		}
	});
}
function mm_report(mid, id) {
	Dreport(mid, id, 0, L['report_title']+id+'\n'+L['report_content']+'\n'+$('#content-'+id).html());
}
var me = '';
function me_init() {
	$('.mm-editor img').on('click', function() {
		var id = $(this).attr('id');
		var nm = id.substring(3, id.length);
		var src = '';
		if(me && me != nm) {
			src = $('#me-'+me).attr('src');
			if(src.indexOf('-on.png') != -1) $('#me-'+me).attr('src', src.replace('-on.png', '.png'));
			$('#mt-'+me).hide();
		}
		src = $(this).attr('src');
		if(src.indexOf('-on.png') == -1) {
			$(this).attr('src', src.replace('.png', '-on.png'));
			$('#mt-'+nm).slideDown(300);
			if(nm == 'video') filev.refresh();
			if(nm == 'image') fileu.refresh();
			me = nm;
		} else {
			$(this).attr('src', src.replace('-on.png', '.png'));
			$('#mt-'+nm).slideUp(300);
			me = '';
		}
	});
}
function me_into(str, job) {
	var f = 'content';
	if(job == 'at') {
		str = $.trim(str);
		str = str.replace(/@/g, '');
		str = str.replace(/#/g, '');
		if(str.length < 2) {
			Dmsg(L['me_member'], f);
			return;
		}
		if(str.indexOf(' ') == -1) {
			if(Dd(f).value.indexOf('@'+str+' ') != -1) {
				Dmsg(L['me_at']+str, f);
				return;
			}
			_into(f, '@'+str+' ');
		} else {
			var arr = str.split(' ');
			for(var i = 0; i < arr.length; i++) {
				if(Dd(f).value.indexOf('@'+arr[i]+' ') == -1) _into(f, '@'+arr[i]+' ');
			}
		}
		Dd(job).value = '';
	} else if(job == 'hash') {
		str = $.trim(str);
		str = str.replace(/#/g, '');
		if(str.length < 2) {
			Dmsg(L['me_topic'], f);
			return;
		}
		if(Dd(f).value.indexOf('#'+str+'#') != -1) {
			Dmsg(L['me_hash']+str, f);
			return;
		}
		_into(f, '#'+str+'#');
		Dd(job).value = '';
	} else {
		_into(f, ':'+str+job+')');
	}	
}