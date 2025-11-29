<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('member.css');
?>
<style>.userinfo-v0,.userinfo-v1,.userinfo-v2 {margin:-16px 0 0 36px;}</style>
<form action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="export" id="export" value="<?php echo $export;?>"/>
<input type="hidden" name="page" id="page" value="0"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="20" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $group_select;?>&nbsp;
<?php echo $grade_select;?>&nbsp;
<select name="vip">
<option value=""><?php echo VIP;?>级别</option>
<?php 
for($i = 0; $i < 11; $i++) {
	echo '<option value="'.$i.'"'.($i == $vip ? ' selected' : '').'>'.$i.' 级</option>';
}
?>
</select>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>&nbsp;
<span class="f_r"><input type="submit" value="导出数据" class="btn-g" onclick="Dd('export').value=1;Dd('page').value=<?php echo $page;?>;setTimeout(function(){Dd('export').value=0;Dd('page').value=0;},1000);"/></span>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo $enterprise_select;?>&nbsp;
<?php echo $gender_select;?>&nbsp;
<?php echo $avatar_select;?>&nbsp;
<?php echo $vprofile_select;?>&nbsp;
<?php echo $vemail_select;?>&nbsp;
<?php echo $vmobile_select;?>&nbsp;
<?php echo $vtruename_select;?>&nbsp;
<?php echo $vbank_select;?>&nbsp;
<?php echo $vcompany_select;?>&nbsp;
<?php echo $vshop_select;?>&nbsp;
<?php echo $validate_select;?>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo category_select('catid', '所属行业', $catid, 4);?>&nbsp;
<?php echo ajax_area_select('areaid', '所在地区', $areaid);?>&nbsp;
<?php echo $mode_select;?>&nbsp;
<?php echo $type_select;?>&nbsp;
<?php echo $size_select;?>&nbsp;
<select name="mixt">
<option value="m.money"<?php if($mixt == 'm.money') echo ' selected';?>><?php echo $DT['money_name'];?></option>
<option value="m.credit"<?php if($mixt == 'm.credit') echo ' selected';?>><?php echo $DT['credit_name'];?></option>
<option value="m.sms"<?php if($mixt == 'm.sms') echo ' selected';?>>短信</option>
<option value="m.deposit"<?php if($mixt == 'm.deposit') echo ' selected';?>>保证金</option>
<option value="m.fans"<?php if($mixt == 'm.fans') echo ' selected';?>>粉丝</option>
<option value="m.follows"<?php if($mixt == 'm.follows') echo ' selected';?>>关注</option>
<option value="m.moments"<?php if($mixt == 'm.moments') echo ' selected';?>>动态</option>
<option value="m.logtimes"<?php if($mixt == 'm.logtimes') echo ' selected';?>>登录次数</option>
<option value="c.regyear"<?php if($mixt == 'c.regyear') echo ' selected';?>>注册年份</option>
<option value="c.capital"<?php if($mixt == 'c.capital') echo ' selected';?>>注册资本</option>
<option value="c.hits"<?php if($mixt == 'c.hits') echo ' selected';?>>浏览次数</option>
<option value="c.comments"<?php if($mixt == 'c.comment') echo ' selected';?>>评论次数</option>
</select>&nbsp;
<input type="text" size="6" name="minv" value="<?php echo $minv;?>"/>~<input type="text" size="6" name="maxv" value="<?php echo $maxv;?>"/>
<input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/>图片&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<select name="timetype">
<option value="m.regtime"<?php if($timetype == 'm.regtime') echo ' selected';?>>注册时间</option>
<option value="m.logintime"<?php if($timetype == 'm.logintime') echo ' selected';?>>登录时间</option>
<option value="c.totime"<?php if($timetype == 'c.totime') echo ' selected';?>>服务到期</option>
<option value="c.fromtime"<?php if($timetype == 'c.fromtime') echo ' selected';?>>服务开始</option>
<option value="c.validtime"<?php if($timetype == 'c.validtime') echo ' selected';?>>认证时间</option>
<option value="c.styletime"<?php if($timetype == 'c.styletime') echo ' selected';?>>模板到期</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="uid" value="<?php echo $uid;?>" size="10" placeholder="会员ID" title="会员ID 双击显示会员资料" ondblclick="if(this.value){_user(this.value,'userid');}"/>&nbsp;
<input type="text" name="passport" value="<?php echo $passport;?>" size="12" placeholder="会员昵称" title="会员昵称"/>&nbsp;
<input type="text" name="mobile" value="<?php echo $mobile;?>" size="12" placeholder="手机号" title="手机号"/>&nbsp;
</td>
</tr>
</table>
</form>
<?php
$cols = 3;
if($DT['im_web']) $cols++;
if($DT['im_qq']) $cols++;
if($DT['im_wx']) $cols++;
if($DT['im_ali']) $cols++;
if($DT['im_skype']) $cols++;
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="48">头像</th>
<th>会员名</th>
<th>昵称</th>
<th width="16"></th>
<th>公司</th>
<th>姓名</th>
<th>职位</th>
<th>性别</th>
<th>手机</th>
<th data-hide-1200="1" data-hide-1400="1" data-hide-1600="1">邮件</th>
<?php if($DT['im_qq']) { ?><th data-hide-1200="1" data-hide-1400="1">QQ</th><?php } ?>
<?php if($DT['im_wx']) { ?><th data-hide-1200="1" data-hide-1400="1">微信</th><?php } ?>
<th colspan="<?php echo $cols;?>">联系方式</th>
<th width="40" data-hide-1200="1" data-hide-1400="1">状态</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><img src="<?php echo useravatar($v['username'], 'large');?>" width="48" height="48" class="c_p avatar" onclick="_preview(this.src);"/><i class="userinfo-v<?php echo $v['validate'];?>" title="<?php echo valid_name($v['validate']);?>" onclick="Dq('validate', '<?php echo $v['validate'];?>');"></i></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="<?php echo userurl($v['username'], 'file=space');?>" title="个人空间" target="_blank"><?php echo $v['passport'];?></a></td>
<td><?php if($v['vip']) {?><img src="<?php echo DT_SKIN;?>vip_<?php echo $v['vip'];?>.gif" title="<?php echo VIP;?>:<?php echo $v['vip'];?>" align="absmiddle"/><?php } ?></td>
<td align="left">&nbsp;<a href="<?php echo userurl($v['username']);?>" title="公司主页" target="_blank"><?php if($v['company']) { ?><span<?php if($v['vcompany']) {?> class="f_green" title="已认证"<?php } ?>><?php echo $v['company'];?></span><?php } else { ?><span<?php if($v['vshop']) {?> class="f_green" title="已认证"<?php } ?>><?php echo $v['shop'];?></span><?php } ?></a></td>
<td><span<?php if($v['vtruename']) {?> class="f_green" title="已认证"<?php } ?>><?php echo $v['truename'];?></span></td>
<td><?php echo $v['career'];?></td>
<td><?php echo gender($v['gender']);?></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $v['mobile'];?>', '发送短信');"><span<?php if($v['vmobile']) {?> class="f_green" title="已认证"<?php } ?>><?php echo $v['mobile'];?></span></a></td>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $v['email'];?>', '发送邮件');"><span<?php if($v['vmobile']) {?> class="f_green" title="已认证"<?php } ?>><?php echo $v['email'];?></span></a></td>
<?php if($DT['im_qq']) { ?><td data-hide-1200="1" data-hide-1400="1"><?php echo $v['qq'];?></td><?php } ?>
<?php if($DT['im_wx']) { ?><td data-hide-1200="1" data-hide-1400="1"><?php echo $v['wx'];?></td><?php } ?>
<td width="20"><?php if($v['mobile']) { ?><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $v['mobile'];?>', '发送短信');"><img src="static/image/mobile.gif" title="发送短信" alt=""/></a><?php } ?></td>
<td width="20"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=message&action=send&touser=<?php echo $v['username'];?>', '发送消息');"><img width="16" height="16" src="static/image/msg.gif" title="发送消息" alt=""/></a></td> 
<td width="20"><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $v['email'];?>', '发送邮件');"><img width="16" height="16" src="static/image/email.gif" title="发送邮件" alt=""/></a></td>
<?php if($DT['im_web']) { ?><td width="20"><?php echo im_web($v['username']);?></td><?php } ?>
<?php if($DT['im_qq']) { ?><td width="20"><?php if($v['qq']) { echo im_qq($v['qq']); } ?></td><?php } ?>
<?php if($DT['im_wx']) { ?><td width="20"><?php if($v['wx']) { echo im_wx($v['wx'], $v['username']); } ?></td><?php } ?>
<?php if($DT['im_ali']) { ?><td width="20"><?php if($v['ali']) { echo im_ali($v['ali']); } ?></td><?php } ?>
<?php if($DT['im_skype']) { ?><td width="20"><?php if($v['skype']) { echo im_skype($v['skype']); } ?></td><?php } ?>
<td data-hide-1200="1" data-hide-1400="1"><?php $ol = online($v['userid']);if($ol == 1) { ?><span class="f_green">在线</span><?php } else if($ol == -1) { ?><span class="f_blue">隐身</span><?php } else { ?><span class="f_gray">离线</span><?php } ?></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<br/>
<script type="text/javascript">Menuon(4);</script>
<?php include tpl('footer');?>