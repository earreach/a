<?php

defined('IN_DESTOON') or exit('Access Denied');

echo 000000000000;
// die();
$shuxing_title = isset($shuxing_title) ? strip_tags($shuxing_title) : '';
$shuxing_extend = isset($shuxing_extend) ? decrypt($shuxing_extend, DT_KEY.'ARE') : '';
$shuxingid = isset($shuxingid) ? intval($shuxingid) : 0;
$shuxing_deep = isset($shuxing_deep) ? intval($shuxing_deep) : 0;
$shuxing_id= isset($shuxing_id) ? intval($shuxing_id) : 1;
$shuxing_moduleid= isset($shuxing_moduleid) ? intval($shuxing_moduleid) : 1;


echo get_shuxing_select($shuxing_title, $shuxingid,$shuxing_moduleid,$shuxing_extend, $shuxing_deep, $shuxing_id);
?>