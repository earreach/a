<?php
defined('IN_DESTOON') or exit('Access Denied');
include DT_ROOT.'/api/map/baidu/config.inc.php';
$_isadmin = defined('DT_ADMIN') ? 1 : 0;
$moddir = $_isadmin ? $MODULE[2]['moduledir'].'/editor/' : 'editor/';
$draft = $textareaid == 'content' && $_userid && $DT['save_draft'];
if($DT['save_draft'] == 2 && !$_isadmin) $draft = false;
$_width = is_numeric($width) ? $width.'px' : $width;
$_height = is_numeric($height) ? $height.'px' : $height;
if($_width == '100%') $_width = '99%';
if($destoon_editor_id == 1) {
	$editor .= '<link rel="stylesheet" type="text/css" href="'.$moddir.'umeditor/themes/default/css/umeditor.css?v='.DT_REFRESH.'"/>';
	$editor .= '<style tyle="text/css">.edui-body-container a {color:'.($DT['home_link'] ? $DT['home_link'] : '#024893;').'}</style>';
	$editor .= '<script type="text/javascript" src="'.$moddir.'umeditor/third-party/template.min.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.$moddir.'umeditor/umeditor.config.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.$moddir.'umeditor/umeditor.min.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.$moddir.'umeditor/lang/zh-cn/zh-cn.js?v='.DT_REFRESH.'"></script>';
}
$editor .= '<script type="text/javascript">';
$editor .= 'var ModuleID = '.$moduleid.';';
$editor .= 'var EDW = "'.$_width.'";';
$editor .= 'var EDH = "'.$_height.'";';
$editor .= 'var EDD = "'.($draft ? 1 : 0).'";';
$editor .= 'var EID = "'.$textareaid.'";';
$editor .= 'var MAK = "'.$map_key.'";';
$editor .= '$(function(){$(\'.edui-container,.edui-body-container\').css({width:\''.$_width.'\'});$(\'.edui-body-container\').show();});';
$editor .= 'var um = UM.getEditor(\''.$textareaid.'\',';
$editor .= '$opt={';
$editor .= 'autoFloatEnabled:false,';
$editor .= 'initialFrameWidth:"'.$_width.'",';
$editor .= 'imageUrl:UPPath+"?from=editor&moduleid='.$moduleid.'",';
$editor .= 'toolbar:';
if($toolbarset == 'Destoon') {
	$editor .= "['source | bold italic underline strikethrough | forecolor backcolor | paragraph fontfamily fontsize | justifyleft justifycenter justifyright | link unlink | image video | drafts removeformat fullscreen']";
} elseif($toolbarset == 'Simple') {
	$editor .= $editor .= "['source | bold italic underline strikethrough | forecolor | fontfamily fontsize | justifyleft justifycenter justifyright | link unlink | image video | drafts removeformat fullscreen']";
} elseif($toolbarset == 'Basic') {
	$editor .= $editor .= "['source | bold italic | forecolor | justifyleft justifycenter justifyright | link unlink | image video | drafts removeformat fullscreen']";
} elseif($toolbarset == 'Message') {
	$editor .= "['source | bold italic | forecolor | justifyleft justifycenter justifyright | link unlink | emotion image video | removeformat fullscreen']";
} else {
	$editor .= "['source | undo redo | bold italic underline strikethrough | superscript subscript | forecolor backcolor | insertorderedlist insertunorderedlist paragraph | fontfamily fontsize | justifyleft justifycenter justifyright justifyjustify | link unlink | emotion image video map horizontal formula | selectall cleardoc print preview drafts removeformat fullscreen']";
}
if(!$_isadmin && !$DT['editor_html']) $editor = str_replace("source | ", '', $editor);
$editor .="}";
$editor .= ');';
$editor .= '</script>';
if($destoon_editor_id == 1) {
	$editor .= '<script type="text/javascript" src="'.$moddir.'umeditor/init.api.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.DT_STATIC.'script/editor.js?v='.DT_REFRESH.'"></script>';
}
?>