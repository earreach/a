<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!isset($dialog)) show_menu($menus);
load('member.css');
?>
<style>.userinfo-v0,.userinfo-v1,.userinfo-v2 {margin:-48px 0 0 120px;}</style>
<div class="tt" ondblclick="window.location.reload();">会员资料<?php if(isset($dialog)) {?><img src="<?php echo DT_STATIC;?>admin/tool-full.png" width="16" height="16" title="全屏" onclick="window.open('?moduleid=<?php echo $moduleid;?>&action=show&username=<?php echo $username;?>');" alt="" class="f_r c_p" style="padding:16px 0 0 0;"/><?php } ?></div>
<table cellspacing="0" class="tb">
<tr>
<td rowspan="<?php echo $totime ? 10 : 9;?>" align="center" width="156" class="f_gray" valign="top">
<img src="<?php echo useravatar($username, 'large');?>" width="128" height="128" style="margin:16px 0;" class="c_p avatar" onclick="_preview(this.src);"/><i class="userinfo-v<?php echo $validate;?>" title="<?php echo $svalidate[$validate];?>"></i>
<div><?php $ol = online($userid);if($ol == 1) { ?><span class="f_green">● 在线</span><?php } else if($ol == -1) { ?><span class="f_orange">● 隐身</span><?php } else { ?><span class="f_gray">● 离线</span><?php } ?></div>
<div style="padding:16px 0 0 0;">
<?php if($DT['im_web']) { ?><?php echo im_web($username);?> <?php } ?> &nbsp; 
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=message&action=send&touser=<?php echo $username;?>', '发送消息');"><img width="16" height="16" src="static/image/msg.gif" title="发送消息" align="absmiddle"/></a> &nbsp; 
<?php if($mobile) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $mobile;?>', '发送短信');"><img src="static/image/mobile.gif" title="发送短信" align="absmiddle"/></a> &nbsp; <?php } ?>
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $email;?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送邮件" align="absmiddle"/></a>  &nbsp; 
</div>
<div style="padding:16px 0 0 0;">
<?php echo im_wx($wx, $username);?> &nbsp; 
<?php echo im_gzh($gzh, $username);?> &nbsp; 
<?php echo im_qq($qq);?> &nbsp; 
<?php echo im_ali($ali);?> &nbsp; 
<?php echo im_skype($skype);?> &nbsp; 
</div>
</td>
<td class="tl">会员名</td>
<td id="top-side">&nbsp;<?php echo $username;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo DT_STATIC;?>image/ico-edit.png" width="16" height="16" title="修改会员名" align="absmiddle" class="c_p" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&action=edit_username&userid=<?php echo $userid;?>', '修改会员名', 600, 400);"/><span class="f_r"><a href="<?php echo $linkurl;?>" class="t" target="_blank">会员主页</a> &nbsp; | &nbsp; <a href="<?php echo userurl($username, 'file=space', $domain);?>" class="t" target="_blank">个人空间</a></span></td>
<td class="tl">会员ID</td>
<td>&nbsp;<?php echo $userid;?><span class="f_r"><a href="?moduleid=<?php echo $moduleid;?>&action=avatar&userid=<?php echo $userid;?>" class="t" onclick="return confirm('确定要删除此会员的头像吗？，此操作将不可撤销');">删除头像</a> &nbsp; | &nbsp; <a href="?moduleid=<?php echo $moduleid;?>&action=delete&userid=<?php echo $userid;?>&forward=<?php echo urlencode('?moduleid='.$moduleid);?>" class="t"<?php if(isset($dialog)) {?> target="_blank"<?php } ?> onclick="return confirm('确定要删除此会员吗？系统将删除选中用户所有信息，此操作将不可撤销\n如果网站数据较多，删除可能比较缓慢，建议先禁止访问然后再删除');">删除会员</a></span></td>
</tr>
<tr> 
<td class="tl">昵称</td>
<td>&nbsp;<?php echo $passport;?>&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo DT_STATIC;?>image/ico-edit.png" width="16" height="16" title="修改昵称" align="absmiddle" class="c_p" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&action=edit_passport&userid=<?php echo $userid;?>', '修改昵称', 600, 400);"/><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=message&touser=<?php echo $username;?>', '站内信件');" class="t">站内信件</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=chat&username=<?php echo $username;?>', '交谈记录');" class="t">交谈记录</a></span></td>
<td class="tl">姓名</td>
<td>&nbsp;<?php echo $truename;?>  &nbsp; <span class="f_gray">(<?php echo $gender == 1 ? '先生' : '女士';?>)</span></td>
</tr>
<tr>
<td class="tl">会员组</td>
<td<?php if($groupid == 1) {echo ' class="f_green"';} elseif($groupid == 2 || $groupid == 4) {echo ' class="f_red"';} elseif($vip) {echo ' style="color:#CE994F;"';}?>>&nbsp;<?php echo $GROUP[$groupid]['groupname'];?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=grade&username=<?php echo $username;?>', '升级记录');" class="t">升级记录</a></span></td>
<td class="tl"><?php echo VIP;?>指数</td>
<td>&nbsp;<img src="<?php echo DT_SKIN;?>vip_<?php echo $vip;?>.gif"/></td>
</tr>
<?php if($totime) { ?>
<tr>
<td class="tl">服务开始</td>
<td>&nbsp;<?php echo timetodate($fromtime, 3);?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=4&action=record&username=<?php echo $username;?>', '续费记录');" class="t">续费记录</a></span></td>
<td class="tl">服务结束</td>
<td>&nbsp;<?php echo timetodate($totime, 3);?> &nbsp; <?php echo $totime < $DT_TIME ? '<span class="f_red">已过期</span>' : '<span class="f_gray">剩余'.ceil(($totime - $DT_TIME)/86400).'天</span>';?></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><?php echo $DT['money_name'];?>余额</td>
<td>&nbsp;<?php echo $DT['money_sign'];?><?php echo $money;?> <?php echo $DT['money_unit'];?><span class="f_r f_gray"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=record&username=<?php echo $username;?>&action=add', '<?php echo $DT['money_name'];?>增减');" class="t"><?php echo $DT['money_name'];?>增减</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=record&username=<?php echo $username;?>', '<?php echo $DT['money_name'];?>流水');" class="t"><?php echo $DT['money_name'];?>流水</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=2&file=charge&username=<?php echo $username;?>', '充值记录');" class="t">充值记录</a></span></td>
<td class="tl">保证金</td>
<td>&nbsp;<?php echo $DT['money_sign'];?><?php echo $deposit;?> <?php echo $DT['money_unit'];?><span class="f_r f_gray"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=deposit&username=<?php echo $username;?>&action=add', '资金增减');" class="t">资金增减</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=deposit&username=<?php echo $username;?>', '资金记录');" class="t">资金记录</a></span></td>
</tr>
<tr>
<td class="tl">短信余额</td>
<td>&nbsp;<?php echo $sms;?><span class="f_r f_gray"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=sms&action=add&username=<?php echo $username;?>', '短信增减');" class="t">短信增减</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=sms&action=record&username=<?php echo $username;?>', '短信记录');" class="t">短信记录</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=sendsms&action=record&username=<?php echo $username;?>', '发送记录');" class="t">发送记录</a></span></td>
<td class="tl">会员<?php echo $DT['credit_name'];?></td>
<td>&nbsp;<?php echo $credit;?><span class="f_r f_gray"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=credit&username=<?php echo $username;?>&action=add', '<?php echo $DT['credit_name'];?>奖惩');" class="t"><?php echo $DT['credit_name'];?>奖惩</a> &nbsp; | &nbsp; <a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=credit&username=<?php echo $username;?>', '<?php echo $DT['credit_name'];?>流水');" class="t"><?php echo $DT['credit_name'];?>流水</a></span></td>
</tr>
<tr>
<td class="tl">粉丝数量</td>
<td>&nbsp;<?php echo $fans;?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=follow&fusername=<?php echo $username;?>', '粉丝列表');" class="t">粉丝列表</a></span></td>
<td class="tl">关注人数</td>
<td>&nbsp;<?php echo $follows;?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=follow&username=<?php echo $username;?>', '关注列表');" class="t">关注列表</a></span></td>
</tr>
<tr>
<td class="tl">更新时间</td>
<td>&nbsp;<?php echo $edittime ? timetodate($edittime, 6) : '';?></td>
<td class="tl">登录次数</td>
<td>&nbsp;<?php echo $logintimes;?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=loginlog&username=<?php echo $username;?>', '登录日志');" class="t">登录日志</a></span></td>
</tr>
<tr>
<td class="tl">上次登录</td>
<td>&nbsp;<?php echo timetodate($logintime, 6);?></td>
<td class="tl">登录IP</td>
<td>&nbsp;<?php echo $loginip;?> - <?php echo ip2area($loginip);?></td>
</tr>
<tr>
<td class="tl">注册时间</td>
<td>&nbsp;<?php echo timetodate($regtime, 6);?></td>
<td class="tl">注册IP</td>
<td>&nbsp;<?php echo $regip;?> - <?php echo ip2area($regip);?></td>
</tr>
</table>
<div class="tt">备注信息</div>
<table cellspacing="0" class="tb">
<?php
	if($note) {
		echo '<tr><th>时间</th><th>内容</th><th width="150">管理员</th></tr>';
		$N = explode('--------------------', $note);
		foreach($N as $n) {
			if(strpos($n, '|') === false) continue;
			list($_time, $_name, $_note) = explode('|', $n);
			if(strlen(trim($_time)) == 16 && check_name($_name) && $_note) echo '<tr><td align="center">'.trim($_time).'</td><td style="padding:6px 10px;line-height:24px;">'.nl2br(trim($_note)).'</td><td align="center"><a href="javascript:;" onclick="_user(\''.$_name.'\')">'.$_name.'</a></td></tr>';
		}
	}
