<?php 
defined('DT_ADMIN') or exit('Access Denied');
?>
<!doctype html>
<html lang="<?php echo DT_LANG;?>">
<head>
<meta charset="<?php echo DT_CHARSET;?>"/>
<title>提示信息</title>
<link rel="stylesheet" href="<?php echo DT_STATIC;?>admin/style.css?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>" type="text/css" />
<script type="text/javascript" src="<?php echo DT_PATH;?>lang/<?php echo DT_LANG;?>/lang.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<?php if(strpos($DT_MBS, 'IE') === false) { ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/jquery-3.6.4.min.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<?php } else { ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/jquery-1.12.4.min.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/common.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/panel.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
</head>
</body>
<div id="box" style="padding:16px 16px 0 16px;line-height:2.0;">
<?php echo $dcontent; ?>
</div>
<script type="text/javascript">
try{parent.Dd('dload').style.display='none';parent.Dd('diframe').style.height = Dd('box').scrollHeight+'px';} catch(e){}
</script>
</body>
</html>