<?php
require '../../../common.inc.php';
set_cookie('bind', '');
dheader(DT_MOB.'api/weixin.php?action=connect&url='.urlencode(get_cookie('forward_url')));
?>