?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="note_add"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<tr>
<td class="tl">追加备注</td>
<td>
<textarea name="note" style="width:98%;height:48px;overflow:visible;padding:0;"></textarea></td>
<td align="center" width="150"><input type="submit" name="submit" value="追加" class="btn-g"/><?php if($_admin == 1) {?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="$('#edit_note').toggle();" class="t">修改</a><?php } ?></td>
</tr>
</form>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="note_edit"/>
<input type="hidden" name="userid" value="<?php echo $userid;?>"/>
<tbody id="edit_note" style="display:none;">
<tr>
<td class="tl">修改备注</td>
<td>
<textarea name="note" style="width:98%;height:100px;overflow:visible;padding:0;"><?php echo $note;?></textarea></td>
<td align="center"><input type="submit" name="submit" value="修改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="?moduleid=<?php echo $moduleid;?>&action=note_edit&userid=<?php echo $userid;?>&note=" class="t" onclick="return confirm('确定要清空此会员的备注信息吗？此操作将不可撤销');">清空</a></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">&nbsp; 请只修改备注文字，不要改动 | 和 - 符号以及时间和管理员</td>
</tr>
</tbody>
</form>
</table>
<div class="tt">认证信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">手机认证</td>
<td data-side="1">&nbsp;<?php echo $vmobile ? '<span class="f_green">已认证</span>' : '<span class="f_gray">未认证</span>';?> &nbsp; &nbsp; <?php echo $mobile;?><?php if($mobile) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-mobile" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=mobile&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
<td class="tl">邮件认证</td>
<td>&nbsp;<?php echo $vemail ? '<span class="f_green">已认证</span>' : '<span class="f_gray">未认证</span>';?> &nbsp; &nbsp; <?php echo $email;?><?php if($email) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-email" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=email&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
</tr>
<tr>
<td class="tl">实名认证</td>
<td>&nbsp;<?php echo $vtruename ? '<span class="f_green">已认证</span>' : '<span class="f_gray">未认证</span>';?> &nbsp; &nbsp; <?php echo $truename;?> <?php echo $idtype;?> <?php echo $idno;?><?php if($idno) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-idno" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=truename&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
<td class="tl">银行认证</td>
<td>&nbsp;<?php echo $vbank ? '<span class="f_green">已认证</span>' : '<span class="f_gray">未认证</span>';?> &nbsp; &nbsp; <?php echo $bank;?> <?php echo $account;?><?php if($account) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-account" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=bank&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
</tr>
<tr>
<td class="tl">公司认证</td>
<td>&nbsp;<?php echo $vcompany ? '<span class="f_green">已认证</span>' : '<span class="f_gray">未认证</span>';?> &nbsp; &nbsp; <?php echo $company;?> <?php echo $taxid;?><?php if($taxid) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-taxid" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=company&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
<td class="tl">商铺认证</td>
<td>&nbsp;<?php echo $vshop ? '<span class="f_green">已认证</span>' : '<span class="f_gray">未认证</span>';?> &nbsp; &nbsp; <?php echo $shop;?><?php if($shop) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-shop" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=shop&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
</tr>
<tr>
<td class="tl">总体认证</td>
<td>&nbsp;<?php echo $validated ? '<span class="f_green">已完成</span>' : '<span class="f_gray">未完成</span>';?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=validate&action=company&username=<?php echo $username;?>', '认证记录');" class="t">认证记录</a></span></td>
<td class="tl">第三方认证</td>
<td>&nbsp;<?php echo $validator ? '<span class="f_blue">'.$validator.'</span>' : '<span class="f_gray">无</span>';?><span class="f_r f_gray"><?php echo $validator && $validtime ? timetodate($validtime, 3) : '';?></span></td>
</tr>
</table>

