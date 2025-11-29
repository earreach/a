<?php
defined('IN_DESTOON') or exit('Access Denied');
$_isadmin = defined('DT_ADMIN') ? 1 : 0;
$moddir = $_isadmin ? $MODULE[2]['moduledir'].'/editor/' : 'editor/';
$draft = $textareaid == 'content' && $_userid && $DT['save_draft'];
if($DT['save_draft'] == 2 && !$_isadmin) $draft = false;
$_width = is_numeric($width) ? $width.'px' : $width;
$_height = is_numeric($height) ? $height.'px' : $height;
if($destoon_editor_id == 1) {
	$editor .= '<script type="text/javascript" charset="utf-8" src="'.$moddir.'kindeditor/kindeditor-min.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" charset="utf-8" src="'.$moddir.'kindeditor/lang/zh_CN.js?v='.DT_REFRESH.'"></script>';
}
$editor .= '<script type="text/javascript">';
$editor .= 'var ModuleID = '.$moduleid.';';
$editor .= 'var DTAdmin = '.($_isadmin ? 1 : 0).';';
$editor .= 'var EDPath = "'.$moddir.'kindeditor/";';
$editor .= 'var ABPath = "'.$MODULE[2]['linkurl'].'editor/kindeditor/";';
$editor .= 'var EDW = "'.$_width.'";';
$editor .= 'var EDH = "'.$_height.'";';
$editor .= 'var EDD = "'.($draft ? 1 : 0).'";';
$editor .= 'var EID = "'.$textareaid.'";';
$editor .= '$(\'#'.$textareaid.'\').css({width:\''.$_width.'\',height:\''.$_height.'\',display:\'\'});';
$editor .= 'KindEditor.ready(function(K) { ';
$editor .= 'window.editor = K.create(\'#'.$textareaid.'\', {';
$editor .= 'urlType:\'domain\',';
if($toolbarset == 'Destoon') {
	$editor .= "items : ['source', '|', 'wordpaste', 'plainpaste', '|', 'bold', 'forecolor', 'fontsize', 'link', 'unlink', 'image', 'media', 'hr', 'justifyleft', 'justifycenter', 'justifyright', 'insertfile', ".($MODULE[$moduleid]['module'] == 'club' ? "'emoticons', " : "")."'fullscreen'],";
} else if($toolbarset == 'Simple') {
	$editor .= "items : ['source', '|', 'wordpaste', 'plainpaste', '|', 'bold', 'forecolor', 'fontsize', 'link', 'unlink', 'image', 'media', 'justifyleft', 'justifycenter', 'justifyright', 'insertfile', ".($MODULE[$moduleid]['module'] == 'club' ? "'emoticons', " : "")."'fullscreen'],";
} else if($toolbarset == 'Basic') {
	$editor .= "items : ['source', '|', 'bold', 'forecolor', 'fontsize', 'link', 'unlink', 'image', 'media', 'justifyleft', 'justifycenter', 'justifyright', ".($MODULE[$moduleid]['module'] == 'club' ? "'emoticons', " : "")."'fullscreen'],";
} else if($toolbarset == 'Message') {
	$editor .= "items : ['source', '|', 'wordpaste', 'plainpaste', '|', 'bold', 'forecolor', 'fontsize', 'link', 'unlink', 'image', 'media', 'emoticons', 'justifyleft', 'justifycenter', 'justifyright', 'insertfile', ".($MODULE[$moduleid]['module'] == 'club' ? "'emoticons', " : "")."'fullscreen'],";
} else {
	$editor .= "items : ['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'cut', 'copy', 'paste', 'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',	'justifyfull', 'insertorderedlist', 'insertunorderedlist', '|', 'removeformat', 'clearhtml', 'quickformat', '|', 'fullscreen', '/', 'link', 'unlink', 'anchor','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',	'italic', 'underline', 'strikethrough', 'lineheight', 'table', 'hr', 'emoticons', '|', 'image', 'media', 'insertfile'],";
}
if(!$_isadmin && !$DT['editor_html']) $editor = str_replace("'source', '|', ", '', $editor);
$editor .= 'uploadJson:UPPath+\'?action=kindeditor&from=editor&moduleid='.$moduleid.'\'';
$editor .= '}); });';
$editor .= '</script>';
if($destoon_editor_id == 1) {
	$editor .= '<script type="text/javascript" src="'.$moddir.'kindeditor/init.api.js?v='.DT_REFRESH.'"></script>';
	$editor .= '<script type="text/javascript" src="'.DT_STATIC.'script/editor.js?v='.DT_REFRESH.'"></script>';
}
?>