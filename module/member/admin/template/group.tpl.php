<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="82">排序</th>
<th width="80">组ID</th>
<th width="200">会员组</th>
<th width="100">属性</th>
<th width="100">类型</th>
<th width="100">费用</th>
<th width="100"><?php echo VIP;?>指数</th>
<th width="40">设置</th>
<th width="40">删除</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<?php if($k > 5) { ?>
<td><input type="text" size="2" name="listorder[<?php echo $v['groupid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<?php } else { ?>
<td><input type="text" size="2" value="<?php echo $v['listorder'];?>" disabled/></td>
<?php } ?>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&groupid=<?php echo $v['groupid'];?>', '<?php echo $v['groupname'];?> 会员列表');"><?php echo $v['groupid'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&groupid=<?php echo $v['groupid'];?>', '<?php echo $v['groupname'];?>设置');"><?php echo $v['groupname'];?></a></td>
<td><?php echo $v['diy'] ? '自定义' : '系统组';?></td>
<td><?php echo $v['type'] ? '企业' : '个人';?></td>
<td><?php echo $v['vip'] ? '<span class="f_red">'.$DT['money_sign'].$v['fee'].'/年</span>' : '<span class="f_green">免费</span>';?></td>
<td><?php echo $v['vip'];?></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&groupid=<?php echo $v['groupid'];?>', '<?php echo $v['groupname'];?>设置');"><img src="<?php echo DT_STATIC;?>admin/set.png" width="16" height="16" title="设置" alt=""/></a></td>
<td><?php if($v['groupid'] > 7) { ?>
<a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&groupid=<?php echo $v['groupid'];?>"  onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a>
<?php } else {?>

<?php } ?>
</td>
<td></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="submit" value="更新排序" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=order';"/>
</div>
</form>
<table cellspacing="0" class="tb">
<tr>
<td class="ts">
&nbsp;&nbsp;- 会员组请按服务的范围(服务级别)由低到高依次排序，否则将影响会员的升级<br/>
&nbsp;&nbsp;- 免费模式会员组可以注册时选择，收费模式需要会员在线升级<br/>
</td>
</tr>
</table>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>