<div class="tt">个人资料</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">姓名</td>
<td data-side="1">&nbsp;<?php echo $truename;?>  &nbsp; <span class="f_gray">(<?php echo $gender == 1 ? '先生' : '女士';?>)</span> <?php if($truename) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-truename" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
<td class="tl">手机</td>
<td>&nbsp;<?php if($mobile) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $mobile;?>', '发送短信');"><img src="static/image/mobile.gif" title="发送短信" align="absmiddle"/></a> <?php } ?><a href="javascript:;" onclick="_mobile('<?php echo $mobile;?>');" title="归属地查询"><?php echo $mobile;?></a><?php if($mobile) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-mobile" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><?php if($mobile) {?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&action=record&mobile=<?php echo $mobile;?>', '短信记录');" class="t">短信记录</a></span><?php }?></td>
</tr>
<tr>
<td class="tl">证件</td>
<td>&nbsp;<?php echo $idtype;?></td>
<td class="tl">号码</td>
<td>&nbsp;<?php echo $idno;?><?php if($idno) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-idno" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">部门</td>
<td>&nbsp;<?php echo $department;?></td>
<td class="tl">职位</td>
<td>&nbsp;<?php echo $career;?></td>
</tr>
<tr>
<td class="tl">电话</td>
<td>&nbsp;<?php echo $telephone;?><?php if($telephone) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-telephone" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
<td class="tl">传真</td>
<td>&nbsp;<?php echo $fax;?><?php if($fax) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-fax" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">邮件 (不公开)</td>
<td>&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $email;?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送Email <?php echo $email;?>" alt="" align="absmiddle"/></a> <?php echo $email;?><?php if($email) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-email" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><?php if($email) {?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&action=record&email=<?php echo $email;?>', '邮件记录');" class="t">邮件记录</a></span><?php }?></td>
<td class="tl">邮件 (公开)</td>
<td>&nbsp;<?php if($mail) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $mail;?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送Email <?php echo $mail;?>" alt="" align="absmiddle"/></a> <?php } ?><?php echo $mail;?><?php if($mail) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-mail" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><?php if($mail) {?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&action=record&email=<?php echo $mail;?>', '邮件记录');" class="t">邮件记录</a></span><?php }?></td>
</tr>
<tr>
<td class="tl">QQ</td>
<td>&nbsp;<?php echo im_qq($qq);?> <?php echo $qq;?><?php if($qq) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-qq" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
<td class="tl">阿里旺旺</td>
<td>&nbsp;<?php echo im_ali($ali);?> <?php echo $ali;?><?php if($ali) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-ali" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">微信</td>
<td>&nbsp;<?php echo im_wx($wx, $username);?> <?php echo $wx;?><?php if($wx) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-wx" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><?php if($wxqr) {?><span class="f_r"><a href="javascript:;" onclick="_preview('<?php echo $wxqr;?>');" class="t">二维码</a></span><?php } ?></td>
<td class="tl">Skype</td>
<td>&nbsp;<?php echo im_skype($skype);?> <?php echo $skype;?><?php if($skype) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-skype" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">个人空间</td>
<td>&nbsp;<a href="<?php echo userurl($username, 'file=space', $domain);?>" class="t" target="_blank"><?php echo userurl($username, 'file=space', $domain);?></a></td>
<td class="tl">空间封面</td>
<td>&nbsp;<a href="javascript:;" onclick="_preview('<?php echo $cover;?>');" class="t"><?php echo $cover;?></a></td>
</tr>
<tr>
<td class="tl">个性签名</td>
<td colspan="3">&nbsp;<?php echo $sign;?></td>
</tr>
<tr>
<td class="tl">相关操作</td>
<td colspan="3" class="f_gray">&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&buyer=<?php echo $username;?>', '订单记录');" class="t">订单记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&action=contract&buyer=<?php echo $username;?>', '合同记录');" class="t">合同记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&action=invoice&buyer=<?php echo $username;?>', '开票记录');" class="t">开票记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=promo&action=coupon&username=<?php echo $username;?>', '领券记录');" class="t">领券记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?file=keyword&action=record&username=<?php echo $username;?>', '搜索记录');" class="t">搜索记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=favorite&userid=<?php echo $userid;?>', '收藏记录');" class="t">收藏记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=history&username=<?php echo $username;?>', '浏览历史');" class="t">浏览历史</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=oauth&username=<?php echo $username;?>', '社交账号');" class="t">社交账号</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=friend&userid=<?php echo $userid;?>', '好友列表');" class="t">好友列表</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=follow&fusername=<?php echo $username;?>', '粉丝列表');" class="t">粉丝列表</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=follow&username=<?php echo $username;?>', '关注列表');" class="t">关注列表</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=friend&action=list&username=<?php echo $username;?>', '黑名单');" class="t">黑名单</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=sendpush&username=<?php echo $username;?>', '推送记录');" class="t">推送记录</a>
</td>
</tr>
</table>

