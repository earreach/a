<?php
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('添加会员', '?moduleid='.$moduleid.'&action=add'),
    array('会员列表', '?moduleid='.$moduleid),
    array('审核会员', '?moduleid='.$moduleid.'&action=check'),
    array('会员副表', '?moduleid='.$moduleid.'&action=misc'),
    array('联系会员', '?moduleid='.$moduleid.'&file=contact'),
);
$sfields = array('按条件', '公司名', '会员名', '昵称', '个性签名','姓名', '部门', '职位', '手机号码','电话号码','传真号码', '详细地址', '邮政编码', '公司类型', '公司规模', '销售', '采购', '主营行业', '经营模式', 'Email', 'QQ', '微信', '阿里旺旺', 'Skype', '注册IP', '登录IP', '客服专员', '邀请人');
$dfields = array('m.username', 'm.company', 'm.username', 'm.passport', 'm.sign', 'm.truename', 'm.department', 'm.career', 'm.mobile', 'c.telephone', 'c.fax', 'c.address', 'c.postcode', 'c.type', 'c.size', 'c.sell', 'c.buy', 'c.business', 'c.mode', 'm.email', 'm.qq', 'm.wx', 'm.ali', 'm.skype', 'm.regip', 'm.loginip', 'm.support', 'm.inviter');
$sorder  = array('结果排序方式', '注册时间降序', '注册时间升序', '登录时间降序', '登录时间升序', '登录次数降序', '登录次数升序', '账户'.$DT['money_name'].'降序', '账户'.$DT['money_name'].'升序', '会员'.$DT['credit_name'].'降序', '会员'.$DT['credit_name'].'升序', '短信余额降序', '短信余额升序', VIP.'指数降序', VIP.'指数升序', '注册年份降序', '注册年份升序', '注册资本降序', '注册资本升序', '服务开始降序', '服务开始升序', '服务结束降序', '服务结束升序','浏览人气降序','浏览人气升序');
$dorder  = array('m.userid DESC', 'm.regtime DESC', 'm.regtime ASC', 'm.logintime DESC', 'm.logintime ASC', 'm.logintimes DESC', 'm.logintimes ASC', 'm.money DESC', 'm.money ASC', 'm.credit DESC', 'm.credit ASC', 'm.sms DESC', 'm.sms ASC', 'c.vip DESC', 'c.vip ASC', 'c.regyear DESC', 'c.regyear ASC', 'c.capital DESC', 'c.capital ASC', 'c.fromtime DESC', 'c.fromtime ASC', 'c.totime DESC', 'c.totime ASC', 'c.hits DESC', 'c.hits ASC');
$sgender = array('性别', '先生' , '女士');
$savatar = array('头像', '已上传' , '未上传');
$sprofile = array('资料', '已完善' , '未完善');
$semail = array('邮件', '已认证' , '未认证');
$smobile = array('手机', '已认证' , '未认证');
$struename = array('实名', '已认证' , '未认证');
$sbank = array('银行', '已认证' , '未认证');
$scompany = array('公司', '已认证' , '未认证');
$sshop = array('商铺', '已认证' , '未认证');
$senterprise = array('个人', '机构');
$svalidate = array('未认证', '个人认证', '机构认证');
$modes = explode('|', '经营模式|'.$MOD['com_mode']);
$types = explode('|', '公司类型|'.$MOD['com_type']);
$sizes = explode('|', '公司规模|'.$MOD['com_size']);


isset($fields) && isset($dfields[$fields]) or $fields = 0;
isset($order) && isset($dorder[$order]) or $order = 0;
(isset($fromdate) && is_time($fromdate)) or $fromdate = '';
$fromtime = $fromdate ? datetotime($fromdate) : 0;
(isset($todate) && is_time($todate)) or $todate = '';
$totime = $todate ? datetotime($todate) : 0;
isset($timetype) or $timetype = 'm.regtime';
(isset($mixt) && in_array($mixt, array('m.money', 'm.cardit', 'm.sms', 'm.deposit','m.fans','m.follows','m.moments','m.logtimes', 'c.regyear', 'c.capital', 'c.hits', 'c.comments'))) or $mixt = 'm.money';
$minv = isset($minv) ? intval($minv) : '';
$maxv = isset($maxv) ? intval($maxv) : '';
$minv or $minv = '';
$maxv or $maxv = '';

