<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">操作说明</td>
<td style="line-height:32px;font-size:14px;color:#666666;">
<?php if($split) {?>
<span class="f_red">开启内容分表之前必须先拆分数据</span><br/>
如果确认开启，请点击开始拆分按钮<br/>
<?php } else {?>
<span class="f_red">如无特殊原因强烈不建议关闭内容分表</span><br/>
关闭内容分表之前必须先合并数据<br/>
如果确认关闭，请点击开始合并按钮<br/>
<?php } ?>
</td>
</tr>
<tr>
<td class="tl"></td>
<td>
<?php if($split) {?>
<input type="button" value="开始拆分" class="btn-g" onclick="Go('?file=<?php echo $file;?>&mid=<?php echo $mid;?>&action=split');"/>
<?php } else {?>
<input type="button" value="开始合并" class="btn-g" onclick="Go('?file=<?php echo $file;?>&mid=<?php echo $mid;?>&action=merge');"/>
<?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="window.parent.location.reload();"/>
</td>
</tr>
</table>
<?php include tpl('footer');?>