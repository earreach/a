/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
function Mshow(i) {
	if(i == 'comment') {
		Dd('t_detail').className = 'mall_tab_1';
		Dd('t_comment').className = 'mall_tab_2';
		Dh('c_detail');
		Ds('c_comment');
		comment_load(0);
	} else {
		Dd('t_detail').className = 'mall_tab_2';
		Dd('t_comment').className = 'mall_tab_1';
		Ds('c_detail');
		Dh('c_comment');
	}
	$('html, body').animate({scrollTop:$('.mall_tab').offset().top-128}, 200);
}
function addE(i) {
	$('#p'+i+' li').click(function() {
		$('#p'+i+' li')[s_s[i]].className = 'nv_1';
		this.className = 'nv_2';
		s_s[i] = $(this).index();
		if(mallmode == 1) {
			if(i == 1) {
				var prices = $('#p1').data('prices').split('|');
				$('#mall-price').html(prices[s_s[i]]);
			}
		} else if(mallmode == 3) {			
			var key = s_s[1]+'-'+s_s[2]+'-'+s_s[3];
			var obj = typeof mallstocks[key] == 'undefined' ? '' : mallstocks[key];
			var pic = $('#thumbs img:first').attr('src');
			if(obj) {
				$('#mall-price').html(obj.price);
				$('#mall-amount').html(obj.amount);
				$('#thumbs .ab_on').attr('class', 'ab_im');
				$('#mid_pic').attr('src', obj.thumb.replace('.thumb.', '.middle.'));
				$('.btn-buy,.btn-cart').attr('disabled', obj.amount > 0 ? false : true);
			} else {
				$('#mall-price').html($('#mall-price').data('price'));
				$('#mall-amount').html('0');
				$('#mid_pic').attr('src', pic.replace('.thumb.', '.middle.'));
				$('.btn-buy,.btn-cart').attr('disabled', true);
			}
		}
	});
}
function BuyNow() {
	Go(mallurl+'buy'+DTExt+'?mid='+mallmid+'&itemid='+mallid+'&s1='+s_s[1]+'&s2='+s_s[2]+'&s3='+s_s[3]+'&a='+Dd('amount').value);
}
function AddCart() {
	Go(mallurl+'cart'+DTExt+'?mid='+mallmid+'&itemid='+mallid+'&s1='+s_s[1]+'&s2='+s_s[2]+'&s3='+s_s[3]+'&a='+Dd('amount').value);
}
function Malter(t, min, max) {
	var a = parseInt($('#amount').val());
	if(t == '+') {
		if(a >= max) {
			Dd('amount').value = max;
		} else {
			Dd('amount').value = a + 1;
		}
	} else if(t == '-') {
		if(Dd('amount').value <= min) {
			Dd('amount').value = min;
		} else {
			Dd('amount').value = a - 1;
		}
	} else {
		if(a > max) Dd('amount').value = max;
		if(a < min) Dd('amount').value = min;
	}
	if(mallmode == 2) {
		a = parseInt($('#amount').val());
		var a3 = $('#data-step').data('a3');
		if(a3 > 1 && a > a3) {
			$('#mall-price').html($('#data-step').data('p3'));
			return;
		}
		var a2 = $('#data-step').data('a2');
		if(a2 > 1 && a > a2) {
			$('#mall-price').html($('#data-step').data('p2'));
			return;
		}
		$('#mall-price').html($('#data-step').data('p1'));
	}
}

function comment_load(p) {
	if(n_c == 0) {
		Dd('c_comment').innerHTML = '<div class="comment-empty">'+$('#c_comment').data('nocomments')+'</div>';
		return;
	}
	if(p == 0 && Dd('c_comment').innerHTML != c_c) return;
	$('#c_comment').load(AJPath+'?action=mall&job=comment&moduleid='+mallmid+'&sum='+n_c+'&itemid='+mallid+'&page='+p);
}
function comment_filter() {
	var star = 0;
	for(var i = 0; i < 6; i++) {
		if(Dd('ss_'+i).checked) {
			star = i;
			break;
		}
	}
	var par = '1&star='+star;
	if(Dd('ss_t').checked) par += '&thumb=1';
	if(Dd('ss_v').checked) par += '&video=1';
	comment_load(par);
}
function comment_thumb_show(id, obj) {
	$('#thumbs-'+id+' img').each(function() {
		if($(this).attr('src') == obj.src) {
			$(this).css('border', '#FF6600 2px solid');
		} else {
			$(this).css('border', '#EEEEEE 1px solid');
		}
	});
	$('#thumbshow-'+id).html(obj.src.indexOf('play.gif') == -1 ? '<img src="'+obj.src.replace('.thumb.'+ext(obj.src), '')+'"/>' : player($(obj).data('video'),480,270,1));
	$('#thumbshow-'+id).show();
}
function comment_thumb_next(id) {
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
		comment_thumb_show(id, obj);
	} else {
		comment_thumb_hide(id);
	}
}
function comment_thumb_hide(id) {
	$('#thumbshow-'+id).html('');
	$('#thumbshow-'+id).hide();
	$('#thumbs-'+id+' img').each(function() {
		$(this).css('border', '#EEEEEE 1px solid');
	});
}