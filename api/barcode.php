<?php
$_SERVER['REQUEST_URI'] = '';
$_COOKIE = array();
require '../common.inc.php';
//check_referer() or exit;
if($DT_BOT) dhttp(403);
isset($auth) or $auth = '';
$auth or exit;
$text = decrypt($auth, DT_KEY.'BARCODE');
preg_match("/^[0-9a-zA-Z\-_]{2,}$/", $text) or exit;
$size = isset($size) ? intval($size) : 1;
($size > 0 && $size < 10) or $size = 1;
$fontsize = isset($fontsize) ? intval($fontsize) : 9 + $size;
($fontsize > 8 && $fontsize < 30) or $fontsize = 9 + $size;
// Including all required classes
require_once DT_ROOT.'/api/barcode/BCGFontFile.php';
require_once DT_ROOT.'/api/barcode/BCGColor.php';
require_once DT_ROOT.'/api/barcode/BCGDrawing.php';

// Including the barcode technology
require_once DT_ROOT.'/api/barcode/BCGcode39.barcode.php';

// Loading Font
$font = new BCGFontFile(DT_ROOT.'/api/barcode/Arial.ttf', $fontsize);

// Don't forget to sanitize user inputs

// The arguments are R, G, B for color.
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
    $code = new BCGcode39();
    $code->setScale($size); // Resolution
    $code->setThickness(30); // Thickness
    $code->setForegroundColor($color_black); // Color of bars
    $code->setBackgroundColor($color_white); // Color of spaces
    $code->setFont($font); // Font (or 0)
    $code->parse($text); // Text
} catch(Exception $exception) {
    $drawException = $exception;
}

/* Here is the list of the arguments
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new BCGDrawing('', $color_white);
if($drawException) {
    $drawing->drawException($drawException);
} else {
    $drawing->setBarcode($code);
    $drawing->draw();
}

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');
header('Content-Disposition: inline; filename="barcode.png"');

// Draw (or save) the image into PNG format.
$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
?>