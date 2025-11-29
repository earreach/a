<?php 
defined('IN_DESTOON') or exit('Access Denied');
require DT_ROOT.'/include/module.func.php';
require DT_ROOT.'/module/'.$module.'/global.func.php';
$table = $DT_PRE.$module.'_'.$moduleid;
$table_data = $DT_PRE.$module.'_data_'.$moduleid;
$table_apply = $DT_PRE.$module.'_apply_'.$moduleid;
$table_talent = $DT_PRE.$module.'_talent_'.$moduleid;
$table_resume = $DT_PRE.'resume_'.$MOD['rid'];
$CATEGORY = cache_read('category-'.$moduleid.'.php');
$AREA = cache_read('area.php');
$TYPE = explode('|', trim($MOD['type']));
$GENDER = explode('|', trim($MOD['gender']));
$MARRIAGE = explode('|', trim($MOD['marriage']));
$EDUCATION = explode('|', trim($MOD['education']));
?>