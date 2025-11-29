<?php
require '../common.inc.php';
require DT_ROOT.'/include/post.func.php';
(isset($file) && check_name($file)) or $file = 'demo';
@include DT_ROOT.'/api/json/'.$file.'.inc.php';
?>