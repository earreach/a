/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function Print(i) {if(isIE) {window.print();} else {var i = i ? i : 'content'; var w = window.open('','',''); w.opener = null; w.document.write('<div style="width:630px;">'+Dd(i).innerHTML+'</div>'); w.window.print();}}
function addFav(t) {document.write('<a href="'+window.location.href+'" title="'+document.title.replace(/<|>|'|"|&/g, '')+'" rel="sidebar" onclick="if(UA.indexOf(\'chrome\') != -1){alert(\''+L['chrome_fav_tip']+'\');return false;}window.external.addFavorite(this.href, this.title);return false;">'+t+'</a>');}
function View(s) {window.open(DTPath+'api/view'+DTExt+'?img='+s);}
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
	if(!get_cookie('auth')) {
		if($('#destoon-login').length > 0) {Go($('#destoon-login').attr('href'));return;}
		Dtoast(L['login_tip']);return;
	}
	if($('.favorited').length > 0 && !confirm(L['favorited_tip'])){return;}
	$.post(AJPath, 'action=favorite&mid='+mid+'&itemid='+itemid, function(data) {
		if(data) {
			if(data == 'ok') {
				Dtoast(L['favorited']);
				$('.favorite').attr('class', 'favorited');
				$('.favorited b').html(parseInt($('.favorited b').html())+1);
			} else if(data == 'ko') {
				Dtoast(L['canceled']);
				$('.favorited').attr('class', 'favorite');
				$('.favorite b').html(parseInt($('.favorite b').html())-1);
			} else {
				Dtoast(data);
			}
		}
	});
}
function Dlike(mid, tid, rid) {
	if(!get_cookie('auth')) {
		if($('#destoon-login').length > 0) {Go($('#destoon-login').attr('href'));return;}
		Dtoast(L['login_tip']);return;
	}
	$.post(AJPath, 'action=like&mid='+mid+'&itemid='+tid+'&rid='+rid, function(data) {
		if(data) {
			if(rid) {
				if(data == 'ok') {
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-liked');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())+1);
					Dtoast(L['liked']);
				} else if(data == 'ok0') {
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-liked');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())+1);
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hate');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(L['liked']);
				} else if(data == 'ko') {
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-like');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(L['canceled']);
				} else {
					Dtoast(data);
				}
			} else {
				if(data == 'ok') {
					$('.like').attr('class', 'liked');
					$('.liked b').html(parseInt($('.liked b').html())+1);
					Dtoast(L['liked']);
				} else if(data == 'ok0') {
					$('.like').attr('class', 'liked');
					$('.liked b').html(parseInt($('.liked b').html())+1);
					$('.hated').attr('class', 'hate');
					$('.hate b').html(parseInt($('.hate b').html())-1);
					Dtoast(L['liked']);
				} else if(data == 'ko') {
					$('.liked').attr('class', 'like');
					$('.like b').html(parseInt($('.like b').html())-1);
					Dtoast(L['canceled']);
				} else {
					Dtoast(data);
				}
			}
		}
	});
}
function Dhate(mid, tid, rid) {
	if(!get_cookie('auth')) {
		if($('#destoon-login').length > 0) {Go($('#destoon-login').attr('href'));return;}
		Dtoast(L['login_tip']);return;
	}
	$.post(AJPath, 'action=like&job=hate&mid='+mid+'&itemid='+tid+'&rid='+rid, function(data) {
		if(data) {
			if(rid) {
				if(data == 'ok') {
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hated');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())+1);
					Dtoast(L['hated']);
				} else if(data == 'ok1') {
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hated');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())+1);
					$('#like-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-like');
					$('#like-'+mid+'-'+tid+'-'+rid).html(parseInt($('#like-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(L['hated']);
				} else if(data == 'ko') {
					$('#hate-'+mid+'-'+tid+'-'+rid).parent().attr('class', 'ui-ico-hate');
					$('#hate-'+mid+'-'+tid+'-'+rid).html(parseInt($('#hate-'+mid+'-'+tid+'-'+rid).html())-1);
					Dtoast(L['canceled']);
				} else {
					Dtoast(data);
				}
			} else {
				if(data == 'ok') {
					$('.hate').attr('class', 'hated');
					$('.hated b').html(parseInt($('.hated b').html())+1);
				} else if(data == 'ok1') {
					$('.hate').attr('class', 'hated');
					$('.hated b').html(parseInt($('.hated b').html())+1);
					$('.liked').attr('class', 'like');
					$('.like b').html(parseInt($('.like b').html())-1);
				} else if(data == 'ko') {
					$('.hated').attr('class', 'hate');
					$('.hate b').html(parseInt($('.hate b').html())-1);
					Dtoast(L['canceled']);
				} else {
					Dtoast(data);
				}
			}
		}
	});
}
function Dfollow(username) {
	if(!get_cookie('auth')) {
		if($('#destoon-login').length > 0) {Go($('#destoon-login').attr('href'));return;}
		Dtoast(L['login_tip']);return;
	}
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
				Dtoast(L['followed']);
			} else if(data == 'ko') {
				$('#follow-'+username).attr('class', 'follow0');
				$('#follow-'+username).attr('title', L['unfollow_title']);
				$('#follow-'+username+' b').html(L['follow']);
				if(num > 0) $('#follow-'+username+' i').html(--num);
				Dtoast(L['unfollow']);
			} else {
				Dtoast(data);
			}
		}
	});
}
function Dreport(mid, tid, rid, c) {
	var c = c ? c : ($('#title').length > 0 ? $('#title').html() : document.title)+'\n'+window.location.href;
	var htm = '<form method="post" action="'+DTPath+'api/report'+DTExt+'" id="dreport" target="_blank">';
	htm += '<input type="hidden" name="forward" value="'+window.location.href+'"/>';
	htm += '<input type="hidden" name="mid" value="'+mid+'"/>';
	htm += '<input type="hidden" name="itemid" value="'+tid+'"/>';
	htm += '<input type="hidden" name="rid" value="'+rid+'"/>';
	htm += '<textarea style="display:none;" name="content">'+c+'</textarea>';
	htm += '</form>';
	$('#destoon-space').html(htm);
	Dd('dreport').submit();
}
var tip_word = '';
function DSearch() {
	if($('#destoon-kw').val().length < 1) {
		$('#search-mod').hide();
		$('#destoon-kw').val('');
		$('#destoon-kw').attr('placeholder', '');
		window.setTimeout(function(){$('#destoon-kw').attr('placeholder', L['keyword_message']);}, 500);
		return false;
	}
	return true;
}
function DsMod(i, n, l) {
	if(i == searchid) {
		$('#search-mod').fadeOut('fast');
		return;
	}
	Dd('destoon-search').action = l+'search'+DTExt;searchid = i;Dd('destoon-mod').value = n;$('#search-mod').fadeOut('fast');
	$.get(AJPath+'?action=search&job=hot&mid='+i,function(data){ 
		if(data) $('.search-hot').html(data);
	});
}
function DsTip(w) {
	if(w.length < 1 || w == tip_word) return;
	tip_word = w;
	$.get(AJPath+'?action=search&job=tip&mid='+searchid+'&word='+w, function(data) {
		if(data.indexOf('onclick') != -1) {
			$('#search-tip').html('<div class="search-tip">'+data+'</div>');
			$('#search-tip').show();
			$('#search-rec').hide();
		} else {
			$('#search-tip').html('');
			$('#search-tip').hide();
		}
	});
}
function DsRec() {
	$('#search-cls').show();
	if($('#search-rec').html().indexOf('data-rec="'+searchid+'"') != -1) {
		$('#search-rec').show();
		$('#search-tip').hide();
		return;
	}
	$.get(AJPath+'?action=search&mid='+searchid, function(data) {
		if(data.indexOf('onclick') != -1) {
			$('#search-rec').html('<div class="search-rec" data-rec="'+searchid+'">'+data+'</div>');
			$('#search-rec').show();
			$('#search-tip').hide();
		} else {
			$('#search-rec').hide();
			$('#search-rec').html('');
		}
	});
}
function DsDel() {	
	$.post(AJPath, 'action=search&job=del&mid='+searchid, function(data) {
		if(data == 'ok') {
			$('#search-rec').html('');
			$('#search-rec').hide();
			$('#destoon-kw').focus();
		}
	});
}
function DsKW(w) {$('#destoon-kw').val(w); $('#destoon-search').submit();}
function user_login() {
	if(Dd('user_name').value.length < 2) {Dd('user_name').focus(); return false;}
	if(Dd('user_pass').value == 'password' || Dd('user_pass').value.length < 6) {Dd('user_pass').focus(); return false;}
}
function show_answer(u, i) {document.write('<iframe src="'+u+'answer'+DTExt+'?itemid='+i+'" name="destoon_answer" id="des'+'toon_answer" style="width:100%;height:0px;" scrolling="no" frameborder="0"></iframe>');}
function Dtask(p, s) {$.getScript(DTPath+'api/task'+DTExt+'?'+p+(s ? '&screenw='+window.screen.width+'&screenh='+window.screen.height+'&refer='+encodeURIComponent(document.referrer) : '')+'&refresh='+Math.random()+'.js');}
var sell_n = 0;
function sell_tip(o, i) {
	if(o.checked) {sell_n++; Dd('item_'+i).style.backgroundColor='#F1F6FC';} else {Dd('item_'+i).style.backgroundColor='#FFFFFF'; sell_n--;}
	if(sell_n < 0) sell_n = 0;
	if(sell_n > 1) {
		var aTag = o; var leftpos = toppos = 0;
		do {aTag = aTag.offsetParent; leftpos	+= aTag.offsetLeft; toppos += aTag.offsetTop;
		} while(aTag.offsetParent != null);
		var X = o.offsetLeft + leftpos - 10;
		var Y = o.offsetTop + toppos - 70;
		Dd('sell_tip').style.left = X + 'px';
		Dd('sell_tip').style.top = Y + 'px';
		o.checked ? Ds('sell_tip') : Dh('sell_tip');
	} else {
		Dh('sell_tip');
	}
}
function img_tip(o, i) {
	if(i) {
		if(i.indexOf('nopic.gif') == -1) {
			if(i.indexOf('.thumb.') != -1) {var t = i.split('.thumb.');var s = t[0];} else {var s = i;}
			var aTag = o; var leftpos = toppos = 0;
			do {aTag = aTag.offsetParent; leftpos	+= aTag.offsetLeft; toppos += aTag.offsetTop;
			} while(aTag.offsetParent != null);
			var X = o.offsetLeft + leftpos + 90;
			var Y = o.offsetTop + toppos - 20;
			Dd('img_tip').style.left = X + 'px';
			Dd('img_tip').style.top = Y + 'px';
			Ds('img_tip');
			Inner('img_tip', '<img src="'+s+'" onload="if(this.width<200) {Dh(\'img_tip\');}else if(this.width>300){this.width=300;}Dd(\'img_tip\').style.width=this.width+\'px\';"/>')
		}
	} else {
		Dh('img_tip');
	}
}
function Dqrcode() {
	var url = $('meta[http-equiv=mobile-agent]').attr('content');
	url = url ? url.substr(17) : window.location.href;
	if($('#destoon-qrcode').length > 0) {
		if($('#destoon-qrcode').html().length < 10) {
			$('#destoon-qrcode').css({'position':'fixed','z-index':'99999','left':'50%','top':'0','margin-left':'-130px','width':'260px','background':'#FFFFFF','text-align':'center'});
			$('#destoon-qrcode').html('<div style="text-align:right;color:#555555;font-size:16px;font-family:Verdana;font-weight:100;padding-right:6px;cursor:pointer;">x</div><img src="'+DTPath+'api/qrcode'+DTExt+'?auth='+encodeURIComponent(url)+'" width="140" height="140"/><div style="padding:10px 0;font-size:14px;font-weight:bold;color:#555555;">'+L['scan_open']+'</div><div style="padding-bottom:20px;color:#999999;">'+L['scan_tool']+'</div>');
			$('#destoon-qrcode').click(function(){$('#destoon-qrcode').fadeOut('fast');});
		}
		$('#destoon-qrcode').fadeIn('fast');
	}
}
function Dmobile() {
	var url = $('meta[http-equiv=mobile-agent]').attr('content');
	Go(DTPath+'api/mobile'+DTExt+(url ? '?uri='+encodeURIComponent(url.substr(17)) : ''));
}
function Dhot() {
	if($('.search-hot')) {
		window.setInterval(function() {
			if($('.search-hot a').length > 5) {
				$('.search-hot').append($('.search-hot a:first').prop('outerHTML'));
				$('.search-hot a:first').fadeOut(300, function() {
					$('.search-hot a:first').remove();
				});
			}
		},  5000);
	}
}
function Dfixon() {
	if($('#destoon-fixon')) {
		$(window).on("scroll.fixon", function() {
			if($(document).scrollTop() > 122) {
				$('.menu').css('margin-top', '80px');
				$('#destoon-fixon').addClass('fixon');
				//$('#destoon-fixon').animate({height:'80px'},300);
				$('#destoon-fixon').slideDown(300);
			} else {
				$('.menu').css('margin-top', '0');
				$('#destoon-fixon').removeClass('fixon');
			}
		});
	}
	$('#head').click(function(e) {
		if(e.target.nodeName == 'DIV') $('html, body').animate({scrollTop:0}, 200);
	});
}
function Dusercard(mid, obj) {
	$(obj).on('mouseover', function() {
		var username = cutstr($(this).attr('src'), 'username=', '&');
		if(username.match(/^[a-z0-9_\-]{2,30}$/)) {
			var xy = $(this).offset(); 
			if($('#destoon-usercard').html().indexOf('card-'+username) != -1) {
				$('#card-'+username).css({'top':xy.top-$(window).scrollTop(),'left':xy.left});
				$('#card-'+username).fadeIn(300);
			} else {
				$.get(AJPath+'?action=card&job=user&moduleid='+mid+'&username='+username, function(data) {
					if(data.indexOf('card-'+username) != -1) {
						$('#destoon-usercard').append(data);
						$('#card-'+username).css({'top':xy.top-$(window).scrollTop(),'left':xy.left});
						$('#card-'+username).on('mouseleave', function() {
							$(this).fadeOut(200);
						});
					}
				});
			}
		}
	});
}
function Dcard(job, obj) {
	if(job != 'member' && !$(obj).text().match(/[1-9]/)) return;
	if($('#destoon-card').attr('data-job') == job) return;
	var xy = $(obj).offset();
	window.setTimeout(function(){
		$('#destoon-card').html('<br/><br/><br/><br/><br/><br/>');
		$('#destoon-card').attr('data-job', job);
		$('#destoon-card').css({'left':xy.left-280});
		$('#destoon-card').fadeIn(300);
		$.get(AJPath+'?action=card&job='+job, function(data) {
			$('#destoon-card').html(data);
		});
		$('#destoon-card').on('mouseleave', function() {
			$(this).fadeOut(300, function() {
				$(this).html('<br/><br/><br/><br/><br/><br/>');
				$('#destoon-card').attr('data-job', '');
			});
		});
	}, 300);
}
function oauth_logout() {
	set_cookie('oauth_site', '');
	set_cookie('oauth_user', '');
	window.location.reload();
}