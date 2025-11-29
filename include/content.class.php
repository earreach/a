<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
class DC {
	public static function format($content, $pc = 1) {
		if(strpos($content, '<video') !== false) {
			if(preg_match_all("/<video[^>]*>(.*?)<\/video>/i", $content, $matches)) {
				foreach($matches[0] as $m) {
					$content = str_replace($m, self::video($m), $content);
				}
			}
		}
		if(strpos($content, '<embed') !== false) {
			if(preg_match_all("/<embed[^>]*>(.*?)<\/embed>/i", $content, $matches)) {
				foreach($matches[0] as $m) {
					$content = str_replace($m, self::video($m), $content);
				}
			}
		}
		if($pc) {
			//
		} else {
			if(strpos($content, 'href="') !== false) {
				if(preg_match_all("/href=\"([^\"]*)\"/i", $content, $matches)) {
					foreach($matches[0] as $m) {
						$n = moburl($m);
						if(strpos($n, 'attach'.DT_EXT) !== false || strpos($n, 'space') !== false) $n = str_replace('href=', 'rel="external" href=', $n);
						$content = str_replace($m, $n, $content);
					}
				}
				$content = str_replace(' target="_blank"', '', $content);
			}
		}
		return $content;
	}
	
	public static function keylink($content, $item, $pc = 1) {
		global $KEYLINK;
		$KEYLINK or $KEYLINK = cache_read('keylink-'.$item.'.php');
		if(!$KEYLINK) return $content;
		$data = $content;
		foreach($KEYLINK as $k=>$v) {
			$quote = str_replace(array("'", '-'), array("\'", '\-'), preg_quote($v['title']));
			if($pc) {
				$data = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$quote.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', '<a href="'.$v['url'].'" target="_blank"><strong class="keylink">'.$v['title'].'</strong></a>', $data, 1);
			} else {
				$data = preg_replace('\'(?!((<.*?)|(<a.*?)|(<strong.*?)))('.$quote.')(?!(([^<>]*?)>)|([^>]*?</a>)|([^>]*?</strong>))\'si', '<a href="'.moburl($v['url']).'" target="_blank" rel="external">'.$v['title'].'</a>', $data, 1);
			}
			if($data == '') $data = $content;
		}
		return $data;
	}

	public static function lazy($content) {
		return preg_replace("/src=([\"|']?)([^ \"'>]+\.(jpg|jpeg|gif|png|bmp))\\1/i", "src=\"".DT_STATIC."image/lazy.gif\" class=\"lazy\" original=\"\\2\"", $content);
	}

	public static function pagebreak($content) {
		$content = str_replace('"de-pagebreak" /', '"de-pagebreak"/', $content);
		$content = str_replace('[pagebreak]', '<hr class="de-pagebreak"/>', $content);
		return strpos($content, '<hr class="de-pagebreak"/>') === false ? array() : explode('<hr class="de-pagebreak"/>', $content);
	}

	public static function fee($item_fee, $mod_fee) {
		if($item_fee < 0) {
			$fee = 0;
		} else if($item_fee == 0) {
			$fee = $mod_fee;
		} else {
			$fee = $item_fee;
		}
		return $fee;
	}

	public static function description($content, $length) {
		if($length) {
			$content = str_replace(array(' ', '[pagebreak]'), array('', ''), $content);
			return nl2br(dsubstr(trim(strip_tags($content)), $length, '...'));
		} else {
			return '';
		}
	}

	public static function icon($thumb, $content) {
		if(strpos($thumb, '.thumb.') !== false) return substr($thumb, 0, strpos($thumb, '.thumb.'));
		if($thumb) return $thumb;
		if(strpos($content, '<img') !== false) return 'auto';
		return DT_PATH.'apple-touch-icon-precomposed.png';
	}

	public static function player($url, $width = 480, $height = 270, $autoplay = 0, $loop = 0, $extend = '') {
		$url = url2video($url);
		$domain = cutstr($url, '://', '/');
		$ext = file_ext($url);
		if(in_array($domain, array('player.youku.com', 'v.qq.com', 'm.iqiyi.com', 'player.bilibili.com', 'www.bilibili.com', 'www.acfun.cn', 'www.youtube.com'))) {//, 'liveshare.huya.com'
			if(!$autoplay && in_array($domain, array('player.bilibili.com', 'www.bilibili.com'))) $url = $url.'&autoplay=0';
			return '<iframe src="'.$url.'" width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" allowfullscreen="true" allowtransparency="true" allow="'.($autoplay ? 'autoplay; ' : '').'encrypted-media" data-video="frame"></iframe>';
		} else if($ext == 'mp4') {
			return '<video src="'.$url.'" width="'.$width.'" height="'.$height.'"'.($autoplay ? ' autoplay="autoplay" muted="muted"' : '').($loop ? ' loop="loop"' : '').$extend.' controls="controls" data-video="video"></video>';
		} else if($ext == 'mp3') {
			return '<audio src="'.$url.'"'.($autoplay ? ' autoplay="autoplay"' : '').($loop ? ' loop="loop"' : '').$extend.' controls="controls"></audio>';
		} else {
			return '<a href="'.$url.'" target="_blank" rel="external"><div style="width:'.$width.'px;height:'.$height.'px;background:#000000 url('.DT_STATIC.'image/play.png) no-repeat center center;background-size:48px 48px;margin:auto;" data-video="div"></div></a>';
		}
	}

	public static function video($content) {
		$url = '';
		if(strpos($content, 'vcastr3.swf') !== false) {
			$url = cutstr($content, 'source&gt;', '&lt;/');
		} else if(strpos($content, 'src="') !== false) {
			$url = cutstr($content, 'src="', '"');
		}
		if(strlen($url) < 15) return $content;
		$width = intval(cutstr($content, 'width="', '"'));
		$width or $width = 480;
		$height = intval(cutstr($content, 'height="', '"'));
		$height or $height = 270;
		$auto = strpos($content, 'autoplay') === false ? 0 : 1;
		$loop = strpos($content, 'loop') === false ? 0 : 1;
		return self::player($url, $width, $height, $auto, $loop);
	}
}
?>