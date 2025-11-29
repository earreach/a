<?php
define('DT_ADMIN', true);
require '../../common.inc.php';
$auth = isset($auth) ? decrypt($auth, DT_KEY.'WXTS') : '';
substr($auth, 0, 1) == '{' or exit('E001');
$arr = json_decode($auth, true);
($arr && is_array($arr)) or exit('E002');
$username = $arr['username'];
check_name($username) or exit('E003');
$user = $db->get_one("SELECT openid,push FROM {$DT_PRE}weixin_user WHERE username='$username'");
$user or exit('E004');
$user['push'] or exit('E005');
$par = array();
$openid = $user['openid'];
$par['touser'] = $openid;
$par['template_id'] = $arr['template_id'];
$par['url'] = $arr['url'];
$par['topcolor'] = $arr['topcolor'];
$par['data'] = $arr['data'];
//log_write($par, 'wxp', 1);
require DT_ROOT.'/include/post.func.php';
require DT_ROOT.'/api/weixin/init.inc.php';
$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$wx->access_token;
$arr = $wx->http_post($url, urldecode(json_encode($par)));
//log_write($arr, 'wxa', 1);
$post = array();
$post['content'] = '';
foreach($par['data'] as $k=>$v) {
	$post['content'] .= $k.':'.$v['value']."\n";
}
$post['content'] = trim($post['content']);
$post['type'] = $arr['errcode'] == 0 ? 'template' : 'untemplate';
$post['openid'] = $openid;
$post['editor'] = 'system';
$post['addtime'] = $DT_TIME;
$post['misc'] = $arr['errcode'];
$post = daddslashes($post);
$db->query("INSERT INTO {$DT_PRE}weixin_chat ".arr2sql($post, 0));
exit($arr['errcode'] == 0 ? 'OK' : 'KO');
?>