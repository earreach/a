<?php
defined('IN_DESTOON') or exit('Access Denied');
include DT_ROOT.'/api/map/baidu/config.inc.php';
$_isadmin = defined('DT_ADMIN') ? 1 : 0;
$moddir = $_isadmin ? $MODULE[2]['moduledir'].'/editor/' : 'editor/';
$draft = $textareaid == 'content' && $_userid && $DT['save_draft'];
if($DT['save_draft'] == 2 && !$_isadmin) $draft = false;
$_width = is_numeric($width) ? $width.'px' : $width;
$_height = is_numeric($height) ? $height.'px' : $height;
if($destoon_editor_id == 1) {
	$editor .= '<script type="text/javascript" src="'.$moddir.'ueditor/ueditor.config.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.$moddir.'ueditor/ueditor.all.min.js?v='.DT_REFRESH.'"></script>';
}
$editor .= '<script type="text/javascript">';
$editor .= 'var ModuleID = '.$moduleid.';';
$editor .= 'var EDW = "'.$_width.'";';
$editor .= 'var EDH = "'.$_height.'";';
$editor .= 'var EDD = "'.($draft ? 1 : 0).'";';
$editor .= 'var EID = "'.$textareaid.'";';
$editor .= 'var MAK = "'.$map_key.'";';
$editor .= '$(\'#'.$textareaid.'\').css({width:\''.$_width.'\',display:\'block\'});';//$(function(){}); ,height:\''.$_height.'\'
$editor .= 'var ue = UE.getEditor(\''.$textareaid.'\',{';
$editor .= "initialFrameWidth:$('#".$textareaid."').css('width').replace('px', ''),";
$editor .= 'initialFrameHeight:'.$height.',';
$editor .= 'autoHeightEnabled: true,';
$editor .= 'wordCount:false,';
$editor .= 'elementPathEnabled:false,';
$editor .= 'autoFloatEnabled: true,';
$editor .= 'saveInterval: 5000,';
$editor .= 'serverUrl:UPPath+"?from=editor&moduleid='.$moduleid.'",';
$editor .= 'toolbars: [';
if($toolbarset == 'Destoon') {
	$editor .= "['source','|', 'bold','italic','underline','forecolor','fontsize','|','justifyleft','justifyright','justifycenter','justifyjustify','|','pasteplain','drafts','removeformat','|','link','unlink','simpleupload','insertimage','insertvideo','attachment','fullscreen']";
} elseif($toolbarset == 'Simple') {
	$editor .= "['source','|','bold','italic','underline','forecolor','fontsize','|','justifyleft','justifyright','justifycenter','|','drafts','removeformat','|','link','unlink','simpleupload','insertvideo','attachment','fullscreen']";
} elseif($toolbarset == 'Basic') {
	$editor .= "['source','|', 'bold','forecolor','|','justifyleft','justifyright','justifycenter','|','drafts','removeformat','|','link','unlink','simpleupload','fullscreen']";
} elseif($toolbarset == 'Message') {
	$editor .= "['source','|','bold','italic','underline','forecolor','fontsize','|','justifyleft', 'justifycenter', 'justifyright','|','removeformat','|','link', 'unlink','emotion','simpleupload','insertvideo','attachment','fullscreen']";
} else {
	$editor .= "['source','|','undo', 'redo', '|','preview','print','formatmatch','pasteplain','drafts','removeformat','|','inserttable','insertrow', 'insertcol', '|','justifyleft','justifyright','justifycenter','justifyjustify','|','link','unlink','anchor'],['fontfamily','fontsize','|','forecolor','backcolor','bold','italic','underline','strikethrough','|','emotion', 'map','|','simpleupload','insertimage','insertvideo','attachment','fullscreen']";
}
if(!$_isadmin && !$DT['editor_html']) $editor = str_replace("'source','|',", '', $editor);
$editor .= '],';
$editor .= '});';
$editor .= '</script>';
if($destoon_editor_id == 1) {
	$editor .= '<script type="text/javascript" src="'.$moddir.'ueditor/init.api.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.DT_STATIC.'script/editor.js?v='.DT_REFRESH.'"></script>';
}
?>