<div class="tt">公司资料</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">公司名称</td>
<td data-side="1">&nbsp;<?php echo $company;?><?php if($company) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-company" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="<?php echo DT_PATH;?>api/company<?php echo DT_EXT;?>?wd=<?php echo urlencode($company);?>" target="_blank" class="t">工商查询</a></span></td>
<td class="tl">商铺名称</td>
<td>&nbsp;<?php echo $shop;?><?php if($shop) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-shop" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&seller=<?php echo $username;?>', '订单记录');" class="t">订单记录</a></span></td>
</tr>
<tr>
<td class="tl">公司主页</td>
<td>&nbsp;<a href="<?php echo $linkurl;?>" target="_blank" class="t"><?php echo $linkurl;?></a></td>
<td class="tl">浏览次数</td>
<td>&nbsp;<?php echo $hits;?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&homepage=<?php echo $username;?>', '[<?php echo $company;?>] 浏览记录');" class="t">浏览记录</a></span></td>
</tr>
<tr>
<td class="tl">公司网站</td>
<td>&nbsp;<a href="<?php echo gourl($homepage);?>" class="t" target="_blank"><?php echo $homepage;?></a></td>
<td class="tl">形象图片</td>
<td>&nbsp;<a href="javascript:;" onclick="_preview('<?php echo $thumb;?>');" class="t"><?php echo $thumb;?></a></td>
</tr>
<tr>
<td class="tl">公司类型</td>
<td>&nbsp;<?php echo $type;?></td>
<td class="tl">社会信用代码</td>
<td>&nbsp;<?php echo $taxid;?><?php if($taxid) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-taxid" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">可开发票</td>
<td>&nbsp;<?php echo $invoice;?><?php if($invoice) {?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&action=invoice&seller=<?php echo $username;?>', '开票记录');" class="t">开票记录</a></span><?php }?></td>
<td class="tl">经营模式</td>
<td>&nbsp;<?php echo $mode;?></td>
</tr>
<tr>
<td class="tl">注册资本</td>
<td>&nbsp;<?php echo $capital;?>万<?php echo $regunit;?></td>
<td class="tl">公司规模</td>
<td>&nbsp;<?php echo $size;?></td>
</tr>
<tr>
<td class="tl">成立年份</td>
<td>&nbsp;<?php echo $regyear;?></td>
<td class="tl">公司所在地</td>
<td>&nbsp;<?php echo area_pos($areaid, '/');?></td>
</tr>
<tr>
<td class="tl">注册地址</td>
<td>&nbsp;<?php echo $address;?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=address&username=<?php echo $username;?>', '收货地址');" class="t">收货地址</a></span></td>
<td class="tl">邮政编码</td>
<td>&nbsp;<?php echo $postcode;?></td>
</tr>

<tr>
<td class="tl">审核订单</td>
<td>&nbsp;<?php echo $checkorder ? '开启' : '关闭';?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&seller=<?php echo $username;?>', '订单记录');" class="t">订单列表</a></span></td>
<td class="tl">分销代理</td>
<td>&nbsp;<?php echo $agent ? '开启' : '关闭';?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=agent&username=<?php echo $username;?>', '代理列表');" class="t">代理列表</a></span></td>
</tr>

<tr>
<td class="tl">微信公众号</td>
<td>&nbsp;<?php echo im_gzh($gzh, $username);?> <?php echo $gzh;?><?php if($gzh) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-gzh" onclick="Dtoast('复制成功');" title="复制"/><?php } ?><?php if($gzhqr) {?><span class="f_r"><a href="javascript:;" onclick="_preview('<?php echo $gzhqr;?>');" class="t">二维码</a></span><?php } ?></td>
<td class="tl">线下收款</td>
<td>&nbsp;<?php echo $bill ? '开启' : '关闭';?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&seller=<?php echo $username;?>&bill=1', '收款记录');" class="t">收款记录</a></span></td>
</tr>
<tr>
<td class="tl">销售产品</td>
<td>&nbsp;<?php echo $sell;?></td>
<td class="tl">采购产品</td>
<td>&nbsp;<?php echo $buy;?></td>
</tr>
<tr>
<td class="tl">经营范围</td>
<td colspan="3">&nbsp;<?php echo $business;?></td>
</tr>
<?php if($catid) { ?>
<?php $MOD['linkurl'] = $MODULE[4]['linkurl'];?>
<?php $catids = explode(',', substr($catid, 1, -1));?>
<tr>
<td class="tl">主营行业</td>
<td colspan="3">&nbsp;<?php foreach($catids as $i=>$c) { ?><?php echo cat_pos(get_cat($c), ' / ', '_blank');?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
</td>
</tr>
<?php } ?>
<tr>
<td class="tl">相关操作</td>
<td colspan="3" class="f_gray">&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&seller=<?php echo $username;?>', '订单记录');" class="t">订单记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&action=contract&seller=<?php echo $username;?>', '合同记录');" class="t">合同记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=16&file=order&action=invoice&seller=<?php echo $username;?>', '开票记录');" class="t">开票记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=promo&username=<?php echo $username;?>', '促销记录');" class="t">促销记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=alert&username=<?php echo $username;?>', '贸易提醒');" class="t">贸易提醒</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=agent&username=<?php echo $username;?>', '代理列表');" class="t">代理列表</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=agent&pusername=<?php echo $username;?>', '上级代理');" class="t">上级代理</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=child&username=<?php echo $username;?>', '子账号');" class="t">子账号</a>
</td>
</tr>
</table>

