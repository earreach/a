<?php
define('DT_REWRITE', true);

require '../common.inc.php';
// echo "string";  
// die();
$moduleid = 21;
$module = 'article';

// 加载模块配置
isset($MODULE[$moduleid]) or dheader($CFG['url']);
require DT_ROOT.'/module/'.$module.'/quote_view.inc.php';

?>