$thumb = isset($thumb) ? intval($thumb) : 0;
$mincapital = isset($mincapital) ? dround($mincapital) : '';
$mincapital or $mincapital = '';
$maxcapital = isset($maxcapital) ? dround($maxcapital) : '';
$maxcapital or $maxcapital = '';
$areaid = isset($areaid) ? intval($areaid) : 0;
isset($mode) && isset($modes[$mode]) or $mode = 0;
isset($type) && isset($types[$type]) or $type = 0;
isset($size) && isset($sizes[$size]) or $size = 0;
$vip = isset($vip) ? ($vip === '' ? -1 : intval($vip)) : -1;
$valid = isset($valid) ? intval($valid) : 0;
$groupid = isset($groupid) ? intval($groupid) : 0;
$gradeid = isset($gradeid) ? intval($gradeid) : 0;
$gender = isset($gender) ? intval($gender) : 0;
$avatar = isset($avatar) ? intval($avatar) : 0;
$export = isset($export) ? intval($export) : 0;
$uid = isset($uid) ? intval($uid) : '';
$vprofile = isset($vprofile) ? intval($vprofile) : 0;
$vemail = isset($vemail) ? intval($vemail) : 0;
$vmobile = isset($vmobile) ? intval($vmobile) : 0;
$vtruename = isset($vtruename) ? intval($vtruename) : 0;
$vbank = isset($vbank) ? intval($vbank) : 0;
$vcompany = isset($vcompany) ? intval($vcompany) : 0;
$vshop = isset($vshop) ? intval($vshop) : 0;
$validate = isset($validate) ? intval($validate) : -1;
$enterprise = isset($enterprise) ? intval($enterprise) : -1;
$uid = isset($uid) ? intval($uid) : '';
$uid or $uid = '';
$passport = isset($passport) ? trim($passport) : '';
(isset($username) && check_name($username)) or $username = '';
(isset($mobile) && is_mobile($mobile)) or $mobile = '';
$minmoney = isset($minmoney) ? intval($minmoney) : '';
$maxmoney = isset($maxmoney) ? intval($maxmoney) : '';
$mincredit = isset($mincredit) ? intval($mincredit) : '';
$maxcredit = isset($maxcredit) ? intval($maxcredit) : '';
$minsms = isset($minsms) ? intval($minsms) : '';
$maxsms = isset($maxsms) ? intval($maxsms) : '';

$fields_select = dselect($sfields, 'fields', '', $fields);
$order_select  = dselect($sorder, 'order', '', $order);
$gender_select = dselect($sgender, 'gender', '', $gender);
$avatar_select = dselect($savatar, 'avatar', '', $avatar);
$group_select = group_select('groupid', '会员组', $groupid);
$grade_select = grade_select('gradeid', '积分组', $gradeid);
$vprofile_select = dselect($sprofile, 'vprofile', '', $vprofile);
$vemail_select = dselect($semail, 'vemail', '', $vemail);
$vmobile_select = dselect($smobile, 'vmobile', '', $vmobile);
$vtruename_select = dselect($struename, 'vtruename', '', $vtruename);
$vbank_select = dselect($sbank, 'vbank', '', $vbank);
$vcompany_select = dselect($scompany, 'vcompany', '', $vcompany);
$vshop_select = dselect($sshop, 'vshop', '', $vshop);
$mode_select = dselect($modes, 'mode', '', $mode);
$type_select = dselect($types, 'type', '', $type);
$size_select = dselect($sizes, 'size', '', $size);
$validate_select = dselect($svalidate, 'validate', '认证', $validate, '', 1, '-1');
$enterprise_select = dselect($senterprise, 'enterprise', '类型', $enterprise, '', 1, '-1');

