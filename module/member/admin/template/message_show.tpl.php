<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">标题</td>
<td class="px14 f_b"><?php echo $title;?></td>
</tr>
<tr>
<td class="tl">发件人</td>
<td><a href="javascript:;" onclick="_user('<?php echo $fromuser;?>');"><?php echo $fromuser;?></a></td>
</tr>
<tr>
<td class="tl">收件人</td>
<td><a href="javascript:;" onclick="_user('<?php echo $touser;?>');"><?php echo $touser;?></a></td>
</tr>
<tr>
<td class="tl">时间</td>
<td><?php echo timetodate($addtime, 6);?></td>
</tr>
<tr>
<td class="tl">IP</td>
<td><?php echo $ip;?> - <?php echo ip2area($ip);?></td>
</tr>
<?php if($mid && $tid) { ?>
<tr>
<td class="tl">原文</td>
<td><a href="<?php echo gourl('?mid='.$mid.'&itemid='.$tid);?>" target="_blank" class="t">点击查看</a></td>
</tr>
<?php } ?>
<tr>
<td class="tl">内容</td>
<td class="px14 lh20"><?php echo $content;?></td>
</tr>
</tbody>
</table>
<div class="sbt"><input type="button" value="返 回" class="btn-g" onclick="history.back(-1);"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="删 除" class="btn-r" onclick="if(confirm('确定要删除吗？此操作将不可撤销')) {Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete&itemid=<?php echo $itemid;?>&forward=<?php echo urlencode($forward);?>');}"/></div>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>