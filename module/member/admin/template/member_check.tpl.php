<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $gender_select;?>&nbsp;
<?php echo ajax_area_select('areaid', '所在地区', $areaid);?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="timetype">
<option value="regtime"<?php if($timetype == 'regtime') echo ' selected';?>>注册时间</option>
<option value="logintime"<?php if($timetype == 'logintime') echo ' selected';?>>登录时间</option>
<option value="edittime"<?php if($timetype == 'edittime') echo ' selected';?>>修改时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="12" placeholder="会员名" title="会员名"/>&nbsp;
<input type="text" name="uid" value="<?php echo $uid;?>" size="12" placeholder="会员ID" title="会员ID"/>&nbsp;
<input type="text" name="passport" value="<?php echo $passport;?>" size="12" placeholder="会员昵称" title="会员昵称"/>&nbsp;
<input type="text" name="mobile" value="<?php echo $mobile;?>" size="12" placeholder="手机号" title="手机号"/>&nbsp;
<input type="text" name="inviter" value="<?php echo $inviter;?>" size="12" placeholder="邀请人" title="邀请人"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>会员名</th>
<th>昵称</th>
<th>手机</th>
<th>公司</th>
<th>姓名</th>
<th>性别</th>
<th>注册组</th>
<th>注册时间</th>
<th>注册IP</th>
<th>归属地</th>
<th>邀请人</th>
<th width="150">注册理由</th>
<th width="40">登入</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="userid[]" value="<?php echo $v['userid'];?>"/></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="<?php echo userurl($v['username'], 'file=space');?>" title="个人空间" target="_blank"><?php echo $v['passport'];?></a></td>
<td><a href="javascript:;" onclick="_mobile('<?php echo $v['mobile'];?>');"><?php echo $v['mobile'];?></a></td>
<td align="left">&nbsp;<a href="<?php echo userurl($v['username']);?>" title="公司主页" target="_blank"><?php echo $v['company'] ? $v['company'] : $v['shop'];?></a></td>
<td><?php echo $v['truename'];?></td>
<td><?php echo $v['gender'] == 1 ? '先生' : '女士';?></td>
<td><?php echo $GROUP[$v['regid']]['groupname'];?></td>
<td><a href="javascript:;" onclick="Dq('timetype','regtime',0);Dq('date',this.title);" title="<?php echo $v['regdate'];?>"><?php echo timetoread($v['regtime']);?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',15,0);Dq('kw','='+this.innerHTML);"><?php echo $v['regip'];?></a></td>
<td><a href="javascript:;" onclick="_ip('<?php echo $v['regip'];?>');"><?php echo ip2area($v['regip'], 2);?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['inviter'];?>');"><?php echo $v['inviter'];?></a></td>
<td title="<?php echo $v['reason'];?>"><textarea style="width:150px;height:32px;"><?php echo $v['reason'];?></textarea></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&action=login&userid=<?php echo $v['userid'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="进入会员中心" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&action=edit&userid=<?php echo $v['userid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="通过审核" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&action=check';"/>&nbsp;
<input type="submit" value="删除会员" class="btn-r" onclick="if(confirm('确定要删除选中<?php echo $MOD['name'];?>吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="禁止访问" class="btn-r" onclick="if(confirm('确定要禁止选中会员访问吗？')){this.form.action='?moduleid=<?php echo $moduleid;?>&action=move&groupids=2'}else{return false;}"/>&nbsp;
<input type="submit" value="移动至" class="btn" onclick="if(Dd('mgroupid').value==0){alert('请选择会员组');Dd('mgroupid').focus();return false;}this.form.action='?moduleid=<?php echo $moduleid;?>&action=move';"/>&nbsp;
<?php echo group_select('groupid', '会员组', 0, 'id="mgroupid"');?> 
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>