/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
var dgX = dgY = 0; var dgDiv, dWin;
function mkDialog(u, c, t, w, s, p, px, py) {
	w = w ? w : 300;
	u = u ? u : '';
	c = c ? c : (u ? '<iframe src="'+u+'" width="'+(w-25)+'" height="0" border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" scrolling="no" style="display:block;"></iframe>' : '');
	t = t ? t : L['system_tips'];
	s = s ? s : 0;
	p = p ? p : 0;
	px = px ? px : 0;
	py = py ? py : 0;
	var cw = $(document).width();
	var ch = $(window).height();
	var bsh = $(document).height();
	var bst = $(document).scrollTop();
	var bsl = $(document).scrollLeft();
	var bh = parseInt((bsh < ch) ? ch : bsh);
	$('#Dmid').remove();$('#Dtop').remove();
	if(!s) {
		var Dmid = document.createElement("div");
		Dmid.id = "Dmid";
		document.body.appendChild(Dmid);
		$('#Dmid').css({'zIndex':9998,'position':'absolute','width':cw+'px','height':bh+'px','overflow':'hidden','top':0,'left':0,'border':'none','background':'#DDDDDD','opacity':0.5});
		$('#Dmid').click(function(){cDialog();});
	}
	var sl = px ? px : bsl + parseInt((cw-w)/2);
	var st = py ? py : bst + parseInt(ch/2) - 100;
	var Dtop = document.createElement("div");
	Dtop.id = 'Dtop';
	document.body.appendChild(Dtop);
	$('#Dtop').css({'zIndex':9999,'position':'absolute','width':w+'px','left':sl+'px','top':st+'px','display':'none'});
	$('#Dtop').html('<div class="dbody"><div class="dhead" ondblclick="cDialog();" onmousedown="dragstart(\'Dtop\', event);"  onmouseup="dragstop(event);" onselectstart="return false;"><span onclick="cDialog();" title="'+L['dialog_close']+'"></span>'+t+'</div><div class="dbox">'+c+'</div></div>');
	Eh();
	$('#Dtop').show(1, function() {
		st = py ? py : bst + parseInt(ch/2) - parseInt($('#Dtop').height()/2);
		$('#Dtop').animate({top:st+'px'}, 1, function() {
			if(c.indexOf('DP_image') != -1) {
				var _stop = 0;
				$('.DP_image').on('load', function() {
					var iw = $('.DP_image').width();
					if(iw < 100) iw = 100;
					var _sl = px ? px : bsl + parseInt((cw-iw)/2);
					if(_sl < 10) _sl = 10;
					var _st = py ? py : bst + parseInt((ch-$('.DP_image').height())/2);
					if(_st < 10) _st = 10;
					$('#Dtop').animate({width:iw+'px',left:_sl+'px',top:_st+'px'}, 50);
					_stop = 1;
				});
				if(!_stop) {
					$('.DP_image').ready(function() {
						var iw = $('.DP_image').width();
						if(iw < 100) iw = 100;
						var _sl = px ? px : bsl + parseInt((cw-iw)/2);
						if(_sl < 10) _sl = 10;
						var _st = py ? py : bst + parseInt((ch-$('.DP_image').height())/2);
						if(_st < 10) _st = 10;
						$('#Dtop').animate({width:iw+'px',left:_sl+'px',top:_st+'px'}, 50);
					});
				}
			}
		});
	});
}
function cDialog() {
	if(dWin) dWin.close();
	$('#Dmid').remove();
	$('#Dtop').fadeOut('fast', function() {
		$('#Dtop').remove();
		Es();
	});
}
function Dalert(c, w, s, t) {
	if(!c) return;
	s = s ? s : 0; w = w ? w : 350; t = t ? t : 0;
	c = '<div style="padding:16px 16px 0 16px;">'+c+'</div><div style="padding:16px;text-align:center;"><input type="button" class="btn-s" value=" '+L['ok']+' " onclick="cDialog();"/></div>';
	mkDialog('', c, '', w, s);
	if(t) window.setTimeout(function(){cDialog();}, t);
}
function Dconfirm(c, u, w, s) {
	if(!c) return;
	s = s ? s : 0; w = w ? w : 350; 
	var d = u ? (u.indexOf('logout') == -1 ? 'window' : 'top')+".location = '"+u+"'" : 'cDialog()';
	c = '<div style="padding:16px 16px 0 16px;">'+c+'</div><div style="padding:16px;text-align:center;"><input type="button" class="btn-s" value=" '+L['ok']+' " onclick="'+d+'"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn-c" value=" '+L['cancel']+' " onclick="cDialog();"/></div>';
	mkDialog('', c, '', w, s);
}
function Diframe(u, w, s, l, t) {
	s = s ? s : 0; w = w ? w : 350; l = l ? true : false;
	var c = '<iframe src="'+u+'" width="'+(w-25)+'" height=0" id="diframe" border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" scrolling="no" style="display:block;"></iframe><div style="padding:16px;text-align:center;"><input type="button" class="btn-s" value=" '+L['ok']+' " onclick="cDialog();"/></div>';
	if(l) c = '<div id="dload" style="line-height:48px;text-align:center;">Loading...</div>'+c;
	mkDialog('', c, t, w, s);
}
function Dtip(c, w, t) {
	if(!c) return;
	w = w ? w : 350; t = t ? t : 2000;
	mkDialog('', '<div style="padding:16px;">'+c+'</div>', '', w);
	window.setTimeout(function(){cDialog();}, t);
}
function Dfile(m, o, i, e) {
	e = e ? e : '';
	var c = '<iframe name="UploadFile" style="display:none;" src=""></iframe>';
	c += '<form method="post" target="UploadFile" enctype="multipart/form-data" action="'+UPPath+'" onsubmit="return isImg(\'upfile\',\''+e+'\');"><input type="hidden" name="moduleid" value="'+m+'"/><input type="hidden" name="from" value="file"/><input type="hidden" name="old" value="'+o+'"/><input type="hidden" name="fid" value="'+i+'"/><table cellpadding="6" style="margin-bottom:10px;"><tr><td style="word-break:break-all;"><input id="upfile" type="file" size="20" name="upfile" onchange="if(isImg(\'upfile\',\''+e+'\')){this.form.submit();Dd(\'Dsubmit\').disabled=true;Dd(\'Dsubmit\').value=\''+L['uploading']+'\';}"/>'+(e ? '<div style="width:210px;line-height:20px;padding-top:10px;word-break:break-all;color:#999999;">'+L['upload_allow']+e+'</div>' : '')+'</td></tr><tr><td><input type="submit" class="btn-s" value="'+L['upload']+'" id="Dsubmit"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn-c" value="'+L['cancel']+'" onclick="cDialog();"/></td></tr></table></form>';
	mkDialog('', c, L['upload_file'], 256);
}
function Dthumb(m, w, h, o, s, i) {
	s = s ? 'none' : ''; i = i ? i : 'thumb'; 
	var c = '<iframe name="UploadThumb" style="display:none;" src=""></iframe>';
	c += '<form method="post" target="UploadThumb" enctype="multipart/form-data" action="'+UPPath+'" onsubmit="return isUP(\'upthumb\');" id="upform"><input type="hidden" name="moduleid" value="'+m+'"/><input type="hidden" name="from" value="thumb"/><input type="hidden" name="old" value="'+o+'"/><input type="hidden" name="fid" value="'+i+'"/><table cellpadding="6" style="margin-bottom:10px;"><tr><td><label><input id="remote_0" type="radio" name="isremote" value="0" checked onclick="ReLo(0, \'upthumb\');"/> '+L['up_local']+'</label>&nbsp;&nbsp;&nbsp;<label><input id="remote_1" type="radio" name="isremote" value="1" onclick="ReLo(1, \'upthumb\');"/> '+L['up_remote']+'</label>&nbsp;&nbsp;&nbsp;<label><input id="remote_2" type="radio" name="isremote" value="2" onclick="ReLo(2, \'upthumb\', '+m+');"/> '+L['up_album']+'</label></td></tr><tr id="remote_url" style="display:none;"><td><input id="remote" type="text" size="30" name="remote" placeholder="'+cutstr(DTPath, '', '://')+'://'+'"/></td></tr><tr id="local_url"><td><input id="upthumb" type="file" name="upthumb" accept="image/*" onchange="if(isImg(\'upthumb\')){this.form.submit();Dd(\'Dsubmit\').disabled=true;Dd(\'Dsubmit\').value=\''+L['uploading']+'\';}"/></td></tr><tr style="display:'+s+'"><td>'+L['width']+' <input type="text" size="3" name="width" value="'+(w > 0 ? w : '')+'"/> px &nbsp;&nbsp;&nbsp;'+L['height']+' <input type="text" size="3" name="height" value="'+(h > 0 ? h : '')+'"/> px </td></tr><tr><td><input type="submit" class="btn-s" value="'+L['upload']+'" id="Dsubmit"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn-c" value="'+L['cancel']+'" onclick="cDialog();"/></td></tr></table></form>';
	mkDialog('', c, L['upload_img'], 256);
}
function Dalbum(f, m, w, h, o, s) {
	s = s ? 'none' : ''; 
	var c = '<iframe name="UploadAlbum" style="display:none" src=""></iframe>';
	c += '<form method="post" target="UploadAlbum" enctype="multipart/form-data" action="'+UPPath+'" onsubmit="return isUP(\'upalbum\');" id="upform"><input type="hidden" name="fid" value="'+f+'"/><input type="hidden" name="moduleid" value="'+m+'"/><input type="hidden" name="from" value="album"/><input type="hidden" name="old" value="'+o+'"/><table cellpadding="6" style="margin-bottom:10px;"><tr><td><label><input id="remote_0" type="radio" name="isremote" value="0" checked onclick="ReLo(0, \'upalbum\');"/> '+L['up_local']+'</label>&nbsp;&nbsp;&nbsp;<label><input id="remote_1" type="radio" name="isremote" value="1" onclick="ReLo(1, \'upalbum\');"/> '+L['up_remote']+'</label>&nbsp;&nbsp;&nbsp;<label><input id="remote_2" type="radio" name="isremote" value="2" onclick="ReLo(2, \'upalbum\', '+m+');"/> '+L['up_album']+'</label></td></tr><tr id="remote_url" style="display:none;"><td><input id="remote" type="text" size="30" name="remote" value=""  placeholder="'+cutstr(DTPath, '', '://')+'://'+'"/></td></tr><tr id="local_url"><td><input id="upalbum" type="file" size="20" name="upalbum" accept="image/*" multiple="multiple" onchange="if(isImg(\'upalbum\')){this.form.submit();Dd(\'Dsubmit\').disabled=true;Dd(\'Dsubmit\').value=\''+L['uploading']+'\';}"/></td></tr><tr style="display:'+s+'"><td>'+L['width']+' <input type="text" size="3" name="width" value="'+(w > 0 ? w : '')+'"/> px &nbsp;&nbsp;&nbsp;'+L['height']+' <input type="text" size="3" name="height" value="'+(h > 0 ? h : '')+'"/> px </td></tr><tr><td><input type="submit" class="btn-s" value="'+L['upload']+'" id="Dsubmit"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn-c" value="'+L['cancel']+'" onclick="cDialog();"/></td></tr></table></form>';
	mkDialog('', c, L['upload_img'], 256);
}
function Dphoto(f, m, w, h, o, s) {
	s = s ? 'none' : ''; 
	var c = '<iframe name="UploadPhoto" style="display:none" src=""></iframe>';
	c += '<form method="post" target="UploadPhoto" enctype="multipart/form-data" action="'+UPPath+'" onsubmit="return isUP(\'upalbum\');" id="upform"><input type="hidden" name="fid" value="'+f+'"/><input type="hidden" name="moduleid" value="'+m+'"/><input type="hidden" name="from" value="photo"/><input type="hidden" name="old" value="'+o+'"/><table cellpadding="6" style="margin-bottom:10px;"><tr><td><label><input id="remote_0" type="radio" name="isremote" value="0" checked onclick="ReLo(0, \'upalbum\');"/> '+L['up_local']+'</label>&nbsp;&nbsp;&nbsp;<label><input id="remote_1" type="radio" name="isremote" value="1" onclick="ReLo(1, \'upalbum\');"/> '+L['up_remote']+'</label>&nbsp;&nbsp;&nbsp;<label><input id="remote_2" type="radio" name="isremote" value="2" onclick="ReLo(2, \'upalbum\', '+m+');"/> '+L['up_album']+'</label></td></tr><tr id="remote_url" style="display:none;"><td><input id="remote" type="text" size="30" name="remote" value=""  placeholder="'+cutstr(DTPath, '', '://')+'://'+'"/></td></tr><tr id="local_url"><td><input id="upalbum" type="file" size="20" name="upalbum" accept="image/*" onchange="if(isImg(\'upalbum\')){this.form.submit();Dd(\'Dsubmit\').disabled=true;Dd(\'Dsubmit\').value=\''+L['uploading']+'\';}"/></td></tr><tr style="display:'+s+'"><td>'+L['width']+' <input type="text" size="3" name="width" value="'+(w > 0 ? w : '')+'"/> px &nbsp;&nbsp;&nbsp;'+L['height']+' <input type="text" size="3" name="height" value="'+(h > 0 ? h : '')+'"/> px </td></tr><tr><td><input type="submit" class="btn-s" value="'+L['upload']+'" id="Dsubmit"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" class="btn-c" value="'+L['cancel']+'" onclick="cDialog();"/></td></tr></table></form>';
	mkDialog('', c, L['upload_img'], 256);
}
function Dwidget(u, t, w, h, s) {
	if(!u) return window.parent.document.getElementById('Dtop') == null ? false : true;
	w = w ? w : (document.body.scrollWidth - 100);
	h = h ? h : ($(window).height() - 100);
	s = s ? s : 'auto';
	mkDialog('', '<iframe src="'+u+(u.indexOf('?')==-1 ? '?' : '&')+'widget=1" width="100%" height="'+h+'" border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" scrolling="'+s+'" style="display:block;"></iframe>', t, w+20, 0, 0);
}
function TabAll() {
	var i = 0;
	while($('#Tab'+i).length > 0) {
		if(all) {
			if($('#Tab'+i).attr('class') == 'tab_on'){$('#Tabs'+i).show();}else{$('#Tabs'+i).hide();}
		} else {
			$('#Tabs'+i).show();
		}
		i++;
	}
	var str = $('#ShowAll').attr('title').split('/');
	$('#ShowAll').val(all ? str[0] : str[1]);
	if(all && $('.highlight').length > 0) {
		var id = $('.highlight:first').parents('div').attr('id');
		if(id.indexOf('Tabs') != -1) {
			id = id.replace('Tabs', '');
			Tab(id);
		}
	}
	all = all ? 0 : 1;
}
function addAlbum(url, max) {
	for(var i = 0; i < max; i++) {
		if($('#thumb'+i).length) {
			if($('#thumb'+i).val() == '') {
				$('#thumb'+i).val(url);
				$('#showthumb'+i).attr('src', url);
				return;
			}
		} else {
			$('#thumbs').append($('#thumbtpl').html().replace(/-99/g, i));
			$('#thumb'+i).val(url);
			$('#showthumb'+i).attr('src', url);
			return;
		}
	}
	$('#thumbmuti').hide();
}
function newAlbum(max) {
	for(var i = 0; i < max; i++) {
		if($('#thumb'+i).length < 1) {
			$('#thumbs').append($('#thumbtpl').html().replace(/-99/g, i));
			return;
		}
		if($('#thumb'+i).val() == '') return;
	}
}
function _preview(s, t) {
	var t = t ? true : false;
	if(s) {
		s = cutstr(s, '', '?');
		var x = ext(s);
		if(x == 'jpg' || x == 'jpeg' || x == 'png' || x == 'gif' || x == 'bmp') {
			if(t) s = cutstr(s, '', '.thumb.');	
			if(s.indexOf('.thumb.') != -1) s = s.replace('.thumb.', '.middle.');
			mkDialog('', '<img src="'+s+'" style="display:block;" class="DP_image" onclick="cDialog();"/>', L['preview_img']);
		} else if(x == 'mp4') {
			mkDialog('', '<div style="width:800px;height:450px;background:#000000;"><video src="'+s+'?v='+RandStr(6)+'" width="800" height="450" autoplay="autoplay" controls="controls"></video></div>', L['preview_mp4'], 800);
		} else if(x == 'mp3') {
			mkDialog('', '<div style="width:320px;height:64px;text-align:center;padding:10px 0 0 0;"><audio src="'+s+'?v='+RandStr(6)+'" autoplay="autoplay" controls="controls"></audio></div>', L['preview_mp3'], 320);
		} else if(x == 'pdf') {
			Dwidget(s+'?v='+RandStr(6), L['preview_pdf']);
		} else if(x == 'doc' || x == 'docx' || x == 'xls' || x == 'xlsx' || x == 'ppt' || x == 'pptx') {
			Dwidget(DTPath+'api/view'+DTExt+'?job=preview&url='+encodeURIComponent(s), L['preview_doc']);
		} else {
			window.open(s);
		}
	} else {
		Dtip(L['preview_url']);
	}
}
function getAlbum(v, i) {Dd('thumb'+i).value = v; Dd('showthumb'+i).src = v;if(typeof album_max != 'undefined') {newAlbum(album_max);}}
function delAlbum(i, s) {Dd('thumb'+i).value = ''; Dd('showthumb'+i).src = DTPath+'static/image/upload-image.png';$('#thumbmuti').show();}
function ReLo(r, i, m) {if(r) {Dd(i).value = '';Ds('remote_url');Dh('local_url');} else {Dd('remote').value = '';Dh('remote_url');Ds('local_url');} if(r == 2) {Dwindow(AJPath+'?action=choose&moduleid='+m+'&from='+(i == 'upthumb' ? 'thumb' : 'album')+'&fid='+i, 888);}}
function isUP(i) {if(Dd('remote_0').checked) {return isImg(i);} else {if(Dd('remote').value.length < 18) {confirm(L['type_imgurl']); return false;} else {Dd('Dsubmit').disabled=true;Dd('Dsubmit').value=L['uploading'];}}}
function isImg(i, e) {var v = Dd(i).value;if(v == '') {confirm(L['choose_file']); return false;}var t = ext(v);var a = typeof e == 'undefined' ? 'jpg|jpeg|png|gif|bmp' : e;if(a.length > 2 && a.indexOf(t) == -1) {confirm(L['upload_ext']+t+' '+L['upload_allow']+a); return false;}return true;}
function check_box(f, t) {var t = t ? true : false; var box = Dd(f).getElementsByTagName('input'); for(var i = 0; i < box.length; i++) {box[i].checked = t;}}
function schcate(i) {Dh('catesch'); var name = prompt(L['type_category'], ''); if(name){$.post(AJPath, 'moduleid='+i+'&action=schcate&name='+name, function(data) {Ds('catesch'); Dd('catesch').innerHTML = data ? '<strong>'+L['related_found']+'</strong><br/>'+data : '<span class="f_red">'+L['related_not_found']+'</span>';});}}
function reccate(i, o) {if(Dd(o).value.length > 1) {Dh('catesch');$.post(AJPath, 'moduleid='+i+'&action=reccate&name='+Dd(o).value, function(data) {Ds('catesch'); Dd('catesch').innerHTML = data ? '<strong>'+L['related_found']+'</strong><br/>'+data : '<span class="f_red">'+L['related_not_found']+'</span>';});}}
function ckpath(m, i) {if(Dd('filepath').value.length > 4) {$.post(AJPath, 'moduleid='+m+'&action=ckpath&itemid='+i+'&path='+Dd('filepath').value, function(data) {Dd('dfilepath').innerHTML = data;});} else {alert(L['type_valid_filepath']); Dd('filepath').focus();}}
function tpl_edit(f, d, i) {var v = Dd('destoon_template_'+i).firstChild.value; var n = v ? v : f; Dwidget('?file=template&action=edit&fileid='+n+'&dir='+d, L['tpl_edit']);}
function tpl_add(f, d) {Dwidget('?file=template&action=add&type='+f+'&dir='+d, L['tpl_add']);}
function _ip(i) {mkDialog('', '<iframe src="?file=ip&js=1&ip='+i+'" width="180" height=30" border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" scrolling="no" style="display:block;"></iframe>', 'IP:'+i, 200, 0, 0);}
function _mobile(i) {mkDialog('', '<iframe src="?file=mobile&js=1&mobile='+i+'" width="180" height=30" border="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" scrolling="no" style="display:block;"></iframe>', i, 200, 0, 0);}
function _user(n, f) {if(n){var f = f ? f : 'username';Dwidget('?moduleid=2&action=show&dialog=1&'+f+'='+n, lang(L['dialog_user'], [n]));}}
function _islink() {if(Dd('islink').checked) {Ds('link'); Dh('basic'); Df('linkurl'); if(Dd('linkurl').value == '') { Dd('linkurl').value = '';}} else {Dh('link'); Ds('basic');}}
function _delete() {return confirm(L['confirm_del']);}
function _into(i, str) {var o = Dd(i);if(typeof document.selection != 'undefined') {o.focus();var r = document.selection.createRange(); var ctr = o.createTextRange(); var i; var s = o.value; var w = "www.d"+"e"+"s"+"t"+"o"+"o"+"n.com";r.text = w;i = o.value.indexOf(w);	r.moveStart("character", -w.length);r.text = '';o.value = s.substr(0, i) + str + s.substr(i, s.length);ctr.collapse(true);ctr.moveStart("character", i + str.length);ctr.select();} else if(o.setSelectionRange) {var s = o.selectionStart; var e = o.selectionEnd; var a = o.value.substring(0, s); var b = o.value.substring(e);o.value = a + str + b;} else {Dd(i).value = Dd(i).value + str;o.focus();}}
function pagebreak() {EditorAPI('content', 'ins', '<hr class="de-pagebreak"/>');}
function RandStr(l) {l=l?l:18;var chars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";var str = '';for(var i=0;i<l;i++){str += chars.charAt(Math.floor( Math.random()*chars.length));}return str;}
function select_item(m, f) {f = f ? f : '';Dwidget(AJPath+'?action=choose&mid='+m+'&job=item&from='+f, L['choose_item']);}
function Menuon(i) {$('#Tab'+i).attr('class', 'tab_on');}
function type_reload() {if(Dd('Dtop') == null) { $.get(AJPath+'?action=type&item='+type_item+'&name='+type_name+'&default='+type_default+'&itemid='+type_id,function(data){ $('#type_box').html(data);});clearInterval(type_interval);}}
function Dn(r) {var r = r ? 1 : 0;if(Dd('msg').checked) {Dd('sms').disabled = false;Dd('wec').disabled = false;} else { Dd('sms').checked = false;Dd('wec').checked = false;Dd('sms').disabled = true;Dd('wec').disabled = true;}if(r && (Dd('msg').checked || Dd('eml').checked) && (Dd('reason').value.length > 2 || Dd('reason').value == L['op_reason'])) {alert(L['op_reason_null']);Dd('reason').focus();}}
function CloudSplit(f, t) {if(Dd(f).value.length > 4) {$.post(AJPath, 'action=split&text='+encodeURIComponent(Dd(f).value), function(data) {Dd(t).value = data;});} else {Dd(f).focus();}}
var MMove = 1;
function dragstart(i, e) {dgDiv = Dd(i); if(!e) {e = window.event;} dgX = e.clientX - parseInt(dgDiv.style.left); dgY = e.clientY - parseInt(dgDiv.style.top); document.onmousemove = dragmove;}
function dragmove(e) {if(!e) {e = window.event;} if(!MMove) return; dgDiv.style.left = (e.clientX - dgX) + 'px';  dgDiv.style.top = (e.clientY - dgY) + 'px';}
function dragstop() {dgX = dgY = 0; document.onmousemove = null;}
document.onmouseup = function(e){MMove = 0;}
document.onmousedown = function(e){MMove = 1;}
$(document).keyup(function(e){var k = e.which || e.keyCode;if(k == 27 && Dd('Dtop') != null) cDialog();});