$condition = 'm.userid=c.userid';//
if($keyword) $condition .= match_kw($dfields[$fields], $keyword);
if($gender) $condition .= " AND m.gender=$gender";
if($avatar) $condition .= $avatar == 1 ? " AND m.avatar=1" : " AND m.avatar=0";
if($groupid) $condition .= " AND m.groupid=$groupid";
if($gradeid) $condition .= " AND m.gradeid=$gradeid";
if($uid) $condition .= " AND m.userid=$uid";
if($username) $condition .= " AND m.username='$username'";
if($vprofile) $condition .= $vprofile == 1 ? " AND m.edittime>0" : " AND m.edittime=0";
if($vemail) $condition .= $vemail == 1 ? " AND m.vemail>0" : " AND m.vemail=0";
if($vmobile) $condition .= $vmobile == 1 ? " AND m.vmobile>0" : " AND m.vmobile=0";
if($vtruename) $condition .= $vtruename == 1 ? " AND m.vtruename>0" : " AND m.vtruename=0";
if($vbank) $condition .= $vbank == 1 ? " AND m.vbank>0" : " AND m.vbank=0";
if($vcompany) $condition .= $vcompany == 1 ? " AND m.vcompany>0" : " AND m.vcompany=0";
if($vshop) $condition .= $vshop == 1 ? " AND m.vshop>0" : " AND m.vshop=0";
if($validate > -1) $condition .= " AND m.validate=$validate";
if($enterprise > -1) $condition .= " AND m.enterprise=$enterprise";
if($fromtime) $condition .= " AND $timetype>=$fromtime";
if($totime) $condition .= " AND $timetype<=$totime";
if($minmoney) $condition .= " AND m.money>=$minmoney";
if($maxmoney) $condition .= " AND m.money<=$maxmoney";
if($mincredit) $condition .= " AND m.credit>=$mincredit";
if($maxcredit) $condition .= " AND m.credit<=$maxcredit";
if($minsms) $condition .= " AND m.sms>=$minsms";
if($maxsms) $condition .= " AND m.sms<=$maxsms";
if($catid) $condition .= " AND c.catids LIKE '%,".$catid.",%'";
if($areaid) $condition .= ($ARE['child']) ? " AND c.areaid IN (".$ARE['arrchildid'].")" : " AND c.areaid=$areaid";
if($mode) $condition .= " AND c.mode LIKE '%$modes[$mode]%'";
if($type) $condition .= " AND c.type='$types[$type]'";
if($size) $condition .= " AND c.size='$sizes[$size]'";
if($thumb) $condition .= " AND c.thumb<>''";
if($mincapital) $condition .= " AND c.capital>$mincapital";
if($maxcapital) $condition .= " AND c.capital<$maxcapital";
if($minv) $condition .= " AND $mixt>=$minv";
if($maxv) $condition .= " AND $mixt<=$maxv";
if($export) {
	$data = '会员ID,会员名,昵称,会员组,积分组,公司名,店铺名,姓名,职位,性别,电话,手机,详细地址,邮编,电子邮件';
	if($DT['im_qq']) $data .= ',QQ';
	if($DT['im_wx']) $data .= ',微信';
	if($DT['im_ali']) $data .= ',阿里旺旺';
	if($DT['im_skype']) $data .= ',Skype';
	$data .= "\n";
	$result = $db->query("SELECT * FROM {$DT_PRE}member m,{$DT_PRE}company c WHERE {$condition} ORDER BY {$dorder[$order]}");
	while($r = $db->fetch_array($result)) {
		$data .= $r['userid'].',';
		$data .= $r['username'].',';
		$data .= $r['passport'].',';
		$data .= $GROUP[$r['groupid']]['groupname'].',';
		$data .= $GRADE[$r['gradeid']]['name'].',';
		$data .= $r['company'].',';
		$data .= $r['shop'].',';
		$data .= $r['truename'].',';
		$data .= $r['career'].',';
		$data .= gender($r['gender']).',';
		$data .= $r['telephone'].',';
		$data .= $r['mobile'].',';
		$data .= $r['address'].',';
		$data .= $r['postcode'].',';
		$data .= $r['email'].',';
		if($DT['im_qq']) $data .= $r['qq'].',';
		if($DT['im_wx']) $data .= $r['wx'].',';
		if($DT['im_ali']) $data .= $r['ali'].',';
		if($DT['im_skype']) $data .= $r['skype'].',';
		$data .= "\n";
	}
	$data = convert($data, DT_CHARSET, 'GBK');
	file_down('', 'contact.csv', $data);
}
if($page > 1 && $sum) {
	$items = $sum;
} else {
	$r = $db->get_one("SELECT COUNT(*) AS num FROM {$DT_PRE}member m,{$DT_PRE}company c WHERE {$condition}");
	$items = $r['num'];
}
$pages = pages($items, $page, $pagesize);
$lists = array();
$result = $db->query("SELECT * FROM {$DT_PRE}member m,{$DT_PRE}company c WHERE {$condition} ORDER BY {$dorder[$order]} LIMIT {$offset},{$pagesize}");
while($r = $db->fetch_array($result)) {
	$r['logindate'] = timetodate($r['logintime'], 5);
	$r['regdate'] = timetodate($r['regtime'], 5);
	$lists[] = $r;
}
include tpl('member_contact', $module);
?>