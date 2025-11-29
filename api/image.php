<?php
require '../common.inc.php';
header("content-type:image/png");
if(isset($auth) && $auth && !$DT_BOT && check_referer()) {
	$str = decrypt($auth, DT_KEY.'SPAM');
	if(preg_match("/^[a-z0-9_@\-\s\/\.\,\(\)\+]{5,}$/i", $str)) {
		$imageX = strlen($str)*7.2;
		$imageY = 20;
		$im = @imagecreate($imageX, $imageY) or exit();
		imagecolorallocate($im, 255, 255, 255);
		$color = imagecolorallocate($im, 68, 68, 68);
		imagestring($im, 3, 0, 5, $str, $color);
		imagepng($im);
		imagedestroy($im);
		exit;
	}
}
dheader(DT_STATIC.'image/spacer.png');
?>