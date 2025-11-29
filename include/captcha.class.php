<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class captcha {
	var $chars = 'abcdeghkmnpqstwxy234789ABCEFGHJKLMNPRSTWXY';
	var $length = 4;
	var $soundtag;
	var $soundstr;
	var $cn;
	var $font;

	function question($id) {
		$r = DB::get_one("SELECT * FROM ".DT_PRE."question ORDER BY rand()");
		$_SESSION['answerstr'] = encrypt($r['answer'], DT_KEY.'ANS');
		exit('document.getElementById("'.$id.'").innerHTML = "'.$r['question'].'";');
	}

	function image() {
		if(strpos(DT_UA, 'MSIE') !== false) {
			header('Cache-Control:must-revalidate, post-check=0, pre-check=0');
			header('Pragma:public');
		} else {
			header('Pragma:no-cache');
		}
		header('Expires:'.gmdate('D, d M Y H:i:s').' GMT');
		header("Content-type:image/png");	
		$string = $this->mk_str();
		$_SESSION['captchastr'] = encrypt(strtoupper($string), DT_KEY.'CPC');
		$imageX = $this->length*28;
		$imageY = 32;
		$im = imagecreatetruecolor($imageX, $imageY);  
		imagefill($im, 0, 0, imagecolorallocate($im, 250, 250, 250));
		if($this->cn) {
			$angle = mt_rand(-15, 15);
			$size = mt_rand(12, 22);
			$font = $this->font;
			$X = $size + mt_rand(5, 10);
			$Y = $size + mt_rand(5, 10);
			imagettftext($im, $size, $angle, $X, $Y, $this->mk_rgb($im), $font, $string);
			$this->mk_sin($im, $color);
			imagepng($im);
			imagedestroy($im);
		} else {
			$fonts = glob(DT_ROOT.'/file/captcha/*.ttf');
			$BG = imagecolorallocate($im, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagefill($im, 0, 0, $BG);
			$X = 0;
			for($i = 0; $i < $this->length; $i++) {
				$size = mt_rand(16, 24);
				$angle = mt_rand(-10, 10);
				if($i > 0) $X += $size + mt_rand(-2, 6);
				$Y = $size + mt_rand(0, 4);
				imagettftext($im, $size, $angle, $X, $Y, $this->mk_rgb($im), $fonts[array_rand($fonts)], $string[$i]);
			}
			$this->mk_sin($im, $this->mk_rgb($im));
			imagepng($im);
		}
		exit;
	}

	function mk_rgb($im) {
		return imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
	}

	function mk_sin($im, $color) {
		$R = mt_rand(5, 20);
		$X = mt_rand(15, 25);
		$Y = mt_rand(5, 10);
		$L = mt_rand(50, 80);
		for($yy = $R; $yy <= $R + 1; $yy++) {
			for($px = -$L; $px <= $L; $px = $px + 0.1) {
				$x = $px/$X;
				if($x != 0) $y = sin($x);
				$py = $y*$Y;
				imagesetpixel($im, $px + $L, $py + $yy, $color);
			}
		}
	}

	function mk_str() {
		$str = '';
		if($this->cn) {
			$step = DT_CHARSET == 'UTF-8' ? 3 : 2;
			$text = substr(file_get(DT_ROOT.'/file/config/cncaptcha.inc.php'), 13);
			$max = strlen($text) - 1 - $step;
			$j = 0;
			while($j++ < 10) {
				$i = mt_rand(0, $max);
				if($i%$step == 0) {
					$str .= substr($text, $i, $step);
					break;
				}
			}
			$j = 0;
			while($j++ < 10) {
				$i = mt_rand(0, $max);
				if($i%$step == 0) {
					$str .= substr($text, $i, $step);
					break;
				}
			}
		} else {
			$max = strlen($this->chars) - 1;
			$j = 0;
			while($j++ < 10) {
				if(strlen($str) == $this->length) break;
				$r = mt_rand(0, $max);
				if(strpos(strtolower($str), strtolower($this->chars[$r])) === false) $str .= $this->chars[$r];
			}
		}
		return $str;
	}
}
?>