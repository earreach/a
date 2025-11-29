<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">收款银行</td>
<td><?php echo $item['bank'];?></td>
</tr>
<tr>
<td class="tl">开户网点</td>
<td><?php echo $item['branch'];?></td>
</tr>
<tr>
<td class="tl">收款户名</td>
<td><?php echo $item['truename'];?></td>
</tr>
<tr>
<td class="tl">收款帐号</td>
<td><?php echo $item['account'];?></td>
</tr>
<tr class="on">
<td class="tl">实付金额</td>
<td class="f_red"><?php echo $DT['money_sign'];?><?php echo $item['amount'];?></td>
</tr>
<tr>
<td class="tl">会员名</td>
<td><a href="javascript:;" onclick="_user('<?php echo $item['username'];?>');" class="t"><?php echo $item['username'];?></a></td>
</tr>
<tr>
<td class="tl">资金流水</td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=2&file=record&username=<?php echo $item['username'];?>', '资金流水');" class="t">点击查看</a></td>
</tr>
<tr>
<td class="tl">申请时间</td>
<td><?php echo $item['addtime'];?></td>
</tr>
<tr>
<td class="tl">申请IP</td>
<td><?php echo $item['ip'];?> - <?php echo ip2area($item['ip']);?></td>
</tr>
<tr class="on">
<td class="tl">受理结果</td>
<td><?php echo $dstatus[$item['status']];?></td>
</tr>
<tr>
<td class="tl">原因及备注</td>
<td><?php echo $item['note'];?></td>
</tr>
<tr>
<td class="tl">受理人</td>
<td><?php echo $item['editor'];?></td>
</tr>
<tr>
<td class="tl">受理时间</td>
<td><?php echo $item['edittime'];?></td>
</tr>
</table>
<div class="sbt"><input type="button" value=" 确定 " class="btn-g" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>