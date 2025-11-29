<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('member.css');
?>
<style>.userinfo-v0,.userinfo-v1,.userinfo-v2 {margin:-16px 0 0 36px;}</style>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $group_select;?>&nbsp;
<?php echo $grade_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo $enterprise_select;?>&nbsp;
<?php echo $gender_select;?>&nbsp;
<?php echo $avatar_select;?>&nbsp;
<?php echo $vprofile_select;?>&nbsp
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
<select name="datetype">
<option value="regtime"<?php if($datetype == 'regtime') echo ' selected';?>>注册时间</option>
<option value="logintime"<?php if($datetype == 'logintime') echo ' selected';?>>登录时间</option>
<option value="edittime"<?php if($datetype == 'edittime') echo ' selected';?>>修改时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mixt">
<option value="money"<?php if($mixt == 'money') echo ' selected';?>><?php echo $DT['money_name'];?></option>
<option value="credit"<?php if($mixt == 'credit') echo ' selected';?>><?php echo $DT['credit_name'];?></option>
<option value="sms"<?php if($mixt == 'sms') echo ' selected';?>>短信</option>
<option value="deposit"<?php if($mixt == 'deposit') echo ' selected';?>>保证金</option>
<option value="fans"<?php if($mixt == 'fans') echo ' selected';?>>粉丝</option>
<option value="follows"<?php if($mixt == 'follows') echo ' selected';?>>关注</option>
<option value="moments"<?php if($mixt == 'moments') echo ' selected';?>>动态</option>
<option value="logtimes"<?php if($mixt == 'logtimes') echo ' selected';?>>登录次数</option>
</select>&nbsp;
<input type="text" size="6" name="minv" value="<?php echo $minv;?>"/>~<input type="text" size="6" name="maxv" value="<?php echo $maxv;?>"/>&nbsp;
<?php echo ajax_area_select('areaid', '所在地区', $areaid);?>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo $snn_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="12" placeholder="会员名" title="会员名"/>&nbsp;
<input type="text" name="uid" value="<?php echo $uid;?>" size="12" placeholder="会员ID" title="会员ID"/>&nbsp;
<input type="text" name="passport" value="<?php echo $passport;?>" size="12" placeholder="会员昵称" title="会员昵称"/>&nbsp;
<input type="text" name="mobile" value="<?php echo $mobile;?>" size="12" placeholder="手机号" title="手机号"/>&nbsp;
<input type="text" name="inviter" value="<?php echo $inviter;?>" size="12" placeholder="邀请人" title="邀请人"/>&nbsp;
<input type="text" name="support" value="<?php echo $support;?>" size="12" placeholder="客服" title="客服"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th data-hide-1200="1" width="48"><a href="javascript:;" onclick="Dq('avatar','1');">头像</a></th>
<th>会员名</th>
<th>昵称</th>
<th>公司</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');"><?php echo $DT['money_name'];?> <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');"><?php echo $DT['credit_name'];?> <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');">粉丝 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 25 ? 26 : 25;?>');">会员组 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 26 ? 'asc' : ($order == 25 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 27 ? 28 : 27;?>');">积分组 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 28 ? 'asc' : ($order == 27 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">注册时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($nn) { ?>
<th><?php echo $snn[$nn];?></th>
<?php } else { ?>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">最后登录 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">登录次数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1" data-hide-1600="1">登录地</th>
<?php } ?>
<th data-hide-1200="1" data-hide-1400="1" data-hide-1600="1" width="150">备注</th>
<th width="40">登入</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="userid[]" value="<?php echo $v['userid'];?>"/></td>
<td data-hide-1200="1"><img src="<?php echo useravatar($v['username'], 'large');?>" width="48" height="48" class="c_p avatar" onclick="_preview(this.src);"/><i class="userinfo-v<?php echo $v['validate'];?>" title="<?php echo valid_name($v['validate']);?>" onclick="Dq('validate', '<?php echo $v['validate'];?>');"></i></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="<?php echo userurl($v['username'], 'file=space');?>" title="个人空间" target="_blank"><?php echo $v['passport'];?></a></td>
<td align="left">&nbsp;<a href="<?php echo userurl($v['username']);?>" title="公司主页" target="_blank"><?php echo $v['company'] ? $v['company'] : $v['shop'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=record&username=<?php echo $v['username'];?>', '[<?php echo $v['username'];?>] <?php echo $DT['money_name'];?>记录');"><?php echo $DT['money_sign'].number_format($v['money'], 2);?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=credit&username=<?php echo $v['username'];?>', '[<?php echo $v['username'];?>] <?php echo $DT['credit_name'];?>记录');"><?php echo numtoread($v['credit']);?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=follow&username=<?php echo $v['username'];?>', '[<?php echo $v['username'];?>] 粉丝列表');"><?php echo numtoread($v['fans']);?></a></td>
<td><a href="javascript:;" onclick="Dq('groupid','<?php echo $v['groupid'];?>');"><?php echo $GROUP[$v['groupid']]['groupname'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('gradeid','<?php echo $v['gradeid'];?>');"><?php echo $GRADE[$v['gradeid']]['name'];?></a></td>
<td title="修改时间:<?php echo $v['edittime'] ? timetodate($v['edittime']) : '无';?>"><a href="javascript:;" onclick="Dq('datetype','regtime',0);Dq('date',this.title);" title="<?php echo $v['regdate'];?>"><?php echo timetoread($v['regtime']);?></a></td>
<?php if($nn) { ?>
<td>
	<?php if($nn == 'inviter' || $nn == 'support') { ?>
		<a href="javascript:;" onclick="_user('<?php echo $v[$nn];?>');"><?php echo $v[$nn];?></a>
	<?php } else if($nn == 'shop') { ?>
		<a href="<?php echo userurl($v['username']);?>" title="公司主页" target="_blank"><?php echo $v[$nn];?></a>
	<?php } else if($nn == 'sign') { ?>
		<textarea style="width:150px;height:32px;" title="<?php echo $v[$nn];?>"><?php echo $v[$nn];?></textarea>
	<?php } else if($nn == 'mobile') { ?>
		<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendsms&mobile=<?php echo $v[$nn];?>', '发送短信');"><?php echo $v[$nn];?></a>
	<?php } else if($nn == 'email') { ?>
		<a href="javascript:;" onclick="Dwidget('?moduleid=2&file=sendmail&email=<?php echo $v[$nn];?>', '发送邮件');"><?php echo $v[$nn];?></a>
	<?php } else if($nn == 'wx') { ?>
		<a href="api/wx.php?wid=<?php echo $v[$nn];?>&username=<?php echo $v['username'];?>" target="_blank"><?php echo $v[$nn];?></a>
	<?php } else if($nn == 'qq') { ?>
		<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $v[$nn];?>&site=qq&menu=yes" target="_blank"><?php echo $v[$nn];?></a>
	<?php } else { ?>
		<?php echo $v[$nn];?>
	<?php } ?>
</td>
<?php } else { ?>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('datetype','logintime',0);Dq('date',this.title);" title="<?php echo $v['logindate'];?>"><?php echo timetoread($v['logintime']);?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=loginlog&username=<?php echo $v['username'];?>&action=record', '[<?php echo $v['username'];?>] 登录记录');"><?php echo $v['logintimes'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1"><a href="javascript:;" onclick="_ip('<?php echo $v['loginip'];?>');"><?php echo ip2area($v['loginip'], 2);?></a></td>
<?php } ?>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1"><textarea style="width:150px;height:32px;" title="双击修改备注<?php echo $v['note'] ? '&#10;'.$v['note'] : '';?>" ondblclick="_user('<?php echo $v['username'];?>');"><?php echo $v['note'];?></textarea></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&action=login&userid=<?php echo $v['userid'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="进入会员中心" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&action=edit&userid=<?php echo $v['userid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更新会员" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&action=update';"/>&nbsp;
<input type="submit" value="删除会员" class="btn-r" onclick="if(confirm('确定要删除选中会员吗？系统将删除选中用户所有信息，此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="删除头像" class="btn-r" onclick="if(confirm('确定要删除选中会员头像吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&action=avatar'}else{return false;}"/>&nbsp;
<input type="submit" value="禁止访问" class="btn-r" onclick="if(confirm('确定要禁止选中会员访问吗？')){this.form.action='?moduleid=<?php echo $moduleid;?>&action=move&groupids=2'}else{return false;}"/>&nbsp;
<input type="submit" value="设置<?php echo VIP;?>" class="btn" onclick="this.form.action='?moduleid=4&action=add';"/>&nbsp;
<span data-hide-1200="1" data-hide-1400="1">
<input type="submit" value=" <?php echo $DT['money_name'];?>增减 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=record&action=add';"/>&nbsp;
<input type="submit" value=" <?php echo $DT['credit_name'];?>奖惩 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=credit&action=add';"/>&nbsp;
<input type="submit" value=" 短信增减 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=sms&action=add';"/>&nbsp;
<input type="submit" value=" 发送短信 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=sendsms';"/>&nbsp;
<input type="submit" value=" 推送消息 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=sendpush';"/>&nbsp;
</span>
<input type="submit" value=" 发送邮件 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=sendmail';"/>&nbsp;
<input type="submit" value=" 站内信件 " class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=message&action=send';"/>&nbsp;
<input type="submit" value="移动至" class="btn" onclick="if(Dd('mgroupid').value==0){alert('请选择会员组');Dd('mgroupid').focus();return false;}this.form.action='?moduleid=<?php echo $moduleid;?>&action=move';"/>&nbsp;
<?php echo group_select('groupid', '会员组', 0, 'id="mgroupid"');?> 
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>