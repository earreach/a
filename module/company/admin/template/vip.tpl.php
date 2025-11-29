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
<?php echo $group_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="fromtime"<?php if($datetype == 'fromtime') echo ' selected';?>>开通时间</option>
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>到期时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="vip">
<option value="0"><?php echo VIP;?>指数</option>
<?php 
for($i = 1; $i < 11; $i++) {
	echo '<option value="'.$i.'"'.($i == $vip ? ' selected' : '').'>'.$i.' 级</option>';
}
?>
</select>&nbsp;
<input type="text" name="vipt" value="<?php echo $vipt;?>" size="6" placeholder="理论值" title="理论值"/>&nbsp;
<input type="text" name="vipr" value="<?php echo $vipr;?>" size="6" placeholder="修正值" title="修正值"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>公司名称</th>
<th>会员名</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">会员组 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><?php echo VIP;?>指数 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">理论值 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">修正值 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">开通时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">到期时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 4 ? 3 : 4;?>');">剩余(天) <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></th>
<th width="40">续费</th>
<th width="40">登入</th>
<th width="40">修改</th>
</tr>
<?php foreach($companys as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="userid[]" value="<?php echo $v['userid'];?>"/></td>
<td align="left">&nbsp;<a href="<?php echo $v['linkurl'];?>" target="_blank"><?php echo $v['company'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('groupid','<?php echo $v['groupid'];?>');"><?php echo $GROUP[$v['groupid']]['groupname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('vip','<?php echo $v['vip'];?>');"><img src="<?php echo DT_SKIN;?>vip_<?php echo $v['vip'];?>.gif"/></a></td>
<td><a href="javascript:;" onclick="Dq('vipt','<?php echo $v['vipt'];?>');"><?php echo $v['vipt'];?></a></td>
<td><a href="javascript:;" onclick="Dq('vipr','<?php echo $v['vipr'];?>');"><?php echo $v['vipr'];?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','fromtime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['fromtime'], 3);?></a></td>
<td><a href="javascript:;" onclick="Dq('datetype','totime',0);Dq('date',this.innerHTML);"><?php echo timetodate($v['totime'], 3);?></a></td>
<td><?php echo $v['totime'] > $DT_TIME ? intval(($v['totime']-$DT_TIME)/86400) : '<span class="f_red">过期</span>';?></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=renew&userid=<?php echo $v['userid'];?>"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="续费" alt=""/></a></td>
<td><a href="?moduleid=2&action=login&userid=<?php echo $v['userid'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="进入会员中心" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&userid=<?php echo $v['userid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更新指数" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=update';"/>&nbsp;
<input type="submit" value="撤销<?php echo VIP;?>" class="btn-r" onclick="if(confirm('确定要撤销选中公司<?php echo VIP;?>资格吗吗？')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete'}else{return false;}"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<div class="tt">名词解释</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><?php echo VIP;?>指数</td>
<td class="f_gray"><?php echo VIP;?>指数，是对<?php echo VIP;?>会员的综合评分的一组1-10的数字，是理论值和修正值之和。指数越大，会员的级别、实力、可信度等越高，信息排名越靠前</td>
</tr>
<tr>
<td class="tl">理论值</td>
<td class="f_gray">理论值是由系统根据管理员设置的评分标准计算出的<?php echo VIP;?>指数值</td>
</tr>
<tr>
<td class="tl">修正值</td>
<td class="f_gray">为了消除理论值与会员实际综合实力的误差，由管理员进行人工干预的数值，可为正数，也可为负数</td>
</tr>
</table>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>