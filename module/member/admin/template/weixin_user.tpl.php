<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn" onclick="Dd('export').value=0;"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="addtime"<?php if($datetype == 'addtime') echo ' selected';?>>关注时间</option>
<option value="visittime"<?php if($datetype == 'visittime') echo ' selected';?>>打开时间</option>
<option value="logintime"<?php if($datetype == 'logintime') echo ' selected';?>>登录时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="sex">
<option value="-1">性别</option>
<?php
foreach($SEX as $k=>$v) {
	echo '<option value="'.$k.'" '.($sex == $k ? 'selected' : '').'>'.$v.'</option>';
}
?>
</select>&nbsp;
<select name="subscribe">
<option value="-1">状态</option>
<?php
foreach($SUBSCRIBE as $k=>$v) {
	echo '<option value="'.$k.'" '.($subscribe == $k ? 'selected' : '').'>'.strip_tags($v).'</option>';
}
?>
</select>&nbsp;
<label><input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/> 头像&nbsp;</label>
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="60"><a href="javascript:;" onclick="Dq('thumb',<?php echo $thumb ? 0 : 1;?>);">头像</a></th>
<th>昵称</th>
<th width="50">性别</th>
<th>省份</th>
<th>城市</th>
<th>会员名</th>
<th width="90">状态</th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">关注时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">打开时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">登录时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1">登录IP</th>
<th data-hide-1200="1">归属地</th>
<th data-hide-1200="1" width="100"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">登录次数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['headimgurl'];?>');"><img src="<?php echo $v['headimgurl'];?>" width="48" height="48" class="avatar"/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&openid=<?php echo $v['openid'];?>&action=chat', '与[<?php echo $v['nickname'];?>]交谈', 800, 520);"><?php echo $v['nickname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('sex','<?php echo $v['sex'];?>');"><?php echo $v['gender'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['province'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);"><?php echo $v['city'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>')"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('subscribe','<?php echo $v['subscribe'];?>');"><?php echo $v['status'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','addtime',0);Dq('date',this.innerHTML);"><?php echo $v['adddate'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','visittime',0);Dq('date',this.innerHTML);"><?php echo $v['visitdate'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('datetype','logintime',0);Dq('date',this.innerHTML);"><?php echo $v['logindate'];?></a></td>
<td data-hide-1200="1"><?php echo $v['loginip'];?></td>
<td data-hide-1200="1"><?php echo $v['loginip'] ? ip2area($v['loginip'], 2) : '';?></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=oauth&action=login&site=weixin&username=<?php echo $v['username'];?>&ip=<?php echo $v['loginip'];?>', '登录记录');"><?php echo $v['logintimes'];?></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="解除绑定" class="btn-r" onclick="if(confirm('确定要解除会员绑定吗？此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=unbind'}else{return false;}"/> &nbsp; 
<input type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除会员记录吗？不建议删除已关注的用户')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=remove'}else{return false;}"/> &nbsp; 
<input type="button" value="同步用户" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=sync');" title="同步微信平台上的用户信息"/>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>