<div class="tt">财务信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">开户银行</td>
<td data-side="1">&nbsp;<?php echo $bank;?><?php if($bank) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-bank" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
<td class="tl">开户网点</td>
<td>&nbsp;<?php echo $branch;?><?php if($branch) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-branch" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">收款户名</td>
<td>&nbsp;<?php echo $MG['type'] ? $company : $truename;?><?php if($MG['type'] ? $company : $truename) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-<?php echo $MG['type'] ? 'company' : 'truename';?>" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
<td class="tl">收款帐号</td>
<td>&nbsp;<?php echo $account;?><?php if($account) {?><img src="static/image/ico-copy.png" class="cp" data-clipboard-action="copy" data-clipboard-target="#copy-account" onclick="Dtoast('复制成功');" title="复制"/><?php } ?></td>
</tr>
<tr>
<td class="tl">相关操作</td>
<td colspan="3" class="f_gray">&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=charge&username=<?php echo $username;?>', '充值记录');" class="t">充值记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=record&username=<?php echo $username;?>', '资金流水');" class="t">资金流水</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=cash&username=<?php echo $username;?>', '提现记录');" class="t">提现记录</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=pay&username=<?php echo $username;?>', '信息支付');" class="t">信息支付</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&username=<?php echo $username;?>', '信息打赏');" class="t">信息打赏</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=card&username=<?php echo $username;?>', '充值卡');" class="t">充值卡</a> &nbsp; | &nbsp;
<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=deposit&username=<?php echo $username;?>', '保证金');" class="t">保证金</a>
</td>
</tr>
</table>
<div class="tt">其他信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">主页风格目录 </td>
<td>&nbsp;<?php echo $skin;?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=style&action=order&username=<?php echo $username;?>', '购买记录');" class="t">购买记录</a></span></td>
</tr>
<tr>
<td class="tl">主页模板目录 </td>
<td>&nbsp;<?php echo $template;?></td>
</tr>
<tr>
<td class="tl">顶级域名</td>
<td>&nbsp;<?php echo $domain;?></td>
</tr>
<tr>
<td class="tl">ICP备案号</td>
<td>&nbsp;<?php echo $icp;?></td>
</tr>
<tr>
<td class="tl">网安备案号</td>
<td>&nbsp;<?php echo $wan;?></td>
</tr>
<tr>
<td class="tl">客服专员</td>
<td>&nbsp;<a href="javascript:;" onclick="_user('<?php echo $support;?>');" class="t"><?php echo $support;?></a><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=ask&username=<?php echo $username;?>', '问答记录');" class="t">问答记录</a></span></td>
</tr>
<tr>
<td class="tl">邀请注册人</td>
<td>&nbsp;<a href="javascript:;" onclick="_user('<?php echo $inviter;?>');" class="t"><?php echo $inviter;?></a><?php if($inviter) { ?><span class="f_r"><a href="javascript:;" onclick="Dwidget('?moduleid=2&inviter=<?php echo $inviter;?>', '推荐记录');" class="t">推荐记录</a></span><?php } ?></td>
</tr>
<?php 
if($MFD) {
	foreach($MFD as $k=>$v) {
?>
<tr>
<td class="tl"><?php echo $v['title'];?></td>
<td>&nbsp;<?php echo $$v['name'];?></td>
</tr>
<?php 
	}
}
?>
<?php 
if($CFD) {
	foreach($CFD as $k=>$v) {
?>
<tr>
<td class="tl"><?php echo $v['title'];?></td>
<td>&nbsp;<?php echo $$v['name'];?></td>
</tr>
<?php 
	}
}
?>
</table>
<div class="sbt">
	<input type="button" value="登录前台" class="btn-g" onclick="window.open('?moduleid=<?php echo $moduleid;?>&action=login&userid=<?php echo $userid;?>');"/> &nbsp; &nbsp;
	<input type="button" value="修改资料" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=edit&userid=<?php echo $userid;?>&forward=<?php echo urlencode($DT_URL);?>');"/> &nbsp; &nbsp;
	<input type="button" value="更新缓存" class="btn" onclick="if(confirm('确定要更新此会员的缓存数据吗？仅前台会员资料异常时可以更新，一般无需更新')) Go('?moduleid=<?php echo $moduleid;?>&action=clean&username=<?php echo $username;?>');"/> &nbsp; &nbsp;
	<input type="button" value="禁止访问" class="btn-r" onclick="if(confirm('确定要禁止此会员访问吗？')) Go('?moduleid=<?php echo $moduleid;?>&action=move&groupids=2&userid=<?php echo $userid;?>');"/> &nbsp; &nbsp;
	<input type="button" value=" 返 回 " class="btn" onclick="if(window.parent.document.getElementById('Dtop')){window.parent.cDialog()}else{history.back(-1);}"/>
