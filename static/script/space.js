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
	$('#thumbshow-'+id).html(obj.src.indexOf('play.gif') == -1 ? '<img src="'+obj.src.replace('.thumb.'+ext(obj.src), '')+'"/>' : player($(obj).data('video'),400,300,1));
	$('#thumbshow-'+id).show();
}
function mm_pic_hide(id) {
	$('#thumbshow-'+id).html('');
	$('#thumbshow-'+id).hide();
	$('#thumbs-'+id+' img').each(function() {
		$(this).css('border', '#EEEEEE 1px solid');
	});
}
function mm_more(id, par, pid) {
	$.get(AJPath+'?action=moment&'+par,function(data) { 
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
	$.get(AJPath+'?action=moment&moduleid='+mid+'&job=topic&kw='+encodeURIComponent($('#topic-kw').val()),function(data) { 
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