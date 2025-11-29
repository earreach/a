<?php
defined('DT_ADMIN') or exit('Access Denied');
tohtml('sitemaps', $module);
msg('SiteMaps 更新成功', '?moduleid='.$moduleid.'&file=setting#sitemaps');
?>