<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT_BOT) dhttp(403);
$url = isset($url) ? trim($url) : '';
$url = str_replace('/mobile/file/upload/', '/file/upload/', $url);
$name = isset($name) ? trim($name) : '';
strlen($url) > 15 or dheader($DT_PC ? DT_PATH : DT_MOB);
$ext = file_ext($url);
$ext or dheader($DT_PC ? DT_PATH : DT_MOB);
$name or dheader($url);
$ext == file_ext($name) or dheader($DT_PC ? DT_PATH : DT_MOB);
in_array($ext, array('rar', 'zip', 'gz', 'tar', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx')) or dheader($url);
$file = strpos($url, 'file/upload/') !== false ? cutstr($url, 'file/upload/') : str_replace($DT['remote_url'], '', $url);
preg_match("/^[0-9\-\/]{18,}$/",  substr($file, 0, -strlen($ext)-1)) or dheader($url);
$localfile = DT_ROOT.'/file/upload/'.$file;
is_file($localfile) or dheader($url);
$title = substr($name, 0, -strlen($ext)-1);
$title = file_vname($title);
$title or dheader($url);
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) $title = str_replace(' ', '_', $title);
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'rv:1') !== false) $title = convert($title, DT_CHARSET, 'GBK');
$title or dheader($url);
file_down($localfile, $title.'.'.$ext);
?>