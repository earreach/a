<?php
defined('DT_ADMIN') or exit('Access Denied');
if($job == 'install' || $job == 'upgrade') {
	cache_all();
	tohtml('index');
}
$update = DT_CACHE.'/update-'.DT_RELEASE.'.php';
if(is_file($update)) {
	foreach(glob(DT_CACHE.'/update-*.php') as $v) {
		include $v;
		file_del($v);
	}
}
$width = $_admin == 2 ? $DT['admin_left'] - 48 : $DT['admin_left'];
?>
<!doctype html>
<html lang="<?php echo DT_LANG;?>">
<head>
<meta charset="<?php echo DT_CHARSET;?>"/>
<title>管理中心 - <?php echo $DT['sitename']; ?> - Powered By DESTOON V<?php echo DT_VERSION; ?> R<?php echo DT_RELEASE;?></title>
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width"/>
<meta name="robots" content="noindex,nofollow"/>
<meta name="generator" content="DESTOON - www.destoon.com"/>
<meta http-equiv="x-ua-compatible" content="IE=8"/>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo DT_PATH;?>favicon.ico"/>
<link rel="bookmark" type="image/x-icon" href="<?php echo DT_PATH;?>favicon.ico"/>
<style type="text/css">
html{height:100%;}
body {background:#2E2E2E url('<?php echo DT_STATIC;?>admin/side.png') no-repeat center center;cursor:pointer;margin:0;height:100%;}
</style>
</head>
<body onclick="top.document.getElementById('destoon-panel').cols='<?php echo $width;?>,0,*';" title="显示侧栏">
<?php 
if($_admin == 1) {
	if(!is_file(DT_ROOT.'/file/md5/'.DT_VERSION.'.php')) echo '<script type="text/javascript" src="?file=md5&action=add&js=1"></script>';
	if(DT_TIME - filemtime(DT_CACHE.'/doctor.php') > 864000) echo '<script type="text/javascript" src="?file=doctor&js=1"></script>';
} 
?>
<script type="text/javascript" src="?action=cron"></script>
</body>
</html>