<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
load('member.css');
?>
<script type="text/javascript">var errimg = '<?php echo DT_STATIC;?>image/nopic60.png';</script>
<style>.userinfo-v0,.userinfo-v1,.userinfo-v2 {margin:-16px 0 0 48px;}</style>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="25" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $level_select;?>&nbsp;
<select name="vip">
<option value=""><?php echo VIP;?>级别</option>
<?php 
for($i = 0; $i < 11; $i++) {
	echo '<option value="'.$i.'"'.($i == $vip ? ' selected' : '').'>'.$i.' 级</option>';
}
?>
</select>&nbsp;
<?php echo $group_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo category_select('catid', '所属行业', $catid, $moduleid);?>&nbsp;
<?php echo ajax_area_select('areaid', '所在地区', $areaid);?>&nbsp;
<?php echo $mode_select;?>&nbsp;
<?php echo $type_select;?>&nbsp;
<?php echo $size_select;?>&nbsp;
<?php echo $valid_select;?>&nbsp;
<select name="agent">
<option value="-1"<?php if($agent == -1) echo ' selected';?>>分销代理</option>
<option value="1"<?php if($agent == 1) echo ' selected';?>>开启</option>
<option value="0"<?php if($agent == 0) echo ' selected';?>>关闭</option>
</select>&nbsp;
<select name="bill">
<option value="-1"<?php if($bill == -1) echo ' selected';?>>线下收款</option>
<option value="1"<?php if($bill == 1) echo ' selected';?>>开启</option>
<option value="0"<?php if($bill == 0) echo ' selected';?>>关闭</option>
</select>&nbsp;
<?php echo $snn_select;?>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="totime"<?php if($datetype == 'totime') echo ' selected';?>>服务到期</option>
<option value="fromtime"<?php if($datetype == 'fromtime') echo ' selected';?>>服务开始</option>
<option value="validtime"<?php if($datetype == 'validtime') echo ' selected';?>>认证时间</option>
<option value="styletime"<?php if($datetype == 'styletime') echo ' selected';?>>模板到期</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<select name="mixt">
<option value="regyear"<?php if($mixt == 'regyear') echo ' selected';?>>注册年份</option>
<option value="capital"<?php if($mixt == 'capital') echo ' selected';?>>注册资本</option>
<option value="hits"<?php if($mixt == 'hits') echo ' selected';?>>浏览次数</option>
<option value="comments"<?php if($mixt == 'comments') echo ' selected';?>>评论数量</option>
</select>&nbsp;
<input type="text" size="6" name="minv" value="<?php echo $minv;?>"/>~<input type="text" size="6" name="maxv" value="<?php echo $maxv;?>"/>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="uid" value="<?php echo $uid;?>" size="10" title="会员ID" placeholder="会员ID"/>&nbsp;
<label><input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/> 图片&nbsp;</label>
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="60"><a href="javascript:;" onclick="Dq('thumb',<?php echo $thumb ? 0 : 1;?>);">图片</a></th>
<th><?php echo $MOD['name'];?>名称</th>
<th width="16"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 25 ? 26 : 25;?>');"><img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 26 ? 'asc' : ($order == 25 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>会员名</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 31 ? 32 : 31;?>');">会员组 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 32 ? 'asc' : ($order == 31 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>所在地</th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 27 ? 28 : 27;?>');">注册年份 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 28 ? 'asc' : ($order == 27 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 29 ? 30 : 29;?>');">注册资本 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 30 ? 'asc' : ($order == 29 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($nn) { ?>
<th><?php echo $snn[$nn];?></th>
<?php } else { ?>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">浏览 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 9 ? 10 : 9;?>');">点赞 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 10 ? 'asc' : ($order == 9 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php if($order == 11 || $order == 12) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 11 ? 12 : 11;?>');">反对 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 12 ? 'asc' : ($order == 11 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 13 || $order == 14) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 13 ? 14 : 13;?>');">收藏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 14 ? 'asc' : ($order == 13 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 15 || $order == 16) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 15 ? 16 : 15;?>');">打赏 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 16 ? 'asc' : ($order == 15 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 17 || $order == 18) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 17 ? 18 : 17;?>');">赏金 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 18 ? 'asc' : ($order == 17 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 19 || $order == 20) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 19 ? 20 : 19;?>');">分享 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 20 ? 'asc' : ($order == 19 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<?php if($order == 21 || $order == 22) { ?><th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 21 ? 22 : 21;?>');">举报 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 22 ? 'asc' : ($order == 21 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th><?php } ?>
<th data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 23 ? 24 : 23;?>');">评论 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 24 ? 'asc' : ($order == 23 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<?php } ?>
<th width="40">登入</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="userid[]" value="<?php echo $v['userid'];?>"/></td>
<td><?php if($v['level']) {?><a href="javascript:;" onclick="Dq('level','<?php echo $v['level'];?>');"><img src="<?php echo DT_STATIC;?>admin/level_<?php echo $v['level'];?>.gif" title="<?php echo $v['level'];?>级" alt=""/></a><?php } ?></td>
<td><a href="javascript:;" onclick="_preview('<?php echo $v['thumb'];?>');"><img src="<?php echo $v['thumb'] ? $v['thumb'] : DT_STATIC.'image/nopic60.png';?>" width="60" onerror="this.src=errimg;"/></a><i class="userinfo-v<?php echo $v['validated'] ? 2 : 0;?>" title="<?php echo $v['validated'] ? '机构认证' : '未认证';?>" onclick="Dq('valid', '<?php echo $v['validated'] ? 1 : 2;?>');"></i></td>
<td>
<div class="lt">
<a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['company'];?></a>
<div>
<?php echo $v['type'];?><br/>
<?php echo $v['size'];?>
</div>
</div>
</td>
<td><?php if($v['vip']) {?><a href="javascript:;" onclick="Dq('vip','<?php echo $v['vip'];?>');"><img src="<?php echo DT_SKIN;?>vip_<?php echo $v['vip'];?>.gif" title="<?php echo VIP;?>:<?php echo $v['vip'];?>"/></a><?php } ?></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('groupid','<?php echo $v['groupid'];?>');"><?php echo $GROUP[$v['groupid']]['groupname'];?></a></td>
<td><a href="javascript:;" onclick="Dq('areaid','<?php echo $v['areaid'];?>');"><?php echo area_pos($v['areaid'], ' ');?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('minv','<?php echo $v['regyear'];?>',0);Dq('maxv','<?php echo $v['regyear'];?>',0);Dq('mixt','regyear');"><?php echo $v['regyear'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('minv','<?php echo $v['capital'];?>',0);Dq('maxv','<?php echo $v['capital'];?>',0);Dq('mixt','capital');"><?php echo $v['capital'] ? $v['capital'].'万'.$v['regunit'] : '未填';?></a></td>
<?php if($nn) { ?>
<td>
	<?php if($nn == 'lng') { ?>
	<?php echo $v[$nn];?>,<?php echo $v['lat'];?>
	<?php } else { ?>
	<?php echo $v[$nn];?>
	<?php } ?>
</td>
<?php } else { ?>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&homepage=<?php echo $v['username'];?>', '[<?php echo $v['company'];?>] 浏览记录');"><?php echo $v['hits'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dwidget('?file=like&action=like&mid=<?php echo $moduleid;?>&tid=<?php echo $v['userid'];?>', '点赞记录');"><?php echo $v['likes'];?></a></td>
<?php if($order == 11 || $order == 12) { ?><td><a href="javascript:;" onclick="Dwidget('?file=like&action=hate&mid=<?php echo $moduleid;?>&tid=<?php echo $v['userid'];?>', '反对记录');"><?php echo $v['hates'];?></a></td><?php } ?>
<?php if($order == 13 || $order == 14) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=favorite&mid=<?php echo $moduleid;?>&tid=<?php echo $v['userid'];?>', '[<?php echo $v['company'];?>] 收藏记录');"><?php echo $v['favorites'];?></a></td><?php } ?>
<?php if($order == 15 || $order == 16) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['userid'];?>', '[<?php echo $v['company'];?>] 打赏记录');"><?php echo $v['awards'];?></a></td><?php } ?>
<?php if($order == 17 || $order == 18) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=award&mid=<?php echo $moduleid;?>&tid=<?php echo $v['userid'];?>', '[<?php echo $v['company'];?>] 打赏记录');"><?php echo $v['award'];?></a></td><?php } ?>
<?php if($order == 19 || $order == 20) { ?><td><a href="javascript:;" onclick="Dwidget('?file=stats&action=pv&mid=<?php echo $moduleid;?>&itemid=<?php echo $v['userid'];?>&kw=share.php', '[<?php echo $v['company'];?>] 分享记录');"><?php echo $v['shares'];?></a></td><?php } ?>
<?php if($order == 21 || $order == 22) { ?><td><a href="javascript:;" onclick="Dwidget('?moduleid=3&file=guestbook&mid=<?php echo $moduleid;?>&tid=<?php echo $v['userid'];?>', '举报记录');"><?php echo $v['reports'];?></a></td><?php } ?>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dwidget('?moduleid=3&file=comment&mid=<?php echo $moduleid;?>&itemid=<?php echo $v['userid'];?>', '[<?php echo $v['company'];?>] 评论列表');"><?php echo $v['comments'];?></a></td>
<?php } ?>
<td><a href="?moduleid=2&action=login&userid=<?php echo $v['userid'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="进入会员中心" alt=""/></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=2&action=edit&userid=<?php echo $v['userid'];?>', '修改资料');"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="更新公司" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&action=update';"/>&nbsp;
<input type="submit" value="设置<?php echo VIP;?>" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&action=add';"/>&nbsp;
<input type="submit" value="删除公司" class="btn-r" onclick="if(confirm('确定要删除选中公司吗？系统将删除选中用户所有信息，此操作将不可撤销')){this.form.action='?moduleid=2&action=delete'}else{return false;}"/>&nbsp;
<input type="submit" value="移动地区" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=move';"/>&nbsp;
<?php echo level_select('level', '设置级别为</option><option value="0">取消', 0, 'onchange="this.form.action=\'?moduleid='.$moduleid.'&file='.$file.'&action=level\';this.form.submit();"');?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>