</div>
<div style="z-index:1000;position:absolute;top:-10000px;">
<textarea id="copy-company"><?php echo $company;?></textarea>
<textarea id="copy-shop"><?php echo $shop;?></textarea>
<textarea id="copy-taxid"><?php echo $taxid;?></textarea>
<textarea id="copy-gzh"><?php echo $gzh;?></textarea>
<textarea id="copy-truename"><?php echo $truename;?></textarea>
<textarea id="copy-mobile"><?php echo $mobile;?></textarea>
<textarea id="copy-idno"><?php echo $idno;?></textarea>
<textarea id="copy-telephone"><?php echo $telephone;?></textarea>
<textarea id="copy-fax"><?php echo $fax;?></textarea>
<textarea id="copy-email"><?php echo $email;?></textarea>
<textarea id="copy-mail"><?php echo $mail;?></textarea>
<textarea id="copy-qq"><?php echo $qq;?></textarea>
<textarea id="copy-ali"><?php echo $ali;?></textarea>
<textarea id="copy-wx"><?php echo $wx;?></textarea>
<textarea id="copy-skype"><?php echo $skype;?></textarea>
<textarea id="copy-bank"><?php echo $bank;?></textarea>
<textarea id="copy-branch"><?php echo $branch;?></textarea>
<textarea id="copy-account"><?php echo $account;?></textarea>
</div>
<?php load('clipboard.min.js');?>
<script type="text/javascript">
var clipboard = new Clipboard('[data-clipboard-action]');
$(function() {
	var w = ($(window).width()-352)/2;
	$('[data-side]').width(w);
	$('#top-side').width(w-176);
});
Menuon(1);
</script>
<?php include tpl('footer');?>